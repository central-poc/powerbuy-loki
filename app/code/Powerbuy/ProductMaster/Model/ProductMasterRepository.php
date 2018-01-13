<?php
namespace Powerbuy\ProductMaster\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

use Powerbuy\ProductMaster\Api\Data\ProductMasterInterface;
use Powerbuy\ProductMaster\Api\ProductMasterRepositoryInterface;
use Powerbuy\ProductMaster\Model\ResourceModel\ProductMaster;
use Powerbuy\ProductMaster\Model\ResourceModel\ProductMaster\CollectionFactory;
use Powerbuy\Promotion\Helpers\ConnectSql;

class ProductMasterRepository implements ProductMasterRepositoryInterface
{

    protected $objectFactory;

    protected $collectionFactory;

    /**
     * @var \Powerbuy\ProductMaster\Model\ResourceModel\ProductMaster
     */
    private $resourceModel;

    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var ConnectSql
     */
    private $connectSql;

    public function __construct(
        ProductMaster $resourceModel,
        ProductMasterFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        ConnectSql $connectSql
    ){
        $this->resourceModel = $resourceModel;
        $this->objectFactory = $objectFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->connectSql = $connectSql;
    }
    
    public function save(ProductMasterInterface $object)
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

    /**
     * {@inheritdoc}
     */
    public function getByStoreAndSku($storeId, $sku)
    {
        $object = $this->objectFactory->create();
        $objectId = $this->resourceModel->getIdByStoreIdAndSku($storeId, $sku);
        if (!$objectId) {
            throw new NoSuchEntityException(__('Requested object doesn\'t exist'));
        }
        $object->load($objectId);
        return $object;
    }

    public function delete(ProductMasterInterface $object)
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

    public function getStoreIds()
    {
        return $this->resourceModel->getStoreIds();
    }

    /**
     * @return bool
     */
    public function saveProductFromInterface()
    {
        $conn = $this->connectSql->ConnectPWBLegacy();
        $storesId = [
            '00010','00011','00012','00027','00039','00096','00099','00131','00134','00171','00174','00991','00185'
       ];

        foreach ($storesId as $store)
        {
            $query = 'EXEC dbo.GetProductByStoreId @StoreId = \'' . $store .'\'' ;
            $result = sqlsrv_query($conn,$query);
            $item = array();
            if(sqlsrv_has_rows($result))
            {
                while ($row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC))
                {
                    $item[] = [
                        'sku' => $row['sku'],
                        'store_id' => $row['store_id'],
                        'barcode' => $row['barcode'],
                        'price' => $row['price'],
                        'special_price' => $row['special_price'],
                        'special_price_from' => $row['special_price_from'] ? $row['special_price_from']->format('Y-m-d') : $row['special_price_from'],
                        'special_price_to' => $row['special_price_to'] ? $row['special_price_to']->format('Y-m-d') : $row['special_price_to'],
                        'stock_available' => $row['stock_available'],
                        'stock_on_hand' => $row['stock_on_hand']
                    ];

                }
                $this->resourceModel->deleteAllByStore($store);

                $this->resourceModel->saveProductMultipleRow($item);
            }
        }
        return true;
    }
}
