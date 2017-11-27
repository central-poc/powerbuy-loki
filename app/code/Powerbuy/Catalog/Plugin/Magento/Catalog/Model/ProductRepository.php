<?php

namespace Powerbuy\Catalog\Plugin\Magento\Catalog\Model;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\Catalog\Model\Product\Attribute\SetRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\Product\Attribute\Repository;

use Magento\TestFramework\Event\Magento;
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
     * @var AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * ProductRepository constructor.
     * @param RestRequest $restRequest
     * @param ProductResource $productResource
     * @param PromotionResource $promotionResource
     * @param Config $eavConfig
     * @param ProductExtensionFactory $extensionFactory
     * @param SetRepository $setRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Repository $repository
     * @param AttributeValueFactory $attributeValueFactory
     */
    public function __construct(
        RestRequest $restRequest,
        ProductResource $productResource,
        PromotionResource $promotionResource,
        Config $eavConfig,
        ProductExtensionFactory $extensionFactory,
        SetRepository $setRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Repository $repository,
        AttributeValueFactory $attributeValueFactory
    )
    {
        $this->restRequest = $restRequest;
        $this->productResource = $productResource;
        $this->promotionresource = $promotionResource;
        $this->eavConfig = $eavConfig;
        $this->extensionFactory = $extensionFactory;
        $this->setRepository = $setRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->repository = $repository;
        $this->attributeValueFactory = $attributeValueFactory;
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
            $this->setExtensionBrand($product);
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
        $this->setExtensionSpecifications($result);
        $this->setExtensionBrand($result);
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
            $image = "https://prod.powerbuy.co.th/media/catalog/product" . $image;
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

    private function setExtensionBrand(ProductInterface $product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->extensionFactory->create();
        }
        if($product->getCustomAttribute('brand') == null){
            $brand = "";
        }
        else {
            $brand = $product->getCustomAttribute('brand')->getValue();

            $attribute_brand = $this->eavConfig->getAttribute('catalog_product', 'brand');
            $options = $attribute_brand->getSource()->getAllOptions();
            foreach ($options as $option) {
                if ($option['value'] == $brand) {
                    $brand = $option['label'];
                    break;
                }
            }
        }

        $extensionAttributes->setBrand($brand);
        $product->setExtensionAttributes($extensionAttributes);
        return $product;
    }

    /**
     * @param ProductInterface $product
     * @return ProductInterface
     */
    private function setExtensionSpecifications(ProductInterface $product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->extensionFactory->create();
        }

        $set_name = $this->setRepository->get($product->getAttributeSetId())->getAttributeSetName();


        $obj = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Magento\Catalog\Model\Config $config */
        $config= $obj->get('Magento\Catalog\Model\Config');

        $attributeGroupId = $config->getAttributeGroupId($product->getAttributeSetId(), $set_name);

        $filters = array();
        $filters[] = $this->filterBuilder
            ->setField('attribute_group_id')
            ->setValue($attributeGroupId)
            ->create();
        $this->searchCriteriaBuilder->addFilters($filters);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->repository->getList($searchCriteria);

        $spec = array();

        $specifications = $searchResults->getItems();
        if($searchResults->getItems() != null) {
            foreach ($specifications as $specification) {

                $attribute_spec = $this->attributeValueFactory->create();
                $attribute_spec->setAttributeCode($specification->getData("frontend_label"));
                $attr_value = $product->getCustomAttribute($specification->getData('attribute_code'));
                if ($attr_value == null)
                    $attribute_spec->setValue("");
                else
                    $attribute_spec->setValue($attr_value->getValue());

                $spec[] = $attribute_spec;
            }
        }

        $extensionAttributes->setSpecifications($spec);
        $product->setExtensionAttributes($extensionAttributes);
        return $product;
    }

    function setStoreCode($storeCode)
    {
        $this->storeCode = $storeCode;
    }
}
