<?php
/**
 * Plugin Name: WPGlobus Translate Options
 * Plugin URI: https://github.com/WPGlobus/wpglobus-translate-options
 * Description: Translate options from wp_options table for <a href="https://wordpress.org/plugins/wpglobus/">WPGlobus</a>.
 * Version: 1.3.1
 * Author: WPGlobus
 * Author URI: http://www.wpglobus.com/
 * Network: false
 * License: GPL2
 * Credits: Alex Gor (alexgff) and Gregory Karpinsky (tivnet)
 * Copyright 2015 WPGlobus
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPGLOBUS_TRANSLATE_OPTIONS_VERSION', '1.3.1' );

add_filter( 'wpglobus_option_sections', 'wpglobus_add_options_section' );
/**
 * Filter the value of an option.
 * @see filter wpglobus_option_sections
 *
 * @since 1.0.0
 *
 * @param array $sections Array of the options.
 * @return array
 */
function wpglobus_add_options_section( $sections ) {

	$sections[] = array(
		'title' => 'Translation options',
		'icon' => 'el-icon-link',
		'class' => 'wpglobus-translate-options-group',
		'fields' => array(
			array(
				'id'       => 'translate_options_link',
				'type'     => version_compare( WPGLOBUS_VERSION, '1.2.2', '>=' ) ? 'wpglobus_info' : 'info',
				'title'    => 'Click to open <a href="admin.php?page=wpglobus-translate-options">Translate options page</a>',
				'style'    => 'info',
			)

		)
	);
	
	return $sections;
	
}

add_action( 'plugins_loaded', 'wpglobus_translate_options_load', 11 );
function wpglobus_translate_options_load() {
	if ( defined( 'WPGLOBUS_VERSION' ) ) { 
		new WPGlobus_Translate_Options();
	}	
}

if ( ! class_exists( 'WPGlobus_Translate_Options' ) ) :
	
	/**
	 * WPGlobus_Translate_Options
	 * @todo Move to a separate file
	 */
	class WPGlobus_Translate_Options {

		/**
		 * All options page 
		 */
		const TRANSLATE_OPTIONS_PAGE = 'wpglobus-translate-options';	
	
		/**
		 * WPGlobus Translate Options about page
		 */	
		const ABOUT_PAGE = 'wpglobus-translate-options-about';	
	
		/**
		 * WPGlobus Translate Options settings page
		 */	
		const SETTINGS_PAGE = 'wpglobus-translate-options-settings';	

		/**
		 * WPGlobus Translate Options options key
		 */			
		const TRANSLATE_OPTIONS_KEY = 'wpglobus_translate_options';	
		
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
		var $options = array();
	
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
	
		/** */
		function __construct() {

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				self::$_SCRIPT_DEBUG  = true;
				self::$_SCRIPT_SUFFIX = '';
			}
			
			$this->options = get_option( self::TRANSLATE_OPTIONS_KEY );
			
			if ( is_admin() ) {
				
				add_action( 'admin_menu', array(
					$this,
					'on_admin_menu'
				) );				
				
				add_action( 'admin_print_styles', array(
					$this,
					'on_admin_styles'
				) );				
				
				add_action( 'admin_print_scripts', array(
					$this,
					'on_admin_scripts'
				) );
				
			} else {
			
				if ( !empty($this->options['wpglobus_translate_options']) ) :
			
					foreach ( $this->options['wpglobus_translate_options'] as $option ) {
						$keys = explode('+', $option );
						
						$this->keys[] = $keys;
						
						add_filter( 'option_' . $keys[0], array(
							$this,
							'on_translate_option'
						) );			
					}
				
				endif;
				
			}

		}

		/**
		 * Filter the value of an option.
		 * @see filter 'option_' . $option in \wp-includes\option.php
		 *
		 * @since 1.0.0
		 *
		 * @param mixed $options Value of the option.
		 * @return mixed
		 */
		function on_translate_option( $options ) {
			
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

			if ( is_array($options) ) {	
				foreach( $options as $key=>$value ) {
					
					if ( is_array($value) ) {
						$options[$key] = $this->on_translate_option($value);
					} else {	
						$options[$key] = WPGlobus_Core::text_filter($value, WPGlobus::Config()->language);	
					}

					/** @todo for next versions with translation individual fields */
					/*
					if ( !empty($translating_keys) && in_array($key, $translating_keys) ) {
						$options[$key] = WPGlobus_Core::text_filter($value, WPGlobus::Config()->language);	
					}
					*/

				}
			} elseif ( is_string($options) ) {	
				$options = WPGlobus_Core::text_filter($options, WPGlobus::Config()->language);	
			}
			
			return $options;
			
		}
		
		/**
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
		 * Add hidden submenu
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function on_admin_menu() {
			
			add_submenu_page(
				null,
				'',
				'',
				'administrator',
				self::TRANSLATE_OPTIONS_PAGE,
				array(
					$this,
					'on_translate_options_page'
				)
			);

			add_submenu_page(
				null,
				'',
				'',
				'administrator',
				self::SETTINGS_PAGE,
				array(
					$this,
					'on_translate_options_page'
				)
			);
			
			add_submenu_page(
				null,
				'',
				'',
				'administrator',
				self::ABOUT_PAGE,
				array(
					$this,
					'on_translate_options_page'
				)
			);			
			
		}
		
		/**
		 * Output options page
		 *
		 * @since 1.0.0
		 * @return void
		 */	
		function on_translate_options_page() {
			
			/** @global string $pagenow */
			global $pagenow;
			
			/** @global wpdb $wpdb */
			global $wpdb;

			/** @todo These two vars are set inside a condition. Should refactor. */
			$page = '';
			$option = false;

			$tab_active = array();
			$tab_active[self::TRANSLATE_OPTIONS_PAGE] = '';
			$tab_active[self::SETTINGS_PAGE] 		  = '';

			if ( $pagenow == 'admin.php' && isset($_GET['page']) ) : 
				
				$page = $_GET['page'];
				
				if ( self::TRANSLATE_OPTIONS_PAGE == $page  ) {			

					$tab_active[self::TRANSLATE_OPTIONS_PAGE] = ' nav-tab-active';
					if ( isset($_GET['option']) ) {
				
						$option = $_GET['option'];
						
					} else {	

						$option = false;
						
					}	
				
				} elseif ( self::SETTINGS_PAGE == $page  ) {			
					
					$tab_active[self::SETTINGS_PAGE] = ' nav-tab-active';

				}	
			
			endif;			
		

			if ( isset( $_POST['wpglobus_translate_form'] ) ) {
				
				$opts = str_replace(array("\r"), '', $_POST['wpglobus_translate_options']);
				$opts = explode( "\n", $opts );
				
				$this->options['wpglobus_translate_options'] = $opts;
				update_option(self::TRANSLATE_OPTIONS_KEY, $this->options);		
			
			} 
			
			if ( isset( $_POST['wpglobus_settings_form'] ) ) {
				
				$opts = str_replace(array("\r"), '', $_POST['disabled_masks']);
				$opts = explode( "\n", $opts );
				
				$this->options['wpglobus_disabled_masks'] = $opts;
				update_option(self::TRANSLATE_OPTIONS_KEY, $this->options);		
			
			}	
						
			if ( !isset($this->options['wpglobus_disabled_masks']) ) {

				$filename = plugin_dir_path( __FILE__ ) . 'masks.txt';
				if ( file_exists($filename) ) {
					$data = file($filename);
					if ( false !== $data ) {
						$r = implode( ',', $data );
						$r = str_replace(array("\r", "\n"), '', $r);
						$this->disabled_masks = explode(',', $r);							
					}	
				}

			} else {
			
				$this->disabled_masks = $this->options['wpglobus_disabled_masks'];
			
			};
			
			?>
			
			<div class="wrap">

				<h2><?php
					/**
					 * @quirk
					 * This should be H2, so that it goes above the WP admin notices
					 */
					esc_html_e( 'WPGlobus Translate Options', '' );
					?></h2>


				<div class="wrap translate_options-wrap">
		
					<h2 class="nav-tab-wrapper">
						<a href="admin.php?page=<?php echo self::TRANSLATE_OPTIONS_PAGE; ?>" class="nav-tab<?php echo $tab_active[self::TRANSLATE_OPTIONS_PAGE]; ?>">
							<?php _e( 'All options' ); ?>
						</a><a href="admin.php?page=<?php echo self::SETTINGS_PAGE; ?>" class="nav-tab<?php echo $tab_active[self::SETTINGS_PAGE]; ?>">
							<?php _e( 'Settings' ); ?>
						</a><a href="admin.php?page=<?php echo self::ABOUT_PAGE; ?>" class="nav-tab">
							<?php _e( 'About', '' ); ?>
						</a>
					</h2>		

					<?php
		
					switch( $page ) :
					case self::TRANSLATE_OPTIONS_PAGE :	?>
						
						<form method="post" id="options"> <?php
							
							$search = false;
							if ( ! empty( $_POST['search'] ) ) {
								$search = $_POST['search'];
								$option = '[]';
							}	
							
							if ( $option ) {
							
								$show_source = false;
								if ( isset( $_GET['source'] ) && 'true' == $_GET['source'] ) {
									$show_source = true;
								}
								
								$option_names = array();
								if ( $search ) {
									
									$show_source = true;
									$results = $wpdb->get_results( 
										"SELECT option_name FROM $wpdb->options WHERE option_value LIKE '%$search%' AND option_name NOT LIKE '_%transient%' ORDER BY option_name ASC" );
									
									foreach( $results as $opt_obj ) {
										$option_names[] = $opt_obj->option_name;	
									}	
									
								} else {
									$option_names[] = $option;	
								}	
								
								if ( empty($option_names) ) : ?>
									<h4>Not found</h4> <?php
								else :
									foreach( $option_names as $option ) :
										?>
										
										<h3><a href="#" class="wpglobus-translate" title="Click to add to the translation list" data-source="<?php echo $option; ?>"><?php echo $option; ?><span></span></a></h3>
										<?php
										if ( $show_source ) { ?>
											<h4><a href="?page=<?php echo self::TRANSLATE_OPTIONS_PAGE . '&option=' . $option; ?>">back</a></h4>	<?php
											$data = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name='$option'");
										} else {  	?>
											<h4><a href="?page=<?php echo self::TRANSLATE_OPTIONS_PAGE . '&option=' . $option . '&source=true'; ?>">source</a></h4>	<?php
											$data = get_option( $option );
										}
										?>
										<table class="" style="width:100%">
										<tbody><tr>
											<td style="width:70%;">
											<?php
												if ( $show_source ) { 

													if ( $search ) {
														$data = preg_split( '/'.$search.'/ui', $data );
														$output = $data[0];
													} else {
														$output = $data;		
													}	
													if ( sizeof($data) == 1 ) { ?>
														<div class="textarea"><pre><?php echo htmlspecialchars($output); ?></pre></div>	<?php
													} else { ?>
														<div class="textarea"> <?php
															for( $i=0; $i < sizeof($data); $i++ ) { ?>
																<pre style="margin-bottom:0;"><?php echo htmlspecialchars($data[$i]); ?></pre> <?php
																if ( ! empty( $data[$i+1] ) ) { ?>
																	<span style="background-color:#0f0;"><?php echo $search; ?></span>
																	<pre style="margin-top:0;"><?php echo htmlspecialchars($data[$i+1]); ?></pre>	<?php
																}	
																$i++;
															} 	?>	
														</div>	<?php
													}
													
												} else {	
													if ( $data ) {
														if ( is_array($data) || is_object($data) ) {
															foreach ($data as $key=>$items) :
																echo $this->get_item($key, $items, $option);
															endforeach;
														} else {
															echo $this->get_item($option, $data, false);
														}	
													}
												}	
											?></td>
											<td style="vertical-align:top;width:30%;">
												<?php $this->get_float_block(); ?>
											</td>
										</tr>
										</tbody>
										</table>
										<?php	
									endforeach;
								endif;
							} else {
								
								$filename = plugin_dir_path( __FILE__ ) . 'options.txt';

								if ( file_exists($filename) ) {
								
									$data = file($filename);
									
									if ( false !== $data ) { ?>
										
										<div class="search">Find text in the Options table: <input id="search" size="40" name="search" value="" />
										<input type="submit" value="Search" /></div> <?php
										
										$r = implode( ',', $data );
										$r = str_replace(array("\r", "\n"), '', $r);
										
										$this->disabled_options = explode(',', $r);
										
										$options = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}options AS opt WHERE opt.option_name NOT LIKE '_%transient%' ORDER BY opt.option_name ASC" );
										?>
										<table class="" style="width:100%">
											<tbody><tr>
											<td style="width:70%;"> <?php									

												echo '<ul>';
												foreach( $options as $option ) {

													if ( ! in_array( $option->option_name, $this->disabled_options ) ) {
														
														if ( ! $this->check_masks($option->option_name) ) {
															echo '<li><a href="?page=' . self::TRANSLATE_OPTIONS_PAGE . '&option=' . $option->option_name . '">' . $option->option_name . '</a></li>';
														}
														
													}
													
												}
												echo '</ul>'; ?> 
											</td>
											<td style="vertical-align:top;width:30%;">
												<?php $this->get_float_block(); ?>
											</td>
											</tr>
											</tbody>
										</table> <?php										

									}
									
								}	
							
							} 	// endif $option; ?>
							
						</form>	<!-- #options --><?php
							
					break;
						
					case self::SETTINGS_PAGE :
					
						$masks = implode( "\n", $this->disabled_masks );	?>
						
						<div class="settings-page">
							<form method="post">
								<div>Disabled masks :</div>
								<textarea name="disabled_masks" id="disabled_masks" cols="80" rows="30"><?php echo $masks; ?></textarea>
								<input type="hidden" name="wpglobus_settings_form" value="" />
								<div>
									<input type="submit" value="Save" />
								</div>	
							</form>	
						</div>
						
						<?php
						
					break;
					
					case self::ABOUT_PAGE :
						/**
						 * @todo Store these texts in options (self-demo)
						 */
						?>
						<div class="about-page" style="max-width: 30em;">
							<?php if ( 'ru' === WPGlobus::Config()->language ) { ?>
								<!--@formatter:off-->
<p>
При разработке плагина WPGlobus учитывался фактор быстродействия, поэтому для разбора языковых меток были использованы всего 2 фильтра для опций 'blogdescription' и 'blogname' из таблицы 'wp_options'.</p>
<p>
При работе возникает необходимость использовать фильтры и для других опций. Плагин WPGlobus Translate Options позволяет добавить те опции, которые нужно выводить, разобрав текст по языковым меткам.</p>
<p>
Для примера можно взять тему <a href="https://wordpress.org/themes/ample/" target="_blank">Ample</a> из репозитория. Она имеет встроенный слайдер, в настройках которого можно добавить текст для наложения на слайды. Если затем указать опцию 'ample' в разделе Options to translate, то текст будет выведен на слайдах согласно согласно выбранному языку.
</p>
<!--@formatter:on-->
						<?php } else { ?>
								<!--@formatter:off-->
<p>In the WPGlobus core plugin, we are keeping the amount of filters as low as possible, to minimize the potential performance hit. Therefore, only two WordPress options, 'blogdescription' and 'blogname' are supported by default.</p>
<p>Sometimes, it is necessary to allow multiple languages in other options. For instance, the slider used in the <a href="https://wordpress.org/themes/ample/" target="_blank">Ample</a> theme stores the textual overlays in the options table. With the WPGlobus Translate Options plugin, all you need is to add 'ample' into the Options to translate, and all the slider texts will be multilingual!
</p>
<!--@formatter:on-->
						<?php } ?>
						</div>
						<?php
					break;
					endswitch;	
					?>
				</div>
				
			</div>			<?php
		
		}

		/**
		 * Output float block
		 *
		 * @since 1.0.0
		 * @return void
		 */		
		function get_float_block() {	

			$options = '';
			if ( !empty($this->options['wpglobus_translate_options']) ) {
				$options = implode( "\n", $this->options['wpglobus_translate_options'] );
			}
			?>
		
			<div class="float-block">
				Options to translate:<br />
				<textarea cols="40" rows="20" name="wpglobus_translate_options" id="wpglobus_translate_options"><?php echo $options; ?></textarea>
				<br />
				<input type="hidden" name="wpglobus_translate_form" value="" />
				<input type="submit" value="Save" />
			</div>		<?php
			
		}

		/**
		 * Get item
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
		 * Convert string
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
			//return '<div style="vertical-align:top;"><a href="#" class="wpglobus-translate" title="Click to add translation list" data-source="' . $str . '" onclick="return false;">' . $r . '</a></div>' . '';
			return '<div style="vertical-align:top;">' . $r . '</div>';
		}
		
		
		/**
		 * Enqueue admin styles
		 *
		 * @since 1.0.0
		 * @return void
		 */		
		function on_admin_styles() {
			
			/** @global string $pagenow */
			global $pagenow;

			if ( $pagenow == 'admin.php' 
					&& isset($_GET['page']) 
					&& in_array( $_GET['page'], array(self::TRANSLATE_OPTIONS_PAGE, self::SETTINGS_PAGE) ) ) :			
			
				wp_register_style(
					'wpglobus-translate-options',
					plugin_dir_url( __FILE__ ) . 'wpglobus-translate-options' . self::$_SCRIPT_SUFFIX . ".css",
					array(),
					WPGLOBUS_TRANSLATE_OPTIONS_VERSION,
					'all'
				);
				wp_enqueue_style( 'wpglobus-translate-options' );	
			
			endif;
			
		}	
	
		/**
		 * Enqueue admin scripts
		 *
		 * @since 1.0.0
		 * @return void
		 */			
		function on_admin_scripts() {
			
			/** @global string $pagenow */
			global $pagenow;

			if ( $pagenow == 'admin.php' && isset($_GET['page']) && self::TRANSLATE_OPTIONS_PAGE == $_GET['page']  ) :			
			
				wp_register_script(
					'wpglobus-translate-options',
					plugin_dir_url( __FILE__ ) . 'wpglobus-translate-options' . self::$_SCRIPT_SUFFIX . ".js",
					array( 'jquery' ),
					WPGLOBUS_TRANSLATE_OPTIONS_VERSION,
					true
				);
				wp_enqueue_script( 'wpglobus-translate-options' );	
			
			endif;			
			
		}	
		
	}

endif; // class_exists
