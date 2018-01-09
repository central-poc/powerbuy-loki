<?php
namespace Powerbuy\Promotion\Model;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Powerbuy\Promotion\Api\Data\PromotionInterface;

class Promotion extends AbstractModel implements PromotionInterface, IdentityInterface
{
    const CACHE_TAG = 'powerbuy_promotion_promotion';

    /**
     * Promotion constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Powerbuy\Promotion\Model\ResourceModel\Promotion');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }


    public function getPromotionNum()
    {
        return $this->getData(self::PROMOTION_NUM);
    }

    public function setPromotionNum($promoNum)
    {
        return $this->setData(self::PROMOTION_NUM, $promoNum);
    }

    public function getPromotionName()
    {
        return $this->getData(self::PROMOTION_NAME);
    }
    public function  setPromotionName($name)
    {
        return $this->setData(self::PROMOTION_NAME, $name);
    }

    public function getPromotionType()
    {
        return $this->getData(self::PROMOTION_TYPE);
    }
    public function  setPromotionType($type)
    {
        return $this->setData(self::PROMOTION_TYPE, $type);
    }

    public function getStartDate()
    {
        return $this->getData(self::START_DATE);
    }
    public function  setStartDate($startDate)
    {
        return $this->setData(self::START_DATE, $startDate);
    }

    public function getEndDate()
    {
        return $this->getData(self::END_DATE);
    }
    public function  setEndDate($endDate)
    {
        return $this->setData(self::END_DATE, $endDate);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }
    public function  setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getProductSKU()
    {
        return $this->getData(self::PRODUCT_SKU);
    }
    public function  setProductSKU($sku)
    {
        return $this->setData(self::PRODUCT_SKU, $sku);
    }

    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }
}
