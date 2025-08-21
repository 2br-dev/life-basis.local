{include file=$field->getOriginalTemplate()}

<script>
    $(function() {
        $('[name="order_num_generate_type"]').change(function() {
            $('[name="generated_ordernum_mask"]').closest('tr')
                .toggle($(this).val() == 'order_num_random'
                        || $(this).val() == 'order_num_increment');

            $('[name="generated_ordernum_numbers"]').closest('tr')
                .toggle($(this).val() == 'order_num_random'
                    || $(this).val() == 'order_num_increment');

            $('[name="generated_ordernum_start_number"]').closest('tr')
                .toggle($(this).val() == 'order_num_increment');
        }).change();
    });
</script>