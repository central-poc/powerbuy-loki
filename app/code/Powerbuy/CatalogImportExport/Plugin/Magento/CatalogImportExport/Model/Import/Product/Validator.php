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
        if ($attrCode == 'brand')
        {
            $valueValid = array_key_exists('brand', $rowData) && !empty($rowData['brand']);
            if ($valueValid) {
                $this->attributeOptionHelper->createOrGetId('brand', $rowData['brand']);
                $options = $this->productAttributeRepository->get('brand')->getOptions();
                foreach ($options as $option) {
                    $attrParams['options'][strtolower($option->getLabel())] = $option->getValue();
                }
            }
        }

        return $proceed($attrCode, $attrParams, $rowData);

    }
}
