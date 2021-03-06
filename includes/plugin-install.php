<?php





if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Include the TGM_Plugin_Activation class.
require_once( dirname( __FILE__ ) . '/class-tgm-plugin-activation.php' );


add_action( 'tgmpa_register', 'songbook_tgmpa_register' );

function songbook_tgmpa_register( ) {

	$plugin_extensions = SONGBOOK_Settings::get_instance( )->selected_plugins( 'songbook_plugin_extension' );
        $plugins = array_filter( $plugin_extensions ); // Remove any empty array items.
        
        if ( ! $plugins ) {
            return;
        }

	$config = array(
		'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'plugins.php',            // Parent menu slug.
		'capability'   => 'install_plugins',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automtaically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.


		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'song-book' ),
			'menu_title'                      => __( 'Install Plugins', 'song-book' ),
			'installing'                      => __( 'Installing Plugin: %s', 'song-book' ), // %s = plugin name.
			'oops'                            => __( 'Something went wrong with the plugin API.', 'song-book' ),
			'notice_can_install_required'     => _n_noop(
				'Song Book requires the following plugin: %1$s.',
				'Song Book requires the following plugins: %1$s.',
				'song-book'
			), // %1$s = plugin name(s).
			'notice_can_install_recommended'  => _n_noop(
				'Song Book recommends the following plugin: %1$s.',
				'Song Book recommends the following plugins: %1$s.',
				'song-book'
			), // %1$s = plugin name(s).
			'notice_cannot_install'           => _n_noop(
				'Sorry, but you do not have the correct permissions to install the %1$s plugin.',
				'Sorry, but you do not have the correct permissions to install the %1$s plugins.',
				'song-book'
			), // %1$s = plugin name(s).
			'notice_ask_to_update'            => _n_noop(
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with Song Book: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with Song Book: %1$s.',
				'song-book'
			), // %1$s = plugin name(s).
			'notice_ask_to_update_maybe'      => _n_noop(
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'song-book'
			), // %1$s = plugin name(s).
			'notice_cannot_update'            => _n_noop(
				'Sorry, but you do not have the correct permissions to update the %1$s plugin.',
				'Sorry, but you do not have the correct permissions to update the %1$s plugins.',
				'song-book'
			), // %1$s = plugin name(s).
			'notice_can_activate_required'    => _n_noop(
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				'song-book'
			), // %1$s = plugin name(s).
			'notice_can_activate_recommended' => _n_noop(
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				'song-book'
			), // %1$s = plugin name(s).
			'notice_cannot_activate'          => _n_noop(
				'Sorry, but you do not have the correct permissions to activate the %1$s plugin.',
				'Sorry, but you do not have the correct permissions to activate the %1$s plugins.',
				'song-book'
			), // %1$s = plugin name(s).
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'song-book'
			),
			'update_link' 					  => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'song-book'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'song-book'
			),
			'return'                          => __( 'Return to Required Plugins Installer', 'song-book' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'song-book' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', 'song-book' ),
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'song-book' ),  // %1$s = plugin name(s).
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for Song Book. Please update the plugin.', 'song-book' ),  // %1$s = plugin name(s).
			'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'song-book' ), // %s = dashboard link.
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'tgmpa' ),
			'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
		),

	);
        
	tgmpa( $plugins, $config );

}

