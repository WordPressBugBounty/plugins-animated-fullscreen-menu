<?php
/**
 * Polylang Integration Class
 *
 * @package AnimatedFullscreenMenu
 */

namespace AnimatedFullscreenMenu\Integrations;

/**
 * Handles Polylang integration for the menu.
 */
class Polylang {

	/**
	 * Check if Polylang is active.
	 *
	 * @return bool
	 */
	public static function is_active(): bool {
		return function_exists( 'pll_the_languages' );
	}

	/**
	 * Get translated post ID.
	 *
	 * @param int $post_id Original post ID.
	 * @return int Translated post ID or original if no translation.
	 */
	public static function get_translated_post( int $post_id ): int {
		if ( ! self::is_active() ) {
			return $post_id;
		}

		if ( function_exists( 'pll_get_post' ) ) {
			$translated_id = pll_get_post( $post_id );
			if ( $translated_id ) {
				return $translated_id;
			}
		}

		return $post_id;
	}

	/**
	 * Render language switcher.
	 *
	 * @param string $display    Display type ('name', 'slug', 'raw').
	 * @param bool   $show_flags Whether to show flags.
	 * @return void
	 */
	public function render_switcher( string $display = 'slug', bool $show_flags = true ): void {
		if ( ! self::is_active() ) {
			return;
		}

		echo '<ul class="navbar__languages">';
		pll_the_languages(
			array(
				'display_names_as' => $display,
				'show_flags'       => $show_flags,
			)
		);
		echo '</ul>';
	}

	/**
	 * Get available languages.
	 *
	 * @param array $args Arguments for pll_the_languages.
	 * @return array
	 */
	public function get_languages( array $args = array() ): array {
		if ( ! self::is_active() ) {
			return array();
		}

		if ( ! function_exists( 'pll_the_languages' ) ) {
			return array();
		}

		$defaults = array(
			'raw' => true,
		);

		$args = wp_parse_args( $args, $defaults );

		return pll_the_languages( $args );
	}

	/**
	 * Get current language.
	 *
	 * @param string $field Field to return ('name', 'slug', 'locale').
	 * @return string
	 */
	public function get_current_language( string $field = 'slug' ): string {
		if ( ! self::is_active() ) {
			return '';
		}

		if ( function_exists( 'pll_current_language' ) ) {
			return pll_current_language( $field );
		}

		return '';
	}
}
