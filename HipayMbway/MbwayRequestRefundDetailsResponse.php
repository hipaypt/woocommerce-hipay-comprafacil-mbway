<?php

namespace HipayMbway;

use HipayMbway\MbwayRequestResponse;
use HipayMbway\MbwayRefundDetailsResult;

/**
 * Description of MbwayRequestRefundDetailsResponse
 *
 * @author hipay.pt
 */
class MbwayRequestRefundDetailsResponse extends MbwayRequestRefundResponse {

    private $RefundDetails;

    function __construct($response) {
        $this->ErrorCode = $response->ErrorCode;
        $this->Success = $response->Success;
        $this->ErrorDescription = $response->ErrorDescription;
        $this->RefundDetails = new MbwayRefundDetailsResult($response->RefundDetails);
    }

    function get_RefundDetails() {
        return $this->RefundDetails;
    }

}
