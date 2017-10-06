<?php
namespace Powerbuy\Catalog\Plugin\Magento\Catalog\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Powerbuy\Catalog\Model\ResourceModel\Product as ProductResource;

class ProductRepository
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
}
