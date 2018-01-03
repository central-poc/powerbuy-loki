<?php

namespace Powerbuy\Promotion\Model\ResourceModel;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Promotion extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('powerbuy_promotion_promotion','promotion_id');
    }

    /**
     * @param $sku
     * @return array
     */
    public function getPromotionByProduct($sku)
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->getMainTable())
            ->where('product_sku = ?', $sku)
            ->where('status = \'A\'')
            ->where('promotion_type IN ( \'I\',\'O\',\'P\' )')
            ->where('? BETWEEN start_date AND end_date', date("Y-m-d"))
        ;
        return $connection->fetchAll($select);
    }

    public function getPromotionPaymentByProduct($sku)
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->getMainTable())
            ->where('product_sku = ?', $sku)
            ->where('status = \'A\'')
            ->where('promotion_type IN ( \'B\' )')
            ->where('? BETWEEN start_date AND end_date', date("Y-m-d"))
        ;
        return $connection->fetchAll($select);
    }

    public function savePromotion($item)
    {
//        $connection = $this->getConnection();
//        $connection->insertMultiple($this->getMainTable(),$item);
        $connection = $this->getConnection();
        $connection->insertOnDuplicate(
            $this->getMainTable(),
            $item,
            ['promotion_name', 'promotion_type', 'start_date', 'end_date', 'status']
        );
        return $this;
    }

    public function deleteAll()
    {
        $connection = $this->getConnection();
        $query = "DELETE FROM " . $this->getMainTable();
        $connection->query($query);
    }
}
