<?php
/**
 * Template name: WooCommerce - My Account
 *
 * This template adds My account to the sidebar.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.19.0
 */

get_header(); ?>

<?php do_action( 'flatsome_before_page' ); ?>

<?php wc_get_template( 'myaccount/header.php' ); ?>

<div class="page-wrapper my-account mb">
	<div class="container" role="main">

		<?php if ( is_user_logged_in() ) { ?>

			<div class="row vertical-tabs">
				<div class="large-3 col col-border">
					<?php wc_get_template( 'myaccount/account-user.php' ); ?>

					<?php do_action( 'woocommerce_before_account_navigation' ); ?>

					<nav class="woocommerce-MyAccount-navigation" aria-label="<?php esc_html_e( 'Account pages', 'woocommerce' ); ?>">
						<ul id="my-account-nav" class="account-nav nav nav-line nav-uppercase nav-vertical mt-half">
							<?php wc_get_template( 'myaccount/account-links.php' ); ?>
						</ul>
					</nav>

					<?php do_action( 'woocommerce_after_account_navigation' ); ?>
				</div>

				<div class="large-9 col">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php the_content(); ?>
					<?php endwhile; // end of the loop. ?>
				</div>
			</div>

		<?php } else { ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php the_content(); ?>

			<?php endwhile; // end of the loop. ?>

		<?php } ?>

	</div>
</div>

<?php do_action( 'flatsome_after_page' ); ?>

<?php get_footer(); ?>
