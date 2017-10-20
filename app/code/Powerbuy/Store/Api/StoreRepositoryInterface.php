<?php
namespace Powerbuy\Store\Api;

use Powerbuy\Store\Api\Data\StoreInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;

interface StoreRepositoryInterface 
{
    /**
     * @param string $sku
     * @return array
     * @throws NoSuchEntityException
     */
     public function get($sku);

    /**
     * @return mixed
     */
     public function getList();
}
