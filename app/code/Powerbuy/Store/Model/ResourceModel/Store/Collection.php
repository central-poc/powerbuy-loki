<?php
namespace Powerbuy\Store\Model\ResourceModel\Store;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Powerbuy\Store\Model\Store','Powerbuy\Store\Model\ResourceModel\Store');
    }
}
