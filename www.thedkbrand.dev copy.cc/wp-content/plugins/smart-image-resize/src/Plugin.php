<?php

namespace WP_Smart_Image_Resize;

/**
 * Class WP_Smart_Image_Resize\Plugin
 *
 * @package WP_Smart_Image_Resize\Inc
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( '\WP_Smart_Image_Resize\Plugin' ) ) :
    class Plugin
    {

        protected static $instance = null;

        /**
         * @return Plugin
         */
        public static function get_instance()
        {
            if ( is_null( static::$instance ) ) {
                static::$instance = new Plugin();
            }

            return static::$instance;
        }

        /**
         * Run plugin
         */
        public function run()
        {

            $this->define_hooks();
            $this->load_dependencies();
        }

        /**
         * Load js scripts.
         *
         * @return void
         */
        public function enqueue_scripts()
        {
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_script(
                'multi-select',
                WP_SIR_URL . 'js/multiselect.min.js',
                [ 'jquery' ],
                null,
                true
            );

            wp_enqueue_script( 'jquery-ui-progressbar' );

            wp_enqueue_script(
                WP_SIR_NAME,
                WP_SIR_URL . 'js/scripts.js',
                [ 'multi-select', 'wp-color-picker', 'jquery-ui-progressbar' ],
                WP_SIR_VERSION,
                true
            );

            wp_localize_script( 'wp-smart-image-resize', 'wp_sir_object', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'wp-sir-ajax' )
            ] );


        }

        /**
         * Load css.
         *
         * @return void
         */
        public function enqueue_styles()
        {

            wp_enqueue_style( 'wp-color-picker' );
            $wp_scripts = wp_scripts();

            wp_enqueue_style(
                'jquery-ui-theme-smoothness',
                sprintf(
                    '//ajax.googleapis.com/ajax/libs/jqueryui/%s/themes/smoothness/jquery-ui.css',
                    $wp_scripts->registered[ 'jquery-ui-core' ]->ver
                )
            );
            wp_enqueue_style(
                'multi-select',
                WP_SIR_URL . 'css/multiselect.min.css',
                [],
                WP_SIR_VERSION
            );
            wp_enqueue_style(
                WP_SIR_NAME,
                WP_SIR_URL . 'css/style.css',
                [ 'wp-color-picker', 'multi-select' ],
                WP_SIR_VERSION
            );
        }

        /**
         * Load plugin dependencies.
         *
         * @return void
         */
        public function load_dependencies()
        {
            require_once WP_SIR_DIR . 'src/vendor/autoload.php';
            require_once WP_SIR_DIR . 'src/helpers.php';
            require_once WP_SIR_DIR . 'src/functions.php';
            //require_once WP_SIR_DIR . 'src/RegenerateThumbnails.php';
            require_once WP_SIR_DIR . 'src/ImageTool.php';
            require_once WP_SIR_DIR . 'src/Admin.php';
            require_once WP_SIR_DIR . 'src/hooks.php';

            if ( extension_loaded( 'fileinfo' ) ) {
                ImageTool::get_instance()->init();
            }

            Admin::get_instance()->init();
        }

        /**
         * Load plugin text domain.
         *
         * @return void
         */
        public function set_locale()
        {
            load_plugin_textdomain(
                WP_SIR_NAME,
                false,
                WP_SIR_DIR . '/languages/'
            );
        }

        /**
         * Define run hooks.
         *
         * @return void
         */
        public function define_hooks()
        {
            add_action( 'plugins_loaded', [ $this, 'set_locale' ] );
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
        }
    }
endif;
