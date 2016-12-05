$(function () {

    $('.layout-rows').sortable({
        placeholder: '<div class="placeholder"></div>',
        containerSelector: '.layout-rows',
        itemSelector: '.layout-row',
        handle: '.layout-handle a',
        nested: false,
        onDrop: function ($item, container, _super, event) {
            $container = $item.closest('.layout-field_type');

            $container.find('.layout-row').each(function (index) {
                $(this).find('.layout-row-sort-order').attr('value', index);
            });

            _super($item, container);
        },
        onDragStart: function ($item, container, _super, event) {
            $('.placeholder').css({
                height: $item.height(),
                width: $item.width()
            });

            item = $item;

            adjustment = {
                left: container.rootGroup.pointer.left - $item.offset().left,
                top: container.rootGroup.pointer.top - $item.offset().top
            };

            _super($item, container);
        },
        onDrag: function ($item, position) {
            $item.css({
                left: position.left - adjustment.left,
                top: position.top - adjustment.top
            });
        },
        afterMove: function ($placeholder, container, $closestItemOrContainer) {
            $placeholder.height(item.outerHeight());
        }
    });

    $(document.body).on('click', '.add-widget', function (event) {
        event.preventDefault();

        var $this = $(this);

        var href = $this.attr('href');
        var $container = $('#' + $this.data('field_slug'));
        var $rowsContainer = $container.find('.layout-rows');

        /**
         * Find the next available instance id.
         */
        var instanceId = 0;
        while($('.layout-form-container[data-instance="' + instanceId + '"]').length > 0) {
            instanceId++;
        }

        $.post(href, {instance_id: instanceId, field_slug: $this.data('field_slug')}, function (data) {

            /**
             * This is a hack to get around a bug that exists in the editor field type.
             * If ace has already been loaded then search for a line containing ace.js and remove it.
             */
            if(typeof(ace) == 'object') {
                var dataArray = data.split('\n');
                var removeIndex = -1;
                for(var i = 0; i < dataArray.length; i++) {
                    if(dataArray[i].includes('ace.js')) {
                        removeIndex = i;
                    }
                }
                if(removeIndex > -1) {
                    dataArray.splice(removeIndex, 1);
                }

                data = dataArray.join('\n');
            }

            var $row = $('<div>').addClass('layout-row').append(data);
            $rowsContainer.append($row);
        });

        $('#modal').modal('hide');

        return false;
    });

    $(document.body).on('click', '.layout-row-delete', function (event) {
        var $layoutRow = $(this).closest('.layout-row');
        var layoutRowId = $layoutRow.find('.layout-row-id').val();
        var $formGroup = $(this).closest('.form-group');
        var deleteIds = $formGroup.find('.delete-ids');
        var values = deleteIds.val();
        if(values == "") {
            values = layoutRowId;
        }
        else {
            values = values.split(',');
            values.push(layoutRowId);
            values = values.join(',');
        }

        deleteIds.val(values);

        $layoutRow.remove();
    });
});