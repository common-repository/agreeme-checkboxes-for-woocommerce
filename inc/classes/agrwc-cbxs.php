<?php
/**
 * Handles storage and retrieval of Checkboxes
 *
 * @version 1.0.2
 * @since   1.0.0
 * @package AGRWC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AGR Checkboxes class.
 */
class AGRWC_CBX {

	/**
	 * Return the pricing cbx class.
	 *
	 * @return string
	 */
	private static function get_cbx_class_name() {
		$classname = 'AGRWC_CB';
		

		return $classname;
	}

	/**
	 * Return a empty pricing cbx object.
	 *
	 * @return AGRWC_CB
	 */
	public static function create() {
		$classname = self::get_cbx_class_name();
		return new $classname();
	}

	/**
	 * Save a cbx.
	 *
	 * @since 1.8.0
	 * @param AGRWC_CB $cbx Checkbox instance.
	 * @return string
	 */
	public static function save( $cbx ) {
		$cbxs = (array) get_option( 'agrwc_checkboxes_data', array() );

		if ( ! $cbx->get_cbx_id() ) {
			$cbx_id = self::get_unique_slug( sanitize_key( sanitize_title( $cbx->get_name() ) ), array_keys( $cbxs ) );
			$cbx->set_cbx_id( $cbx_id );
		} else {
			$cbx_id = $cbx->get_cbx_id();
		}
		$cbx_data = $cbx->get_data();
		unset( $cbx_data['cbx_id'] );

		$cbxs[ $cbx_id ] = $cbx_data;
	
		update_option( 'agrwc_checkboxes_data', $cbxs );

		return $cbx_id;
	}

	/**
	 * Save a group cbxs.
	 *
	 * @since 1.8.0
	 * @param array $cbxs Array of  cbxs.
	 */
	public static function bulk_save( $cbxs ) {
		$acbxs = (array) get_option( 'agrwc_checkboxes_data', array() );

		foreach ( $cbxs as $cbx ) {

			if ( ! $cbx->get_cbx_id() ) {
				$cbx_id = self::get_unique_slug( sanitize_key( sanitize_title( $cbx->get_name() ) ), array_keys( $cbxs ) );
				$cbx->set_cbx_id( $cbx_id );
			} else {
				$cbx_id = $cbx->get_cbx_id();
			}

			$cbx_data = $cbx->get_data();
			unset( $cbx_data['cbx_id'] );

			$acbxs[ $cbx_id ] = $cbx_data;
		}
		update_option( 'agrwc_checkboxes_data', $acbxs );
	}

	/**
	 * Get a unique slug that indentify a cbx
	 *
	 * @since 1.8.0
	 * @param string $new_slug New slug.
	 * @param array  $slugs All IDs of the cbxs.
	 * @return array
	 */
	private static function get_unique_slug( $new_slug, $slugs ) {
		$seqs = array();

		foreach ( $slugs as $slug ) {
			$slug_parts = explode( '-', $slug, 2 );
			if ( $slug_parts[0] === $new_slug && ( count( $slug_parts ) === 1 || is_numeric( $slug_parts[1] ) ) ) {
				$seqs[] = isset( $slug_parts[1] ) ? $slug_parts[1] : 0;
			}
		}

		if ( $seqs ) {
			rsort( $seqs );
			$new_slug = $new_slug . '-' . ( $seqs[0] + 1 );
		}

		return $new_slug;
	}

	/**
	 * Delete a cbx.
	 *
	 * @since 1.8.0
	 * @param AGRWC  $cbx instance.
	 */
	public static function delete( $cbx ) {
		global $wpdb;

		$cbxs = (array) get_option( 'agrwc_checkboxes_data', array() );

		if ( isset( $cbxs[ $cbx->get_cbx_id() ] ) ) {
			unset( $cbxs[ $cbx->get_cbx_id() ] );
			update_option( 'agrwc_checkboxes_data', $cbxs );
		}
	}

	/**
	 * Get pricing cbxs.
	 *
	 * @param array $cbx_ids Array of IDs of Pricing cbxs to filter the result. Optional. False return all.
	 * @return array Array of AGRWC_CB instances.
	 */
	public static function get_cbxs( $cbx_ids = false ) {
		$classname = self::get_cbx_class_name();
		$cbxs     = array();

		foreach ( (array) get_option( 'agrwc_checkboxes_data', array() ) as $id => $data ) {
			if ( ! empty( $cbx_ids ) && is_array( $cbx_ids ) && ! in_array( $id, $cbx_ids, true ) ) {
				continue;
			}
			$cbxs[ $id ] = new $classname( array_merge( $data, array( 'cbx_id' => $id ) ) );
		}

		return $cbxs;
	}

	/**
	 * Get a  cbx.
	 *
	 * @param mixed $the_cbx AGRWC_CB|array|string|bool cbx instance, array of  cbx properties,  cbx ID, or false to return the current  cbx.
	 * @return AGRWC_CB
	 */
	public static function get_cbx( $the_cbx = false ) {
		$cbx      = false;
		$classname = self::get_cbx_class_name();

		if ( is_object( $the_cbx ) && in_array( get_class( $the_cbx ), array( 'AGRWC_CB', 'AGRWC_CB_Pro' ), true ) ) {
			$cbx = $the_cbx;
		} elseif ( is_array( $the_cbx ) ) {
			$cbx = new $classname( $the_cbx );
		} elseif ( ! $the_cbx ) {
			$cbx = WCPBC()->current_cbx;
		} else {
			$cbx = self::get_cbx_by_id( $the_cbx );
		}

		return $cbx;
	}

	/**
	 * Get cbx by an ID.
	 *
	 * @param string $id  cbx ID.
	 * @return AGRWC_CB
	 */
	public static function get_cbx_by_id( $id ) {
		$cbx      = null;
		$cbxs     = (array) get_option( 'agrwc_checkboxes_data', array() );
		$classname = self::get_cbx_class_name();

		if ( ! empty( $cbxs[ $id ] ) ) {
			$cbx = new $classname( array_merge( $cbxs[ $id ], array( 'cbx_id' => $id ) ) );
		}

		return $cbx;
	}





	/**
	 * There is  cbxs.
	 *
	 * @return bool
	 */
	public static function has_cbxs() {
		$cbxs = (array) get_option( 'agrwc_checkboxes_data', array() );
		return count( $cbxs );
	}
}
