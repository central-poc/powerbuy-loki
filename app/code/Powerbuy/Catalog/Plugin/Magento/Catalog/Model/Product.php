<?php

namespace Powerbuy\Catalog\Plugin\Magento\Catalog\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Powerbuy\Catalog\Model\ResourceModel\Product as ProductResource;


class Product
{
    private $restRequest;

    public function __construct(
        RestRequest $restRequest,
        ProductResource $productResource
    )
    {
        $this->restRequest = $restRequest;
        $this->productResource = $productResource;
    }
    

    function afterGetPrice($subject, $result)
    {
        $newPrice = 0;
        $searchProduct =  $this->restRequest->getParams();

        $storeId = $searchProduct['store_id'];
        if(empty($storeId))
        {
            throw new NoSuchEntityException(__('store_id is reguest!!'));
        }else{
            $productDetail = $subject->getData();
            $sku = $productDetail['sku'];
            $productPrice = $this->productResource->getPrice($sku,$storeId);
            $newPrice = min($productPrice['price'], $productPrice['special_price']);
        }
        return $newPrice;
    }
}
