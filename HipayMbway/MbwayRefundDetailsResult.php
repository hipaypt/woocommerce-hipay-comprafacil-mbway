<?php

namespace HipayMbway;

/**
 * Description of MbwayRefundDetailsResult
 *
 * @author hipay.pt
 */
class MbwayRefundDetailsResult {

    const REFUND_STATUS_NEW = "New";
    const REFUND_STATUS_SUCCESS = "Success";
    const REFUND_STATUS_UNSUCCESS = "Unsuccess";

    private $Amount;
    private $CFEntity;
    private $CFMerchantId;
    private $ClientEmail;
    private $OperationId;
    private $Phone;
    private $ProcessedDate;
    private $RequestDate;
    private $Source;
    private $Status;
    private $StatusDescription;

    function __construct($details) {
        if (isset($details->Amount))
            $this->Amount = $details->Amount;
        if (isset($details->CFEntity))
            $this->CFEntity = $details->CFEntity;
        if (isset($details->CFMerchantId))
            $this->CFMerchantId = $details->CFMerchantId;
        if (isset($details->ClientEmail))
            $this->ClientEmail = $details->ClientEmail;
        if (isset($details->OperationId))
            $this->OperationId = $details->OperationId;
        if (isset($details->Phone))
            $this->Phone = $details->Phone;
        if (isset($details->ProcessedDate))
            $this->ProcessedDate = $details->ProcessedDate;
        if (isset($details->Source))
            $this->Source = $details->Source;
        if (isset($details->OperationId))
            $this->OperationId = $details->OperationId;
        if (isset($details->RequestDate))
            $this->RequestDate = $details->RequestDate;
        if (isset($details->Status))
            $this->Status = $details->Status;
        if (isset($details->StatusDescription))
            $this->StatusDescription = $details->StatusDescription;
    }

    /*
    * New | Success | Unsuccess
    */
    function get_Status() {
        return $this->Status;
    }

    function get_StatusDescription() {
        return $this->StatusDescription;
    }

    function get_OperationId() {
        return $this->OperationId;
    }

    function get_StatusDescriptionDetail() {
        return $this->StatusDescriptionDetail;
    }

    function get_Amount() {
        return $this->Amount;
    }

    function get_Phone() {
        return $this->Phone;
    }

    function get_RequestDate() {
        return $this->RequestDate;
    }

    function get_ProcessedDate() {
        return $this->ProcessedDate;
    }

}
