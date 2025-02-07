<?php

class WC_HiPayMbway_Blocks_Integration extends Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType {

    protected $name = 'hipaymbway'; // Deve corresponder ao ID do método de pagamento
	
    public function initialize() {
        $this->settings = get_option('woocommerce_hipaymbway_settings', array());
    }

    public function is_active() {
        return !empty($this->settings['enabled']) && 'yes' === $this->settings['enabled'];
		
    }

    public function get_payment_method_script_handles() {
        wp_register_script(
            'wc-hipaymbway-blocks-integration',
            plugins_url('../assets/js/hipaymbway-blocks.js', __FILE__),
            array('wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-i18n'),
            '1.0.0',
            true
        );
        
        if ($this->settings['payment_image'] != "0" )
            $image = WP_PLUGIN_URL . "/" . plugin_basename( dirname(__FILE__)) . '/../images/btn-mbway'.$this->settings['payment_image'].'.jpg';
        else
            $image = '';
        
        $args = array(
            'image' => $image,
        );
    
        wp_localize_script(
            'wc-hipaymbway-blocks-integration', 
            'hipayMbwayData', 
            $args 
        );

        if( ! wp_script_is( 'wc-hipaymbway-blocks-integration', 'enqueued' ) ) {
            wp_enqueue_script('wc-hipaymbway-blocks-integration');       
        }   
           
        return array('wc-hipaymbway-blocks-integration');
    }

    public function get_payment_method_data() {
        return array(
            'title'       => $this->settings['method_title'] ?? 'MB WAY',
            'description' => $this->settings['method_description'] ?? 'Pay with MB WAY',
            'supports'    => array('products'),
        );
    }
}
