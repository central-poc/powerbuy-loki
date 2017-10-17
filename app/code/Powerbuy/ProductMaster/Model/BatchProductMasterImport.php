<?php

namespace Powerbuy\ProductMaster\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request as RestRequest;

use Powerbuy\ProductMaster\Api\BatchProductMasterImportInterface;
use Powerbuy\ProductMaster\Api\Data\ProductMasterInterfaceFactory;
use Powerbuy\ProductMaster\Api\ProductMasterRepositoryInterface;
use Powerbuy\ProductMaster\Helper\Attribute;

class BatchProductMasterImport implements BatchProductMasterImportInterface
{
    /**
     * Website id to import product to
     */
    const XML_POWERBUY_PRODUCTIMPORT_WEBSITE_ID = 'powerbuy_productmaster/productmasterimport/website_id';

    /**
     * Set product status as
     */
    const XML_POWERBUY_PRODUCTIMPORT_PRODUCT_STATUS = 'powerbuy_productmaster/productmasterimport/product_status';


    /**
     * Store id to use as online store price
     */
    const XML_POWERBUY_PRODUCTIMPORT_ONLINE_STORE_PRICE_STORE_ID = 'powerbuy_productmaster/productmasterimport/online_store_id';

    /**
     * Store id to use as in store price
     */
    const XML_POWERBUY_PRODUCTIMPORT_IN_STORE_PRICE_STORE_ID = 'powerbuy_productmaster/productmasterimport/in_store_price_store_id';


    /**
     * Default Payment ID
     */
    const XML_POWERBUY_PRODUCTIMPORT_DEFAULT_PAYMENT_ID = 'powerbuy_productmaster/productmasterimport/default_payment_method_id';


    /**
     * Default Delivery ID
     */
    const XML_POWERBUY_PRODUCTIMPORT_DEFAULT_DELIVERY_ID = 'powerbuy_productmaster/productmasterimport/default_delivery_method_id';

    /**
     * @var \Magento\Catalog\Api\Data\ProductInterfaceFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    private $restRequest;

    /**
     * @var \Powerbuy\ProductMaster\Api\Data\ProductInterfaceFactory
     */
    private $productMasterFactory;

    /**
     * @var \Powerbuy\ProductMaster\Api\ProductRepositoryInterface
     */
    private $productMasterRepository;
    /**
     * @var \Powerbuy\ProductMaster\Helper\Attribute
     */
    private $attrbuteHelper;

    /**
     * @var integer
     */

    private $defaultAttributeSetId;
    /**
     * @var ProductInterface
     */
    private $catalogProduct;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var integer
     */
    private $defaultWebsiteId;

    /**
     * @var integer
     */
    private $defaultProductStatusId;

    /**
     * @var integer
     */
    private $onlineStorePriceUseStoreId;

    /**
     * @var integer
     */
    private $inStorePriceUseStoreId;


    /**
     * @var integer
     */
    private $defaultPaymentMethodId;

    /**
     * @var integer
     */
    private $defaultDeliveryMethodId;

    /**
     * BatchProductMasterImport constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductInterface $catalogProduct
     * @param \Magento\Framework\Webapi\Rest\Request $restRequest
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Powerbuy\ProductMaster\Api\Data\ProductMasterInterfaceFactory $productMasterFactory
     * @param \Powerbuy\ProductMaster\Api\ProductMasterRepositoryInterface $productMasterRepository
     * @param Attribute $attributeHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductInterface $catalogProduct,
        RestRequest $restRequest,
        ProductInterfaceFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        ProductMasterInterfaceFactory $productMasterFactory,
        ProductMasterRepositoryInterface $productMasterRepository,
        Attribute $attributeHelper
    ){
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->restRequest = $restRequest;
        $this->productMasterFactory = $productMasterFactory;
        $this->productMasterRepository = $productMasterRepository;
        $this->attrbuteHelper = $attributeHelper;
        $this->scopeConfig = $scopeConfig;
        $this->catalogProduct = $catalogProduct;

        $this->defaultWebsiteId = $this->scopeConfig->getValue(
            self::XML_POWERBUY_PRODUCTIMPORT_WEBSITE_ID,
            ScopeInterface::SCOPE_STORE);
        $this->defaultProductStatusId = $this->scopeConfig->getValue(
            self::XML_POWERBUY_PRODUCTIMPORT_PRODUCT_STATUS,
            ScopeInterface::SCOPE_STORE);
        $this->inStorePriceUseStoreId = $this->scopeConfig->getValue(
            self::XML_POWERBUY_PRODUCTIMPORT_IN_STORE_PRICE_STORE_ID,
            ScopeInterface::SCOPE_STORE);
        $this->onlineStorePriceUseStoreId = $this->scopeConfig->getValue(
            self::XML_POWERBUY_PRODUCTIMPORT_ONLINE_STORE_PRICE_STORE_ID,
            ScopeInterface::SCOPE_STORE);
        $this->defaultPaymentMethodId = $this->scopeConfig->getValue(
            self::XML_POWERBUY_PRODUCTIMPORT_DEFAULT_PAYMENT_ID,
            ScopeInterface::SCOPE_STORE);
        $this->defaultDeliveryMethodId = $this->scopeConfig->getValue(
            self::XML_POWERBUY_PRODUCTIMPORT_DEFAULT_DELIVERY_ID,
            ScopeInterface::SCOPE_STORE);
    }

    /**
     * Add or update product
     *
     * @api
     * @return int The sum of the SKUs.
     */
    public function import()
    {
        $result = [];
        $productItems = $this->restRequest->getBodyParams();
        foreach ($productItems as $index => $item) {
            $sku = array_key_exists('sku', $item) ? $item['sku'] : '';
            if (empty($sku)) {
                $result[] = $this->formatStatus($index, "", 'failed', 'sku is required');
                continue;
            }
            $errorMessage = $this->validateProduct(BatchProductMasterImportInterface::PRODUCT_ATTRIBUTE_MAP, $item);
            if ($errorMessage) {
                $result[] = $this->formatStatus($index, $item['sku'], 'failed', $errorMessage);
                continue;
            }
            $errorMessage = $this->validateProductStoreData(BatchProductMasterImportInterface::PRODUCT_PRICE_STOCK_ATTRIBUTE_MAP, $item);
            if ($errorMessage) {
                $result[] = $this->formatStatus($index, $item['sku'],'failed', $errorMessage);
                continue;
            }

            $error = $this->createProductMaster($item);
            if ($error) {
                $result[] = $this->formatStatus($index, $item['sku'],'failed', $error);
                continue;
            }
            $error = $this->createProduct($item);
            if ($error) {
                $result[] = $this->formatStatus($index, $item['sku'],'failed', $error);
                continue;
            }

            $result[] = $this->formatStatus($index, $item['sku'],'success', '');
        }
        return $result;
    }

    /**
     * @param $data
     * @return string
     */
    public function createProductMaster($data)
    {
        $storeProducts = $data['stores'];
        if ($storeProducts) {
            foreach ($storeProducts as $productData) {
                $productData['sku'] = $data['sku'];
                try {
                    $product = $this->productMasterRepository->getByStoreAndSku(
                        $productData['store_id'],
                        $data['sku']
                    );
                } catch (NoSuchEntityException $e) {
                    $product = $this->productMasterFactory->create();

                }

                array_walk($productData, function($v, $k) use( $product){
                    return $product->setData($k, $v);
                });

                $this->productMasterRepository->save($product);
            }
        }
    }

    /**
     * @param $data
     * @return string
     */
    public function createProduct($data)
    {
        $productData = $data;
        $productData = array_merge($productData, $this->getPriceData($data));
        $productData = array_merge($productData, $this->getInStorePriceData($data));
        $productData = array_merge($productData, $this->getRequiredFields());
        if (array_key_exists('brand', $productData)) {
            $productData['brand'] =
                $this->attrbuteHelper->createOrGetId('brand', $productData['brand']);
        }

        unset($productData['stores']);
        $isUpdate = false;
        try {
            $product = $this->productRepository->get($data['sku']);
            unset($productData['sku']);
            $isUpdate = true;
        } catch (NoSuchEntityException $e) {
            $product = $this->productFactory->create();
            $product->setWebsiteIds([$this->defaultWebsiteId]);
            $product->setPaymentMethod([$this->defaultPaymentMethodId]);
            $product->setDeliveryMethod([$this->defaultDeliveryMethodId]);
            $product->setAttributeSetId($product->getDefaultAttributeSetId());
            $product->setStatus($this->defaultProductStatusId);
            $product->setTypeId('simple');
        }

        array_walk($productData, function($v, $k) use( $product){
            return $product->setData($k, $v);
        });
        try {
            $this->productRepository->save($product);
            if ($isUpdate) {
                //force update special price from/to
                $product->getResource()->saveAttribute($product, 'special_price');
                $product->getResource()->saveAttribute($product, 'special_from_date');
                $product->getResource()->saveAttribute($product, 'special_to_date');
            }
        } catch (AlreadyExistsException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $index
     * @param $sku
     * @param $status
     * @param $message
     * @return array
     */
    private function  formatStatus($index, $sku, $status, $message) {
        return [
            'index' => $index,
            'sku' => $sku,
            'status' => $status,
            'error' => $message
        ];
    }

    /**
     * @param $data
     * @return array
     */
    private function getPriceData($data)
    {
        $onlineStoreId = $this->onlineStorePriceUseStoreId;
        $onlineStores = array_filter($data['stores'], function($store) use ($onlineStoreId){
            return $store['store_id'] == $this->onlineStorePriceUseStoreId;
        });

        if ($onlineStores) {
            $onlineStore = array_values($onlineStores)[0];
            $priceData = [
                'price' => 0,
                'special_price' => 0,
                'special_price_from' => 0,
                'special_price_to' => 0
            ];

            foreach ($priceData as $k => $v) {
                if (array_key_exists($k, $onlineStore)) {
                    $priceData[$k] = $onlineStore[$k];
                }
            }
            $priceData['special_from_date'] = $priceData['special_price_from'];
            $priceData['special_to_date'] = $priceData['special_price_to'];
            unset($priceData['special_price_from']);
            unset($priceData['special_price_to']);
            return $priceData;
        }
        return [];
    }

    /**
     * @param $data
     * @return array
     */
    private function getInStorePriceData($data)
    {
        $inStorePriceStoreId = $this->inStorePriceUseStoreId;
        $stores = array_filter($data['stores'], function($store) use ($inStorePriceStoreId){
            return $store['store_id'] == $inStorePriceStoreId;
        });

        if ($stores) {
            return [
                'store_price' => array_values($stores)[0]['price'],
            ];
        }
        return [];
    }

    /**
     * @param array $map
     * @param $data
     * @return string
     */
    private function validateProduct(array $map, $data)
    {
        return $this->validate($map, $data);
    }

    /**
     * @param $map
     * @param $data
     * @return string
     */
    private function validateProductStoreData($map, $data)
    {
        if (!array_key_exists("stores", $data)) {
            return 'price and stock data are required';
        }
        $results = [];
        foreach ($data['stores'] as $index => $item) {
            $message = $this->validate($map, $item);
            if ($message) {
                $results[] = "store data at {$index}, {$message}";
            }
        }
        if ($results) {
            return implode(', ', $results);
        }
        return '';

    }

    /**
     * @param $map
     * @param $data
     * @return string
     */
    private function validate($map, $data)
    {
        $requireFieldMap = array_filter($map, function($item) {
            return $item['required'] == true;
        });

        $missingRequireFieldKeys = array_diff_key($requireFieldMap, $data);

        $emptyRequireFieldKeys = array_filter(array_keys($requireFieldMap), function ($k) use ($data){
            return empty($data[$k]);
        });

        $invalidRequiredFields = array_merge($missingRequireFieldKeys, array_flip($emptyRequireFieldKeys));

        if ($invalidRequiredFields) {
            $verb =  count($invalidRequiredFields) == 1 ? ' is ' : ' are ';
            return implode(", ", array_keys($invalidRequiredFields)) . $verb  . 'required';
        }
        return '';
    }

    private function getRequiredFields()
    {
        return array(
            'one_year_warranty' => false,
            'free_gift' => false,
            'free_installation' => false,
            'free_delivery' => false,
            'easy_payment' => false,
            'click_collect' => false
        );
    }
}

