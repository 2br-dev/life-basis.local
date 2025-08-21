<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Model\Microdata;

use RS\Application\Microdata\AbstractMicrodataEntity;
use RS\Application\Microdata\InterfaceMicrodataSchemaOrgJsonLd;
use Site\Model\Orm\Config;

/**
 * Микроразметка главной страницы
 */
class MicrodataOrganization extends AbstractMicrodataEntity implements InterfaceMicrodataSchemaOrgJsonLd
{
    /** @var Config */
    protected $site_config;

    public function __construct(Config $site_config)
    {
        $this->site_config = $site_config;
    }

    /**
     * Возвращает данные для микроразметки Schema.org в формате JSON-LD
     *
     * @return array
     */
    function getSchemaOrgJsonLd(): array
    {
        $result = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $this->site_config['firm_name'],
        ];

        if ($this->site_config['firm_description']) {
            $result['description'] = $this->site_config['firm_description'];
        }

        if ($this->site_config['logo']) {
            $result['image'] = $this->site_config['__logo']->getUrl(275, 80, 'xy', true);
        }

        if ($this->site_config['admin_phone']) {
            $admin_phones = $this->site_config['admin_phone'];
            if (str_contains($admin_phones, ',')) {
                $admin_phones = explode(',', $this->site_config['admin_phone']);
            }
            $result['telephone'] = $admin_phones;
        }

        if ($this->site_config['admin_email']) {
            $admin_emails = $this->site_config['admin_email'];
            if (str_contains($admin_emails, ',')) {
                $admin_emails = explode(',', $this->site_config['admin_email']);
            }
            $result['email'] = $admin_emails;
        }

        if ($this->site_config['firm_legal_address']) {
            $result['address'] = $this->site_config['firm_legal_address'];
        }

        return $result;
    }
}
