<?php
/**
 * @package WPGlobus Translate Options
 * @subpackage Administration
 *
 * @since 1.4.0
 */

if ( ! class_exists( 'WPGlobus_TO_Customize_Options' ) ) :
	/**
	 * Class WPGlobus_Customize_Options
	 */
	class WPGlobus_TO_Customize_Options {
		
		protected static $translate_option_page = false;
		
		/**
		 * Controller
		 */
		public static function controller( $translate_option_page ) {
			
			self::$translate_option_page = $translate_option_page;
			
			add_action( 'wpglobus_customize_register', array(
				'WPGlobus_TO_Customize_Options',
				'customize_register'
			) );

			add_action( 'wpglobus_customize_data', array(
				'WPGlobus_TO_Customize_Options',
				'customize_data'
			) );

			add_action( 'admin_print_scripts', array(
				'WPGlobus_TO_Customize_Options',
				'on_admin_scripts'
			) );			
			
		}
	
		/**
		 * Enqueue admin scripts
		 *
		 * @return void
		 */	
		public static function on_admin_scripts() {
			
			wp_register_script(
				'wpglobus-to-cusomizer',
				plugin_dir_url( __FILE__ ) . 'js/wpglobus-translate-options-customizer' . WPGlobus::SCRIPT_SUFFIX() . ".js",
				array( 'jquery' ),
				WPGLOBUS_TRANSLATE_OPTIONS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-to-cusomizer' );
			wp_localize_script(
				'wpglobus-to-cusomizer',
				'WPGlobusTOCustomizer',
				array(
					'version'		=> WPGLOBUS_TRANSLATE_OPTIONS_VERSION,
					'toOptionPage'	=> admin_url() . 'admin.php?page=' . self::$translate_option_page
				)
			);
			
		}
		
		public static function customize_data( $data ) {
			$data[ 'sections' ][ 'wpglobus_to_section' ] = 'wpglobus_to_section';
			//$data[ 'settings' ][ 'wpglobus_to_section' ][ 'wpglobus_customize_plus_selector_menu_style' ][ 'type' ]   = 'checkbox';
			//$data[ 'settings' ][ 'wpglobus_to_section' ][ 'wpglobus_customize_plus_selector_menu_style' ][ 'option' ] = 'switcher_menu_style';			
			return $data;
		}	
		
		/**
		 * Callback for customize_register
		 * 
		 * @param WP_Customize_Manager $wp_customize
		 */
		public static function customize_register( WP_Customize_Manager $wp_customize ) {
			
			/**
			 * SECTION: WPGlobus Translate Options
			 */		
			$wp_customize->add_section( 'wpglobus_to_section' , array(
				'title'      => __( 'WPGlobus Translate Options', 'wpglobus' ),
				'priority'   => 90,
				'panel'		 => 'wpglobus_settings_panel'
			) );

			/** WPGlobusTO dummy to options page */
			$wp_customize->add_setting( 'wpglobus_to_customize_dummy', array( 
				'type' => 'option',
				'capability' => 'manage_options',
				'transport' => 'postMessage'
			) );			

			$wp_customize->add_control( 'wpglobus_to_customize_dummy', array(
					'settings' 		=> 'wpglobus_to_customize_dummy',
					'title'   		=> __( 'Title', 'wpglobus' ),
					'label'   		=> __( 'Label', 'wpglobus' ),
					'section' 		=> 'wpglobus_to_section',
					'type'    		=> 'checkbox',
					'priority'  	=> 10,
					'description' 	=> __( 'Description', 'wpglobus' ),
				)	
			);

		}

			
	}

endif;