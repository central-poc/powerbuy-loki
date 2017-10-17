<?php
namespace Powerbuy\ProductMaster\Api\Data;

use Magento\Framework\ObjectManagerInterface;

/**
 * Factory class for @see \Powerbuy\ProductMaster\Api\Data\ProductMasterInterface
 */
class ProductMasterInterfaceFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = '\\Powerbuy\\ProductMaster\\Api\\Data\\ProductMasterInterface'
    )
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Powerbuy\ProductMaster\Api\Data\ProductMasterInterface
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
