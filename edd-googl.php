<?php
/**
 * Plugin Name:     EDD Googl
 * Plugin URI:      https://wordpress.org/plugins/edd-googl/
 * Description:     Automatically creates a Goo.gl shortened url on published downloads
 * Version:         1.0.0
 * Author:          rubengc
 * Author URI:      http://rubengc.com
 * Text Domain:     edd-googl
 *
 * @package         EDD\Googl
 * @author          rubengc
 * @copyright       Copyright (c) rubengc
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Googl' ) ) {

    /**
     * Main EDD_Googl class
     *
     * @since       1.0.0
     */
    class EDD_Googl {

        /**
         * @var         EDD_Googl $instance The one true EDD_Googl
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_Googl
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_Googl();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_GOOGL_VER', '1.0.0' );

            // Plugin path
            define( 'EDD_GOOGL_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_GOOGL_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            // Include scripts
            require_once EDD_GOOGL_DIR . 'includes/ajax.php';
            require_once EDD_GOOGL_DIR . 'includes/scripts.php';
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
            // Register settings
            add_filter( 'edd_settings_sections_extensions', array( $this, 'settings_section' ) );
            add_filter( 'edd_settings_extensions', array( $this, 'settings' ), 1 );

            // Adds shortlink information on download page
            add_action( 'edit_form_after_title', array( $this, 'download_shortlink_box' ) );

            // Updates download shortlink on save post
            add_action( 'save_post', array( $this, 'update_download_shortlink' ) );
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_GOOGL_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_googl_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-googl' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-googl', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-googl/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-googl/ folder
                load_textdomain( 'edd-googl', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-googl/languages/ folder
                load_textdomain( 'edd-googl', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-googl', false, $lang_dir );
            }
        }

        /**
         * Add settings section
         *
         * @access      public
         * @since       1.0.0
         * @param       array $sections The existing EDD settings sections array
         * @return      array The modified EDD settings sections array
         */
        public function settings_section( $sections ) {
            $sections['edd-googl'] = __( 'EDD Googl', 'edd-googl' );

            return $sections;
        }

        /**
         * Add settings
         *
         * @access      public
         * @since       1.0.0
         * @param       array $settings The existing EDD settings array
         * @return      array The modified EDD settings array
         */
        public function settings( $settings ) {
            $edd_googl_api_key = edd_get_option( 'edd_googl_api_key' );

            // If an API Key exists, then adds an update all button
            $update_all_html = (isset($edd_googl_api_key) && !empty($edd_googl_api_key)) ? '<div id="edd-googl-update-all-container"><button id="edd-googl-update-all-button" type="button" class="button">Update All Shortlinks</button> <span>Update multiple times does not changes shortlinks, they are unique per API Key</span></div>' : '';

            $edd_googl_settings = array(
                array(
                    'id'    => 'edd_googl_header',
                    'name'  => '<strong>' . __( 'EDD Googl Settings', 'edd-googl' ) . '</strong>',
                    'desc'  => __( 'Configure EDD Googl Settings', 'edd-googl' ),
                    'type'  => 'header',
                ),
                array(
                    'id'    => 'edd_googl_api_key',
                    'name'  => __( 'Googl API Key', 'edd-googl' ),
                    'desc'  => __( 'You need enable <a href="https://console.developers.google.com/apis/api/urlshortener-json.googleapis.com/overview">URL Shortener API</a> and create an <a href="https://console.developers.google.com/apis/credentials">API Key</a> from your <a href="https://console.developers.google.com">Google developer console</a>', 'edd-googl' ) . $update_all_html,
                    'type'  => 'text',
                )
            );

            if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
                $edd_googl_settings = array( 'edd-googl' => $edd_googl_settings );
            }

            return array_merge( $settings, $edd_googl_settings );
        }

        /**
         * Adds information about stored shortlink after download title element
         *
         * @access      public
         * @since       1.0.0
         * @param       WP_Post $post The current post
         */
        public function download_shortlink_box( $post ) {
            if($post->post_type == 'download') {
                $shortlink = $this->get_download_shortlink( $post->ID );

                if($shortlink != '') {
                    ?>
                    <div id="edd-googl-shortlink-box">
                        <strong>Googl Shortlink:</strong>
                        <span
                            id="edd-googl-shortlink"><?php echo sprintf('<a href="%1$s">%1$s</a>', $shortlink); ?></span>
                        <span
                            id="edd-googl-shortlink-info"><?php echo sprintf('<a href="%1$s" class="button button-small">%2$s</a>', str_replace('goo.gl/', 'goo.gl/info/', $shortlink), 'View info'); ?></span>
                    </div>
                    <?php
                }
            }
        }

        /**
         * Shorten an url from URL Shortener API using the registered API Key
         *
         * @access      public
         * @since       1.0.0
         * @param       array $url The url to shorten
         * @return      string|boolean The shortened url or false on error
         */
        public function shorten( $url ) {
            $edd_googl_api_key = edd_get_option( 'edd_googl_api_key' );

            if(isset($edd_googl_api_key) && !empty($edd_googl_api_key)) {
                $result = wp_remote_post(
                    add_query_arg('key', $edd_googl_api_key, 'https://www.googleapis.com/urlshortener/v1/url'),
                    array(
                        'body' => json_encode( array( 'longUrl' => esc_url_raw( $url ) ) ),
                        'headers' => array('Content-Type' => 'application/json')
                    )
                );

                if (is_wp_error($result)) {
                    return false;
                }

                $result = json_decode($result['body']);

                return $result->id;
            } else {
                return false;
            }
        }

        /**
         * Updates a shorten download permalink into a meta field
         *
         * @access      public
         * @since       1.0.0
         * @param       integer $download_id The download id
         */
        public function update_download_shortlink( $download_id ) {
            $download = get_post( $download_id );

            // Only creates a shortlink if download has published
            if($download->post_status == 'publish') {
                $permalink = get_permalink( $download_id );

                $shortlink = $this->shorten($permalink);

                update_post_meta( $download_id, 'edd_googl_url', $shortlink );
            }
        }

        /**
         * Get the shorten download url
         *
         * @access      public
         * @since       1.0.0
         * @param       integer $download_id The download id
         * @return      string The shortened url or an empty string
         */
        public function get_download_shortlink( $download_id ) {
            return get_post_meta( $download_id, 'edd_googl_url', true );
        }
    }
} // End if class_exists check

/**
 * Shortcut for edd_googl()->get_download_shortlink()
 *
 * @since       1.0.0
 * @return      string The shortened url or an empty string
 */
function edd_googl_shortlink( $download_id ) {
    return edd_googl()->get_download_shortlink( $download_id );
}


/**
 * The main function responsible for returning the one true EDD_Googl
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \EDD_Googl The one true EDD_Googl
 */
function edd_googl() {
    return EDD_Googl::instance();
}
add_action( 'plugins_loaded', 'edd_googl' );
