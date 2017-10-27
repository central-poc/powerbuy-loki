<?php
namespace Powerbuy\Store\Setup;

use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        //START: install stuff
        //END:   install stuff
        
        //START table setup
        $table = $installer->getConnection()->newTable(
            $installer->getTable('powerbuy_store_store')
        )->addColumn(
            'powerbuy_store_store_id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
            'Entity ID'
        )->addColumn(
            'StoreCode',
            Table::TYPE_TEXT,
            5,
            [ 'nullable' => false, ],
            'store id from MMS'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ],
            'Store Name'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ],
            'Description'
        )->addColumn(
            'address',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ],
            'Address'
        )->addColumn(
            'open_time',
            Table::TYPE_TEXT,
            50,
            [ 'nullable' => false, ],
            'Open Time'
        )->addColumn(
            'telephone',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ],
            'Telephone'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ],
            'Email'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            [ 'nullable' => false, 'default' => '1', ],
            'Is Active'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            [ 'nullable' => false, 'default' => Table::TIMESTAMP_INIT, ],
            'Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            [ 'nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE, ],
            'Modification Time'
        );
        $installer->getConnection()->createTable($table);
        //END   table setup
$installer->endSetup();
    }
}
