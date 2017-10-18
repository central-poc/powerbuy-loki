<?php
namespace Powerbuy\CatalogImportExport\Plugin\Magento\CatalogImportExport\Model\Import;

use Powerbuy\CatalogImportExport\Helper\ProductAttribute;
use Powerbuy\CatalogImportExport\Helper\ProductAttributeOption;

class Product
{
    /**
     * @var \Powerbuy\CatalogImportExport\Helper\ProductAttribute
     */
    private $productAttributeHelper;
    /**
     * @var ProductAttributeOption
     */
    private $productAttributeOptionHelper;

    /**
     * Product constructor.
     * @param \Powerbuy\CatalogImportExport\Helper\ProductAttribute $productAttributeHelper
     * @param ProductAttributeOption $productAttributeOptionHelper
     */
    public function __construct
    (
        ProductAttribute $productAttributeHelper,
        ProductAttributeOption $productAttributeOptionHelper
    )
    {
        $this->productAttributeHelper = $productAttributeHelper;
        $this->productAttributeOptionHelper = $productAttributeOptionHelper;
    }
    function beforeValidateRow($subject, array $rowData, $rowNum)
    {
        //create brand option
        if (array_key_exists('brand', $rowData)) {
            $this->productAttributeOptionHelper->createOrGetId('brand', $rowData['brand']);
        }
        //create attribute set
        $attributeSetCode = $rowData['attribute_set_code'];
        $attributeSet = $this->productAttributeHelper
            ->ensureAttributeSetExists($attributeSetCode);

        //create attribute set group
        $attributeGroup = $this->productAttributeHelper
            ->ensureAttributeGroupExistsAndAssignToAttributeSet("Import Data", $attributeSet);

        $availableAttributes = $this->productAttributeHelper
            ->getAttributeInAttributeSet($attributeSet->getId());
        $availableAttributeCodes = array_map(function($attr){
                return $attr->getAttributeCode();
            },
            $availableAttributes
        );

        //prepare attributes to create
        $attributes = $this->prepareAttributes($attributeSetCode, $rowData);
        $attributes = array_filter($attributes, function($attr) use ($availableAttributeCodes, $attributeSetCode) {
            return !in_array($attr[0], $availableAttributeCodes);
        });
        foreach ($attributes as $attributeCode => $attributeValue)
        {
            $attributeLabel = $this->formatAttributeName($attributeCode);
            $this->productAttributeHelper->ensureAttributeExistsAndAssignToAttributeSet(
                $attributeCode,
                $attributeLabel,
                $attributeSet,
                $attributeGroup
            );
        }
    }

    private function prepareAttributes($attributeSetCode, $data)
    {
        $output = array();
        foreach ($data as $k => $v) {
            if (strpos($k, $attributeSetCode) > 0) {
                $output[$k] = $v;
            }
        }
        return $output;
    }

    private function formatAttributeName($name)
    {
        if (strpos($name, '_') === false) {
            return $name;
        }
        $parts = explode('_', $name);
        $parts = array_slice($parts, 0, -1);
        return ucwords(implode(' ', $parts));
    }
}
