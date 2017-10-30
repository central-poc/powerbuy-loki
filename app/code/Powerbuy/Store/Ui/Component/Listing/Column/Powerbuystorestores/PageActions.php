<?php
namespace Powerbuy\Store\Ui\Component\Listing\Column\Powerbuystorestores;

class PageActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData("name");
                $id = "X";
                if(isset($item["powerbuy_store_store_id"]))
                {
                    $id = $item["powerbuy_store_store_id"];
                }
                $item[$name]["view"] = [
                    "href"=>$this->getContext()->getUrl(
                        "powerbuy_store_stores/store/edit",["powerbuy_store_store_id"=>$id]),
                    "label"=>__("Edit")
                ];
            }
        }

        return $dataSource;
    }    
    
}
