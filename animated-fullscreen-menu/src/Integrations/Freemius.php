<?php
/**
 * Freemius Integration Class
 *
 * @package AnimatedFullscreenMenu
 */

namespace AnimatedFullscreenMenu\Integrations;

/**
 * Handles Freemius SDK integration for licensing and analytics.
 */
class Freemius {

	/**
	 * Freemius instance.
	 *
	 * @var \Freemius|null
	 */
	private static $freemius = null;

	/**
	 * Plugin file path.
	 *
	 * @var string
	 */
	private string $plugin_file;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_file Main plugin file path.
	 */
	public function __construct( string $plugin_file ) {
		$this->plugin_file = $plugin_file;
		$this->init();
	}

	/**
	 * Initialize Freemius SDK.
	 *
	 * @return void
	 */
	private function init(): void {
		if ( null !== self::$freemius ) {
			return;
		}

		$freemius_path = dirname( $this->plugin_file ) . '/freemius/start.php';

		if ( ! file_exists( $freemius_path ) ) {
			return;
		}

		require_once $freemius_path;

		/*
		 * The plugin is distributed as a single package: the same build serves free
		 * and PRO users, and the license unlocks PRO features at runtime.
		 *
		 * Keep this false. It tells Freemius there is no separate premium package to
		 * download, so it never tries to swap this build for another one. PRO gating
		 * is decided by Freemius::has_paid_plan(), which checks the license.
		 */
		$pro = false;

		self::$freemius = fs_dynamic_init(
			array(
				'id'                  => '3887',
				'slug'                => 'animated-fullscreen-menu',
				'premium_slug'        => 'animated-fullscreen-menu',
				'type'                => 'plugin',
				'public_key'          => 'pk_95d707fced75c19ff9b793853ac8a',
				'is_premium'          => $pro,
				'premium_suffix'      => $pro ? 'Pro' : 'Free',
				'has_premium_version' => $pro,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'trial'               => array(
					'days'               => 7,
					'is_require_payment' => true,
				),
				'menu'                => array(
					'slug'       => 'animatedfsm_settings',
					'first-path' => 'admin.php?page=animatedfsm_settings',
					'contact'    => $pro,
					'support'    => true,
				),
			)
		);

		// Signal that SDK was initiated.
		do_action( 'animatedfsm_loaded' );

		// Override i18n strings (defer to init to avoid "too early" translation notice).
		add_action( 'init', array( $this, 'override_i18n' ) );
	}

	/**
	 * Override Freemius i18n strings.
	 *
	 * @return void
	 */
	public function override_i18n(): void {
		if ( null === self::$freemius ) {
			return;
		}

		self::$freemius->override_i18n(
			array(
				'start-trial' => __( 'Free 7 Day Pro Trial', 'animated-fullscreen-menu' ),
				'upgrade'     => __( 'Get Pro Features', 'animated-fullscreen-menu' ),
			)
		);
	}

	/**
	 * Get Freemius instance.
	 *
	 * @return \Freemius|null
	 */
	public static function get_instance() {
		return self::$freemius;
	}

	/**
	 * Check if the current install has an active paid license.
	 *
	 * The plugin ships as a single package, so PRO features are unlocked by the
	 * license at runtime rather than by a separate premium build. This checks the
	 * license itself, not whether this is the premium package.
	 *
	 * @return bool
	 */
	public static function is_premium(): bool {
		if ( null === self::$freemius ) {
			return false;
		}

		return self::$freemius->has_features_enabled_license();
	}

	/**
	 * Check if user is in trial.
	 *
	 * @return bool
	 */
	public static function is_trial(): bool {
		if ( null === self::$freemius ) {
			return false;
		}

		return self::$freemius->is_trial();
	}

	/**
	 * Check whether PRO features should be available (active license or trial).
	 *
	 * This is the single source of truth for PRO gating across the plugin.
	 *
	 * @return bool
	 */
	public static function has_paid_plan(): bool {
		if ( null === self::$freemius ) {
			return false;
		}

		return self::$freemius->can_use_premium_code();
	}
}

/**
 * Global helper function for backward compatibility.
 *
 * @return \Freemius|null
 */
function animatedfsm() {
	return Freemius::get_instance();
}
