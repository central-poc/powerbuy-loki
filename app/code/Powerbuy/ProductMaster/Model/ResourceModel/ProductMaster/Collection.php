<?php
namespace Powerbuy\ProductMaster\Model\ResourceModel\ProductMaster;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Powerbuy\ProductMaster\Model\ProductMaster','Powerbuy\ProductMaster\Model\ResourceModel\ProductMaster');
    }
}
