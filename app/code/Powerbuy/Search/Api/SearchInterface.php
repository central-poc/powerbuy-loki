<?php
namespace Powerbuy\Search\Api;

use Magento\Framework\Api\Search\SearchCriteriaInterface;

interface SearchInterface
{

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return array
     */
    public function search(SearchCriteriaInterface $searchCriteria);
}
