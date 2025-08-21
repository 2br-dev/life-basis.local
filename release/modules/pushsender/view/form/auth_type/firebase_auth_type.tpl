{include file=$field->getOriginalTemplate() field=$elem.__firebase_auth_type}

<script>
    $(function() {
        $('[name="firebase_auth_type"]').change(function() {
            let authType = $(this).val();

            $('[name="googlefcm_server_key"], [name="project_id"], [name="cloud_messaging_api"]').each(function() {
                $(this).closest('tr').hide();
            });

            if (authType === 'cloud_messaging_api') {
                $('[name="cloud_messaging_api"]').closest('tr').show();
                $('[name="project_id"]').closest('tr').show();
            }else {
                $('[name="googlefcm_server_key"]').closest('tr').show();
            }
        }).change();
    });
</script>