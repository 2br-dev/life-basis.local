<input type="text" placeholder="{t}Поиск по названию или id модуля{/t}" class="w-100 m-b-20 fast-search-modules">
<div class="side-modules">
    {foreach $modules as $module}
        <div class="m-b-20" data-module-class="{$module.class}">
            <a href="{adminUrl do="edit" mod=$module.class mod_controller="modcontrol-control"}" class="f-18 title">
                {$module.name}&nbsp;&nbsp;<span class="f-12 c-black f-700">{$module.class}</span>
            </a>
            <p>{$module.description}</p>
        </div>
    {/foreach}
</div>

<script>
    $(function() {
        /* Быстрый поиск по модулям */
        $('.fast-search-modules').on('input', function() {
            let searchValue = this.value.toLowerCase();
            $('.side-modules [data-module-class]').each(function() {
                let visible = searchValue === ''
                    || $(this).data('moduleClass').indexOf(searchValue) > -1;

                if (searchValue !== '' && !visible) {
                    visible = $('.title', this).filter(function() {
                        return $(this).text().toLowerCase().indexOf(searchValue) > -1;
                    }).length > 0;
                }

                $(this).toggleClass('hidden', !visible);
            });
        });
    });
</script>