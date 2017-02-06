$(function () {


    var layouts = $('[data-provides="fritzandandre.field_type.layout"]:not([data-initialized])');

    layouts.each(function () {

        $(this).attr('data-initialized', '');

        var wrapper = $(this);
        var items = $(this).find('.layout-row');
        var cookie = 'layout:' + $(this).closest('.layout-container').data('field_name');

        var collapsed = Cookies.getJSON(cookie);

        items.each(function () {

            var item = $(this);
            var toggle = $(this).find('[data-toggle="collapse"]');
            var text = toggle.find('span');

            /**
             * Hide initial items.
             */
            if (typeof collapsed == 'undefined') {
                collapsed = {};
            }

            if (collapsed[items.index(item)] == true) {
                item
                    .toggleClass('collapsed')
                    .find('[data-toggle="collapse"] i')
                    .toggleClass('fa-compress')
                    .toggleClass('fa-expand');

                if (toggle.find('i').hasClass('fa-compress')) {
                    text.text(toggle.data('collapse'));
                } else {
                    text.text(toggle.data('expand'));
                }
            }
        });

        wrapper.on('click', '[data-toggle="collapse"]', function () {

            var toggle = $(this);
            var item = toggle.closest('.layout-row');
            var text = toggle.find('span');

            item
                .toggleClass('collapsed')
                .find('[data-toggle="collapse"] i')
                .toggleClass('fa-compress')
                .toggleClass('fa-expand');

            if (toggle.find('i').hasClass('fa-compress')) {
                text.text(toggle.data('collapse'));
            } else {
                text.text(toggle.data('expand'));
            }

            toggle
                .closest('.dropdown')
                .find('.dropdown-toggle')
                .trigger('click');

            if (typeof collapsed == 'undefined') {
                collapsed = {};
            }

            collapsed[items.index(item)] = item.hasClass('collapsed');

            Cookies.set(cookie, JSON.stringify(collapsed), {path: window.location.pathname});

            return false;
        });
    });

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
        var $container = $('.layout-container[data-field_name="' + $this.data('field_slug') + '"]');
        var $rowsContainer = $container.find('.layout-rows');

        /**
         * Find the next available instance id.
         */
        var instanceId = 0;
        while($('.layout-form-container[data-instance="' + instanceId + '"]').length > 0) {
            instanceId++;
        }

        /**
         * Reset the sort orders for every row
         */
        var sortOrder = 0;
        $('.layout-container[data-field_name="' + $this.data('field_slug') + '"] .layout-rows .layout-row').each(function(index, row) {
            $(row).find('.layout-row-sort-order').val(index);
            sortOrder = index;
        });

        $.post(href, {instance_id: instanceId, field_slug: $this.data('field_slug'), sort_order: ++sortOrder}, function (data) {

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

            $rowsContainer.append(data);
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