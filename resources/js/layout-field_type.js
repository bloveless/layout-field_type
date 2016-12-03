$(function() {
    $(document).on('click', '.add-widget', function(event) {
        event.preventDefault();

        var $this = $(this);

        var href = $this.attr('href');
        var $container = $('#' + $this.data('field_slug'));
        var $rowsContainer = $container.find('.rows');
        var $layoutRows = $container.find('.layout-row');

        $.post(href, {instance_id: $layoutRows.length + 1, field_slug: $this.data('field_slug')}, function(data) {
            console.log(data);
            var $row = $('<div>').addClass('layout-row').append(data);
            $rowsContainer.append($row);
        });

        $('#modal').modal('hide');

        return false;
    });
});