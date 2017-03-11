<?php


add_action('pre_get_posts','song_book_filter_out_category');

function song_book_filter_out_category( $query ){
    
    
//    if( ! current_user_can( 'read_song' ) );
//        return;
    
     //Don't touch the admin
     if( is_admin() )
         return;

     //drop out is not a 'song' post type query
     $post_types = $query->get( 'post_type' );
     if( !empty($post_types) && 'song' != $post_types )
         return;

    //drop out is an archive_page
     $archive_page = $query->get('is_post_type_archive');
     if( $archive_page )
         return;

     //Get current tax query
     $tax_query = $query->get('tax_query');
//echo var_dump($tax_query)."</BR>";
     //Return only posts which are not in our category by 
     //adding a new element to the $tax_query
     $tax_query[] = array(
                        'taxonomy' => 'license',
                        'field' => 'slug',
                        'terms' => array( 'publicdomain', 'ccli-37814' ),
                        'operator' => 'IN'
                          );

     //If this is 'OR', then our tax query above might be ignored.
     $tax_query['relation'] = 'AND';

     $query->set('tax_query',$tax_query);
//echo var_dump( $query)."</BR>";
}


?>