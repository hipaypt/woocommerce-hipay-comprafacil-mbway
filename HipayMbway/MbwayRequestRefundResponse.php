<?php

namespace HipayMbway;
/**
 * Description of MbwayRequestRefundResponse
 *
 * @author hipay.pt
 */
class MbwayRequestRefundResponse extends MbwayRequestResponse{

    protected $Success;
    protected $ErrorCode;
    protected $ErrorDescription;
   
    function __construct($response) {
        $this->ErrorCode = $response->ErrorCode;
        $this->Success = $response->Success;
        $this->ErrorDescription = $response->ErrorDescription;
    }

    function get_Success() {
        return $this->Success;
    }

    function get_ErrorDescription() {
        return $this->ErrorDescription;
    }

    function get_ErrorCode() {
        return $this->ErrorCode;
    }

}