<?php
/**
 * Created by PhpStorm.
 * User: ruang
 * Date: 11/1/2018 AD
 * Time: 23:34
 */

namespace Powerbuy\ProductMaster\Cron;

use Powerbuy\ProductMaster\Api\ProductMasterRepositoryInterface;
use Powerbuy\ProductMaster\Model\ResourceModel\ProductMaster as ProductResource;
use Powerbuy\Promotion\Helpers\ConnectSql;
use \Psr\Log\LoggerInterface;

class UpdateProduct {
    protected $logger;
    /**
     * @var ProductResource
     */
    private $productMasterResource;
    /**
     * @var ConnectSql
     */
    private $connectSql;
    /**
     * @var ProductMasterRepositoryInterface
     */
    private $productMasterRepositoryInterface;

    /**
     * UpdateProduct constructor.
     * @param LoggerInterface $logger
     * @param ConnectSql $connectSql
     * @param ProductResource $productMasterResource
     * @param ProductMasterRepositoryInterface $productMasterRepoInterface
     */
    public function __construct(
        LoggerInterface $logger,
        ConnectSql $connectSql,
        ProductResource $productMasterResource,
        ProductMasterRepositoryInterface $productMasterRepoInterface
    ) {
        $this->logger = $logger;
        $this->productMasterResource = $productMasterResource;
        $this->connectSql = $connectSql;
        $this->productMasterRepositoryInterface = $productMasterRepoInterface;
    }



    public function execute() {

        $this->productMasterRepositoryInterface->saveProductFromInterface();
        return true;
    }

}