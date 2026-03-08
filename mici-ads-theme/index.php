<?php
/**
 * Mici Ads Theme — Fallback Index Template
 *
 * WordPress requires index.php; all real routes use dedicated templates.
 *
 * @package MiciAds
 */

get_header();
?>
<main class="site-main">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2><?php the_title(); ?></h2>
					<div><?php the_content(); ?></div>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No content found.', 'mici-ads' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
