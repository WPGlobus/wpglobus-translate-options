/**
 * WPGlobus Translate Options Customizer
 * Interface JS functions
 *
 * @since 1.4.0
 *
 * @package WPGlobus Translate Options
 * @subpackage Administration
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCustomizeOptions, WPGlobusTOCustomizer*/
jQuery(document).ready(function($) {
	"use strict";

	if ( 'undefined' === typeof WPGlobusCustomizeOptions ) {
		return;	
	}

	if ( 'undefined' === typeof WPGlobusTOCustomizer ) {
		return;
	}	

	$(document).on( 
		'click',
		'#accordion-panel-wpglobus_settings_panel',
		function(ev) {
			$( '#accordion-section-' + WPGlobusCustomizeOptions.sections.wpglobus_to_section ).css( 'display', 'block' );
		}
	);	
	
	/** open Translate Options page in new tab */
	$( '#accordion-section-' + WPGlobusCustomizeOptions.sections.wpglobus_to_section + ' .accordion-section-title' ).off( 'click keydown' );
	$(document).on( 
		'click',
		'#accordion-section-' + WPGlobusCustomizeOptions.sections.wpglobus_to_section + ' .accordion-section-title',
		function(ev) {
			window.open( WPGlobusTOCustomizer.toOptionPage, '_blank' );
		}
	);
});