# README #

A Magento 2 module provides API to create product from product master from Powerbuy MMS system.

# Recommended Flow

1. Get the data Amazon SQS.
2. Transform data and call Batch Product Add / Update API. See API for more infomation. Creating product before minimizes updating stock fail unknown SKUs.
3. Update stock by calling MCOM. Stock source is COLPRCSTOCK.COLSTR and stock is COLPRCSTOCK.COLAVAIL. See Data Mapping.

# Installation

Please use composer to install the extension. 

    1. Add ssh public key to your bitbucket account.
    2. Contact nachatchai@central.co.th to grant your access.
    3. At your magento root, 

        * composer config repositories.powerbuyproductmaster git git@bitbucket.org:centraltechnology/powerbuy-productmaster.git
        * composer require powerbuy/productmaster:dev-master
        * php bin/magento module:enable Powerbuy_ProductMaster


# Configuration

* Stores/Configuration/Powerbuy/Product Import API

    * Import Product to Website - Set the website to which the product will belong to.
    * Import Product as Status - Default is Disabled.
    * Online Store Id - Default to 139 
    * In Store Price use Store Id - Product In store price store ID (Will be shown in product detail page)
    * Default Payment Method - Default is 2C2P Full Payment
    * Default Delivery Method - Default is PWB Standard
       
# API

## Batch Product Add / Update

Import product by creating or updating (if sku already exists).

##### URL
    
    /rest/V1/products/batchimport
    
##### Headers
    Authorization: Bearer <token>
    Content-Type: application/json
    
##### Method
    
    POST
    
#### Sample Parameter / Post Data

    [{
        "sku": "216849",
        "name": "ครื่องเล่น DVD PIONEER DV-2242",
        "model": "DV-2242",
        "mms_id": "10203040",
        "weight": 0,
        "length": 0,
        "width": 0,
        "height": 0,
        "brand": "PIONEER",
        "package_weight": "1.40",
        "package_length": "7.59",
        "package_width": "31.37",
        "package_height": "27.83",
        "vendor_code": "030503",
        "vendor_name": "CENTRAL TRADING CO.,LTD",
        "stores": [
            {
                "store_id": 139,
                "price": 250, 
                "special_price": 250,  
                "special_price_from": "2017-09-01",  
                "special_price_to": "2017-09-30", 
                "stock_available": 10,
                "stock_on_hand": 10, 
                "barcode": "2384738478"
            },
            {
                "store_id": 140,
                "price": 250, 
                "special_price": 250,  
                "special_price_from": "2017-09-01",  
                "special_price_to": "2017-09-30", 
                "stock_available": 10,
                "stock_on_hand": 10, 
                "barcode": "2384738478"
            }
        ]
    }]

###### Product Data Mapping 

| Field | MMS Data |
| ------ | ------ |
| sku | COLSKU.COLSKU |
| name | COLSKU.COLDSC |
| model | COLSKU.COLMDL |
| mms_id* | COLSKU.COLDPT[1-2] + COLSKU.COLSDP[1-2] + COLSKU.COLCLS[1-2] + COLSKU.COLSCS[1-2] |
| weight | COLSKU.COLWHT |
| length | COLSKU.COLLGT |
| width | COLSKU.COLWDT |
| height | COLSKU.COLHGT |
| brand | COLSKU.COLBRN |
| package_weight | COLSKU.COLPWG |
| package_length | COLSKU.COLPLE |
| package_width | COLSKU.COLPWI |
| package_height | COLSKU.COLPHI |
| vendor_code | COLSKU.COLVDC |
| vendor_name | COLSKU.COLVDN |
    
    
*substring position 1 to 2 (starts from 0).
    
##### Product Price and Stock Data Mapping
    
Each product create/update must include price and stock data of each store from table COLPRCSTOCK.

| Field | MMS Data |
| ------ | ------ |
| store_id | COLPRCSTOCK.COLSTR |
| price | COLPRCSTOCK.COLREGPRI |
| special_price | COLPRCSTOCK.COLPRI | 
| special_price_from | COLPRCSTOCK.COLPSTD | 
| special_price_to | COLPRCSTOCK.COLPETD |
| stock_available | COLPRCSTOCK.COLAVAIL |
| stock_on_hand | COLPRCSTOCK.COLONH |
| barcode | COLPRCSTOCK.COLUPC |

    
##### Response

    [
        {
            "index": 0,
            "sku": "984294803",
            "status": "failed",
            "error": "mms_id, name are required"
        },
        {
            "index": 1,
            "sku": "",
            "status": "failed",
            "error": "sku is required"
        },
        {
            "index": 2,
            "sku": "dsdsdsd",
            "status": "failed",
            "error": "store data at 0, store_id, price are required, store data at 1, store_id, price are required"
        }
    ]