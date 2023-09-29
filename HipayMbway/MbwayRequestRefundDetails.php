<?php

namespace HipayMbway;

use HipayMbway\MbwayRequest;

/**
 * Description of MbwayRequestRefundDetails
 *
 * @author hipay.pt
 */
class MbwayRequestRefundDetails extends MbwayRequest {

    private $operationId;

    function __construct($cfMerchantId, $password, $operationId, $cfEntityOrType = 1) {
        $this->set_cfMerchantId($cfMerchantId);
        $this->set_password($password);
        $this->set_OperationId($operationId);
        $this->set_cfEntityOrType($cfEntityOrType);
    }

    function get_operationId() {
        return $this->operationId;
    }

    function set_operationId($operationId) {
        $this->operationId = $operationId;
    }

}
