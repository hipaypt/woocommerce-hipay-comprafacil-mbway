<?php
/*
  Plugin Name: WooCommerce HiPay Comprafacil MB WAY
  Plugin URI: https://github.com/hipaypt/woocommerce-hipay-comprafacil-mbway/
  Description: Plugin WooCommerce for MB WAY payments via HiPay. For more information contact <a href="mailto:hipay.portugal@hipay.com" target="_blank">hipay.portugal@hipay.com</a>.
  Version: 1.1.0
  Author: Hi-Pay Portugal
  Author URI: https://www.hipaycomprafacil.com
 */

add_action('plugins_loaded', 'woocommerce_hipaymbway_init', 0);

include_once(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'HipayMbway/MbwayClient.php');
include_once(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'HipayMbway/MbwayRequest.php');
include_once(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'HipayMbway/MbwayRequestTransaction.php');
include_once(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'HipayMbway/MbwayRequestDetails.php');
include_once(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'HipayMbway/MbwayRequestResponse.php');
include_once(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'HipayMbway/MbwayRequestDetailsResponse.php');
include_once(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'HipayMbway/MbwayRequestTransactionResponse.php');
include_once(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'HipayMbway/MbwayPaymentDetailsResult.php');
include_once(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'HipayMbway/MbwayRequestRefund.php');
include_once(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'HipayMbway/MbwayNotification.php');

use HipayMbway\MbwayClient;
use HipayMbway\MbwayRequest;
use HipayMbway\MbwayRequestTransaction;
use HipayMbway\MbwayRequestDetails;
use HipayMbway\MbwayRequestResponse;
use HipayMbway\MbwayRequestDetailsResponse;
use HipayMbway\MbwayRequestTransactionResponse;
use HipayMbway\MbwayPaymentDetailsResult;
use HipayMbway\MbwayRequestRefund;
use HipayMbway\MbwayNotification;

function woocommerce_hipaymbway_init() {

    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    class WC_HipayMbway extends WC_Payment_Gateway {

		const HIPAY_LOG_INFO = 'INFO';
		const HIPAY_LOG_ERROR = 'ERROR';
		
		protected $logger;
		
        public function __construct() {

            global $woocommerce;
            $this->id = 'hipaymbway';
            load_plugin_textdomain("woocommerce-gateway-hipaymbway", false, basename(dirname(__FILE__)) . '/languages');
            $this->has_fields = false;
            $this->method_title = __('MB WAY via HiPay', 'woocommerce-gateway-hipaymbway');
            $this->init_form_fields();
            $this->init_settings();
            $this->title = __('MB WAY', 'woocommerce-gateway-hipaymbway');
            $this->description = __('Pay with your MB WAY app', 'woocommerce-gateway-hipaymbway');
            $this->entity = $this->get_option('entity');
            $this->sandbox = $this->get_option('sandbox');
            $this->username = $this->get_option('hw_username');
            $this->password = $this->get_option('hw_password');
            $this->stockonpayment = $this->get_option('stockonpayment');
            $this->payment_image = $this->get_option('payment_image');
			$this->logActions 	= $this->get_option('logActions');
			
            if ($this->payment_image != "")
                $this->icon = WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . '/images/btn-mbway' . $this->payment_image . '.jpg';
            $this->category = get_option('woocommerce_hipaymbway_business', array('woocommerce_hipaymbway_category' => $this->get_option('woocommerce_hipaymbway_category'),));
            if (get_locale() == "pt_PT") {
                include_once( plugin_dir_path(__FILE__) . 'includes/business_areas_pt.php' );
            } else {
                include_once( plugin_dir_path(__FILE__) . 'includes/business_areas_en.php' );
            }

			$this->logger = wc_get_logger();
			
			add_action('woocommerce_api_wc_hipaymbway', array($this, 'check_callback_response'));
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'save_category_option'));
            add_action('woocommerce_thankyou_hipaymbway', array($this, 'thanks_page'));
        }

        /**
         * Save category id
         */
        public function save_category_option() {

            $category = array();
            $category['woocommerce_hipaymbway_category'] = sanitize_title($_POST['woocommerce_hipaymbway_category']);
            update_option('woocommerce_hipaymbway_business', $category);
        }

        /**
         * init_form_fields
         */
        function init_form_fields() {

            global $wpdb;
            $table_name = $wpdb->prefix . 'woocommerce_' . $this->id;
            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				
				$this->logActionMessage( "Support table does not exist, try to create " . $table_name , self::HIPAY_LOG_INFO );
				
                $charset_collate = $wpdb->get_charset_collate();
                $sql = "CREATE TABLE $table_name (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `reference` varchar(30) NOT NULL,
				  `processed` tinyint(4) NOT NULL DEFAULT '0',
				  `order_id` bigint(20) NOT NULL,
				  `processed_date` datetime NOT NULL,
				  `entity` varchar(7) NOT NULL,
				  `phone` varchar(17) NOT NULL,
				  `sandbox` tinyint(4) NOT NULL DEFAULT '1',
				UNIQUE KEY id (id)
				) $charset_collate;";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }

            $this->form_fields = array(
                'enabled' => array(
                    'title' => __("Enable/Disable", "woocommerce-gateway-hipaymbway"),
                    'type' => 'checkbox',
                    'label' => __('Activate payment method', 'woocommerce-gateway-hipaymbway'),
                    'default' => 'no'
                ),
                'sandbox' => array(
                    'title' => __('Use Sandbox', 'woocommerce-gateway-hipaymbway'),
                    'type' => 'checkbox',
                    'label' => __('Activate test account', 'woocommerce-gateway-hipaymbway'),
                    'default' => 'no'
                ),
                'entity' => array(
                    'title' => __('Entity', 'woocommerce-gateway-hipaymbway'),
                    'type' => 'select',
                    'description' => __('Your user account entity.', 'woocommerce-gateway-hipaymbway'),
                    'options' => array(
                        '11249' => __('11249', 'woocommerce-gateway-hipaymbway'),
                        '10241' => __('10241 / 12029', 'woocommerce-gateway-hipaymbway')
                    )
                ),
                'hw_username' => array(
                    'title' => __('Username', 'woocommerce-gateway-hipaymbway'),
                    'type' => 'text',
                    'description' => __('Username for MB WAY webservice.', 'woocommerce-gateway-hipaymbway'),
                    'required' => true
                ),
                'hw_password' => array(
                    'title' => __('Password', 'woocommerce-gateway-hipaymbway'),
                    'type' => 'text',
                    'description' => __('Password for MB WAY webservice.', 'woocommerce-gateway-hipaymbway'),
                    'required' => true
                ),
                'category_settings' => array(
                    'type' => 'category_settings',
                ),
                'payment_image' => array(
                    'title' => __('Default payment image', 'woocommerce-gateway-hipaymbway'),
                    'type' => 'select',
                    'description' => __('Default image used during the checkout process.', 'woocommerce-gateway-hipaymbway'),
                    'options' => array(
                        '' => __('No image', 'woocommerce-gateway-hipaymbway'),
                        '80' => __('Large', 'woocommerce-gateway-hipaymbway'),
                        '25' => __('Medium', 'woocommerce-gateway-hipaymbway'),
                        '16' => __('Small', 'woocommerce-gateway-hipaymbway')
                    )
                ),
                'stockonpayment' => array(
                    'title' => __('Update stocks', 'woocommerce-gateway-hipaymbway'),
                    'type' => 'checkbox',
                    'description' => __('Reduce stocks only after payment confirmation.', 'woocommerce-gateway-hipaymbway'),
                    'default' => 'no'
                ),
				'logActions' => array(
					'title' => __( 'Logs', $this->id ),
					'type' => 'checkbox',
					'description' => '',
					'default' => 'no'

				),				
            );
        }

		
	    /**
	     * log action
	     * 
	     * @param string 	$messsage
	     * @param string 	$type
		 */
		private function logActionMessage($message,$type) {
			
			if ($this->logActions == "yes") {
				
				if (is_array($message) || is_object($message)){
					$message = wc_print_r( $message, true );
				}
				
				if ($type === self::HIPAY_LOG_INFO){
					$this->logger->info( $message, array( 'source' => $this->id ) );
				} else {
					$this->logger->error( $message, array( 'source' => $this->id ) );
				}
			}
		}
		
		
        /**
         * Generate Category List
         */
        function generate_category_settings_html() {

            ob_start();
            global $business_areas;
            $woocommerce_hipaymbway_category = esc_textarea($this->category["woocommerce_hipaymbway_category"]);
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc"><?php _e('Category', 'woocommerce-gateway-hipaymbway'); ?></th>
                <td class="forminp">
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php _e('Category', 'woocommerce-gateway-hipaymbway'); ?></span>
                        </legend>
                        <select class="select " name="woocommerce_hipaymbway_category" id="woocommerce_hipaymbway_category"
                            style="">
                            <?php
                        foreach ($business_areas as $key => $value) {
                            $user_business_current = explode("|", $value);
                            echo "<option value='" . $user_business_current[0] . "'";
                            if ($user_business_current[0] == $woocommerce_hipaymbway_category)
                                echo " SELECTED";
                            if ($user_business_current[1] == "disabled")
                                echo " disabled>" . __($user_business_current[2], 'woocommerce-gateway-hipaymbway');
                            else
                                echo ">&nbsp;&nbsp;&nbsp;&nbsp;" . __($user_business_current[1], 'woocommerce-gateway-hipaymbway');
                            echo "</option>";
                        }
                        ?>
                        </select>
                        <p class="description"><?php _e("Category for MB WAY webservice.", 'woocommerce-gateway-hipaymbway'); ?></p>
                    </fieldset>
                </td>
            </tr>
            <?php
            return ob_get_clean();
        }

	    /**
	     * admin options 
		 */
        public function admin_options() {

            $soap_active = false;
            $has_webservice_access = false;
            $has_webservice_access_error = "";
            if (extension_loaded('soap')) {
                $soap_active = true;
            }
            if ($soap_active && $this->username != "" && $this->password != "") {

                if ($this->sandbox == "no")
                    $this->sandbox = false;

                $mbway = new MbwayClient((bool) $this->sandbox);
                $mbwayRequestDetails = new MbwayRequestDetails($this->username, $this->password, "000000000", $this->entity);
                $mbwayRequestDetailsResult = new MbwayRequestDetailsResponse($mbway->getPaymentDetails($mbwayRequestDetails)->GetPaymentDetailsResult);
                if ($mbwayRequestDetailsResult->get_ErrorCode() <> -3) {
                    $has_webservice_access = true;
                }
                $has_webservice_access_error = $mbwayRequestDetailsResult->get_ErrorDescription();
            }
            ?>

            <h3><?php _e('MB WAY via HiPay', 'woocommerce-gateway-hipaymbway'); ?></h3>
            <p><?php _e('Payment with MB WAY app.', 'woocommerce-gateway-hipaymbway'); ?></p>
            <table class="wc_emails widefat" cellspacing="0">
                <tbody>
                    <tr>
                        <td class="wc-email-settings-table-status">
                            <?php if ($soap_active) { ?>
                            <span class="status-enabled"></span>
                            <?php } else {
                                            ?>
                            <span class="status-disabled"></span>
                            <?php }
                                        ?>
                        </td>
                        <td class="wc-email-settings-table-name"><?php echo __('SOAP Library', 'woocommerce-gateway-hipaymbway'); ?>
                        </td>
                        <td>
                            <?php if (!$soap_active) echo __('Install and activate SOAP library.', 'woocommerce-gateway-hipaymbway'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="wc-email-settings-table-status">
                            <?php if ($soap_active && $has_webservice_access) { ?>
                            <span class="status-enabled"></span>
                            <?php } else {
                                            ?>
                            <span class="status-disabled"></span>
                            <?php }
                                        ?>
                        </td>
                        <td class="wc-email-settings-table-name">
                            <?php echo __('Webservice configuration', 'woocommerce-gateway-hipaymbway'); ?></td>
                        <td><?php
                            if (!$has_webservice_access) {
                                echo __('Please check the configuration or the server access to the webservice.', 'woocommerce-gateway-hipaymbway');
                                if ($has_webservice_access_error != "")
                                    echo "<br>" . __('Error: ', 'woocommerce-gateway-hipaymbway') . $has_webservice_access_error;
                            } elseif (!$soap_active) {
                                echo __('Install and activate SOAP library.', 'woocommerce-gateway-hipaymbway');
                            }
                                        ?></td>
                    </tr>
                </tbody>
            </table>

            <table class="form-table">
            <?php
                $this->generate_settings_html();
            ?>
            </table>

            <p>&bull;<?php _e('Ensure that you have SOAP library and Woocommerce REST API activated.', 'woocommerce-gateway-hipaymbway'); ?>
            </p>
            <p>&bull;<?php _e('Activate sandbox if you wish to use a test account.', 'woocommerce-gateway-hipaymbway'); ?></p>
            <p>&bull;<?php _e('Entity, username, password and category are provided by HiPay.', 'woocommerce-gateway-hipaymbway'); ?>
            </p>
        <?php
        }

	    /**
	     * payment fields 
		 */		
        public function payment_fields() {
            global $woocommerce;
            $fields = WC()->checkout()->checkout_fields;
            if (!isset($fields["billing"]["billing_phone"])) {
                _e("Please enter your phone number:", 'woocommerce-gateway-hipaymbway');
            } else {
                _e("If necessary, enter a different phone number to make the payment:", 'woocommerce-gateway-hipaymbway');
            }
            ?>
            <div class="">
                <input type="tel" class="input-text " name="billing_phone_alt" id="billing_phone_alt" placeholder="" value=""
                    autocomplete="tel" maxlength="9">
            </div>
        <?php
        }

	    /**
	     * process payment
	     * 
	     * @param integer 	$order_id
		 */
		
        function process_payment($order_id) {

            global $woocommerce;
            global $wpdb;

            $order = new WC_Order($order_id);
            $order_total = $order->get_total();
            $billing_data = get_post_meta($order_id);
            if (isset($_POST['billing_phone_alt']) && $_POST['billing_phone_alt'] != "")
                $mbway_phone = sanitize_text_field($_POST['billing_phone_alt']);
            elseif (isset($billing_data['_billing_phone'][0]))
                $mbway_phone = sanitize_text_field($billing_data['_billing_phone'][0]);

            $mbway_phone = filter_var($mbway_phone, FILTER_SANITIZE_NUMBER_INT);
            if (strlen($mbway_phone) < 9) {
                throw new Exception(__("Invalid MB WAY phone number.", 'woocommerce-gateway-hipaymbway'));
            }
            if (strlen($mbway_phone) > 9) {
                $mbway_phone = substr($mbway_phone, -9);
            }

            //$callback_url = site_url() . '/wc-api/WC_HipayMbway/?order=' . $order_id;
            $callback_url = site_url() . '?wc-api=WC_HipayMbway&order=' . $order_id;
            if ($this->sandbox == "no")
                $this->sandbox = false;
            $mbway = new MbwayClient($this->sandbox);
            $mbwayRequestTransaction = new MbwayRequestTransaction($this->username, $this->password, $order_total, $mbway_phone, $billing_data['_billing_email'][0], $order_id, $this->category["woocommerce_hipaymbway_category"], $callback_url, $this->entity);
            $mbwayRequestTransaction->set_description($order_id);
            $mbwayRequestTransaction->set_clientVATNumber("");
            $mbwayRequestTransaction->set_clientName("");

            $mbwayRequestTransactionResult = new MbwayRequestTransactionResponse($mbway->createPayment($mbwayRequestTransaction)->CreatePaymentResult);
            if ($mbwayRequestTransactionResult->get_Success() && $mbwayRequestTransactionResult->get_ErrorCode() == "0") {
                switch ($mbwayRequestTransactionResult->get_MBWayPaymentOperationResult()->get_StatusCode()) {
                    case "vp1":
                        $reference = $mbwayRequestTransactionResult->get_MBWayPaymentOperationResult()->get_OperationId();
                        $table_name = $wpdb->prefix . 'woocommerce_' . $this->id;
                        $wpdb->insert($table_name, array('entity' => $this->entity, 'reference' => $reference, 'sandbox' => $this->sandbox, 'phone' => $mbway_phone, 'order_id' => $order_id));
                        $order->update_status('on-hold', __('Waiting payment confirmation from MB WAY.', 'woocommerce-gateway-hipaymbway'));
                        if ($this->stockonpayment != "yes")
                            $order->reduce_order_stock();
                        $order->add_order_note('MB WAY Ref. ' . $reference);
						$this->logActionMessage( "Order => " . $order_id . " MB WAY => " . $reference , self::HIPAY_LOG_INFO );

                        return array(
                            'result' => 'success',
                            'redirect' => add_query_arg('order', $order_id, add_query_arg('key', $order->get_order_key(), $order->get_checkout_order_received_url()))
                        );
                        break;
                    case "vp2":
						$this->logActionMessage( "Order => " . $order_id . " Operation refused " . $mbwayRequestTransactionResult->get_MBWayPaymentOperationResult()->get_StatusCode() , self::HIPAY_LOG_INFO );
                        $order->add_order_note($mbwayRequestTransactionResult->get_MBWayPaymentOperationResult()->get_StatusDescription());
                        throw new Exception(__("Operation refused. Please try again or choose another payment method.", 'woocommerce-gateway-hipaymbway'));
						break;	
                    case "vp3":
						$this->logActionMessage( "Order => " . $order_id . " Operation refused, user limit exceeded." , self::HIPAY_LOG_INFO );
                        $order->add_order_note($mbwayRequestTransactionResult->get_MBWayPaymentOperationResult()->get_StatusDescription());
                        throw new Exception(__("Operation refused. Limit exceeded. Please try again or choose another payment method.", 'woocommerce-gateway-hipaymbway'));
                    	break;
					case "er1":
						$this->logActionMessage( "Order => " . $order_id . " Operation refused, invalid phone number." , self::HIPAY_LOG_INFO );
                        $order->add_order_note($mbwayRequestTransactionResult->get_MBWayPaymentOperationResult()->get_StatusDescription());
                        throw new Exception(__("Operation refused. Invalid phone number. Please try again with another phone number or choose another payment method.", 'woocommerce-gateway-hipaymbway'));
						break;
                    case "er2":
						$this->logActionMessage( "Order => " . $order_id . " Operation refused, unassined phone number." , self::HIPAY_LOG_INFO );
                        $order->add_order_note($mbwayRequestTransactionResult->get_MBWayPaymentOperationResult()->get_StatusDescription());
                        throw new Exception(__("Operation refused. Unassigned phone number. Please try again with another phone number or choose another payment method.", 'woocommerce-gateway-hipaymbway'));
						break;
                    default:
						$this->logActionMessage( "Order => " . $order_id . " Operation refused." , self::HIPAY_LOG_INFO );
                        $order->add_order_note(_("Operation refused. Unknown error.", 'woocommerce-gateway-hipaymbway'));
                        throw new Exception(__("Operation refused. Please try again or choose another payment method.", 'woocommerce-gateway-hipaymbway'));
                		break;
				}
            } else {
				$this->logActionMessage( "Order => " . $order_id . " Operation error " . $mbwayRequestTransactionResult->get_ErrorDescription() , self::HIPAY_LOG_ERROR );

                $order->add_order_note($mbwayRequestTransactionResult->get_ErrorDescription());
                throw new Exception($mbwayRequestTransactionResult->get_ErrorDescription());
            }
        }

		/**
	     * check callback response
		 */
        function check_callback_response() {

            global $woocommerce;
            global $wpdb;
            $table_name = $wpdb->prefix . 'woocommerce_' . $this->id;

            $order_id = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_NUMBER_INT);
            if ($order_id == "") {
                exit;
            }
            $order_id = (int) $order_id;
            if ($this->sandbox == "no")
                $this->sandbox = false;

            try {

                $order = new WC_Order($order_id);
                $order_mbway = $wpdb->get_row("SELECT ID, reference, order_id, processed,sandbox FROM $table_name WHERE order_id = '" . $order_id . "'");
                if (!isset($order_mbway->ID) || !isset($order_mbway->reference)) {
					$this->logActionMessage( "Order => " . $order_id . " notification received but order not found on support table." , self::HIPAY_LOG_INFO );
                    $order->add_order_note(__("Notification: Order not found.", "woocommerce-gateway-hipaymbway"));
                    exit;
                }

                $mbway = new MbwayClient($this->sandbox);
                $mbwayRequestDetails = new MbwayRequestDetails($this->username, $this->password, $order_mbway->reference, $this->entity);
                $mbwayRequestDetailsResult = new MbwayRequestDetailsResponse($mbway->getPaymentDetails($mbwayRequestDetails)->GetPaymentDetailsResult);

                if ($mbwayRequestDetailsResult->get_ErrorCode() <> 0 || !$mbwayRequestDetailsResult->get_Success()) {
					$this->logActionMessage( "Order => " . $order_id . " notification received but unable to confirm status." , self::HIPAY_LOG_INFO );
					$order->add_order_note(__("Notification: Unable to confirm payment status.", "woocommerce-gateway-hipaymbway"));
                    exit;
                }

                $detailStatusCode = $mbwayRequestDetailsResult->get_MBWayPaymentDetails()->get_StatusCode();
                $detailAmount = $mbwayRequestDetailsResult->get_MBWayPaymentDetails()->get_Amount();
                $detailOperationId = $mbwayRequestDetailsResult->get_MBWayPaymentDetails()->get_OperationId();

                $transaction_id = $order_mbway->reference;
                $order_total = $order->get_total();

                if ($detailOperationId != $transaction_id) {
					$this->logActionMessage( "Order => " . $order_id . " notification received but Transaction Id does not match." , self::HIPAY_LOG_INFO );
                    $order->add_order_note(__("Notification: Transaction ID does not match.", "woocommerce-gateway-hipaymbway"));
                    exit;
                }

                switch ($detailStatusCode) {
                    case "c1":
                        if ($this->stockonpayment == "yes") {
                            $order->reduce_order_stock();
                            $order->add_order_note(__('Stock updated', "woocommerce-gateway-hipaymbway"));
                        }
						$this->logActionMessage( "Order => " . $order_id . " notification status " . $detailStatusCode . " received. Payment complete." , self::HIPAY_LOG_INFO );
                        $order->payment_complete($transaction_id);
                        break;
                    case "c3":
                    case "c6":
                    case "vp1":
						$this->logActionMessage( "Order => " . $order_id . " notification status " . $detailStatusCode . " received. Waiting capture." , self::HIPAY_LOG_INFO );
                        $order->add_order_note(__('Waiting capture notification', "woocommerce-gateway-hipaymbway"));
                        break;
                    case "ap1":
						$this->logActionMessage( "Order => " . $order_id . " notification " . $detailStatusCode . " received. Refund." , self::HIPAY_LOG_INFO );
						$order->update_status('refunded', __('Refunded', "woocommerce-gateway-hipaymbway"), 0);
                        break;
                    case "c2":
                    case "c4":
                    case "c5":
                    case "c7":
                    case "c8":
                    case "c9":
                    case "vp2":
						$this->logActionMessage( "Order => " . $order_id . " notification " . $detailStatusCode . " received. Order Cancelled." , self::HIPAY_LOG_INFO );

                        $order->update_status('cancelled', __("MB WAY payment cancelled.", "woothemes"), 0);
                        if ($this->stockonpayment != "yes") {

                            $products = $order->get_items();
                            foreach ($products as $product) {
                                $qt = $product['item_meta']['_qty'][0];
                                $product_id = $product['item_meta']['_product_id'][0];
                                $variation_id = (int) $product['item_meta']['_variation_id'][0];

                                if ($variation_id > 0) {
                                    $pv = new WC_Product_Variation($variation_id);
                                    if ($pv->managing_stock()) {
                                        $pv->increase_stock($qt);
                                    } else {
                                        $p = new WC_Product($product_id);
                                        $p->increase_stock($qt);
                                    }
                                } else {
                                    $p = new WC_Product($product_id);
                                    $p->increase_stock($qt);
                                }
                            }
                        }
                        break;
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
				$this->logActionMessage( "Order => " . $order_id . " notification. Exception " . $error , self::HIPAY_LOG_ERROR );
                return false;
            }

            return true;
        }

	    /**
	     * thanks page
	     * 
	     * @param integer 	$order_id
		 */
        function thanks_page($order_id) {

            global $woocommerce;
            echo "<p>" . __("Please confirm the payment using your MB WAY App. The order will be processed as soon as we get the payment confirmation.", 'woocommerce-gateway-hipaymbway') . "<center><img src='" . WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . "/images/btn-mbway80.jpg'  border='0' align='center'></center></p>";
            $woocommerce->cart->empty_cart();
            unset($_SESSION['order_awaiting_payment']);
        }

    }

	/**
     * filter hipay mbway gateway
     * 
     * @param array 	$methods
	 */
    function filter_hipaymbway_gateway($methods) {

        global $woocommerce;
        if (isset($woocommerce->cart)) {
            $currency = get_woocommerce_currency();
            if ($currency !== "EUR") {
                unset($methods['hipaymbway']);
            }
        }
        return $methods;
    }

    add_filter('woocommerce_available_payment_gateways', 'filter_hipaymbway_gateway');

	/**
     * add hipay mbway gateway
     * 
     * @param array 	$methods
	 */
    function add_hipaymbway_gateway($methods) {
        $methods[] = 'WC_HipayMbway';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'add_hipaymbway_gateway');
}