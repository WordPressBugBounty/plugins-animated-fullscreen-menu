( function ( blocks, element ) {
	var el = element.createElement;
	const { InspectorControls } = wp.blockEditor;

	let burger =  '';

	
	blocks.registerBlockType( 'animated-fullscreen-menu/hamburger', {
		attributes: {
			mobileOnly: {
				type: 'boolean',
				default: false,
			},
		},
		edit: function (props) {
			var blockProps = wp.blockEditor.useBlockProps();
			var isChecked = props.attributes.mobileOnly;
		
			var checkbox = el('input', {
				type: 'checkbox',
				id: 'mobileOnlyCheckbox',
				checked: isChecked,
				onChange: function (event) {
					props.setAttributes({ mobileOnly: event.target.checked });
				},
			});
		
			var label = el('label', {
				htmlFor: 'mobileOnlyCheckbox',
				style: { marginRight: '10px' },
			}, 'Mobile Only');
		
			burger = el( 'button', {
				className: 'animatedfsmenu-navbar-toggler right_top animatedfsmenu-navbar-toggler__block' + ( isChecked ? ' animatedfsmenu__mobile' : '' ),
			}, 
				el( 'div', {
					className: 'bar top',
				}),
				el( 'div', {
					className: 'bar bot',
				}),
				el( 'div', {
					className: 'bar mid',
				}),
			);
			
			return [
				el(InspectorControls, null,
					el('div', { className: 'components-panel__body' },
						el('div', null, label, checkbox)
					)
				),
				el('div', blockProps,
					burger
				)
			];
		},
		save: function () {
			return burger;
		},
		getEditWrapperProps: function (attributes) {
			return { 'data-align': attributes.align };
		},
	} );
} )( window.wp.blocks, window.wp.element );

