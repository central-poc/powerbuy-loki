<?php
namespace Powerbuy\Search\Api;

use Magento\Framework\Api\Search\SearchCriteriaInterface;

interface SearchInterface
{

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function search(SearchCriteriaInterface $searchCriteria);
}
