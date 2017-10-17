<?php
namespace Powerbuy\ProductMaster\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Catalog\Api\Data\ProductTypeExtensionInterface;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;

use \Powerbuy\ProductMaster\Api\Data\ProductMasterInterface;

class ProductMaster extends AbstractModel implements ProductMasterInterface, IdentityInterface
{
    const CACHE_TAG = 'powerbuy_productmaster_productmaster';

    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
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

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Powerbuy\ProductMaster\Model\ResourceModel\ProductMaster');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get product SKU
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * Set product SKU
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * Get product store id
     *
     * @return string
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Set product store id
     *
     * @param string $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get product Barcode
     *
     * @return string
     */
    public function getBarcode()
    {
        return $this->getData(self::BARCODE);
    }

    /**
     * Set product Barcode
     *
     * @param string $barcode
     * @return $this
     */
    public function setBarcode($barcode)
    {
        return $this->setData(self::BARCODE, $barcode);
    }

    /**
     * Get product Price
     *
     * @return float $price
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * Set product Price
     *
     * @param float $value
     * @return $this
     */
    public function setPrice($value)
    {
        return $this->setData(self::PRICE, $value);
    }

    /**
     * Get product Special Price
     *
     * @return float $price
     */
    public function getSpecialPrice()
    {
        return $this->getData(self::SPECIAL_PRICE);
    }

    /**
     * Set product Special Price
     *
     * @param float $value
     * @return $this
     */
    public function setSpecialPrice($value)
    {
        return $this->setData(self::SPECIAL_PRICE, $value);
    }

    /**
     * Get product Stock Available
     *
     * @return integer $stock
     */
    public function getStockAvailable()
    {
        return $this->getData(self::STOCK_AVAILABLE);
    }

    /**
     * Set product Stock Available
     *
     * @param integer $stock
     * @return $this
     */
    public function setStockAvailable($stock)
    {
        return $this->setData(self::STOCK_AVAILABLE, $stock);
    }

    /**
     * Get product Stock On Hand
     *
     * @return integer $stock
     */
    public function getStockOnHand()
    {
        return $this->getData(self::STOCK_ON_HAND);
    }

    /**
     * Set product Stock On Hand
     *
     * @param integer $stock
     * @return $this
     */
    public function setStockOnHand($stock)
    {
        return $this->setData(self::STOCK_ON_HAND, $stock);
    }

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return $this
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return $this
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Retrieve existing extension attributes object.
     *
     * @return \Magento\Catalog\Api\Data\ProductTypeExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        // TODO: Implement getExtensionAttributes() method.
    }

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Catalog\Api\Data\ProductTypeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        ProductTypeExtensionInterface $extensionAttributes
    )
    {
        // TODO: Implement setExtensionAttributes() method.
    }

    /**
     * Get special price from
     *
     * @return string|null
     */
    public function getSpecialPriceFrom()
    {
        return $this->getData(self::SPECIAL_PRICE_FROM);
    }

    /**
     * Set special price from
     *
     * @param string $specialPriceFrom
     * @return $this
     */
    public function setSpecialPriceFrom($specialPriceFrom)
    {
        return $this->setData(self::SPECIAL_PRICE_FROM, $specialPriceFrom);
    }

    /**
     * Get special price to
     *
     * @return string|null
     */
    public function getSpecialPriceTo()
    {
        return $this->getData(self::SPECIAL_PRICE_TO);
    }

    /**
     * Set special price to
     *
     * @param string $specialPriceTo
     * @return $this
     */
    public function setSpecialPriceTo($specialPriceTo)
    {
        return $this->setData(self::SPECIAL_PRICE_TO, $specialPriceTo);
    }
}
