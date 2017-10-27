<?php
namespace Powerbuy\Store\Model;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Powerbuy\Store\Api\Data\StoreInterface;

class Store extends AbstractModel implements StoreInterface, IdentityInterface
{
    const CACHE_TAG = 'powerbuy_store_store';

    protected function _construct()
    {
        $this->_init('Powerbuy\Store\Model\ResourceModel\Store');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getStoreCode()
    {
        return $this->getData(self::STORE_CODE);
    }

    public function setStoreCode($storeCode)
    {
        return $this->setData(self::STORE_CODE, $storeCode);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::STORE_CODE, $name);
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    public function getOpenTime()
    {
        return $this->getData(self::OPEN_TIME);
    }

    public function setOpenTime($openTime)
    {
        return $this->setData(self::OPEN_TIME, $openTime);
    }

    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function getCreateTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    public function setCreateTime($createTime)
    {
        return $this->setData(self::CREATION_TIME, $createTime);
    }

    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }
}
