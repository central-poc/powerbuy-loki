<?php
namespace Central\CreateProductAttributes\Setup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    const NAME_CSV_INSTALL = 'productAttributes_stable1.csv';
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private $eavSetupFactory;
    /**
     * @var \Central\CreateProductAttributes\Helper\Data
     */
    protected $helper;
    /**
     * @var \Central\CreateProductAttributes\Model\AttributeSet
     */
    protected $attributeSet;

    /**
     * InstallSchema constructor.
     *
     * @param EavSetupFactory                                     $eavSetupFactory
     * @param ModuleDataSetupInterface                            $eavSetup
     * @param \Central\CreateProductAttributes\Helper\Data        $helper
     * @param \Central\CreateProductAttributes\Model\AttributeSet $attributeSet
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $eavSetup,
        \Central\CreateProductAttributes\Helper\Data $helper,
        \Central\CreateProductAttributes\Model\AttributeSet $attributeSet
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavSetup = $eavSetup;
        $this->helper = $helper;
        $this->attributeSet = $attributeSet;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * @var \Magento\Eav\Setup\EavSetup $eavSetup
         */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->eavSetup]);
        $installer = $setup;
        $installer->startSetup();
        $productAttributesCsv = $this->helper->getCsvContent(self::NAME_CSV_INSTALL, false);
        $this->attributeSet->attributeProducts($eavSetup, $productAttributesCsv);
        $installer->endSetup();
    }
}
