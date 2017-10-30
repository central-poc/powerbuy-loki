<?php
namespace Powerbuy\ProductMaster\Ui\Component\Listing\Column\Powerbuyproductmasterproductmasters;

class PageActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepareDataSource(array $dataSource)
    {
        $entityId = "entity_id";
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData("name");
                $id = "X";
                if(isset($item[$entityId]))
                {
                    $id = $item[$entityId];
                }
                $item[$name]["view"] = [
                    "href"=>$this->getContext()->getUrl(
                        "powerbuy_productmaster_productmasters/productmaster/edit", [$entityId => $id]),
                    "label"=>__("Edit")
                ];
            }
        }

        return $dataSource;
    }    
    
}
