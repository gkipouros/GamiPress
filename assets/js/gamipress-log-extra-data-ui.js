(function($) {
    function gamipress_update_log_tags_list() {
        var type = $('#log-data .cmb2-id-type select').val();

        // Show/hide tags based on type
        var tags_to_show = [];
        switch (type) {
            case 'event_trigger':
                tags_to_show = ['user', 'trigger-type', 'count'];
                break;
            case 'achievement_earn':
            case 'achievement_award':
                tags_to_show = ['user', 'achievement', 'achievement-type'];
                break;
            case 'points_earn':
            case 'points_award':
                tags_to_show = ['user', 'points', 'points-type', 'total-points'];
                break;
        }

        if( type === 'achievement_award' || type === 'points_award' ) {
            tags_to_show.push('admin');
        }

        var tags_to_show_selector = '#tag-' + tags_to_show.join(', #tag-');

        $('.gamipress-log-pattern-tags-list li:not(' + tags_to_show_selector + ')').hide();
        $(tags_to_show_selector).show();
    }

    gamipress_update_log_tags_list();

    $('#log-data .cmb2-id-type select').change(function() {
        var type = $(this).val();

        gamipress_update_log_tags_list();

        $( '#log-extra-data-ui').html('<span class="spinner is-active" style="float: none;"></span>');

        // Ajax request to get log extra data form
        jQuery.post(
            ajaxurl,
            {
                action: 'get_log_extra_data_ui',
                log_id: $('input#object_id').val(),
                type: type
            },
            function( response ) {
                $( '#log-extra-data-ui').html(response);
            }
        );
    });
})(jQuery);