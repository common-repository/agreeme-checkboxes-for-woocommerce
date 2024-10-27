<?php
/**
 * WooCommerce Agreeme Checkboxes Listing table.
 *
 *
 * @since   1.0.0
 * @version 1.0.5
 * @package AGRWC
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * AGRWC_Cbx_List_Table Class
 */
class AGRWC_Cbx_List_Table extends WP_List_Table {
    /**
     * Base currency
     *
     * @var string
     */
    protected $base_currency;
    protected $ix;
    /**
     * Initialize the regions table list
     */
    public function __construct() {
        parent::__construct(array('singular' => __('Checkbox', 'agreeme-checkbox-for-woocommerce'), 'plural' => __('Checkboxes', 'agreeme-checkbox-for-woocommerce'), 'ajax' => false,));
    }
    /**
     * Get a list of CSS classes for the WP_List_Table table tag.
     *
     * @return array List of CSS classes for the table tag.
     */
    protected function get_table_classes() {
        return array('widefat', 'fixed', $this->_args['plural']);
    }
    /**
     * Get list columns
     *
     * @return array
     */
    public function get_columns() {
        return apply_filters('agreeme-checkbox-for-woocommerce_columns', array('cb' => '', 'name' => __('Checkbox name', 'agreeme-checkbox-for-woocommerce'),'products' => __('Products', 'agreeme-checkbox-for-woocommerce'), 'locations' => __('Locations', 'agreeme-checkbox-for-woocommerce'), 'required' => __('Required', 'agreeme-checkbox-for-woocommerce'),));
    }
    /**
     * Default column handler.
     *
     * @param AGRWC_CBX $item        Item being shown.
     * @param string     $column_name Name of column being shown.
     * @return string Default column output.
     */
    public function column_default($item, $column_name) {
        return apply_filters('agreeme-checkbox-for-woocommerce_column_' . $column_name, $item);
    }
    /**
     * Column cb.
     *
     * @param AGRWC_CBX $cbx  cbx instance.
     * @return string
     */
    public function column_cb($cbx) {
        if ($cbx->get_cbx_id()) {
            return '<span></span>';
        } else {
            return '<span class="cbx-worldwide-icon"></span>';
        }
    }
    public function column_hash($cbx) {
        if ($cbx->get_cbx_id()) {
            return '<span>' . $cbx->get_cbx_id() . '</span>';
        } else {
            return '<span class="cbx-worldwide-icon"></span>';
        }
    }
    /**
     * Return name column.
     *
     * @param AGRWC_CBX $cbx  cbx instance.
     * @return string
     */
    public function column_name($cbx) {
        if ($cbx->get_cbx_id()) {
            $edit_url = admin_url('admin.php?page=wc-settings&tab=agrwc-cbx&section=cbxs&cbx_id=' . $cbx->get_cbx_id());
            $actions = array('id' => sprintf('Slug: %s', $cbx->get_cbx_id()), 'edit' => '<a href="' . esc_url($edit_url) . '">' . __('Edit', 'agreeme-checkbox-for-woocommerce') . '</a>', 'trash' => '<a class="submitdelete wcpbc-delete-cbx" title="' . esc_attr__('Delete', 'agreeme-checkbox-for-woocommerce') . '" href="' . esc_url(wp_nonce_url(add_query_arg(array('delete_cbx' => $cbx->get_cbx_id()), admin_url('admin.php?page=wc-settings&tab=agrwc-cbx&section=cbxs')), 'agreeme-checkbox-for-woocommerce-delete-cbx')) . '">' . __('Delete', 'agreeme-checkbox-for-woocommerce') . '</a>',);
            $row_actions = array();
            foreach ($actions as $action => $link) {
                $row_actions[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
            }
            $output = sprintf('<a href="%1$s">%2$s</a>', esc_url($edit_url), $cbx->get_name());
            $output.= '<div class="row-actions">' . implode(' | ', $row_actions) . '</div>';
        } else {
            $output = '<span>' . $cbx->get_name() . '</span><div class="row-actions">&nbsp;</div>';
        }
        return $output;
    }
    /**
     * Return countries column.
     *
     * @param AGRWC_CBX $cbx cbx instance.
     * @return string
     */
    public function column_products($cbx) {
   $output = $cbx->get_productname();
       
        return $output;
    }
    /**
     * Return currency column
     *
     * @param AGRWC_CBX $cbx cbx instance.
     * @return string
     */
    public function column_locations($cbx) {
        $output = $cbx->get_locationnames();
        if ($cbx->get_cbx_id()) {
        }
        return $output;
    }
    public function column_required($cbx) {
        $output = $cbx->data['required'];
        return $output;
    }
    /**
     * Prepare table list items.
     */
    public function prepare_items() {
        //$default_cbx = AGRWC_CBX::create();
        //$default_cbx->set_name( __( 'Countries not covered by your other cbxs', 'agreeme-checkbox-for-woocommerce' ) );
        $cbxs = AGRWC_CBX::get_cbxs();
        //$cbxs[] = $default_cbx;
        $this->_column_headers = array($this->get_columns(), array(), array());
        $this->items = $cbxs;
    }
    /**
     * Generate the table navigation above or below the table. No need the tablenav section.
     *
     * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
     */
    protected function display_tablenav($which) {
    }
    /**
     * Generates content for a single row of the table.
     *
     * @param AGRWC_CBX $cbx  cbx instance.
     */
    public function single_row($cbx) {
        if ($cbx->get_cbx_id()) {
            parent::single_row($cbx);
        } else {
            echo '<tr class="cbx-worldwide">';
            $this->single_row_columns($cbx);
            echo '</tr>';
        }
    }
}
