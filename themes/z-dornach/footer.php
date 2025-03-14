<?php

/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Sydney
 */
?>
</div>
</div>
</div><!-- #content -->

<?php do_action('sydney_before_footer'); ?>

<?php if (is_active_sidebar('footer-1')) : ?>
	<?php get_sidebar('footer'); ?>
<?php endif; ?>

<a class="go-top">
	<img src="./wp-content/uploads/arrow_up_icon.png" alt="arrow-up" style="width:32px;" />
</a>

<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="site-info container">

		<div style="width: 100%;">

			<div class="d-footer-part-left">
				<span>
					<?php
					echo sprintf("Dornach - %s", date("Y"));
					?>
				</span>
			</div>

			<!--
			  <div class="d-footer-part-right">
			 	<?php
					wp_nav_menu(array('theme_location' => 'd-menu-1', 'container_class' => 'd_menu_footer'));
					?>
			  </div>
			  -->

		</div>

	</div><!-- .site-info -->
</footer><!-- #colophon -->

<?php do_action('sydney_after_footer'); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>