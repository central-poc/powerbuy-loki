<?php
namespace Powerbuy\Catalog\Model\ResourceModel\Category;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Powerbuy\Catalog\Model\Category','Powerbuy\Catalog\Model\ResourceModel\Category');
    }
}
