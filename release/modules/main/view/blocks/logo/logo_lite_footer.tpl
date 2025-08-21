{if $site_config.logo}
    <img class="logo {if !$site_config.slogan}logo_without-desc{/if}"
            src="{$site_config["__{$logo_field}"]->getUrl($width, $height)}" loading="lazy" alt=""
            {if $site_config["__{$logo_field}"]->getExtension() != 'svg'}
                srcset="{$site_config["__{$logo_field}"]->getUrl($width*2, $height*2)} 2x"
            {/if}>
{/if}
{if $site_config.slogan}
    <div class="logo-desc logo-desc_footer">{$site_config.slogan}</div>
{/if}