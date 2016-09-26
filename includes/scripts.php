<?php
/**
 * Scripts
 *
 * @package     EDD\Googl\Scripts
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @global      array $edd_settings_page The slug for the EDD settings page
 * @global      string $post_type The type of post that we are editing
 * @return      void
 */
function edd_google_admin_scripts( $hook ) {
    global $edd_settings_page, $post_type;

    if( ($hook == 'post.php' && $post_type == 'download') || $hook == $edd_settings_page ) {
        wp_enqueue_script( 'edd_plugin_name_admin_js', EDD_GOOGL_URL . '/assets/js/edd-googl.js', array( 'jquery' ) );
        wp_enqueue_style( 'edd_plugin_name_admin_css', EDD_GOOGL_URL . '/assets/css/edd-googl.css' );
    }
}
add_action( 'admin_enqueue_scripts', 'edd_google_admin_scripts', 11 );