<?php
namespace Powerbuy\Catalog\Api;

use Powerbuy\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;

interface CategoryRepositoryInterface 
{
    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getTree();
}
