<?php
namespace Powerbuy\Promotion\Model\ResourceModel\Promotion;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Powerbuy\Promotion\Model\Promotion','Powerbuy\Promotion\Model\ResourceModel\Promotion');
    }
}
