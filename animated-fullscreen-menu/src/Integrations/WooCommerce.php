<?php
/**
 * WooCommerce Integration Class
 *
 * @package AnimatedFullscreenMenu
 */

namespace AnimatedFullscreenMenu\Integrations;

/**
 * Handles WooCommerce integration for the menu.
 */
class WooCommerce {

	/**
	 * Plugin URL.
	 *
	 * @var string
	 */
	private string $plugin_url;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_url Plugin directory URL.
	 */
	public function __construct( string $plugin_url ) {
		$this->plugin_url = $plugin_url;
	}

	/**
	 * Check if WooCommerce is active.
	 *
	 * @return bool
	 */
	public static function is_active(): bool {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Get WooCommerce menu pages.
	 *
	 * @return array
	 */
	public function get_menu_pages(): array {
		if ( ! self::is_active() ) {
			return array();
		}

		$pages = array();

		// Account page.
		$account_id = wc_get_page_id( 'myaccount' );
		if ( $account_id && $account_id > 0 ) {
			$account_id = $this->get_translated_page_id( $account_id );
			$pages['account'] = array(
				'title' => get_the_title( $account_id ),
				'url'   => get_the_permalink( $account_id ),
				'icon'  => 'fa-user',
			);
		}

		// Shop page.
		$shop_id = wc_get_page_id( 'shop' );
		if ( $shop_id && $shop_id > 0 ) {
			$shop_id = $this->get_translated_page_id( $shop_id );
			$pages['shop'] = array(
				'title' => get_the_title( $shop_id ),
				'url'   => get_the_permalink( $shop_id ),
				'icon'  => 'fa-store',
			);
		}

		// Cart page.
		$cart_id = wc_get_page_id( 'cart' );
		if ( $cart_id && $cart_id > 0 ) {
			$cart_id = $this->get_translated_page_id( $cart_id );
			$pages['cart'] = array(
				'title' => get_the_title( $cart_id ),
				'url'   => get_the_permalink( $cart_id ),
				'icon'  => 'fa-shopping-cart',
			);
		}

		// Checkout page.
		$checkout_id = wc_get_page_id( 'checkout' );
		if ( $checkout_id && $checkout_id > 0 ) {
			$checkout_id = $this->get_translated_page_id( $checkout_id );
			$pages['checkout'] = array(
				'title' => get_the_title( $checkout_id ),
				'url'   => get_the_permalink( $checkout_id ),
				'icon'  => 'fa-shopping-bag',
			);
		}

		return $pages;
	}

	/**
	 * Get translated page ID if Polylang is active.
	 *
	 * @param int $page_id Original page ID.
	 * @return int
	 */
	private function get_translated_page_id( int $page_id ): int {
		if ( function_exists( 'pll_get_post' ) ) {
			$translated_id = pll_get_post( $page_id );
			if ( $translated_id ) {
				return $translated_id;
			}
		}
		return $page_id;
	}

	/**
	 * Render WooCommerce menu.
	 *
	 * @return void
	 */
	public function render_menu(): void {
		$pages = $this->get_menu_pages();

		if ( empty( $pages ) ) {
			return;
		}

		echo '<ul class="animatedfsmenu_woocommerce">';
		foreach ( $pages as $page ) {
			printf(
				'<li><a href="%s"><i class="fas %s"></i>%s</a></li>',
				esc_url( $page['url'] ),
				esc_attr( $page['icon'] ),
				esc_html( $page['title'] )
			);
		}
		echo '</ul>';
	}

	/**
	 * Get cart items.
	 *
	 * @return array
	 */
	public function get_cart_items(): array {
		if ( ! self::is_active() ) {
			return array();
		}

		global $woocommerce;

		if ( ! $woocommerce || ! isset( $woocommerce->cart ) ) {
			return array();
		}

		$cart  = $woocommerce->cart->get_cart();
		$items = array();

		foreach ( $cart as $cart_item ) {
			$product_id = $cart_item['product_id'];

			$items[] = array(
				'id'       => $product_id,
				'quantity' => $cart_item['quantity'],
				'total'    => $cart_item['line_total'],
				'name'     => get_the_title( $product_id ),
				'url'      => get_the_permalink( $product_id ),
				'image'    => wp_get_attachment_url( get_post_thumbnail_id( $product_id ) ),
			);
		}

		return $items;
	}

	/**
	 * Render cart carousel.
	 *
	 * @return void
	 */
	public function render_cart(): void {
		$items = $this->get_cart_items();

		if ( empty( $items ) ) {
			return;
		}

		// Enqueue Owl Carousel.
		$this->enqueue_owl_carousel();

		echo '<h3 class="afs-cart-title">' . esc_html__( 'Products in cart', 'animated-fullscreen-menu' ) . '</h3>';
		echo '<div class="afs-owl-cart owl-carousel">';

		foreach ( $items as $item ) {
			$formatted_price = wc_price( $item['total'] );
			?>
			<div class="item">
				<a href="<?php echo esc_url( $item['url'] ); ?>" title="<?php echo esc_attr( $item['name'] ); ?>">
					<div class="afs_item-container">
						<div class="afs_item__img" style="background-image: url(<?php echo esc_url( $item['image'] ); ?>);"></div>
						<h3 class="afs_item__title"><?php echo esc_html( $item['name'] ); ?></h3>
						<p class="afs_item__qtt">
							<?php
							printf(
								/* translators: %d: Number of units */
								esc_html( _n( '%d unit', '%d units', $item['quantity'], 'animated-fullscreen-menu' ) ),
								intval( $item['quantity'] )
							);
							?>
						</p>
						<p class="afs_item__total"><?php echo $formatted_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					</div>
				</a>
			</div>
			<?php
		}

		echo '</div>';

		// Output initialization script.
		$this->render_cart_script();
	}

	/**
	 * Enqueue Owl Carousel assets.
	 *
	 * @return void
	 */
	private function enqueue_owl_carousel(): void {
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
	 * Render cart carousel initialization script.
	 *
	 * @return void
	 */
	private function render_cart_script(): void {
		?>
		<script>
		function afs_owl_cart() {
			jQuery(".afs-owl-cart").owlCarousel({
				loop: false,
				margin: 10,
				nav: true,
				responsive: {
					0: { items: 2 },
					600: { items: 3 },
					1000: { items: 4 }
				}
			});
		}
		</script>
		<?php
	}
}
