<?php
/**
 * Menu Renderer Class
 *
 * @package AnimatedFullscreenMenu
 */

namespace AnimatedFullscreenMenu\Frontend;

use AnimatedFullscreenMenu\Utilities\Options;
use AnimatedFullscreenMenu\Integrations\WooCommerce;
use AnimatedFullscreenMenu\Integrations\Polylang;

/**
 * Handles rendering of the fullscreen menu.
 */
class MenuRenderer {

	/**
	 * Options instance.
	 *
	 * @var Options
	 */
	private Options $options;

	/**
	 * Plugin path.
	 *
	 * @var string
	 */
	private string $plugin_path;

	/**
	 * Plugin URL.
	 *
	 * @var string
	 */
	private string $plugin_url;

	/**
	 * Whether user has pro license.
	 *
	 * @var bool
	 */
	private bool $is_pro;

	/**
	 * Constructor.
	 *
	 * @param Options $options     Options instance.
	 * @param string  $plugin_path Plugin directory path.
	 * @param string  $plugin_url  Plugin directory URL.
	 */
	public function __construct( Options $options, string $plugin_path, string $plugin_url ) {
		$this->options     = $options;
		$this->plugin_path = $plugin_path;
		$this->plugin_url  = $plugin_url;
		$this->is_pro      = $this->check_pro_status();
	}

	/**
	 * Check if user has pro license.
	 *
	 * @return bool
	 */
	private function check_pro_status(): bool {
		return false;
	}

	/**
	 * Render the menu.
	 *
	 * @return void
	 */
	public function render(): void {
		// Check if menu should be hidden on this page.
		if ( $this->options->should_hide_on_current_page() ) {
			return;
		}

		// Check testing mode.
		if ( $this->options->is_testing_mode() && ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Get all settings.
		$settings = $this->get_render_settings();

		// Render schema.org markup if enabled.
		$this->render_schema_markup( $settings );

		// Render inline styles.
		$this->render_inline_styles( $settings );

		// Render the menu HTML.
		$this->render_menu_html( $settings );
	}

	/**
	 * Get all settings needed for rendering.
	 *
	 * @return array
	 */
	private function get_render_settings(): array {
		$settings = $this->options->get_all();

		return array(
			// Button settings.
			'button_image'          => $this->get_pro_setting( $settings, 'animatedfsm_button_image', '' ),
			'button_position'       => $this->get_pro_setting( $settings, 'animatedfsm_button_position', 'right_top' ),
			'disable_button'        => 'on' === ( $settings['animatedfsm_disable_button'] ?? '' ),
			'unfix_button'          => $this->is_pro && 'on' === ( $settings['animatedfsm_unfix_button'] ?? '' ),

			// Design settings.
			'background01'          => $settings['animatedfsm_background01'] ?? '#000000',
			'background02'          => $settings['animatedfsm_background02'] ?? '#45aacc',
			'background_image'      => $settings['animatedfsm_backgroundimage'] ?? '',
			'background_video'      => $this->get_pro_setting( $settings, 'animatedfsm_backgroundvideo', '' ),
			'text_color'            => $settings['animatedfsm_textcolor'] ?? '#FFFFFF',
			'font'                  => $settings['animatedfsm_font'] ?? 'inherit',
			'font_weight'           => $settings['animatedfsm_fontweight'] ?? '400',
			'font_size'             => $this->get_pro_setting( $settings, 'animatedfsm_fontsize', '' ),
			'animation'             => $settings['animatedfsm_animation'] ?? 'opacity',
			'columns'               => $this->get_pro_setting( $settings, 'animatedfsm_number_of_columns', '1' ),
			'hover_effect'          => $settings['animatedfsm_hoverfocus'] ?? 'animation_line',
			'hover_background'      => $settings['animatedfsm_hoverbackground'] ?? 'rgba(0,0,0,0.85)',
			'text_align'            => $this->get_pro_setting( $settings, 'animatedfsm_text_align', 'align_left' ),
			'scroll'                => 'on' === ( $settings['animatedfsm_scroll'] ?? '' ),
			'side_menu'             => 'on' === ( $settings['animatedfsm_sidemenu'] ?? '' ),

			// Text shadow (PRO).
			'text_shadow'           => $this->get_text_shadow( $settings ),

			// Menu settings.
			'menu_id'               => $settings['animatedfsm_menuselected'] ?? 'none',
			'html_content'          => $settings['animatedfsm_html'] ?? '',
			'social_icons'          => $settings['socialicons_group'] ?? array(),
			'privacy_policy'        => 'on' === ( $settings['animatedfsm_privacy_on'] ?? '' ),
			'show_search'           => 'on' === ( $settings['animatedfsm_searchbar_on'] ?? '' ),
			'search_placeholder'    => $settings['animatedfsm_searchbar_placeholder'] ?? 'Search something...',
			'language_switcher'     => 'on' === ( $settings['animatedfsm_languageswitcher'] ?? '' ),
			'schema_org'            => 'on' === ( $settings['animatedfsm_add_schemaorg'] ?? '' ),

			// Mode settings.
			'mobile_only'           => 'on' === ( $settings['animatedfsm_mobile_only'] ?? '' ),
			'anchor_mode'           => 'on' === ( $settings['animatedfsm_anchor'] ?? '' ),
			'autohide_scroll'       => $this->is_pro && 'on' === ( $settings['animatedfsm_autohide_scroll'] ?? '' ),
			'multi_level'           => $this->is_pro && 'on' === ( $settings['animatedfsm_multi_level_menu'] ?? '' ),
			'open_levels'           => $this->is_pro && 'on' === ( $settings['animatedfsm_openlevels'] ?? '' ),
			'move_to_element'       => $this->get_pro_setting( $settings, 'animatedfsm_element_class', '' ),

			// Lateral menu.
			'lateral_menu'          => 'on' === ( $settings['animatedfsm_lateralmenu'] ?? '' ),
			'lateral_pages'         => $settings['animatedfsm_lateralmenu_pages'] ?? array(),

			// WooCommerce.
			'woocommerce_menu'      => 'on' === ( $settings['animatedfsm_woocommerce_on'] ?? '' ),
			'woocommerce_cart'      => $this->is_pro && 'on' === ( $settings['animatedfsm_woocommerce_cart_on'] ?? '' ),
		);
	}

	/**
	 * Get a PRO-only setting.
	 *
	 * @param array  $settings All settings.
	 * @param string $key      Setting key.
	 * @param mixed  $default  Default value.
	 * @return mixed
	 */
	private function get_pro_setting( array $settings, string $key, $default ) {
		if ( ! $this->is_pro ) {
			return $default;
		}
		return $settings[ $key ] ?? $default;
	}

	/**
	 * Get text shadow CSS value.
	 *
	 * @param array $settings All settings.
	 * @return string
	 */
	private function get_text_shadow( array $settings ): string {
		if ( ! $this->is_pro ) {
			return '';
		}

		if ( 'on' !== ( $settings['animatedfsm_textshadow_activate'] ?? '' ) ) {
			return '';
		}

		return sprintf(
			'%spx %spx %spx %s',
			$settings['animatedfsm_textshadow_horizontal'] ?? '2',
			$settings['animatedfsm_textshadow_vertical'] ?? '2',
			$settings['animatedfsm_textshadow_blur_radius'] ?? '8',
			$settings['animatedfsm_textshadow_color'] ?? '#000000'
		);
	}

	/**
	 * Render schema.org JSON-LD markup.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_schema_markup( array $settings ): void {
		if ( ! $settings['schema_org'] ) {
			return;
		}

		$schema = array(
			'@context' => 'http://schema.org',
			'@type'    => 'SiteNavigationElement',
			'name'     => get_bloginfo( 'name' ),
			'url'      => get_site_url(),
		);

		printf(
			'<script type="application/ld+json">%s</script>',
			wp_json_encode( $schema )
		);
	}

	/**
	 * Render inline styles.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_inline_styles( array $settings ): void {
		// Font styles.
		if ( 'inherit' !== $settings['font'] ) {
			$this->render_font_styles( $settings );
		}

		// Typography styles.
		$this->render_typography_styles( $settings );

		// Background image.
		if ( ! empty( $settings['background_image'] ) ) {
			$this->render_background_image_styles( $settings['background_image'] );
		}

		// Background video.
		if ( ! empty( $settings['background_video'] ) ) {
			$this->render_background_video_script( $settings['background_video'] );
		}

		// Main styles.
		$this->render_main_styles( $settings );
	}

	/**
	 * Render font family styles.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_font_styles( array $settings ): void {
		?>
		<style>
		.animatedfsmenu,
		.afsmenu_search .search_submit,
		input[type="text"],
		.afs-cart-title {
			font-family: <?php echo esc_attr( $settings['font'] ); ?> !important;
		}
		</style>
		<?php
	}

	/**
	 * Render typography styles.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_typography_styles( array $settings ): void {
		?>
		<style>
		.animatedfsmenu a,
		.afs-cart-title {
			<?php if ( $settings['text_shadow'] ) : ?>
			text-shadow: <?php echo esc_attr( $settings['text_shadow'] ); ?>;
			<?php endif; ?>
			font-weight: <?php echo esc_attr( $settings['font_weight'] ); ?> !important;
			<?php if ( $settings['font_size'] ) : ?>
			font-size: <?php echo esc_attr( $settings['font_size'] ); ?> !important;
			<?php endif; ?>
		}
		</style>
		<?php
	}

	/**
	 * Render main CSS styles.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_main_styles( array $settings ): void {
		?>
		<style>
		<?php if ( intval( $settings['columns'] ) > 1 ) : ?>
		@media screen and (min-width: 768px) {
			ul.afsmenu {
				columns: <?php echo esc_attr( $settings['columns'] ); ?>;
			}
		}
		<?php endif; ?>

		<?php if ( $settings['unfix_button'] ) : ?>
		.animatedfsmenu .animatedfsmenu-navbar-toggler,
		.animatedfsmenu {
			position: absolute;
		}
		<?php endif; ?>

		.turbolinks-progress-bar,
		.animatedfsmenu {
			background-color: <?php echo esc_attr( $settings['background01'] ); ?>;
		}

		.animatedfsmenu.navbar-expand-md,
		.animatedfsmenu.navbar-expand-ht {
			background-color: <?php echo esc_attr( $settings['background02'] ); ?> !important;
		}

		.animatedfsmenu button:focus,
		.animatedfsmenu button:hover {
			background: <?php echo esc_attr( $settings['background01'] ); ?> !important;
		}

		.animatedfsmenu-navbar-toggler {
			background: <?php echo esc_attr( $settings['background01'] ); ?>;
		}

		.animatedfs_menu_list a,
		.afsmenu_search input[type="text"],
		.afs-cart-title {
			color: <?php echo esc_attr( $settings['text_color'] ); ?> !important;
		}

		.animatedfs_menu_list li > a:before,
		.animatedfsmenu .animatedfsmenu-navbar-toggler .bar {
			background: <?php echo esc_attr( $settings['text_color'] ); ?> !important;
		}

		.animatedfsmenu .privacy_policy {
			color: <?php echo esc_attr( $settings['text_color'] ); ?>;
		}

		.animatedfsmenu .social-media li {
			border-color: <?php echo esc_attr( $settings['text_color'] ); ?>;
		}

		.animatedfsmenu.animation_background li > a:before,
		.animatedfsmenu.animation_background__border_radius li > a:before {
			background: <?php echo esc_attr( $settings['hover_background'] ); ?> !important;
		}
		</style>
		<?php
	}

	/**
	 * Render background image styles with responsive sizes.
	 *
	 * @param string $image_url Background image URL.
	 * @return void
	 */
	private function render_background_image_styles( string $image_url ): void {
		$image_id = attachment_url_to_postid( $image_url );
		$metadata = wp_get_attachment_metadata( $image_id );

		if ( ! $metadata ) {
			return;
		}

		$sizes = isset( $metadata['sizes'] ) ? array_reverse( $metadata['sizes'] ) : array();
		?>
		<style>
		.animatedfsmenu .animatedfs_background {
			background-image: url(<?php echo esc_url( wp_upload_dir()['baseurl'] . '/' . $metadata['file'] ); ?>);
		}
		<?php foreach ( $sizes as $size_name => $image ) : ?>
		@media screen and (max-width: <?php echo esc_attr( $image['width'] ); ?>px) {
			.animatedfsmenu .animatedfs_background {
				background-image: url(<?php echo esc_url( wp_get_attachment_image_url( $image_id, $size_name ) ); ?>);
			}
		}
		<?php endforeach; ?>
		</style>
		<?php
	}

	/**
	 * Render background video script.
	 *
	 * @param string $video_url Video URL.
	 * @return void
	 */
	private function render_background_video_script( string $video_url ): void {
		?>
		<style>
		#afsmenu_video {
			position: fixed;
			right: 0;
			bottom: 0;
			min-width: 100%;
			min-height: 100%;
		}
		</style>
		<script>
		function afsmenu_render_video() {
			var video = '<video autoplay muted loop id="afsmenu_video"><source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4"></video>';
			jQuery('.animatedfs_background').html(video);
		}
		</script>
		<?php
	}

	/**
	 * Render the menu HTML structure.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_menu_html( array $settings ): void {
		// Build CSS classes.
		$classes = $this->build_menu_classes( $settings );
		?>
		<div id="animatedfsmenu_css" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<div class="animatedfs_background"></div>

			<?php $this->render_toggle_button( $settings ); ?>

			<div class="navbar-collapse animatedfs_menu_list">
				<?php
				$this->render_search_bar( $settings );
				$this->render_language_switcher( $settings );
				$this->render_navigation_menu( $settings );
				$this->render_html_content( $settings );
				$this->render_social_icons( $settings );
				$this->render_move_to_element_script( $settings );
				$this->render_woocommerce( $settings );
				$this->render_privacy_policy( $settings );

				// Allow extensions.
				do_action( 'animatedfsmenu_aftermenu' );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Build menu CSS classes.
	 *
	 * @param array $settings Render settings.
	 * @return array
	 */
	private function build_menu_classes( array $settings ): array {
		$classes = array( 'animatedfsmenu' );

		// Open levels class.
		if ( $settings['open_levels'] ) {
			$classes[] = 'animatedfsmenu_openlevels';
		}

		// Multi-level class.
		if ( $settings['multi_level'] ) {
			$classes[] = 'afsmenu-sub-level-activated';
		}

		// Text alignment.
		$classes[] = esc_attr( $settings['text_align'] );

		// Side menu.
		if ( $settings['side_menu'] ) {
			$classes[] = 'animatedfsmenu__sidemenu';
		}

		// Mobile only.
		if ( $settings['mobile_only'] ) {
			$classes[] = 'animatedfsmenu__mobile';
		}

		// Animation.
		$classes[] = 'animatedfsmenu__' . esc_attr( $settings['animation'] );

		// Lateral menu.
		if ( $settings['lateral_menu'] && is_array( $settings['lateral_pages'] ) ) {
			if ( in_array( get_the_ID(), $settings['lateral_pages'], true ) ) {
				$classes[] = 'animatedfsmenu__lateralmenu';
			}
		}

		// Anchor mode.
		if ( $settings['anchor_mode'] ) {
			$classes[] = 'animatedfsmenu__anchor';
		}

		// Hover effect.
		$classes[] = esc_attr( $settings['hover_effect'] );

		return $classes;
	}

	/**
	 * Render the toggle button.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_toggle_button( array $settings ): void {
		if ( $settings['disable_button'] ) {
			return;
		}

		$button_classes = array(
			'animatedfsmenu-navbar-toggler',
			esc_attr( $settings['button_position'] ),
		);

		if ( $settings['button_image'] ) {
			$button_classes[] = 'custom-burger';
		}

		if ( $settings['mobile_only'] ) {
			$button_classes[] = 'animatedfsmenu__mobile';
		}
		?>
		<button class="<?php echo esc_attr( implode( ' ', $button_classes ) ); ?>" type="button">
			<?php if ( $settings['button_image'] ) : ?>
				<img class="animatedfsmenu-custom-image" src="<?php echo esc_url( $settings['button_image'] ); ?>" alt="" />
			<?php else : ?>
				<div class="bar top"></div>
				<div class="bar bot"></div>
				<div class="bar mid"></div>
			<?php endif; ?>
		</button>
		<?php
	}

	/**
	 * Render search bar.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_search_bar( array $settings ): void {
		if ( ! $settings['show_search'] ) {
			return;
		}
		?>
		<div class="afsmenu_search">
			<form action="<?php echo esc_url( get_site_url() ); ?>" autocomplete="off">
				<input id="search" name="s" type="text" placeholder="<?php echo esc_attr( $settings['search_placeholder'] ); ?>">
				<div class="search_submit">
					<i class="fas fa-search"></i>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Render language switcher.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_language_switcher( array $settings ): void {
		if ( ! $settings['language_switcher'] ) {
			return;
		}

		$polylang = new Polylang();
		$polylang->render_switcher( 'slug', true );
	}

	/**
	 * Render navigation menu.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_navigation_menu( array $settings ): void {
		$container_class = $settings['scroll'] ? 'afsmenu_scroll' : '';
		$schema_attr     = $settings['schema_org'] ? ' itemscope itemtype="http://schema.org/SiteNavigationElement"' : '';
		$schema_class    = $settings['schema_org'] ? ' afsmenu-schemaorg' : '';

		$menu_args = array(
			'menu_class'      => 'afsmenu' . $schema_class,
			'container'       => 'div',
			'container_class' => $container_class,
			'items_wrap'      => '<ul id="%1$s" class="%2$s"' . $schema_attr . '>%3$s</ul>',
		);

		if ( 'none' !== $settings['menu_id'] && 'menulocation' !== $settings['menu_id'] ) {
			$menu_args['menu'] = $settings['menu_id'];
			wp_nav_menu( $menu_args );
		} elseif ( 'menulocation' === $settings['menu_id'] ) {
			$menu_args['theme_location'] = 'animated-fullscreen-menu';
			wp_nav_menu( $menu_args );
		}
	}

	/**
	 * Render custom HTML content.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_html_content( array $settings ): void {
		if ( empty( $settings['html_content'] ) ) {
			return;
		}

		$content = $this->process_content( $settings['html_content'] );
		echo '<div class="animatedfsmenu-html-area">' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Process content with shortcodes and embeds.
	 *
	 * @param string $content Content to process.
	 * @return string
	 */
	private function process_content( string $content ): string {
		global $wp_embed;

		$content = $wp_embed->autoembed( $content );
		$content = $wp_embed->run_shortcode( $content );
		$content = do_shortcode( $content );

		return $content;
	}

	/**
	 * Render social icons.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_social_icons( array $settings ): void {
		if ( empty( $settings['social_icons'] ) || ! is_array( $settings['social_icons'] ) ) {
			return;
		}

		// Filter out empty entries.
		$icons = array_filter( $settings['social_icons'], function( $icon ) {
			return ! empty( $icon['icon'] );
		});

		if ( empty( $icons ) ) {
			return;
		}
		?>
		<div class="social-media">
			<ul>
				<?php foreach ( $icons as $social ) : ?>
					<?php $url = $social['animatedfsm_url'] ?? '#'; ?>
					<li>
						<a href="<?php echo esc_url( $url ); ?>" title="<?php echo esc_attr( $social['title'] ?? '' ); ?>" target="_blank" rel="noopener noreferrer">
							<i class="fa <?php echo esc_attr( $social['icon'] ); ?>"></i>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render script to move menu to custom element.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_move_to_element_script( array $settings ): void {
		if ( empty( $settings['move_to_element'] ) ) {
			return;
		}
		?>
		<style>
		.animatedfsmenu .animatedfsmenu-navbar-toggler {
			display: none;
		}
		</style>
		<script>
		jQuery(document).ready(function() {
			var menu = jQuery("#animatedfsmenu_css");
			var toggler = jQuery(".animatedfsmenu-navbar-toggler");
			jQuery("<?php echo esc_js( $settings['move_to_element'] ); ?>").append(menu);
			jQuery(toggler).appendTo("<?php echo esc_js( $settings['move_to_element'] ); ?>");
		});
		</script>
		<?php
	}

	/**
	 * Render WooCommerce integration.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_woocommerce( array $settings ): void {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$woocommerce = new WooCommerce( $this->plugin_url );

		if ( $settings['woocommerce_menu'] ) {
			$woocommerce->render_menu();
		}

		if ( $settings['woocommerce_cart'] ) {
			$woocommerce->render_cart();
		}
	}

	/**
	 * Render privacy policy link.
	 *
	 * @param array $settings Render settings.
	 * @return void
	 */
	private function render_privacy_policy( array $settings ): void {
		if ( ! $settings['privacy_policy'] ) {
			return;
		}

		$policy_page_id = get_option( 'wp_page_for_privacy_policy' );

		if ( ! $policy_page_id ) {
			return;
		}
		?>
		<div class="privacy_policy">
			<a href="<?php echo esc_url( get_the_permalink( $policy_page_id ) ); ?>" target="_blank">
				<?php echo esc_html( get_the_title( $policy_page_id ) ); ?>
			</a>
		</div>
		<?php
	}
}
