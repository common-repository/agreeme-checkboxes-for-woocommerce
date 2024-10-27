<?php
/*
Plugin Name: AgreeMe Checkboxes For WooCommerce
Plugin URI: https:///agreeme-checkbox-for-woocommerce/
Description: Add checkbox fields to WooCommerce pages.
Version: 1.1.2
Author: Amin Y
Author URI: https://qcompsolutions.com
Text Domain: agree-me-woocommerce
Domain Path: /languages
Copyright: © 2022 Qcompsolutions.com
WC tested up to: 8.2.2
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Constants
if ( ! defined( 'AGR_WC_VERSION' ) ) {
	define( 'AGR_WC_VERSION', '1.0.0' );
}
if ( ! defined( 'AGR_WC_ID' ) ) {
	define( 'AGR_WC_ID',      'agrwc-cbx' );
}
if ( ! defined( 'AGR_WC_KEY' ) ) {
	define( 'AGR_WC_KEY',     'agr_wc_checkbox' );
}
// Define AGRWC_PLUGIN_FILE.
if ( ! defined( 'AGRWC_PLUGIN_FILE' ) ) {
	define( 'AGRWC_PLUGIN_FILE', __FILE__ );
}


define( 'AGRWC_YES',       __( 'Yes', 'agreeme-checkbox-for-woocommerce' ) );
define( 'AGRWC_NO',       __( 'No', 'agreeme-checkbox-for-woocommerce' ) );

//HPOS COMPATIBLE CODE
 add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );


if ( ! function_exists( 'agr_wc_get_option' ) ) {
	/**
	 * agr_wc_get_option.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function agrwc_get_option( $option, $default = false ) {
		return get_option( AGR_WC_ID . '_' . $option, $default );
	}
}










if ( ! class_exists( 'AGR_WC' ) ) :

 function agrwc_activate() {
	////////////// check for options............and add basic options

	   if(get_option('agrwc_enabled', false)){

    }
    else {
	
	

	add_option('agrwc_enabled', 'yes');
	  
	
	}
}

 function agrwc_deactivate() {
	//////////////
	////////////// remove options
	
	
	
	}
	
	



	/**
	 * agrwc_uninstall.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
function agrwc_uninstall() {
	//////////////
	////////////// remove options
	
	delete_option("agrwc_checkboxes_data");
	
	}
	
	
register_activation_hook(__FILE__,  'agrwc_activate'  );
register_deactivation_hook(__FILE__, 'agrwc_deactivate'  );
register_uninstall_hook(__FILE__, 'agrwc_uninstall');
/**
 * Main AGR_WC Class
 *
 * @class   AGR_WC
 * @version 1.0.2
 */
final class AGR_WC {

	/**
	 * @var   AGR_WC The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main AGR_WC Instance
	 *
	 * Ensures only one instance of AGR_WC is loaded or can be loaded.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 * @static
	 * @return  AGR_WC - Main instance
	 */
	static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * AGR_WC Constructor.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 * @access  public
	 *
	 */
	function __construct() {
		

		
		// Check for active plugins
		if (
			! $this->is_plugin_active( 'woocommerce/woocommerce.php' )
			
		) {
			return;
		}
       

		// Set up localisation
load_plugin_textdomain( 'agreeme-checkbox-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	
	

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}
	}

	/**
	 * is_plugin_active.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function includes() {
		// Functions
		require_once( 'inc/agrwc-functions.php' );

	
		
			
		// Main
	 require_once( 'inc/classes/agrwc-main.php' );
	}
	
	
	

	
/**
	 
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	public static function agrwc_activate() {
	////////////// check for options............and add basic options



	   if(get_option('agrwc_enabled', false)){

    }
    else {
	add_option('agrwc_enabled', 'yes');
	add_option('agrwc_buttonclasses', 'yes');
	add_option('agrwc_formclasses', 'yes');
	add_option('agrwc_alertmsg', 'yes');
	}
}



	/**
	 * alg_wc_ccf_get_default_date_format.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	public static  function agrwc_deactivate() {
	//////////////
	////////////// remove options
	
	
	
	}
	
	



	/**
	 * alg_wc_ccf_get_default_date_format.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	public static function agrwc_uninstall() {
	//////////////
	////////////// remove options
	
	delete_option('agrwc_enabled');
	delete_option('agrwc_buttonclasses');
	delete_option('agrwc_formclasses');
	delete_option('agrwc_alertmsg');
	
	}
	
	
	/**
	 * admin.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		// Version update
		if ( agrwc_get_option( 'version', '' ) !== AGR_WC_VERSION ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . AGR_WC_ID ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';

		return array_merge( $custom_links, $links );
	}

	/**
	 * Add Custom Checkout Fields settings tab to WooCommerce settings.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		
	
		$settings[] = require_once( 'inc/options/agrwc-settings-cbx.php' );
		
		
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function version_updated() {
		update_option( AGR_WC_ID . '_' . 'version', AGR_WC_VERSION );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 * @return  string
	 */
	public static function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 * @return  string
	 */
	public static function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

endif;




if ( ! function_exists( 'agrwc_checkbox_fields' ) ) {
	/**
	 * Returns the main instance of AGR_WC.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 * @return  AGR_WC
	 */
	 
	function agrwc_checkbox_fields() {
		
		
		return AGR_WC::instance();
		


	
	
}

return agrwc_checkbox_fields();

	
   
	
		
		
	}
