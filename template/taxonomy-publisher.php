<?php get_header(); ?>
<?php $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); ?>

<div id="main-content" class="clearfix">


	<div id="left-area">
		<?php get_template_part('includes/breadcrumbs'); ?>

		</Br><h1 class="entry-title"> <?php echo '<strong>Publisher :</strong> ' . $term->name; ?> </h1>


		<div id="entries">
			<?php if ( is_active_sidebar( '468_top_area' ) ) { ?>
				<?php if ( !dynamic_sidebar('468_top_area') ) : ?>
				<?php endif; ?>
			<?php } ?>

                        <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                            
							<?php if ( get_first_embed_media($post_id) )  { ?>
								<a class="song-icon-youtube" href="<?php the_permalink(); ?>"><?php the_title( $song_number ); ?></a></br>
							<?php } else { ?>
								<a class="song-icon-sheet" href="<?php the_permalink(); ?>"><?php the_title( $song_number ); ?></a></br>
							<?php } ?>


                        <?php endwhile; // end of the loop. ?>

			<?php if ( is_active_sidebar( '468_bottom_area' ) ) { ?>
				<?php if ( !dynamic_sidebar('468_bottom_area') ) : ?>
				<?php endif; ?>
			<?php } ?>
		</div> <!-- end #entries -->
	</div> <!-- end #left-area -->
	<?php get_sidebar(); ?>

<?php get_footer(); ?>