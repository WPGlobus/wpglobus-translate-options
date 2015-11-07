/**
 * WPGlobus Translate Options
 * Interface JS functions
 *
 * @since 1.0.0
 *
 * @package WPGlobus Translate Options
 * @subpackage Administration
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCore, WPGlobusCoreData*/
var WPGlobusTranslateOptions;

jQuery(document).ready(function($) {
	"use strict";
	var api;
	api = WPGlobusTranslateOptions = {

		init : function() {
			
			api.setFloatBlock( $('h2.nav-tab-wrapper').offset().top );
			
			$('.wpglobus-translate').on('click', function(event){
				
				var opt = $(this).data('source'),
					s = $('#wpglobus_translate_options'),
					v = s.val(),
					cr = '\n';
					
				if ( '' == v ) {
					cr = '';		
				}	
				s.val(v+cr+opt);
			
			});
			
		},
		
		setFloatBlock : function(fromTopPx){
			var $float_block = $('.float-block');
			$(window).scroll(function(){
				var scrolledFromtop = $(window).scrollTop();
				if (scrolledFromtop > fromTopPx) {
					$float_block.addClass('fixed-block');
				} else {
					$float_block.removeClass('fixed-block');
				}
			});
		}		
	
	
	};

	WPGlobusTranslateOptions.init();

});