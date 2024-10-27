<?php
/**
 * Agreeme Checkboxes for WooCommerce - Order Display Class
 *
 * @version 1.0.2
 * @since   1.0.0
 * @author  Amin Yasser.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'argwc_Orderdisplay' ) ) :

class argwc_Orderdisplay {

	/**
	 * Constructor.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function __construct() {
		
	///add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'add_agrwc_meta_admin_order' ), PHP_INT_MAX );
		
		///add_action( 'woocommerce_email_after_order_table',                 array( $this, 'add_agrwc_meta_to_emails' ), PHP_INT_MAX );
	
	}








	/**
	 * get_order_id.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
public	function get_order_id( $_order ) {
		if ( ! $_order || ! is_object( $_order ) ) {
			return 0;
		}
		if ( ! isset( $this->is_wc_version_below_3_0_0 ) ) {
			$this->is_wc_version_below_3_0_0 = version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' );
		}
		return ( $this->is_wc_version_below_3_0_0 ? $_order->id : $_order->get_id() );
	}



}

endif;

return new argwc_Orderdisplay();