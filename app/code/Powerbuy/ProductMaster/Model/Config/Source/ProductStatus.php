<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Powerbuy\ProductMaster\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * @api
 * @since 100.0.2
 */
class ProductStatus implements ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;
    /**
     * @var Status
     */
    private $productStatus;


    /**
     * ProductStatus constructor.
     * @param Status $productStatus
     */
    public function __construct(
        Status $productStatus
    )
    {
        $this->productStatus = $productStatus;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->productStatus->getAllOptions();
    }
}