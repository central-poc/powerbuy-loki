<?php
namespace Powerbuy\Catalog\Model\ResourceModel;
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('catalog_category_entity', 'entity_id');
    }

    /**
     * @return array
     */
    public function getRootCategory()
    {
        $connection = $this->getConnection();

        $select = $connection
            ->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('level = 2');

        return $connection->fetchAll($select);
    }
}
