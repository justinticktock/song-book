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

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		
			</Br><h1 class="entry-title"> <?php echo '<strong>Song Books :</strong> ' ; ?> </h1>
			
			
			<?php
			
			 $args= array(  
			 	'posts_per_page' => 7,  
			 	'post_type' => 'songbook',
				'paged' => get_query_var('paged'),
                                'orderby' => 'title',
                                'order'   => 'menu_order',
                                //'order'   => 'DESC',
                                'order'   => 'ASC',
			 ); ?>
			<?php $original_query = $wp_query; ?>
			<?php $wp_query = null; ?>			
			<?php $wp_query = new WP_Query( $args ); ?>

                        <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                            
                            <a class="songbook-icon"  href="<?php the_permalink(); ?>"><?php echo the_title( "  " ); ?></a>
                            </br>

                        <?php endwhile; // end of the loop. ?>


			<?php $wp_query = null; ?>
			<?php $wp_query = $original_query; ?>
			<?php wp_reset_postdata(); ?>

	</article><!-- #post-## -->

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
