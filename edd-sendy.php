<?php
/*
Plugin Name: Easy Digital Downloads - Sendy
Plugin URL: http://easydigitaldownloads.com/extension/sendy
Description: Include a Sendy signup option with your Easy Digital Downloads checkout
Version: 1.0
Author: Dave Kiss
Author URI: http://davekiss.com
Contributors: Dave Kiss
*/

define( 'EDD_SENDY_STORE_API_URL', 'https://easydigitaldownloads.com' );
define( 'EDD_SENDY_PRODUCT_NAME', 'Sendy' );
define( 'EDD_SENDY_PATH', dirname( __FILE__ ) );

/*
|--------------------------------------------------------------------------
| LICENSING / UPDATES
|--------------------------------------------------------------------------
*/

if ( class_exists( 'EDD_License' ) && is_admin() ) {
  $edd_sendy_license = new EDD_License( __FILE__, EDD_SENDY_PRODUCT_NAME, '1.0', 'Dave Kiss' );
}

if ( ! class_exists( 'EDD_Newsletter' ) ) {
  include( EDD_SENDY_PATH . '/includes/class-edd-newsletter.php' );
}

if ( ! class_exists( 'EDD_Sendy' ) ) {
  include( EDD_SENDY_PATH . '/includes/class-edd-sendy.php' );
}

new EDD_Sendy( 'sendy', 'Sendy' );