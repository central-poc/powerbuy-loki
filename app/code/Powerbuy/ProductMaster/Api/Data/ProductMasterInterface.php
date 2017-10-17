<?php
namespace Powerbuy\ProductMaster\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;
use \Magento\Catalog\Api\Data\ProductTypeExtensionInterface;

interface ProductMasterInterface extends ExtensibleDataInterface
{

    const ENTITY_ID             = 'entity_id';
    const SKU                   = 'sku';
    const STORE_ID              = 'store_id';
    const BARCODE               = 'barcode';
    const PRICE                 = 'price';
    const SPECIAL_PRICE         = 'special_price';
    const SPECIAL_PRICE_FROM    = 'special_price_from';
    const SPECIAL_PRICE_TO      = 'special_price_to';
    const STOCK_AVAILABLE       = 'stock_available';
    const STOCK_ON_HAND         = 'stock_on_hand';
    const CREATION_TIME         = 'creation_time';
    const UPDATE_TIME           = 'update_time';

    /**
     * Get SKU
     *
     * @return string
     */
    public function getSku();
    /**
     * Set SKU
     *
     * @param string sku
     * @return $this
     */
    public function setSku($sku);
    /**
     * Get store id
     *
     * @return string
     */
    public function getStoreId();
    /**
     * Set product store id
     *
     * @param string $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get barcode
     *
     * @return string
     */
    public function getBarcode();
    /**
     * Set Barcode
     *
     * @param string $barcode
     * @return $this
     */
    public function setBarcode($barcode);
    /**
     * Get Price
     *
     * @return float $price
     */
    public function getPrice();
    /**
     * Set Price
     *
     * @param float $value
     * @return $this
     */
    public function setPrice($value);

    /**
     * Get Special Price
     *
     * @return float $price
     */
    public function getSpecialPrice();
    /**
     * Set Special Price
     *
     * @param float $value
     * @return $this
     */
    public function setSpecialPrice($value);

    /**
     * Get special price from
     *
     * @return string|null
     */
    public function getSpecialPriceFrom();
    /**
     * Set special price from
     *
     * @param string $specialPriceFrom
     * @return $this
     */
    public function setSpecialPriceFrom($specialPriceFrom);

    /**
     * Get special price to
     *
     * @return string|null
     */
    public function getSpecialPriceTo();
    /**
     * Set special price to
     *
     * @param string $specialPriceTo
     * @return $this
     */
    public function setSpecialPriceTo($specialPriceTo);

    /**
     * Get Stock Available
     *
     * @return integer $stock
     */
    public function getStockAvailable();
    /**
     * Set Stock Available
     *
     * @param integer $stock
     * @return $this
     */
    public function setStockAvailable($stock);

    /**
     * Get Stock On Hand
     *
     * @return integer $stock
     */
    public function getStockOnHand();
    /**
     * Set Stock On Hand
     *
     * @param integer $stock
     * @return $this
     */
    public function setStockOnHand($stock);

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();
    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return $this
     */
    public function setCreationTime($creationTime);

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return $this
     */
    public function setUpdateTime($updateTime);


    /**
     * Retrieve existing extension attributes object.
     *
     * @return \Magento\Catalog\Api\Data\ProductTypeExtensionInterface|null
     */
    public function getExtensionAttributes();
    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Catalog\Api\Data\ProductTypeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        ProductTypeExtensionInterface $extensionAttributes
    );
}