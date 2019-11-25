<?php

class Hustle_Popup_Integrations extends Hustle_Meta {
	
	/**
	 * Get the defaults for this meta.
	 * 
	 * @since 4.0
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'allow_subscribed_users' => '0',
			'active_integrations' 	 => 'local_list', // Default active integration.
			'active_integrations_count' => '1', // Default. Only local_list is active.
		);
	}
}
