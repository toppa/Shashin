<?php
/**
 * Display informational or error message in the Shashin admin panels.
 *
 * This file is part of Shashin. Please see the Shashin.phl file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 3.0
 * @package Shashin
 * @subpackage AdminPanels
 *
 */

echo '<div id="message" class="updated fade"><p>' . $message .'</p></div>';
unset($message);

if ($db_error == true) {
    global $wpdb;
    $current_error_setting = $wpdb->show_errors;
    $wpdb->show_errors();
    $wpdb->print_error();
    $wpdb->show_errors($current_error_setting);
    $db_error = false;
}

?>
