<?php
namespace Powerbuy\Store\Model\ResourceModel;
class Store extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('powerbuy_store_store','powerbuy_store_store_id');
    }

    function getAvailableStock($sku)
    {
        $connection = $this->getConnection();

        $query = 'SELECT store.*, prodmaster.stock_available
                  FROM powerbuy_store_store store 
                        INNER JOIN powerbuy_productmaster_productmaster prodmaster ON prodmaster.store_id = store.StoreCode
                  WHERE prodmaster.stock_available > 0 AND prodmaster.sku = ' . $sku ;

        return $connection->fetchAll($query);
    }
}
