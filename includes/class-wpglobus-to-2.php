<?php
/**
 * File: class-wpglobus-to-2.php
 *
 * @package WPGlobus Translate Options
 * @subpackage Administration
 *
 * @since 2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPGlobus_Translate_Options_2' ) ) :

	/**
	 * Class WPGlobus_Translate_Options_2.
	 */
	class WPGlobus_Translate_Options_2 {

		/**
		 * Options page.
		 */
		const TRANSLATE_OPTIONS_PAGE = 'wpglobus-translate-options';

		/**
		 * WPGlobus Translate Options options key for `options` table.
		 */
		const PLUGIN_OPTIONS_KEY = WPGLOBUS_TRANSLATE_OPTIONS_KEY;
		
		const OPTION_TO_TRANSLATE_KEY = 'wpglobus_translate_options';
		
		const OPTION_ALL_OPTIONS_TABLE_ITEMS_PER_PAGE = 'allOptionsTableItemsPerPage';
		
		const OPTION_OPTIONS_TO_TRANSLATE_TABLE_ITEMS_PER_PAGE = 'optionsToTranslateTableItemsPerPage';
	
		const OPTION_DISABLED_MASKS_KEY = 'wpglobus_disabled_masks';
		
		const OPTION_INTERFACE_VERSION_KEY = WPGLOBUS_TRANSLATE_OPTIONS_INTERFACE_VERSION_KEY;

		/**
		 * @var bool $_SCRIPT_DEBUG Internal representation of the define('SCRIPT_DEBUG')
		 */
		protected static $_SCRIPT_DEBUG = false;

		/**
		 * @var string $_SCRIPT_SUFFIX Whether to use minimized or full versions of JS and CSS.
		 */
		protected static $_SCRIPT_SUFFIX = '.min';

		/**
		 * @var array of plugin options.
		 */
		protected $options = array();

		/**
		 * @var array of disabled for loading options.
		 * @see options.txt file
		 */
		var $disabled_options = array();

		/**
		 * @var array of disabled for loading masks, may be updated by user
		 * @see masks.txt file
		 */
		var $disabled_masks = array();

		/**
		 * @var array of option keys, in v.1.0.0 use $keys[0] only
		 */
		var $keys = array();

		/**
		 * @var string from multidimensional array assembled by + sign
		 */
		var $order = '';

		/**
		 * @since 2.0.0
		 */
		protected $args = array();

		/**
		 * @since 2.0.0
		 */		
		protected static $rest_namespace = 'wpglobus-to/v1';
		
		/**
		 * @since 2.0.0
		 */		
		protected static $rest_base = 'options';

		/**
		 * @since 2.0.0
		 */		
		protected static $endpoints = null;
		
		/**
		 * Constructor.
         */
		function __construct( $args ) {

			/**
			 * @since 2.0.0
			 */
			$defaults = array(
				'plugin_file' => '',
			);

			/**
			 * @since 2.0.0
			 */
			$this->args = wp_parse_args( $args, $defaults );

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				self::$_SCRIPT_DEBUG  = true;
				self::$_SCRIPT_SUFFIX = '';
			}
 
			$this->options = get_option( self::PLUGIN_OPTIONS_KEY );

			if ( is_admin() ) {

				add_action( 'admin_menu', array(
					$this,
					'on__admin_menu'
				) );
				
				add_action( 'admin_print_scripts', array(
					$this,
					'on__admin_scripts'
				) );

				add_filter( 'plugin_action_links_' . plugin_basename( $this->get_arg( 'plugin_file' ) ), array(
					$this,
					'filter__plugin_action_links'
				) );
				
				/*
				// global $pagenow;
				// if ( 'customize.php' === $pagenow ) {

					//if ( version_compare( $wp_version, '4.5.0', '<=' ) ) {
						// require_once 'customize-options-wp44.php';
						// WPGlobus_TO_Customize_Options::controller( self::TRANSLATE_OPTIONS_PAGE );
					//} else {
						//require_once 'includes/customize-options-wp45.php';
					//}

				// }
				// */
			} else {

				/**
				 * @scope front.
				 */

				if ( ! empty($this->options[ self::get_option_to_translate_key() ]) ) :

					// foreach ( $this->options['wpglobus_translate_options'] as $option ) {
					foreach ( $this->options[ self::get_option_to_translate_key() ] as $option ) {
						$keys = explode('+', $option );

						$this->keys[] = $keys;

						add_filter( 'option_' . $keys[0], array(
							$this,
							'filter__translate_option'
						) );
					}

				endif;
				
				if ( class_exists('Cookie_Notice') && defined('WPGLOBUS_TRANSLATE_OPTIONS_COOKIE_NOTICE') && WPGLOBUS_TRANSLATE_OPTIONS_COOKIE_NOTICE ) {
					/**
					 * @see https://wordpress.org/plugins/cookie-notice/
					 */
					add_filter('cn_cookie_notice_args', array($this, 'filter__translate_option') );
				}

			}
		}

		/**
		 * Get option keys.
		 */
		public static function get_option_keys() {
			return array(
				'plugin_options_key' => self::get_plugin_options_key(),
				'to_translate_key' => self::get_option_to_translate_key(),
				'disabled_masks_key' => self::get_option_disabled_masks_key(),
				'interface_version_key' => self::get_option_interface_version_key(),
				self::OPTION_ALL_OPTIONS_TABLE_ITEMS_PER_PAGE => self::get_option_all_options_table_items_per_page(),
				self::OPTION_OPTIONS_TO_TRANSLATE_TABLE_ITEMS_PER_PAGE => self::get_option_options_to_translate_table_items_per_page(),
			);
		}
		
		/**
		 * Get PLUGIN_OPTIONS_KEY.
		 */
		public static function get_plugin_options_key() {
			return self::PLUGIN_OPTIONS_KEY;
		}
	
		/**
		 * Get OPTION_TO_TRANSLATE_KEY.
		 */	
		public static function get_option_to_translate_key() {
			return self::OPTION_TO_TRANSLATE_KEY;
		}
		
		/**
		 * Get OPTION_DISABLED_MASKS_KEY.
		 */	
		public static function get_option_disabled_masks_key() {
			return self::OPTION_DISABLED_MASKS_KEY;
		}
	
		/**
		 * Get OPTION_INTERFACE_VERSION_KEY.
		 */	
		public static function get_option_interface_version_key() {
			return self::OPTION_INTERFACE_VERSION_KEY;
		}	
			
		/**
		 * Get OPTION_ALL_OPTIONS_TABLE_ITEMS_PER_PAGE.
		 */	
		public static function get_option_all_options_table_items_per_page() {
			return self::OPTION_ALL_OPTIONS_TABLE_ITEMS_PER_PAGE;
		}	
	
		/**
		 * Get OPTION_OPTIONS_TO_TRANSLATE_TABLE_ITEMS_PER_PAGE.
		 */
		public static function get_option_options_to_translate_table_items_per_page() {
			return self::OPTION_OPTIONS_TO_TRANSLATE_TABLE_ITEMS_PER_PAGE;
		}		 
		 
		/**
		 * Get REST namespace.
		 */	
		public static function get_rest_namespace() {
			return self::$rest_namespace;
		}
	
		/**
		 * Get REST base.
		 */	
		public static function get_rest_base() {
			return self::$rest_base;
		}	

		/**
		 * Get themes info.
		 */	
		public static function get_themes() {
			
			$themes = array();
			
			$theme = wp_get_theme();
			$theme_caption = 'Active theme'; 
			$params = array( 
				'Name', 
				'ThemeURI', 
				'Description', 
				'Author', 
				'AuthorURI', 
				'Version', 
				'Template', 
				#'Status', 
				#'Tags', 
				'TextDomain', 
				'DomainPath' 
			);
					
			$parent_template = $theme->get('Template');	
			
			if ( empty( $parent_template ) ) {
				//
			} else {
				$parent_theme = wp_get_theme( get_template() );
				$theme_caption .= ' (child theme)'; 
				$parent_theme_caption = 'Parent theme'; 
			}				

			if ( empty( $parent_template ) ) {
				
				$themes['parent'] = array(
					'name' => $theme->get('Name'),
					'template' => get_template(),
					'themeModsOption' => 'theme_mods_' . get_template(),
				);
				$themes['child'] = false;
				
			} else {

				$themes['parent'] = array(
					'name'	=> $parent_theme->get('Name'),
					'template' => $parent_theme->get_template(),
					'themeModsOption' => 'theme_mods_' . get_template(),
				);
				$themes['child'] = array(
					'name' => $theme->get('Name'),
					'template' => $theme->get_template(),
					'themeModsOption' => 'theme_mods_' . get_stylesheet(),
				);	
			}
			
			return $themes;
		}
		
		/**
		 * Get Endpoint by default.
		 */	
		public static function get_endpoint_by_default( $endpoint = 'optionsToTranslate' ) {
			$endpoints = self::get_endpoints();
			
			if ( ! isset($endpoints[$endpoint] ) ) {
				return '';
			}
			
			return $endpoints[$endpoint];
		}
		
		/**
		 * Get Endpoints.
		 */	
		public static function get_endpoints() {
			
			if ( ! is_null( self::$endpoints ) ) {
				return self::$endpoints;
				
			}
			
			self::$endpoints = array(
				'optionsToTranslate' => array(
					'route' => 'totranslate',
					'tableName' => 'optionsToTranslate',
					'options' => array(
						'itemsPerPage' => self::OPTION_OPTIONS_TO_TRANSLATE_TABLE_ITEMS_PER_PAGE
					)
				),
				'themeOptions' => array(
					'route' => 'themeOptions',
					'tableName' => 'themeOptions',
					'options' => array(
						'itemsPerPage' => 2,					
						'tablePageSize' => 5,					
					)						
				),
				'allOptions' => array(
					'route' => '',
					'readable_callback' => 'get_items',
					'tableName' => 'allOptions',
					'options' => array(
						'itemsPerPage' => self::OPTION_ALL_OPTIONS_TABLE_ITEMS_PER_PAGE
					)
				),
				'getAboutInfo' => array(
					'route' => 'about',
					'readable_callback' => 'get_about_info'
				),
				'getMasks' => array(
					'route' => 'masks',
					'readable_callback' => 'get_mask_items',
					'editable_callback' => 'update_masks'
				),
				'updateMasks' => array(
					'route' => 'masks',
					'readable_callback' => 'get_mask_items',
					'editable_callback' => 'update_masks'
				),
				'getRaw' =>  array(
					'route' => 'raw',
					'readable_callback' => 'get_raw',
				),
				'translateIt' => array(
					'route' => 'translateit',
					'readable_callback' => 'get_items_to_translate',
				),
				'updateItemsPerPage' => array(
					'route' => 'itemsperpage',
					'readable_callback' => 'get_option_items_per_page',
					'editable_callback' => 'update_option_items_per_page',
				),
				'switchInterface' => array(
					'route' => 'switchInterface',
					'readable_callback' => 'get_option_interface_version', // 'get_option_interface_version',
					'editable_callback' => 'update_option_interface_version' // 'update_option_interface_version',					
				),
				// 'getOption' => array(
					// 'route' => 'getOptionValue',
					// 'route' => '/\S+/',
					// 'readable_callback' => 'get_option_value', // 'get_option_value'				
				// )				
			);
			
			return self::$endpoints;
		}	

		/**
		 * Get option.
		 */
		public function get_option( $option, $default = null ) {
			if ( ! isset( $this->options[ $option ] ) ) {
				return $default;
			}
			return $this->options[ $option ];
		}
		
		/**
		 * Enqueue admin scripts.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		function on__admin_scripts() {
			
			/** @global string $pagenow */
			global $pagenow;
				
			if ( $pagenow == 'admin.php' && isset($_GET['page']) && self::TRANSLATE_OPTIONS_PAGE == $_GET['page']  ) :

				$namespace = self::get_rest_namespace();
				$rest_base = self::get_rest_base(); 
				
				$full_route = 'wp-json/' . $namespace . '/' . trim( $rest_base, '/' );

				global $wp_version;

				$data = array(
					'debug' 	  => defined('WPGLOBUS_TO_DEV') ? 'true' : 'false',
					'nonce' 	  => wp_create_nonce( 'wpglobus_to_nonce' ),
					'wpVersion'   => $wp_version,
					'version'     => WPGLOBUS_TRANSLATE_OPTIONS_VERSION,
					'mode'		  => 'production',
					'homeUrl'	  => home_url(),
					'namespace'   => $namespace,
					'fullRoute'   => $full_route,
					'apiBaseUrl'  => home_url($full_route.'/'),
					'endPoints'   => self::get_endpoints(),
					'endPointByDefault'    => self::get_endpoint_by_default(),
					'defaultTablePageSize' => $this->get_option('defaultTablePageSize', 15),
					self::OPTION_ALL_OPTIONS_TABLE_ITEMS_PER_PAGE => $this->get_option(self::OPTION_ALL_OPTIONS_TABLE_ITEMS_PER_PAGE, 25),
					'optionsToTranslateTableItemsPerPage' => $this->get_option('optionsToTranslateTableItemsPerPage', 10),
					'interfaceVersion' => $this->get_option('interface_version', '2'),
				);

				if ( defined('WPGLOBUS_TO_DEV_ASSETS_URL') && WPGLOBUS_TO_DEV_ASSETS_URL ) {
					$app_css = WPGLOBUS_TO_DEV_ASSETS_URL . 'css/wpglobus-to-app.css';
					$app_js  = WPGLOBUS_TO_DEV_ASSETS_URL . 'js/wpglobus-to-app.js';
				} else {
					$app_css = plugin_dir_url( __FILE__ ) . 'assets/css/wpglobus-to-app.css';
					$app_js  = plugin_dir_url( __FILE__ ) . 'assets/js/wpglobus-to-app.js';
				}

				wp_enqueue_style( 'wpglobus-to-app', $app_css, array(), WPGLOBUS_TRANSLATE_OPTIONS_VERSION );

				wp_register_script(
					'wpglobus-to-app',
					$app_js,
					array(),
					WPGLOBUS_TRANSLATE_OPTIONS_VERSION,
					true
				);
				wp_enqueue_script( 'wpglobus-to-app' );
				wp_localize_script(
					'wpglobus-to-app',
					'WPGlobusTranslateOptions',
					$data
				);
				
			endif;
		}
	
		/**
		 * Add a link to the settings page to the plugins list.
		 * @since 1.4.1
		 *
		 * @param array $links array of links for the plugins, adapted when the current plugin is found.
		 *
		 * @return array $links
		 */
		public function filter__plugin_action_links( $links ) {
			$settings_link = '<a class="" href="' . esc_url( admin_url( 'admin.php?page=' . self::TRANSLATE_OPTIONS_PAGE ) ) . '">' . esc_html__( 'Options' ) . '</a>';
			array_unshift( $links, $settings_link );
			return $links;
		}

		/**
		 * Get passed argument.
		 *
		 * @since 2.0.0
		 */
		function get_arg( $arg = '' ) {
			
			if ( empty( $arg ) ) {
				return null;
			}
			
			if ( ! isset( $this->args[ $arg ] ) ) {
				return null;
			}
			
			return $this->args[ $arg ];
		}	

		/**
		 * Extract strings of current language.
		 *
		 * @since 1.4.5
		 *
		 * @param  array|string $options array or string to extract.
		 * @return array|string
		 */
		public function _translate( $options ) {

			if ( is_array($options) ) {

				foreach( $options as $key=>$value ) {

					if ( empty( $value ) ) {
						continue;
					}

					/**
					 * exclude from translation the objects.
					 */
					if ( is_object( $value ) ) {
						continue;
					}

					if ( is_array($value) ) {
						$options[$key] = $this->_translate( $value );
					} else {
						if ( WPGlobus_Core::has_translations( $value ) ) {
							if ( defined('WPGLOBUS_TRANSLATE_OPTIONS_USE_TEXT_FILTER') && WPGLOBUS_TRANSLATE_OPTIONS_USE_TEXT_FILTER ) {
								$options[$key] = WPGlobus_Core::text_filter( $value, WPGlobus::Config()->language );
							} else {
								$options[$key] = WPGlobus_Core::extract_text( $value, WPGlobus::Config()->language );
							}
						}
					}

					/** @todo for next versions with translation individual fields */
					/*
					if ( !empty($translating_keys) && in_array($key, $translating_keys) ) {
						$options[$key] = WPGlobus_Core::text_filter($value, WPGlobus::Config()->language);
					}
					*/

				}

			} elseif ( is_string($options) ) {
				if ( WPGlobus_Core::has_translations( $options ) ) {
					if ( defined('WPGLOBUS_TRANSLATE_OPTIONS_USE_TEXT_FILTER') && WPGLOBUS_TRANSLATE_OPTIONS_USE_TEXT_FILTER ) {
						$options = WPGlobus_Core::text_filter( $options, WPGlobus::Config()->language );
					} else {
						$options = WPGlobus_Core::extract_text( $options, WPGlobus::Config()->language );
					}
				}
			}

			return $options;
		}

		/**
		 * Add hidden submenu.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function on__admin_menu() {

			add_submenu_page(
				null,
				'',
				'',
				'administrator',
				self::TRANSLATE_OPTIONS_PAGE,
				array(
					$this,
					'on__translate_options_page'
				)
			);
		}

		/**
		 * Output options page.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function on__translate_options_page() {
			$_caption = esc_html__( 'WPGlobus Translate Options', '' ) . ': v.' . WPGLOBUS_TRANSLATE_OPTIONS_VERSION;
			?>
			<div class="wrap">
				<h2><?php
					/**
					 * @quirk
					 * This should be H2, so that it goes above the WP admin notices
					 */
					echo $_caption;
				?></h2>
				<div id="wpglobus-to-2-app" class="wrap translate_options-wrap" style=""></div>
			</div><!-- .wrap -->	
			<?php
		}

		/**
		 * Filter the value of an option.
		 * @see filter 'option_' . $option in \wp-includes\option.php
		 *
		 * @scope front
		 * @since 1.0.0
		 *
		 * @param mixed $options Value of the option.
		 * @return mixed
		 */
		public function filter__translate_option( $options ) {

			if ( is_admin() || is_object($options) ) {
				return $options;
			}

			/** @todo for next versions with translation individual fields */
			/**
			$translating_keys = array();

			foreach( $this->keys as $opt=>$keys ) {

				$value = $options;
				$k = 0;
				for( $i=1; $i < count($keys); $i++ ) {

					if ( isset($keys[$i]) ) {

						$value = $value[$keys[$i]];
						$k = $i;
					}

				}

				if ( $k > 0 && ! is_array($keys[$k]) ) {
					$translating_keys[] = $keys[$k];
				}

			}	*/

			$options = $this->_translate( $options );

			return $options;
		}

		/**
		 * @obsolete @since 2.0.0
		 *
		 * Check disabled option
		 *
		 * @since 1.0.0
		 *
		 * @param string $option
		 * @return boolean
		 */
		function check_masks($option) {

			foreach($this->disabled_masks as $mask) {
				if ( empty($mask) ) {
					continue;
				}
				if ( 0 === strpos($option, $mask) ) {
					return true;
				}

			}

			return false;

		}

		/**
		 * @obsolete @since 2.0.0
		 *
		 * Get item.
		 * @since 1.0.0
		 *
		 * @param string $key
		 * @param string|array|object $items // TODO this is a code smell
		 * @param string $option
		 * @param string $chain
		 *
		 * @return string
		 */
		function get_item($key, $items, $option='', $chain='') {

			if ( false === $option ) {
				// do nothing
			} else {
				if ( '' == $chain ) {
					$this->order = '+' . $key;
					$chain = $key;
				} else {
					$this->order = '+' .$chain . '+' . $key;
					$chain .= '+' . $key;
				}
			}

			$return  = '<ul>';
			$return .= '<li>';
			if ( is_array($items) || is_object($items) ) {
				$return .= $this->convert($option . $this->order);
				foreach ($items as $k=>$v) {
					$return .= $this->get_item($k, $v, $option, $chain);
				}
			} else {
				if ( empty($items) )  {
					$items = '<textarea readonly cols="100"></textarea>';
				} else {
					$items = '<textarea readonly cols="100">' . $items . '</textarea>';
				}
				if ( false === $option ) {
					$return .= $this->convert($key) . $items;
				} else {
					$return .= $this->convert($option . $this->order) . $items;
				}
			}
			$return .= '</li>';
			$return .= '</ul>';

			return $return;
		}
		
		/**
		 * @obsolete @since 2.0.0
		 *		
		 * Convert string.
		 * @since 1.0.0
		 *
		 * @param string $str
		 *
		 * @return string
		 */
		function convert($str) {
			$r = '';
			$arr = explode('+', $str);
			$i = 0;
			foreach( $arr as $v ) {
				if ( $i == 0 ) {
					$r  = $v;
				} else {
					$r .= '[' . $v . ']';
				}
				$i++;
			}
			return '<div style="vertical-align:top;">&nbsp;&nbsp;&nbsp;<strong>' . $r . '</strong></div>';
		}
	}

endif; // class WPGlobus_Translate_Options_2.

# --- EOF