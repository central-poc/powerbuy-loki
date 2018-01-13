<?php
namespace Powerbuy\ProductMaster\Api;

use Powerbuy\ProductMaster\Api\Data\ProductMasterInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface ProductMasterRepositoryInterface 
{
    /**
     * @param ProductMasterInterface $product
     * @return mixed
     */
    public function save(ProductMasterInterface $product);

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param $storeId
     * @param $sku
     * @return mixed
     */
    public function getByStoreAndSku($storeId, $sku);

    /**
     * @param SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * @param ProductMasterInterface $product
     * @return mixed
     */
    public function delete(ProductMasterInterface $product);

    /**
     * @param $id
     * @return mixed
     */
    public function deleteById($id);

    /**
     * @return mixed
     */
    public function getStoreIds();

    /**
     * @return bool
     */
    public function saveProductFromInterface();
}
