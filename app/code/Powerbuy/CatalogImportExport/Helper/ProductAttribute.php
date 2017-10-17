<?php

namespace Powerbuy\CatalogImportExport\Helper;

use Magento\Eav\Api\AttributeGroupRepositoryInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory as AttributeGroupCollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Api\SearchCriteriaBuilder;

use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory;

use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Eav\Api\AttributeSetManagementInterface;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\GroupFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;

class ProductAttribute extends AbstractHelper
{
    /**
     * @var AttributeManagementInterface
     */
    private $attributeManagement;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var AttributeManagementInterface
     */
    private $attributeSetManagement;
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $attributeSetFactory;
    /**
     * @var AttributeSetRepositoryInterface
     */
    private $attributeSetRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var array
     */
    private $attributeSetNames;
    /**
     * @var CollectionFactory
     */
    private $attributeSetCollection;
    /**
     * @var TypeFactory
     */
    private $eavTypeFactory;
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var AttributeFactory
     */
    private $attributeFactory;
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\GroupFactory
     */
    private $attributeGroupFactory;
    /**
     * @var AttributeGroupRepositoryInterface
     */
    private $attributeGroupRepository;
    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    private $attributeRepository;
    /**
     * @var Group
     */
    private $attributeGroupCollection;
    /**
     * @var AttributeGroupCollectionFactory
     */
    private $attributeGroupCollectionFactory;


    /**
     * Validator constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Config $config
     * @param TypeFactory $eavTypeFactory
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     * @param \Magento\Eav\Model\Entity\Attribute\GroupFactory $attributeGroupFactory
     * @param AttributeGroupRepositoryInterface $attributeGroupRepository
     * @param Group $attributeGroupCollection
     * @param AttributeGroupCollectionFactory $attributeGroupCollectionFactory
     * @param AttributeFactory $attributeFactory
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param CollectionFactory $attributeSetCollection
     * @param AttributeSetManagementInterface $attributeSetManagement
     * @param AttributeManagementInterface $attributeManagement
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        Config $config,
        TypeFactory $eavTypeFactory,
        AttributeSetRepositoryInterface $attributeSetRepository,
        SetFactory $attributeSetFactory,
        GroupFactory $attributeGroupFactory,
        AttributeGroupRepositoryInterface $attributeGroupRepository,
        Group $attributeGroupCollection,
        AttributeGroupCollectionFactory $attributeGroupCollectionFactory,
        AttributeFactory $attributeFactory,
        ProductAttributeRepositoryInterface $attributeRepository,
        CollectionFactory $attributeSetCollection,
        AttributeSetManagementInterface $attributeSetManagement,
        AttributeManagementInterface $attributeManagement,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->config = $config;
        $this->attributeManagement = $attributeManagement;
        $this->attributeSetManagement = $attributeSetManagement;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attributeSetCollection = $attributeSetCollection;
        $this->eavTypeFactory = $eavTypeFactory;
        $this->attributeFactory = $attributeFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeGroupFactory = $attributeGroupFactory;
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->attributeRepository = $attributeRepository;
        $this->attributeGroupCollection = $attributeGroupCollection;
        $this->attributeGroupCollectionFactory = $attributeGroupCollectionFactory;
    }

    public function ensureAttributeSetExists($attributeSetName)
    {
        $eavType = $this->eavTypeFactory->create()->loadByCode(Product::ENTITY);
        $attributeSet = $this->attributeSetCollection->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('entity_type_id', array('eq' => $eavType->getEntityTypeId()))
            ->addFieldToFilter('attribute_set_name', array('eq' => $attributeSetName))
            ->getFirstItem();

        if ($attributeSet->getId()) {
            return $attributeSet;
        }

        $attributeSet = $this->attributeSetFactory->create();
        $attributeSet->setAttributeSetName($attributeSetName);
        $attributeSet->setEntityTypeId($eavType->getEntityTypeId());

        return $this->attributeSetManagement->create(
            $eavType->getEntityTypeCode(),
            $attributeSet,
            $eavType->getDefaultAttributeSetId()
        );

    }

    public  function ensureAttributeGroupExistsAndAssignToAttributeSet($attributeGroupName, $attributeSet)
    {
        $attributeGroup = $this->attributeGroupFactory->create();
        $attributeGroup->setAttributeGroupName($attributeGroupName);
        $attributeGroup->setAttributeSetId($attributeSet->getId());
        if ($this->attributeGroupCollection->itemExists($attributeGroup)) {
            return $this->attributeGroupCollectionFactory
                ->create()
                ->setAttributeSetFilter($attributeSet->getId())
                ->addFilter('attribute_group_name', $attributeGroupName)
                ->getFirstItem();
        }

        return $this->attributeGroupRepository->save($attributeGroup);
    }

    /**
     * @param $attributeCode
     * @param $attributeLabel
     * @param $attributeSet
     * @param $attributeGroup
     */
    public function ensureAttributeExistsAndAssignToAttributeSet($attributeCode, $attributeLabel, $attributeSet, $attributeGroup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        //$eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
        $eavSetup->addAttribute(
            Product::ENTITY,
            $attributeCode,
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => $attributeLabel,
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );
        $this->attributeManagement->assign(
            Product::ENTITY,
            $attributeSet->getId(),
            $attributeGroup->getId(),
            $attributeCode,
            0);
    }

    public function getAttributeInAttributeSet($attributeSetId)
    {
        return $this->attributeManagement->getAttributes(Product::ENTITY, $attributeSetId);
    }
}