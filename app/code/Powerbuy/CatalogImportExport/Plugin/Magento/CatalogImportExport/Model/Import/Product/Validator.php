<?php
namespace Powerbuy\CatalogImportExport\Plugin\Magento\CatalogImportExport\Model\Import\Product;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Powerbuy\CatalogImportExport\Helper\ProductAttributeOption;

class Validator
{
    /**
     * @var ProductAttributeOption
     */
    private $attributeOptionHelper;
    /**
     * @var Repository
     */
    private $productAttributeRepository;

    /**
     * Validator constructor.
     * @param Repository $productAttributeRepository
     * @param ProductAttributeOption $attributeOptionHelper
     */
    public function __construct(
        Repository $productAttributeRepository,
        ProductAttributeOption $attributeOptionHelper
    )
    {

        $this->attributeOptionHelper = $attributeOptionHelper;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    function aroundIsAttributeValid($subject, $proceed, $attrCode, array $attrParams, array $rowData)
    {
        $attributeType = $attrParams['type'] ?? '';
        if (in_array($attributeType, ['select'])) 
        {
            $rawValue = $rowData[$attrCode] ?? '';
            $values = str_getcsv($rawValue, ",", '"');
            $cleanValues = array_filter($values, function($value) { 
                return !empty($value); }
            );

            foreach($cleanValues as $value) 
            {
                $this->attributeOptionHelper->createOrGetId($attrCode, $value);
            }
            $options = $this->productAttributeRepository->get($attrCode)->getOptions();
            foreach ($options as $option) {
                $label = $option->getLabel();
                if (!is_string($label)) {
                    $label = $label->getText();
                }
                $value = $option->getValue();
                $attrParams['options'][strtolower($option->getLabel())] = $option->getValue();
            }
        }

        return $proceed($attrCode, $attrParams, $rowData);

    }
}
