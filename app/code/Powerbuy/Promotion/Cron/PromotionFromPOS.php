<?php
namespace Powerbuy\Promotion\Cron;

use \Powerbuy\Promotion\Helpers\ConnectSql;
use \Powerbuy\Promotion\Model\ResourceModel\Promotion;
use \Powerbuy\Promotion\Model\ResourceModel\Promotion\CollectionFactory;

class PromotionFromPOS {

    protected $helper;

    protected $resourcePromotion;

    protected $collectionFactory;

    public function __construct(
        ConnectSql $helper,
        Promotion $resourcePromotion,
        CollectionFactory $collectionFactory
    )
    {
        $this->helper = $helper;
        $this->resourcePromotion = $resourcePromotion;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $conn = $this->helper->ConnectDBInterface();
        $query = 'EXECUTE dbo.sp_GetPromotionForTablet';
        $result = sqlsrv_query($conn,$query);

        if(sqlsrv_has_rows($result))
        {
            $this->resourcePromotion->deleteAll();
            $item = array();
            while ($row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC))
            {
                $item[] = [
                    'promotion_num' => $row['PMNum'],
                    'promotion_name' => $row['PMName'],
                    'promotion_type' => $row['PMType'],
                    'start_date' => $row['SDATE']->format('Y-m-d'),
                    'end_date' => $row['EDATE']->format('Y-m-d'),
                    'status' => 'A',
                    'product_sku' => $row['SKU']
                ];

            }
            $this->resourcePromotion->savePromotion($item);
        }
    }

}