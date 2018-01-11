<?php
/**
 * Created by PhpStorm.
 * User: ruang
 * Date: 11/1/2018 AD
 * Time: 23:34
 */

namespace Powerbuy\ProductMaster\Cron;

use \Psr\Log\LoggerInterface;

class UpdateProduct {
    protected $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Write to system.log
     *
     * @return void
     */

    public function execute() {
        $this->logger->info('Cron Product Master Works');
    }

}
