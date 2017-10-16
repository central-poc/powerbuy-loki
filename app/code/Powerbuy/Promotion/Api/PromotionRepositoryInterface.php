<?php
namespace Powerbuy\Promotion\Api;

use Powerbuy\Promotion\Api\Data\PromotionInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;

interface PromotionRepositoryInterface 
{
    public function save(PromotionInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(PromotionInterface $page);

    public function deleteById($id);
}
