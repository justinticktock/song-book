<?php
/**
 * The template for displaying search results pages.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'song-book' ), get_search_query() ); ?></h1>
			</header><!-- .page-header -->

			<?php
			// Start the loop.
			while ( have_posts() ) : the_post(); ?>

                       
                                <article id="post-<?php the_ID(); ?>" <?php //  post_class(); ?>>
                                        <?php // twentyfifteen_post_thumbnail(); ?>

                                        <header class="entry-header">

                                            <?php if ( 'post' == get_post_type() ) : ?>
                                                    <a  href="<?php the_permalink(); ?>"></a></br>
                                            <?php endif; ?>

                                            <?php if ( 'song' == get_post_type() ) : ?>

                                                    <?php if ( get_first_embed_media($post_id) )  { ?>
                                                            <a class="song-icon-youtube" href="<?php the_permalink(); ?>"><?php the_title( $song_number ); ?></a></br>
                                                    <?php } else { ?>
                                                            <a class="song-icon-sheet" href="<?php the_permalink(); ?>"><?php the_title( $song_number ); ?></a></br>
                                                    <?php } ?>


                                            <?php endif; ?>  
                                                    
                                                    
                                        </header><!-- .entry-header -->



                                        <?php  // edit_post_link( __( 'Edit', 'song-book' ), '<footer class="entry-footer"><span class="edit-link">', '</span></footer><!-- .entry-footer -->' ); ?>

                                </article><!-- #post-## -->    

				<?php
			// End the loop.
			endwhile;

			// Previous/next page navigation.
			the_posts_pagination( array(
				'prev_text'          => __( 'Previous page', 'song-book' ),
				'next_text'          => __( 'Next page', 'song-book' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'song-book' ) . ' </span>',
			) );

		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'content', 'none' );

		endif;
		?>

		</main><!-- .site-main -->
	</section><!-- .content-area -->

<?php get_footer(); ?>
