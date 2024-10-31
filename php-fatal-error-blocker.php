<?php
/*
 * Plugin Name: PHP Fatal Error Blocker
 * Plugin URI:  https://moreaddons.com/downloads/php-fatal-error-blocker/
 * Description: The plugin will help your WordPress Site for Crashing.
 * Version:     1.0.0
 * Author:      MoreAddons
 * Author URI:  https://moreaddons.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}
if (!defined('MA_FATAL_BLOCKER_MAIN_URL')) {
    define('MA_FATAL_BLOCKER_MAIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('MA_FATAL_BLOCKER_MAIN_PATH')) {
    define('MA_FATAL_BLOCKER_MAIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('MA_FATAL_BLOCKER_VERSION')) {
    define('MA_FATAL_BLOCKER_VERSION', '1.0.0');
}
if (!defined('MA_FATAL_BLOCKER_MAIN_IMG')) {
    define('MA_FATAL_BLOCKER_MAIN_IMG', MA_FATAL_BLOCKER_MAIN_URL . "assets/img/");
}
if (!defined('MA_FATAL_BLOCKER_MAIN_CSS')) {
    define('MA_FATAL_BLOCKER_MAIN_CSS', MA_FATAL_BLOCKER_MAIN_URL . "assets/css/");
}
if (!defined('MA_FATAL_BLOCKER_MAIN_VIEW')) {
    define('MA_FATAL_BLOCKER_MAIN_VIEW', MA_FATAL_BLOCKER_MAIN_PATH . "views/");
}

if(!class_exists('MA_Fatal_Error_Init'))
{
    require_once( MA_FATAL_BLOCKER_MAIN_PATH . 'includes/php-fatal-error-init.php' );
}

add_action('admin_notices', array('MA_Fatal_Error_Init','ma_fatal_blocker_check_activation'));
add_action('admin_menu', array('MA_Fatal_Error_Init', 'ma_fatal_blocker_menu_add'));
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'ma_fatal_error_action_link');
function ma_fatal_error_action_link($links) {
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=ma_php_fatal_blocker') . '">'.__('Configuration','ma_php_fatal_error_blocker').'</a>',
    );
    if ( array_key_exists( 'deactivate', $links ) ) {
        $links['deactivate'] = str_replace( '<a', '<a class="php-fatal-error-blocker-deactivate-link"', $links['deactivate'] );
    }
    return array_merge($plugin_links, $links);
}
function ma_php_blocker_admin_init()
{
    if (!class_exists('MoreAddons_Uninstall_feedback_Listener')) {
        require_once (MA_FATAL_BLOCKER_MAIN_PATH . "includes/class-moreaddons-uninstall.php");
    }
    $qvar = array(
        'name' => 'PHP Fatal Error Blocker',
        'version' => MA_FATAL_BLOCKER_VERSION,
        'slug' => 'php-fatal-error-blocker',
        'lang' => 'ma_php_fatal_error_blocker',
        'logo' => MA_FATAL_BLOCKER_MAIN_IMG.'logo_sm.png'
    );
    new MoreAddons_Uninstall_feedback_Listener($qvar);
}
add_action('admin_enqueue_scripts', array('MA_Fatal_Error_Init', 'ma_fatal_blocker_register_styles_scripts'));
add_action('admin_init', 'ma_php_blocker_admin_init');
register_activation_hook( __FILE__, array('MA_Fatal_Error_Init','ma_fatal_blocker_activate') );
register_deactivation_hook( __FILE__, array('MA_Fatal_Error_Init','ma_fatal_blocker_deactivate') );
