/* global jQuery */

jQuery( function ( $ )
{
	'use strict';

	var storageKey = 'cmb2_active_tab';

	// Function to activate a tab by panel name
	function activateTab( panel ) {
		var $li = $( '.cmb-tab-nav li[data-panel="' + panel + '"]' );

		if ( $li.length ) {
			var $wrapper = $li.parents( '.cmb-tabs' ).find( '.cmb2-wrap-tabs' );
			var $panel = $wrapper.find( '.cmb-tab-panel-' + panel );

			$li.addClass( 'cmb-tab-active' ).siblings().removeClass( 'cmb-tab-active' );
			$panel.addClass( 'show' ).siblings().removeClass( 'show' );
		}
	}

	// On page load, restore the active tab from localStorage
	var savedTab = localStorage.getItem( storageKey );
	if ( savedTab ) {
		activateTab( savedTab );
	}

	// On tab click, save the active tab to localStorage
	$( '.cmb-tab-nav' ).on( 'click', 'a', function ( e )
	{
		e.preventDefault();

		var $li = $( this ).parent(),
			panel = $li.data( 'panel' ),
			$wrapper = $li.parents( '.cmb-tabs' ).find( '.cmb2-wrap-tabs' ),
			$panel = $wrapper.find( '.cmb-tab-panel-' + panel );

		$li.addClass( 'cmb-tab-active' ).siblings().removeClass( 'cmb-tab-active' );
		$panel.addClass( 'show' ).siblings().removeClass( 'show' );

		// Save the active tab to localStorage
		localStorage.setItem( storageKey, panel );
	} );
});
