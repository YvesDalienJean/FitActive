<?php

class Hustle_Settings_Admin_Ajax {
	private $_hustle;

	private $_admin;

	public function __construct( Hustle_Settings_Admin $admin ) {
		$this->_admin = $admin;

		add_action( 'wp_ajax_hustle_remove_ips', array( $this, 'remove_ips_from_tables' ) );
		add_action( 'wp_ajax_hustle_reset_settings', array( $this, 'reset_settings' ) );
		add_action( 'wp_ajax_hustle_toggle_module_for_user', array( $this, 'toggle_module_for_user' ) );
		// This is used in wizards. Should be moved into popup-admin-ajax instead, since there's where common ajax actions from wizards are.
		add_action( 'wp_ajax_hustle_shortcode_render', array( $this, 'shortcode_render' ) );

		/**
		 * save settings
		 */
		add_action( 'wp_ajax_hustle_save_settings', array( $this, 'ajax_settings_save' ) );
	}

	/**
	 * Filter IPs
	 *
	 * @since 4.0
	 * @param string $ip_string
	 * @return array valid IPs
	 */
	private function filter_ips( $ip_string ) {

		// Create an array with their values.
		$ip_array = preg_split( '/[\s,]+/', $ip_string, null, PREG_SPLIT_NO_EMPTY );

		// Remove from the array the IPs that are not valid IPs.
		foreach ( $ip_array as $key => $ip ) {
			if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				unset( $ip_array[ $key ] );
				continue;
			}
		}

		return $ip_array;
	}

	/**
	 * Reset the plugin
	 *
	 * @since 4.0.3
	 */
	public function reset_settings() {
		Opt_In_Utils::validate_ajax_call( 'hustle_reset_settings' );
		Opt_In_Utils::is_user_allowed_ajax( 'hustle_edit_settings' );

		/**
		 * Fires before Settings reset
		 *
		 * @since 4.0.3
		 */
		do_action( 'hustle_before_reset_settings' );

		// Delete starts here.
		Hustle_Deletion::hustle_delete_custom_options();
		Hustle_Deletion::hustle_delete_addon_options();
		Hustle_Deletion::hustle_clear_module_views();
		Hustle_Deletion::hustle_clear_module_submissions();
		Hustle_Deletion::hustle_clear_modules();

		/**
		 * Fires after Settings reset
		 *
		 * @since 4.0.3
		 */
		do_action( 'hustle_after_reset_settings' );

	}

	/**
	 * Remove the requested IPs from views and conversions on batches.
	 *
	 * @since 3.0.6
	 */
	public function remove_ips_from_tables() {
		Opt_In_Utils::validate_ajax_call( 'hustle_remove_ips' );
		Opt_In_Utils::is_user_allowed_ajax( 'hustle_edit_settings' );

		/**
		 * From Tracking
		 */
		$range = filter_input( INPUT_POST, 'range', FILTER_SANITIZE_STRING );
		$tracking = Hustle_Tracking_Model::get_instance();
		global $hustle;
		$hustle_entries_admin = new Hustle_Entries_Admin( $hustle );
		if ( 'all' === $range ) {
			$tracking->set_null_on_all_ips();
			$hustle_entries_admin->delete_all_ip();
			$message = esc_html__( 'All IP addresses have been successfully deleted from the database.', 'wordpress-popup' );

		} else {
			$values = filter_input( INPUT_POST, 'ips', FILTER_SANITIZE_STRING );
			if ( ! empty( $values ) ) {
				$values = preg_replace( '/ /', '', $values );
				$r = preg_split( '/[\r\n]/', $values );
				$ios = array();
				foreach ( $r as $one ) {
					$is_valid = filter_var( $one, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
					if ( $is_valid ) {
						$ips[] = $one;
						continue;
					}
					$a = explode( '-', $one );
					if ( 2 !== sizeof( $a ) ) {
						continue;
					}
					$is_valid = filter_var( $a[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
					if ( ! $is_valid ) {
						continue;
					}
					$is_valid = filter_var( $a[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
					if ( ! $is_valid ) {
						continue;
					}
					$ips[] = array_map( 'ip2long', $a );
				}
				$tracking->set_null_on_selected_ips( $ips );
				$hustle_entries_admin->delete_selected_ips( $ips );
				$message = esc_html__( 'All selected IP addresses have been successfully deleted from the database.', 'wordpress-popup' );

			} else {
				$message = esc_html__( 'No IPs were deleted. You must provide at least one IP.', 'wordpress-popup' );
			}
		}

		wp_send_json_success( [ 'message' => $message ] );
	}

	public function toggle_module_for_user() {
		Opt_In_Utils::validate_ajax_call( 'hustle_modules_toggle' );
		Opt_In_Utils::is_user_allowed_ajax( 'hustle_edit_settings' );

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$user_type = filter_input( INPUT_POST, 'user_type', FILTER_SANITIZE_STRING );

		$module = Hustle_Module_Model::instance()->get( $id );
		if ( is_wp_error( $module ) ) {
			wp_send_json_error();
		}
		$result = $module->toggle_activity_for_user( $user_type );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_messages() );
		}
		wp_send_json_success( sprintf( __( 'Successfully toggled for user type %s', 'wordpress-popup' ), $user_type ) );
	}

	/**
	 * Saves the global privacy settings.
	 *
	 * @since 4.0
	 */
	public function save_global_privacy_settings() {

		$filter_args = array(
			'ip_tracking'						=> FILTER_SANITIZE_STRING,
			// Account erasure request
			'retain_sub_on_erasure'				=> FILTER_SANITIZE_STRING,
			// Submissions retention
			'retain_submission_forever'			=> FILTER_SANITIZE_STRING,
			'submissions_retention_number'		=> FILTER_SANITIZE_NUMBER_INT,
			'submissions_retention_number_unit'	=> FILTER_SANITIZE_STRING,
			// IPs retention
			'retain_ip_forever'					=> FILTER_SANITIZE_STRING,
			'ip_retention_number'				=> FILTER_SANITIZE_NUMBER_INT,
			'ip_retention_number_unit'			=> FILTER_SANITIZE_STRING,
			// Tracking retention
			'retain_tracking_forever'			=> FILTER_SANITIZE_STRING,
			'tracking_retention_number'			=> FILTER_SANITIZE_NUMBER_INT,
			'tracking_retention_number_unit'	=> FILTER_SANITIZE_STRING,
		);
		$data = filter_input_array( INPUT_POST, $filter_args, false );

		$stored_settings = Hustle_Settings_Admin::get_privacy_settings();
		
		$new_settings = array_merge( $stored_settings, $data );

		Hustle_Settings_Admin::update_hustle_settings( $new_settings, 'privacy' );
		$this->send_success_notification();
	}

	/**
	 * Saves the global privacy settings.
	 *
	 * @since 4.0.2
	 */
	public function save_global_data_settings( $data ) {

		$reset_settings_uninstall = '0';

		//Settings retention
		if( isset( $data['reset_settings_uninstall'] ) && '1' ===  $data['reset_settings_uninstall'] ){
			$reset_settings_uninstall = '1';
		}

		$value = array(
			'reset_settings_uninstall' => $reset_settings_uninstall,
		);
		Hustle_Settings_Admin::update_hustle_settings( $value, 'data' );
		$this->send_success_notification();
	}

	/**
	 * Saves the global email sender name and email address.
	 *
	 * @since 3.0.5
	 */
	public function save_global_email_settings() {
		$default = [
			'sender_email_name' => '',
			'sender_email_address' => '',
		];
		$old_value = Hustle_Settings_Admin::get_hustle_settings( 'emails' );
		$new_value = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( ! $new_value ) {
			$new_value = array();
		}

		$value = array_merge( $old_value, $new_value );
		$value = shortcode_atts( $default, $value );
		if ( !empty( $value['sender_email_address'] ) && !is_email( $value['sender_email_address'] ) ) {
			wp_send_json_error();
		}
		Hustle_Settings_Admin::update_hustle_settings( $value, 'emails' );
	}

	public function shortcode_render() {
		Opt_In_Utils::validate_ajax_call( 'hustle_shortcode_render' );
		Opt_In_Utils::is_user_allowed_ajax( 'hustle_edit_settings' );
		$content = filter_input( INPUT_POST, 'content' );
		$rendered_content = apply_filters( 'the_content', $content );
		wp_send_json_success( array(
			'content' => $rendered_content,
		));
	}

	/**
	 * Save the data under the Top Metric tab.
	 *
	 * @since 4.0
	 *
	 * @return bool
	 */
	private function save_top_metrics_settings() {

		$metrics = $_POST['metrics']; // WPCS: CSRF ok.

		// Only 3 metrics can be selected. No more.
		if ( 3 < count( $metrics ) ) {
			return false;
		}

		$allowed_metric_keys = array( 'average_conversion_rate', 'today_conversions', 'last_week_conversions', 'last_month_conversions', 'total_conversions', 'most_conversions', 'inactive_modules_count', 'total_modules_count' );

		$data_to_store = array();
		foreach ( $metrics as $name ) {
			if ( in_array( $name, $allowed_metric_keys, true ) ) {
				$data_to_store[] = $name;
			}
		}

		Hustle_Settings_Admin::update_hustle_settings( $data_to_store, 'top_metrics' );

		return true;
	}

	/**
	 * Save the reCaptcha settings.
	 *
	 * @since 4.0
	 *
	 * @param array $data Submitted data to be saved
	 */
	private function save_recaptcha_settings( $data ) {
		$sitekey = '';
		if ( isset( $data['sitekey'] ) ) {
			$sitekey = filter_var( $data['sitekey'], FILTER_SANITIZE_STRING );
		}
		$secret = '';
		if ( isset( $data['secret'] ) ) {
			$secret = filter_var( $data['secret'], FILTER_SANITIZE_STRING );
		}
		$language = '';
		if ( isset( $data['language'] ) ) {
			$language = filter_var( $data['language'], FILTER_SANITIZE_STRING );
		}
		$value = array(
			'sitekey' => $sitekey,
			'secret' => $secret,
			'language' => $language,
		);
		Hustle_Settings_Admin::update_hustle_settings( $value, 'recaptcha' );
	}

	/**
	 * Save the Accessibility settings.
	 *
	 * @since 4.0
	 *
	 * @param array $data Submitted data to be saved
	 */
	private function save_accessibility_settings( $data ) {
		$color = null;
		if ( isset( $data['accessibility_color'] ) ) {
			$color = filter_var( $data['accessibility_color'], FILTER_VALIDATE_BOOLEAN );
		}
		if ( is_null( $color ) ) {
			wp_send_json_error();
		}
		$value = array(
			'accessibility_color' => $color,
		);
		Hustle_Settings_Admin::update_hustle_settings( $value, 'accessibility' );
	}

	/**
	 * Save the Unsubscribe settings.
	 *
	 * @since 4.0
	 *
	 * @param array $data Submitted data to be saved
	 * @return bool
	 */
	private function save_unsubscribe_settings( $data ) {

		$email_body = wp_json_encode( $data['email_message'] );
		$sanitized_data = Opt_In_Utils::validate_and_sanitize_fields( $data );

		// Save the messages to be displayed in the unsubscription process.
		$messages_data = array(
			'enabled' => isset( $sanitized_data['messages_enabled'] ) ? $sanitized_data['messages_enabled'] : '0',
			'get_lists_button_text' => $sanitized_data['get_lists_button_text'],
			'submit_button_text' => $sanitized_data['submit_button_text'],
			'invalid_email' => $sanitized_data['invalid_email'],
			'email_not_found' => $sanitized_data['email_not_found'],
			'invalid_data' => $sanitized_data['invalid_data'],
			'email_submitted' => $sanitized_data['email_submitted'],
			'successful_unsubscription' => $sanitized_data['successful_unsubscription'],
			'email_not_processed' => $sanitized_data['email_not_processed'],
		);

		// Save the unsubscription email settings.
		$email_data = array(
			'enabled' => isset( $sanitized_data['email_enabled'] ) ? $sanitized_data['email_enabled'] : '0',
			'email_subject' => $sanitized_data['email_subject'],
			'email_body' => $email_body,
		);

		$value = array(
			'messages' => $messages_data,
			'email' => $email_data,
		);
		Hustle_Settings_Admin::update_hustle_settings( $value, 'unsubscribe' );

		return true;

	}

	/**
	 * Save Hustle settings
	 *
	 * @since 4.0
	 *
	 * @todo Handle error messages
	 */
	public function ajax_settings_save() {
		Opt_In_Utils::validate_ajax_call( 'hustle-settings' );
		Opt_In_Utils::is_user_allowed_ajax( 'hustle_edit_settings' );

		$tab = filter_input( INPUT_POST, 'target', FILTER_SANITIZE_STRING );

		switch ( $tab ) {
			case 'permissions':
				$data = isset( $_POST['data'] ) ? $_POST['data'] : array(); // WPCS: CSRF ok.
				$roles = Opt_In_Utils::get_user_roles();

				foreach ( $data as $key => $value ) {

					$permission = '';
					$value = ! empty( $value ) ? $value : array();

					// "Edit" permission role for each module.
					if ( preg_match( '/^module\-(\d+)$/', $key, $matches ) ) {
						$update_edit_module = true;
						$id = $matches[1];
						$module = Hustle_Module_Model::instance()->get( $id );
						if ( ! is_wp_error( $module ) ) {
							$module->update_edit_role( $value );
						}

					} elseif ( 'create' === $key ) {
						// Global "Create" permission role.
						$permission = 'permission_create';
						Hustle_Settings_Admin::update_hustle_settings( $value, $permission );

					} elseif ( 'edit_integrations' === $key ) {
						// Global "Edit Integration" role.
						$permission = 'permission_edit_integrations';
						Hustle_Settings_Admin::update_hustle_settings( $value, $permission );

					} elseif ( 'access_emails' === $key ) {
						// Global "Access Email List" role.
						$permission = 'permission_access_emails';
						Hustle_Settings_Admin::update_hustle_settings( $value, $permission );

					} elseif ( 'edit_settings' === $key ) {
						// Global "Edit Settings" role.
						$permission = 'permission_edit_settings';
						Hustle_Settings_Admin::update_hustle_settings( $value, $permission );

					} else {
						continue;
					}

					if ( ! empty( $permission ) ) {
						$cap = str_replace( 'permission_', 'hustle_', $permission );
						foreach ( $roles as $role_key => $role_name ) {
							if ( 'administrator' === $role_key ) {
								continue;
							}
							// get the role object
							$role = get_role( $role_key );

							if ( ! $role ) {
								continue;
							}

							if ( in_array( $role_key, $value, true ) ) {
								// add capability
								$role->add_cap( $cap );
							} else {
								// remove capability
								$role->remove_cap( $cap );
							}
						}
					}
				}

				if ( ! empty( $update_edit_module ) ) {
					// add/remove hustle_edit_module capability
					Opt_In_Utils::update_hustle_edit_module_capability();
				}

				// add/remove hustle_menu capability
				$hustle_capabilities = array(
					'hustle_edit_module',
					'hustle_create',
					'hustle_edit_integrations',
					'hustle_access_emails',
					'hustle_edit_settings',
				);

				foreach ( $roles as $role_key => $role_name ) {
					$role = get_role( $role_key );
					$capabilities = $role->capabilities;
					if ( ! empty( array_intersect( array_keys( $capabilities ), $hustle_capabilities ) ) ) {
						$role->add_cap( 'hustle_menu' );
					} else {
						$role->remove_cap( 'hustle_menu' );
					}
				}
				/**
				 * success
				 */
				$this->send_success_notification();
				break;

			case 'top_metrics':
				$saved = $this->save_top_metrics_settings();
				if ( $saved ) {
					$this->send_success_notification();
				} else {
					wp_send_json_error();
				}
				break;

			case 'analytics':
				$data = isset( $_POST['data'] ) ? $_POST['data'] : array(); // WPCS: CSRF ok.
				if ( isset( $data['enabled'] ) ) {
					$value = Hustle_Settings_Admin::get_hustle_settings( 'analytics' );
					$value['enabled'] = $data['enabled'];
					$reload = true;
				} else {
					$value = array(
						'enabled' => '1',
						'title' => isset( $data['title'] )? filter_var( $data['title'], FILTER_SANITIZE_STRING ):'',
						'role' => isset( $data['role'] )? filter_var( $data['role'], FILTER_SANITIZE_STRING ):'',
						'modules' => isset( $data['modules'] )? $data['modules']:array(),
					);
					$reload = false;
				}

				Hustle_Settings_Admin::update_hustle_settings( $value, 'analytics' );
				$this->send_success_notification( '', $reload );
				break;

			case 'recaptcha':
				$data = isset( $_POST['data'] ) ? $_POST['data'] : array(); // WPCS: CSRF ok.
				$this->save_recaptcha_settings( $data );
				$success_message = __( "reCAPTCHA configured successfully. You can now add reCAPTCHA field to your opt-in forms where you want the reCAPTCHA to appear.", 'wordpress-popup' );
				$this->send_success_notification( $success_message );
				break;

			case 'accessibility':
				$data = isset( $_POST['data'] ) ? $_POST['data'] : array(); // WPCS: CSRF ok.
				$this->save_accessibility_settings( $data );
				$this->send_success_notification();
				break;

			case 'unsubscribe':
				parse_str( $_POST['data'], $data ); // WPCS: CSRF ok.
				$this->save_unsubscribe_settings( $data );
				$this->send_success_notification();
				break;

			case 'emails':
				$this->save_global_email_settings();
				$this->send_success_notification();
				break;

			case 'privacy':
				$this->save_global_privacy_settings();
				break;
			case 'data':
				$data = isset( $_POST['data'] ) ? $_POST['data'] : array(); // WPCS: CSRF ok.
				$this->save_global_data_settings( $data );
				break;

			default: // Failed
				wp_send_json_error();
		}

		wp_send_json_error();
	}

	/**
	 * Call wp_send_json_success with the expected response
	 *
	 * @since 4.0
	 *
	 * @param string $message
	 * @param boolean $reload
	 */
	private function send_success_notification( $message = '', $reload = false ) {
		$response = array(
			'message' => $message, //if it's empty - use optinVars.messages.settings_saved
			'reload'  => $reload,
		);
		wp_send_json_success( $response );
	}
}
