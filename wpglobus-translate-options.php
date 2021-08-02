<?php
/**
 * Plugin Name: WPGlobus Translate Options
 * Plugin URI: https://github.com/WPGlobus/wpglobus-translate-options
 * Description: Translate options from 'wp_options' table for <a href="https://wordpress.org/plugins/wpglobus/">WPGlobus</a>.
 * Text Domain: wpglobus-translate-options
 * Domain Path: /languages/
 * Version: 1.9.0
 * Author: WPGlobus
 * Author URI: https://wpglobus.com/
 * Network: false
 * Copyright 2015-2021 Alex Gor (alexgff) / WPGlobus
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl.txt
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPGLOBUS_TRANSLATE_OPTIONS_VERSION', '1.9.0' );

add_filter( 'wpglobus_option_sections', 'filter__wpglobus_to_add_option_section' );
/**
 * Filter the value of an option.
 * @see filter `wpglobus_option_sections` wpglobus\includes\options\class-wpglobus-options.php
 *
 * @since 1.0.0
 * @since 1.6.0
 * @since 1.8.0 Plugin tab moved up on WPGlobus Options page.
 *
 * @param array $sections Array of the options.
 * @return array
 */
function filter__wpglobus_to_add_option_section( $sections ) {
	
	if ( empty( $sections ) ) {
		return $sections;
	}
	
	$pos = array_search( 'wpglobus-plus', array_keys( $sections ), true );
	
	if ( ! $pos ) {
		
		$pos = array_search( 'rest-api', array_keys( $sections ), true );
		
		if ( ! $pos ) {
			$pos = count( $sections );
		}
		
	}

	$section['translate-strings'] = array(
		'wpglobus_id'  => 'translate_options_link',
		'title' 	   => esc_html__( 'Translate strings', 'wpglobus' ),
		'icon' 		   => 'dashicons dashicons-admin-tools',
		'tab_href'     => add_query_arg( 'page', 'wpglobus-translate-options', admin_url( 'admin.php' ) ),
		'externalLink' => true
	);

	$sections = array_merge(
		array_slice( $sections, 0, $pos + 1 ),
		$section,
		array_slice( $sections, $pos + 1 )
	);
	
	return $sections;
}

add_action( 'plugins_loaded', 'wpglobus_translate_options_load', 11 );
function wpglobus_translate_options_load() {
	if ( defined( 'WPGLOBUS_VERSION' ) ) {
		require_once 'includes/class-wpglobus-translate-options.php';
		new WPGlobus_Translate_Options(
			array(
				'plugin_file' => __FILE__
			)
		);
	}
}

# --- EOF