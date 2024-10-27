<?php
/**
 * WooCommerce AgreeMe Checkboxes settings page
 *
 * @version 1.0.2
 * @package AGRWC
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
    
}
if (!class_exists('agrwc_settings_cbx')):
    /**
     * agrwc settings_cbx Class
     */
    class agrwc_settings_cbx extends WC_Settings_Page {
        /**
         * Checkbox ID.
         *
         * @var string
         */
        protected $cbx_id;
        /**
         * Constructor.
         */
        public function __construct() {
            $this->id = 'agrwc-cbx';
            $this->label = __('AgreeMe Checkboxes', 'agreeme-checkbox-for-woocommerce');
            $this->cbx_id = empty($_GET['cbx_id']) ? false : wc_clean(wp_unslash($_GET['cbx_id'])); // phpcs:ignore WordPress.Security.NonceVerification
            $this->init_hooks();
            $this->delete_cbx();
        }
        /**
         * Init action and filters
         */
        protected function init_hooks() {
            add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_page'), 20);
            add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
            add_action('woocommerce_sections_' . $this->id, array($this, 'update_cbx_notice'), 5);
            add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
            add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
        }
        /**
         * Delete a cbx
         */
        protected function delete_cbx() {
            if (!empty($_GET['delete_cbx']) && isset($_GET['tab']) && 'agrwc-cbx' === $_GET['tab'] && isset($_GET['section']) && 'cbxs' === $_GET['section']) { // WPCS: CSRF ok.
                if (empty($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'agreeme-checkbox-for-woocommerce-delete-cbx')) { // WPCS: input var ok, sanitization ok.
                    wp_die(esc_html__('Action failed. Please refresh the page and retry.', 'agreeme-checkbox-for-woocommerce'));
                }
                $cbx = AGRWC_CBX::get_cbx_by_id(wc_clean(wp_unslash($_GET['delete_cbx'])));
                if (!$cbx) {
                    wp_die(esc_html__('Checkbox does not exist!', 'agreeme-checkbox-for-woocommerce'));
                }
                AGRWC_CBX::delete($cbx);
                WC_Admin_Settings::add_message(__('Checkbox have been deleted.', 'agreeme-checkbox-for-woocommerce'));
            }
        }
        /**
         * Checks the current section
         *
         * @param string $section String to check.
         * @return bool
         */
        protected function is_section($section) {
            global $current_section;
            return $section === $current_section;
        }
        /**
         * Get sections
         *
         * @return array
         */
        public function get_sections() {
            $sections = array('cbxs' => __('Checkboxes', 'agreeme-checkbox-for-woocommerce'), 'general' => __('General options', 'agreeme-checkbox-for-woocommerce'),);
            return $sections;
        }
        /**
         * Get settings array
         *
         * @return array
         */
        public function get_settings() {
            $settings = apply_filters('agreeme_checkbox_for_woocommerce_settings_general', array(array('title' => __('General Options', 'agreeme-checkbox-for-woocommerce'), 'type' => 'title', 'desc' => '', 'id' => 'general_options',), array('title' => __('Enable', 'agreeme-checkbox-for-woocommerce'), 'desc' => __('Enable Switch On Off all checkboxes', 'agreeme-checkbox-for-woocommerce'), 'id' => 'agrwc_enabled', 'default' => 'yes', 'type' => 'checkbox',
            // translators: HTML tags.
            'desc_tip' => __('Enable Plugin to Display the created Checkboxes at your Woocommerce pages.', 'agreeme-checkbox-for-woocommerce')), array('title' => __('Classes for Buttons to trigger required field alerts', 'agreeme-checkbox-for-woocommerce'), 'id' => 'agrwc_buttonclasses', 'type' => 'text', 'default' => '.single_add_to_cart_button,.checkout-button,#place_order',), array('title' => __('Classes for form submit to trigger required field alerts', 'agreeme-checkbox-for-woocommerce'), 'id' => 'agrwc_formclasses', 'type' => 'text', 'default' => '.cart,.checkout',), array('title' => __('Required field alert text message'), 'id' => 'agrwc_alertmsg', 'type' => 'text', 'default' => 'You need to check and agree to our terms and conditions',), array('type' => 'sectionend', 'id' => 'general_options',),));
            return $settings;
        }
        /**
         * Output the settings
         */
        public function output() {
            ob_start();
            if ($this->is_section('cbxs') || $this->is_section('')) {
                $this->output_cbx_screen();
             }elseif ($this->is_section('general')) {
                $settings = $this->get_settings();
                WC_Admin_Settings::output_fields($settings);
            }
            $output = ob_get_clean();
            echo $output;
        }
        /**
         * Save settings
         */
        public function save() {
            if ($this->is_section('cbxs') && $this->cbx_id) {
                $this->save_cbx();
            } elseif ($this->is_section('license') && class_exists('agrwc_License_Settings')) {
				//coming in next version
                WCPBC_License_Settings::save_fields();
            } elseif (!$this->is_section('cbxs')) {
                // Save General settings.
                $settings = $this->get_settings();
                //this will update the woo options..
                WC_Admin_Settings::save_fields($settings);
            }
        }
        /**
         * Handles output of the Checkbox page in admin.
         */
        protected function output_cbx_screen() {
            global $hide_save_button;
            $hide_save_button = true; // @codingStandardsIgnoreLine
            if ($this->cbx_id) {
                // Single cbx screen.
                if ('new' === $this->cbx_id) {
                    $cbx = AGRWC_CBX::create();
                } else {
                    $cbx = AGRWC_CBX::get_cbx_by_id($this->cbx_id);
                }
                if (!$cbx) {
                    wp_die(esc_html__('Checkbox does not exist!', 'agreeme-checkbox-for-woocommerce'));
                }
                include dirname(__FILE__) . '/view/admin-page-cbx.php';
            } else {
                // Checkbox list table.
                include_once AGR_WC::plugin_path() . '/inc/classes/agrwc-cbx-list-table.php';
                echo '<h3>' . esc_html__('AgreeMe Checkboxes', 'agreeme-checkbox-for-woocommerce') . ' <a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=agrwc-cbx&section=cbxs&cbx_id=new')) . '" class="add-new-h2">' . esc_html__('Add New', 'agreeme-checkbox-for-woocommerce') . '</a></h3>';
                echo '<p>' . esc_html__('These are Checkboxes to which you want your customers to notify/check during their checkout process.', 'agreeme-checkbox-for-woocommerce') . '</p>';
                $table_list = new AGRWC_Cbx_List_Table();
                $table_list->prepare_items();
                $table_list->views();
                $table_list->display();
            }
        }
        /**
         * Save a Checkbox from the $_POST array.
         */
        protected function save_cbx() {
            do_action('agrwc_before_save_cbx');
            $postdata = wc_clean(wp_unslash($_POST)); // WPCS: CSRF ok.
			
            $allowed_html = array('a' => array('href' => array(),), 'br' => array(),);
            //clean the post...with only required values...
            $postdata['label'] = wp_kses($_POST['label'], $allowed_html);
            $postdata['label'] = wp_unslash($postdata['label']); //allow links in label
            $postdata['required'] = isset($postdata['required']) ? 'yes' : 'no';
			 $postdata['append_fee'] = isset($postdata['append_fee']) ? 'yes' : 'no';
            if (!isset($postdata['limit_products'])) {
                $postdata['limit_products'] = array();
            }
            if (!isset($postdata['limit_categories'])) {
                $postdata['limit_categories'] = array();
            }
            $postdata['conditionx'] = html_entity_decode($postdata['conditionx']);
            if ('new' === $this->cbx_id) {
                $cbx = AGRWC_CBX::create();
            } else {
                $cbx = AGRWC_CBX::get_cbx_by_id($this->cbx_id);
            }
            if (!$cbx) {
                wp_die(esc_html__('Checkbox does not exist!', 'agreeme-checkbox-for-woocommerce'));
            }
            foreach ($postdata as $field => $value) {
                if (!isset($cbx->data[$field])) unset($postdata[$field]);
            }
            // Fields validation.
            $pass = false;
            if (empty($postdata['name'])) {
                WC_Admin_Settings::add_error(__('Checkbox name is required.', 'agreeme-checkbox-for-woocommerce'));
            } elseif (count($postdata['locations']) == 0) {
                $pass = true;
                //WC_Admin_Settings::add_error( __( 'Add at least one location to the list.', 'agreeme-checkbox-for-woocommerce' ) );
                
            } else {
                $pass = true;
            }
            if ($pass) {
                foreach ($postdata as $field => $value) {
                    if (is_callable(array($cbx, 'set_' . $field))) {
                        $cbx->{'set_' . $field}($value);
                    }
                }
                $id = AGRWC_CBX::save($cbx);
                do_action('agrwc_after_save_cbx', $id);
                wp_safe_redirect(admin_url('admin.php?page=wc-settings&tab=agrwc-cbx&section=cbxs&cbx_id=' . $id . '&updated=1'));
            }
        }
        /**
         * Output the cbx update notice
         */
        public function update_cbx_notice() {
            if ($this->is_section('cbxs') && !empty($_GET['updated'])) { // WPCS: CSRF ok.
                
?>
			<div id="message" class="updated inline">
				<p><strong><?php esc_html_e('Checkbox updated successfully.', 'agreeme-checkbox-for-woocommerce'); ?></strong></p>
				<p>
					<a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=agrwc-cbx&section=cbxs')); ?>">&larr; <?php esc_html_e('Back to Checkboxs', 'agreeme-checkbox-for-woocommerce'); ?></a>
					<a style="margin-left:15px;" href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=agrwc-cbx&section=cbxs&cbx_id=new')); ?>"><?php esc_html_e('Add a new checkbox', 'agreeme-checkbox-for-woocommerce'); ?></a>
				</p>
			</div>
				<?php
            }
        }
    }
endif;
return new agrwc_settings_cbx();
