<?php

/**
 * Class Hustle_Get_Response_Form_Hooks
 * Define the form hooks that are used by Get_Response
 *
 * @since 4.0
 */
class Hustle_Get_Response_Form_Hooks extends Hustle_Provider_Form_Hooks_Abstract {


	/**
	 * Add Get_Response data to entry.
	 *
	 * @since 4.0
	 *
	 * @param array $submitted_data
	 * @return array
	 */
	public function add_entry_fields( $submitted_data ) {

		$addon = $this->addon;
		$module_id = $this->module_id;
		$form_settings_instance = $this->form_settings_instance;

		/**
		 * Filter submitted form data to be processed
		 *
		 * @since 4.0
		 *
		 * @param array                                    $submitted_data
		 * @param int                                      $module_id                current module_id
		 * @param Hustle_Get_Response_Form_Settings 	   $form_settings_instance
		 */
		$submitted_data = apply_filters( 
			'hustle_provider_get_response_form_submitted_data', 
			$submitted_data, 
			$module_id, 
			$form_settings_instance 
		);
		$addon_setting_values = $form_settings_instance->get_form_settings_values();

		try {
			$global_multi_id = $addon_setting_values['selected_global_multi_id'];
			$api_key = $addon->get_setting( 'api_key', '', $global_multi_id );
			$api = $addon::api( $api_key );

			if ( empty( $submitted_data['email'] ) ) {
				throw new Exception( __( 'Required Field "email" was not filled by the user.', 'wordpress-popup' ) );
			}

			$list_id = $addon_setting_values['list_id'];

			$submitted_data = $this->check_legacy( $submitted_data );

			$email = $submitted_data['email'];

			$name = array();
			if ( ! empty( $submitted_data['first_name'] ) ) {
				$name['first_name'] = $submitted_data['first_name'];
			}
			if ( ! empty( $submitted_data['last_name'] ) ) {
				$name['last_name'] = $submitted_data['last_name'];
			}

			$new_data = array(
				'email'         => $email,
				'dayOfCycle'    => apply_filters( 'hustle_optin_get_response_cycle', '0' ),
				'campaign'      => array(
					'campaignId' => $list_id,
				),
				'ipAddress'     => Opt_In_Geo::get_user_ip(),
			);

			if ( count( $name ) ) {
				$new_data['name'] = implode( ' ', $name ); }

			// Extra fields
			$extra_data = array_diff_key( $submitted_data, array(
				'email' => '',
				'first_name' => '',
				'last_name' => '',
			) );
			$extra_data = array_filter( $extra_data );
			$is_sent = false;
			$member_status = __( 'Member could not be subscribed.', 'wordpress-popup' );

			if ( ! empty( $extra_data ) ) {
				$new_data['customFieldValues'] = array();

				$cf = $api->get_custom_fields();
				if ( is_wp_error( $cf ) ) {
					throw new Exception( $cf->get_error_message() );
				}

				$custom_fields = wp_list_pluck( $cf, 'name', 'customFieldId' );

				foreach ( $extra_data as $key => $value ) {
					$key = str_replace( array( '-', '_' ), '', $key );

					if ( in_array( $key, $custom_fields, true ) ) {
						$custom_field_id = array_search( $key, $custom_fields, true );
					} else {
						$custom_field = array(
							'name' => $key,
							'type' => 'text', // We only support text for now
							'hidden' => false,
							'values' => array(),
						);
						$custom_field_id = $api->add_custom_field( $custom_field );
						if ( is_wp_error( $custom_field_id ) ) {
							throw new Exception( $custom_field_id->get_error_message() );
						}
					}
					$new_data['customFieldValues'][] = array(
						'customFieldId' => $custom_field_id,
							'value' => array( $value ),
					);
				}
			}

			/**
			 * Fires before adding subscriber
			 *
			 * @since 4.0.2
			 *
			 * @param int    $module_id
			 * @param array  $submitted_data
			 * @param object $form_settings_instance 
			 */
			do_action( 'hustle_provider_get_response_before_add_subscriber', 
				$module_id, 
				$submitted_data, 
				$form_settings_instance 
			);

			$res = $api->subscribe( $new_data );

			/**
			 * Fires after adding subscriber
			 *
			 * @since 4.0.2
			 *
			 * @param int    $module_id
			 * @param array  $submitted_data
			 * @param mixed  $res
			 * @param object $form_settings_instance 
			 */
			do_action( 'hustle_provider_get_response_after_add_subscriber', 
				$module_id, 
				$submitted_data, 
				$res,
				$form_settings_instance 
			);


			if ( is_wp_error( $res ) ) {
				$error_code = $res->get_error_code();
				$error_message = $res->get_error_message( $error_code );

				if ( preg_match( '%Conflict%', $error_message ) ) {
					$details = __( 'This email address has already subscribed.', 'wordpress-popup' );
				} else {
					$details = $res->get_error_message();
				}
			} else {
				$is_sent = true;
				$details = __( 'Successfully added or updated member on Get_Response list', 'wordpress-popup' );
				$member_status = __( 'OK', 'wordpress-popup' );
			}

			$entry_fields = array(
				array(
					'name'  => 'status',
					'value' => array(
						'is_sent'       => $is_sent,
						'description'   => $details,
						'member_status' => $member_status,
					),
				),
			);
		} catch ( Exception $e ) {
			$entry_fields = $this->exception( $e );
		}

		if ( !empty( $addon_setting_values['list_name'] ) ) {
			$entry_fields[0]['value']['list_name'] = $addon_setting_values['list_name'];
		}

		$entry_fields = apply_filters( 'hustle_provider_' . $addon->get_slug() . '_entry_fields',
			$entry_fields,
			$module_id,
			$submitted_data,
			$form_settings_instance
		);

		return $entry_fields;
	}

	/**
	 * Check whether the email is already subscribed.
	 *
	 * @since 4.0
	 *
	 * @param $submitted_data
	 * @return bool
	 */
	public function on_form_submit( $submitted_data, $allow_subscribed = true ) {

		$is_success 				= true;
		$module_id                	= $this->module_id;
		$form_settings_instance 	= $this->form_settings_instance;
		$addon 						= $this->addon;
		$addon_setting_values 		= $form_settings_instance->get_form_settings_values();

		if ( empty( $submitted_data['email'] ) ) {
			return __( 'Required Field "email" was not filled by the user.', 'wordpress-popup' );
		}

		if ( ! $allow_subscribed ) {

			/**
			 * Filter submitted form data to be processed
			 *
			 * @since 4.0
			 *
			 * @param array                                    $submitted_data
			 * @param int                                      $module_id                current module_id
			 * @param Hustle_Get_Response_Form_Settings $form_settings_instance
			 */
			$submitted_data = apply_filters(
				'hustle_provider_get_response_form_submitted_data_before_validation',
				$submitted_data,
				$module_id,
				$form_settings_instance
			);

			//triggers exception if not found.
			$global_multi_id 	= $addon_setting_values['selected_global_multi_id'];
			$api_key 			= $addon->get_setting( 'api_key', '', $global_multi_id );
			$api 				= $addon::api( $api_key );
			$existing_member 	= $this->get_subscriber( $api, $submitted_data['email'] );
			
			if ( $existing_member )
				$is_success = self::ALREADY_SUBSCRIBED_ERROR;
		}

		/**
		 * Return `true` if success, or **(string) error message** on fail
		 *
		 * @since 4.0
		 *
		 * @param bool                                     $is_success
		 * @param int                                      $module_id                current module_id
		 * @param array                                    $submitted_data
		 * @param Hustle_Get_Response_Form_Settings $form_settings_instance
		 */
		$is_success = apply_filters(
			'hustle_provider_get_response_form_submitted_data_after_validation',
			$is_success,
			$module_id,
			$submitted_data,
			$form_settings_instance
		);

		// process filter
		if ( true !== $is_success ) {
			// only update `_submit_form_error_message` when not empty
			if ( ! empty( $is_success ) ) {
				$this->_submit_form_error_message = (string) $is_success;
			}
			return $is_success;
		}

		return true;
	}

	/**
	 * Get subscriber for providers
	 *
	 * This method is to be inherited
	 * And extended by child classes.
	 * 
	 * Make use of the property `$_subscriber`
	 * Method to omit double api calls
	 *
	 * @since 4.0.2
	 *
	 * @param 	object 	$api
	 * @param 	mixed  	$data
	 * @return  mixed 	array/object API response on queried subscriber
	 */
	protected function get_subscriber( $api, $data ) {
		if( empty ( $this->_subscriber ) && ! isset( $this->_subscriber[ md5( $data ) ] ) ){
			$this->_subscriber[ md5( $data ) ] = $api->get_contact( $data );
		}
		return $this->_subscriber[ md5( $data ) ];
	}
}
