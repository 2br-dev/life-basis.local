{include file=$field->getOriginalTemplate()}
<script>
    $(function() {
        var updateTypeForm = function() {
            var type = $('select[name="type"]').val();
            $.ajaxQuery({
                url: '{$router->getAdminUrl("getTypeForm")}',
                data: { type: type },
                success: function(response) {
                    $('#type-form').html(response.html).trigger('new-content');
                }
            })
        }

        $('select[name="type"]').change(function() {
            updateTypeForm();
        });
    });
</script>
</td></tr>
<tbody id="type-form">
{if $elem['type']}
    {$service_type = $elem->getServiceTypeObject()}
    {include file="%ai%/admin/service/type_form.tpl"}
{/if}
</tbody>
<tr><td></td><td>