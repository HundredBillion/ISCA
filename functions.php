<?php
function my_theme_enqueue_styles() { 
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

//WOOCOMMERCE CUSTOM CODE////


// this reorders the check out fields in woocommerce
add_filter("woocommerce_checkout_fields", "custom_override_checkout_fields", 1);
function custom_override_checkout_fields($fields) {
    $fields['billing']['billing_first_name']['priority'] = 1;
    $fields['billing']['billing_last_name']['priority'] = 2;
    $fields['billing']['billing_email']['priority'] = 3;
    $fields['billing']['billing_phone']['priority'] = 4;

    $fields['billing']['billing_address_1']['priority'] = 6;
    $fields['billing']['billing_address_2']['priority'] = 7;
    $fields['billing']['billing_city']['priority'] = 8;
    $fields['billing']['billing_state']['priority'] = 9;
    $fields['billing']['billing_postcode']['priority'] = 10;
    $fields['billing']['billing_country']['priority'] = 11;

    $fields['shipping']['shipping_first_name']['priority'] = 1;
    $fields['shipping']['shipping_last_name']['priority'] = 2;
  
    $fields['shipping']['shipping_address_1']['priority'] = 6;
    $fields['shipping']['shipping_address_2']['priority'] = 7;
    $fields['shipping']['shipping_city']['priority'] = 8;
    $fields['shipping']['shipping_state']['priority'] = 9;
    $fields['shipping']['shipping_postcode']['priority'] = 10;
    $fields['shipping']['shipping_country']['priority'] = 11;
    
    return $fields;
}
// remove label from woocommerce forms
add_filter('woocommerce_checkout_fields','custom_wc_checkout_fields_no_label');
function custom_wc_checkout_fields_no_label($fields) {
    // loop by category
    foreach ($fields as $category => $value) {
        // loop by fields
        foreach ($fields[$category] as $field => $property) {
            // remove label property
            unset($fields[$category][$field]['label']);
        }
    }
     return $fields;
}

// add placeholder for woocommerce forms
add_filter( 'woocommerce_checkout_fields' , 'override_billing_checkout_fields', 20, 1 );
function override_billing_checkout_fields( $fields ) {
    $fields['billing']['billing_first_name']['placeholder'] = 'First Name';
    $fields['billing']['billing_last_name']['placeholder'] = 'Last Name';
    $fields['billing']['billing_email']['placeholder'] = 'Email';
    $fields['billing']['billing_address_1']['placeholder'] = 'Address 1';
    $fields['billing']['billing_address_2']['placeholder'] = 'Address 2';
    $fields['billing']['billing_city']['placeholder'] = 'City';
    $fields['billing']['billing_state']['placeholder'] = 'State';
    $fields['billing']['billing_postcode']['placeholder'] = 'Zip';
    $fields['billing']['billing_phone']['placeholder'] = 'Phone Number';

    $fields['shipping']['shipping_first_name']['placeholder'] = 'First Name';
    $fields['shipping']['shipping_last_name']['placeholder'] = 'Last Name';
  
    $fields['shipping']['shipping_address_1']['placeholder'] = 'Address 1';
    $fields['shipping']['shipping_address_2']['placeholder'] = 'Address 2';
    $fields['shipping']['shipping_city']['placeholder'] = 'City';
    $fields['shipping']['shipping_state']['placeholder'] = 'State';
    $fields['shipping']['shipping_postcode']['placeholder'] = 'Zip';

    return $fields;
}


// Create Custome Fields for WooCommerce
function isca_custom_checkout_fields($fields){
    $fields['isca_extra_fields'] = array(
            'isca_cc_name_field' => array(
                'class' => array( 'form-row-first cc-form' ),
                'type' => 'text',
                'required' => true,
                'placeholder' => __( 'Name' )
                ),
            'isca_cc_field' => array(
                'class' => array( 'form-row-last cc-form' ),
                'type' => 'number',
                'required'      => true,
                'placeholder' => __( 'Credit Card Number' )
                ),   
            'isca_cc_expiration_field' => array(
                'class' => array( 'form-row-first cc-form' ),
                'type' => 'text',
                'required'  => true,
                'placeholder' => __( 'MM/YY' )
                ),  
            'isca_cc_ccv_field' => array(
                'class' => array( 'form-row-last cc-form' ),
                'type' => 'number',
                'required'  => true,
                'placeholder' => __( 'CCV' )
                ),   
            );
    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'isca_custom_checkout_fields' );

// Add Form with custom fields to WooCommerce Checkout page
function isca_extra_checkout_fields(){
    $checkout = WC()->checkout(); ?>
    <br/>
    <div class="extra-fields">
    <h3><?php _e( 'Credit Card Information' ); ?></h3>
    <?php
       foreach ( $checkout->checkout_fields['isca_extra_fields'] as $key => $field ) : ?>
            <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
        <?php endforeach; ?>
    </div>
<?php }
add_action( 'woocommerce_after_checkout_billing_form' ,'isca_extra_checkout_fields' );

// Save Credit Card data 
function isca_save_extra_checkout_fields( $order_id, $posted ){
    // don't forget appropriate sanitization if you are using a different field type
    if( isset( $posted['isca_cc_name_field'] ) ) {
        update_post_meta( $order_id, 'isca_cc_name_field', sanitize_text_field( $posted['isca_cc_name_field'] ) );
    }
    if( isset( $posted['isca_cc_field'] ) ) {
        update_post_meta( $order_id, 'isca_cc_field', sanitize_text_field( $posted['isca_cc_field'] ) );
    }
    if( isset( $posted['isca_cc_expiration_field'] ) ) {
        update_post_meta( $order_id, 'isca_cc_expiration_field', sanitize_text_field( $posted['isca_cc_expiration_field'] ) );
    }
    if( isset( $posted['isca_cc_ccv_field'] ) ) {
        update_post_meta( $order_id, 'isca_cc_ccv_field', sanitize_text_field( $posted['isca_cc_ccv_field'] ) );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'isca_save_extra_checkout_fields', 10, 2 );


//Display Admin Custom Credit Card info
function isca_save_extra_details( $post_id, $post ){
    update_post_meta( $post_id, '_isca_cc_name_field', wc_clean( $_POST[ '_isca_cc_name_field' ] ) );
    update_post_meta( $post_id, '_isca_cc_field', wc_clean( $_POST[ 'isca_cc_field' ] ) );
    update_post_meta( $post_id, '_isca_cc_expiration_field', wc_clean( $_POST[ '_isca_cc_expiration_field' ] ) );
    update_post_meta( $post_id, '_isca_cc_ccv_field', wc_clean( $_POST[ '_isca_cc_ccv_field' ] ) );
}
add_action( 'woocommerce_process_shop_order_meta', 'isca_save_extra_details', 45, 2 );




//Add a custom fields (in an order) to the Admin emails

 add_filter( 'woocommerce_email_order_meta_fields', 'custom_woocommerce_email_order_meta_fields', 10, 3 );
 function custom_woocommerce_email_order_meta_fields( $fields, $sent_to_admin, $order ) {
    if( !$sent_to_admin ){
        return;
    }
    $fields['isca_cc_name_field'] = array(
        'label' => __( 'Credit Card Name' ),
        'value' => get_post_meta( $order->get_id(), 'isca_cc_name_field', true ),
    );
    $fields['isca_cc_field'] = array(
        'label' => __( 'Credit Card Number' ),
        'value' => get_post_meta( $order->get_id(), 'isca_cc_field', true ),
    );
    $fields['isca_cc_expiration_field'] = array(
        'label' => __( 'Expiration' ),
        'value' => get_post_meta( $order->get_id(), 'isca_cc_expiration_field', true ),
    );
    $fields['isca_cc_ccv_field'] = array(
        'label' => __( 'CCV' ),
        'value' => get_post_meta( $order->get_id(), 'isca_cc_ccv_field', true ),
    );
    return $fields;
}




// Display correct shipping rate on woocommerce checkout page
 
add_filter( 'woocommerce_package_rates', 'isca_custom_woocommerce_tiered_shipping', 10, 2 );  
function isca_custom_woocommerce_tiered_shipping( $rates, $package ) {
   
   $threshold = 100;
    
   if ( WC()->cart->subtotal > $threshold ) {
         unset( $rates["flat_rate:1"] );
   } else {
      unset( $rates["flat_rate:3"] );
   }
   
   return $rates;  
}

//Remove the shipping name from checkout field - look for : and remove that and text before it
add_filter( 'woocommerce_cart_shipping_method_full_label', 'bbloomer_remove_shipping_label', 9999, 2 );
   
function bbloomer_remove_shipping_label( $label, $method ) {
    $new_label = preg_replace( '/^.+:/', '', $label );
    return $new_label;
}

//Change Woo Commerce New Order Email for Admin to Display Customer Name
add_filter('woocommerce_email_subject_new_order', 'custom_admin_email_subject', 1, 2);    
function custom_admin_email_subject( $subject, $order ) {
    global $woocommerce;
    foreach($order->get_items() as $item_id => $item ){
        if ( has_term( 'MEC-Woo-Cat', 'product_cat' , $item->get_product_id() ) ) { 
            $subject = sprintf( 'ISCA Event Registration Confirmation' );
            break;
        }
    } 
    return $subject;
}

// Add PDFs to list of permitted mime types for file uploading renewal forms
function my_prefix_pewc_get_permitted_mimes( $permitted_mimes ) {
 // Add PDF to the list of permitted mime types
 $permitted_mimes['pdf'] = "application/pdf";
 // Remove a mime type - uncomment the line below if you wish to prevent JPGs from being uploaded
 // unset( $permitted_mimes['jpg'] );
 return $permitted_mimes;
}
add_filter( 'pewc_permitted_mimes', 'my_prefix_pewc_get_permitted_mimes' );
// Add PDF to the list of restricted filetypes
function my_prefix_pewc_protected_directory_allowed_filetypes( $restricted_filetypes ) {
 $restricted_filetypes[] = 'pdf';
 return $restricted_filetypes;
}
add_filter( 'pewc_protected_directory_allowed_filetypes', 'my_prefix_pewc_protected_directory_allowed_filetypes' );

/***********************/
/***********************/
/***********************/
function wpse_add_custom_meta_box_2() {
   add_meta_box(
       'custom_meta_box-2',       	// $id
       'Used Or Not',               // $title
       'show_custom_meta_box_2',  	// $callback
       'coupon',                 	// $page
       'normal',                  	// $context
       'high'                     	// $priority
   );
}
add_action('add_meta_boxes', 'wpse_add_custom_meta_box_2');


function show_custom_meta_box_2() {
    global $post;
    // Use nonce for verification to secure data sending
    wp_nonce_field( basename( __FILE__ ), 'wpse_our_nonce' );
	$usedornot  = get_post_meta( get_the_ID(), 'usage', true );
    ?>
    <select name="usage">
		<option value="unused" <?php if($usedornot=='unused'){echo 'selected';} ?>>Unused</option>
		<option value="used" <?php if($usedornot=='used'){echo 'selected';} ?>>Used</option>
	</select>
    <?php
	if($usedornot=='used'){
		$first_name = get_post_meta( get_the_ID(), 'first_name', true );
		$last_name = get_post_meta( get_the_ID(), 'last_name', true );
		$email = get_post_meta( get_the_ID(), 'email', true );
		$Address1 = get_post_meta( get_the_ID(), 'Address1', true );
		$Address2 = get_post_meta( get_the_ID(), 'Address2', true );
		$City = get_post_meta( get_the_ID(), 'City', true );
		$State = get_post_meta( get_the_ID(), 'State', true );
		$Zip = get_post_meta( get_the_ID(), 'Zip', true );
		$phone = get_post_meta( get_the_ID(), 'phone', true );
		$confirmation_number = get_post_meta( get_the_ID(), 'confirmation_number', true );
		$Product_description = get_post_meta( get_the_ID(), 'Product_description', true );
		$Product_sku = get_post_meta( get_the_ID(), 'Product_sku', true );
		$Order_date = get_post_meta( get_the_ID(), 'Order_date', true );
		?>
		<div class="form-row">
			<div class="form-group col-md-6">
			  <label for="first_name">First Name</label>
			  <input type="text" class="form-control" id="first_name" placeholder="First Name" value="<?php echo $first_name;?>" readonly>
			</div>
			<div class="form-group col-md-6">
			  <label for="lst_name">Last Name</label>
			  <input type="text" class="form-control" id="lst_name" placeholder="Last Name" value="<?php echo $last_name;?>" readonly>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group col-md-6">
			  <label for="inputEmail4">Email</label>
			  <input type="email" class="form-control" id="inputEmail4" placeholder="Email" value="<?php echo $email;?>" readonly>
			</div>
			<div class="form-group col-md-6">
			  <label for="inputPassword4">Phone</label>
			  <input type="text" class="form-control" id="inputPassword4" placeholder="Phone" value="<?php echo $phone;?>" readonly>
			</div>
		</div>
		<div class="form-group">
			<label for="inputAddress">Address</label>
			<input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St" value="<?php echo $Address1;?>" readonly>
		</div>
		<div class="form-group">
			<label for="inputAddress2">Address 2</label>
			<input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor" value="<?php echo $Address2;?>" readonly>
		</div>
		<div class="form-group">
			<label for="inputAddress2">City</label>
			<input type="text" class="form-control" id="city" placeholder="City" value="<?php echo $City;?>" readonly>
		</div>
		<div class="form-row">
			<div class="form-group col-md-6">
				<label for="inputState">State</label>
				<input type="text" class="form-control" id="inputState" value="<?php echo $State;?>" readonly>
			</div>
			<div class="form-group col-md-6">
			  <label for="inputZip">Zip</label>
			  <input type="text" class="form-control" id="inputZip" value="<?php echo $Zip;?>" readonly>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group col-md-6">
				<label for="confirmation_number">Confirmation Number</label>
				<input type="text" class="form-control" id="confirmation_number" value="<?php echo $confirmation_number;?>" readonly>
			</div>
			<div class="form-group col-md-6">
			  <label for="Product_description">Product Description</label>
			  <input type="text" class="form-control" id="Product_description" value="<?php echo $Product_description;?>" readonly>
			</div>
			<div class="form-group col-md-6">
			  <label for="Product_sku">Product SKU</label>
			  <input type="text" class="form-control" id="Product_sku" value="<?php echo $Product_sku;?>" readonly>
			</div>
			<div class="form-group col-md-6">
			  <label for="Order_date">Order Date</label>
			  <input type="text" class="form-control" id="Order_date" value="<?php echo $Order_date;?>" readonly>
			</div>
		</div>
		<?php
	} 
}


function wpse_save_meta_fields( $post_id ) {

  // verify nonce
	if (!isset($_POST['wpse_our_nonce']) || !wp_verify_nonce($_POST['wpse_our_nonce'], basename(__FILE__)))
		return 'nonce not verified';

  // check autosave
	if ( wp_is_post_autosave( $post_id ) )
		return 'autosave';

  //check post revision
	if ( wp_is_post_revision( $post_id ) )
		return 'revision';

  // check permissions
	if ( 'coupon' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
		return 'cannot edit page';
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return 'cannot edit post';
	}
	$wpse_value = $_POST['usage'];
	if('coupon' == $_POST['post_type'] && $wpse_value=='unused')
	{
		delete_post_meta($post_id, 'confirmation_number');
	}
	if ( 'coupon' == $_POST['post_type'] && $wpse_value=='used' && !get_post_meta( $post_id, 'confirmation_number', true ) ) 
	{
		$couponposts = get_posts( array(
			'post_type' => 'coupon',
			'meta_query' => array(
			array(
				'key'   => 'usage',
				'value' => 'used',
				 )
			),
			'order' => 'ASC',
			'post_status' => 'publish',
			'numberposts' => -1,
			) );
			if ( $couponposts ) 
			{
				$sequential=count($couponposts)+1;
				update_post_meta( $post_id, 'confirmation_number', $sequential );
			}
	}
	update_post_meta( $post_id, 'usage', $wpse_value );
}
add_action( 'save_post', 'wpse_save_meta_fields' );
add_action( 'new_to_publish', 'wpse_save_meta_fields' );




/*show form in single product page*/
// function prod_form(){
// 	$page_id = get_the_ID();
// 	if($page_id===10807 ){
// 		echo do_shortcode('[contact-form-7 id="11742" title="StrengthKit1"]');
// 	}elseif($page_id===10812 ){
// 		echo do_shortcode('[contact-form-7 id="33929" title="StrengthKit2"]');
// 	}elseif($page_id===11279 ){
// 		echo do_shortcode('[contact-form-7 id="33930" title="StrengthKit3"]');
// 	}else{
// 		echo do_shortcode('[contact-form-7 id="33931" title="StrengthKit4"]');
// 	}
// }
// add_action( 'woocommerce_after_single_product', 'prod_form', 10 );

/*chnge coupon meta value used or not*/
add_action( 'wpcf7_before_send_mail', 'mycustom_wp_footer' );
function mycustom_wp_footer() {
	$wpcf7 = WPCF7_ContactForm::get_current();
	if($wpcf7->id === 11742 || $wpcf7->id === 33929 || $wpcf7->id === 33930 || $wpcf7->id === 33931 ){
		if($_POST['coupon']!=''){
			$coupon_id = get_post_id(strtolower($_POST['coupon']),'coupon');
			$status = get_post_meta( $coupon_id, 'usage', true );
			if($status=='unused'){
				$couponposts = get_posts( array(
				'post_type' => 'coupon',
				'meta_query' => array(
				array(
					'key'   => 'usage',
					'value' => 'used',
					 )
				),
				'order' => 'ASC',
				'post_status' => 'publish',
				'numberposts' => -1,
				) );
				if ( $couponposts ) 
				{
					$sequential=count($couponposts)+1;
					update_post_meta( $coupon_id, 'confirmation_number', $sequential );
				}
				update_post_meta( $coupon_id, 'usage', 'used' );
			}
		}
		update_post_meta( $coupon_id, 'Order_date', $_POST['OrderDate'] );
		update_post_meta( $coupon_id, 'Product_description', $_POST['ProductDescription'] );
		update_post_meta( $coupon_id, 'Product_sku', $_POST['ProductSKU'] );
		update_post_meta( $coupon_id, 'first_name', $_POST['FirstName'] );
		update_post_meta( $coupon_id, 'last_name', $_POST['LastName'] );
		update_post_meta( $coupon_id, 'email', $_POST['Email'] );
		update_post_meta( $coupon_id, 'Address1', $_POST['Address1'] );
		update_post_meta( $coupon_id, 'Address2', $_POST['Address2'] );
		update_post_meta( $coupon_id, 'City', $_POST['City'] );
		update_post_meta( $coupon_id, 'State', $_POST['State'] );
		update_post_meta( $coupon_id, 'Zip', $_POST['ZIP'] );
		update_post_meta( $coupon_id, 'phone', $_POST['phone'] );
	}
}
/*validate coupon and show message*/
function custom_text_validation_filter($result,$tag){
	$wpcf7 = WPCF7_ContactForm::get_current();
	if (@$wpcf7->id === 11742 || @$wpcf7->id === 33929 || @$wpcf7->id === 33930 || @$wpcf7->id === 33931  ) {
		$type = $tag['type'];
		$name = $tag['name'];
		$the_value = $_POST[$name];
		if($name == 'coupon'){
			$coupon_id = get_post_id(strtolower($_POST['coupon']),'coupon');
			if($coupon_id==""){
				$result->invalidate('coupon', 'Your Coupon Code Is Not Valid. Please Check Before Use!');
			}
			
			$status = get_post_meta( $coupon_id, 'usage', true );
			if($status=='used'){
				$result->invalidate('coupon', 'Sorry that coupon code has been used and is no longer valid!');
			}
		}
		return $result;
	}
}
add_filter('wpcf7_validate_text*','custom_text_validation_filter', 5, 2); // Normal field


function get_post_id( $slug, $post_type ) {
    $query = new WP_Query(
        array(
            'name' => $slug,
            'post_type' => $post_type
        )
    );
    $query->the_post();
    return get_the_ID();
}

/*redirect after succesful submission*/
// add_action( 'wp_footer', 'redirect_cf7' );
/*function redirect_cf7() {
	$wpcf7 = WPCF7_ContactForm::get_current();
	if (@$wpcf7->id === 11742 || @$wpcf7->id === 33929 || @$wpcf7->id === 33930 || @$wpcf7->id === 33931 ) {
		?>
		<script type="text/javascript">
		document.addEventListener( 'wpcf7mailsent', function( event ) {
			location = 'http://www.ash.fitness/';
		}, false );
		</script>
		<?php
	}
}
*/



// add order date and confirmation number to hidden fields
add_action( 'wp_footer', 'redirect_order_date' );
function redirect_order_date() {
  $page_id = get_the_ID();
  if($page_id===11699 || $page_id===11709 || $page_id===11710 ||$page_id===11729 ){
		global $wpdb;
		//confirmation number will be the number of orders that have been placed so far
		$confirmation_number = $wpdb->get_var("SELECT count(*) FROM $wpdb->postmeta  WHERE meta_value = 'used' ");
    ?>
    <script type="text/javascript">	
			var today = new Date();
			var dd = String(today.getDate()).padStart(2, '0');
			var mm = String(today.getMonth() + 1).padStart(2, '0');
			var yyyy = today.getFullYear();
			today = mm + '/' + dd + '/' + yyyy;
			document.getElementById('order-date').value=today;
			
			//document.getElementById('confirmation-number').value = <?php echo $confirmation_number ?> + 9000;
    </script>
    <?php
  }
}


/*add used or not column to cpt table*/
function add_coupon_columns($column) {
    // Remove Date
    unset($column['date']);
    $column['usage'] = 'Unused or Used';
		$column['date'] = 'Date';
    return $column;
}
add_filter('manage_coupon_posts_columns', 'add_coupon_columns');

// Register the column as sortable
function register_sortable_columns( $columns ) {
    $columns['usage'] = 'usage';
    return $columns;
}
add_filter( 'manage_edit-coupon_sortable_columns', 'register_sortable_columns' );

// now let's fill our new columns with post meta content  
function wpc_custom_table_content( $column_name, $post_id ) {  
    if ($column_name == 'usage') { 
        $status = get_post_meta( $post_id, 'usage', true );
        echo $status; 
    } 
} 
add_action( 'manage_coupon_posts_custom_column', 'wpc_custom_table_content',10,2 );


add_action( 'pre_get_posts', 'mycpt_custom_orderby' );
function mycpt_custom_orderby( $query ) {
  if ( ! is_admin() )
    return;
  $orderby = $query->get( 'orderby');

  if ( 'usage' == $orderby ) {
    $query->set( 'meta_key', 'usage' );
    $query->set( 'orderby', 'meta_value' );
  }
}


function admin_post_list_add_export_button( $which ) {
    global $typenow;
  
    if ( 'coupon' === $typenow && 'top' === $which ) {
        ?>
        <input type="submit" name="export_all_posts" class="button button-primary" value="<?php _e('Export Used Coupon'); ?>" />
        <?php
    }
}
 
add_action( 'manage_posts_extra_tablenav', 'admin_post_list_add_export_button', 20, 1 );


function func_export_all_posts() {
    if(isset($_GET['export_all_posts'])) {
       $arg = array(
			'post_type' => 'coupon',
			'posts_per_page'   => -1,
		   'meta_query' => array(
			   array(
				   'key' => 'usage',
				   'value' => 'used',
				   'compare' => '=',
			   )
		   )
		);
  
        global $post;
        $arr_post = get_posts($arg);
		/* global $wpdb;

// Print last SQL query string
echo $wpdb->last_query;
		echo "<pre>";
		print_r($arr_post);
		echo "<pre>"; */
		
		
		
        if ($arr_post) {
  
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="used_coupon.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');  
  
            $file = fopen('php://output', 'w');
  
            fputcsv($file,
						array(
							'CODE', 
							'SKU #',
							'Description',
							'Order Conf #',
							'Order Date',
							'First Name', 
							'Last Name', 
							'Address', 
							'Suite/Apt',
							'City',
							'State', 
							'Zip', 
							'Phone',
							'Email',
							'Ship Date',
							'Tracking #'
							)
					);
  
            foreach ($arr_post as $post) {
                setup_postdata($post);
                
                 fputcsv($file, 
							array(
								get_the_title(), 
								get_post_meta( get_the_ID(), 'Product_sku', true ),
								get_post_meta( get_the_ID(), 'Product_description', true ),
								get_post_meta( get_the_ID(), 'confirmation_number', true ),
								get_post_meta( get_the_ID(), 'Order_date', true ),
								get_post_meta( get_the_ID(), 'first_name', true ),
								get_post_meta( get_the_ID(), 'last_name', true ),
								get_post_meta( get_the_ID(), 'Address1', true ),
								get_post_meta( get_the_ID(), 'Address2', true ),
								get_post_meta( get_the_ID(), 'City', true ),
								get_post_meta( get_the_ID(), 'State', true ),
								get_post_meta( get_the_ID(), 'Zip', true ),
								get_post_meta( get_the_ID(), 'phone', true ),
								get_post_meta( get_the_ID(), 'email', true ),
								//get_post_meta( get_the_ID(), 'usage', true ),
							)
						);
            }
  
            exit();
        }
    }
}
 
add_action( 'init', 'func_export_all_posts' );
