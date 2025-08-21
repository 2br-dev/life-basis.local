{$config=ConfigLoader::byModule('pushsender')}
<p>{t}Пункт меню{/t}</p>
{$options = Menu\Model\Api::staticSelectList()}
{html_options name="mobile_menu_id" options=$options selected=$elem.mobile_menu_id}