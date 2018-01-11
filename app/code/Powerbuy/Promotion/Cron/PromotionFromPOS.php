<?php
namespace Powerbuy\Promotion\Cron;

use \Powerbuy\Promotion\Helpers\ConnectSql;
use \Powerbuy\Promotion\Model\ResourceModel\Promotion;
use \Powerbuy\Promotion\Model\ResourceModel\Promotion\CollectionFactory;
use Psr\Log\LoggerInterface;

class PromotionFromPOS {

    protected $helper;

    protected $resourcePromotion;

    protected $collectionFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PromotionFromPOS constructor.
     * @param ConnectSql $helper
     * @param Promotion $resourcePromotion
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConnectSql $helper,
        Promotion $resourcePromotion,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    )
    {
        $this->helper = $helper;
        $this->resourcePromotion = $resourcePromotion;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        $this->logger->info('Cron Promotion,WORK!!! ');
        $conn = $this->helper->ConnectDBInterface();
        $query = 'EXEC dbo.GetPromoTablet';
        $result = sqlsrv_query($conn,$query);

        if(sqlsrv_has_rows($result))
        {
            while ($row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC))
            {
                $item = [
                    'promotion_num' => $row['PMNum'],
                    'promotion_name' => $row['PMName'],
                    'promotion_type' => $row['PMType'],
                    'start_date' => $row['SDATE']->format('Y-m-d'),
                    'end_date' => $row['EDATE']->format('Y-m-d'),
                    'status' => $row['PMSTATUS'],
                    'product_sku' => $row['SKU'],
                    'store_id' => $row['STCODE']
                ];
                $this->resourcePromotion->savePromotion($item);
            }
        }
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(__METHOD__);
        return $this;
    }

}