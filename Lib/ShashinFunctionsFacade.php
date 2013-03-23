<?php

class Lib_ShashinFunctionsFacade {
    public function __construct() {
    }

    public function useHook($hook, $customFunction, $priority = null, $numberOfAcceptedArgs = null) {
        if ($priority && $numberOfAcceptedArgs) {
            return add_action($hook, $customFunction, $priority, $numberOfAcceptedArgs);
        }

        elseif ($priority) {
            return add_action($hook, $customFunction, $priority);
        }

        return add_action($hook, $customFunction);
    }

    public function useFilter($filter, $customFunction, $priority = null, $numberOfAcceptedArgs = null) {
        if ($priority && $numberOfAcceptedArgs) {
            return add_filter($filter, $customFunction, $priority, $numberOfAcceptedArgs);
        }

        elseif ($priority) {
            return add_filter($filter, $customFunction, $priority);
        }

        return add_filter($filter, $customFunction);
    }

    public function getSiteUrl($pathToAppendToUrl = null, $scheme = null) {
        if (!$scheme && isset($_SERVER['HTTPS'])) {
            $scheme = 'https';
        }

        return site_url($pathToAppendToUrl, $scheme);
    }

    public function getAdminUrl($pathToAppendToUrl = null, $scheme = null) {
        if (!$scheme && is_ssl()) {
            $scheme = 'https';
        }

        elseif (!$scheme) {
            $scheme = 'admin';
        }

        return admin_url($pathToAppendToUrl, $scheme);
    }

    public function getUrlForCustomizableFile($fileName, $baseFile, $relativePath = null) {
        if (file_exists(get_stylesheet_directory() . '/' . $fileName)) {
            $url = get_bloginfo('stylesheet_directory') . '/' . $fileName;
        }

        else {
            $url = $this->getPluginsUrl($relativePath . $fileName, $baseFile);
        }

        return $url;
    }

    public function getPluginsUrl($relativePath, $baseFile) {
        return plugins_url($relativePath, $baseFile);
    }

    public function getPluginsPath() {
        return WP_PLUGIN_DIR;
    }

    public function getBasePath() {
        return ABSPATH;
    }

    public function getPluginDirectoryName($path) {
        return dirname(plugin_basename($path));
    }

    public function registerStylesheet($handle, $relativePath = false, $dependencies = false, $version = null, $media = null) {
        return wp_register_style($handle, $relativePath, $dependencies, $version, $media);
    }

    public function enqueueStylesheet($handle, $relativePath = false, $dependencies = false, $version = null, $media = null) {
        return wp_enqueue_style($handle, $relativePath, $dependencies, $version, $media);
    }

    public function enqueueScript($handle, $relativePath, $dependencies = false, $version = null, $media = null, $inFooter = false) {
        return wp_enqueue_script($handle, $relativePath, $dependencies, $version, $media, $inFooter);
    }

    public function localizeScript($handle, $objectName, $data) {
        wp_localize_script($handle, $objectName, $data);
    }

    // works with page slug, ID, or title
    public function isPage($anyPageIdentifier) {
        return is_page($anyPageIdentifier);
    }

    public function getPost($postId, $outputType = OBJECT) {
		return get_post($postId, $outputType);
	}

    public function getTermBy($field, $value, $taxonomy, $output = OBJECT, $filter = 'raw') {
        return get_term_by($field, $value, $taxonomy, $output, $filter);
    }

    public function getTermLink($term) {
        return get_term_link($term);
    }

    public function getPermalink($idOrPostObject = null) {
        if ($idOrPostObject) {
            return get_permalink($idOrPostObject);
        }

        return get_permalink();
    }

    // unfortunately WP_Http is not written to a more generic interface
    public function getHttpRequestObject() {
        require_once(ABSPATH . WPINC . '/class-http.php');
        return new WP_Http();
    }

    public function getScriptsObject() {
        global $wp_scripts;
        return $wp_scripts;
    }

    public function createAdminHiddenInputFields($label) {
        return settings_fields($label);
    }

    // echoes input fields if $echoFormField == true; returns them otherwise
    public function createNonceFields($myActionName = null, $nonceFieldName = null, $validateReferrer = true, $echoFormField = true) {
        return wp_nonce_field($myActionName, $nonceFieldName, $validateReferrer, $echoFormField);
    }

    public function addNonceToUrl($url, $nonceName) {
        return wp_nonce_url($url, $nonceName);
    }

    // returns true if security check is successful; dies otherwise
    // the default $nonceFieldName is important for nonces on WP links, where it's always _wpnonce
    public function checkAdminNonceFields($myActionName = null, $nonceFieldName = '_wpnonce') {
        return check_admin_referer($myActionName, $nonceFieldName);
    }

    public function checkPublicNonceField($nonce, $nonceName = -1) {
        return wp_verify_nonce($nonce, $nonceName);
    }

    public function setShortcodeAttributes(array $shortcodeDefaults, $userShortcode) {
        return shortcode_atts($shortcodeDefaults, $userShortcode);
    }

    public function callFunctionForNetworkSites($callback, $checkNetworkwide = true) {
        global $wpdb;

        if (function_exists('is_multisite') && is_multisite()) {
            // for a plugin uninstall, $_GET['networkwide'] is not set
            if (!$checkNetworkwide || ($checkNetworkwide && isset($_GET['networkwide']) && $_GET['networkwide'] == 1)) {
                $blogIds = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));

                foreach ($blogIds as $blogId) {
                    switch_to_blog($blogId);
                    call_user_func($callback);
                }

                restore_current_blog();
                return true;
            }
        }

        call_user_func($callback);
        return true;
    }

    public function checkEmailHasValidFormat($email) {
        return is_email($email);
    }

    public function sendEmail($to, $subject = null, $message = null, $headers = null, array $attachments = null) {
        return wp_mail($to, $subject, $message, $headers, $attachments);
    }

    /*
     * WordPress function checks for invalid UTF-8, Convert single < characters to entity,
     * strip all tags,remove line breaks, tabs and extra white space, strip octets.
     */
    public function sanitizeString($string) {
        return sanitize_text_field($string);
    }

   /*
    * Encodes < > & " ' (less than, greater than, ampersand, double quote, single quote).
    * Will never double encode entities.
    */
    public function htmlSpecialCharsOnce($string) {
        return esc_attr($string);
    }

    public function escHtml($string) {
        return esc_html($string);
    }

    public function dateI18n($dateFormat, $timestamp = false, $convertToGmt = false) {
        return date_i18n($dateFormat, $timestamp, $convertToGmt);
    }

    // Managing settings: http://www.presscoders.com/wordpress-settings-api-explained/
    // http://striderweb.com/nerdaphernalia/2008/07/consolidate-options-with-arrays/
    public function getSetting($setting) {
        return get_option($setting);
    }

    public function setSetting($setting, $value) {
        // true if value was changed, false otherwise
        return update_option($setting, $value);
    }

    public function deleteSetting($setting) {
        // true if successful, false if not
        return delete_option($setting);
    }

    // File system functions
    public function checkFileExists($path) {
        return file_exists($path);
    }

    public function requireOnce($path) {
        return require_once $path;
    }

    public function directoryName($path) {
        return dirname($path);
    }
}
