<?php
/**
 * Agreeme Checkbox for WooCommerce - Thirdparty Class
 * Checks and add code for plugin to work with other plugins.
 * @version 1.0.2
 * @since   1.0.0
 * @author  Amin Yasser.
 */
use Automattic\WooCommerce\Utilities\OrderUtil; //HPOS

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'AGRWC_Thirdparty' ) ) :

class AGRWC_Thirdparty {

	/**
	 * Constructor.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 * @todo    [dev] (test) "WooCommerce – Store Exporter" - test if it's still working
	 */
	function __construct() {
		// "WooCommerce – Store Exporter" plugin - https://wordpress.org/plugins/woocommerce-exporter/
		add_filter( 'woo_ce_order_fields', array( $this, 'add_custom_fields_to_store_exporter' ) );
		add_filter( 'woo_ce_order',        array( $this, 'add_custom_fields_to_store_exporter_order' ), PHP_INT_MAX, 2 );
	}

	/**
	 * add_custom_fields_to_store_exporter_order.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function add_custom_fields_to_store_exporter_order( $order, $order_id ) {
	$fields_data = array();
		
	$val_arr[0]=AGRWC_NO;
	$val_arr[1]=AGRWC_YES;
		

			$cbxs   = AGRWC_CBX::get_cbxs();
		$show_cbx=array();
		//loop through all...
		
		foreach($cbxs as $id=>$cbx)
		{
			$location_arr=$cbx->get_olocations();
			
		if(in_array(2,$location_arr)) //product page addtocart button
			{
$show_cbx[]=$id;
			}				
			
		}
		
		
		
	
	/////AMIN WC3 HPOS	 
	if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// HPOS usage is enabled.

	
	$meta_data_obj =	 $order->get_meta_data() ;
foreach( $order->get_meta_data() as $meta_data_obj ) {
    $meta_data_array = $meta_data_obj->get_data();
	   $key   = $meta_data_array['key']; // The meta key
    $val = $meta_data_array['value']; // The meta value
	
	if(strstr($key,"argwc_"))
	{
		$keysarr=explode("_argwc_",$key);
		
		if(in_array(trim($keysarr[1]),$show_cbx))
		{
			
			$id=(int)trim($keysarr[1]);
			$valid=WC()->session->get( 'agrwc-'.$id );
			$meta_key='Agree-'.$id;
$meta_value=$cbx->get_label()."-".$val_arr[(int)$valid];
	$order->{$meta_key} = $meta_value;
					
		
		}
		
	}
	
}

	/////AMIN WC3 HPOS	 $meta_data_obj =	 $order->get_meta_data() ;
} else {
	// Traditional CPT-based orders are in use.
		
		
        $custom_field_value = get_post_meta( $order->id );
		
	
		foreach($custom_field_value as $key=>$val)
{
	
	if(strstr($key,"argwc_"))
	{
		$keysarr=explode("_argwc_",$key);
		
		if(in_array(trim($keysarr[1]),$show_cbx))
		{$id=(int)trim($keysarr[1]);
			
			$meta_key='Agree-'.$id;
			$valid=WC()->session->get( 'agrwc-'.$id );
$meta_value=$cbx->get_label()."-".$val_arr[$valid];
	$order->{$meta_key} = $meta_value;
					
		
		}
		
	}
	
}

	
}			
						
	/////////////	
	if($session_started==1)	session_write_close();
	
		return $order;
	}
	/**
	 * add_custom_fields_to_store_exporter.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function add_custom_fields_to_store_exporter( $fields ) {

	    return $fields;
	}
}

endif;

return new AGRWC_Thirdparty();
