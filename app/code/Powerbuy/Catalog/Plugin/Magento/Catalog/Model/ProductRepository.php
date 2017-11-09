<?php
namespace Powerbuy\Catalog\Plugin\Magento\Catalog\Model;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Powerbuy\Catalog\Model\ResourceModel\Product as ProductResource;
use Powerbuy\Promotion\Model\ResourceModel\Promotion as PromotionResource;

class ProductRepository
{

    private $restRequest;

    protected $eavConfig;
    /**
     * @var ProductExtensionFactory
     */
    private $extensionFactory;

    /**
     * ProductRepository constructor.
     * @param RestRequest $restRequest
     * @param ProductResource $productResource
     * @param PromotionResource $promotionResource
     * @param Config $eavConfig
     * @param ProductExtensionFactory $extensionFactory
     */
    public function __construct(
        RestRequest $restRequest,
        ProductResource $productResource,
        PromotionResource $promotionResource,
        Config $eavConfig,
        ProductExtensionFactory $extensionFactory
    )
    {
        $this->restRequest = $restRequest;
        $this->productResource = $productResource;
        $this->promotionresource = $promotionResource;
        $this->eavConfig = $eavConfig;
        $this->extensionFactory = $extensionFactory;
    }

    
    function beforeGetList($subject,  $result)
    {
        $branchId = 0;
        foreach ($result->getFilterGroups() as $group) {
            foreach ($group->getFilters() as $filter) {
                $conditionType = $filter->getConditionType();
                if ($filter->getField() == 'in_stores') {
                    $branchId = $filter->getValue();
                    $this->setStoreCode($branchId);
                    $attribute = $this->eavConfig->getAttribute('catalog_product', 'in_stores');
                    $options = $attribute->getSource()->getAllOptions();
                    foreach ($options as $option) {
                        if ($option['label'] == $branchId) {
                            $filter->setValue($option['value']);
                        }
                    }
                } else if ($filter->getField() == 'barcode') {
                    $barcode = $filter->getValue();
                    $sku = $this->productResource->getProductByBarcode($barcode, $branchId);
                    $filter->setField('sku');
                    $filter->setValue($sku['sku']);
                }
            }
        }

        return [$result];
    }


    function afterGetList($subject,  $result)
    {
        foreach ($result->getItems() as $product) {
            $this->setExtensionProductDescription($product);
            $this->setExtensionProductImage($product);
            $this->setExtensionProductByStore($product, $this->storeCode);
        }

        return $result;
    }

    function afterGet($subject, ProductInterface $result)
    {
        $requestParams = $this->restRequest->getParams();
        if (array_key_exists('branch_id', $requestParams)) {
            $storeId = $requestParams['branch_id'];

        }else{
            $storeId = '00099';
        }
        $this->setExtensionProductDescription($result);
        $this->setExtensionProductImage($result);
        $this->setExtensionProductByStore($result, $storeId);
        $this->setExtensionPromotionByProduct($result);
        $this->setExtensionPromotionPaymentByProduct($result);
        return $result;
    }

    private function setExtensionProductDescription(ProductInterface $product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->extensionFactory->create();
        }
        $attr_description = $product->getCustomAttribute('description');
        $description = '';
        if($attr_description != null)
        {
            $description = $attr_description->getValue();
        }
        $extensionAttributes->setDescription($description);
        $product->setExtensionAttributes($extensionAttributes);
        return $product;
    }

    private function setExtensionProductImage(ProductInterface $product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->extensionFactory->create();
        }
        $attr_image = $product->getCustomAttribute('image');
        $image = '';
        if($attr_image != null)
        {
            $image = $attr_image->getValue();
        }
        $extensionAttributes->setImage($image);
        $product->setExtensionAttributes($extensionAttributes);
        return $product;
    }

    private function setExtensionProductByStore(ProductInterface $product, $storeId)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->extensionFactory->create();
        }

        $storeDetail = $this->productResource->getProductByStore($product['sku'], $storeId);
        $extensionAttributes->setByStore($storeDetail);

        $product->setExtensionAttributes($extensionAttributes);
        return $product;
    }

    private function setExtensionPromotionByProduct(ProductInterface $product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->extensionFactory->create();
        }
        $promotion = $this->promotionresource->getPromotionByProduct($product['sku']);
        $extensionAttributes->setPromotion($promotion);
        $product->setExtensionAttributes($extensionAttributes);
        return $product;
    }

    private function setExtensionPromotionPaymentByProduct(ProductInterface $product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->extensionFactory->create();
        }
        $promotion = $this->promotionresource->getPromotionPaymentByProduct($product['sku']);
        $extensionAttributes->setPayment($promotion);
        $product->setExtensionAttributes($extensionAttributes);
        return $product;
    }

//    private function setExtensionAttribute(ProductInterface $product)
//    {
//        $extensionAttributes = $product->getExtensionAttributes();
//        if (empty($extensionAttributes)) {
//            $extensionAttributes = $this->extensionFactory->create();
//        }
//        $attributes = $product->getCustomAttributes();
//        $attribute_compare = null;
//        foreach ($attributes as $attribute)
//        {
//            if($attribute->)
//        }
//
//        $extensionAttributes->setPayment($attribute_compare);
//        $product->setExtensionAttributes($extensionAttributes);
//        return $product;
//    }

    function setStoreCode($storeCode)
    {
        $this->storeCode = $storeCode;
    }
}
