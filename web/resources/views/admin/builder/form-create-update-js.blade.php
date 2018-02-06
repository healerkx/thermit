<script>
    $(function() {

        $('#add-field-link').click(function() {
            var table = $('#field-table');
            var template = table.find('.field-template').clone();
            template.css('display', '').removeClass('field-template');
            template.appendTo(table.find('.listview'))
        });


        $('#field-table').delegate('a.btn-del', 'click', function() {
            var div = $(this).closest('div.media');
            div.remove();
        })
    });
</script>