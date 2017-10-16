<?php
namespace Powerbuy\Promotion\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

interface PromotionInterface extends ExtensibleDataInterface
{
    const PROMOTION_ID      = 'promotion_id';
    const PROMOTION_NUM     = 'promotion_num';
    const PROMOTION_NAME    = 'promotion_name';
    const PROMOTION_TYPE    = 'promotion_type';
    const START_DATE        = 'start_date';
    const END_DATE          = 'end_date';
    const STATUS            = 'status';
    const PRODUCT_SKU       = 'product_sku';

    public function getPromotionNum();
    public function  setPromotionNum($promoNum);

    public function getPromotionName();
    public function  setPromotionName($name);

    public function getPromotionType();
    public function  setPromotionType($type);

    public function getStartDate();
    public function  setStartDate($startDate);

    public function getEndDate();
    public function  setEndDate($endDate);

    public function getStatus();
    public function  setStatus($status);

    public function getProductSKU();
    public function  setProductSKU($sku);

}