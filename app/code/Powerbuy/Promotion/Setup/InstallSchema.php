<?php
namespace Powerbuy\Promotion\Setup;
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
            $installer->getTable('powerbuy_promotion_promotion')
        )->addColumn(
            'promotion_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
            'Promotion ID'
        )->addColumn(
            'promotion_num',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [ 'nullable' => false, 'unsigned' => true, ],
            'Promotion NUM'
        )->addColumn(
            'promotion_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            [ 'nullable' => false, ],
            'Promotion Name'
        )->addColumn(
            'promotion_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1,
            [ 'nullable' => false, ],
            'Promotion Type B=BMGN, O=Onetop, I=Installment,P=PMCR'
        )->addColumn(
            'start_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            [ 'nullable' => false, ],
            'Promotion Start Date'
        )->addColumn(
            'end_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            [ 'nullable' => false, ],
            'Promotion End Date'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1,
            [ 'nullable' => false, ],
            'Promotion Status A = Active, I = Inactive'
        )->addColumn(
            'product_sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [ 'nullable' => false, ],
            'Product in promotion'
        );
        $installer->getConnection()->createTable($table);
        //END   table setup
$installer->endSetup();
    }
}
