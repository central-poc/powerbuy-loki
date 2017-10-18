<?php

namespace Powerbuy\ProductMaster\Api;

interface BatchProductMasterImportInterface
{
    const PRODUCT_ATTRIBUTE_MAP = array(
        'sku' => array(
            'required' => true,
            'create_attribute' => false
        ),
        'mms_id' => array(
            'required' => true,
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'MMS ID',
            'visible' => true,
            'create_attribute' => true
        ),
        'name' => array(
            'required' => true,
            'create_attribute' => false
        ),
        'model' => array(
            'required' => false,
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'Model',
            'visible' => true,
            'create_attribute' => true
        ),
        'store_price' => array(
            'required' => false,
            'type' => 'decimal',
            'input' => 'text',
            'label' => 'Store Price',
            'visible' => true,
            'create_attribute' => true
        ),
        'weight' => array(
            'required' => false,
            'type' => 'decimal',
            'input' => 'text',
            'label' => 'Weight',
            'visible' => true,
            'create_attribute' => true
        ),
        'length' => array(
            'required' => false,
            'type' => 'decimal',
            'input' => 'text',
            'label' => 'Length',
            'visible' => true,
            'create_attribute' => true
        ),
        'width' => array(
            'required' => false,
            'type' => 'decimal',
            'input' => 'text',
            'label' => 'Width',
            'visible' => true,
            'create_attribute' => true
        ),
        'height' => array(
            'required' => false,
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'Height',
            'visible' => true,
            'create_attribute' => true
        ),
        'brand' => array(
            'required' => false,
            'type' => 'varchar',
            'input' => 'select',
            'label' => 'Brand',
            'visible' => true,
            'create_attribute' => true
        ),
        'package_weight' => array(
            'required' => false,
            'type' => 'decimal',
            'input' => 'text',
            'label' => 'Package Weight',
            'visible' => true,
            'create_attribute' => true
        ),
        'package_length' => array(
            'required' => false,
            'type' => 'decimal',
            'input' => 'text',
            'label' => 'Package Length',
            'visible' => true,
            'create_attribute' => true
        ),
        'package_width' => array(
            'required' => false,
            'type' => 'decimal',
            'input' => 'text',
            'label' => 'Package Width',
            'visible' => true,
            'create_attribute' => true
        ),
        'package_height' => array(
            'required' => false,
            'type' => 'decimal',
            'input' => 'text',
            'label' => 'Package Height',
            'visible' => true,
            'create_attribute' => true
        ),
        'vendor_code' => array(
            'required' => false,
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'Vendor Code',
            'visible' => true,
            'create_attribute' => true
        ),
        'vendor_name' => array(
            'required' => false,
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'Vendor Name',
            'visible' => true,
            'create_attribute' => true
        )
    );

    const PRODUCT_PRICE_STOCK_ATTRIBUTE_MAP = array(
        'store_id' => array('required' => true),
        'barcode' => array('required' => false),
        'price' => array('required' => true),
        'special_price' => array('required' => false),
        'special_price_from' => array('required' => false),
        'special_price_to' => array('required' => false),
        'stock_available' => array('required' => true),
        'stock_on_hand' => array('required' => false)
    );
    /**
     * Add or update product master
     *
     * @api
     * @return int The sum of the SKUs.
     */
    public function import();
}
