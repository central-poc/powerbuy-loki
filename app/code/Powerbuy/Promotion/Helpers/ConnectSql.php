<?php

namespace Powerbuy\Promotion\Helpers;

use \Magento\Framework\App\Helper\AbstractHelper;

class ConnectSql extends AbstractHelper
{
    public function ConnectDBInterface()
    {
        echo "This is Helper in Magento 2";
        //$serverName = "192.168.54.139";
        $serverName = "27.254.48.150";
        $connectionOptions = array(
            "Database" => "DBPWB",
            "Uid" => "sa",
            "PWD" => "P@ssw0rd"
        );
        //Establishes the connection
        $conn = sqlsrv_connect( $serverName, $connectionOptions );
        if( $conn === false ) {
            die( FormatErrors( sqlsrv_errors()));
        }
        return $conn;
    }

    public function ConnectPWBLegacy()
    {
        echo "This is Helper in Magento 2";
        //$serverName = "gw-powerbuy.cenergy.co.th";
        $serverName = "private-db.powerbuy.co.th";
        $connectionOptions = array(
            "Database" => "DBPWB",
            "Uid" => "powerbuy",
            "PWD" => "pwb@123!"
        );
        //Establishes the connection
        $conn = sqlsrv_connect( $serverName, $connectionOptions );
        if( $conn === false ) {
            die( FormatErrors( sqlsrv_errors()));
        }
        return $conn;
    }
}