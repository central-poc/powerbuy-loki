<?php
namespace Powerbuy\Store\Model;

use Powerbuy\Store\Api\StoreRepositoryInterface;
use Powerbuy\Store\Api\Data\StoreInterface;
use Powerbuy\Store\Model\StoreFactory;
use Powerbuy\Store\Model\ResourceModel\Store\CollectionFactory;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Powerbuy\Store\Model\ResourceModel\Store;
class StoreRepository implements \Powerbuy\Store\Api\StoreRepositoryInterface
{
    protected $objectFactory;
    protected $collectionFactory;
    public function __construct(
        StoreFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        Store $resourceStore      
    )
    {
        $this->objectFactory        = $objectFactory;
        $this->collectionFactory    = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resourceStore = $resourceStore;
    }
    
    /**
     * @param string $sku
     * @return array
     * @throws NoSuchEntityException
     */
     public function get($sku)
     {
         $result = array();
 
         $allStore = $this->resourceStore->getAvailableStock($sku);
         if(!$allStore)
         {
             throw new NoSuchEntityException(__('Don\'t have stocks.' ));
         }
 
         foreach ($allStore as $store)
         {
             $storeItem = [
                 'store_code' => $store['StoreCode'],
                 'name' => $store['Name'],
                 'address' => $store['Address'],
                 'open_time' => $store['OpenTime'],
                 'telephone' => $store['Telephone'],
                 'email' => $store['Email'],
                 'stock_available' => $store['stock_available']
             ];
             $result[] = $storeItem;
         }
 
         return $result;
     }

    /**
     * @return array
     */
    public function getList()
     {
         $result = array();
         $allStore = $this->resourceStore->getAllStore();
         foreach ($allStore as $store)
         {
             $storeItem = [
                 'store_code' => $store['StoreCode'],
                 'name' => $store['Name'],
                 'address' => $store['Address'],
                 'open_time' => $store['OpenTime'],
                 'telephone' => $store['Telephone'],
                 'email' => $store['Email'],
             ];
             $result[] = $storeItem;
         }

         return $result;
     }
}
