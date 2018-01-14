<?php
namespace Powerbuy\Promotion\Api;

use Powerbuy\Promotion\Api\Data\PromotionInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;

interface PromotionRepositoryInterface 
{
    /**
     * @param PromotionInterface $page
     * @return mixed
     */
    public function save(PromotionInterface $page);

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * @param PromotionInterface $page
     * @return mixed
     */
    public function delete(PromotionInterface $page);

    /**
     * @param $id
     * @return mixed
     */
    public function deleteById($id);

    /**
     * @return mixed
     */
    public function importPro();

}
