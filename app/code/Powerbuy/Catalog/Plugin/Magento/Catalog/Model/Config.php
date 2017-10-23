<?php
namespace Powerbuy\Catalog\Plugin\Magento\Catalog\Model;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;

class Config
{
    /**
     * @var CollectionFactory
     */
    private $attributeSetCollectionFactory;

    /**
     * Config constructor.
     * @param CollectionFactory $attributeSetCollectionFactory
     */
    public function __construct(
        CollectionFactory $attributeSetCollectionFactory
    )
    {
        $this->attributeSetCollectionFactory = $attributeSetCollectionFactory;
    }

    function afterGetAttributeSetId($subject, $result, $entityTypeId, $name = null)
    {
        if (is_int($result))
        {
            return $result;
        }
        $attributeSet = $this->attributeSetCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('entity_type_id', array('eq' => $entityTypeId))
            ->addFieldToFilter('attribute_set_name', array('eq' => $name))
            ->getFirstItem();

        if ($attributeSet->getId()) {
            return $attributeSet->getId();
        }

    }
}
