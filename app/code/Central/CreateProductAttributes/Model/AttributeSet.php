<?php

namespace Central\CreateProductAttributes\Model;

/**
 * Class AttributeSet
 *
 * @package Central\CreateProductAttributes\Model
 */
class AttributeSet
{
    const GROUP_DEFAUL = ['General'];
    const ID_DEFALUT_SKELETON = 4;
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $attributeSetFatory;
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Set
     */
    protected $attributeSet;
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\GroupFactory
     */
    protected $_attrGroupFactory;
    /**
     * FilterManager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;
    /**
     * Model of product
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;
    /**
     * Attribute Factory
     *
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $attributeFactory;
    /**
     * Model of EAV
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $eavAttribute;

    /**
     * AttributeSet constructor.
     *
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory    $setFactory       factory
     * @param \Magento\Eav\Model\Entity\Attribute\Set           $set              set
     * @param \Magento\Framework\Filter\FilterManager           $filterManager    fileManager
     * @param \Magento\Catalog\Model\Product                    $product          product
     * @param \Magento\Eav\Model\Entity\AttributeFactory        $attributeFactory attributefactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute     eavattribute
     * @param \Magento\Eav\Model\Entity\Attribute\GroupFactory  $groupFactory     groupfactory
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        \Magento\Eav\Model\Entity\Attribute\Set $set,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Eav\Model\Entity\Attribute\GroupFactory $groupFactory
    ) {

        $this->attributeSetFatory = $setFactory;
        $this->attributeSet = $set;
        $this->filterManager = $filterManager;
        $this->product = $product;
        $this->attributeFactory = $attributeFactory;
        $this->eavAttribute = $eavAttribute;
        $this->_attrGroupFactory = $groupFactory;
    }

    /**
     * Attribute set
     *
     * @param string $attributeCode attribute code
     * @param string $groups        group
     *
     * @return null
     */
    public function addAttributeToSet($groups, $attributeId)
    {
        $groups = explode("\n", $groups);
        foreach ($groups as $group) {
            if ($group) {
                $group = $this->filterManager->stripTags($group);
                $group = trim($group);
                $attributeSetModel = $this->getAttributeSetModel($group);
                /**
                 * Model group
                 *
                 * @var \Magento\Eav\Model\Entity\Attribute\Group $group
                 */
                $group = $this->initGroupModel($group, $attributeSetModel->getId());
                $modelAttribute = $this->attributeFactory->create();
                $modelAttribute->setId(
                    $attributeId
                )->setAttributeGroupId(
                    $group->getId()
                )->setAttributeSetId(
                    $attributeSetModel->getId()
                )->setEntityTypeId(
                    $this->product->getResource()->getTypeId()
                );
                $modelAttributeArray[] = $modelAttribute;
                $group->setAttributes($modelAttributeArray);
                $modelGroupArray[] = $group;
                $attributeSetModel->setGroups($modelGroupArray);
                $attributeSetModel->save();
                $modelGroupArray = $modelAttributeArray = [];
            }
        }
    }

    /**
     * Get Model attribute set
     *
     * @param string $labelAttributeSet label attribute set
     *
     * @return $this|\Magento\Eav\Model\Entity\Attribute\Set
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeSetModel($labelAttributeSet)
    {
        $entityTypeId = $this->product->getResource()->getTypeId();
        /**
         * ModelFactory
         *
         * @var \Magento\Eav\Model\Entity\Attribute\Set $modelFactory
         */
        $modelFactory = $this->attributeSetFatory->create();
        if ($attributeSetId = $this->getAttributeSetIdByLabel($labelAttributeSet)) {
            return $modelFactory->load($attributeSetId);

        }
        $model = $modelFactory->setEntityTypeId($entityTypeId);
        $model->setAttributeSetName($labelAttributeSet);
        //  This attribute is not exits, Create new set attribute
        $model->validate();
        $model->save();
        $model->initFromSkeleton(self::ID_DEFALUT_SKELETON);
        $model->save();
        return $model;

    }

    /**
     * Get id attribute set by label
     *
     * @param string $labelAttributeSet label of attributeset
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeSetIdByLabel($labelAttributeSet)
    {
        $resource = $this->attributeSet->getResource();
        $con = $resource->getConnection();
        $select = $con->select()
            ->from($resource->getMainTable(), 'attribute_set_id')
            ->where('attribute_set_name = ?', $labelAttributeSet);
        return $con->fetchOne($select);
    }

    /**
     * Init group attribute
     *
     * @param string $groupName      name of Group
     * @param int    $attributeSetId id of attribute set
     *
     * @return \Magento\Eav\Model\Entity\Attribute\Group
     */
    public function initGroupModel($groupName = '', $attributeSetId = null)
    {
        $modelGroup = $this->_attrGroupFactory->create();
        $groupId = $this->getIdByAttributeGroupName($groupName);
        $modelGroup
            ->setId($groupId ? $groupId : null)
            ->setAttributeGroupName($groupName)
            ->setAttributeSetId($attributeSetId);

        if ($modelGroup->getId()) {
            $group = $this->_attrGroupFactory->create()->load($modelGroup->getId());
            if ($group->getId()) {
                $modelGroup->setAttributeGroupCode($group->getAttributeGroupCode());
            }
        }
        return $modelGroup;
    }

    /**
     * Get id attribute group by group name
     *
     * @param string $groupName name of group attribute
     *
     * @return null|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getIdByAttributeGroupName($groupName = '')
    {
        $resource = $this->_attrGroupFactory->create()->getResource();
        $con = $resource->getConnection();
        $select = $con->select()
            ->from($resource->getMainTable(), 'attribute_group_id')
            ->where('attribute_group_name = ?', $groupName);
        if ($attributeId = $con->fetchOne($select)) {
            return $attributeId;
        }
        return null;
    }

    /**
     * Add attribute product
     *
     * @param \Magento\Eav\Setup\EavSetup $eavSetup          eavsetup
     * @param array                       $productAttributes productattribute
     * @param bool                        $update            update
     *
     * @return null
     */
    public function attributeProducts(\Magento\Eav\Setup\EavSetup $eavSetup, $productAttributes = [], $update = false)
    {
        $attributeAdded = [];
        /**
         * Note : array key
         *
         * 0 : name
         * 1 : file_type
         * 2 : description
         * 3 : attribute_code
         * 4 : filter
         * 5 : front_end
         * 6 : attribute set
         * 7 : bool;
         */
        foreach ($productAttributes as $key => $attribute) {
            $boolOption = $groupDefault = false;
            $required = $showFront = true;
            $backend = $source = null;
            $options = [];
            $attributeCode = strtolower($attribute[3]);
            //start if
            if($this->checkAttributeToAdd($attributeCode) && !in_array($attributeCode, $attributeAdded)){
                if ($update) {
                    $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
                }
                if (strtolower($attribute[1]) == 'text/dropdown') {
                    $attributeType = 'select';
                } else {
                    $attributeType = strtolower($attribute[1]);
                }

                if (strtolower($attribute[4]) == 'n') {
                    $required = false;
                }

                if (strtolower($attribute[5]) == 'n') {
                    $showFront = false;
                }

                if ($attributeType == 'dropdown'
                    || $attributeType == 'multiselect'
                    || $attributeType == 'select'
                ) {
                    $boolOption = true;
                    $backend = 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend';
                    if ($attribute[2]) {
                        $optionAdds = explode("\n", $attribute[2]);
                        foreach ($optionAdds as $option) {
                            $options['values'][] = $option;
                        }
                    }
                }

                if (in_array($attribute[6], self::GROUP_DEFAUL) && isset($attribute[6])) {
                    $groupDefault = true;
                }

                if (isset($attribute[7]) && $attribute[7]) {
                    $source = 'Magento\Eav\Model\Entity\Attribute\Source\Boolean';
                }
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, $attributeCode, [
                        'group' => isset($attribute[6]) && $groupDefault ? $attribute[6] : null,
                        'type' => 'varchar',
                        'input' => $attributeType,
                        'label' => $attribute[0],
                        'backend' => $boolOption ? $backend : null,
                        'source' => $source != null ? $source : null,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                        'visible' => true,
                        'required' => $required,
                        'user_defined' => true,
                        'searchable' => $required,
                        'filterable' => $required,
                        'comparable' => false,
                        'visible_on_front' => $showFront,
                        'used_in_product_listing' => false,
                        'is_used_in_grid' => false,
                        'unique' => false,
                        'apply_to' => '',
                        'option' => $boolOption && $options ? $options : null,
                    ]
                );
                $attributeAdded[] = $attributeCode;
                //add attribute set
                if (isset($attribute[6]) && !$groupDefault && $attribute[6]) {
                    $attributeId = $eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY,$attributeCode);
                    $this->addAttributeToSet($attribute[6], $attributeId);
                }
            } //endif
        }
        return null;
    }

    /**
     * @param $attributeCode
     * @return bool
     */
    public function checkAttributeToAdd($attributeCode){
        if (strlen($attributeCode) > 30 || !$attributeCode
        ) {
            return false;
        }
        return true;
    }

    /**
     * Remove attribute set
     * 
     * @param array $attributesetArr attributeArr
     */
    public function removeAttributeSet($attributesetArr = []){
        foreach ($attributesetArr as $attributeSetLabel){
            if($attributeSetLabel){
                $idAttrbuteSet = $this->getAttributeSetIdByLabel($attributeSetLabel);
                if($idAttrbuteSet){
                    $attributeSetModel = $this->attributeSetFatory->create()->load($idAttrbuteSet);
                    if($attributeSetModel->getId()){
                        $attributeSetModel->delete();
                    }
                }
            }
        }
    }
}