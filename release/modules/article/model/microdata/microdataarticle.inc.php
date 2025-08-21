<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Model\microdata;

use Article\Model\Orm\Article;
use Users\Model\Orm\User;
use Photo\Model\Orm\Image;
use RS\Application\Microdata\AbstractMicrodataEntity;
use RS\Application\Microdata\InterfaceMicrodataSchemaOrgJsonLd;
use RS\Img\Exception;

/**
 * Микроразметка статьи
 */
class MicrodataArticle extends AbstractMicrodataEntity implements InterfaceMicrodataSchemaOrgJsonLd
{
    /** @var Article */
    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Возвращает данные для микроразметки Schema.org в формате JSON-LD
     *
     * @return array
     * @throws \Exception
     */
    function getSchemaOrgJsonLd(): array
    {
        $date = new \DateTime($this->article['dateof']);
        $formatted_date = $date->format('Y-m-d\TH:i:s');

        $result = [
            '@context' => 'https://schema.org/',
            '@type' => 'Article',
            'headline' => $this->article['title'],
            'description' => $this->article['meta_description'] ?: $this->article['title'],
            'author' => [],
            'datePublished' => $formatted_date,
            'dateModified' => $formatted_date,
            'mainEntityOfPage' => [],
            //'publisher' => если создатель - организация,
        ];

        $user = new User($this->article->user_id);

        $images = $this->getImages();

        if (!empty($images)) {
            $result['image'] = $images;
        }

        $author_data = [
            '@type' => 'Person',
            'name' => $user->name
        ];
        $result['author'] = $author_data;

        $entity_data = [
            '@type' => 'WebPage',
            '@id' => $this->article->getUrl(true)
        ];
        $result['mainEntityOfPage'] = $entity_data;

        if ($user->is_company) {
            $publisher_data = [
                '@type' => 'Organization',
                'name' => $user->company,
            ];
            $result['publisher'] = $publisher_data;
        }

        return $result;
    }

    /**
     * Возвращает массив URL ссылок всех изображений статьи, включая картинку-превью
     *
     * @return array
     * @throws Exception
     */
    function getImages()
    {
        $images = [];

        //Добавляем превью изображения
        if ($this->article->image) {
            new Image();
            $images[] = $this->article->__image->getUrl(1500, 1500, 'xy', true);
        }

        //Добавляем прикреплённые к статье фотографии
        $photos = $this->article->getPhotos();
        /** @var Image $photo */
        foreach ($photos as $photo) {
            $images[] = $photo->getUrl(1500, 1500, 'xy', true);
        }

        return $images;
    }
}
