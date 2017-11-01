<?php
/**
 * Created by PhpStorm.
 * User: ruang
 * Date: 11/1/17
 * Time: 11:56 AM
 */

namespace Powerbuy\Search\Model;

use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Search\Request\Builder;
use Powerbuy\Search\Api\SearchInterface;
use Magento\Framework\Search\SearchEngineInterface;
use Magento\Framework\Search\SearchResponseBuilder;
use Magento\Catalog\Api\ProductRepositoryInterface;


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

    private $productRepo;

    /**
     * @param Builder $requestBuilder
     * @param ScopeResolverInterface $scopeResolver
     * @param SearchEngineInterface $searchEngine
     * @param SearchResponseBuilder $searchResponseBuilder
     */
    public function __construct(
        Builder $requestBuilder,
        ScopeResolverInterface $scopeResolver,
        SearchEngineInterface $searchEngine,
        SearchResponseBuilder $searchResponseBuilder,
        ProductRepositoryInterface $productRepository
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->scopeResolver = $scopeResolver;
        $this->searchEngine = $searchEngine;
        $this->searchResponseBuilder = $searchResponseBuilder;
        $this->productRepo = $productRepository;
    }


    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return array
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

        $result = array();

        if($response->getTotalCount() > 0) {
            foreach ($response->getItems() as $item) {
                $product = $this->productRepo->getById($item->getId());
                $product_detail = [
                    'sku' => $product->getSku(),
                    'name' => $product->getName(),
                    'extension_attributes' => $product->getExtensionAttributes(''),
                    'custom_attributes' => $product->getCustomAttribute('image')
                ];
                $result[] = $product_detail;
          }
        }

        return $result;
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