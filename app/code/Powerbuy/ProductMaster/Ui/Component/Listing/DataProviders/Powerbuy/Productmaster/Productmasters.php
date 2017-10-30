<?php
namespace Powerbuy\ProductMaster\Ui\Component\Listing\DataProviders\Powerbuy\Productmaster;

class Productmasters extends \Magento\Ui\DataProvider\AbstractDataProvider
{    
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Powerbuy\ProductMaster\Model\ResourceModel\ProductMaster\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
