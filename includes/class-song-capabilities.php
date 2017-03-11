<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * SONG_Capabilities class.
 */
class SONG_Capabilities {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( ) {
	
            // Add Meta Capability Handling 
            add_filter( 'map_meta_cap', array( $this, 'song_map_meta_cap' ), 10, 4);
	}

	/**
	 * song_add_role_caps function.
	 *
	 * @access public
	 * @return void
	 */
        public static function song_add_role_caps() {

            global $wp_roles;

            // Load Roles if not set
            if ( ! isset( $wp_roles ) )
                $wp_roles = new WP_Roles();

            add_role(
               'songbook_editor',
               __( 'Songbook Editor', 'user-upgrade-capability' )
               );

            $administrator   = get_role('administrator');
            //$subscriber      = get_role('subscriber');
            $songbook_editor   = get_role('songbook_editor');

            $capability_type = 'song';

            // add subscriber capabilities
            //$subscriber->add_cap( "edit_{$capability_type}s" );
            //$subscriber->add_cap( "edit_others_{$capability_type}s" );
            //$subscriber->add_cap( "publish_{$capability_type}s" );
            //$subscriber->add_cap( "read_private_{$capability_type}s" );
            //$subscriber->add_cap( "delete_{$capability_type}s" );
            //$subscriber->add_cap( "delete_private_{$capability_type}s" );
            //$subscriber->add_cap( "delete_published_{$capability_type}s" );
            //$subscriber->add_cap( "delete_others_{$capability_type}s" );
            //$subscriber->add_cap( "edit_private_{$capability_type}s" );
            //$subscriber->add_cap( "edit_published_{$capability_type}s" );     
            //$subscriber->add_cap( "create_{$capability_type}s" );  

            // add administrator capabilities
            // don't allocate any of the three primitive capabilities to a users role
            $administrator->add_cap( "edit_{$capability_type}" );
            $administrator->add_cap( "read_{$capability_type}" );
            $administrator->add_cap( "delete_{$capability_type}" );

            // add administrator capabilities
            $administrator->add_cap( "edit_{$capability_type}s" );
            $administrator->add_cap( "edit_others_{$capability_type}s" );
            $administrator->add_cap( "publish_{$capability_type}s" );
            $administrator->add_cap( "read_private_{$capability_type}s" );
            $administrator->add_cap( "delete_{$capability_type}s" );
            $administrator->add_cap( "delete_private_{$capability_type}s" );
            $administrator->add_cap( "delete_published_{$capability_type}s" );
            $administrator->add_cap( "delete_others_{$capability_type}s" );
            $administrator->add_cap( "edit_private_{$capability_type}s" );
            $administrator->add_cap( "edit_published_{$capability_type}s" );
            $administrator->add_cap( "manage_categories_{$capability_type}" );
            $administrator->add_cap( "create_{$capability_type}s" );
                

             // add songbook_editor capabilities
            $songbook_editor->add_cap( "edit_{$capability_type}s" );
            $songbook_editor->add_cap( "edit_others_{$capability_type}s" );
            $songbook_editor->add_cap( "publish_{$capability_type}s" );
            $songbook_editor->add_cap( "read_private_{$capability_type}s" );
            $songbook_editor->add_cap( "delete_{$capability_type}s" );
            $songbook_editor->add_cap( "delete_private_{$capability_type}s" );
            $songbook_editor->add_cap( "delete_published_{$capability_type}s" );
            $songbook_editor->add_cap( "delete_others_{$capability_type}s" );
            $songbook_editor->add_cap( "edit_private_{$capability_type}s" );
            $songbook_editor->add_cap( "edit_published_{$capability_type}s" );
            $songbook_editor->add_cap( "manage_categories_{$capability_type}" );
            $songbook_editor->add_cap( "create_{$capability_type}s" );
            
        }
        




    public function song_map_meta_cap( $caps, $cap, $user_id, $args ) {

        if ( "map_meta_cap" ==  $cap){
          //  var_dump($cap);
        }
        $capability_type = 'song';

            if ( ("edit_{$capability_type}" == $cap || "delete_{$capability_type}" == $cap || "read_{$capability_type}" == $cap )) {
                            $post = get_post( $args[0] );
                            $post_type = get_post_type_object( $post->post_type );

                            /* Set an empty array for the caps. */
                            $caps = array();

            }

            /* If editing a song, assign the required capability. */
            if ( "edit_{$capability_type}" == $cap ) {

                    if( $user_id == $post->post_author )
                            $caps[] = $post_type->cap->edit_posts;
                    else
                            $caps[] = $post_type->cap->edit_others_posts;
            }

            /* If deleting a song, assign the required capability. */
            else if( "delete_{$capability_type}" == $cap ) {

                    if( isset($post->post_author ) && $user_id == $post->post_author  && isset($post_type->cap->delete_posts) )
                            $caps[] = $post_type->cap->delete_posts;
                    elseif (isset($post_type->cap->delete_others_posts))
                            $caps[] = $post_type->cap->delete_others_posts;
            }

            /* If reading a private song, assign the required capability. */
            elseif( "read_{$capability_type}" == $cap ) {

                    if( 'private' != $post->post_status )
                            $caps[] = 'read';
                    elseif ( $user_id == $post->post_author )
                            $caps[] = 'read';
                    else
                            $caps[] = $post_type->cap->read_private_posts;
          }	

            /* Return the capabilities required by the user. */
            return $caps;

    }        
}

new SONG_Capabilities( );