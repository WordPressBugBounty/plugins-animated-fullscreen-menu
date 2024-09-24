<?php

// Check if there is a preview
$is_preview   = isset( $_GET['afs_preview_menu'] ) ? $_GET['afs_preview_menu'] : false;
$scripts_hook = $is_preview ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';
$render_hook  = $is_preview ? 'admin_init' : 'wp_head';

add_action( $scripts_hook, 'animatedfsmenu_enqueue_scripts', 22, 1 );

function animatedfsmenu_get_woocommerce_menu() {
	

	$account  = ( null !== wc_get_page_id( 'myaccount' ) ? wc_get_page_id( 'myaccount' ) : false );
	$shop     = ( null !== wc_get_page_id( 'shop' ) ? wc_get_page_id( 'shop' ) : false );
	$cart     = ( null !== wc_get_page_id( 'cart' ) ? wc_get_page_id( 'cart' ) : false );
	$checkout = ( null !== wc_get_page_id( 'checkout' ) ? wc_get_page_id( 'checkout' ) : false );

	if ( function_exists( 'pll_the_languages' ) ) {
		$account  = pll_get_post( $account );
		$shop     = pll_get_post( $shop );
		$cart     = pll_get_post( $cart );
		$checkout = pll_get_post( $checkout );
	}

	$woocommerce_menu = array();

	if ( $account ) {
		$woocommerce_menu['account'] = array(
			'page_title' => get_the_title( $account ),
			'page_url'   => get_the_permalink( $account ),
			'icon'       => 'fab-user',
		);
	}
	if ( $shop ) {
		$woocommerce_menu['shop'] = array(
			'page_title' => get_the_title( $shop ),
			'page_url'   => get_the_permalink( $shop ),
			'icon'       => 'fa-store',
		);
	}
	if ( $cart ) {
		$woocommerce_menu['cart'] = array(
			'page_title' => get_the_title( $cart ),
			'page_url'   => get_the_permalink( $cart ),
			'icon'       => 'fa-shopping-cart',
		);
	}
	if ( $checkout ) {
		$woocommerce_menu['checkout'] = array(
			'page_title' => get_the_title( $checkout ),
			'page_url'   => get_the_permalink( $checkout ),
			'icon'       => 'fa-shopping-bag',
		);
	}

	return $woocommerce_menu;
}





function render_animatedfsmenu_nav() { 

	$settings = get_option( 'animatedfsm_settings' );
	$hide_menu_pages    = ( isset( $settings['animatedfsm_hide_menu_pages'] ) ? $settings['animatedfsm_hide_menu_pages'] : false );
	
	
	//Hide menu on specific pages
	if( $hide_menu_pages && in_array( get_the_ID(), $hide_menu_pages ) ){
		return;
	}
	
	$pro_user 			   = animatedfsm()->is__premium_only();
	$testing_mode 		   = ( isset( $settings['animatedfsm_testing_mode'] ) ? $settings['animatedfsm_testing_mode'] : false );
	$button_image          = ( isset( $settings['animatedfsm_button_image'] ) && $pro_user  ? $settings['animatedfsm_button_image'] : false );
	$button_position       = ( isset( $settings['animatedfsm_button_position'] ) && $pro_user ? $settings['animatedfsm_button_position'] : 'right_top' );
	$text_align		       = ( isset( $settings['animatedfsm_text_align'] ) && $pro_user ? $settings['animatedfsm_text_align'] : 'align_left' );
	$unfix_button 		   = ( isset( $settings['animatedfsm_unfix_button'] ) && $pro_user ? $settings['animatedfsm_unfix_button'] : false );
	$privacy_policy_on     = ( isset( $settings['animatedfsm_privacy_on'] ) ? $settings['animatedfsm_privacy_on'] : false );
	$googlefont            = $settings['animatedfsm_font'];
	$font_weight           = $settings['animatedfsm_fontweight'];
	$font_size			   = ( isset( $settings['animatedfsm_fontsize'] )  && $pro_user ? $settings['animatedfsm_fontsize'] : false );
	$background01          = $settings['animatedfsm_background01'];
	$background02          = $settings['animatedfsm_background02'];
	$background_image      = ( isset( $settings['animatedfsm_backgroundimage'] ) ? $settings['animatedfsm_backgroundimage'] : null );
	$background_video      = ( isset( $settings['animatedfsm_backgroundvideo'] ) && $pro_user ? $settings['animatedfsm_backgroundvideo'] : null );
	$menu_id               = $settings['animatedfsm_menuselected'];
	$textcolor             = $settings['animatedfsm_textcolor'];
	$social_media_array    = $settings['socialicons_group'];
	$animation_class       = 'animatedfsmenu__' . esc_attr( $settings['animatedfsm_animation'] );
	$mobile_only           = ( isset( $settings['animatedfsm_mobile_only'] ) ? $settings['animatedfsm_mobile_only'] : false );
	$mobile_class          = ( 'on' === $mobile_only ? 'animatedfsmenu__mobile' : '' );
	$woocommerce_on        = ( isset( $settings['animatedfsm_woocommerce_on'] ) ? $settings['animatedfsm_woocommerce_on'] : false );
	$woocommerce_cart_on   = ( isset( $settings['animatedfsm_woocommerce_cart_on'] ) && $pro_user ? $settings['animatedfsm_woocommerce_cart_on'] : false );
	$language_switcher     = ( isset( $settings['animatedfsm_languageswitcher'] ) ? $settings['animatedfsm_languageswitcher'] : false );
	$anchor				   = ( ( isset( $settings['animatedfsm_anchor'] ) && 'on' === $settings['animatedfsm_anchor'] ) ? 'animatedfsmenu__anchor' : '' );
	$autohide_scroll       = ( isset( $settings['animatedfsm_autohide_scroll'] ) && $pro_user  ? $settings['animatedfsm_autohide_scroll'] : false );
	$lateral_menu          = ( isset( $settings['animatedfsm_lateralmenu'] ) ? $settings['animatedfsm_lateralmenu'] : false );
	$lateral_pages         = ( isset( $settings['animatedfsm_lateralmenu_pages'] ) ? $settings['animatedfsm_lateralmenu_pages'] : false );
	$searchbar_on          = ( isset( $settings['animatedfsm_searchbar_on'] ) ? $settings['animatedfsm_searchbar_on'] : false );
	$searchbar_placeholder = ( isset( $settings['animatedfsm_searchbar_placeholder'] ) ? $settings['animatedfsm_searchbar_placeholder'] : false );
	$hover_effect          = ( isset( $settings['animatedfsm_hoverfocus'] ) ? $settings['animatedfsm_hoverfocus'] : 'animation_line' );
	$hover_background      = ( isset( $settings['animatedfsm_hoverbackground'] ) ? $settings['animatedfsm_hoverbackground'] : 'rgba(0,0,0,0.85)' );
	$side_menu             = ( isset( $settings['animatedfsm_sidemenu'] ) ? $settings['animatedfsm_sidemenu'] : '' );
	$html_shortcodes       = animatedfsmenu_get_wysiwyg_output( isset( $settings['animatedfsm_html'] ) ? '<div class="animatedfsmenu-html-area">' . $settings['animatedfsm_html'] . '</div>' : ''  );
	$lateral_class         = '';
	$animatedfsm_scroll    = ( isset( $settings['animatedfsm_scroll'] ) ? $settings['animatedfsm_scroll'] : false );
	$sub_level_activated   = ( isset( $settings['animatedfsm_multi_level_menu'] ) && $settings['animatedfsm_multi_level_menu'] ? 'afsmenu-sub-level-activated' : '' );
	$text_shadow 		   = ( isset( $settings['animatedfsm_textshadow_activate']) && $settings['animatedfsm_textshadow_activate'] && $pro_user ? $settings['animatedfsm_textshadow_horizontal'] . 'px ' . $settings['animatedfsm_textshadow_vertical'] . 'px ' . $settings['animatedfsm_textshadow_blur_radius'] . 'px ' . $settings['animatedfsm_textshadow_color'] : '' );
	$open_levels 		   = ( isset( $settings['animatedfsm_openlevels']) && $settings['animatedfsm_openlevels'] && $pro_user ? true : false );
	$move_to_element_class = ( ! empty( $settings['animatedfsm_element_class'] ) && $pro_user ? $settings['animatedfsm_element_class'] : false );
	$number_of_columns     = ( ! empty( $settings['animatedfsm_number_of_columns'] ) && $pro_user ? $settings['animatedfsm_number_of_columns'] : '1' );
	$animatedfsm_disable_button = ( isset( $settings['animatedfsm_disable_button'] ) && 'on' === $settings['animatedfsm_disable_button'] ? true : false );
	$add_schemaorg         = ( isset( $settings['animatedfsm_add_schemaorg'] ) && 'on' === $settings['animatedfsm_add_schemaorg'] ? true : false );
		
	wp_localize_script( 'afsmenu-scripts', 'afsmenu', array(
		'autohide_scroll' => ( $autohide_scroll ? 'true' : '' )
	));

	if( $add_schemaorg ) {
		echo '<script type="application/ld+json">
			{
				"@context": "http://schema.org",
				"@type": "SiteNavigationElement",
				"name": "' . get_bloginfo( 'name' ) . '",
				"url": "' . get_site_url() . '"
			}
		</script>';
	}
	
	if( $testing_mode && ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( $lateral_menu == 'on' ){
		$actual_page = get_the_ID();
		if( is_array( $lateral_pages ) && in_array( $actual_page, $lateral_pages ) ) {
			$lateral_class = 'animatedfsmenu__lateralmenu';
		
		}

	}

	if ( $side_menu == 'on' ){
		$side_menu = 'animatedfsmenu__sidemenu';
	}
	

	if ( count( $social_media_array ) > 0 || 'on' === $woocommerce_on || 'on' === $searchbar_on ) {
		animatedfsm_enqueue_fontawesome();
	}

	if ( 'inherit' !== $googlefont ) {
		animatedfsm_enqueue_google_fonts( $googlefont );
		?>
<style>
.animatedfsmenu,
.afsmenu_search .search_submit,
input[type="text"],
.afs-cart-title {
    font-family: <?php echo esc_attr($googlefont);
    ?> !important;
}
</style>
<?php } 
		?>
<style>
.animatedfsmenu a,
.afs-cart-title {
    <?php if($text_shadow):
        echo 'text-shadow:'. $text_shadow . ';';
    endif;
    ?>font-weight: <?php echo esc_attr($font_weight);
    ?> !important;

    <?php if($font_size) echo 'font-size: '. esc_attr($font_size) . ' !important;';
    ?>
}
</style>
<?php

	if ( null !== $background_image ) {
			render_animatedfsmenu_backgroundimages( $background_image );
	}

	if ( null !== $background_video ) {
		render_animatedfsmenu_backgroundvideo( $background_video );
	}

	function animatedfsm_getlanguages( $display = 'flag', $flags = false ) { //phpcs:ignore

		if ( function_exists( 'pll_the_languages' ) ) {
			echo '<ul class="navbar__languages">';
			pll_the_languages(
				array(
					'display_names_as' => $display,
					'show_flags'       => $flags,
				)
			);
			echo '</ul>';
		}
	}
	?>

<style>
<?php if ( 1 < $number_of_columns ) : ?>

@media screen and (min-width: 768px){
	ul.afsmenu{
		columns: <?php echo esc_attr( $number_of_columns ); ?>;
	}
}
<?php endif; ?>

<?php if($unfix_button) {

    ?>.animatedfsmenu .animatedfsmenu-navbar-toggler,
    .animatedfsmenu {
        position: absolute;
    }

    <?php
}

?>.turbolinks-progress-bar,
.animatedfsmenu {
    background-color: <?php echo esc_attr($background01);
    ?>;
}

.animatedfsmenu.navbar-expand-md,
.animatedfsmenu.navbar-expand-ht {
    background-color: <?php echo esc_attr($background02); ?> !important;
}

.animatedfsmenu button:focus,
.animatedfsmenu button:hover {
    background: <?php echo esc_attr($background01);
    ?> !important;
}

.animatedfsmenu-navbar-toggler {
    background: <?php echo esc_attr($background01);
    ?>;
}

.animatedfs_menu_list a,
.afsmenu_search input[type="text"],
.afs-cart-title {
    color: <?php echo esc_attr($textcolor);
    ?> !important;
}

.animatedfs_menu_list li>a:before,
.animatedfsmenu .animatedfsmenu-navbar-toggler .bar {
    background: <?php echo esc_attr($textcolor);
    ?> !important;
}

.animatedfsmenu .privacy_policy {
    color: <?php echo esc_attr($textcolor);
    ?>;
}

.animatedfsmenu .social-media li {
    border-color: <?php echo esc_attr($textcolor);
    ?>;
}

.animatedfsmenu.animation_background li>a:before,
.animatedfsmenu.animation_background__border_radius li>a:before {
    background: <?php echo esc_attr($hover_background);
    ?> !important;
}
</style>

<div id="animatedfsmenu_css"
    class="animatedfsmenu <?php echo ($open_levels ? 'animatedfsmenu_openlevels' : '' ); ?> <?php echo esc_attr( $sub_level_activated ); ?> <?php echo esc_attr( $text_align ); ?> <?php echo esc_attr( $side_menu ); ?> <?php echo esc_attr( $mobile_class ); ?> <?php echo esc_attr( $animation_class ); ?> <?php echo esc_attr( $lateral_class ); ?> <?php echo esc_attr( $anchor ); ?> <?php echo esc_attr( $hover_effect ); ?>">
    <div class="animatedfs_background"></div>
	<?php if ( ! $animatedfsm_disable_button ) { ?>
    <button
        class="animatedfsmenu-navbar-toggler <?php echo animatedfsm_render_button_position( $button_position ); ?> <?php echo ( $button_image ? 'custom-burger' : '' ); ?> <?php echo esc_attr( $mobile_class ); ?>"
        type="button">
        <?php if( $button_image ){ 
			echo '<img class="animatedfsmenu-custom-image" src="' . esc_url( $button_image ) . '" />';
		} else { ?>
        <div class="bar top"></div>
        <div class="bar bot"></div>
        <div class="bar mid"></div>
        <?php } ?>
    </button>
	<?php } ?>

    <div class="navbar-collapse animatedfs_menu_list">
        <?php
		if ( 'on' === $searchbar_on ) {
			?>
        <div class="afsmenu_search">
            <form action="<?php echo get_site_url(); ?>" autocomplete="off">
                <input id="search" name="s" type="text" placeholder="<?php echo esc_attr( $searchbar_placeholder ); ?>">
                <div class="search_submit">
                    <i class="fas fa-search"></i>
                </div>
            </form>
        </div>
        <?php
		}
		// get languages if there is Polylang Plugin installed
		if ( 'on' === $language_switcher ) {
			animatedfsm_getlanguages( 'slug', true );
		}
	
	$container_class = '';
	if( $animatedfsm_scroll ){
		$container_class = 'afsmenu_scroll';
	}
	$schema_for_url = $add_schemaorg ? ' itemscope itemtype="http://schema.org/SiteNavigationElement"' : '';
	$schema_class   = $add_schemaorg ? ' afsmenu-schemaorg' : '';

	if ( 'none' !== $menu_id && 'menulocation' != $menu_id ) {

			wp_nav_menu(
				array(
					'menu' 			  => $menu_id,
					'menu_class' 	  => 'afsmenu' . $schema_class,
					'container'       => 'div',
					'container_class' => $container_class,
					'items_wrap'      => '<ul id="%1$s" class="%2$s"' . $schema_for_url . '>%3$s</ul>',

				)
			);
	} else if ( 'menulocation' === $menu_id ){
			wp_nav_menu(
				array(
					'menu_class' 	  => 'afsmenu' . $schema_class,
					'theme_location'  => 'animated-fullscreen-menu',
					'container'       => 'div',
					'container_class' => $container_class,
					'items_wrap'      => '<ul id="%1$s" class="%2$s"' . $schema_for_url . '>%3$s</ul>',

					)
			);
	}
	echo $html_shortcodes;
	?>
        <?php if ( count( $social_media_array ) > 0 ) : ?>
        <div class="social-media">
            <ul>
                <?php
					foreach ( $social_media_array as $social ) :
						$url = ( isset( $social['animatedfsm_url'] ) ? $social['animatedfsm_url'] : '#' );
						?>
                <li>
                    <a href="<?php echo esc_url( $url ); ?>" title="<?php echo esc_attr( $social['title'] ); ?>"
                        target="_blank">
                        <i class="fa <?php echo esc_attr( $social['icon'] ); ?>"></i>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
			endif;

		if ( $move_to_element_class ) {

			echo '
			<style>
			.animatedfsmenu .animatedfsmenu-navbar-toggler{
				display:none; 
			}
			</style>
			<script>
			jQuery(document).ready(function(){
				const menu = jQuery("#animatedfsmenu_css");
				const toggler = jQuery(".animatedfsmenu-navbar-toggler");
				jQuery("' . $move_to_element_class . '").append(menu);
				jQuery(toggler).appendTo("' . $move_to_element_class . '");
			});
			</script>';
				
		}

		if ( class_exists( 'WooCommerce' ) && 'on' === $woocommerce_on ) :
			// get woocommerce pages id
			animatedfsm_render_woocommerce_menu();
		endif;
		if( class_exists( 'WooCommerce' ) && 'on' === $woocommerce_cart_on ) :
			animatedfsm_render_cart();
		endif;

		$privacy_policy = get_option( 'wp_page_for_privacy_policy' );

		if ( 'on' === $privacy_policy_on && $privacy_policy ) {
			?>
		<div class="privacy_policy">
			<?php
			echo '<a href="' . esc_attr( get_the_permalink( $privacy_policy ) ) . '" target="_blank">' . esc_html( get_the_title( $privacy_policy ) ) . '</a>';
			?>
		</div>
			<?php
		}
		?>
		<?php  $afs_renderaction = do_action( 'animatedfsmenu_aftermenu' ); ?>

	</div>

</div>

<?php

}


add_action( $render_hook, 'render_animatedfsmenu_nav' );

function animatedfsmenu_enqueue_scripts()  { //phpcs:ignore
	// Get the render hook
	global $is_preview;

	if ( is_admin() && ! $is_preview ) {
		return;
	}
	

	wp_enqueue_style( 'afsmenu-styles', plugins_url( '/frontend/css/nav.css', __FILE__ ), '', animatedfsmenu_get_plugin_version() );
	wp_enqueue_script( 'afsmenu-scripts', plugins_url( '/frontend/js/nav.js', __FILE__ ), array('jquery'), animatedfsmenu_get_plugin_version(), true );
	
}


function animatedfsm_enqueue_fontawesome() { //phpcs:ignore
	wp_enqueue_style( 'afsmenu-font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css', array(), '6.0.0' );
}


function animatedfsm_enqueue_owlcarousel() { //phpcs:ignore
	wp_enqueue_style( 'afsmenu-owl-carousel', plugins_url( '/frontend/vendor/owl-carousel/owl.carousel.min.css', __FILE__ ) );
	wp_enqueue_script('afsmenu-owl-carousel-script', plugins_url( '/frontend/vendor/owl-carousel/owl.carousel.min.js', __FILE__ ), null, null, true );

}

function animatedfsm_enqueue_google_fonts( $font ) { //phpcs:ignore
	wp_enqueue_style( 'afsmenu-google-fonts', '//fonts.googleapis.com/css?family=' . $font );

}

function animatedfsm_render_button_position( $button_position = 'right_top' ){
	return $button_position;	
}

function animatedfsm_render_woocommerce_menu() {
	$woocommerce_menu = animatedfsmenu_get_woocommerce_menu();

	$render_woocommerce_menu = '<ul class="animatedfsmenu_woocommerce">';

	foreach ( $woocommerce_menu as $menu ) {
		$render_woocommerce_menu .= '<li><a href="' . esc_attr( $menu['page_url'] ) . '"><i class="fas ' . esc_attr( $menu['icon'] ) . '"></i>' . esc_html( $menu['page_title'] ) . '</a></li>';
	}

	$render_woocommerce_menu .= '</ul>';

	echo $render_woocommerce_menu; //phpcs:ignore - Escapes already done on the HTML construct
}

function animatedfsm_render_cart() { //phpcs:ignore
	global $woocommerce;
	$currency    = get_woocommerce_currency_symbol();
	$cart        = $woocommerce->cart->get_cart();
	$actual_cart = array();

	foreach ( $cart as $item => $values ) {

		$temp_id = $values['product_id'];

		$temp = array(
			'id'       => $temp_id,
			'quantity' => $values['quantity'],
			'total'    => $values['line_total'],
			'name'     => get_the_title( $temp_id ),
			'url'      => get_the_permalink( $temp_id ),
			'image'    => wp_get_attachment_url( get_post_thumbnail_id( $temp_id ) ),
		);

		array_push( $actual_cart, $temp );
	}

	if ( count( $actual_cart ) > 0 ) {
		animatedfsm_enqueue_owlcarousel();
		echo '<h3 class="afs-cart-title">' . esc_html__( 'Products in cart', 'animated-fullscreen-menu' ) . '</h3>';
		$render_cart = '<div class="afs-owl-cart owl-carousel">';
		foreach ( $actual_cart as $item ) :
			$formatted_price = wc_price( $item['total'] );
			
			$render_cart .= '<div class="item">';
			$render_cart   .= '<a href="' . $item['url'] . '" title="' . $item['name'] . '">';
			$render_cart      .= '<div class="afs_item-container">';
			$render_cart         .= '<div class="afs_item__img" style="background-image:url(' . $item['image'] . ');"></div>';
			$render_cart         .= '<h3 class="afs_item__title">' . $item['name'] . '</h3>';
			$render_cart         .= '<p class="afs_item__qtt">' . $item['quantity'] . ' ' . __( 'units', 'animated-fullscreen-menu') . '</p>';
			$render_cart         .= '<p class="afs_item__total">' . $formatted_price . '</p>';
			$render_cart      .= '</div>';
			$render_cart   .= '</a>';
			$render_cart .= '</div>';

		endforeach;
		echo $render_cart .= '</div>';

		echo $script_cart = '<script>
						function afs_owl_cart(){

							jQuery(".afs-owl-cart").owlCarousel({
								loop:false,
								margin:10,
								nav:true,
								responsive:{
									0:{
										items:2
									},
									600:{
										items:3
									},
									1000:{
										items:4
									}
								}
							});
						
						}
						</script>';
		
	}
}

function animatedfsmenu_get_wysiwyg_output( $content ) {
	global $wp_embed;


	$content = $wp_embed->autoembed( $content );
	$content = $wp_embed->run_shortcode( $content );
	$content = do_shortcode( $content );

	return $content;
}

function render_animatedfsmenu_backgroundimages( $background_image ) { //phpcs:ignore

	$background_image_id = attachment_url_to_postid( $background_image );
	$background_image    = wp_get_attachment_metadata( $background_image_id );
	if ( ! $background_image ) {
		return;
	}
	$background_image_sizes = $background_image['sizes'];
	if ( '' !== $background_image_sizes && null !== $background_image_sizes ) {
		$background_image_sizes = array_reverse( $background_image_sizes );
	}
	?>
<style>
.animatedfsmenu .animatedfs_background {
    background-image: url(<?php echo esc_attr( wp_upload_dir()['baseurl'] . '/'. $background_image['file']);
    ?>);
}

<?php if (count($background_image_sizes) > 0) {
    foreach ($background_image_sizes as $sizes=> $image):

        ?> @media screen and (max-width: <?php echo esc_attr($image['width']); ?>px) {
        .animatedfsmenu .animatedfs_background {
            background-image: url(<?php echo esc_attr( wp_get_attachment_image_url( $background_image_id, $sizes ));
            ?>);
        }
    }

    <?php endforeach;
}

?>
</style>
<?php	
}

function render_animatedfsmenu_backgroundvideo( $background_video ){
	?> 
	<style>
		#afsmenu_video{
			position: fixed;
			right: 0;
			bottom: 0;
			min-width: 100%;
			min-height: 100%;
		}
	</style>
	<script>
		function afsmenu_render_video(){
			var video = '<video autoplay muted loop id="afsmenu_video"><source src="<?php echo esc_attr( $background_video ); ?>" type="video/mp4"></video>';
			jQuery('.animatedfs_background').html(video);
		}
	</script>

	<?php
}

// Menu Item has Children - Edit
add_filter('nav_menu_css_class', 'animatedfsmenu_edit_class_has_children', 10, 3);

function animatedfsmenu_edit_class_has_children( $classes, $items, $args ){
	if( ! in_array( 'menu-item-has-children', $classes ) ){
		return $classes;
	}
	foreach( $classes as $i => $class ){
		if( $class == 'menu-item-has-children'){
			$classes[$i] = 'afs-menu-item-has-children';
		}
	}
	return $classes;
}

// Add schema.org markup to menu items
add_filter( 'nav_menu_link_attributes', 'animatedfsmenu_add_schema_to_menu', 99, 3 );
function animatedfsmenu_add_schema_to_menu( $atts, $item, $args ) {

	if ( empty( $args->menu_class ) || ! str_contains( $args->menu_class, 'afsmenu-schemaorg' ) ) {
		return $atts;
	}
	$atts['itemprop'] = 'url';
	return $atts;
}


