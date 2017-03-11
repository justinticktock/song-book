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

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php p2p_type( 'songbooks_to_songs' )->each_connected( $wp_query ); ?>
		
		<?php 
		// Start the loop.
		// Find connected songbooks (for all songs)

		while ( have_posts() ) : the_post(); ?>	

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<!-- add book link for navigation -->   

			<header class="entry-header">
				
				<!-- Add the Related Songbooks above the song title -->		

				<?php if ( $post->connected ) { ?>
					<strong>Related Songbooks:</strong>
				<?php } ?>
				
				
				<ul class="song" >
				<?php
				foreach ( $post->connected as $post ) : setup_postdata( $post );
					
					if ( p2p_get_meta( get_post()->p2p_id, 'number', true ) ) {
						$song_number =  " (" . p2p_get_meta( get_post()->p2p_id, 'number', true ) . ") "; 
					} else {
						$song_number =  "";
					}

					?>
					<li><a class="songbookheader-icon" href="<?php the_permalink(); ?>"><?php the_title( null, $song_number ); ?></a></li>
					<?php

					
				endforeach;

				wp_reset_postdata(); // set $post back to original post
				?>
					</ul>
			

				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</header><!-- .entry-header -->

			<div class="entry-content">
				<?php the_content(); ?>

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
                                        $publisher = get_the_term_list( $post->ID, 'publisher', '<strong>Publisher:</strong> ', ', ', '' );  
                                        echo  $publisher . '</Br>' ;

                                        /* 
                                         * echo out the publisher info without link
                                                                               
                                        $terms = get_the_terms( null, 'publisher' );
                                         if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                                             echo '<strong>Publisher:</strong> ';
                                             foreach ( $terms as $term ) {
                                               echo $term->name . ', ';

                                             }
                                             echo '</Br>';
                                         }
                                        */  
                                         
                                                                               
                                        
                                        
                                        
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
                                         */
                                        $songwriter = get_the_term_list( $post->ID, 'songwriter', '<strong>Authors:</strong> ', ', ', '' );  
                                        echo  $songwriter . '</Br>' ;

                                        /* 
                                         * echo out the songwriter info without link
                                                                               
                                        $terms = get_the_terms( null, 'songwriter' );
                                         if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                                             echo '<strong>Song Wrtier:</strong> ';
                                             foreach ( $terms as $term ) {
                                               echo $term->name . ', ';

                                             }
                                             echo '</Br>';
                                         }
                                        */  
                                         
                                         
                                         
                                         
                                         
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
                                         
                                         
                                        echo "</p>";
				
				
				
				
                                        ?>
				
				
				
				
				
				
				
				
				
				
				
			</div><!-- .entry-content -->

			<?php edit_post_link( __( 'Edit', 'twentyfifteen' ), '<footer class="entry-footer"><span class="edit-link">', '</span></footer><!-- .entry-footer -->' ); ?>

		</article><!-- #post-## -->


		
		

		
		<?php
		// End the loop.
		endwhile;
		?>

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
