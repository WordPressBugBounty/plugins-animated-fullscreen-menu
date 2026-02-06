<?php
/**
 * Options Wrapper Class
 *
 * @package AnimatedFullscreenMenu
 */

namespace AnimatedFullscreenMenu\Utilities;

/**
 * Handles all plugin options with a centralized API.
 */
class Options {

	/**
	 * Option key in wp_options table.
	 *
	 * @var string
	 */
	public const OPTION_KEY = 'animatedfsm_settings';

	/**
	 * Cached options.
	 *
	 * @var array
	 */
	private array $options;

	/**
	 * Default values.
	 *
	 * @var array
	 */
	private array $defaults;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->defaults = $this->get_defaults();
		$this->options  = $this->load_options();
	}

	/**
	 * Get a single option value.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Default value if not set.
	 * @return mixed
	 */
	public function get( string $key, $default = null ) {
		if ( isset( $this->options[ $key ] ) ) {
			return $this->options[ $key ];
		}

		if ( null !== $default ) {
			return $default;
		}

		return $this->defaults[ $key ] ?? null;
	}

	/**
	 * Set a single option value.
	 *
	 * @param string $key   Option key.
	 * @param mixed  $value Option value.
	 * @return bool
	 */
	public function set( string $key, $value ): bool {
		$this->options[ $key ] = $value;
		return $this->save();
	}

	/**
	 * Get all options merged with defaults.
	 *
	 * @return array
	 */
	public function get_all(): array {
		return array_merge( $this->defaults, $this->options );
	}

	/**
	 * Get raw options without defaults.
	 *
	 * @return array
	 */
	public function get_raw(): array {
		return $this->options;
	}

	/**
	 * Check if the menu is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 'on' === $this->get( 'animatedfsm_on' );
	}

	/**
	 * Check if testing mode is enabled.
	 *
	 * @return bool
	 */
	public function is_testing_mode(): bool {
		return 'on' === $this->get( 'animatedfsm_testing_mode' );
	}

	/**
	 * Check if mobile only mode is enabled.
	 *
	 * @return bool
	 */
	public function is_mobile_only(): bool {
		return 'on' === $this->get( 'animatedfsm_mobile_only' );
	}

	/**
	 * Check if the default button is disabled.
	 *
	 * @return bool
	 */
	public function is_button_disabled(): bool {
		return 'on' === $this->get( 'animatedfsm_disable_button' );
	}

	/**
	 * Get pages where menu should be hidden.
	 *
	 * @return array
	 */
	public function get_hidden_pages(): array {
		$pages = $this->get( 'animatedfsm_hide_menu_pages' );
		return is_array( $pages ) ? $pages : array();
	}

	/**
	 * Check if menu should be hidden on current page.
	 *
	 * @return bool
	 */
	public function should_hide_on_current_page(): bool {
		$hidden_pages = $this->get_hidden_pages();
		if ( empty( $hidden_pages ) ) {
			return false;
		}
		return in_array( get_the_ID(), $hidden_pages, true );
	}

	/**
	 * Load options from database.
	 *
	 * @return array
	 */
	private function load_options(): array {
		$options = get_option( self::OPTION_KEY, array() );
		return is_array( $options ) ? $options : array();
	}

	/**
	 * Save options to database.
	 *
	 * @return bool
	 */
	private function save(): bool {
		return update_option( self::OPTION_KEY, $this->options );
	}

	/**
	 * Refresh options from database.
	 *
	 * @return void
	 */
	public function refresh(): void {
		$this->options = $this->load_options();
	}

	/**
	 * Get default option values.
	 *
	 * @return array
	 */
	private function get_defaults(): array {
		return array(
			// General settings.
			'animatedfsm_on'                    => '',
			'animatedfsm_testing_mode'          => '',
			'animatedfsm_mobile_only'           => '',
			'animatedfsm_anchor'                => '',
			'animatedfsm_autohide_scroll'       => '',
			'animatedfsm_hide_menu_pages'       => array(),

			// Design settings.
			'animatedfsm_background01'          => '#000000',
			'animatedfsm_background02'          => '#45aacc',
			'animatedfsm_backgroundimage'       => '',
			'animatedfsm_backgroundvideo'       => '',
			'animatedfsm_textcolor'             => '#FFFFFF',
			'animatedfsm_font'                  => 'inherit',
			'animatedfsm_fontweight'            => '400',
			'animatedfsm_fontsize'              => '',
			'animatedfsm_animation'             => 'opacity',
			'animatedfsm_number_of_columns'     => '1',
			'animatedfsm_scroll'                => '',
			'animatedfsm_hoverfocus'            => 'animation_line',
			'animatedfsm_hoverbackground'       => 'rgba(0,0,0,0.85)',
			'animatedfsm_lateralmenu'           => '',
			'animatedfsm_lateralmenu_pages'     => array(),
			'animatedfsm_sidemenu'              => '',

			// Text shadow (PRO).
			'animatedfsm_textshadow_activate'   => '',
			'animatedfsm_textshadow_horizontal' => '2',
			'animatedfsm_textshadow_vertical'   => '2',
			'animatedfsm_textshadow_blur_radius' => '8',
			'animatedfsm_textshadow_color'      => '#000000',
			'animatedfsm_text_align'            => 'align_left',

			// Button settings.
			'animatedfsm_disable_button'        => '',
			'animatedfsm_button_position'       => 'right_top',
			'animatedfsm_button_image'          => '',
			'animatedfsm_unfix_button'          => '',
			'animatedfsm_element_class'         => '',

			// Content settings.
			'animatedfsm_menuselected'          => 'none',
			'animatedfsm_html'                  => '',
			'socialicons_group'                 => array(),
			'animatedfsm_privacy_on'            => '',
			'animatedfsm_searchbar_on'          => '',
			'animatedfsm_searchbar_placeholder' => 'Search something...',
			'animatedfsm_languageswitcher'      => '',

			// Multi-level menu (PRO).
			'animatedfsm_multi_level_menu'      => '',
			'animatedfsm_openlevels'            => '',

			// WooCommerce.
			'animatedfsm_woocommerce_on'        => '',
			'animatedfsm_woocommerce_cart_on'   => '',

			// SEO.
			'animatedfsm_add_schemaorg'         => '',

			// Data removal.
			'animatedfsm_removedata_on'         => '',
		);
	}
}
