<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Append new links to the Plugin admin side

add_filter( 'plugin_action_links_' . SONGBOOK::get_instance()->plugin_file , 'songbook_plugin_action_links');

function songbook_plugin_action_links( $links ) {

	$songbook = SONGBOOK::get_instance();
	$settings_link = '<a href="options-general.php?page=' . $songbook->menu . '">' . __( 'Settings' ) . "</a>";
	array_push( $links, $settings_link );
	return $links;	
}


// add action after the settings save hook.
add_action( 'tabbed_settings_after_update', 'songbook_after_settings_update' );

function songbook_after_settings_update( ) {

	flush_rewrite_rules();	
	
}



/**
 * SONGBOOK_Settings class.
 *
 * Main Class which inits the CPTs and plugin
 */
class SONGBOOK_Settings {
	
	// Refers to a single instance of this class.
    private static $instance = null;
	
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	private function __construct() {
	}
	
	/**
     * Creates or returns an instance of this class.
     *
     * @return   A single instance of this class.
     */
    public static function get_instance() {

		$songbook = SONGBOOK::get_instance();
		

		$config = array(
				'default_tab_key' => 'songbook_general',		// Default settings tab, opened on first settings page open.
				'menu_parent' => 'options-general.php',    		// menu options page slug name
				'menu_access_capability' => 'manage_options',    	// menu options page slug name.
				'menu' => $songbook->menu,    				// menu options page slug name.
				'menu_title' => $songbook->menu_title,    		// menu options page slug name.
				'page_title' => $songbook->page_title,    		// menu options page title.
				);
				
		$settings = 	apply_filters( 'SONGBOOK_settings', 
						array(								
							'songbook_general' => array(
								'title' 		=> __( 'General', 'songbook-text-domain' ),
								'description' 	=> __( 'Settings for the song book general purpose.', 'songbook-text-domain' ),	
                                                                'settings' 		=> array(		
																								array(
																										'name' 		=> 'song_book_ccli_license_number',
																										'std' 		=> '',
																										'label' 	=> __( 'CCLI License: ', 'song_book' ),
																										'desc'		=> __( "Enter the your organisation CCLI License number (e.g. 73846 ), once entered a new meta box will appear in the songs edit session.", 'song_book' ),
																										//'type'      => 'field_email_post_types_option',
																										),								
																						),
                                                        ),															
							'songbook_plugin_extension' => array(
									'title' 		=> __( 'Plugin Extensions', 'songbook-text-domain' ),
									'description' 	=> __( 'These settings are optional.  Selection of any suggested plugin here will prompt you through the installation.  The plugin will be forced active while this is selected; deselecting will not remove the plugin, you will need to manually uninstall.', 'songbook-text-domain' ),					
									'settings' 		=> array(
                                                                array(
                                                                                                                            															
																'name' 		=> 'posts_to_posts',
																'std' 		=> true,
																'label' 	=> 'Posts 2 Posts',
																'cb_label'  => __( 'Enable', 'songbook-text-domain' ),
																'desc'		=> __( 'This is a necessary plugin and is used to create the association between songs and books.', 'songbook-text-domain' ),
																'type'      => 'field_plugin_checkbox_option',
																// the following are for tgmpa_register activation of the plugin
																'slug'      			=> 'posts-to-posts',
																'plugin_dir'			=> SONGBOOK_PLUGIN_DIR,
																'required'              => true,
																'force_deactivation' 	=> false,
																'force_activation'      => true,												
																),
                                                                array(  														
																'name' 		=> 'wp_force_login',
																'std' 		=> true,
																'label' 	=> 'Force Login',
																'cb_label'  => __( 'Enable', 'songbook-text-domain' ),
																'desc'		=> __( 'This, or another pluing with the same functionality, should be used to keep the site and songbooks private for copyright reasons.  The plugin makes the site a private site for login members only.', 'songbook-text-domain' ),
																'type'      => 'field_plugin_checkbox_option',
																// the following are for tgmpa_register activation of the plugin
																'slug'      			=> 'wp-force-login',
																'plugin_dir'			=> SONGBOOK_PLUGIN_DIR,
																'required'              => false,
																'force_deactivation' 	=> false,
																'force_activation'      => true,												
																),
                                                                array(
																'name' 		=> 'songbook_wp_csv',
																'std' 		=> false,
																'label' 	=> 'WP CSV',
																'cb_label'  => __( 'Enable', 'songbook-text-domain' ),
																'desc'		=> __( 'This is a useful plugin for Administrators to export/import songs.  Once installed you can find it located under the "Tool" menu.', 'songbook-text-domain' ),
																'type'      => 'field_plugin_checkbox_option',
																// the following are for tgmpa_register activation of the plugin
																'slug'      			=> 'wp-csv',
																'plugin_dir'			=> SONGBOOK_PLUGIN_DIR,
																'required'              => false,
																'force_deactivation' 	=> false,
																'force_activation'      => true,												
																),
                                                                array(													
																'name' 		=> 'netbible-tagger',
																'std' 		=> true,
																'label' 	=> 'FT NetBible Tagger',
																'cb_label'  => __( 'Enable', 'songbook-text-domain' ),
																'desc'		=> __( 'This is a suggestion to turn bible verse references into links to net.bible.org .', 'songbook-text-domain' ),
																'type'      => 'field_plugin_checkbox_option',
																// the following are for tgmpa_register activation of the plugin
																'slug'      			=> 'netbible-tagger',
																'plugin_dir'			=> SONGBOOK_PLUGIN_DIR,
																'required'              => true,
																'force_deactivation' 	=> false,
																'force_activation'      => true,												
																),
                                                                                                                        
                                                                            
                                                                            
                                                                            
                                                                            
                                                                            
                                                            ),
							)				
						)
					);

							
        if ( null == self::$instance ) {
            self::$instance = new Tabbed_Settings( $settings, $config );
        }
 
        return self::$instance;
 
    } 
}


/**
 * sb_Settings_Additional_Methods class.
 */

/**
 * SONGBOOK_Settings_Additional_Methods class.
 */
class SONGBOOK_Settings_Additional_Methods {

}


// Include the Tabbed_Settings class.
require_once( dirname( __FILE__ ) . '/class-tabbed-settings.php' );

// Create new tabbed settings object for this plugin..
// and Include additional functions that are required.
SONGBOOK_Settings::get_instance()->registerHandler( new SONGBOOK_Settings_Additional_Methods() );


?>