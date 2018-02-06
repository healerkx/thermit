<script>
    $(function() {

        $('#add-filter-link').click(function() {
            var table = $('#filter-table');
            var template = table.find('.filter-template').clone();
            template.css('display', '').removeClass('filter-template');
            template.appendTo(table.find('.listview'))
        });

        $('#add-column-link').click(function() {
            var table = $('#column-table');
            var template = table.find('.column-template').clone();
            template.css('display', '').removeClass('column-template');
            template.appendTo(table.find('.listview'))
        });

        $('#filter-table, #column-table').delegate('a.btn-del', 'click', function() {
            var div = $(this).closest('div.media');
            div.remove();
        })
    });
</script>