<?php

namespace HipayMbway;

/**
 * Description of MbwayClient
 *
 * @author hipay.pt
 */
class MbwayClient {

    const HIPAY_MBWAY_ENDPOINT_PRODUCTION = "https://mbway.hipay.pt/Webservice/v4/MBWayV3.svc?singleWsdl";
    const HIPAY_MBWAY_ENDPOINT_SANDBOX = "https://mbway.hipay.pt/MBWayWebservice-test/MBWayV3.svc?singleWsdl";

    private $soapClient;
    private $isSandbox;
    private $endpoint;

    function __construct($isSandbox = true) {
        $this->set_isSandbox($isSandbox);      
        $this->set_endpoint();
        $this->set_soapClient();
    }

    function get_isSandbox() {
        return $this->isSandbox;
    }

    function get_endpoint() {
        return $this->endpoint;
    }

    private function set_isSandbox($isSandbox) {
        $this->isSandbox = $isSandbox;
    }

    private function set_endpoint() {
        if (!$this->isSandbox) {
            $this->endpoint = self::HIPAY_MBWAY_ENDPOINT_PRODUCTION;
        } else {
            $this->endpoint = self::HIPAY_MBWAY_ENDPOINT_SANDBOX;
        }
    }

    private function set_soapClient() {
        if (extension_loaded('soap')) {
            $this->soapClient = new \SoapClient($this->endpoint);
        } else {
            return false;
        }
    }

    /*
    * get payment status
    */ 
    public function getPaymentDetails($request) {
        $result = $this->soapClient->GetPaymentDetails($request);
        return $result;
    }

    /*
    * request a new mbway transaction
    */
    public function createPayment($request) {
        $result = $this->soapClient->CreatePayment($request);
        return $result;
    }

    /*
    * request a refund 
    */
    public function requestRefund($request) {
        $result = $this->soapClient->RequestRefund($request);
        return $result;
    }

    /*
    * get refund request status
    */ 
    public function getRequestRefundDetails($request) {
        $result = $this->soapClient->GetRequestRefundDetails($request);
        return $result;
    }
}
