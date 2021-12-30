<?php
/**
 * File: class-wpglobus-to-rest-controller.php
 *
 * REST API: WP_REST_WPGlobus_TO_Controller class.
 *
 * @package WPGlobus Translate Options
 * @subpackage REST_API
 * @since 2.0.0
 */

/**
 * Core class to access to options via the REST API.
 *
 * @since 2.0.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_WPGlobus_TO_Controller extends WP_REST_Controller {

	/** @var WP_User $current_user */
	protected $current_user;
	
	/**
	 * Incoming arguments.
	 */
	protected $args = null;

	/**
	 * Endpoints.
	 *
	 * @var array
	 */	
	protected $endpoints = array();

	/**
	 * Rest base.
	 *
	 * @var string
	 */		
	protected $rest_base = null;
	
	/**
	 * Plugin options.
	 */
	protected $options = null;
	
	/**
	 * Controller constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct( $args ) {
		
		$this->args = $args;

		$this->namespace = self::get_arg('rest_namespace');
		$this->rest_base = self::get_arg('rest_base');
		$this->endpoints = self::get_arg('endpoints');
		$this->options 	 = self::get_options();
	}

	/**
	 * Registers the routes for the controller.
	 *
	 * @since 2.0.0
	 */
	public function register_routes() {
		
		$this->current_user = wp_get_current_user();
		
		/**
		 * wp-json/wpglobus-to/v1/options/about
		 */
		$ep = $this->get_endpoint('getAboutInfo'); 
		
		if ( $ep && ! empty($ep['route']) && is_callable($ep['readable_callback'], true, $callback) ) {
			register_rest_route(
				$this->namespace,
				$this->rest_base . '/' . $ep['route'],
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, $callback ), // 'get_about_info'
						'permission_callback' => array( $this, 'get_permissions_check' ),
						'args'                => $this->get_collection_params(),
					)
				)
			);
		}

		/**
		 * wp-json/wpglobus-to/v1/options/themeOptions
		 */
		$ep = $this->get_endpoint('themeOptions'); 

		if ( $ep && ! empty($ep['route']) ) {
			register_rest_route(
				$this->namespace,
				$this->rest_base . '/' . $ep['route'],
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_theme_options' ), 
						'permission_callback' => array( $this, 'get_permissions_check' ),
						// 'args'                => $this->get_collection_params(),
					)
				)
			);
		}

		/**
		 * wp-json/wpglobus-to/v1/options
		 */
		$ep = $this->get_endpoint('allOptions'); 

		if ( $ep && 
			 empty($ep['route']) && 
			 is_callable($ep['readable_callback'], true, $callback) 
		) {		
			register_rest_route(
				$this->namespace,
				$this->rest_base,
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, $callback ),	// 'get_items'
						'permission_callback' => array( $this, 'get_permissions_check' ),
						'args'                => $this->get_collection_params(),
					),
					#array(
					#	'methods'             => WP_REST_Server::CREATABLE,
					#	'callback'            => array( $this, 'create_item' ),
					#	'permission_callback' => array( $this, 'create_item_permissions_check' ),
					#	'args'                => $this->get_endpoint_args_for_item_schema(),
					#),
					#'allow_batch' => array( 'v1' => true ),
					#'schema'      => array( $this, 'get_public_item_schema' ),
				)
			);
		}
	
		
		/**
		 * wp-json/wpglobus-to/v1/options/masks
		 */
		$ep = $this->get_endpoint('getMasks'); 		 

		if ( $ep && 
			 ! empty($ep['route']) && 
			 is_callable($ep['readable_callback'], true, $callback) && 
			 is_callable($ep['editable_callback'], true, $editable_callback)
		) {
			register_rest_route(
				$this->namespace,
				$this->rest_base . '/' . $ep['route'],
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, $callback ), // 'get_mask_items'
						'permission_callback' => array( $this, 'get_permissions_check' ),
						'args'                => $this->get_collection_params(),
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, $editable_callback ), // 'update_masks'
						'permission_callback' => array( $this, 'update_item_permissions_check' ),
						#'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					),
				)
			);
		}
		
		/**
		 * wp-json/wpglobus-to/v1/options/raw
		 */		
		$ep = $this->get_endpoint('getRaw');
		
		if ( $ep && 
			 ! empty($ep['route']) && 
			 is_callable($ep['readable_callback'], true, $callback)
		) {		
			register_rest_route(
				$this->namespace,
				$this->rest_base . '/' . $ep['route'],
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, $callback ), // 'get_raw'
						'permission_callback' => array( $this, 'get_permissions_check' ),
						'args'                => $this->get_collection_params(),
					)
				)
			);
		}
		
		/**
		 * wp-json/wpglobus-to/v1/options/itemsperpage
		 */
		$ep = $this->get_endpoint('updateItemsPerPage'); 

		if ( $ep && 
			 ! empty($ep['route']) && 
			 is_callable($ep['readable_callback'], true, $callback) && 
			 is_callable($ep['editable_callback'], true, $editable_callback)
		) {			
			register_rest_route(
				$this->namespace,
				$this->rest_base . '/' . $ep['route'],
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, $callback ), //  'get_option_items_per_page'
						'permission_callback' => array( $this, 'get_permissions_check' ),
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, $editable_callback ), // 'update_option_items_per_page'
						'permission_callback' => array( $this, 'update_item_permissions_check' ),
						#'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					),				
					#'schema' => array( $this, 'get_public_item_schema' ),
				)
			);
		}	

		/**
		 * wp-json/wpglobus-to/v1/options/switchInterface
		 */
		$ep = $this->get_endpoint('switchInterface'); 

		if ( $ep && 
			 ! empty($ep['route']) && 
			 is_callable($ep['readable_callback'], true, $callback) && 
			 is_callable($ep['editable_callback'], true, $editable_callback)
		) {			
			register_rest_route(
				$this->namespace,
				$this->rest_base . '/' . $ep['route'],
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, $callback ), //  'get_option_interface_version'
						'permission_callback' => array( $this, 'get_permissions_check' ),
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, $editable_callback ), // 'update_option_interface_version'
						'permission_callback' => array( $this, 'update_item_permissions_check' ),
						#'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					),				
					#'schema' => array( $this, 'get_public_item_schema' ),
				)
			);
		}	

		/**
		 * wp-json/wpglobus-to/v1/options/translateit
		 */
		$ep = $this->get_endpoint('translateIt'); 

		if ( $ep && 
			 ! empty($ep['route']) && 
			 is_callable($ep['readable_callback'], true, $callback)
		) {			
			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/(?P<'.$ep['route'].'>[\w-]+)',
				array(
					'args'   => array(
						$ep['route'] => array(
							'description' => __( "Options to translate." ),
							'type'        => 'string',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, $callback ), //  'get_items_to_translate'
						'permission_callback' => array( $this, 'get_permissions_check' ),
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_item' ),
						'permission_callback' => array( $this, 'update_item_permissions_check' ),
						#'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					),				
					#'schema' => array( $this, 'get_public_item_schema' ),
				)
			);
		}

		/**
		 * wp-json/wpglobus-to/v1/options//\S+/
		 * @since 2.1.0 @W.I.P
		 */
		/* 
		$ep = $this->get_endpoint('getOption'); 

		if ( $ep && 
			 $this->is_regexp($ep['route']) && 
			 is_callable($ep['readable_callback'], true, $callback) 
		) {		
		
			register_rest_route(
				$this->namespace,
				$this->rest_base . '/' . $ep['route'],
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_option_value' ),	// 'get_option_value'
						'permission_callback' => array( $this, 'get_permissions_check' ),
						'args'                => $this->get_collection_params(),
					),
					#array(
					#	'methods'             => WP_REST_Server::CREATABLE,
					#	'callback'            => array( $this, 'create_item' ),
					#	'permission_callback' => array( $this, 'create_item_permissions_check' ),
					#	'args'                => $this->get_endpoint_args_for_item_schema(),
					#),
					#'allow_batch' => array( 'v1' => true ),
					#'schema'      => array( $this, 'get_public_item_schema' ),
				)
			);
		}		
		// */
		
		$file = $this->get_mask_file(true);

		$masks = array();
		
		/**
		 * @since 2.0.0. @W.I.P
		 */
		/* 
		if ( file_exists($file) ) {
			
			$_masks = json_decode( file_get_contents( $file ), true );
			
			if ( is_null($_masks) || ! $_masks ) {
				// * json cannot be decoded or if the encoded data is deeper than the nesting limit.
			} else {
				$masks = $_masks;
				unset( $_masks );
			}
		}
		// */		
	}

	/**
	 * @since 2.1.0 @W.I.P
	 */
	/* 
	public function is_regexp( $regexp = '' ) {
		
		if ( empty($regexp) || strlen($regexp) < 3 ) {
			return false;
		}
		
		if( $regexp[0] == '/' && $regexp[ strlen($regexp)-1 ] == '/' ) {
			return true;
		}
		
		return false;
	}
	// */
	
	/**
	 * Get option `interface_version`.	 
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */	
	public function get_option_interface_version( $request ) {
		$data = $this->get_response_schema();
		$data['response'] = 'ok';				
		$data['message'] = 'Reloading ...';				
		return rest_ensure_response( $data );		
	}

	/**
	 * Update option `interface_version`.	 
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */	
	public function update_option_interface_version( $request ) {
		
		$data = $this->get_response_schema();
		$body = json_decode( $request->get_body() );
		
		if ( 
			$this->update_option(
				$this->get_arg( $body->optionKey, 'option_keys' ),
				$body->value
			)
		) {
			$data['response'] = 'ok';				
			$data['message'] = 'Option updated. Reloading ...';				
		} else {
			$data['response'] = 'error';
			$data['message'] = 'Option is not updated.';
		}		

		return rest_ensure_response( $data );				
	}
	
	/**
	 * Get option `items_per_page`.	 
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function get_option_items_per_page( $request ) {
		$data = $this->get_response_schema();
		return rest_ensure_response( $data );	
	}
	
	/**
	 * Update option `items_per_page`.	 
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function update_option_items_per_page( $request ) {
		
		$data = $this->get_response_schema();

		$body = json_decode( $request->get_body() );
	
		if ( 
			$this->update_option(
				$body->itemsPerPageOption,
				$body->itemsPerPage
			)
		) {
			$data['response'] = 'ok';				
			$data['message'] = $body->itemsPerPageOption . ' updated.';				
		} else {
			$data['response'] = 'error';
		}
		
		return rest_ensure_response( $data );		
	}
	
	/**
	 * Update mask list.
	 *
	 * @since 2.0.0 @W.I.P
	 */
	public function update_masks( $request ) {

		$data = $this->get_response_schema();
		
		$body = json_decode( $request->get_body() );

		if ( ! in_array( $body->action, array( 'updateMasks', 'addNewMask' ) ) ) {
			$data['response'] = 'error';
			$data['message'] = 'Incorrect action.';
			$response = rest_ensure_response( $data );
			return $response;			
		}

		$new_masks = $body->masks;
			
		if ( empty($new_masks) ) {
			// @todo
		} else {
			
			if ( 'addNewMask' == $body->action ) {
				
				if ( is_string($new_masks) ) {
					$new_masks = array( $new_masks );
				}
				
				$new_masks = array_merge(
					$this->options[ $this->get_arg('disabled_masks_key', 'option_keys') ],
					$new_masks
				);

			} else {
				
				// @W.I.P
				/**
				 * updateMasks.
				 */
				/**
				 * It is array off all masks.
				 */
				foreach( $new_masks as $_key=>$_mask ) {
					$_mask = trim($_mask);
					if ( empty($_mask) ) {
						unset( $new_masks[$_key] );
					}
				}
			}

			if ( 
				$this->update_option(
					$this->get_arg('disabled_masks_key', 'option_keys'),
					$new_masks
				)
			) {
				$data['response'] = 'ok';				
				$data['message'] = 'Masks updated.';				
			} else {
				$data['response'] = 'error';
			}
		}

		return rest_ensure_response( $data );	
	}

	/**
	 * Get mask item list.	 
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function get_mask_items( $request ) {
		return rest_ensure_response( $this->get_masks($request) );			
	}

	/**
	 * Get active theme options or both child and parent theme options.
	 *
	 * @since 2.1.0
	 */
	protected function get_active_theme_options() {
		
		static $options = null;
		if ( ! is_null($options) ) {
			return $options;
		}
		
		global $wpdb;
		
		$themes = $this->args['themes'];

		$_option_names = array();
		foreach( $themes as $theme=>$param ) {
			if ( ! empty( $param['themeModsOption'] ) ) {
				$_option_names[$theme] = '"'.$param['themeModsOption'].'"';
			}
		}
		
		$_options = false;
		
		if ( ! empty( $_option_names ) ) {
			
			$option_names = implode( ',', $_option_names );

			$_options = $wpdb->get_results( 
				"SELECT * FROM {$wpdb->prefix}options AS opt WHERE opt.option_name IN ( $option_names )"
			);		
		}

		foreach( $_option_names as $_theme => $_option_name ) {
			foreach( $_options as $_key=>$_option ) {
				if ( '"' . $_option->option_name . '"' == $_option_name ) {
					$_options[$_key]->theme = $_theme;
					break;
				}
			}
		}

		$options = $_options;
		unset($_options);
		
		return $options;	
	}
	
	/**
	 * Get theme options.	 
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */	
	public function get_theme_options( $request ) {
		
		$response = $this->get_response_schema();
		
		$response['themes'] = $this->args['themes'];
		
		$theme_options = $this->get_active_theme_options();
		
		if ( ! $theme_options ) {
			
			$response['response'] = 'error';
			$response['message'] = 'No theme options found.';
		
		} else {

			$response['response'] = 'ok';
			$response['message'] = '';
			foreach( $theme_options as $key=>$option ) {
				$response['data'][$key] = $this->prepare_item_for_response( $option, $request );
			}
		}

		return rest_ensure_response( $response );		
	}
	
	/**
	 * Get about info page.
	 */
	public function get_about_info( $request ) {
		
		$data = $this->get_response_schema();
		
		$data['title'] = 'About WPGlobus Translate Options plugin';
		
		$file = plugin_dir_path( $this->get_arg('plugin_file') ) . 'includes/templates/about.php';
		
		if ( file_exists( $file) ) {
			ob_start();
			require_once($file);
			$data['content'] = ob_get_contents();
			ob_end_clean();
		}
		
		return rest_ensure_response( $data );		
	}
	
	/**
	 * Updates one item from the collection.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {

		$body = json_decode( $request->get_body() );
		
		if ( ! isset($body->name) || ! isset($body->action) ) {
			return new WP_Error( 'rest_incorrect_body', 'No option name or action found.', array( 'status' => 404 ) );
		}
		
		$save_result = false;
		
		if ( 'unset' == $body->action ) {
			
			foreach( $this->get_option( $this->get_arg('to_translate_key', 'option_keys') ) as $_key=>$_option ) :
				if ( $_option == $body->name ) {
					unset( $this->options[$this->get_arg('to_translate_key', 'option_keys')][$_key] );
					$save_result = true;
					// break; // Don't break to remove duplicate values.
				}
			endforeach;
		} else if ( 'set' == $body->action ) {
			$this->options[ $this->get_arg('to_translate_key', 'option_keys') ][] = $body->name;
			$save_result = true;
		}

		$data = array();
		
		if ( $save_result ) {
			
			$data['response'] = 'error';
			if ( 
				$this->update_option( 
					$this->get_arg('plugin_options_key', 'option_keys'), 
					$this->options[ $this->get_arg('to_translate_key', 'option_keys') ]
				) 
			) {
				$data['response'] = 'ok';
			}

		}
		
		return rest_ensure_response( $data );		
	}
	
	/**
	 * Retrieves a collection of options to translate.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */	
	public function get_items_to_translate( $request ) {
		
		global $wpdb;

		$data = $this->get_response_schema();
		
		$_opts = self::get_option( $this->get_arg('to_translate_key', 'option_keys') );
		
		if ( empty( $_opts ) ) {
			
			$data['response'] = 'error';
			$data['message'] = 'No options to translate found.';

		} else {
			
			$_option_names = array();
			foreach( $_opts as $_option_name ) {
				$_option_names[] = '"'.$_option_name.'"';
			}

			$option_names = implode( ',', $_option_names );
			
			$_options = $wpdb->get_results( 
				"SELECT * FROM {$wpdb->prefix}options AS opt WHERE opt.option_name IN ( $option_names )"
			);

			if ( empty($_options) ) {
				
				$data['response'] = 'error';
				$data['message'] = 'No options in DB to translate found.';
			
			} else {

				$data['response'] = 'ok';
				foreach( $_options as $key=>$option ) {
					$data['data'][$key] = $this->prepare_item_for_response( $option, $request );
				}
			}
		}

		return rest_ensure_response( $data );		
	}
	
	/**
	 * @since 2.1.0 @W.I.P
	 */
	public function get_option_value( $request ) {
		$data = $this->get_response_schema();
		$data['message']  = 'Test message';
		
		// $body = json_decode( $request->get_body() );
		// error_log( print_r( $body, true ) );
		
		return rest_ensure_response( $data );		
	}
	
	/**
	 * Retrieves a collection of options.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
	
		global $wpdb;
		
		$data = $this->get_response_schema();
		
		$disabled_options = $this->fetch_options();
		
		// $query = "SELECT * FROM {$wpdb->prefix}options AS opt WHERE opt.option_name NOT LIKE '_%transient%' ORDER BY opt.option_name ASC";
		$query = "SELECT * FROM {$wpdb->prefix}options AS opt WHERE 1=1 ORDER BY opt.option_name ASC";

		$options = $wpdb->get_results( $query );

		if ( empty( $options) ) {
			
			$data['response'] = 'error';
			$data['message']  = 'No options in DB found.';
		
		} else {

			$masks = $this->get_masks();
			$data['response'] = 'ok';

			$i = 0;
			
			foreach( $options as $key=>$option ) {

				$_add_item = true;

				if ( array_key_exists( $option->option_name, $disabled_options ) ) { 
					$_add_item = false;
				}

				if ( $_add_item ) {			
					foreach( $masks as $mask ) {
						if ( 0 === strpos( $option->option_name, $mask ) ) {
							$_add_item = false;
							break;	
						}
					}
				}
				
				if ( $_add_item ) {
					$data['data'][$i] = $this->prepare_item_for_response( $option, $request );
					$i++;
				}
			}
		
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Prepares a row data option for response.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request 		Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function get_raw( $request ) {
		
		$data = $this->get_response_schema();
		$data['message'] = 'Get raw data.';

		$data['options'] = array();

		$_opt = $this->get_arg('to_translate_key', 'option_keys');
		$_opts = self::get_option( $_opt );
		$data['options'][$_opt] = array();
		
		if ( ! empty( $_opts ) && is_array( $_opts ) ) {
			foreach( $_opts as $_key=>$_value ) {
				$data['options'][$_opt][] = $_key.': '.$_value;
			}
		}

		$_opt = $this->get_arg('disabled_masks_key', 'option_keys');
		$_opts = self::get_option( $_opt );
		$data['options'][$_opt] = array();

		if ( ! empty( $_opts ) && is_array( $_opts ) ) {
			foreach( $_opts as $_key=>$_value ) {
				$data['options'][$_opt][] = $_key.': '.$_value;
			}
		}
		
		return rest_ensure_response( $data );
	}
	
	/**
	 * Prepares a single option output for response.
	 *
	 * @since 2.0.0
	 *
	 * @param stdClass Object $option  Option object.
	 * @param WP_REST_Request $request Request object.

	 * @return array.
	 */
	public function prepare_item_for_response( $option, $request ) {
		
		$item = array();
		
		$item['option_id']   = $option->option_id;
		$item['option_name'] = $option->option_name;
		// $item['option_value'] = substr( $option->option_value, 0, 30 ); @W.I.P @since 2.0.0
		$item['hasMLString'] = WPGlobus_Core::has_translations($option->option_value);
		
		if ( empty( $this->options[ $this->get_arg('to_translate_key', 'option_keys') ] ) ) {
			
			$item[ 'translateIt' ] = false;
		
		} else {
			
			$item[ 'translateIt' ] = in_array(
				$option->option_name, 
				$this->options[ $this->get_arg('to_translate_key', 'option_keys') ] 
			) ? true : false;
		
		}

		$theme_options = $this->get_active_theme_options();
		
		if ( count($theme_options) == 2 ) {
			
			/**
			 * We have parent and child themes.
			 */
			$item['themeStatus'] = false;
			foreach( $theme_options as $theme_option ) {
				if ( $option->option_name == $theme_option->option_name ) {
					$item['themeStatus'] = $theme_option->theme;
				}
			}	
	
		} else {
			
			/**
			 * We have 1 active theme.
			 */
			$item['themeStatus'] = false;
			foreach( $theme_options as $theme_option ) {
				if ( $option->option_name == $theme_option->option_name ) {
					$item['themeStatus'] = 'active';
				}
			}				 
			
		}

		return $item;
	}
	
	/**
	 * Checks if a given request has access to read options.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_permissions_check( $request ) {
		
		if ( user_can( $this->current_user, 'manage_options' ) ) {
			return true;
		}		
		
		return new WP_Error(
			'rest_forbidden',
			__( 'Sorry, you are not allowed to read options.' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Checks if a given request has access to update a specific item.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has access to update the item, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		
		if ( user_can( $this->current_user, 'manage_options' ) ) {
			return true;
		}		
		
		return new WP_Error(
			'rest_forbidden',
			__( 'Sorry, you are not allowed to read options.' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}	

	/**
	 * Get masks.
	 *
	 * @since 2.0.0	 
	 */	
	protected function get_masks($request = false) {
		
		$masks = array();
		$message = '';
		
		// @W.I.P
		$_opts = array();
		// if ( isset( $this->options[ $this->get_arg('disabled_masks_key', 'option_keys') ] ) ) {
			// $_opts = $this->options[ $this->get_arg('disabled_masks_key', 'option_keys') ];
		// }
		
		if ( empty( $_opts ) ) {

			// Fetch masks from file.		
			$masks = $this->fetch_masks();
			$message = 'Mask items were fetched.';
			
			$this->update_option( 
				$this->get_arg('disabled_masks_key', 'option_keys'), 
				$masks
			);
				
		} else {
			$masks = $_opts;
			$message = 'Mask items get from options.';
		}
		
		if ( $request instanceof WP_REST_Request ) {
			
			if ( ! empty($masks) && is_array($masks) ) {
				
				$not_editable_masks = $this->get_not_editable_masks();
			
				$masks_array['response'] = 'ok';
				$masks_array['message']  = $message;
				$i = 0;
				foreach( $masks as $_mask ) :
					if ( ! empty( $_mask ) ) {
						$masks_array['data'][$i]['id']   = $i;
						$masks_array['data'][$i]['mask'] = $_mask;
						$masks_array['data'][$i]['editable'] = in_array( $_mask, $not_editable_masks ) ? false : true;
						$masks_array['data'][$i]['hidden'] = false;
						$i++;
					}
				endforeach;
				
				$masks = $masks_array;
				unset( $masks_array );
				
			} else {
				
				unset( $this->options[ $this->get_arg('disabled_masks_key', 'option_keys') ] );
				return $this->get_masks($request);
			
			}
		}		
		
		return $masks;
	}
	
	/**
	 * Fetch option list from file.
	 *
	 * @since 2.0.0	 
	 */		
	protected function fetch_options() {
		
		$file = $this->get_options_file();

		$options = array();
		
		if ( file_exists($file) ) {
			$_options = json_decode( file_get_contents( $file ), true );
			if ( is_null($_options) || ! $_options ) {
				/**
				 * json cannot be decoded or if the encoded data is deeper than the nesting limit.
				 */
			} else {
				$options = $_options;
				unset( $_options );
			}
		}
		
		return $options;		
	}
	
	/**
	 * Fetch mask list from file.
	 *
	 * @since 2.0.0	 
	 */	
	protected function fetch_masks( $test = true ) {
		
		$file = $this->get_mask_file();

		$masks = array();
		
		if ( file_exists($file) ) {
			ob_start();
			require_once($file);
			$_masks = ob_get_contents();
			ob_end_clean();
			$masks = explode( "\n", $_masks );
		} 
		
		/**
		 * @W.I.P
		 */
		/* 
		if ( file_exists($file) ) {
			
			$_masks1 = json_decode( file_get_contents( $file ), true );
			
			if ( is_null($_masks1) || ! $_masks1 ) {
				// * json cannot be decoded or if the encoded data is deeper than the nesting limit.
			} else {
				$masks1 = $_masks1;
				unset( $_masks1 );
			}
		}
		// */
		
		return $masks;
	}

	/**
	 * Get response schema.
	 *
	 * @since 2.0.0	 
	 */	
	protected function get_response_schema() {
		return array(
			'response' => 'ok',
			'message' => 'Response schema by default.',
			'data' => array()
		);
	}

	/**
	 * Get file with mask list.
	 *
	 * @since 2.0.0	 
	 */
	protected function get_options_file() {
		// return plugin_dir_path( $this->get_arg('plugin_file') ) . 'includes/options.txt';
		return plugin_dir_path( $this->get_arg('plugin_file') ) . 'includes/options.json';
	} 
	
	/**
	 * Get file with mask list.
	 * 
	 * @since 2.0.0 @W.I.P
	 */
	protected function get_mask_file( $get_json = false ) {
		if ( $get_json ) {
			return plugin_dir_path( $this->get_arg('plugin_file') ) . 'includes/masks.json';
		}
		return plugin_dir_path( $this->get_arg('plugin_file') ) . 'includes/masks.txt';
	}

	/**
	 * Get not editable mask list.
	 *
	 * @since 2.0.0	 
	 */	
	protected function get_not_editable_masks() {
		return array(
			'blogdescription',
			'blogname',
			'_transient_',
			'_site_transient',
			'_aioseo_',
		);
	}
	
	/**
	 * Update plugin options.
	 *
	 * @since 2.0.0	 
	 */
	protected function update_option( $option_key = '', $option_value = '' ) {

		if ( empty( $option_key ) ) {
			return false;
		}

		$this->options[ $option_key ] = $option_value;
		
		$result = update_option( 
			$this->get_arg('plugin_options_key', 'option_keys'), 
			$this->options, 
			false 
		);
		
		return $result;
	}

	/**
	 * Get option from options array.
	 *
	 * @since 2.0.0	 
	 */
	protected function get_option( $option_key = '' ) {
		
		if ( empty( $option_key ) ) {
			return null;
		}
		
		if ( ! isset( $this->options[$option_key] ) ) {
			return null;
		}
		
		return $this->options[$option_key];
	}

	/**
	 * Get plugin options.
	 *
	 * @since 2.0.0	 
	 */	 
	protected function get_options() {
		
		if ( ! is_null( $this->options ) ) {
			return $this->options;
		}
		
		$options = get_option( $this->get_arg('plugin_options_key', 'option_keys') );

		if ( empty($options) || ! is_array($options) ) {
			$options = array();
		}
		
		$this->options = $options;
		
		unset( $options );
		
		return $this->options;
	}
	
	/**
	 * Get incoming argument.
	 *
	 * @since 2.0.0	 
	 */		 
	protected function get_arg( $arg = '', $key = false  ) {
	
		if ( empty($arg) ) {
			return $this->args;
		}
	
		if ( $key ) {

			if ( isset($this->args[$key]) && isset($this->args[$key][$arg]) ) {
				return $this->args[$key][$arg];
			}
			
		} else {
			
			if ( isset($this->args[$arg]) ) {
				return $this->args[$arg];
			}
		}
		
		return null;
	}
	
	/**
	 * Get endpoint.
	 */
	protected function get_endpoint( $endpoint = '' ) {
		
		if ( empty($endpoint) ) {
			return $this->endpoints;
		}
		
		if ( ! isset( $this->endpoints[$endpoint] ) ) {
			return false;
		}
		
		return $this->endpoints[$endpoint];
	}
}

# --- EOF