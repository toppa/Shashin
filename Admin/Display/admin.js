jQuery(document).ready(function($) {
    $('#shashinExamples').click(function(event) {
        if ($('#' + $(this).attr('id') + 'Section').is(':visible')) {
            $('#' + $(this).attr('id') + 'Button').attr('src', shashinDisplay.url + '/images/plus.gif');
            $('#' + $(this).attr('id') + 'Section').hide('slow');
        }

        else {
            $('#' + $(this).attr('id') + 'Button').attr('src', shashinDisplay.url + '/images/minus.gif');
            $('#' + $(this).attr('id') + 'Section').show('slow');
        }

        event.preventDefault();
    });
});


