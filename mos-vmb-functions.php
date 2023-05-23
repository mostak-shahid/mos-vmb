<?php
function mos_vmb_admin_enqueue_scripts(){
	$page = @$_GET['page'];
	global $pagenow, $typenow;
	/*var_dump($pagenow); //options-general.php(If under settings)/edit.php(If under post type)
	var_dump($typenow); //post type(If under post type)
	var_dump($page); //mos_vmb_settings(If under settings)*/
	
	if ($pagenow == 'options-general.php' AND $page == 'mos_vmb_settings') {
		wp_enqueue_style( 'mos-vmb-admin', plugins_url( 'css/mos-vmb-admin.css', __FILE__ ) );

		//wp_enqueue_media();

		wp_enqueue_script( 'jquery' );
		
		/*Editor*/
		//wp_enqueue_style( 'docs', plugins_url( 'plugins/CodeMirror/doc/docs.css', __FILE__ ) );
		wp_enqueue_style( 'codemirror', plugins_url( 'plugins/CodeMirror/lib/codemirror.css', __FILE__ ) );
		wp_enqueue_style( 'show-hint', plugins_url( 'plugins/CodeMirror/addon/hint/show-hint.css', __FILE__ ) );

		wp_enqueue_script( 'codemirror', plugins_url( 'plugins/CodeMirror/lib/codemirror.js', __FILE__ ), array('jquery') );
		wp_enqueue_script( 'css', plugins_url( 'plugins/CodeMirror/mode/css/css.js', __FILE__ ), array('jquery') );
		wp_enqueue_script( 'javascript', plugins_url( 'plugins/CodeMirror/mode/javascript/javascript.js', __FILE__ ), array('jquery') );
		wp_enqueue_script( 'show-hint', plugins_url( 'plugins/CodeMirror/addon/hint/show-hint.js', __FILE__ ), array('jquery') );
		wp_enqueue_script( 'css-hint', plugins_url( 'plugins/CodeMirror/addon/hint/css-hint.js', __FILE__ ), array('jquery') );
		wp_enqueue_script( 'javascript-hint', plugins_url( 'plugins/CodeMirror/addon/hint/javascript-hint.js', __FILE__ ), array('jquery') );
		/*Editor*/

		wp_enqueue_script( 'mos-vmb-functions', plugins_url( 'js/mos-vmb-functions.js', __FILE__ ), array('jquery') );
		wp_enqueue_script( 'mos-vmb-admin', plugins_url( 'js/mos-vmb-admin.js', __FILE__ ), array('jquery') );
	}

}
add_action( 'admin_enqueue_scripts', 'mos_vmb_admin_enqueue_scripts' );
function mos_vmb_enqueue_scripts(){
	global $mos_vmb_option;
	if (@$mos_vmb_option['jquery']) {
		wp_enqueue_script( 'jquery' );
	}
	if (@$mos_vmb_option['bootstrap']) {
		wp_enqueue_style( 'bootstrap.min', plugins_url( 'css/bootstrap.min.css', __FILE__ ) );
		wp_enqueue_script( 'bootstrap.min', plugins_url( 'js/bootstrap.min.js', __FILE__ ), array('jquery') );
	}
	if (@$mos_vmb_option['awesome']) {
		wp_enqueue_style( 'font-awesome.min', plugins_url( 'fonts/font-awesome-4.7.0/css/font-awesome.min.css', __FILE__ ) );
	}
	wp_enqueue_style( 'mos-vmb', plugins_url( 'css/mos-vmb.css', __FILE__ ) );
	wp_enqueue_script( 'mos-vmb-functions', plugins_url( 'js/mos-vmb-functions.js', __FILE__ ), array('jquery') );
	wp_enqueue_script( 'mos-vmb', plugins_url( 'js/mos-vmb.js', __FILE__ ), array('jquery') );
}
add_action( 'wp_enqueue_scripts', 'mos_vmb_enqueue_scripts' );
function mos_vmb_ajax_scripts(){
	wp_enqueue_script( 'mos-vmb-ajax', plugins_url( 'js/mos-vmb-ajax.js', __FILE__ ), array('jquery') );
	$ajax_params = array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'ajax_nonce' => wp_create_nonce('mos_vmb_verify'),
	);
	wp_localize_script( 'mos-vmb-ajax', 'ajax_obj', $ajax_params );
}
add_action( 'wp_enqueue_scripts', 'mos_vmb_ajax_scripts' );
add_action( 'admin_enqueue_scripts', 'mos_vmb_ajax_scripts' );
function mos_vmb_scripts() {
	global $mos_vmb_option;
	if (@$mos_vmb_option['css']) {
		?>
		<style>
			<?php echo $mos_vmb_option['css'] ?>
		</style>
		<?php
	}
	if (@$mos_vmb_option['js']) {
		?>
		<style>
			<?php echo $mos_vmb_option['js'] ?>
		</style>
		<?php
	}
}
add_action( 'wp_footer', 'mos_vmb_scripts', 100 );

/**
*  Add custom handling fee to an order 
*/
function mos_vmb_add_handling_fee() {
	global $mos_vmb_options;
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
	if($mos_vmb_options['mos_vmb_enable']){
		global $woocommerce;
		global $new_total_tax;
		$tax = new WC_Tax();
		$total_tax_reduiced = $item_rate = $item_tax = $raw_tax = $raw_profit =0;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			//$product_id = $cart_item['product_id'];
			$product = $cart_item['data'];
			$mos_product_purchase_price = get_post_meta($cart_item['product_id'],'_mos_vmb_product_purchase_price', true);
			if ($mos_product_purchase_price) {
				$taxes = $tax->get_rates($product->get_tax_class());
				$rates = array_shift($taxes);
				if (is_array($taxes) && is_array($rates)){
					//Take only the item rate and round it. 
					$item_rate = round(array_shift($rates));
					if (get_option('woocommerce_calc_taxes') == 'yes'){
						if (get_option('woocommerce_prices_include_tax') == 'yes'){
							$price = $product->get_price();
							$profit_with_tax = $price - $mos_product_purchase_price;
							$raw_profit = $profit_with_tax/(1 + $item_rate/100);
							$raw_tax = round($profit_with_tax - (($profit_with_tax)/(1 + $item_rate/100)));
							$item_tax = round($product->get_price() - (($product->get_price())/(1 + $item_rate/100))) * $cart_item['quantity'];
							//$item_tax = $woocommerce->cart->get_taxes_total();
							$new_tax = $raw_tax * $cart_item['quantity'];
						}
						// else {
						// 	$price = (get_post_meta($cart_item['product_id'], '_sale_price', true))?get_post_meta($cart_item['product_id'], '_sale_price', true):get_post_meta($cart_item['product_id'], '_regular_price', true);
						// 	$item_tax = round($price * $item_rate/100);
						// 	$new_tax = ($price - $mos_product_purchase_price)*$item_rate/100;
						// }				
						$item_tax_reduced = $item_tax - $new_tax;
						$total_tax_reduiced += $item_tax_reduced;
					}
				}
			}
		}
		// $title2 = "Get_taxes_total: " . $woocommerce->cart->get_taxes_total() . ", New tax: " .$new_tax;
		// $fee2 = 0;
		// $woocommerce->cart->add_fee( $title2, $fee2, TRUE, 'standard' );

		if (get_option('woocommerce_calc_taxes') == 'yes'){
			$new_total_tax = $woocommerce->cart->get_taxes_total() - $total_tax_reduiced;
			add_filter( 
				'woocommerce_cart_taxes_total', 
				function(){
					global $new_total_tax;
					return $new_total_tax;
				}, 
				10, 
				4 
			);
		}
		//$title =  'Enable taxes: ' . get_option('woocommerce_calc_taxes') . ', Prices entered with tax: ' . get_option('woocommerce_prices_include_tax');
		//$title = "Title";
		
		// $fee = 0.00;
		// $woocommerce->cart->add_fee( $title, $fee, TRUE, 'standard' );
		//var_dump($woocommerce->cart->get_tax_totals());
		//$woocommerce->cart->get_tax_totals()
		//*$woocommerce->cart->get_taxes_total()
		//$woocommerce->cart->get_taxes()
		//echo $woocommerce->cart->get_taxes();
		//$WC_Order_Item_Tax->set_tax_total( $value );
	}
}
 
// Action -> Add custom handling fee to an order
add_action( 'woocommerce_cart_calculate_fees', 'mos_vmb_add_handling_fee' );
// function get_product_data(){
// 	$product = wc_get_product( 11086 );
// 	var_dump($product);
// }
// add_action('wp_head', 'get_product_data');
// add_action( 'woocommerce_cart_totals_after_order_total', 'uw_display_cart_totals_after' );
// add_action( 'woocommerce_review_order_after_order_total', 'uw_display_cart_totals_after' );
add_action( 'woocommerce_cart_totals_before_order_total', 'mos_vmb_display_cart_totals_after', 99 );
/**
 * Pulls in cart totals and adds a new table row to the cart/checkout totals
 *
 * @author UltimateWoo - https://www.ultimatewoo.com
 */
function mos_vmb_display_cart_totals_after() {
	global $mos_vmb_options;
	if($mos_vmb_options['mos_vmb_enable']){
		global $woocommerce;
		global $new_total_tax;
		$tax = new WC_Tax();
		$total_tax_reduiced = $item_rate = $item_tax = $raw_tax = $raw_profit = $total_profit = 0;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			//$product_id = $cart_item['product_id'];
			$product = $cart_item['data'];
			$mos_product_purchase_price = get_post_meta($cart_item['product_id'],'_mos_vmb_product_purchase_price', true);
			if ($mos_product_purchase_price) {
				$taxes = $tax->get_rates($product->get_tax_class());
				$rates = array_shift($taxes);
				//Take only the item rate and round it. 
				$item_rate = round(array_shift($rates));
				if (get_option('woocommerce_calc_taxes') == 'yes'){
					if (get_option('woocommerce_prices_include_tax') == 'yes'){
						$price = $product->get_price();
						$profit_with_tax = $price - $mos_product_purchase_price;
						$raw_profit = $profit_with_tax/(1 + $item_rate/100) * $cart_item['quantity'];
						$raw_tax = round($profit_with_tax - (($profit_with_tax)/(1 + $item_rate/100)));
						$item_tax = $woocommerce->cart->get_taxes_total();
						$new_tax = $raw_tax * $cart_item['quantity'];
					}
					$total_profit += $raw_profit;
				}
			}
		}
		if ($total_profit) {
			?>
			<tr class="taxable-amount" id="final-total">
				<th><?php _e( 'Taxable amount', 'woocommerce' ); ?></th>
				<td><?php echo $total_profit . get_woocommerce_currency_symbol() ?></td>
			</tr>
			<?php
		}
	}
}

// Display Fields
add_action('woocommerce_product_options_general_product_data', 'mos_vmb_woocommerce_product_custom_fields');

// Save Fields
add_action('woocommerce_process_product_meta', 'mos_vmb_woocommerce_product_custom_fields_save');


function mos_vmb_woocommerce_product_custom_fields() {
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    // Taxable amount
    woocommerce_wp_text_input(
        array(
            'id' => '_mos_vmb_product_purchase_price',
            //'placeholder' => 'Custom Product Number Field',
            'label' => __('Purchase price', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '0'
            )
        )
    );
    //Custom Product Number Field
    /*woocommerce_wp_text_input(
        array(
            'id' => '_custom_product_number_field',
            'placeholder' => 'Custom Product Number Field',
            'label' => __('Custom Product Number Field', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '0'
            )
        )
    );
    //Custom Product  Textarea
    woocommerce_wp_textarea_input(
        array(
            'id' => '_custom_product_textarea',
            'placeholder' => 'Custom Product Textarea',
            'label' => __('Custom Product Textarea', 'woocommerce')
        )
    );*/
    echo '</div>';
}
function mos_vmb_woocommerce_product_custom_fields_save($post_id) {
    // Custom Product Text Field
    $woocommerce_mos_vmb_product_purchase_price = $_POST['_mos_vmb_product_purchase_price'];
    if (!empty($woocommerce_mos_vmb_product_purchase_price))
        update_post_meta($post_id, '_mos_vmb_product_purchase_price', esc_attr($woocommerce_mos_vmb_product_purchase_price));
// Custom Product Number Field
    /*$woocommerce_custom_product_number_field = $_POST['_custom_product_number_field'];
    if (!empty($woocommerce_custom_product_number_field))
        update_post_meta($post_id, '_custom_product_number_field', esc_attr($woocommerce_custom_product_number_field));
// Custom Product Textarea Field
    $woocommerce_custom_procut_textarea = $_POST['_custom_product_textarea'];
    if (!empty($woocommerce_custom_procut_textarea))
        update_post_meta($post_id, '_custom_product_textarea', esc_html($woocommerce_custom_procut_textarea));*/

}