<?php
/**
 * Agreeme checkboxes for WooCommerce - Frontend Class
 *
 * @version 1.0.4
 * @since   1.0.0
 * @author  Amin Yasser.
 */
use Automattic\WooCommerce\Utilities\OrderUtil; //HPOS

if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!class_exists('agrwc_Frontend')):
    class agrwc_Frontend {
        /**
         * Constructor.
         *
         * @version 1.4.0
         * @since   1.0.0
         */
		 
        public $sdata = array();
public $checkoutpage_locations=array();
        function __construct() {
            //check if enabled.. if not skip frontend...
            $enabled = get_option('agrwc_enabled');
            if (!$enabled || $enabled == "no") return false;
   

			
			$this->checkoutpage_locations['after_customer_notes']='woocommerce_after_order_notes';
			$this->checkoutpage_locations['before_customer_detail']='woocommerce_checkout_before_customer_details';
			$this->checkoutpage_locations['after_customer_detail']='woocommerce_checkout_after_customer_details';
			$this->checkoutpage_locations['after_billing_form']='woocommerce_after_checkout_billing_form';
			$this->checkoutpage_locations['before_terms_conditions']='woocommerce_checkout_before_terms_conditions';
			$this->checkoutpage_locations['after_terms_conditions']='woocommerce_checkout_after_terms_conditions';
			$this->checkoutpage_locations['before_submit']='woocommerce_review_order_before_submit';
		//	add_action( 'init',  array($this, 'xxxxxxxxxx_init_session'), PHP_INT_MAX); 
			
            add_action('woocommerce_before_add_to_cart_button', array($this, 'addto_product_page'), PHP_INT_MAX);
            add_filter('woocommerce_add_to_cart_validation', array($this, 'addto_cart_validation'), PHP_INT_MAX);
            add_action('woocommerce_cart_totals_after_order_total', array($this, 'addto_cart_page'), PHP_INT_MAX);
            add_action('woocommerce_after_checkout_validation', array($this, 'validate_checkout'), PHP_INT_MAX, 2);
            add_action('woocommerce_after_order_notes', array($this, 'addto_checkout_page'), PHP_INT_MAX);
			add_action('woocommerce_checkout_before_customer_details', array($this, 'addto_checkout_page'), PHP_INT_MAX);
			add_action('woocommerce_checkout_after_customer_details', array($this, 'addto_checkout_page'), PHP_INT_MAX);
			add_action('woocommerce_after_checkout_billing_form', array($this, 'addto_checkout_page'), PHP_INT_MAX);
			add_action('woocommerce_checkout_before_terms_conditions', array($this, 'addto_checkout_page'), PHP_INT_MAX);
			add_action('woocommerce_checkout_after_terms_conditions', array($this, 'addto_checkout_page'), PHP_INT_MAX);
			add_action('woocommerce_review_order_before_submit', array($this, 'addto_checkout_page'), PHP_INT_MAX);									
			add_action('woocommerce_thankyou', array($this, 'order_completed'), PHP_INT_MAX);
			add_action('woocommerce_checkout_update_order_meta', array($this, 'update_agree_checkbox_fields_order_meta'));
            add_action('woocommerce_cart_calculate_fees', array($this, 'add_fees'), PHP_INT_MAX);
		    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'), PHP_INT_MAX);	
			add_action( 'woocommerce_cart_item_removed', array($this, 'remove_from_cart'), PHP_INT_MAX,2);
			add_action('wp_ajax_agrwc_post', array($this, 'agrwc_post'));
            add_action('wp_ajax_nopriv_agrwc_post', array($this, 'agrwc_post'));
            add_action('wp_footer', array($this, 'load_jsscripts'));
            ///	$this->load_jsscripts();
        
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'add_agrwc_meta_admin_order' ), PHP_INT_MAX );
		
		add_action( 'woocommerce_email_after_order_table',                 array( $this, 'add_agrwc_meta_to_emails' ), PHP_INT_MAX );
	
        }


        public function admin_enqueue($hook) {
            // Only add to the edit.php admin page.
            // See WP docs.
			
            wp_enqueue_script('agrwc-jsscript-backend', /* Handle/Name */
            AGR_WC::plugin_url() . '/js/agrwc-cbx-backend.js', /* Path to the plugin/assets folder */
            //array('jquery', 'xml2json', 'json2xml'), /* Script Dependencies */
            array('jquery'), /* Script Dependencies */
            null, /* null is any version, but could be the specific version of jquery if required */
            true
            /* if true=add to footer, false=add to header */
            );
        }
		
		
        /**
         * Submit checkbox data in cart page, Proceed to Checkout button link click : ajax request.
         *
         * @todo: may need validations for required checkboxes
         */
        public function order_completed($order_id) {
			$cbxs = AGRWC_CBX::get_cbxs();
				 if (!isset(WC()->session)) { return false;}	
	foreach ($cbxs as $id => $cbx) {
		WC()->session->__unset( 'agrwc-'.$id );
		
	}
        }
        public function agrwc_post() {
            if (isset($_POST['action']) == 'agrwc_post') {
	
                $cbxs = AGRWC_CBX::get_cbxs();
                //loop through all...
                foreach ($cbxs as $id => $cbx) {
					
                    if (isset($_POST[$id]))	WC()->session->__unset( 'agrwc-'.$id );
                    if (isset(WC()->session) && isset($_POST[$id]) && $_POST[$id]) {
						WC()->session->set('agrwc-'.$id,(int)$_REQUEST[$id]);
                       // $_SESSION['agrwc'][$id] = (int)$_POST[$id]; //checkbox if checked should be 1 
                    }
                }
                echo json_encode(array("success" => "1"));
                die();
            }
        }
        public function validate_checkout($data, $errors) {
            $passed = true;
            //get the default alert message..
            $alertmsg = get_option('agrwc_alertmsg');
            //loop through all required applicable checkboxes...
            //get the checkboxes as array of cbx objects
            $cbxs = AGRWC_CBX::get_cbxs();
			
	
            //loop through all...
            foreach ($cbxs as $id => $cbx) {
				
				
                //get location of each...and proceed further if matches
                $location_arr = $cbx->get_locations();
                if (in_array(3, $location_arr)) //product page addtocart button
                {
                    //get the product id match if not return...
                    $l_products = $cbx->get_limit_products();
					
					          $l_cats = $cbx->get_limit_categories();
							  
							  
                    if ($cbx->get_reqalert()) $alertmsg = $cbx->get_reqalert();
                    //$l_products=	explode(",",$cbx->get_limit_products());
                    // Getting cart product ids
                    $match_product = false;
                    if (is_array($l_products) && count($l_products)) {
                        foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                            if (in_array($values['product_id'], $l_products)) {
                                $match_product = true;
                            }
                        }
                    } else $match_product = true;
					
				
						
								       //Validate for limiting to specific products with specific cats in cart
                    if (is_array($l_cats) && count($l_cats)) {
                  
                        foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                            if (has_term($l_cats, 'product_cat', $values['product_id'])) {
                                $match_product = true;
                                break;
                            }
                        }
                    }else $match_product = true;
					
					
				
					
                    if (($match_product) && ($cbx->get_required() == 'yes') && (!isset($_REQUEST[$id]) ) && isset($_REQUEST[$id."_"] )) {
					
						
                        $errors->add('notes', $alertmsg);
                        $passed = false;
                    }
					
					if(isset(WC()->session)){
					WC()->session->set('agrwc-'.$id,(int)$_REQUEST[$id]);
					}
                  //  $_SESSION['agrwc'][$id] = (int)$_REQUEST[$id]; //store in session.
                    
                }
            }
            return $passed;
        }
        public function load_jsscripts() {
            $settings = array();
            //get the general options
            wp_enqueue_script('agrwc-jsscript', /* Handle/Name */
            AGR_WC::plugin_url() . '/js/agrwc-cbx.js', /* Path to the plugin/assets folder */
            //array('jquery', 'xml2json', 'json2xml'), /* Script Dependencies */
            array('jquery'), /* Script Dependencies */
            null, /* null is any version, but could be the specific version of jquery if required */
            true
            /* if true=add to footer, false=add to header */
            );
            $settings = $this->sdata;
            //get these options
            $settings['selFTags'] = get_option('agrwc_formclasses');
            $settings['selTags'] = get_option('agrwc_buttonclasses');
            $settings['alertText'] = get_option('agrwc_alertmsg');
            $settings['ajURL'] = admin_url('admin-ajax.php');
            wp_localize_script('agrwc-jsscript', 'AGRWC_VARS', $settings);
        }
        public function addto_cart_validation() {
            $passed = true;
            //loop through all required applicable checkboxes...
            //get the checkboxes as array of cbx objects
            $cbxs = AGRWC_CBX::get_cbxs();
            //loop through all...
			

            foreach ($cbxs as $id => $cbx) {
                //get location of each...and proceed further if matches
                $location_arr = $cbx->get_locations();
                if (in_array(1, $location_arr)) //product page addtocart button
                {
                    //get the product id match if not return...
                    $l_products = $cbx->get_limit_products();
                    //$l_products=	explode(",",$cbx->get_limit_products());
                    // Getting cart product ids
                    $match_product = false;
                    if (is_array($l_products) && count($l_products)) {
						
					
						
                        if (in_array($_REQUEST['add-to-cart'], $l_products)) {
                            if (($cbx->get_required() == 'yes') && !isset($_REQUEST[$id])) {
                                wc_add_notice( __( 'Some required fields are missing', 'woocommerce' ), 'error' );
                               $passed = false;  
                            }
                        }
                    }
                    if (isset(WC()->session) && isset($_REQUEST[$id]) && $_REQUEST['add-to-cart']) { 
					WC()->session->set('agrwc-'.$id,(int)$_REQUEST[$id]);
					//$_SESSION['agrwc'][$id] = (int)$_REQUEST[$id]; //store in session.
					
					
					}
                    
                }
            }
            return $passed;
        }
		
		        public function remove_from_cart( $cart_item_key, $cart ) {
					   $line_item = $cart->removed_cart_contents[ $cart_item_key ];
    $product_id = $line_item[ 'product_id' ];


            $passed = true;
            //loop through all required applicable checkboxes...
            //get the checkboxes as array of cbx objects
            $cbxs = AGRWC_CBX::get_cbxs();
            //loop through all...
            foreach ($cbxs as $id => $cbx) {
			
                //get location of each...and proceed further if matches
                $location_arr = $cbx->get_locations();
				
	
                if (in_array(2, $location_arr)) //product page addtocart button
                {
					
					
                    //get the product id match if not return...
                    $l_products = $cbx->get_limit_products();
                    //$l_products=	explode(",",$cbx->get_limit_products());
                    // Getting cart product ids
                    $match_product = false;
					
					
                    if (is_array($l_products) && count($l_products)) {
                        if (isset(WC()->session) && in_array($product_id, $l_products)) {
								WC()->session->__unset( 'agrwc-'.$id );
                     // if(isset($_SESSION['agrwc'][$id]))  unset($_SESSION['agrwc'][$id]);
                        }
                    }
                   
                }
            }
			
		
            return $passed;
        }
		
		
        public function addto_product_page() {
            //get the checkboxes as array of cbx objects
            $cbxs = AGRWC_CBX::get_cbxs();
            //loop through all...
            foreach ($cbxs as $id => $cbx) {
                //get location of each...and proceed further if matches
                $location_arr = $cbx->get_locations();
                if (in_array(1, $location_arr)) //product page addtocart button
                {
                    //get the product id match if not return...
                    global $product;
                    $pid = $product->get_id();
                    $catidsarr = $product->get_category_ids();
                    $catids = array();
                    $l_products = $cbx->get_limit_products();
                    $l_cats = $cbx->get_limit_categories();
                    foreach ($catidsarr as $cid) {
                        $cc = get_term_by('id', $cid, 'product_cat', 'ARRAY_A');
                        if (isset($cc['slug'])) $catids[] = $cc['slug'];
                    }
                    $match_product = true;
                    //$l_products=	explode(",",$cbx->get_limit_products());
                    // Getting cart product ids
                    if (is_array($l_products) && count($l_products)) {
                        $match_product = false;
                        //get the current product id...in product page..
                        if (in_array($pid, $l_products)) $match_product = true;
                    }
                    if (is_array($l_cats) && count($l_cats)) {
                        $match_product = false;
                        foreach ($catids as $cid) {
                            //get the current product id...in product page..
                            if (in_array($cid, $l_cats)) {
                                $match_product = true;
                                break;
                            }
                        }
                    }
                    if ($cbx->get_required() == 'yes') {
                        $req = true;
                        $reqclass = " required ";
                    } else {
                        $req = false;
                        $reqclass = '';
                    };
					
					if (isset(WC()->session) && WC()->session->get( 'agrwc-'.$id ))$checked = 1;
                   // if (isset($_SESSION[$id])) $checked = 1;
                    else $checked = 0;
                    if ($match_product) {
                        woocommerce_form_field($id, array('type' => 'checkbox', 'class' => array('form-row privacy', $reqclass), 'label_class' => array('woocommerce-form__label checkbox'), 'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox agrwc-cbx', $reqclass), 'required' => $req, 'label' => $cbx->get_label(),), $checked);
			
                        $this->sdata[$id] = esc_js($cbx->get_reqalert());
						
						
                    }
                }
            }
        }
        public function addto_cart_page() {
            //get the checkboxes as array of cbx objects
            $cbxs = AGRWC_CBX::get_cbxs();
            //loop through all the checkboxes...
			

            foreach ($cbxs as $id => $cbx) {
                //get location of each...and proceed further if matches
                $location_arr = $cbx->get_locations();
                if (in_array(2, $location_arr) || in_array(1, $location_arr)) //product page addtocart button
                {
                    $l_products = $cbx->get_limit_products();
                    $l_cats = $cbx->get_limit_categories();
                    //$l_products=	explode(",",$cbx->get_limit_products());
                    // Getting cart product ids
                    $match_product = true;
                    //Validate for limiting to specific products in cart
                    if (is_array($l_products) && count($l_products)) {
                        $match_product = false;
                        $products = array();
                        foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                            if (in_array($values['product_id'], $l_products)) {
                                $match_product = true;
                                break;
                            }
                        }
                    }
                    //Validate for limiting to specific products with specific cats in cart
                    if (is_array($l_cats) && count($l_cats)) {
                        $match_product = false;
                        foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                            if (has_term($l_cats, 'product_cat', $values['product_id'])) {
                                $match_product = true;
                                break;
                            }
                        }
                    }
                    //if order total condition added, validate that also..
                    $l_ordertotal = $cbx->get_limit_ordertotal();
                    if ($l_ordertotal) {
                        $conditionx = $cbx->get_conditionx();
                        $match_product = false;
                        $ordertotal = WC()->cart->get_cart_contents_total();
                        switch ($conditionx) {
                            case "<":
                                if ($ordertotal < $l_ordertotal) $match_product = true;
                                break;
                            case ">":
                                if ($ordertotal < $l_ordertotal) $match_product = true;
                                break;
                            }
                        }
                        if ($match_product) {
                            if ($cbx->get_required() == 'yes') {
                                $req = true;
                                $reqclass = " required ";
                            } else {
                                $req = false;
                                $reqclass = '';
                            };
                          	if (WC()->session->get( 'agrwc-'.$id ))$checked = 1;
                   // if (isset($_SESSION[$id])) $checked = 1;
                    else $checked = 0;
                            woocommerce_form_field($id, array('type' => 'checkbox', 'class' => array('form-row privacy', $reqclass), 'label_class' => array('woocommerce-form__label checkbox'), 'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox agrwc-cbx', $reqclass), 'required' => $req, 'label' => $cbx->get_label(),), $checked);
                            $this->sdata[$id] = esc_js($cbx->get_reqalert());
                        }else
						{
					if (WC()->session->get( 'agrwc-'.$id ))	WC()->session->__unset( 'agrwc-'.$id );
							
						}
                    }else
					{
						
						if (WC()->session->get( 'agrwc-'.$id ))	WC()->session->__unset( 'agrwc-'.$id );	
					
					}
                }
        }
        public function addto_checkout_page() {
            //get the checkboxes as array of cbx objects
			
			 $called_action_hook = current_filter();
			
			 	$checkoutpage_locations['after_customer_notes']='woocommerce_after_order_notes';
			$checkoutpage_locations['before_customer_detail']='woocommerce_checkout_before_customer_details';
			$checkoutpage_locations['after_customer_detail']='woocommerce_checkout_after_customer_details';
			$checkoutpage_locations['after_billing_form']='woocommerce_after_checkout_billing_form';
			$checkoutpage_locations['before_terms_conditions']='woocommerce_checkout_before_terms_conditions';
			$checkoutpage_locations['after_terms_conditions']='woocommerce_checkout_after_terms_conditions';
			$checkoutpage_locations['before_submit']='woocommerce_review_order_before_submit';
	
            $cbxs = AGRWC_CBX::get_cbxs();
            //loop through all...
            foreach ($cbxs as $id => $cbx) {
                //get location of each...and proceed further if matches
                $location_arr = $cbx->get_locations();
				
			
				
                if (in_array(3, $location_arr)) //product page addtocart button
                {
					 $clocation = $cbx->get_clocation();
			
			
					if($checkoutpage_locations[$clocation]==$called_action_hook)
					{
						
					
                    //get the product id match if not return...
                    //get the product id match if not return...
                    $l_products = $cbx->get_limit_products();
                    //$l_products=	explode(",",$cbx->get_limit_products());
					    $l_cats = $cbx->get_limit_categories();
                    // Getting cart product ids
                    $match_product = true;
                    if (is_array($l_products) && count($l_products)) {
                        $match_product = false;
					
						
                        foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                            if (in_array($values['product_id'], $l_products)) $match_product = true;
                        }
                    }
					
					       //Validate for limiting to specific products with specific cats in cart
                    if (is_array($l_cats) && count($l_cats)) {
                        $match_product = false;
                        foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                            if (has_term($l_cats, 'product_cat', $values['product_id'])) {
                                $match_product = true;
                                break;
                            }
                        }
                    }
					
					
					
                    //order total condition added, validate that also..
                    $l_ordertotal = $cbx->get_limit_ordertotal();
                    if ($l_ordertotal) {
						
                        $conditionx = $cbx->get_conditionx();
                        $match_product = false;
                        $ordertotal = WC()->cart->get_cart_contents_total();
						
                        switch ($conditionx) {
                            case "<":
                                if ($ordertotal < $l_ordertotal) $match_product = true;
                                break;
                            case ">":
						
                                if ($ordertotal > $l_ordertotal) $match_product = true;
                                break;
                            }
                        }
						
				
						
                        if ($match_product) {
									
                            if ($cbx->get_required() == 'yes') {
                                $req = true;
                                $reqclass = " required ";
                            } else {
                                $req = false;
                                $reqclass = '';
                            };
                            	if (WC()->session->get( 'agrwc-'.$id ))$checked = 1;
                    else $checked = 0;
						
						
						
                            woocommerce_form_field($id, array('type' => 'checkbox', 'class' => array('form-row privacy', $reqclass), 'label_class' => array('woocommerce-form__label checkbox'), 'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox agrwc-cbx', $reqclass), 'required' => $req, 'label' => $cbx->get_label(),), $checked);
										  woocommerce_form_field($id."_", array('type' => 'hidden'), '1');
                            $this->sdata[$id] = esc_js($cbx->get_reqalert());
                        }
                    }
			} }
        }
        /**
         * add_fees.
         *
         * @version 1.1.0
         * For future versions
         *
         */
        public function add_fees($cart) {
            //coming soon...
            $fees_to_add = array();
			

            //get the checkboxes as array of cbx objects
            $cbxs = AGRWC_CBX::get_cbxs();
            //loop through all...
            foreach ($cbxs as $id => $cbx) {
                //get location of each...and proceed further if matches
                $location_arr = $cbx->get_locations();
                $fee_value = $cbx->get_add_fee();
                if ((in_array(3, $location_arr) || in_array(1, $location_arr) || in_array(2, $location_arr)) && $fee_value && WC()->session->get( 'agrwc-'.$id )) //add fee only works from checkout page..
                {
					
				
                    $fee_title = $cbx->get_fee_text();
                    // Adding fee
                    $fees_to_add[] = array('name' => $fee_title, 'amount' => $fee_value, 'taxable' => (isset($taxable) ? ('yes' === $taxable) : true), 'tax_class' => 'standard',);
                }
            }
            // Add fees
            if (!empty($fees_to_add)) {
                foreach ($fees_to_add as $fee_to_add) {
                    $cart->add_fee($fee_to_add['name'], $fee_to_add['amount'], $fee_to_add['taxable'], $fee_to_add['tax_class']);
                }
            }
        }
		
		
		public function add_agrwc_meta_admin_order( $order ){


		$cbxs   = AGRWC_CBX::get_cbxs();
		$show_cbx=array();
		//loop through all...
		
		foreach($cbxs as $id=>$cbx)
		{
			$location_arr=$cbx->get_olocations();
			
		if(in_array(1,$location_arr)) //product page addtocart button
			{
$show_cbx[]=$id;
			}				
			
		}
	
?>
    <br class="clear" />
    <h4><?php echo __( 'Agree to', 'agreeme-checkbox-for-woocommerce' );?> </h4>
    <?php 
    
		 
		 
	if ( method_exists('Automattic\WooCommerce\Utilities\OrderUtil','custom_orders_table_usage_is_enabled') && OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// HPOS usage is enabled.

	  
	/////AMIN WC3 HPOS	 $meta_data_obj =	 $order->get_meta_data() ;
foreach( $order->get_meta_data() as $meta_data_obj ) {
	
	
    $meta_data_array = $meta_data_obj->get_data();
	

	   $key   = $meta_data_array['key']; // The meta key
    $value = $meta_data_array['value']; // The meta value
	
	
	
		if(strstr($key,"agrwc_"))
	{
		
			
	
		$keysarr=explode("_agrwc_",$key);
		
		if(!in_array($keysarr[1],$show_cbx))continue;
		   $allowed_html = array('a' => array('href' => array(),), 'br' => array(),);

    ?>
    <div class="custom_field">
        <p> <?php  echo esc_html($keysarr[1]) . ' : ' . wp_kses($value,$allowed_html) . '<br/>'; ?></p>
    </div>


<?php
	}
	
}

} else {

	
	
 $custom_field_value = get_post_meta( $order->get_id() );

		foreach($custom_field_value as $key=>$val)
{
	
	if(strstr($key,"agrwc_"))
	{
		$keysarr=explode("_agrwc_",$key);
		
		if(!in_array($keysarr[1],$show_cbx))continue;
		   $allowed_html = array('a' => array('href' => array(),), 'br' => array(),);

    ?>
    <div class="custom_field">
        <p> <?php  echo esc_html($keysarr[1]) . ' : ' . wp_kses($val[0],$allowed_html) . '<br/>'; ?></p>
    </div>


<?php
	}
}

}

}

public function add_agrwc_meta_to_emails( $order ){
	
	
			$cbxs   = AGRWC_CBX::get_cbxs();
		$show_cbx=array();
		//loop through all...
		
		foreach($cbxs as $id=>$cbx)
		{
			$location_arr=$cbx->get_olocations();
			
		//if(in_array(2,$location_arr)) //product page addtocart button
			//{
$show_cbx[]=$id;
			//}				
			
		}

$op ='';


	/////AMIN WC3 HPOS	 
	if ( method_exists('Automattic\WooCommerce\Utilities\OrderUtil',"custom_orders_table_usage_is_enabled") && OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// HPOS usage is enabled.


	
	$meta_data_obj =	 $order->get_meta_data() ;
foreach( $order->get_meta_data() as $meta_data_obj ) {
    $meta_data_array = $meta_data_obj->get_data();
	   $key   = $meta_data_array['key']; // The meta key
    $val = $meta_data_array['value']; // The meta value
		if(strstr($key,"agrwc_"))
	{
		$keysarr=explode("_agrwc_",$key);
		
		if(!in_array($keysarr[1],$show_cbx))continue;
		   $allowed_html = array('a' => array('href' => array(),), 'br' => array(),);

  $op .=' <div class="custom_field"><p>'. wp_kses($val,$allowed_html) . '<br/></p> </div>';

	}
	
}

	/////AMIN WC3 HPOS	 $meta_data_obj =	 $order->get_meta_data() ;
} else {
	// Traditional CPT-based orders are in use.
	
	 $custom_field_value = get_post_meta( $order->get_id() );
		
		
		foreach($custom_field_value as $key=>$val)
{

	if(strstr($key,"agrwc_"))
	{
	
		$keysarr=explode("_agrwc_",$key);
		
		if(in_array(trim($keysarr[1]),$show_cbx))
		{
    $allowed_html = array('a' => array('href' => array(),), 'br' => array(),);
	
   $op .=' <div class="custom_field"><p>'. wp_kses($val[0],$allowed_html) . '<br/></p> </div>';



		}
	}
}
}


       



if($op) echo '<br class="clear" /><h4>'.__( 'Agree to:', 'agreeme-checkbox-for-woocommerce' ).' </h4>'.$op;
   
}



        /**
         * update_custom_checkout_fields_order_meta.
         *
         * @version 1.0.2
         * @since   1.0.0
         * @todo
         */
   public     function update_agree_checkbox_fields_order_meta($order_id) {
			
			   remove_action('woocommerce_checkout_update_order_meta', array($this, 'update_agree_checkbox_fields_order_meta'));
			   
			   
            $fields_data = array();
            $val_arr[0] = AGRWC_NO;
            $val_arr[1] = AGRWC_YES;
            //get the checkboxes as array of cbx objects
            $cbxs = AGRWC_CBX::get_cbxs();
            $type = 'checkbox';
            $section = 'billing';
            //loop through all...
            $ix = 0;
			$order_updated=0;
	$order = wc_get_order( $order_id );
            foreach ($cbxs as $id => $cbx) {
				
                $ix++;
				
				$valid=WC()->session->get( 'agrwc-'.$id );
				
                if ($valid) {
					


	
			if ( method_exists('Automattic\WooCommerce\Utilities\OrderUtil',"custom_orders_table_usage_is_enabled") &&  OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// HPOS usage is enabled.



	
						//HPOS
	$order_updated=1;
	
	
		$order->update_meta_data('_agrwc_' . $id, $cbx->get_label() . " - " . $val_arr[$valid] ); ///


					}else
                    update_post_meta($order_id, '_agrwc_' . $id, $cbx->get_label() . " - " . $val_arr[(int)$valid]);
                }
				
            }
			
			
			
			if($order_updated){"Order saved"; $order->save();
			
			
			
			}
		
			
			add_action('woocommerce_checkout_update_order_meta', array($this, 'update_agree_checkbox_fields_order_meta'));
		
	
		
		
			
        }
    }
endif;
return new agrwc_Frontend();
