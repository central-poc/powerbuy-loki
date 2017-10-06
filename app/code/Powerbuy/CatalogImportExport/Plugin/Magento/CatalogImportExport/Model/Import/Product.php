<?php
namespace Powerbuy\CatalogImportExport\Plugin\Magento\CatalogImportExport\Model\Import;

use Powerbuy\CatalogImportExport\Helper\ProductAttribute;

class Product
{
    /**
     * @var \Powerbuy\CatalogImportExport\Helper\ProductAttribute
     */
    private $productAttributeHelper;

    /**
     * Product constructor.
     * @param \Powerbuy\CatalogImportExport\Helper\ProductAttribute $productAttributeHelper
     */
    public function __construct
    (
        ProductAttribute $productAttributeHelper
    )
    {
        $this->productAttributeHelper = $productAttributeHelper;
    }
    function beforeValidateRow($subject, array $rowData, $rowNum)
    {
        $attributeSetCode = $rowData['attribute_set_code'];
        $attributeSet = $this->productAttributeHelper
            ->ensureAttributeSetExists($attributeSetCode);
        $attributeGroup = $this->productAttributeHelper
            ->ensureAttributeGroupExistsAndAssignToAttributeSet("Import Data", $attributeSet);

        $availableAttributes = $this->productAttributeHelper
            ->getAttributeInAttributeSet($attributeSet->getId());
        $availableAttributeCodes = array_map(function($attr){
                return $attr->getAttributeCode();
            },
            $availableAttributes
        );
        $attributes = $this->prepareAttributes($rowData['additional_attributes']);
        $attributes = array_filter($attributes, function($attr) use ($availableAttributeCodes, $attributeSetCode) {
            return !in_array($attr[0], $availableAttributeCodes);
        });
        foreach ($attributes as $attribute)
        {
            $attributeCode = $attribute[0];
            $attributeLabel = $this->formatAttributeName($attribute[0]);
            $this->productAttributeHelper->ensureAttributeExistsAndAssignToAttributeSet(
                $attributeCode,
                $attributeLabel,
                $attributeSet,
                $attributeGroup
            );
        }
    }

    private function prepareAttributes($attributeString)
    {
        $attributeItems = str_getcsv($attributeString);
        $attributeItems = array_map(function($item){
            return explode('=', $item);
        }, $attributeItems);

        return array_filter($attributeItems, function($item){
            return count($item) == 2 && !empty($item[1]);
        });
    }

    private function formatAttributeName($name)
    {
        if (strpos($name, '_') === false) {
            return $name;
        }
        $parts = explode('_', $name);
        $parts = array_slice($parts, 1);
        return ucwords(implode(' ', $parts));
    }
}
