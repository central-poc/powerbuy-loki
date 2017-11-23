<?php
namespace Powerbuy\ProductMaster\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\Model\ResourceModel\Db\Context as DBContext;
use \Magento\Framework\Stdlib\DateTime\DateTime;

class ProductMaster extends AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
        DBContext $context,
        DateTime $date,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->date = $date;
    }

    protected function _construct(){
        $this->_init('powerbuy_productmaster_productmaster','entity_id');
    }

    /**
     * Process post data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->date->gmtDate());
        }

        $object->setUpdateTime($this->date->gmtDate());
        $this->validateDuplicate($object);

        return parent::_beforeSave($object);
    }

    protected function _afterSave(AbstractModel $object)
    {
        return parent::_afterSave($object);
    }

    /**
     * Get product identifier by store id and sku
     *
     * @param string $storeId
     * @param string $sku
     * @return int|false
     */
    public function getIdByStoreIdAndSku($storeId, $sku)
    {
        $connection = $this->getConnection();

        $select = $connection
            ->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('store_id = :store_id and sku = :sku');

        $bind = [':store_id' => (string)$storeId, ':sku' => (string)$sku];

        return $connection->fetchOne($select, $bind);
    }

    public function getStoreIds()
    {
        $connection = $this->getConnection();

        $items = $connection->fetchAll(
            'select distinct store_id from ' . $this->getMainTable()
        );
        return array_map(function($item) {
            return $item['store_id'];
        }, $items);
    }

    /**
     * Get all storeId by sku
     *
     * @param string $storeId
     * @param string $sku
     * @return int|false
     */
    public function getStoreIdsBySku($sku)
    {
        $connection = $this->getConnection();

        $select = $connection
            ->select()
            ->distinct()
            ->from($this->getMainTable(), 'store_id')
            ->where('sku = :sku');

        $bind = [':sku' => (string)$sku];

        return $connection->fetchCol($select, $bind);
    }

    /**
     * Validate duplicate sku and store_id both
     *
     * @param \Magento\Framework\DataObject $object
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateDuplicate($object)
    {
        $sku     = $object->getData('sku');
        $storeId = $object->getData('store_id');

        if($entityId = $this->getIdByStoreIdAndSku($storeId, $sku)) {
            if($entityId !== $object->getData('entity_id')) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('This sku has been store "%1" already exists.', $storeId)
                );
            }
        }

        return true;
    }
}
