<?php get_header(); // Loads the header.php template (this code is taken from 'http://justintadlock.com/archives/2012/10/16/how-i-run-a-membership-site' ?>

	<div id="content">

		<div class="hfeed">

			<h1 class="entry-title">Member Content</h1>

			<div class="alert">
				<?php if ( !is_user_logged_in() ) { ?>
					<p>This page can only be viewed by rota members.  If you're already a member, please take a moment to log into the site.</p>
				<?php } else { ?>
					<p>Sorry but this page can only be viewed by members.</p>
				<?php } ?>
			</div>

		</div><!-- .hfeed -->

	</div><!-- #content -->

<?php get_footer(); // Loads the footer.php template. ?>