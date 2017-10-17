<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Powerbuy\ProductMaster\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Powerbuy\ProductMaster\Api\ProductMasterRepositoryInterface;

/**
 * @api
 * @since 100.0.2
 */
class PowerbuyStore implements ArrayInterface
{
    const DEFAULT_IN_STORE_PRICE_STORE_ID = 139;

    /**
     * @var array
     */
    protected $_options;

    /**
     * @var ProductMasterRepositoryInterface
     */
    private $productMasterRepository;


    /**
     * PowerbuyStore constructor.
     * @param ProductMasterRepositoryInterface $productMasterRepository
     */
    public function __construct(
        ProductMasterRepositoryInterface $productMasterRepository
    )
    {
        $this->productMasterRepository = $productMasterRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = [];
            $storeIds = $this->productMasterRepository->getStoreIds();
            if (!in_array(self::DEFAULT_IN_STORE_PRICE_STORE_ID, $storeIds)) {
                $storeIds[] = self::DEFAULT_IN_STORE_PRICE_STORE_ID; 
            }

            foreach ($storeIds as $id) {
                if ($id != 0) {
                    $this->_options[] = ['value' => $id, 'label' => $id];
                }
            }
        }
        return $this->_options;
    }
}