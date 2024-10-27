<?php
/**
 * Agreeme Checkboxes for WooCommerce - Functions
 *
 * @version 1.0.9
 * @since   1.0.0
 * @author  Amin Yasser.
 */
use Automattic\WooCommerce\Utilities\OrderUtil; //HPOS

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'agrwc_update_order_fields_data' ) ) {
	/*
	 * agrwc_update_order_fields_data.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 *
	 */
	function agrwc_update_orderfields_data( $order_id, $fields_data ) {
		//HPOS
			if ( method_exists("OrderUtil","custom_orders_table_usage_is_enabled") &&  OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// HPOS usage is enabled.

	$order = wc_get_order( $order_id );
		$order->update_meta_data( '_' . AGR_WC_ID . '_data', $fields_data ); 
	$order->save();
	///
			}else{
		update_post_meta( $order_id, '_' . AGR_WC_ID . '_data', $fields_data );
			}
	}
}



if ( ! function_exists( 'agrwc_get_product_terms' ) ) {
	/**
	 * agrwc_get_product_terms.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function agrwc_get_product_terms( $taxonomy = 'product_cat' ) {
		$product_terms = array();
		$_product_terms = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );
		if ( ! empty( $_product_terms ) && ! is_wp_error( $_product_terms ) ){
			foreach ( $_product_terms as $_product_term ) {
				$product_terms[ $_product_term->term_id ] = $_product_term->name;
			}
		}
		return $product_terms;
	}
}

if ( ! function_exists( 'agrwc_get_products' ) ) {
	/**
	 * agrwc_get_products.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function agrwc_get_products( $products = array(), $post_status = 'any' ) {
		$offset     = 0;
		$block_size = 1024;
		while( true ) {
			$args = array(
				'post_type'      => 'product',
				'post_status'    => $post_status,
				'posts_per_page' => $block_size,
				'offset'         => $offset,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'fields'         => 'ids',
			);
			$loop = new WP_Query( $args );
			if ( ! $loop->have_posts() ) {
				break;
			}
			foreach ( $loop->posts as $post_id ) {
				$products[ $post_id ] = get_the_title( $post_id ) . ' [ID:' . $post_id . ']';
			}
			$offset += $block_size;
		}
		return $products;
	}
}

if ( ! function_exists( 'agrwc_get_shipping_classes' ) ) {
	/**
	 * agrwc_get_shipping_classes.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function agrwc_get_shipping_classes() {
		$shipping_classes = array();
		if ( class_exists( 'WC_Shipping' ) ) {
			$wc_shipping              = WC_Shipping::instance();
			$shipping_classes_terms   = $wc_shipping->get_shipping_classes();
			$shipping_classes         = array( -1 => __( 'No shipping class', 'woocommerce' ) );
			foreach ( $shipping_classes_terms as $shipping_classes_term ) {
				$shipping_classes[ $shipping_classes_term->term_id ] = $shipping_classes_term->name;
			}
		}
		return $shipping_classes;
	}
}

if ( ! function_exists( 'agrwc_get_user_roles' ) ) {
	/**
	 * agrwc_get_user_roles.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 */
	function agrwc_get_user_roles() {
		global $wp_roles;
		$all_roles = ( isset( $wp_roles ) && is_object( $wp_roles ) ) ? $wp_roles->roles : array();
		$all_roles = apply_filters( 'editable_roles', $all_roles );
		$all_roles = array_merge( array(
			'guest' => array(
				'name'         => __( 'Guest', 'agreeme-checkbox-for-woocommerce' ),
				'capabilities' => array(),
			) ), $all_roles );
		$all_roles_options = array();
		foreach ( $all_roles as $_role_key => $_role ) {
			$all_roles_options[ $_role_key ] = $_role['name'];
		}
		return $all_roles_options;
	}
}

if ( ! function_exists( 'agrwc_is_user_role' ) ) {
	/**
	 * agrwc_is_user_role.
	 *
	 * @version 1.0.2
	 * @since   1.0.0
	 * @return  bool
	 */
	function agrwc_is_user_role( $user_roles, $user_id = 0 ) {
		$_user = ( 0 == $user_id ? wp_get_current_user() : get_user_by( 'id', $user_id ) );
		if ( ! isset( $_user->roles ) || empty( $_user->roles ) ) {
			$_user->roles = array( 'guest' );
		}
		if ( ! is_array( $_user->roles ) ) {
			return false;
		}
		if ( is_array( $user_roles ) ) {
			if ( in_array( 'administrator', $user_roles ) ) {
				$user_roles[] = 'super_admin';
			}
			$_intersect = array_intersect( $user_roles, $_user->roles );
			return ( ! empty( $_intersect ) );
		} else {
			return ( 'administrator' == $user_roles ?
				( in_array( 'administrator', $_user->roles ) || in_array( 'super_admin', $_user->roles ) ) :
				( in_array( $user_roles, $_user->roles ) )
			);
		}
	}
}

