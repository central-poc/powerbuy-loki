<?php
namespace Powerbuy\Catalog\Model;
class Category extends \Magento\Framework\Model\AbstractModel implements \Powerbuy\Catalog\Api\Data\CategoryInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'powerbuy_catalog_category';

    protected function _construct()
    {
        $this->_init('Powerbuy\Catalog\Model\ResourceModel\Category');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
