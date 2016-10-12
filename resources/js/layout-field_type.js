$(function() {
    $(document).on('click', '.add-widget', function(event) {
        event.preventDefault();

        var $this = $(this);

        var href = $this.attr('href');
        var $container = $('#' + $this.data('slug'));
        var $rowsContainer = $container.find('.rows');

        $.get(href, function(data) {
            var $row = $('<div>').addClass('layout-row').append(data);
            $rowsContainer.append($row);
        });

        $('#modal').modal('hide');

        return false;
    });
});