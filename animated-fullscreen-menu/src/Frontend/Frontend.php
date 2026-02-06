<?php
/**
 * Frontend Bootstrap Class
 *
 * @package AnimatedFullscreenMenu
 */

namespace AnimatedFullscreenMenu\Frontend;

use AnimatedFullscreenMenu\Utilities\Options;
use AnimatedFullscreenMenu\Integrations\WooCommerce;
use AnimatedFullscreenMenu\Integrations\Polylang;

/**
 * Handles all frontend-related functionality.
 */
class Frontend {

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
	 * Asset loader instance.
	 *
	 * @var AssetLoader
	 */
	private AssetLoader $asset_loader;

	/**
	 * Menu renderer instance.
	 *
	 * @var MenuRenderer
	 */
	private MenuRenderer $menu_renderer;

	/**
	 * Whether we're in preview mode.
	 *
	 * @var bool
	 */
	private bool $is_preview = false;

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

		// Check preview mode.
		$this->is_preview = $this->check_preview_mode();

		// Only initialize if menu is enabled or in preview mode.
		if ( ! $this->should_initialize() ) {
			return;
		}

		// Initialize components.
		$this->asset_loader  = new AssetLoader( $options, $plugin_path, $plugin_url, $this->is_preview );
		$this->menu_renderer = new MenuRenderer( $options, $plugin_path, $plugin_url );

		$this->init_hooks();
	}

	/**
	 * Check if we're in preview mode.
	 *
	 * @return bool
	 */
	private function check_preview_mode(): bool {
		if ( empty( $_GET['afs_preview_menu'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		$preview_value = sanitize_text_field( wp_unslash( $_GET['afs_preview_menu'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( 'true' !== $preview_value ) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if frontend should be initialized.
	 *
	 * @return bool
	 */
	private function should_initialize(): bool {
		// Always initialize in preview mode.
		if ( $this->is_preview ) {
			return true;
		}

		// Check if menu is enabled.
		return $this->options->is_enabled();
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		// Determine which hooks to use based on preview mode.
		$scripts_hook = $this->is_preview ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';
		$render_hook  = $this->is_preview ? 'admin_footer' : 'wp_footer';

		// Enqueue assets.
		add_action( $scripts_hook, array( $this->asset_loader, 'enqueue' ), 22 );

		// Render menu.
		add_action( $render_hook, array( $this->menu_renderer, 'render' ) );

		// Add menu item class filter.
		add_filter( 'nav_menu_css_class', array( $this, 'filter_menu_item_classes' ), 10, 3 );

		// Add schema.org markup filter.
		add_filter( 'nav_menu_link_attributes', array( $this, 'filter_menu_link_attributes' ), 99, 3 );
	}

	/**
	 * Filter menu item classes to replace WordPress default with plugin classes.
	 *
	 * @param array    $classes Array of menu item classes.
	 * @param \WP_Post $item    Menu item object.
	 * @param object   $args    Menu arguments.
	 * @return array
	 */
	public function filter_menu_item_classes( array $classes, $item, $args ): array {
		if ( ! in_array( 'menu-item-has-children', $classes, true ) ) {
			return $classes;
		}

		foreach ( $classes as $i => $class ) {
			if ( 'menu-item-has-children' === $class ) {
				$classes[ $i ] = 'afs-menu-item-has-children';
			}
		}

		return $classes;
	}

	/**
	 * Filter menu link attributes to add schema.org markup.
	 *
	 * @param array    $atts Menu link attributes.
	 * @param \WP_Post $item Menu item object.
	 * @param object   $args Menu arguments.
	 * @return array
	 */
	public function filter_menu_link_attributes( array $atts, $item, $args ): array {
		if ( empty( $args->menu_class ) || false === strpos( $args->menu_class, 'afsmenu-schemaorg' ) ) {
			return $atts;
		}

		$atts['itemprop'] = 'url';

		return $atts;
	}

	/**
	 * Check if we're in preview mode.
	 *
	 * @return bool
	 */
	public function is_preview(): bool {
		return $this->is_preview;
	}
}
