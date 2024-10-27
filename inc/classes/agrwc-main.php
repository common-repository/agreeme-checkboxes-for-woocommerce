<?php
/**
 * Agreeme Checkboxes for WooCommerce - Core Class
 *
 * @version 1.0.2
 * @since   1.0.0
 * @author  Amin Yasser.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'agrwc_main' ) ) :

class agrwc_main {

	/**
	 * Constructor.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function __construct() {
		if ( 'yes' === get_option( 'agrwc_enabled', 'yes' ) ) {
				require_once( 'agrwc-cbx.php' );
			require_once( 'agrwc-cbxs.php' );
			//if ( is_front_page() ){
		require_once( 'agrwc-frontend.php' );
			//}
		//	require_once( 'agrwc-scripts.php' );
	//require_once( 'agrwc-orderdisplay.php' );
		
		}
	}

}

endif;

return new agrwc_main();
