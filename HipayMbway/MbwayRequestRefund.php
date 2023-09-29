<?php

namespace HipayMbway;
use HipayMbway\MbwayRequest;

/**
 * Description of MbwayRequestRefund
 *
 * @author hipay.pt
 */
class MbwayRequestRefund extends MbwayRequest{

    private $operationId;
    private $amount;

    function __construct($cfMerchantId, $password, $operationId, $amount, $cfEntityOrType = 1) {
        $this->set_cfMerchantId($cfMerchantId);
        $this->set_password($password);
        $this->set_OperationId($operationId);
        $this->set_amount($amount);
        $this->set_cfEntityOrType($cfEntityOrType);
    }

    private function get_operationId() {
        return $this->operationId;
    }

    private function set_operationId($operationId) {
        $this->operationId = $operationId;
    }

    private function get_amount() {
        return $this->amount;
    }

    private function set_amount($amount) {
        $this->amount = $amount;
    } 
}
