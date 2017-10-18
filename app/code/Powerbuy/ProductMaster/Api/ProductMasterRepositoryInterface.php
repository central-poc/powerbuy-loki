<?php
namespace Powerbuy\ProductMaster\Api;

use Powerbuy\ProductMaster\Api\Data\ProductMasterInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface ProductMasterRepositoryInterface 
{
    public function save(ProductMasterInterface $product);

    public function getById($id);

    public function getByStoreAndSku($storeId, $sku);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(ProductMasterInterface $product);

    public function deleteById($id);

    public function getStoreIds();
}
