/**
 * Accepts variables passed in from Shashin, and customizes the display of Highslide.
 *
 * This file is part of Shashin. Please see the Shashin.phl file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 3.0
 */


jQuery(document).ready(function($) {
    jQuery.shashin_toggle = function(base_name) {
        if ($('#' + base_name + '_section').is(':visible')) {
            $('#' + base_name + '_button').attr('src', shashin_display.url + '/images/plus.gif');
            $('#' + base_name + '_section').hide('slow');
        }

        else {
            $('#' + base_name + '_button').attr('src', shashin_display.url + '/images/minus.gif');
            $('#' + base_name + '_section').show('slow');
        }

        // returning false disables the href action
        return false;
    };

    $('#shashin_main').click(function () { return $.shashin_toggle('shashin_main'); });
    $('#shashin_picasa').click(function () { return $.shashin_toggle('shashin_picasa'); });
    $('#shashin_highslide').click(function () { return $.shashin_toggle('shashin_highslide'); });
    $('#shashin_examples').click(function () { return $.shashin_toggle('shashin_examples'); });
});


