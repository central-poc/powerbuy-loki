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
            ->where('? BETWEEN start_date AND end_date', date("Y-m-d"))
        ;
        return $connection->fetchAll($select);
    }
}
