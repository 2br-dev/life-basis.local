{include file=$field->getOriginalTemplate()}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        function onChangeImportScheme() {
            let element = document.querySelector('[name="import_scheme"]');
            let child_element = document.querySelector('[name="import_offers_in_one_product"]').closest('tr');
            if (element.value == 'offers_in_import') {
                child_element.classList.remove('hidden');
            } else {
                child_element.classList.add('hidden');
            }
        }

        document.querySelector('[name="import_scheme"]').addEventListener('change', onChangeImportScheme);
        onChangeImportScheme();
    });
</script>