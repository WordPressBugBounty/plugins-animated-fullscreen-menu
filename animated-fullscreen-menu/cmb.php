<?php


if ( file_exists( dirname( __FILE__ ) . '/vendor/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/CMB2/init.php';
}


if ( file_exists( dirname( __FILE__ ) . '/vendor/cmb2-field-faiconselect/iconselect.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/cmb2-field-faiconselect/iconselect.php';
}

if ( file_exists( dirname( __FILE__ ) . '/vendor/cmb2-tabs/cmb2-tabs.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/cmb2-tabs/cmb2-tabs.php';
}

function animatedfsmenu_backend_styles() { //phpcs:ignore
	wp_enqueue_style( 'styles-fullscreen-menu', plugins_url( 'admin/css/styles.css', __FILE__ ), array(), '1.0' );
}
add_action( 'admin_enqueue_scripts', 'animatedfsmenu_backend_styles' );

if ( file_exists( dirname( __FILE__ ) . '/vendor/cmb2-conditionals/cmb2-conditionals.php' ) && isset($_GET['page']) && 'animatedfsm_settings' == $_GET['page'] ) {
	require_once dirname( __FILE__ ) . '/vendor/cmb2-conditionals/cmb2-conditionals.php';
}


/**
 * Conditionally displays a metabox when used as a callback in the 'show_on_cb' cmb2_box parameter
 *
 * @param  CMB2 $cmb CMB2 object.
 *
 * @return bool      True if metabox should show
 */
function animatedfsmenu_show_if_front_page( $cmb ) {
	// Don't show this metabox if it's not the front page template.
	if ( get_option( 'page_on_front' ) !== $cmb->object_id ) {
		return false;
	}
	return true;
}

/**
 * Conditionally displays a field when used as a callback in the 'show_on_cb' field parameter
 *
 * @param  CMB2_Field $field Field object.
 *
 * @return bool              True if metabox should show
 */
function animatedfsmenu_hide_if_no_cats( $field ) {
	// Don't show this field if not in the cats category.
	if ( ! has_tag( 'cats', $field->object_id ) ) {
		return false;
	}
	return true;
}


add_action( 'cmb2_admin_init', 'animatedfsmenu_register_theme_options_metabox' );
/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
function animatedfsmenu_register_theme_options_metabox() {

	

	$pro_user = animatedfsm()->is__premium_only();
	$cmb2_tabs_args = apply_filters( 'animatedfsmenu_cmb2_tabs', 
		array(
			'id'           => 'animatedfsmenu_theme_options_page',
			'title'        => esc_html__( 'Fullscreen Menu Options', 'animated-fullscreen-menu' ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'animatedfsm_settings',
			'icon_url'     => 'dashicons-menu',
			'tabs'         => array(
				'settings'    => array(
					'label' => __( 'Settings', 'animated-fullscreen-menu' ),
					'icon'  => 'dashicons-admin-settings',
				),
				'menu-button-position' => array(
					'label' => __( 'Menu Button Position', 'animated-fullscreen-menu' ),
					'icon'  => 'dashicons-menu',
				),
				'design' => array(
					'label' => __( 'Design/Appearence', 'animated-fullscreen-menu' ),
					'icon'  => 'dashicons-admin-appearance',
				),
				'content' => array(
					'label' => __( 'Menu Content', 'animated-fullscreen-menu' ),
					'icon'  => 'dashicons-networking',
				),
				'seo' => array(
					'label' => __( 'SEO', 'animated-fullscreen-menu' ),
					'icon'  => 'dashicons-search',
				),
				'woocommerce' => array(
					'label' => __( 'WooCommerce', 'animated-fullscreen-menu' ) . ( ! class_exists( 'WooCommerce' ) ? __( '(Not installed)', 'animated-fullscreen-menu' ) : '' ),
					'icon'  => 'dashicons-products',
				),
				'remove_data' => array(
					'label' => __( 'Remove Data', 'animated-fullscreen-menu' ),
					'icon'  => 'dashicons-trash',
				),
			),
			'tab_style'    => 'classic',
		),
	 );
	/**
	 * Registers options page menu item and form.
	 */
	$cmb_options = new_cmb2_box( $cmb2_tabs_args );

	$cmb_options->add_field(
		array(
			'name'          => esc_html__( 'Activate Animated Fullscreen Menu', 'animated-fullscreen-menu' ),
			'id'            => 'animatedfsm_on',
			'type'          => 'checkbox',
			'default'       => 'yes',
			'tab'           => 'settings',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name' => esc_html__( 'Show the menu only for Admin users', 'animated-fullscreen-menu' ),
			'desc' => esc_html__( 'Useful for testing before launching' , 'animated-fullscreen-menu' ),
			'id'   => 'animatedfsm_testing_mode',
			'type' => 'checkbox',
			'tab'  => 'settings',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);


	$cmb_options->add_field(
		array(
			'name'          => esc_html__( 'Disable the default Menu button (Hamburger)', 'animated-fullscreen-menu' ),
			'desc'          => esc_html__( 'You can deactivate the fixed Button and add it as a Block (Fullscreen Menu Button).', 'animated-fullscreen-menu' ),
			'id'            => 'animatedfsm_disable_button',
			'type'          => 'checkbox',
			'tab'           => 'menu-button-position',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name' => esc_html__( 'Mobile only?', 'animated-fullscreen-menu' ),
			'desc' => esc_html__( 'This menu should be appears only for mobile devices? We consider mobile devices as fewer than 1024px resolution.', 'animated-fullscreen-menu' ),
			'id'   => 'animatedfsm_mobile_only',
			'type' => 'checkbox',
			'tab'           => 'settings',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);



	$cmb_options->add_field(
		array(
			'name'          => esc_html__( 'Menu Toggle Button Position', 'animated-fullscreen-menu' ),
			'id'            => 'animatedfsm_toggle_position',
			'type'          => 'title',
			'tab'           => 'menu-button-position',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);


	$cmb_options->add_field(
		array(
			'name'          => esc_html__( 'Show the Menu Toggle in a HTML Element:', 'animated-fullscreen-menu' ),
			'desc'          => esc_html__( 'Add the Class of the HTML element where you want to show the menu toggle. Example: .header-container', 'animated-fullscreen-menu' ) . ( ! $pro_user ? '<br><span>' . __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  . '</span>' : '' ),
			'id'            => 'animatedfsm_element_class',
			'type'          => 'text',
			'classes'       => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
			'tab'           => 'menu-button-position',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);


	$cmb_options->add_field(
		array(
			'name' => esc_html__( 'Background Image/Color', 'animated-fullscreen-menu' ),
			'id'   => 'animatedfsm_title1',
			'type' => 'title',
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);


	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Initial Background Menu', 'animated-fullscreen-menu' ),
			'desc'    => esc_html__( 'First color (closed Menu).', 'animated-fullscreen-menu' ),
			'id'      => 'animatedfsm_background01',
			'type'    => 'colorpicker',
			'default' => '#000000',
			'options' => array( 'alpha' => true ),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Opened Background Menu', 'animated-fullscreen-menu' ),
			'desc'    => esc_html__( 'Menu color when is opened.', 'animated-fullscreen-menu' ),
			'id'      => 'animatedfsm_background02',
			'type'    => 'colorpicker',
			'default' => '#45aacc',
			'options' => array( 'alpha' => true ),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name' => esc_html__( 'Background Image (PRO feature)', 'animated-fullscreen-menu' ),
			'desc' => ( $pro_user ? esc_html__( 'Background Image when menu is opened. Leave blank to use colors above.', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' ) ),
			'id'   => 'animatedfsm_backgroundimage',
			'type' => 'file',
			'classes'     => ( !$pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'          => esc_html__( 'Background Video (PRO feature)', 'animated-fullscreen-menu' ),
			'desc'          => ( $pro_user ? esc_html__( 'Background Video when menu is opened. Leave blank to use colors or image above.', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
			'id'            => 'animatedfsm_backgroundvideo',
			'type'          => 'file',
			'classes'       => ( !$pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name' => esc_html__( 'Menu Appearance', 'animated-fullscreen-menu' ),
			'id'   => 'animatedfsm_title2',
			'type' => 'title',
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);
	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Number of Columns', 'animated-fullscreen-menu' ),
			'id'      => 'animatedfsm_number_of_columns',
			'type'    => 'text',
			'desc'    =>  ( $pro_user ? __( 'Distribute the menu items through columns', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
			'classes' => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
			'default' => 1,
			'tab'     => 'design',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),

		)
	);
	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Font Color', 'animated-fullscreen-menu' ),
			'desc'    => esc_html__( 'Color for fonts, social media icons and navbar hamburger.', 'animated-fullscreen-menu' ),
			'id'      => 'animatedfsm_textcolor',
			'type'    => 'colorpicker',
			'default' => '#FFFFFF',
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Select Menu', 'animated-fullscreen-menu' ),
			'desc'    => esc_html__( 'Create the Menus on Appearance->Menus page. If you select WordPress Menu Location, you need to define your Menu at Appearance->Menus', 'animated-fullscreen-menu' ),
			'id'      => 'animatedfsm_menuselected',
			'type'    => 'select',
			'options' => animatedfsm_get_menus(),
			'tab'           => 'content',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);
	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Free HTML / Shortcodes', 'animated-fullscreen-menu' ),
			'desc'    => esc_html__( 'Here you can put any HTML code or Shortcodes.', 'animated-fullscreen-menu' ),
			'id'      => 'animatedfsm_html',
			'type'    => 'wysiwyg',
			'options' => array(),
			'tab'           => 'content',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Show all the submenus opened by default', 'animated-fullscreen-menu' ),
			'desc'    => ( $pro_user ? esc_html__( 'Open all menu levels.', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
			'id'      => 'animatedfsm_openlevels',
			'type'    => 'checkbox',
			'classes' => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
			'tab'           => 'settings',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name' => esc_html__( 'Close menu on clicking a menu item', 'animated-fullscreen-menu' ),
			'desc' => esc_html__( 'Useful if you are using the menu with anchor links.', 'animated-fullscreen-menu' ),
			'id'   => 'animatedfsm_anchor',
			'type' => 'checkbox',
			'tab'           => 'settings',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Activate Multi-Level Menu (for more than one submenus)', 'animated-fullscreen-menu' ),
			'desc'       => ( $pro_user ? esc_html__( 'Activate this option for displaying more than one level of submenu. <br>Example: About Us>Team>John Smith.', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
			'id'      => 'animatedfsm_multi_level_menu',
			'classes' => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
			'type'    => 'checkbox',
			'tab'           => 'settings',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);


	
	
	$cmb_options->add_field(
		array(
			'name' => esc_html__( 'Auto Hide on scroll', 'animated-fullscreen-menu' ),
			'desc' => ( $pro_user ? esc_html__( 'Activate this option for displaying more than one level of submenu. <br>Example: About Us>Team>John Smith.', 'animated-fullscreen-menu' ) : esc_html__( 'Close the Menu on scroll', 'animated-fullscreen-menu' ) ),
			'id'   => 'animatedfsm_autohide_scroll',
			'type' => 'checkbox',
			'tab'  => 'settings',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	
	$cmb_options->add_field(
		array(
			'name' 			=> esc_html__( 'Lateral menu at specific pages?', 'animated-fullscreen-menu' ),
			'desc' 			=> esc_html__( 'This option transforms your menu into a lateral side menu. Does not affect mobile devices.', 'animated-fullscreen-menu' ),
			'id'   			=> 'animatedfsm_lateralmenu',
			'type' 			=> 'checkbox',
			'tab'  		    => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	

	$cmb_options->add_field(
		array(
			'name'       => esc_html__( 'Pages with lateral menu', 'animated-fullscreen-menu' ),
			'id'         => 'animatedfsm_lateralmenu_pages',
			'type'       => 'multicheck',
			'options'    => animatedfsm_get_allpages(),
			'attributes' => array(
				'data-conditional-id'    => 'animatedfsm_lateralmenu',
				'data-conditional-value' => 'on',
			),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'       => esc_html__( 'Hide Menu on specific pages', 'animated-fullscreen-menu' ),
			'id'         => 'animatedfsm_hide_menu_pages',
			'type'       => 'multicheck',
			'options'    => animatedfsm_get_allpages(),
			'tab'           => 'settings',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Select Font Family (Google Fonts)', 'animated-fullscreen-menu' ),
			'desc'    => ( ! $pro_user ? '<strong>' . esc_html__( 'Upgrade to PRO to get all the available Google Fonts.', 'animated-fullscreen-menu' ) . '</strong><br>' : '' ) . esc_html__( 'Select "Default" if you want use the current font of your theme.', 'animated-fullscreen-menu' ),
			'id'      => 'animatedfsm_font',
			'type'    => 'select',
			'options' => animatedfsm_get_fonts( $pro_user ),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Select Font Weight', 'animated-fullscreen-menu' ),
			'desc'    => esc_html__( 'Font Weight for Menu Text.', 'animated-fullscreen-menu' ),
			'id'      => 'animatedfsm_fontweight',
			'type'    => 'select',
			'options' => animatedfsm_get_fontsweight(),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'       => esc_html__( 'Select Font Size (PRO feature)', 'animated-fullscreen-menu' ),
			'desc'       => ( $pro_user ? esc_html__( 'Font Size for Menu Text. Examples: 12px, 4rem, 2em', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
			'id'         => 'animatedfsm_fontsize',
			'type'	     => 'text',
			'classes'		  => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
			'tab'             => 'design',
			'render_row_cb'   => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		)
	);

	$cmb_options->add_field(
		array(
			'name' 			=> esc_html__( 'Add scrolling to Main Menu', 'animated-fullscreen-menu' ),
			'desc'			=> esc_html__( 'Select this if your Main Menu needs more height', 'animated-fullscreen-menu' ),
			'id'   			=> 'animatedfsm_scroll',
			'type' 			=> 'checkbox',
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);


	$cmb_options->add_field(
		array(
			'name' 			=> esc_html__( 'Text Shadow (PRO feature)', 'animated-fullscreen-menu' ),
			'id'   			=> 'animatedfsm_title',
			'type' 			=> 'title',
			'tab'           => 'design',
			'classes'       => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'          => esc_html__( 'Activate Text Shadow', 'animated-fullscreen-menu' ),
			'desc'          => ( $pro_user ? esc_html__( 'Activate a shadow for all text elements', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
			'id'            => 'animatedfsm_textshadow_activate',
			'type'			=> 'checkbox',
			'classes'       => ( ! $pro_user ? array( 'animatedfsm_pro_feature', 'animated_textshadow_number' ) : array('animated_textshadow_number') ),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);


	$cmb_options->add_field(
		array(
			'name'          => esc_html__( 'Horizontal (px)', 'animated-fullscreen-menu' ),
			'desc'          => ( $pro_user ? esc_html__( 'The position of the horizontal shadow', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
			'id'            => 'animatedfsm_textshadow_horizontal',
			'type'	        => 'text',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
			'default'		=> 2,
			'classes'       => ( ! $pro_user ? array( 'animatedfsm_pro_feature', 'animated_textshadow_number' ) : array('animated_textshadow_number') ),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'          => esc_html__( 'Vertical (px)', 'animated-fullscreen-menu' ),
			'desc'          => ( $pro_user ? esc_html__( 'The position of the vertical shadow', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
			'id'            => 'animatedfsm_textshadow_vertical',
			'type'	        => 'text',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
			'default'		=> 2,
			'classes'       => ( ! $pro_user ? array( 'animatedfsm_pro_feature', 'animated_textshadow_number' ) : array('animated_textshadow_number') ),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);
	
	$cmb_options->add_field(
		array(
			'name'          => esc_html__( 'Blur Radius (px)', 'animated-fullscreen-menu' ),
			'desc'          => ( $pro_user ? esc_html__( 'The blur radius', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
			'id'            => 'animatedfsm_textshadow_blur_radius',
			'type'	        => 'text',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
			'default'		=> 8,
			'classes'       => ( ! $pro_user ? array( 'animatedfsm_pro_feature', 'animated_textshadow_number' ) : array('animated_textshadow_number') ),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);
	
	$cmb_options->add_field(
		array(
			'name'          => esc_html__( 'Color', 'animated-fullscreen-menu' ),
			'desc'          => ( $pro_user ? esc_html__( 'The color of the shadow', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
			'id'            => 'animatedfsm_textshadow_color',
			'type'	        => 'colorpicker',
			'default'		=> '#000000',
			'classes'	    => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);
	

	$cmb_options->add_field(
		array(
			'name' 			=> esc_html__( 'Include Privacy Policy Page (GPRD)', 'animated-fullscreen-menu' ),
			'desc'			=> esc_html__( 'This page is selected at Settings -> Privacy.', 'animated-fullscreen-menu' ),
			'id'   			=> 'animatedfsm_privacy_on',
			'type' 			=> 'checkbox',
			'tab'           => 'content',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'id'         => 'socialicons_group',
			'name'       => '<br>' . esc_html__( 'Social Icons', 'animated-fullscreen-menu' ) . '<br><br>',
			'type'       => 'group',
			'repeatable' => true,
			'required'   => false,
			'options'    => array(
				'group_title'   => 'Social Icon {#}',
				'add_button'    => esc_html__( 'Add Another Icon' ),
				'remove_button' => 'Remove Icon',
				'closed'        => true,
				'sortable'      => true,
			),
			'tab'           => 'content',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_group_field(
		'socialicons_group',
		array(
			'name' => esc_html__( 'Social Icon Title', 'animated-fullscreen-menu' ),
			'desc' => esc_html__( 'Enter the post title for the link text.', 'animated-fullscreen-menu' ),
			'id'   => 'title',
			'type' => 'text',
		)
	);

	
	$cmb_options->add_group_field(
		'socialicons_group',
		array(
			'name'       => esc_html__( 'Social Icon', 'animated-fullscreen-menu' ),
			'id'         => 'icon',
			'desc'       => __( 'Choose the Icon (Font Awesome library).' ),
			'type'       => 'faiconselect',
			'options'    => array(
					'fa-brands fa-facebook-f' => 'fa-brands fa-facebook-f',
					'fa-brands fa-tiktok'        => 'fa-brands fa-tiktok',
					'fa-brands fa-instagram'     => 'fa-brands fa-instagram',
					'fa-brands fa-twitter'       => 'fa-brands fa-twitter',
					'fa-brands fa-github'        => 'fa-brands fa-github',
					'fa-solid fa-envelope' => 'fa-solid fa-envelope',
					'fab fa-behance'       => 'fab fa-behance',
					'fab fa-linkedin'      => 'fab fa-linkedin',
					'fab fa-slack'         => 'fab fa-slack',
					'fab fa-vimeo'         => 'fab fa-vimeo',
					'fab fa-whatsapp'      => 'fab fa-whatsapp',
					'fab fa-youtube'       => 'fab fa-youtube',
					'fab fa-wordpress'     => 'fab fa-wordpress',
					'fab fa-spotify'       => 'fab fa-spotify',
					'fab fa-xing'          => 'fab fa-xing',
			),
		),
	);

	$cmb_options->add_group_field(
		'socialicons_group',
		array(
			'name' => esc_html__( 'Social URL', 'animated-fullscreen-menu' ),
			'desc' => esc_html__( 'Enter the url of the social media.', 'animated-fullscreen-menu' ),
			'id'   => 'animatedfsm_url',
			'type' => 'text_url',
		)
	);
	$cmb_options->add_field(
		array(
			'name' => esc_html__( 'Animation Settings', 'animated-fullscreen-menu' ),
			'id'   => 'animatedfsm_title3',
			'type' => 'title',
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Animation Type', 'animated-fullscreen-menu' ),
			'desc'    => esc_html__( 'Select your animation direction.', 'animated-fullscreen-menu' ),
			'id'      => 'animatedfsm_animation',
			'type'    => 'select',
			'options' => array(
				'opacity' => 'Opacity (0 to 100)',
				'top'     => 'From Top to Bottom',
				'left'    => 'From Left to Right',
				'right'   => 'From Right to Left',
			),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);


	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Side Menu', 'animated-fullscreen-menu' ),
			'desc'    => esc_html__( 'Show a Side lateral menu, instead of fullscreen menu.', 'animated-fullscreen-menu' ),
			'id'      => 'animatedfsm_sidemenu',
			'type'    => 'checkbox',
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'    => esc_html__( 'Hover Effects', 'animated-fullscreen-menu' ),
			'desc'    => esc_html__( 'Select your hover/focus animation.', 'animated-fullscreen-menu' ),
			'id'      => 'animatedfsm_hoverfocus',
			'type'    => 'select',
			'options' => array(
				'animation_line'  	  => 'Padding Line',
				'animation_background'  => 'Background Color on Hover',
				'animation_background__border_radius'  => 'Background Color on Hover (Rounded border)',
			),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name'       => esc_html__( 'Hover Animation Background', 'animated-fullscreen-menu' ),
			'id'         => 'animatedfsm_hoverbackground',
			'type'       => 'colorpicker',
			'attributes' => array(
				'data-conditional-id'    => 'animatedfsm_hoverfocus',
				'data-conditional-value' => 'animation_background',
			),
			'default' => 'rgba(0,0,0,0.91)',
			'options' => array( 'alpha' => true ),
			'tab'           => 'design',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);


	if ( function_exists( 'pll_the_languages' ) ) :
		$cmb_options->add_field(
			array(
				'name' => esc_html__( 'Add Language Switcher', 'animated-fullscreen-menu' ),
				'desc' => esc_html__( 'Check this if you want to use language switcher from Polylang Plugin.', 'animated-fullscreen-menu' ),
				'id'            => 'animatedfsm_languageswitcher',
				'type'          => 'checkbox',
				'tab'           => 'content',
				'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
			)
		);
	endif;

	if ( class_exists( 'WooCommerce' ) ) :
		$cmb_options->add_field(
			array(
				'name' => esc_html__( 'Add WooCommerce Menu', 'animated-fullscreen-menu' ),
				'desc' => esc_html__( 'Adds "My Account", "Shop", "Cart" and "Checkout" menus', 'animated-fullscreen-menu' ),
				'id'   => 'animatedfsm_woocommerce_on',
				'type' => 'checkbox',
				'tab'           => 'woocommerce',
				'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
			)
		);
	

	endif;

	$cmb_options->add_field(
		array(
			'name' => esc_html__( 'Add Search Bar?', 'animated-fullscreen-menu' ),
			'desc' => esc_html__( 'Adds a native WP search bar.', 'animated-fullscreen-menu' ),
			'id'   => 'animatedfsm_searchbar_on',
			'type' => 'checkbox',
			'tab'           => 'content',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	$cmb_options->add_field(
		array(
			'name' => esc_html__( 'Search input placeholder', 'animated-fullscreen-menu' ),
			'desc' => esc_html__( 'Adds a native WP search bar.', 'animated-fullscreen-menu' ),
			'id'   => 'animatedfsm_searchbar_placeholder',
			'type' => 'text',
			'default' => esc_html__( 'Search something...', 'animated-fullscreen-menu'),
			'tab'           => 'content',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	

	$cmb_options->add_field(
		array(
			'name' 			=> esc_html__( 'Remove Data after uninstall', 'animated-fullscreen-menu' ),
			'desc' 			=> esc_html__( 'If you do not select this option, your data is not deleted.', 'animated-fullscreen-menu' ),
			'id'   			=> 'animatedfsm_removedata_on',
			'type' 			=> 'checkbox',
			'tab'           => 'remove_data',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		)
	);

	


        $cmb_options->add_field(
			array(
				'name'        => esc_html__( 'Button position (PRO feature)', 'animated-fullscreen-menu' ),
				'desc'        => ( $pro_user ? esc_html__( 'Select the position for the Burger.', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
				'id'          => 'animatedfsm_button_position',
				'type'    	  => 'select',
				'classes'     => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
				'options'     => array(
					'right_top'     => 'Right Top',
					'left_top'      => 'Left Top',
					'center_top'    => 'Center Top',
					'right_bottom'  => 'Right Bottom',
					'left_bottom'   => 'Left Bottom',
					'center_bottom' => 'Center Bottom',
				),
				'tab'           => 'menu-button-position',
				'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
			)
		);


        $cmb_options->add_field(
			array(
				'name'    		=> esc_html__( 'Burger Button Image (PRO feature)', 'animated-fullscreen-menu' ),
				'desc'    		=> ( $pro_user ? esc_html__( 'Replace the burger button for a custom image.', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
				'id'      		=> 'animatedfsm_button_image',
				'type'    		=> 'file',
				'classes'     => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
				'tab'           => 'menu-button-position',
				'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
			)
		);

		$cmb_options->add_field(
			array(
				'name'    		=> esc_html__( 'Menu Text Align (PRO feature)', 'animated-fullscreen-menu' ),
				'desc'    		=> ( $pro_user ? esc_html__( 'Align the text to the following.', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
				'id'      		=> 'animatedfsm_text_align',
				'type'    		=> 'select',
				'options' 		=> array(
					'align_left'   => 'Align to Left',
					'align_center' => 'Align to Center',
					'align_right'  => 'Align to Right'
				),
				'classes'       => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
				'tab'           => 'design',
				'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
			)
		);

		$cmb_options->add_field(
			array(
				'name'    		=> esc_html__( 'Disafix Burger Button (PRO feature)', 'animated-fullscreen-menu' ),
				'desc'    		=> ( $pro_user ? __( 'Disafix the Burger Button on the DOM', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
				'id'      		=> 'animatedfsm_unfix_button',
				'type'    		=> 'checkbox',
				'classes'       => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
				'tab'           => 'menu-button-position',
				'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
			)
		);

		$cmb_options->add_field(
			array(
				'name'    		=> esc_html__( 'Add Schema.org structure to the menu', 'animated-fullscreen-menu' ),
				'desc'    		=> esc_html__( 'Schema.org/SiteNavigationElement is important for SEO and improves user experience by providing structured data to search engines.', 'animated-fullscreen-menu' ),
				'id'      		=> 'animatedfsm_add_schemaorg',
				'type'    		=> 'checkbox',
				'tab'           => 'seo',
				'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
			),
		);

		if ( class_exists( 'WooCommerce' ) ) :

			$cmb_options->add_field(
				array(
					'name' 			=> esc_html__( 'Add WooCommerce Cart (PRO feature)', 'animated-fullscreen-menu' ),
					'desc' 			=> ( $pro_user ? esc_html__( 'Display a carousel with the products the user has in cart', 'animated-fullscreen-menu' ) : __( 'PRO feature - Try the PRO Version of your plugin, activating it  on the <strong>Fullscreen Menu Options->Free 7 Day Pro Trial</strong>', 'animated-fullscreen-menu' )  ),
					'id' 			=> 'animatedfsm_woocommerce_cart_on',
					'type' 			=> 'checkbox',
					'classes'     => ( ! $pro_user ? array( 'animatedfsm_pro_feature' ) : '' ),
					'tab'           => 'woocommerce',
					'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
				)
			);

		endif;
    
		do_action( 'animatedfsm_add_fields', $cmb_options, $pro_user );


}


function animatedfsm_get_menus() { //phpcs:ignore

	$animatedfsm_all_menus = get_terms(
		'nav_menu',
		array(
			'hide_empty' => true,
		)
	);

	$array_menus         = [];

	if ( $animatedfsm_all_menus ) {
		foreach ( $animatedfsm_all_menus as $menu ) {
			$array_menus[ $menu->term_id ] = $menu->name;
		}
	}
	$array_menus['none'] = __( 'None/Empty', 'animated-fullscreen-menu' );
	$array_menus['menulocation'] = __( 'WordPress Menu Location', 'animated-fullscreen-menu' );

	return $array_menus;
}

function animatedfsm_get_fonts( $pro_user ) { //phpcs:ignore
	
	$google_fonts_name = [
		'Nunito Sans',
		'Amiko',
		'Archivo Black',
		'Roboto',
		'Open Sans',
		'Jomolhari',
		'Bowlby One SC',
		'Lato',
		'Lora',
		'Oswald',
		'Source Sans Pro',
		'Montserrat',
		'Raleway',
		'PT Sans',
		'Prompt',
		'Ubuntu',
		'Work Sans',
	];

	if ( $pro_user ) {
		// Get Google Fonts from this URL API: https://wp-fullscreen-menu.com/wp-json/animatedfsmenu/v1/googlefonts
		$google_fonts_from_api = wp_remote_get( 'https://wp-fullscreen-menu.com/wp-json/animatedfsmenu/v1/googlefonts' );
		if ( $google_fonts_from_api && isset( $google_fonts_from_api['body'] ) ) {	
			$google_fonts_name = json_decode( $google_fonts_from_api['body'] );
		}
	}
	$google_fonts;

	$google_fonts['inherit'] = 'Default';

	foreach ( $google_fonts_name as $font ) {
		$google_fonts[ $font ] = $font;
	}
	return $google_fonts;
}

function animatedfsm_get_fontsweight() {
	$fonts_weight = array(
		'100' => '100',
		'300' => '300',
		'400' => '400',
		'500' => '500',
		'700' => '700',
		
	);
	return $fonts_weight;
}


function animatedfsm_get_allpages() {
	
	$allpages = array();
	$page_ids = get_all_page_ids();

	foreach( $page_ids as $page ) {
		$allpages[ $page ] = get_the_title( $page );
	}

	return $allpages;

}
