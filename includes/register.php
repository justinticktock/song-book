<?php

/* Add capabilities and Flush your rewrite rules for plugin activation */
function song_book_do_on_activation() {

/*   $defaults = array(
      'help_note_post_types'                => array(),
      'help_note_menu_plugin'               => false,
      'help_note_simple_footnotes_plugin'   => false,
      'help_note_simple_page_ordering'   	=> false,
      'help_note_contents_page'             => '0',
      'help_note_general_enabled'           => false,
      'user_widget_enabled'                 => false,
    );

   
    $options = wp_parse_args(get_option(SONG_BOOK_OPTION), $defaults);
   
	// create the option on plugin intialisation 
    update_option(SONG_BOOK_OPTION, $options); 
*/

    //Add the selected role capabilities for use with the song book
	song_book_add_role_caps();
    
	// ATTENTION: This is *only* done during plugin activation hook in this example!
	// You should *NEVER EVER* do this on every page load!!
	flush_rewrite_rules();
    

}

register_activation_hook( SONGBOOK_MYPLUGINNAME_PATH .'song-book.php', 'song_book_do_on_activation' );

?>