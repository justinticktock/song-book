<?php

class SongBookFullContent {

        /**
         * A reference to an instance of this class.
         */
        private static $instance;

        /**
         * Initializes the plugin by setting filters and administration functions.
         */
        private function __construct() {


                /* allow the full book archive */
                add_filter(
					'init',
					 array( $this, 'prefix_songbook_rewrite_rule' ) 
				);

                add_filter(
					'query_vars',
					 array( $this, 'prefix_register_query_var' ) 
				);
                
                add_filter(
					'template_redirect',
					 array( $this, 'prefix_url_rewrite_templates' ) 
				);                

              
        } 

        public function prefix_songbook_rewrite_rule() {
            add_rewrite_rule( 'songbook/([^/]+)/fullsongbook', 'index.php?songbook=$matches[1]&fullsongbook=yes', 'top' );
        }

        public function prefix_register_query_var( $vars ) {
            $vars[] = 'fullsongbook';

            return $vars;
        }

        public function prefix_url_rewrite_templates() {

        if ( get_query_var( 'fullsongbook' ) && is_singular( 'songbook' ) ) {
                add_filter( 'template_include', function() {

                    // Init SONGBOOK
                    $songbook = SONGBOOK::get_instance();


                    // template single-songbook-full.php
                    if ( is_singular( 'songbook' ) && ( $template_found = $songbook->find_custom_template( 'single-songbook-full.php' ) ) )  {
                        return $template_found;
                    }

                    return get_template_directory() . '/single-songbook-full.php';
                });
            }

        }

        /**
         * Returns an instance of this class. 
         */
        public static function get_instance() {

                if( null == self::$instance ) {
                        self::$instance = new SongBookFullContent();
                } 

                return self::$instance;

        } 


} 

add_action( 'after_setup_theme', array( 'SongBookFullContent', 'get_instance' ), 20 );