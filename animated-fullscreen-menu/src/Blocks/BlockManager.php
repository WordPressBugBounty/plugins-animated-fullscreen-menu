<?php
/**
 * Block Manager Class
 *
 * @package AnimatedFullscreenMenu
 */

namespace AnimatedFullscreenMenu\Blocks;

use AnimatedFullscreenMenu\Utilities\Options;

/**
 * Handles registration and management of Gutenberg blocks.
 */
class BlockManager {

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

		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Register all blocks.
	 *
	 * @return void
	 */
	public function register_blocks(): void {
		// Register the hamburger button block.
		$this->register_hamburger_block();
	}

	/**
	 * Register the hamburger button block.
	 *
	 * @return void
	 */
	private function register_hamburger_block(): void {
		$block_json_path = $this->plugin_path . 'block.json';

		if ( ! file_exists( $block_json_path ) ) {
			return;
		}

		register_block_type( $this->plugin_path );
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * @return void
	 */
	public function enqueue_editor_assets(): void {
		// Localize settings for the block editor.
		wp_localize_script(
			'animated-fullscreen-menu-hamburger-editor-script',
			'animatedfsmenu',
			array(
				'animatedfsmenu_settings' => $this->options->get_all(),
			)
		);
	}
}
