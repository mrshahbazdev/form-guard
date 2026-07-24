(function($) {
    'use strict';

    var list = $('#fg-fields-list');
    var index = list.children().length;

    $('#fg-add-field').on('click', function() {
        var html = '<div class="fg-field-row">' +
            '<input type="text" name="fg_fields[' + index + '][label]" placeholder="Label" required>' +
            '<select name="fg_fields[' + index + '][type]">' +
                '<option value="text">Text</option>' +
                '<option value="email">Email</option>' +
                '<option value="textarea">Textarea</option>' +
                '<option value="select">Select</option>' +
                '<option value="checkbox">Checkbox</option>' +
            '</select>' +
            '<input type="text" name="fg_fields[' + index + '][options]" placeholder="Options (for select/checkbox)" class="regular-text">' +
            '<label><input type="checkbox" name="fg_fields[' + index + '][required]" value="1"> Required</label>' +
            '<button type="button" class="button fg-remove-field">Remove</button>' +
        '</div>';
        list.append(html);
        index++;
    });

    list.on('click', '.fg-remove-field', function() {
        $(this).closest('.fg-field-row').remove();
    });
})(jQuery);
