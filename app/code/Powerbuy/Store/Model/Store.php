<?php
namespace Powerbuy\Store\Model;
class Store extends \Magento\Framework\Model\AbstractModel implements \Powerbuy\Store\Api\Data\StoreInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'powerbuy_store_store';

    protected function _construct()
    {
        $this->_init('Powerbuy\Store\Model\ResourceModel\Store');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
