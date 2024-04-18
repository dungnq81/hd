(function ($) {

    const _sortable_helper = function (e, ui) {
        ui.children().children().each(function () {
            $(this).width($(this).width());
        });
        return ui;
    };

    $('table.posts #the-list, table.pages #the-list').sortable({
        'items': 'tr',
        'axis': 'y',
        'helper': _sortable_helper,
        'update': function (e, ui) {
            $.post(ajaxurl, {
                action: 'update-menu-order',
                order: $('#the-list').sortable('serialize'),
            });
        }
    });

    $('table.tags #the-list').sortable({
        'items': 'tr',
        'axis': 'y',
        'helper': _sortable_helper,
        'update': function (e, ui) {
            $.post(ajaxurl, {
                action: 'update-menu-order-tags',
                order: $('#the-list').sortable('serialize'),
            });
        }
    });

    /**
     * Fix for table breaking
     */

    $(window).on('load', function () {

        // make the array for the sizes
        let td_array = [];
        let i = 0;

        $('#the-list tr:first-child').find('td').each(function () {
            td_array[i] = $(this).outerWidth();
            i += 1;
        });

        $('#the-list').find('tr').each(function () {
            let j = 0;
            $(this).find('td').each(function () {
                let paddingx = parseInt($(this).css('padding-left').replace('px', '')) + parseInt($(this).css('padding-right').replace('px', ''));
                $(this).width(td_array[j] - paddingx);
                j += 1;
            });
        });

        let y = 0;

        // check if there are items in the table
        if ($('#the-list > tr.no-items').length === 0) {
            $('#the-list').parent().find('thead').find('th').each(function () {
                let paddingx = parseInt($(this).css('padding-left').replace('px', '')) + parseInt($(this).css('padding-right').replace('px', ''));
                $(this).width(td_array[y] - paddingx);
                y += 1;
            });

            let z = 0;

            $('#the-list').parent().find('tfoot').find('th').each(function () {
                let paddingx = parseInt($(this).css('padding-left').replace('px', '')) + parseInt($(this).css('padding-right').replace('px', ''));
                $(this).width(td_array[z] - paddingx);
                z += 1;
            });
        }
    });

})(jQuery)
