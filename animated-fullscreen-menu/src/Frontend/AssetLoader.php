<?php
/**
 * Asset Loader Class
 *
 * @package AnimatedFullscreenMenu
 */

namespace AnimatedFullscreenMenu\Frontend;

use AnimatedFullscreenMenu\Utilities\Options;

/**
 * Handles loading of frontend scripts and styles.
 */
class AssetLoader {

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
	 * Whether we're in preview mode.
	 *
	 * @var bool
	 */
	private bool $is_preview;

	/**
	 * Constructor.
	 *
	 * @param Options $options     Options instance.
	 * @param string  $plugin_path Plugin directory path.
	 * @param string  $plugin_url  Plugin directory URL.
	 * @param bool    $is_preview  Whether in preview mode.
	 */
	public function __construct( Options $options, string $plugin_path, string $plugin_url, bool $is_preview = false ) {
		$this->options     = $options;
		$this->plugin_path = $plugin_path;
		$this->plugin_url  = $plugin_url;
		$this->is_preview  = $is_preview;
	}

	/**
	 * Enqueue all frontend assets.
	 *
	 * @return void
	 */
	public function enqueue(): void {
		// Skip on admin unless in preview mode.
		if ( is_admin() && ! $this->is_preview ) {
			return;
		}

		$this->enqueue_styles();
		$this->enqueue_scripts();
		$this->maybe_enqueue_font_awesome();
		$this->maybe_enqueue_google_fonts();
	}

	/**
	 * Enqueue main styles.
	 *
	 * @return void
	 */
	private function enqueue_styles(): void {
		wp_enqueue_style(
			'afsmenu-styles',
			$this->plugin_url . 'frontend/css/nav.css',
			array(),
			$this->get_version()
		);
	}

	/**
	 * Enqueue main scripts.
	 *
	 * @return void
	 */
	private function enqueue_scripts(): void {
		wp_enqueue_script(
			'afsmenu-scripts',
			$this->plugin_url . 'frontend/js/nav.js',
			array( 'jquery' ),
			$this->get_version(),
			true
		);

		// Localize script with settings.
		$this->localize_script();
	}

	/**
	 * Localize script with PHP data.
	 *
	 * @return void
	 */
	private function localize_script(): void {
		$is_pro = function_exists( 'animatedfsm' ) && animatedfsm()->is__premium_only();

		wp_localize_script(
			'afsmenu-scripts',
			'afsmenu',
			array(
				'autohide_scroll' => $is_pro && 'on' === $this->options->get( 'animatedfsm_autohide_scroll' ) ? 'true' : '',
			)
		);
	}

	/**
	 * Maybe enqueue Font Awesome.
	 *
	 * @return void
	 */
	private function maybe_enqueue_font_awesome(): void {
		$social_icons   = $this->options->get( 'socialicons_group', array() );
		$woocommerce_on = 'on' === $this->options->get( 'animatedfsm_woocommerce_on' );
		$search_on      = 'on' === $this->options->get( 'animatedfsm_searchbar_on' );

		// Check if we have social icons with content.
		$has_social_icons = ! empty( $social_icons ) && is_array( $social_icons );
		if ( $has_social_icons ) {
			$has_social_icons = count( array_filter( $social_icons, function( $icon ) {
				return ! empty( $icon['icon'] );
			})) > 0;
		}

		if ( $has_social_icons || $woocommerce_on || $search_on ) {
			$this->enqueue_font_awesome();
		}
	}

	/**
	 * Enqueue Font Awesome.
	 *
	 * @return void
	 */
	public function enqueue_font_awesome(): void {
		wp_enqueue_style(
			'afsmenu-font-awesome',
			'//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css',
			array(),
			'6.1.1'
		);
	}

	/**
	 * Maybe enqueue Google Fonts.
	 *
	 * @return void
	 */
	private function maybe_enqueue_google_fonts(): void {
		$font = $this->options->get( 'animatedfsm_font', 'inherit' );

		if ( 'inherit' !== $font && ! empty( $font ) ) {
			$this->enqueue_google_fonts( $font );
		}
	}

	/**
	 * Enqueue Google Fonts.
	 *
	 * @param string $font Font family name.
	 * @return void
	 */
	public function enqueue_google_fonts( string $font ): void {
		wp_enqueue_style(
			'afsmenu-google-fonts',
			'//fonts.googleapis.com/css?family=' . rawurlencode( $font ),
			array(),
			null
		);
	}

	/**
	 * Enqueue Owl Carousel for WooCommerce cart.
	 *
	 * @return void
	 */
	public function enqueue_owl_carousel(): void {
		wp_enqueue_style(
			'afsmenu-owl-carousel',
			$this->plugin_url . 'frontend/vendor/owl-carousel/owl.carousel.min.css',
			array(),
			'2.3.4'
		);

		wp_enqueue_script(
			'afsmenu-owl-carousel-script',
			$this->plugin_url . 'frontend/vendor/owl-carousel/owl.carousel.min.js',
			array( 'jquery' ),
			'2.3.4',
			true
		);
	}

	/**
	 * Get plugin version.
	 *
	 * @return string
	 */
	private function get_version(): string {
		return \AnimatedFullscreenMenu\Plugin::VERSION;
	}
}
