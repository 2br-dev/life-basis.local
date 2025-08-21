<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Config\ModuleRights;
use Crm\Model\Notice\NewMessageToUsers;
use Crm\Model\Orm\ChatHistory;
use Crm\Model\Orm\Task;
use Crm\Model\View\Manager as ViewManager;
use Alerts\Model\Manager as AlertsManager;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Router\Manager;
use Users\Model\Orm\User;

/**
 * API для работы чатом и историей изменений
 */
class ChatHistoryApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\ChatHistory());
    }

    static function getTask($task_id)
    {
        return  Request::make()
            ->from(new Orm\Task())
            ->where(['id' => $task_id])
            ->exec()->fetchRow();
    }

    /**
     * Возвращает текстовое описание изменений
     *
     * @param array $before
     * @param array $after
     * @return array
     */
    private static function summarizeChecklistChanges(array $before, array $after): array
    {
        $result = [];

        $beforeMap = [];
        foreach ($before as $checklist) {
            $beforeMap[$checklist['title']] = $checklist;
        }

        $afterMap = [];
        foreach ($after as $checklist) {
            $afterMap[$checklist['title']] = $checklist;
        }

        foreach ($beforeMap as $title => $checklist) {
            if (!isset($afterMap[$title])) {
                $result[] = t('Удален чеклист «%title»', ['title' => $title]);
            }
        }

        foreach ($afterMap as $title => $checklist) {
            if (!isset($beforeMap[$title])) {
                $result[] = t('Добавлен новый чеклист «%title»', ['title' => $title]);
            }
        }

        foreach ($afterMap as $title => $afterChecklist) {
            if (!isset($beforeMap[$title])) continue;

            $beforeItems = $beforeMap[$title]['items'] ?? [];
            $afterItems = $afterChecklist['items'] ?? [];

            $beforeTitles = array_column($beforeItems, 'title');
            $afterTitles = array_column($afterItems, 'title');

            $added = array_diff($afterTitles, $beforeTitles);
            $removed = array_diff($beforeTitles, $afterTitles);

            if (count($added)) {
                $result[] = t(
                    'В чек лист «%title» [plural:%n:добавлен|добавлено|добавлено] %n [plural:%n:пункт|пункта|пунктов]',
                    ['title' => $title, 'n' => count($added)]
                );
            }
            if (count($removed)) {
                $result[] = t(
                    'У чек листа «%title» [plural:%n:удален|удалено|удалено] %n [plural:%n:пункт|пункта|пунктов]',
                    ['title' => $title, 'n' => count($removed)]
                );
            }

            $beforeDoneMap = [];
            foreach ($beforeItems as $item) {
                $beforeDoneMap[$item['title']] = $item['is_done'] ?? false;
            }

            $marked = $unmarked = 0;
            foreach ($afterItems as $item) {
                $titleText = $item['title'];
                if (!array_key_exists($titleText, $beforeDoneMap)) continue;

                $was = $beforeDoneMap[$titleText];
                $now = $item['is_done'] ?? false;

                if ($was != $now) {
                    if ($now) $marked++;
                    else $unmarked++;
                }
            }

            if ($marked > 0) {
                $result[] = t(
                    'У чеклиста «%title» [plural:%n:отмечен|отмечено|отмечено] %n [plural:%n:пункт|пункта|пунктов]',
                    ['title' => $title, 'n' => $marked]
                );
            }

            if ($unmarked > 0) {
                $result[] = t(
                    'У чеклиста «%title» [plural:%n:отменен|отменены|отменены] %n [plural:%n:пункт|пункта|пунктов]',
                    ['title' => $title, 'n' => $unmarked]
                );
            }
        }

        return $result;
    }


    /**
     * Возвращает историю изменений по задаче
     *
     * @param Task $task
     * @param int|null $before_id
     * @param int|null $after_id
     * @return array
     * @throws \RS\Exception
     */
    public static function getChatHistoryByTask(Task $task, ?int $before_id = null, ?int $after_id = null): array
    {
        if (!$task['id']) return [];

        $router = Manager::obj();
        $rootId = $task['autotask_root_id'] ?: $task['id'];

        $history = Request::make()
            ->from(new Orm\ChatHistory())
            ->where('autotask_root_id = #root', ['root' => $rootId]);

        if ($before_id || $after_id) {
            $history->openWGroup(); // откроет скобки

            if ($before_id) {
                $history->where('id < #before', ['before' => $before_id]);
            }

            if ($after_id) {
                $history->where('id > #after', ['after' => $after_id]);
            }

            $history->closeWGroup(); // закроет скобки
        }

        $history = $history
            ->orderBy('date_of_create DESC')
            ->limit(20)
            ->objects();


        $history = array_reverse($history);

        $first_id = null;
        $last_id = null;

        if (!empty($history)) {
            $first_id = $history[0]['id'];
            $last_id = end($history)['id'];
        }

        $result = [];

        foreach ($history as $entry) {
            $date = date('Y-m-d', strtotime($entry['date_of_create']));
            $time = date('H:i:s', strtotime($entry['date_of_create']));

            $user = $entry['user_id'] ? new User($entry['user_id']) : null;

            $messageData = [
                'type' => $entry['type'],
                'time' => $time,
                'message_id' => $entry['id'],
            ];

            if ($user) {
                $messageData['user'] = [
                    'id' => $user['id'],
                    'name' => $user->getFio(),
                    'is_current_user' => Auth::getCurrentUser()->id == $user['id'],
                ];
            }

            if ($entry['type'] == Orm\ChatHistory::TYPE_MESSAGE) {
                $messageData['message'] = $entry['message'];
                if ($entry['reply_to_id']) {
                    $replyEntry = new ChatHistory($entry['reply_to_id']);
                    if ($replyEntry['id']) {
                        $replyUser = new User($replyEntry['user_id']);
                        $messageData['reply_to'] = [
                            'message_id' => $replyEntry['id'],
                            'user' => [
                                'id' => $replyUser['id'],
                                'name' => $replyUser->getFio(),
                            ],
                            'message' => mb_substr($replyEntry['message'], 0, 150)
                        ];
                    }
                }
                if (!Rights::CheckRightError('crm', ModuleRights::TASK_CHAT_READ_FILES)) {
                    $attachments = $entry->getAttachments();
                    if ($attachments) {
                        $messageData['attachments'] = [];
                        foreach ($attachments as $file) {
                            $messageData['attachments'][] = [
                                'name' => $file->name,
                                'size' => $file->size,
                                'url' => $file->getHashedUrl()
                            ];
                        }
                    }
                }
            } elseif ($entry['type'] == Orm\ChatHistory::TYPE_SYSTEM) {
                $task_data = $entry['task_id'] ? self::getTask($entry['task_id']) : null;
                $autotask = $entry['autotask_id'] ? new Orm\Autotask($entry['autotask_id']) : null;
                if (!$task_data) continue;

                $router = Manager::obj();
                $link_template = '<a href="%link" class="crud-edit" data-crud-dialog-width="90%" data-crud-dialog-height="90%">%title</a>';

                $task_link = t($link_template, [
                    'link' => $router->getAdminUrl(false, ['do' => 'edit', 'id' => $task_data['id']], 'crm-taskctrl'),
                    'title' => "{$task_data['title']} ({$task_data['id']})"
                ]);

                $message = "Задача {$task_link} " . ($entry['extra'] ? "изменена" : "создана");

                if ($autotask) {
                    $autotask_link = t($link_template, [
                        'link' => $router->getAdminUrl(false, ['do' => 'edit', 'id' => $autotask['id']], 'crm-autotaskctrl'),
                        'title' => $autotask['title']
                    ]);
                    $message .= " по автозадаче {$autotask_link}";
                } elseif ($user) {
                    $user_link = t($link_template, [
                        'link' => $router->getAdminUrl(false, ['do' => 'edit', 'id' => $user['id']], 'users-ctrl'),
                        'title' => $user->getFio()
                    ]);
                    $message .= " пользователем {$user_link}";
                }

                $message .= " в {$time}";

                $messageData['message'] = $message;

                // Разбор изменений, если есть
                if (!empty($entry['extra'])) {
                    $changes = @json_decode($entry['extra'], true);
                    if (is_array($changes)) {
                        $messageData['changes'] = [];
                        foreach ($changes as $field => $change) {
                            $title = $change['title'] ?? $field;
                            $before = $change['before_value'] ?? null;
                            $after = $change['current_value'] ?? null;

                            if ($field == 'checklist' && is_array($before) && is_array($after)) {
                                $summary = self::summarizeChecklistChanges($before, $after);
                                $messageData['changes'][] = [
                                    'title' => $title,
                                    'field' => $field,
                                    'before' => null,
                                    'after' => null,
                                    'summary' => implode(', ', $summary),
                                ];
                            } else {
                                $messageData['changes'][] = [
                                    'title' => $title,
                                    'field' => $field,
                                    'before' => is_array($before) ? json_encode($before) : $before,
                                    'after' => is_array($after) ? json_encode($after) : $after,
                                ];
                            }
                        }
                    }
                }
            }

            $result[$date][] = $messageData;
        }

        return [
            'messages' => $result,
            'first_id' => $first_id,
            'last_id' => $last_id
        ];

    }

    /**
     * Добавляет сообщение в переписку
     *
     * @param $task
     * @param $message
     * @param $reply_to_id
     * @param $attachments
     * @return array
     */
    public static function writeMessage($task, $message, $reply_to_id, $attachments)
    {
        $user = \RS\Application\Auth::getCurrentUser();

        $entry = new ChatHistory();
        $entry['task_id'] = $task['id'];
        $entry['user_id'] = $user['id'];
        $entry['message'] = $message;
        $entry['date_of_create'] = date('Y-m-d H:i:s');
        $entry['autotask_root_id'] = $task['autotask_root_id'] ?: $task['id'];
        $entry['type'] = ChatHistory::TYPE_MESSAGE;
        $entry['reply_to_id'] = $reply_to_id;
        $entry['attachments'] = $attachments;
        $entry->insert();

        ViewManager::obj()
            ->markAllAsViewed(trim(str_replace('crm-', '', $entry->getShortAlias())));

        $notice = new NewMessageToUsers();
        $notice->init($task, $entry);
        AlertsManager::send($notice);

        $entryData = [
            'type' => 'user',
            'message' => $message,
            'message_id' => $entry['id'],
            'time' => date('H:i:s'),
            'user' => [
                'id' => $user['id'],
                'name' => $user->getFio(),
                'is_current_user' => true
            ]
        ];

        if ($reply_to_id) {
            $replyEntry = new ChatHistory($reply_to_id);
            if ($replyEntry['id']) {
                $replyUser = new \Users\Model\Orm\User($replyEntry['user_id']);
                $entryData['reply_to'] = [
                    'message_id' => $replyEntry['id'],
                    'user' => [
                        'id' => $replyUser['id'],
                        'name' => $replyUser->getFio(),
                    ],
                    'message' => mb_substr($replyEntry['message'], 0, 150)
                ];
            }
        }

        if ($entry['attachments'] && $entry_attachments = $entry->getAttachments()) {
            $entryData['attachments'] = [];
            foreach ($entry_attachments as $file) {
                $entryData['attachments'][] = [
                    'name' => $file->name,
                    'size' => $file->size,
                    'url' => $file->getHashedUrl()
                ];
            }
        }

        return $entryData;
    }


    /**
     * Добавляет системную запись в историю изменений
     *
     * @param Task $task
     * @param $flag
     * @param $changes
     * @return void
     */
    public static function writeSystemMessage(Task $task, $flag, $changes = null)
    {
        unset($changes['date_of_update']);
        unset($changes['chat']);

        $empty_values = ['-', 'пусто', '- не выбрано -', '(0)', '()', '', null];

        if (!empty($changes)) {
            foreach ($changes as $key => $change) {
                $before_raw = $change['before_value'] ?? '';
                $after_raw = $change['current_value'] ?? '';

                $before = is_array($before_raw) ? json_encode($before_raw, JSON_UNESCAPED_UNICODE) : (string)$before_raw;
                $after = is_array($after_raw) ? json_encode($after_raw, JSON_UNESCAPED_UNICODE) : (string)$after_raw;

                $before_norm = mb_strtolower(strip_tags(trim($before)));
                $after_norm = mb_strtolower(strip_tags(trim($after)));

                if (
                    in_array($before_norm, $empty_values, true) &&
                    in_array($after_norm, $empty_values, true)
                ) {
                    unset($changes[$key]);
                    continue;
                }

                if ($before_norm === $after_norm) {
                    unset($changes[$key]);
                }
            }
        }

        if ($flag === OrmObject::UPDATE_FLAG && !empty($changes) || $flag === OrmObject::INSERT_FLAG) {
            $history = new Orm\ChatHistory();
            $history['task_id'] = $task['id'];
            $history['date_of_create'] = date('Y-m-d H:i:s');
            $history['type'] = Orm\ChatHistory::TYPE_SYSTEM;
            $history['autotask_root_id'] = $task['autotask_root_id'] ?: $task['id'];
            $history['user_id'] = $task['create_by_rule_id'] ? null : Auth::getCurrentUser()->id;
            $history['autotask_id'] = $task['create_by_rule_id'] ?: null;

            $history['extra'] = $flag === OrmObject::UPDATE_FLAG && !empty($changes)
                ? json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : '';

            $history->insert();
        }
    }
}