<?php
/*
Plugin Name: Song Book
Plugin URI: 
Description: The addition of a song post type.
Version: 1.0
Author: Justin Fletcher
Author URI: http://justinandco.com
Text Domain: songbook-text-domain
Domain Path: /languages/
License: GPLv2 or later
*/




if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SONGBOOK class.
 *
 * Main Class which inits the plugin
 */
class SONGBOOK {

	// Refers to a single instance of this class.
    private static $instance = null;
	
    public  $plugin_full_path;
    public  $plugin_file = 'song-book/song-book.php';
	
    // Settings page slug	
    public  $menu = 'song-book-settings';
	
    // Settings Admin Menu Title
    public  $menu_title = 'Song Book';

    // menu item
 //   public  $menu_page = 'books.php';
    
    // Settings Page Title
    public  $page_title = 'Song Book';
    
    // Arguments
    public  $args = array();
    

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	private function __construct() {

            $this->plugin_full_path = plugin_dir_path(__FILE__) . 'song-book.php' ;

            // Set the constants needed by the plugin.
            add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );

            /* Load the functions files. */
            add_action( 'plugins_loaded', array( $this, 'includes' ), 2 );

            /* Hooks... */

            // Attached to set_current_user. Loads the plugin installer CLASS after themes are set-up to stop duplication of the CLASS.
            add_action( 'set_current_user', array( $this, 'set_current_user' ));

            // register admin side - Loads the textdomain, upgrade routine and menu item.
            add_action( 'admin_init', array( $this, 'admin_init' ));
         //   add_action( 'admin_menu', array( $this, 'admin_menu' ) );

            // register the selected-active custom post types
            add_action( 'init', array( $this, 'init' ));

            // allow dashicons to be used on the frontend
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_songbook_styles' ) );

            // allow filtering of custom taxonomies on the admin side.
            $this->args['taxonomy'] = array('album', 'publisher', 'songwriter', 'copyright', 'ccli-song', 'license', 'scripture');
            add_action( 'restrict_manage_posts', array( $this, 'restrict_song_by_taxonomy' ) );
            add_filter( 'parse_query', array( $this, 'convert_song_id_to_term_in_query' ) );


            // add and limit the 'song' cpt to the front of site search.
            add_filter( 'pre_get_posts', array( $this, 'song_cpt_search' ));

            // Load admin error messages	
            add_action( 'admin_init', array( $this, 'deactivation_notice' ) );
            add_action( 'admin_notices', array( $this, 'action_admin_notices' ) );

            // allow the plugin to uses templates and themes to replace them
            add_filter( 'template_include', array( $this, 'template_include' ) );

            /* Add the Custom Post Types to the author post listing */
            add_filter( 'pre_get_posts', array( $this, 'songs_to_loop' ) );      
            
            /* Expand the archive post listing */
            add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
            

                
	}
	

	/**
	 * Defines constants used by the plugin.
	 *
	 * @return void
	 */
	function constants() {

		// Define constants
		define( 'SONGBOOK_MYPLUGINNAME_PATH', plugin_dir_path(__FILE__) );
		define( 'SONGBOOK_PLUGIN_DIR', trailingslashit( plugin_dir_path( SONGBOOK_MYPLUGINNAME_PATH )));
		define( 'SONGBOOK_PLUGIN_URI', plugins_url('', __FILE__) );
		//define( 'SONG_BOOK_OPTION', 'song_book_option');
		
		// admin prompt constants
		define( 'SONGBOOK_PROMPT_DELAY_IN_DAYS', 30);
		define( 'SONGBOOK_PROMPT_ARGUMENT', 'songbook_hide_notice');
         

	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @return void
	 */
	function includes() {

//require_once( SONGBOOK_MYPLUGINNAME_PATH . 'uninstall.php' );       
//song_book_capability_clean_up();		

		// settings 
		require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/settings.php' );  

		// plugin registration
		require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/register.php' );  


		// custom post type capabilities
		require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/class-song-capabilities.php' );  
		require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/class-songbook-capabilities.php' );  

		// add templates to WordPress knowledge
		//require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/capabilitiesXXXXXX.php' );  
		require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/class-page-templater.php' );  

		//add the full songbook ability "/fullsongbook/" for printing 
		require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/class-songbook-fullcontent.php' );  

                
		// settings 
		//require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/class-settings.php' );  

		// if selected install the plugings and force activation
		//require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/install-plugins.php' );    

		// force songs with no license detail to be hidden
                // ..removed for now with conflict with search filter pro
		//require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/restrictions.php' );    
                
            
		// functions
		require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/functions.php' );    
                
                
                
                
                
                

	}

	/**
	 * Loads scripts correctly including..
         * Dashicons for use on the front of site.
	 *
	 * @return void
	 */
        public function enqueue_songbook_styles() {
            
            /* Register our stylesheets. */
           wp_enqueue_style( 'dashicons' );
            
            wp_register_style( 'SongBookStyleSheet', SONGBOOK_PLUGIN_URI . '/assets/css/icons.css', array(), $this->plugin_get_version() );
            wp_enqueue_style( 'SongBookStyleSheet' );
            
        }
     
	/**
	 * Initialise the plugin installs
	 *
	 * @return void
	 */
	public function set_current_user() {

            // install the plugins and force activation if they are selected within the plugin settings
// this is not working at the moment >>>??????? >>>>>>>            require_once( SONGBOOK_MYPLUGINNAME_PATH . 'includes/plugin-install.php' );
	}
        
        /**
	 * Initialise the plugin menu. 
	 *
	 * @return void
	 */
/*	public function admin_menu() {
			
            // check if no help notes are selected before adding the menu ...
            //   Roles are selected in the settings.
            //   OR the general Help Notes is selected in the settings.
            //   OR there are help note posts created and viewable.
             add_menu_page( apply_filters( 'songbook_menu_title', _x( 'Worship', 'Settings Page Title', 'songbook-text-domain' ) ), 
                     apply_filters( 'songbook_menu_title', _x( 'Worship', 'Settings Page Title', 'songbook-text-domain' ) ),
                     'read', 
                     $this->menu_page, 
                     array( &$this, 'menu_page' ), 
                     'dashicons-groups', 
                     '6.456789');
	}
  */  
	/**
	 * menu_page:
	 *
	 * @return void
	 */
	public function menu_page() {
		// This is not used as the first custom post type takes the place of the top level menu:
	}	
		
	/**
	 * Initialise the plugin by handling upgrades and loading the text domain. 
	 *
	 * @return void
	 */
	public function admin_init() {

            $plugin_current_version = get_option( 'songbook_plugin_version' );
            $plugin_new_version =  $this->plugin_get_version();

            // Admin notice hide prompt notice catch
            $this->catch_hide_notice();

            if ( 
TRUE || // DEBUG
                    empty($plugin_current_version) || $plugin_current_version < $plugin_new_version ) {

                $plugin_current_version = isset( $plugin_current_version ) ? $plugin_current_version : 0;

                $this->upgrade( $plugin_current_version );

                // set default options if not already set..
                $this->plugin_do_on_activation();

                // Update the option again after upgrade() changes and set the current plugin revision	
                update_option('songbook_plugin_version', $plugin_new_version ); 
            }

            load_plugin_textdomain('songbook-text-domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Provides an upgrade path for older versions of the plugin
	 *
	 * @param float $current_plugin_version the local plugin version prior to an update 
	 * @return void
	 */
	public function upgrade( $current_plugin_version ) {
		
		// move current database stored values into the next structure
		if ( $current_plugin_version < '0.00001' ) {

		}
	}
	

	/**
	 * Registers all required Help Notes
	 *
	 * @access public	 
	 * @return void
	 */
	public function init() {



            $this->action_init_store_user_meta();


            // loop through each songbook post to generate the songbook category name.


            // register_posttypes for the songbooks

            $songbook_labels = array(

                    'name'               => 'SongBook',
                    'singular_name'      => 'Song Book',
                    'add_new'            => 'Add New',
                    'add_new_item'       => 'Add New Book',
                    'edit_item'          => 'Edit Book',
                    'new_item'           => 'New Book',
                    'view_item'          => 'View Book',
                    'search_items'       => 'Search Books',
                    'not_found'          => 'No Books found',
                    'not_found_in_trash' => 'No Books found in Trash',
                    'parent_item_colon'  => '',
                    'menu_name'          =>  'Song Books',

            );
            
            // Place the songbook post type under the main menu by default
            // However, if the focus is on a songbook post type focus needs to change 
            // as the 'create_posts' capability for adding new songs through meta_map_caps 
            // only works if the custom post type has been added as a top level menu
  //  $show_in_menu =   $this->menu_page ;

  //  if ( isset( $_GET['post_type'] ) && ( 'songbook' === $_GET['post_type'] ) ) {
  //      $show_in_menu =  true ;
   // }
            
            $show_in_menu =  true ;
            
            $song_args = array(

                    'labels'                => $songbook_labels,
                    'public'                => true,  		// true implies the members 'content permissions'
                                                                    // meta box is available.
                    'publicly_queryable'    => true,
                    'exclude_from_search'   => false,
                    'show_ui'               => true,
                    'show_in_menu'          => $show_in_menu,
                    'show_in_admin_bar'     => true,
                    'capability_type'       => 'songbook',
                 //   'capabilities'          => array( 'create_posts' => 'create_songbooks' ),  // explicitly add create new capability            
                    'map_meta_cap'          => true,
                    'hierarchical'          => true,
                    'supports'              => array( 'title', 'thumbnail', 'page-attributes', 'author' ),
                    'has_archive'           => true,
                    'rewrite'               => true,
                    'query_var'             => true,
                    'can_export'            => true,
                    'show_in_nav_menus'     => false,
                    'menu_icon'             => 'dashicons-book',
                    'menu_position'       => 6.9
            );

            register_post_type( 'songbook', $song_args );

            
            
            
            // register_posttypes for the songs

            $song_labels = array(

                    'name'               => 'Songs',
                    'singular_name'      => 'Song',
                    'add_new'            => 'Add New',
                    'add_new_item'       => 'Add New Song',
                    'edit_item'          => 'Edit Song',
                    'new_item'           => 'New Song',
                    'view_item'          => 'View Song',
                    'search_items'       => 'Search Songs',
                    'not_found'          => 'No Songs found',
                    'not_found_in_trash' => 'No Songs found in Trash',
                    'parent_item_colon'  => '',
                    'menu_name'          =>  'Songs',

            );

            // Place the song post type under the main menu by default
            // However, if the focus is on a song post type focus needs to change 
            // as the 'create_posts' capability for adding new songs through meta_map_caps 
            // only works if the custom post type has been added as a top level menu
  //  $show_in_menu =   $this->menu_page ;

  //  if ( isset( $_GET['post_type'] ) && ( 'song' === $_GET['post_type'] ) ) {
  //      $show_in_menu =  true ;
   // }
            
            $show_in_menu =  true ;
            
            $song_args = array(

                    'labels'              => $song_labels,
                    'public'              => true,  		// true implies the members 'content permissions'
                                                                // meta box is available.
                    'publicly_queryable'  => true,
                    'exclude_from_search' => false,
                    'show_ui'             => true,
                    'show_in_menu'        => $show_in_menu,
                    'show_in_admin_bar'   => true,
                    'capability_type'     => 'song',
                  //  'capabilities'        => array( 'create_posts' => 'create_songs' ),  // explicitly add create new capability                            
                    'map_meta_cap'        => true,
                    'hierarchical'        => true,
                    'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' , 'revisions' ),
                    'has_archive'         => true,
                    'rewrite'             => true,
                    'query_var'           => true,
                    'can_export'          => true,
                    'show_in_nav_menus'   => false,
                    'menu_icon'           => 'dashicons-format-audio',
                    'menu_position'       => 6.9
            );

            register_post_type( 'song', $song_args );

            // songs_create_taxonomies

            register_taxonomy('publisher', array ('song'), array(
                    'hierarchical' => false,
                    'labels' => array( 
                                    'name' => _x( 'Publisher', 'taxonomy plural name', 'song-book' ),
                                    'singular_name' => _x( 'Publisher', 'taxonomy singular name', 'song-book' ),
                                ), 
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'rewrite' => array('slug' => 'publisher'),
                    'capabilities' => array (
                        'manage_terms' => 'manage_categories_song', //by default only admin
                        'edit_terms' => 'manage_categories_song',
                        'delete_terms' => 'manage_categories_song',
                        'assign_terms' => 'manage_categories_song'  // means administrator', 'editor', 'author', 'contributor'
                        ),
                    ) );

            register_taxonomy('songwriter', array ('song'), array(
                    'hierarchical' => false, 
                    'labels' => array( 
                                    'name' => _x( 'Songwriter', 'taxonomy plural name', 'song-book' ),
                                    'singular_name' => _x( 'Songwriter', 'taxonomy singular name', 'song-book' ),
                                ), 
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'rewrite' => array('slug' => 'songwriter'),
                    'capabilities' => array (
                        'manage_terms' => 'manage_categories_song', //by default only admin
                        'edit_terms' => 'manage_categories_song',
                        'delete_terms' => 'manage_categories_song',
                        'assign_terms' => 'manage_categories_song'  // means administrator', 'editor', 'author', 'contributor'
                        ),
                    ) );

            register_taxonomy('copyright', array ('song'), array(
                    'hierarchical' => false, 
                    'labels' => array( 
                                    'name' => _x( 'Copyright', 'taxonomy plural name', 'song-book' ),
                                    'singular_name' => _x( 'Copyright', 'taxonomy singular name', 'song-book' ),
                                ), 
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'rewrite' => array('slug' => 'copyright'),
                    'capabilities' => array (
                        'manage_terms' => 'manage_categories_song', //by default only admin
                        'edit_terms' => 'manage_categories_song',
                        'delete_terms' => 'manage_categories_song',
                        'assign_terms' => 'manage_categories_song'  // means administrator', 'editor', 'author', 'contributor'
                        ),
                    ) );

            register_taxonomy('scripture', array ('song'), array(
                    'hierarchical' => false, 
                    'labels' => array( 
                                    'name' => _x( 'Scriptures', 'taxonomy plural name', 'song-book' ),
                                    'singular_name' => _x( 'scripture', 'taxonomy singular name', 'song-book' ),
                                ), 
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'rewrite' => array('slug' => 'scripture'),
                    'capabilities' => array (
                        'manage_terms' => 'manage_categories_song', //by default only admin
                        'edit_terms' => 'manage_categories_song',
                        'delete_terms' => 'manage_categories_song',
                        'assign_terms' => 'manage_categories_song'  // means administrator', 'editor', 'author', 'contributor'
                        ),
                    ) );

                    
            
            
            
            $this->args['ccli_license_number'] = get_option( 'song_book_ccli_license_number' );

            if ( $this->args['ccli_license_number'] ) {
                register_taxonomy('ccli-song', array ('song'), array(
                    'hierarchical' => false, 
                    'labels' => array( 
                                    'name' => _x( 'license CCLI-' . $this->args['ccli_license_number']. ' Song  Ref ', 'taxonomy plural name', 'song-book' ),
                                    'singular_name' => _x( 'license CCLI-' . $this->args['ccli_license_number']. ' Song  Ref ', 'taxonomy singular name', 'song-book' ),
                                ), 
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'rewrite' => array('slug' => 'ccli-song'),
                    'capabilities' => array (
                        'manage_terms' => 'manage_categories_song', //by default only admin
                        'edit_terms' => 'manage_categories_song',
                        'delete_terms' => 'manage_categories_song',
                        'assign_terms' => 'manage_categories_song'  // means administrator', 'editor', 'author', 'contributor'
                        ),
                    ) );
            }

            register_taxonomy('license', array ('song'), array(
                    'hierarchical' => false, 
                    'labels' => array( 
                                    'name' => _x( 'License (non-CCLI)', 'taxonomy plural name', 'song-book' ),
                                    'singular_name' => _x( 'License (non-CCLI)', 'taxonomy singular name', 'song-book' ),
                                ), 
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'rewrite' => array('slug' => 'license'),
                    'capabilities' => array (
                        'manage_terms' => 'manage_categories_song', //by default only admin
                        'edit_terms' => 'manage_categories_song',
                        'delete_terms' => 'manage_categories_song',
                        'assign_terms' => 'manage_categories_song'  // means administrator', 'editor', 'author', 'contributor'
                        ),
                    ) );

            register_taxonomy('album', array ('song'), array(
                    'hierarchical' => false, 
                    'labels' => array( 
                                    'name' => _x( 'Album', 'taxonomy plural name', 'song-book' ),
                                    'singular_name' => _x( 'Album', 'taxonomy singular name', 'song-book' ),
                                ), 
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'rewrite' => array('slug' => 'album'),
                    'capabilities' => array (
                        'manage_terms' => 'manage_categories_song', //by default only admin
                        'edit_terms' => 'manage_categories_song',
                        'delete_terms' => 'manage_categories_song',
                        'assign_terms' => 'manage_categories_song'  // means administrator', 'editor', 'author', 'contributor'
                        ),
                    ) );


            // manually add the taxonomies as sub-menus
            add_action('admin_menu', array( $this, 'add_tax_menus' ));




	}
        
        
        
        
	public function restrict_song_by_taxonomy( ) {

			global $typenow;
                        
                        foreach( $this->args['taxonomy'] as $song_taxonomy ) {
                          
                            if ( $typenow == 'song' ) {
                                    $selected = isset( $_GET[$song_taxonomy] ) ? $_GET[$song_taxonomy] : '';
                                    $info_taxonomy = get_taxonomy( $song_taxonomy );
                                    if ( $info_taxonomy ) { 
                                        wp_dropdown_categories( array(
                                                                    'show_option_all' => __( "Show All {$info_taxonomy->label}" ),
                                                                    'taxonomy' => $song_taxonomy,
                                                                    'name' => $song_taxonomy,
                                                                    'orderby' => 'name',
                                                                    'selected' => $selected,
                                                                    'show_count' => true,
                                                                    'hide_empty' => true,
                                                                    )
                                                                );
                                    }
                            }

                        }
		}


	public function convert_song_id_to_term_in_query( $query ) {
	
		global $pagenow;	
		$q_vars = &$query->query_vars;

                

                foreach( $this->args['taxonomy'] as $song_taxonomy ) {

                    if ( $pagenow == 'edit.php' && isset( $q_vars['post_type'] ) && $q_vars['post_type'] == 'song' && isset( $q_vars[$song_taxonomy] ) && is_numeric( $q_vars[$song_taxonomy] ) && $q_vars[$song_taxonomy] != 0 ) {	
                            $term = get_term_by( 'id', $q_vars[$song_taxonomy], $song_taxonomy );
                            $q_vars[$song_taxonomy] = $term->slug;
                    }

                }





	}
	
	
	// ref http://stackoverflow.com/questions/10950601/missing-taxonomy-menu-when-custom-post-type-has-show-in-menu-set	
			
			
	/**
	 * manually add the taxonomies as sub-menus
	 * ref http://stackoverflow.com/questions/10950601/missing-taxonomy-menu-when-custom-post-type-has-show-in-menu-set	
	 *
	 * @access public
	 * @return null
	 */			
	public function add_tax_menus() {
	
		$key = 'songs.php';
		add_submenu_page($key, 'Publishers', 'Publishers', 'manage_categories_song', 'edit-tags.php?taxonomy=publisher&post_type=song');
		add_submenu_page($key, 'Songwriter', 'Songwriter', 'manage_categories_song', 'edit-tags.php?taxonomy=songwriter&post_type=song');
		add_submenu_page($key, 'Copyright', 'Copyright', 'manage_categories_song', 'edit-tags.php?taxonomy=copyright&post_type=song');
		add_submenu_page($key, 'Scripture', 'Scripture', 'manage_categories_song', 'edit-tags.php?taxonomy=scripture&post_type=song');
		add_submenu_page($key, 'License', 'License', 'manage_categories_song', 'edit-tags.php?taxonomy=license&post_type=song');
		add_submenu_page($key, 'Album', 'Album', 'manage_categories_song', 'edit-tags.php?taxonomy=album&post_type=song');
		add_submenu_page($key, 'CCLI Song', 'CCLI Song', 'manage_categories_song', 'edit-tags.php?taxonomy=ccli-song&post_type=song');
			

		//add_submenu_page($key, 'Song Book 2014', 'Song Book 2014', 'manage_categories', 'edit-tags.php?taxonomy=song-book-2014&post_type=song');

				
		
		// loop through each songbook post to add the songbook category name to the menu
		
		$args = array( 'post_type' => 'songbook', 'posts_per_page' => 10 );
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) : $loop->the_post();
	 
			$songbook_category_name = get_post()->post_title;;
			//$songbook_slug = get_post()->post_name;
			$songbook_slug = get_post()->post_name . 'tax';
//			add_submenu_page($key, $songbook_category_name, $songbook_category_name, 'manage_categories', 'edit-tags.php?taxonomy='.$songbook_slug.'&post_type=song');
			
		endwhile;
		
		wp_reset_query();

	}

       /**
        * This function modifies the main WordPress query to include an array of 
        * post types instead of the default 'post' post type.
        *
        * @param object $query  The original query.
        * @return object $query The amended query.
        */
       function song_cpt_search( $query ) {

           if ( ! is_admin() && $query->is_search ) {
             
				// limit site searches to the 'song' post type
               $query->set( 'post_type', array( 'song' ) );
               
               /*
                * unnecessary
                *
               if ( isset($query->query_vars['post_type']) )  {
                   
                   $query->set( 'post_type', array_merge( $query->query_vars['post_type'], array( 'song' ) ) );
                   
                    //die( var_dump( $query->query_vars['post_type']  ));
                   
               }
               */
           }

           return $query;

       }        
       
       
	/**
	 * Add capabilities and Flush your rewrite rules for plugin activation.
	 *
	 * @access public
	 * @return $settings
	 */	
	public function plugin_do_on_activation() {

		// Record plugin activation date.
		add_option('songbook_install_date',  time() ); 
		
		// create the plugin_version store option if not already present.
		$plugin_version = $this->plugin_get_version();
		update_option('songbook_plugin_version', $plugin_version ); 

		// create the tracking enabled capabilities option if not already present.
//		update_option( 'songbook_caps_created', get_option( 'songbook_caps_created', array() )); 
		
		// Add the selected role capabilities for use with the role help notes
                SONGBOOK_Capabilities::song_book_add_role_caps();
                SONG_Capabilities::song_add_role_caps();

		flush_rewrite_rules();
	}

	/**
	 * Returns current plugin version.
	 *
	 * @access public
	 * @return $plugin_version
	 */	
	public function plugin_get_version() {
		$plugin_data = get_plugin_data( $this->plugin_full_path );	
		$plugin_version = $plugin_data['Version'];
		return filter_var($plugin_version, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}
	
	/**
	 * Returns current plugin filename.
	 *
	 * @access public
	 * @return $plugin_file
	 */	
	public function get_plugin_file() {
		
		$plugin_data = get_plugin_data( $this->plugin_full_path );	
		$plugin_name = $plugin_data['Name'];
		
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		$plugins = get_plugins();
		foreach( $plugins as $plugin_file => $plugin_info ) {
			if ( $plugin_info['Name'] == $plugin_name ) return $plugin_file;
		}
		return null;
	}
		
	/**
	 * Register Plugin Deactivation Hooks for all the currently 
	 * enforced active extension plugins.
	 *
	 * @access public
	 * @return null
	 */
	public function deactivation_notice() {

		$plugins = SONGBOOK_Settings::get_instance()->selected_plugins( 'songbook_plugin_extension' );

			foreach ( $plugins as $plugin ) {

				$filename = ( isset( $plugin['filename'] ) ? $plugin['filename'] : $plugin['slug'] );
				$plugin_main_file =  trailingslashit( $plugin['plugin_dir']. $plugin['slug'] ) .  $filename . '.php' ;			

				register_deactivation_hook( $plugin_main_file, array( 'SONGBOOK', 'on_deactivation' ) );
			}

	}	 


	/**
	 * This function is hooked into plugin deactivation for 
	 * enforced active extension plugins.
	 *
	 * @access public
	 * @return null
	 */
	public static function on_deactivation()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "deactivate-plugin_{$plugin}" );

		$plugin_slug = explode( "/", $plugin );	
		$plugin_slug = $plugin_slug[0];	
		update_option( "songbook_deactivate_{$plugin_slug}", true );
		
    }

	/**
	 * Display the admin warnings.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_notices() {

		$plugins = SONGBOOK_Settings::get_instance()->selected_plugins( 'songbook_plugin_extension' );

		foreach ( $plugins as $plugin ) {
			$this->action_admin_plugin_forced_active_notices( $plugin["slug"] );
			
		}

		// Prompt for rating
                
                if ( current_user_can( 'install_plugins' ) ) {
                    $this->action_admin_rating_prompt_notices();
                }
	}

	/**
	 * Display the admin error message for plugin forced active.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_plugin_forced_active_notices( $plugin ) {

		$plugin_message = get_option("songbook_deactivate_{$plugin}");
		if ( ! empty( $plugin_message ) ) {
			?>
			<div class="error">
				  <p><?php esc_html_e(sprintf( __( 'Error the %1$s plugin is forced active with ', 'songbook-text-domain'), $plugin)); ?>
				  <a href="options-general.php?page=<?php echo $this->menu ; ?>&tab=songbook_plugin_extension"> <?php echo esc_html(__( 'Songbook Settings!', 'songbook-text-domain')); ?> </a></p>
			</div>
			<?php
			update_option("songbook_deactivate_{$plugin}", false); 
		}
	}

		
	/**
	 * Store the current users start date with Help Notes.
	 *
	 * @access public
	 * @return null
	 */
	public function action_init_store_user_meta() {
		
			// start meta for a user
			add_user_meta( get_current_user_id(), 'songbook_start_date', time(), true );
			add_user_meta( get_current_user_id(), 'songbook_prompt_timeout', time() + 60*60*24*  SONGBOOK_PROMPT_DELAY_IN_DAYS, true );
	}

	
	/**
	 * Display the admin message for plugin rating prompt.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_rating_prompt_notices( ) {

		$help_note_post_types =  get_option('songbook_post_types');
		$help_note_post_types = array_filter( (array) $help_note_post_types );  // Filter out any empty entries, if non active.	

		$number_of_help_notes_acitve 	= ( empty( $help_note_post_types ) ? 0 : count($help_note_post_types) );

		$user_responses =  array_filter( (array)get_user_meta( get_current_user_id(), SONGBOOK_PROMPT_ARGUMENT, true ));	
		if ( in_array(  "done_now", $user_responses ) ) 
			return;

		if ( current_user_can( 'install_plugins' ) && ( get_option('songbook_general_enabled') ) ) {
			
			$next_prompt_time = get_user_meta( get_current_user_id(), 'songbook_prompt_timeout', true );
			if ( ( time() > $next_prompt_time )) {
				$plugin_user_start_date = get_user_meta( get_current_user_id(), 'songbook_start_date', true );
				?>
				<div class="update-nag">
					
					<p><?php esc_html(printf( __("You've been using <b>Role Based Help Notes</b> for more than %s.  How about giving it a review by logging in at wordpress.org ?", 'songbook-text-domain'), human_time_diff( $plugin_user_start_date) )); ?>
					
					<?php if ( get_option('songbook_general_enabled') ) { ?>
						<li><?php esc_html(printf( __("The site is using General Help Notes type.", 'songbook-text-domain'))); ?>
					<?php } ?>
					
					<?php if ( $number_of_help_notes_acitve ) { ?>
						<LI><?php esc_html(printf( _n("You are using Help Notes with 1 user role.",  "You are using Help Notes with %d user roles.", $number_of_help_notes_acitve, 'songbook-text-domain'), $number_of_help_notes_acitve ) ); ?>
						<?php  for ($x=0; $x<$number_of_help_notes_acitve; $x++) echo '  :-) '; ?>
					<?php } ?>						
					</p>
					<p>

						<?php echo '<a href="' .  esc_url(add_query_arg( array( SONGBOOK_PROMPT_ARGUMENT => 'doing_now' )))  . '">' .  esc_html__( "Yes, please take me there.", 'song-book-extra-text-domain' ) . '</a> '; ?>
						
						| <?php echo ' <a href="' .  esc_url(add_query_arg( array( SONGBOOK_PROMPT_ARGUMENT => 'not_now' )))  . '">' .  esc_html__( "Not right now thanks.", 'song-book-extra-text-domain' ) . '</a> ';?>
						
						<?php
						if ( in_array(  "not_now", $user_responses ) || in_array(  "doing_now", $user_responses )) { 
							echo '| <a href="' .  esc_url(add_query_arg( array( SONGBOOK_PROMPT_ARGUMENT => 'done_now' )))  . '">' .  esc_html__( "I've already done this !", 'song-book-extra-text-domain' ) . '</a> ';
						}?>

					</p>
				</div>
				<?php
			}
		}	
	}
	
	/**
	 * Store the user selection from the rate the plugin prompt.
	 *
	 * @access public
	 * @return null
	 */
	public function catch_hide_notice() {
	
		if ( isset($_GET[SONGBOOK_PROMPT_ARGUMENT]) && $_GET[SONGBOOK_PROMPT_ARGUMENT] && current_user_can( 'install_plugins' )) {
			
			$user_user_hide_message = array( sanitize_key( $_GET[SONGBOOK_PROMPT_ARGUMENT] )) ;				
			$user_responses =  array_filter( (array)get_user_meta( get_current_user_id(), SONGBOOK_PROMPT_ARGUMENT, true ));	

			if ( ! empty( $user_responses )) {
				$response = array_unique( array_merge( $user_user_hide_message, $user_responses ));
			} else {
				$response =  $user_user_hide_message;
			}
			
			check_admin_referer();	
			update_user_meta( get_current_user_id(), SONGBOOK_PROMPT_ARGUMENT, $response );

			if ( in_array( "doing_now", (array_values((array)$user_user_hide_message ))))  {
				$next_prompt_time = time() + ( 60*60*24*  SONGBOOK_PROMPT_DELAY_IN_DAYS ) ;
				update_user_meta( get_current_user_id(), 'songbook_prompt_timeout' , $next_prompt_time );
				wp_redirect( 'http://wordpress.org/support/view/plugin-reviews/song-book' );
				exit;					
			}

			if ( in_array( "not_now", (array_values((array)$user_user_hide_message ))))  {
				$next_prompt_time = time() + ( 60*60*24*  SONGBOOK_PROMPT_DELAY_IN_DAYS ) ;
				update_user_meta( get_current_user_id(), 'songbook_prompt_timeout' , $next_prompt_time );		
			}
				
				
			wp_redirect( remove_query_arg( SONGBOOK_PROMPT_ARGUMENT ) );
			exit;		
		}
	}

        

        
        public function template_include( $template ) {

            // locate the template for taxonomies
            foreach ($this->args['taxonomy'] as $taxonomy_single ) {
                
                $template_wanted = 'taxonomy-' . $taxonomy_single . '.php';
                if ( is_tax( $taxonomy_single ) && ( $template_found = $this->find_custom_template( $template_wanted ) ) )  {
                    return $template_found;
                }            
            }

            // template single-song.php
            if ( is_singular( 'song' ) && ( $template_found = $this->find_custom_template( 'single-song.php' ) ) )  {
                return $template_found;
            }
            
            // template single-songbook.php
            if ( is_singular( 'songbook' ) && ( $template_found = $this->find_custom_template( 'single-songbook.php' ) ) )  {
                return $template_found;
            }      
       
            // template archive-songbook.php
            if ( is_archive( 'songbook' ) && ( $template_found = $this->find_custom_template( 'archive-songbook.php' ) ) )  {
                return $template_found;
            }
            
            

            // template search.php
            if ( is_search( ) && ( $template_found = $this->find_custom_template( 'song-search.php' ) ) )  {
                return $template_found;
            }
            
            // else return the original template
            return $template;
            
        }


        /**
         * Finds a custom template is available, if present allows the child or 
         * parent theme to override the plugin templates.
         *
         * @return   False if not found otherwise the template file location
         */
        public function find_custom_template( $template_wanted ) {

            $plugin_template_file = false;    

            if ( locate_template( $template_wanted ) != '' ) {

                // locate_template() returns path to file
                // if either the child theme or the parent theme have overridden the template
                $plugin_template_file = locate_template( $template_wanted );

            }
             else {


                /* 
                 * If neither the child nor parent theme have overridden the explicit template 
                 * we are allowing the pluing to use it's own template file.  However if the 
                 * plugin is missing the template file itself then we would drop out of the function
                 * returning the origianl template allowing the themes template hyerarchy to resolve
                 * to an existing template.
                 */

                if ( file_exists( SONGBOOK_MYPLUGINNAME_PATH . "template/$template_wanted" ) ) {

                    // we load the template from the 'templates' sub-directory of the directory this file is in                                
                    $plugin_template_file = SONGBOOK_MYPLUGINNAME_PATH . "template/$template_wanted";
                }

                return $plugin_template_file;


            }
        }

        /**
         * Include the Post Type in the main loop.  This is necessary to show the 
         * Taxonomies ( songwriter, publisher etc..) on the front of site in use
         * with the tag cloud listing the taxonomies
         *
         * @access public
         * @param object $query 
         * @return void
         */		
        public function songs_to_loop( $query ) {

                if( !is_admin( ) && $query->is_main_query( ) && empty( $query->query_vars['suppress_filters'] ) ) {

                        // For author queries add Help Note post types
                        if ( $query->is_tax ) {
                                $include_post_types = array( 'song', 'songbook');
                                $include_post_types[] = 'post';
                                $query->set( 'post_type', $include_post_types );
                        }                                                     
                        
                        // remove the filter after running, run only once!
                       remove_action( 'pre_get_posts', 'songs_to_loop' ); 
                }
        }            

        /**
         * Change the loop query to expand the archive listings
         *
         * @access public
         * @param object $query 
         * @return void
         */		
        public function pre_get_posts( $query ) {

            // show all taxonomy results for publisher archive pages
            if ( is_tax( 'publisher' ) && is_archive() ) {
                // list all publisher songs on the archive page
                $query->set( 'posts_per_page', -1 );
                $query->set( 'orderby', 'title' );
                $query->set( 'order', 'ASC' );
                return;
            }
            
            
            
            // show all taxonomy results for songwriters archive pages
            if ( is_tax( 'songwriter' ) && is_archive() ) {
                // list all songwriters songs on the archive page
                $query->set( 'posts_per_page', -1 );
                // order songs on the archive page
                $query->set( 'orderby', 'title' );
                $query->set( 'order', 'ASC' );               
                return;
            }
            
            // increase the search results to 50
            if ( !is_admin() && $query->is_search && $query->is_main_query() ) {
                // list more songs on the search results page
                $query->set( 'posts_per_page', 50 );
                // order songs on the archive page
                $query->set( 'orderby', 'title' );
                $query->set( 'order', 'ASC' );       
            } 
            
            
            
            }            

        /**
         * Creates or returns an instance of this class.
         *
         * @return   A single instance of this class.
         */
        public static function get_instance() {

            if ( null == self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;

        }	
    }





/**
 * Init SONGBOOK
 */
SONGBOOK::get_instance();



/* 
 * 
 */
function songs_to_songbooks_connection_types() {

    
    p2p_register_connection_type( array(
        'name' => 'songbooks_to_songs',
        'from' => 'song',
        'to' => 'songbook',
        'admin_column' => 'any',
        'sortable' => 'any',
        'admin_box' => array(
                            'show' => 'any',
                            'context' => 'normal'
                            ),
        'fields' => array(
                            'number' => array(
                                            'title' => 'Number',
                                            //'type' => 'numeric',
                                            'type' => 'text',
                                            ),
                        ),        

    ) );    
}
add_action( 'p2p_init', 'songs_to_songbooks_connection_types' );




// http://wordpress.stackexchange.com/questions/175793/get-first-video-from-the-post-both-embed-and-video-shortcodes
function get_first_embed_media( $post_id ) {

    $post = get_post($post_id);
    $content = do_shortcode( apply_filters( 'the_content', $post->post_content ) );
    $embeds = get_media_embedded_in_content( $content );

    if( !empty($embeds) ) {
        //check what is the first embed containg video tag, youtube or vimeo
        foreach( $embeds as $embed ) {
            if( strpos( $embed, 'video' ) || strpos( $embed, 'youtube' ) || strpos( $embed, 'vimeo' ) ) {
                return $embed;
            }
        }

    } else {
        //No video embedded found
        return false;
    }

}  