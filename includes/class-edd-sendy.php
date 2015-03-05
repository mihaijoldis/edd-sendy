<?php
/**
 * EDD Sendy class, extension of the EDD base newsletter classs
 *
 * @copyright   Copyright (c) 2015, Dave Kiss
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0
*/

class EDD_Sendy extends EDD_Newsletter {

	/**
	 * Sets up the checkout label
	 */
	public function init() {
		global $edd_options;
		if ( ! empty( $edd_options['edd_sendy_label'] ) ) {
			$this->checkout_label = trim( $edd_options['edd_sendy_label'] );
		} else {
			$this->checkout_label = __( 'Signup for the newsletter', 'edd-sendy' );
		}

		// add_filter( 'edd_settings_extensions_sanitize', array( $this, 'save_settings' ) );
		add_filter( 'edd_metabox_save__edd_sendy', array( $this, 'save_settings' ) );
	}

	/**
	 * [render_metabox description]
	 * @return [type] [description]
	 */
	public function render_metabox() {

		global $post;

		echo '<p>' . __( 'Enter the list IDs you wish buyers to be subscribed to when purchasing, separated by a comma.', 'edd-sendy' ) . '</p>';

    $lists = (array) get_post_meta( $post->ID, '_edd_' . esc_attr( $this->id ), true );
    $string = empty($lists) ? '' : implode(',', $lists);
    echo '<input type="text" name="_edd_' . esc_attr( $this->id ) . '" value="' . esc_attr( $string ) . '"' . '>';
	}


	/**
	 * Registers the plugin settings
	 */
	public function settings( $settings ) {

		$edd_sendy_settings = array(
			array(
				'id'      => 'edd_sendy_settings',
				'name'    => '<strong>' . __( 'Sendy Settings', 'edd-sendy' ) . '</strong>',
				'desc'    => __( 'Configure Sendy Integration Settings', 'edd-sendy' ),
				'type'    => 'header'
			),
			array(
				'id'      => 'edd_sendy_api',
				'name'    => __( 'Sendy API Key', 'edd-sendy' ),
				'desc'    => __( 'Enter your Sendy API key, found on the Settings page of your Sendy installation', 'edd-sendy' ),
				'type'    => 'text',
				'size'    => 'regular'
			),
			array(
				'id'      => 'edd_sendy_installation_url',
				'name'    => __( 'Sendy Installation URL', 'edd-sendy' ),
				'desc'    => __( 'Enter the URL where you access your Sendy Installation, without the trailing slash.', 'edd-sendy' ),
				'type'    => 'text',
				'size'    => 'regular'
			),
			array(
				'id'      => 'edd_sendy_show_checkout_signup',
				'name'    => __( 'Show Signup on Checkout', 'edd-sendy' ),
				'desc'    => __( 'Allow customers to signup for the list entered below during checkout?', 'edd-sendy' ),
				'type'    => 'checkbox'
			),
			array(
				'id'      => 'edd_sendy_list',
				'name'    => __( 'Enter your list ID', 'edd-sendy'),
				'desc'    => __( 'Enter the list ID you wish to subscribe buyers to. This encrypted & hashed ID can be found under the "View all lists" section of your Sendy installation.', 'edd-sendy' ),
				'type'    => 'text',
				'size'    => 'regular'
			),
			array(
				'id'      => 'edd_sendy_label',
				'name'    => __( 'Checkout Label', 'edd-sendy' ),
				'desc'    => __( 'This is the text shown next to the signup option', 'edd-sendy' ),
				'type'    => 'text',
				'size'    => 'regular'
			),
		);

		return array_merge( $settings, $edd_sendy_settings );
	}

	/**
	 * Save the metabox
	 */
	public function save_metabox( $fields ) {
		$fields[] = '_edd_' . esc_attr( $this->id );
		return $fields;
	}

  /**
   * Convert list string to array on settings save
   */
  public function save_settings( $input ) {

    if ( is_string($input) ) {
      $input = explode(',', preg_replace('/\s+/', '', $input) );
    }

    return $input;
  }

	/**
	 * Determines if the checkout signup option should be displayed
	 */
	public function show_checkout_signup() {
		global $edd_options;

		return ! empty( $edd_options['edd_sendy_show_checkout_signup'] );
	}



	/**
	 * Subscribe an email to a list
	 */
	public function subscribe_email( $user_info = array(), $list_id = false, $opt_in_overridde = false ) {

		global $edd_options;

		// Make sure an API key has been entered
		if ( empty( $edd_options['edd_sendy_api'] ) OR empty( $edd_options['edd_sendy_installation_url'] ) ) {
			return false;
		}

    // Retrieve the global list ID if none is provided
    if ( ! $list_id ) {
      $list_id = ! empty( $edd_options['edd_sendy_list'] ) ? $edd_options['edd_sendy_list'] : false;
      if ( ! $list_id ) {
        return false;
      }
    }

    $config = array (
        'api_key'          => trim( $edd_options['edd_sendy_api'] ),
        'installation_url' => trim( $edd_options['edd_sendy_installation_url'] ),
        'list_id'          => $list_id
    );

		if ( ! class_exists( 'SendyPHP' ) ) {
			include( EDD_SENDY_PATH . '/includes/SendyPHP.php' );
		}

		try {

	    $sendy = new SendyPHP($config);

	    $result = $sendy->subscribe( array(
	                'name'  => $user_info['first_name'],
	                'email' => $user_info['email'],
	              ));

	    if ( $result['status'] == true ) {
	      return true;
	    } else {
	    	return false;
	    }

		} catch (Exception $e) {
			return false;
		}
	}

}