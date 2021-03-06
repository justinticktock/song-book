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
         ); ?>


        <?php $wp_query = new WP_Query( $args ); ?>
    <div>
    <span class="songbookheader-icon"><?php the_title( "   " ); ?></span></br>
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
        <a class="song-icon-youtube" href="<?php the_permalink(); ?>"><?php the_title( $song_number ); ?></a></br>
    <?php } else { ?>
        <a class="song-icon-sheet" href="<?php the_permalink(); ?>"><?php the_title( $song_number ); ?></a></br>
    <?php } ?>
    

    <?php
    //echo '<p>Connection ID: ' . $post->p2p_id . '</p>';
    wp_reset_postdata(); // set $post back to original post
    ?>
	
<?php endwhile; ?>                    


	</article><!-- #post-## -->

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
