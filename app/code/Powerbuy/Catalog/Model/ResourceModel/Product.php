<?php

namespace Powerbuy\Catalog\Model\ResourceModel;

class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('powerbuy_productmaster_productmaster', 'entity_id');
    }


    /**
     * @param $sku
     * @param $branchId
     * @return mixed
     */
    public function getPrice($sku, $branchId)
    {
        $connection = $this->getConnection();

        $select = $connection
           ->select()
           ->from($this->getMainTable())
           ->where('sku = ?', $sku)
           ->where('store_id = ?', $branchId)
        ;

        $productPrice = $connection->fetchAll($select);
        return reset($productPrice);
    }

    public function getProductByStore($sku, $branchId)
    {
        $connection = $this->getConnection();

        $select = $connection
            ->select()
            ->from($this->getMainTable())
            ->where('sku = ?', $sku)
            ->where('store_id = ?', $branchId)
        ;

        return $connection->fetchAll($select);
    }

    public function getProductByBarcode($barcode, $branchId)
    {
        $connection = $this->getConnection();
        $select = $connection
           ->select()
           ->from($this->getMainTable())
           ->where('barcode = ?', $barcode)
           ->where('store_id = ?', $branchId)
        ;

        $productPrice = $connection->fetchAll($select);
        return reset($productPrice);
    }
}
