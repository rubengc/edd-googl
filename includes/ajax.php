<?php
/**
 * Ajax
 *
 * @package     EDD\Googl\Ajax
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

add_action( 'wp_ajax_edd_googl_update_all', 'edd_googl_update_all_ajax' );
function edd_googl_update_all_ajax() {
    $args = array(
        'post_type' => 'download',
        'post_status' => 'publish'
    );

    $query = new WP_Query($args);

    $downloads = $query->get_posts();
    $response = array();

    foreach( $downloads as $download ) {
        edd_googl()->update_download_shortlink( $download->ID );

        $response[ get_permalink( $download->ID ) ] = edd_googl()->get_download_shortlink( $download->ID );
    }

    wp_send_json( $response );
}