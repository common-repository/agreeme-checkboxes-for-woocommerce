<?php
/**
 * Agree Checkboxes admin add/edit view
 *
 * @package agrwc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



?>

<div class="settings-panel agrwc-cbx-settings">

	<h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=agrwc-cbx&section=cbxs' ) ); ?>"><?php esc_html_e( 'Checkboxes', 'agreeme-checkbox-for-woocommerce' ); ?></a> &gt;
		<span class="agrwc-cbx-name"><?php echo esc_html( $cbx->get_name() ? $cbx->get_name() : __( 'Checkbox', 'agreeme-checkbox-for-woocommerce' ) ); ?></span>
	</h2>

	<table class="form-table">

		<!-- Name -->
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Checkbox Name', 'agreeme-checkbox-for-woocommerce' ); ?>*</label>
				<?php echo wp_kses_post( wc_help_tip( __( 'This is the name of the checkbox for your reference.', 'agreeme-checkbox-for-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
					<input name="name" id="name" type="text" value="<?php echo esc_attr( $cbx->get_name() ); ?>"/>
				</td>
		</tr>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="label"><?php esc_html_e( 'Checkbox Label', 'agreeme-checkbox-for-woocommerce' ); ?>*</label>
				<?php echo wp_kses_post( wc_help_tip( __( 'This is the label text of the checkbox , supports links.', 'agreeme-checkbox-for-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
					<input name="label" id="label" type="text" value="<?php echo esc_attr( $cbx->get_label() ); ?>"/>
				</td>
		</tr>
		
		

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Locations', 'agreeme-checkbox-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'These are the Locations where the checkbox should be shown.', 'agreeme-checkbox-for-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text"><input type="hidden" name="locations[]" value="999">
					<input name="locations[]" id="location1" type="checkbox" value="1" <?php if(in_array('1',$cbx->data['locations']))echo "checked"; ?> />Product Page
					<input name="locations[]" id="location2" type="checkbox" value="2" <?php if(in_array('2',$cbx->data['locations']))echo "checked"; ?> />Cart Page
					<input name="locations[]" id="location3" type="checkbox" value="3" <?php if(in_array('3',$cbx->data['locations']))echo "checked"; ?> />Checkout Page
				</td>
		</tr>
		
		
		<tr valign="top" id="clocationtr">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Checkout Locations', 'agreeme-checkbox-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'These are the Checkout Page locations where the checkbox should be shown.', 'agreeme-checkbox-for-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
					<input name="clocation" id="clocation1" type="radio" value="before_customer_detail" <?php if(('before_customer_detail'==$cbx->data['clocation']))echo "checked"; ?> />Before customer detail
					<input name="clocation" id="clocation2" type="radio" value="after_customer_detail" <?php if(('after_customer_detail'==$cbx->data['clocation']))echo "checked"; ?> />After Customer detail
					<input name="clocation" id="clocation3" type="radio" value="after_customer_notes" <?php if(('after_customer_notes'==$cbx->data['clocation']))echo "checked"; ?> />After Customer Notes
				<br><input name="clocation" id="clocation4" type="radio" value="after_billing_form" <?php if(('after_billing_form'==$cbx->data['clocation']))echo "checked"; ?> />After Billing Form
					<input name="clocation" id="clocation5" type="radio" value="before_terms_conditions" <?php if(('before_terms_conditions'==$cbx->data['clocation']))echo "checked"; ?> />Before Terms & Conditions
						<input name="clocation" id="clocation6" type="radio" value="after_terms_conditions" <?php if(('after_terms_conditions'==$cbx->data['clocation']))echo "checked"; ?> />After Terms & Conditions
						
									<input name="clocation" id="clocation6" type="radio" value="before_submit" <?php if(('before_submit'==$cbx->data['clocation']))echo "checked"; ?> />Before Submit
				</td>
		</tr>
		
		
	
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Required', 'agreeme-checkbox-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'If its required to be checked to proceed further', 'agreeme-checkbox-for-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
					<input name="required" id="required" type="checkbox" value="1" <?php if('yes'==$cbx->data['required'])echo "checked"; ?> />Yes/No

				</td>
		</tr>

	<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="label"><?php esc_html_e( 'Required Alert Message', 'agreeme-checkbox-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'This is the alert message if checkbox is not checked and proceed to "Add-to-Cart/Checkout"', 'agreeme-checkbox-for-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
					<input name="reqalert" id="reqalert" type="text" value="<?php echo esc_attr( $cbx->get_reqalert() ); ?>"/>
				</td>
		</tr>

		
<?php 
    $product_ids = $cbx->data['limit_products'];
    if( empty($product_ids) )
        $product_ids = array();
  
		?>
				<tr valign="top">
			<th scope="row" class="titledesc">
                <label for="limit_products"><?php _e( 'Limit to Products', 'agreeme-checkbox-for-woocommerce' ); ?></label>
				</th>
				<td class="forminp forminp-text">
                <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="limit_products" name="limit_products[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-list="products" data-field="autocomplete" data-action="woocommerce_json_search_products">
                    <?php
                        foreach ( $product_ids as $product_id ) {
                            $product = wc_get_product( $product_id );
                            if ( is_object( $product ) ) {
                                echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                            }
                        }
                    ?>
                </select> <?php echo wc_help_tip( __( 'Limit display to the selected products.', 'agreeme-checkbox-for-woocommerce' ) ); ?>
            </td> 
			
			</tr>

	


<?php 
    $category_ids = $cbx->data['limit_categories'];
	
	
    if( empty($category_ids) )
        $category_ids = array();
  
		?>

		
				<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Limit to Categories', 'agreeme-checkbox-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'Limit to these categories, add one or more categories', 'agreeme-checkbox-for-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
				<?php /* ?>	<input name="limit_categories" id="limit_categories" type="text" value="<?php echo esc_attr( $cbx->data['limit_categories'] ); ?>"/> <?php */ ?>
					
						<select class="wc-category-search" name="limit_categories[]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Limit to categories', 'agreeme-checkbox-for-woocommerce' ); ?>" data-allow_clear="true" data-hide_empty="false">
						
						
				<?php
				
				      foreach ( $category_ids as $category_id ) {
                            $current_category = get_term_by( 'slug', $category_id ,'product_cat');
				
				if (  $current_category ) : ?>
					<option value="<?php echo esc_attr( $current_category->slug ); ?>" selected="selected"><?php echo esc_html( htmlspecialchars( wp_kses_post( $current_category->name ) ) ); ?></option>
				<?php endif;


					  }
					  ?>
			</select>

				</td>
		</tr>
		
		
			<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Limit to Order Total', 'agreeme-checkbox-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'Limit to  order total < > = XXX in cart and checkout pages', 'agreeme-checkbox-for-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
				<select  name="conditionx" id="conditionx" style="max-width:60px;">
				<option value=''></option>
				<option value='<' <?php if($cbx->data['conditionx']=='<')echo "selected"; ?> > < </option>
				<option value='>' <?php if($cbx->data['conditionx']=='>')echo "selected"; ?> > > </option>
				
				</select>	<input name="limit_ordertotal" id="limit_ordertotal" type="text" value="<?php echo esc_attr( $cbx->data['limit_ordertotal'] ); ?>"/>
				</td>
		</tr>

			<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Add Fee & Fee Label', 'agreeme-checkbox-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'Add extra fees to the cart order totals when checked, taxable with standard tax rate & corresponding Fee Text' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
				<input name="add_fee" id="add_fee" type="text" value="<?php echo esc_attr( $cbx->data['add_fee'] ); ?>" style="width:100px;" placeholder="0"/>
				<input name="fee_text" id="add_fee" type="text" value="<?php echo esc_attr( $cbx->data['fee_text'] ); ?>" placeholder="Fee Label"/>
				<input name="append_fee" id="append_fee" type="checkbox" value="1" <?php if('yes'==$cbx->data['append_fee'])echo "checked"; ?> />Append Fee Value to Label

				</td>
		</tr>
		
		
			<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Display in Order Details', 'agreeme-checkbox-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'This is where to show the entries', 'agreeme-checkbox-for-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
				
				<input type="hidden" name="olocations[]" value="999">
					<input name="olocations[]" id="olocation1" type="checkbox" value="1" <?php if(in_array('1',$cbx->data['olocations']))echo "checked"; ?> /><?php echo _e( 'Order Details', 'agreeme-checkbox-for-woocommerce' );?>
					<input name="olocations[]" id="olocation2" type="checkbox" value="2" <?php if(in_array('2',$cbx->data['olocations']))echo "checked"; ?> /><?php echo _e( 'Order Emails', 'agreeme-checkbox-for-woocommerce' );?>
					<?php /* ?> <input name="olocations[]" id="olocation3" type="checkbox" value="3" <?php if(in_array('3',$cbx->data['olocations']))echo "checked"; ?> />Invoice
					<?php */ ?>
				</td>
		</tr>

	
		


	</table>


	<input type="hidden" name="page" value="wc-settings" />
	<input type="hidden" name="tab" value="agrwc-cbx" />
	<input type="hidden" name="section" value="cbxs" />

	<p class="submit">
		<?php submit_button( __( 'Save Changes', 'agreeme-checkbox-for-woocommerce' ), 'primary', 'save', false ); ?>
		<?php if ( $cbx->get_cbx_id() ) : ?>
		<a class="agrwc-delete-cbx" style="color: #a00; text-decoration: none; margin-left: 10px;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'delete_cbx' => $cbx->get_cbx_id() ), admin_url( 'admin.php?page=wc-settings&tab=agrwc_cbx&section=cbxs' ) ), 'agreeme-checkbox-for-woocommerce' ) ); ?>"><?php esc_html_e( 'Delete checkbox', 'agreeme-checkbox-for-woocommerce' ); ?></a>
		<?php endif; ?>
	</p>

</div>
