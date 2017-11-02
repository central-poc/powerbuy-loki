<?php
/**
 * Created by PhpStorm.
 * User: ruang
 * Date: 11/1/17
 * Time: 11:56 AM
 */

namespace Powerbuy\Search\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Search\Request\Builder;
use Powerbuy\Catalog\Model\ResourceModel\Product;
use Powerbuy\Search\Api\SearchInterface;
use Magento\Framework\Search\SearchEngineInterface;
use Magento\Framework\Search\SearchResponseBuilder;

class Search implements SearchInterface
{
    /**
     * @var Builder
     */
    private $requestBuilder;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var SearchEngineInterface
     */
    private $searchEngine;

    /**
     * @var SearchResponseBuilder
     */
    private $searchResponseBuilder;
    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    private $productSearchResultsInterfaceFactory;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var ProductFactory
     */
    private $productFactory;
    /**
     * @var ProductInterface
     */
    private $productInterface;
    /**
     * @var Product
     */
    private $productPWB;

    /**
     * @param Builder $requestBuilder
     * @param ScopeResolverInterface $scopeResolver
     * @param SearchEngineInterface $searchEngine
     * @param SearchResponseBuilder $searchResponseBuilder
     * @param ProductSearchResultsInterfaceFactory $productSearchResultsInterfaceFactory
     * @param ProductRepository $productRepository
     * @param ProductFactory $productFactory
     * @param ProductInterface $productInterface
     * @param Product $productPWB
     */
    public function __construct(
        Builder $requestBuilder,
        ScopeResolverInterface $scopeResolver,
        SearchEngineInterface $searchEngine,
        SearchResponseBuilder $searchResponseBuilder,

        ProductSearchResultsInterfaceFactory $productSearchResultsInterfaceFactory,
        ProductRepository $productRepository,
        ProductFactory $productFactory,
        ProductInterface $productInterface,
        Product $productPWB
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->scopeResolver = $scopeResolver;
        $this->searchEngine = $searchEngine;
        $this->searchResponseBuilder = $searchResponseBuilder;
        $this->productSearchResultsInterfaceFactory = $productSearchResultsInterfaceFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->productInterface = $productInterface;
        $this->productPWB = $productPWB;
    }


    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function search(SearchCriteriaInterface $searchCriteria)
    {
        $this->requestBuilder->setRequestName($searchCriteria->getRequestName());

        $scope = $this->scopeResolver->getScope()->getId();
        $this->requestBuilder->bindDimension('scope', $scope);

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $this->addFieldToFilter($filter->getField(), $filter->getValue());
            }
        }

        $this->requestBuilder->setFrom($searchCriteria->getCurrentPage() * $searchCriteria->getPageSize());
        $this->requestBuilder->setSize($searchCriteria->getPageSize());
        $request = $this->requestBuilder->create();
        $searchResponse = $this->searchEngine->search($request);
        $response = $this->searchResponseBuilder->build($searchResponse);

        $product_array = null;

        if($response->getTotalCount() > 0)
        {
            foreach ($response->getItems() as $item)
            {
                $product = $this->productRepository->getById($item->getId());
                $extensionAttributes = $product->getExtensionAttributes();
                if (empty($extensionAttributes)) {
                    $extensionAttributes = $this->productExtensionFactory->create();
                }

                $storeDetail = $this->productPWB->getProductByStore($product->getSku(), '00010');
                $extensionAttributes->setByStore($storeDetail);
                $product->setExtensionAttributes($extensionAttributes);

                $product_array[] = $product;
            }
        }


        $searchResult = $this->productSearchResultsInterfaceFactory->create();
        $searchResult->setTotalCount($response->getTotalCount());
        $searchResult->setItems($product_array);

        return $searchResult;
    }

    /**
     * Apply attribute filter to facet collection
     *
     * @param string $field
     * @param string|array|null $condition
     * @return $this
     */
    private function addFieldToFilter($field, $condition = null)
    {
        if (!is_array($condition) || !in_array(key($condition), ['from', 'to'], true)) {
            $this->requestBuilder->bind($field, $condition);
        } else {
            if (!empty($condition['from'])) {
                $this->requestBuilder->bind("{$field}.from", $condition['from']);
            }
            if (!empty($condition['to'])) {
                $this->requestBuilder->bind("{$field}.to", $condition['to']);
            }
        }

        return $this;
    }
}