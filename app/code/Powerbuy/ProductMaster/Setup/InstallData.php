<?php
namespace Powerbuy\ProductMaster\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;

use \Powerbuy\ProductMaster\Api\BatchProductMasterImportInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var \Powerbuy\ProductMaster\Api\BatchProductMasterImportInterface
     */
    private $batchProductMasterImport;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    private $filterBuilder;


    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeRepositoryInterface $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param \Powerbuy\ProductMaster\Api\BatchProductMasterImportInterface $batchProductMasterImport
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        BatchProductMasterImportInterface $batchProductMasterImport
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->batchProductMasterImport = $batchProductMasterImport;
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $attributes = BatchProductMasterImportInterface::PRODUCT_ATTRIBUTE_MAP;
        $productAttributes = $this->getProductAttributeCodes();
        foreach ($attributes as $attr => $data) {
            //ignore already exists
            if (in_array($attr, $productAttributes)) {
                continue;
            }
            if ($data['create_attribute'] == false) {
                continue;
            }
            //$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, $attr);
            $eavSetup->addAttribute(
                Product::ENTITY,
                $attr,
                [
                    'type' => $data['type'],
                    'label' => $data['label'],
                    'input' => $data['input'],
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => $data['visible'],
                    'required' => $data['required']
                ]
            );
        }
    }

    /**
     * @return array
     */
    private function getProductAttributeCodes()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->attributeRepository->getList(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            $searchCriteria);
        return array_map(function ($attr) {return $attr['attribute_code']; }, $searchResults->getItems());
    }
}
