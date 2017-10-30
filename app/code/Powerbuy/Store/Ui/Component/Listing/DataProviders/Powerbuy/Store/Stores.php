<?php
namespace Powerbuy\Store\Ui\Component\Listing\DataProviders\Powerbuy\Store;

class Stores extends \Magento\Ui\DataProvider\AbstractDataProvider
{    
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Powerbuy\Store\Model\ResourceModel\Store\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
