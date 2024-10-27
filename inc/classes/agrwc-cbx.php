<?php
/**
 * Represents a single checkbox
 *
 * @since   1.0.0
 * @version 1.0.5
 * @package AGRWC
 */
if (!defined('ABSPATH')) {
    exit;
}
/**
 * AGRWC_CB, The checkbox class
 */
class AGRWC_CB {
    /**
     * Checkbox data.
     *
     * @var array
     */
    public $data = array();
    protected $locationnamearr = array();
    /**
     * Constructor for Checkbox.
     *
     * @param array $data CBX attributes as array.
     */
    public function __construct($data = null) {
        $this->locationnamearr[1] = __('Products Page', 'agreeme-checkbox-for-woocommerce');
        $this->locationnamearr[2] = __('Cart Page', 'agreeme-checkbox-for-woocommerce');
        $this->locationnamearr[3] = __('Checkout Page', 'agreeme-checkbox-for-woocommerce');
		$this->locationnamearr[999] = __('', 'agreeme-checkbox-for-woocommerce');
        $this->data = wp_parse_args($data, array('cbx_id' => '', 'name' => '', 'products' => array(), 'locations' => array(), 'olocations' => array(),'clocation' => 'after_customer_notes',  'limit_categories' => array(), 'limit_products' => array(), 'limit_ordertotal' => '', 'conditionx' => '', 'value' => '1', 'label' => '','append_fee' => '0', 'required' => '1', 'reqalert' => '', 'add_fee' => 0, 'fee_text' => ''));
    }
    /**
     * Get checkbox data.
     *
     * @return array
     */
    public function get_data() {
        return $this->data;
    }
    /**
     * Gets a prop for a getter method.
     *
     * @since 1.7.9
     * @param  string $prop Name of prop to get.
     * @return mixed
     */
    protected function get_prop($prop) {
        return isset($this->data[$prop]) ? $this->data[$prop] : false;
    }
    /**
     * Sets a prop for a setter method.
     *
     * @since 1.8.0
     * @param string $prop Name of prop to set.
     * @param mixed  $value Value to set.
     */
    protected function set_prop($prop, $value) {
        if (isset($this->data[$prop])) {
            $this->data[$prop] = $value;
        }
    }
    /**
     * Set checkbox id.
     *
     * @param string $id Checkbox ID.
     */
    public function set_id($id) {
        $this->set_prop('cbx_id', $id);
    }
    /**
     * Get checkbox id.
     *
     * @return string
     */
    public function get_id() {
        return $this->get_prop('cbx_id');
    }
    /**
     * Set checkbox id.
     *
     * @param string $id Checkbox ID.
     */
    public function set_cbx_id($id) {
        $this->set_id($id);
    }
    /**
     * Get checkbox id.
     *
     * @return string
     */
    public function get_cbx_id() {
        return $this->get_id();
    }
    /**
     * Get checkbox name.
     *
     * @return string
     */
    public function get_name() {
        return $this->get_prop('name');
    }
    /**
     * Set the checkbox name.
     *
     * @param string $name Checkbox name.
     */
    public function set_name($name) {
        $this->set_prop('name', $name);
    }
    public function get_label() {
		
			
						$feetitle='';	if($this->get_prop('append_fee')!= 'no' && $this->get_prop('append_fee')!=0 ){
							$fee_amount = $this->get_add_fee();
						    $feetitle="- ".wc_price($fee_amount); }
							
							
        return $this->get_prop('label'). " ".$feetitle;
    }
    /**
     * Set the checkbox name.
     *
     * @param string $name Checkbox name.
     */
    public function set_label($label) {
        $this->set_prop('label', $label);
    }
    public function get_required() {
        return $this->get_prop('required');
    }
    /**
     * Set the checkbox name.
     *
     * @param string $name Checkbox name.
     */
    public function set_required($required) {
        $this->set_prop('required', $required);
    }
    public function get_reqalert() {
        return $this->get_prop('reqalert');
    }
	
	
	  public function set_append_fee($appfee) {
        $this->set_prop('append_fee', $appfee);
    }
    public function get_append_fee() {
        return $this->get_prop('append_fee');
    }
	
	
    /**
     * Set the Required alert message.
     *
     * @param string $reqalert as string.
     */
    public function set_reqalert($reqalert) {
        $this->set_prop('reqalert', $reqalert);
    }
    public function get_add_fee() {
        return $this->get_prop('add_fee');
    }
    /**
     * Set the Required alert message.
     *
     * @param string $reqalert as string.
     */
    public function set_add_fee($fee) {
        $this->set_prop('add_fee',  (float)$fee);
    }
    public function get_fee_text() {
        return $this->get_prop('fee_text');
    }
    /**
     * Set the Required alert message.
     *
     * @param string $reqalert as string.
     */
    public function set_fee_text($feetext) {
        $this->set_prop('fee_text', $feetext);
    }
    /**
     * Get locations.
     *
     * @return array
     */
    public function get_locations() {
        return $this->get_prop('locations');
    }
    public function get_locationnames() {
        $location_name = '';
        foreach ($this->get_prop('locations') as $key) {
            $location_name.= $this->locationnamearr[trim($key) ] . ", ";
        }
        return trim($location_name, ", ");
    }
	
	    public function get_clocation() {
        return $this->get_prop('clocation');
    }
    public function set_clocation($clocation) {
     
        return $this->set_prop('clocation', $clocation);
    }
    /**
     * Get locations.
     *
     * @return array
     */
    public function set_locations($locations) {
        if (!$locations) $locations = array();
        return $this->set_prop('locations', $locations);
    }
    public function get_olocations() {
        return $this->get_prop('olocations');
    }
	
	
	
		   public function get_productname() {
        $pidarr= $this->get_prop('limit_products');
		$pname='';
		if(is_array($pidarr))
		{
			foreach($pidarr as $pid)
			{
			$product = wc_get_product( $pid );

			$pname.= $product->get_title().",";
			}
		}
		return trim($pname,",");
    }
	
	
    /**
     * Get locations.
     *
     * @return array
     */
    public function set_olocations($locations) {
        if (!$locations) $locations = array();
        return $this->set_prop('olocations', $locations);
    }
    /**
     * Get checkbox limit products.
     *
     * @return string
     */
    public function get_limit_products() {
        return $this->get_prop('limit_products');
    }
    /**
     * Set the checkbox limit categories.
     *
     * @param string $categories.
     */
    public function set_limit_categories($categories) {
        $this->set_prop('limit_categories', $categories);
    }
    public function get_limit_categories() {
        return $this->get_prop('limit_categories');
    }
    public function set_limit_ordertotal($products) {
        $this->set_prop('limit_ordertotal',  (float)$products);
    }
    public function get_limit_ordertotal() {
        return $this->get_prop('limit_ordertotal');
    }
    public function get_conditionx() {
        return $this->get_prop('conditionx');
    }
    public function set_conditionx($conditionx) {
        $this->set_prop('conditionx', $conditionx);
    }
    /**
     * Set the checkbox limit to products.
     *
     * @param array $products.
     */
    public function set_limit_products($products) {
        $this->set_prop('limit_products', $products);
    }
   
}
