<?php
namespace Powerbuy\Catalog\Plugin\Magento\Catalog\Model;

use Magento\Eav\Model\Config;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Powerbuy\Catalog\Model\ResourceModel\Product as ProductResource;
use Powerbuy\Promotion\Model\ResourceModel\Promotion as PromotionResource;

class ProductRepository
{

    private $restRequest;

    protected $eavConfig;

    public function __construct(
        RestRequest $restRequest,
        ProductResource $productResource,
        PromotionResource $promotionResource,
        Config $eavConfig
    )
    {
        $this->restRequest = $restRequest;
        $this->productResource = $productResource;
        $this->promotionresource = $promotionResource;
        $this->eavConfig = $eavConfig;
    }

    function beforeGetList($subject, $result)
    {
        $branchId;
        foreach ($result->getFilterGroups() as $group) {
            foreach ($group->getFilters() as $filter)
            {
                $conditionType = $filter->getConditionType();
                if ($filter->getField() == 'in_stores') {
                    $branchId = $filter->getValue();
                    $attribute = $this->eavConfig->getAttribute('catalog_product', 'in_stores');
                    $options = $attribute->getSource()->getAllOptions();
                    foreach ($options as $option)
                    {
                        if($option['label'] == $branchId)
                        {
                            $filter->setValue($option['value']);
                        }
                    }
                }
                else if($filter->getField() == 'barcode'){
                    $barcode = $filter->getValue();
                    $sku = $this->productResource->getProductByBarcode($barcode, $branchId);
                    $filter->setField('sku');
                    $filter->setValue($sku['sku']);
                }
            }
        }

        return [$result];
    }

    function afterGetList($subject, $result)
    {
        $searchProduct =  $this->restRequest->getParams();
        $storeId = $searchProduct['branch_id'];

        foreach ($result->getItems() as $product)
        {
            $this->setExtensionProductByStore($product, $storeId);
        }

        return $result;
    }

    function afterGet($subject, $result)
    {
        $searchProduct =  $this->restRequest->getParams();
        $storeId = $searchProduct['branch_id'];
        $this->setExtensionProductByStore($result, $storeId);
        $this->setExtensionPromotionByProduct($result);
        return $result;
    }

    private function setExtensionProductByStore($product, $storeId)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->productExtensionFactory->create();
        }

        $storeDetail = $this->productResource->getProductByStore($product['sku'], $storeId);
        $extensionAttributes->setByStore($storeDetail);

        $product->setExtensionAttributes($extensionAttributes);
        return $product;
    }

    private function setExtensionPromotionByProduct($product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if(empty($extensionAttributes)){
            $extensionAttributes = $this->productExtensionFactory->create();
        }
        $promotion = $this->promotionresource->getPromotionByProduct($product['sku']);
        $extensionAttributes->setPromotion($promotion);
        $product->setExtensionAttributes($extensionAttributes);
        return $product;
    }
}
