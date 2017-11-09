<?php
/**
 * Created by PhpStorm.
 * User: ruang
 * Date: 11/8/17
 * Time: 2:20 PM
 */

namespace Powerbuy\Compare\Model;


use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Catalog\Model\Product\Attribute\SetRepository;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Webapi\Rest\Request;
use Powerbuy\Compare\Api\CompareRepositoryInterface;

class CompareRepository implements CompareRepositoryInterface
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var SetRepository
     */
    private $setRepository;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var Repository
     */
    private $repository;

    /**
     * CompareRepository constructor.
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param SetRepository $setRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Repository $repository
     */
    public function __construct(
        Request $request,
        ProductRepository $productRepository,
        SetRepository $setRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Repository $repository
    )
    {
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->setRepository = $setRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function get()
    {
        $requestParams = $this->request->getParams();

        $products = $this->getProductList($requestParams['sku']);

        $skus_array = explode(',', $requestParams['sku']);
        $product_list = array();
        $spec_header = array();
        foreach ($products->getItems() as $product)
        {
            //$product = $this->productRepository->get($sku);
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
            //$spec = array();
            foreach ($searchResults->getItems() as $item)
            {

                $spec_header [] = [
                    'frontend_label' => $item['frontend_label'],
                    'attribute_code' => $item['attribute_code']
                ];
            }

            //$product_list[] = $product;
        }
        $spec_header = array_unique($spec_header, SORT_REGULAR);


        $result = [
            $products->getItems(),
            "compare" => $this->getAttributeCompare($spec_header, $product_list)
        ];

        return $result;
    }

    /**
     * @param $skus
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function getProductList($skus)
    {
        $filters = array();
        $filters[] = $this->filterBuilder
            ->setField('sku')
            ->setValue($skus)
            ->setConditionType("in")
            ->create();

//        $filters[] = $this->filterBuilder
//            ->setField('in_stores')
//            ->setValue("00010")
//            ->setConditionType("finset")
//            ->create();

        $this->searchCriteriaBuilder->addFilters($filters);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->productRepository->getList($searchCriteria);
        return $searchResults;
    }

    public function getAttributeCompare($spec_header ,$product_list)
    {
        $result = array();
        foreach ($spec_header as $spec)
        {

            $head = [
                "compare_header" => $spec['frontend_label'],
                "compare_item" => $this->getValueAttribute($product_list ,$spec['attribute_code'])
            ];
            $result[] = $head;
        }
        return $result;
    }

    public function getValueAttribute($product_list ,$attribute_code)
    {
        $result = array();

        foreach ($product_list as $product)
        {
            $value = [
                'detail' => $product->getCustomAttribute($attribute_code)
            ];
            $result[] = $value;
        }

        return $result;
    }
}