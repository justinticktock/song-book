<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */



// remove sidebars.
$songbook = SONGBOOK::get_instance();
wp_enqueue_style(
        'full_songbook_page', 
        plugins_url( 'js/full-songbook.css' , __FILE__ ),
        null,
        $songbook->plugin_get_version( ), 
        'all');     



global $post_id;

get_header(); ?>


	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		

    <?php

     $args= array(  
                    'connected_type' => 'songbooks_to_songs',
                    'connected_items' => get_queried_object(),
                    'connected_orderby' => 'number',
                    'nopaging' => true,  
                    //'posts_per_page' => 100,
     ); ?>


    <?php $wp_query = new WP_Query( $args ); ?>
                    <div>
                    <H1 class="songbookheader-icon"><?php the_title( "   Songbook: " ); ?></H1></br>
                    </div>  </Br>                                    
                                                        
<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

        <?php 
        if ( p2p_get_meta( get_post()->p2p_id, 'number', true ) ) {
            $song_number =  "   " . p2p_get_meta( get_post()->p2p_id, 'number', true ) . " : "; 
        } else {
            $song_number =  "      ";
        }
            ?>

        <?php if ( get_first_embed_media( $post_id ) )  { ?>
            <H1 class="song-icon-youtube" href=""><?php the_title( $song_number ); ?></H1></br>   
             <!-- get content less youtube embeds -->
            <?php remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 ); ?>   
        <?php } else { ?>
            <H1 class="song-icon-sheet" href="<?php the_permalink(); ?>"><?php the_title( $song_number ); ?></H1></br>
        <?php } ?>
        <p <?php the_content(); ?></p></br>

        <?php wp_reset_postdata(); ?>
        <?php //echo '<p>Connection ID: ' . $post->p2p_id . '</p>'; ?>




        <!-- Add the related taxonomy information -->
        <p>

        <?php

        /* 
         * echo out the copyright info with link
         */
        //$copyright = get_the_term_list( $post->ID, 'copyright', '<strong>Copyright:</strong> ', ', ', '' );  
        //echo  $copyright . '</Br>' ;


        // echo out the copyright info without link
        $terms = get_the_terms( null, 'copyright' );
         if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
             echo '<strong>Copyright:</strong> ';
             foreach ( $terms as $term ) {
               echo $term->name . ', ';

             }
             echo '</Br>';
         }




        /* 
         * echo out the publisher info with link
         */
        //$publisher = get_the_term_list( $post->ID, 'publisher', '<strong>Publisher:</strong> ', ', ', '' );  
        //if ( ! empty( $publisher ) && ! is_wp_error( $publisher ) ){
        //     echo  $publisher . '</Br>' ;
        //}


        /* 
         * echo out the publisher info without link
        */                                       
        $terms = get_the_terms( null, 'publisher' );
         if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
             echo '<strong>Publisher:</strong> ';
             foreach ( $terms as $term ) {
               echo $term->name . ', ';

             }
             echo '</Br>';
         }


        /* 
         * echo out the ccli license song number info
         * (goto link e.g. http://uk.search.ccli.com/songs/6428767 )
         */
        $song_book = SONGBOOK::get_instance( );
        if ( $song_book->args['ccli_license_number'] ) {

            add_filter('term_link', 'song_term_link_filter', 10, 3);
            $ccli_licensed_song = get_the_term_list( $post->ID, 'ccli-song', '<strong>CCLI</strong> reference <strong>' . $song_book->args['ccli_license_number'] . '</strong> ( song number: ', ', ', ' )' );  
            echo  $ccli_licensed_song . '</Br>' ; 
            remove_filter('term_link', 'song_term_link_filter', 10, 3);

        }




        /* 
         * echo out the songwriter info with link

        $songwriter = get_the_term_list( $post->ID, 'songwriter', '<strong>Authors:</strong> ', ', ', '' );  
        if ( ! empty( $songwriter ) && ! is_wp_error( $songwriter ) ){
             echo  $songwriter . '</Br>' ;
        }
        */



        /* 
         * echo out the songwriter info without link
        */                                        
        $terms = get_the_terms( null, 'songwriter' );
         if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
             echo '<strong>Song Wrtier:</strong> ';
             foreach ( $terms as $term ) {
               echo $term->name . ', ';

             }
             echo '</Br>';
         }






        /* 
         * echo out the album info info with link
         */
        //$album = get_the_term_list( $post->ID, 'album', '<strong>Album:</strong> ', ', ', '' );  
        //echo  $album . '</Br>' ;

        /* 
         * echo out the album info without link
         */                                        
        $terms = get_the_terms( null, 'album' );
         if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
             echo '<strong>Album:</strong> ';
             foreach ( $terms as $term ) {
               echo $term->name . ', ';

             }
             echo '</Br>';
         }                                         




        /* 
         * echo out the license info with link
         */
        //$license = get_the_term_list( $post->ID, 'license', '<strong>License (other):</strong> ', ', ', '' );  
        //echo  $license . '</Br>' ;

        /* 
         * echo out the license info without link
         */                                        
        $terms = get_the_terms( null, 'license' );
         if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
             echo '<strong>License (other):</strong> ';
             foreach ( $terms as $term ) {
               echo $term->name . ', ';

             }
             echo '</Br>';
         }   


        /* 
         * echo out the bible verses info without link
         */                                        
        $terms = get_the_terms( null, 'scripture' );
         if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
             echo '<strong>Scripture :</strong> ';
             foreach ( $terms as $term ) {
               echo $term->name . ', ';

             }
             echo '</Br>';
         }   
        ?>
        </Br></Br>



    		
				
				
				
				        

<?php endwhile; ?>                    

            
                                               
	</article><!-- #post-## -->

	</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
