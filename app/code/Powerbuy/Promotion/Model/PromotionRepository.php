<?php
namespace Powerbuy\Promotion\Model;

use \Powerbuy\Promotion\Helpers\ConnectSql;
use Powerbuy\Promotion\Api\Data\PromotionInterface;
use Powerbuy\Promotion\Api\PromotionRepositoryInterface;
use Powerbuy\Promotion\Model\ResourceModel\Promotion\CollectionFactory;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use \Powerbuy\Promotion\Model\ResourceModel\Promotion;
class PromotionRepository implements PromotionRepositoryInterface
{
    protected $objectFactory;
    protected $collectionFactory;
    protected $helper;
    protected $resourcePromotion;
    public function __construct(
        PromotionFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        ConnectSql $helper,
        Promotion $resourcePromotion

    )
    {
        $this->objectFactory        = $objectFactory;
        $this->collectionFactory    = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->helper = $helper;
        $this->resourcePromotion = $resourcePromotion;
    }
    
    public function save(PromotionInterface $object)
    {
        try
        {
            $object->save();
        }
        catch(\Exception $e)
        {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $object;
    }

    public function getById($id)
    {
        $object = $this->objectFactory->create();
        $object->load($id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }
        return $object;        
    }       

    public function delete(PromotionInterface $object)
    {
        try {
            $object->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;    
    }    

    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }    

    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);  
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }  
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $objects = [];                                     
        foreach ($collection as $objectModel) {
            $objects[] = $objectModel;
        }
        $searchResults->setItems($objects);
        return $searchResults;        
    }

    public function importPro()
    {
        $conn = $this->helper->ConnectDBInterface();
        $query = 'EXEC dbo.GetPromoTablet';
        $result = sqlsrv_query($conn,$query);

        if(sqlsrv_has_rows($result))
        {
            while ($row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC))
            {
                $item = [
                    'promotion_num' => $row['PMNum'],
                    'promotion_name' => $row['PMName'],
                    'promotion_type' => $row['PMType'],
                    'start_date' => $row['SDATE']->format('Y-m-d'),
                    'end_date' => $row['EDATE']->format('Y-m-d'),
                    'status' => $row['PMSTATUS'],
                    'product_sku' => $row['SKU'],
                    'store_id' => $row['STCODE']
                ];
                $this->resourcePromotion->savePromotion($item);
            }
        }
    }
}
