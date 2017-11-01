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

    public function getVillages()
    {
        return $this->getData(self::VILLAGES);
    }

    public function setVillages($villages)
    {
        // TODO: Implement setVillages() method.
    }

    public function getHomeNo()
    {
        return $this->getData(self::HOME_NO);
    }

    public function setHomeNo($homeNo)
    {
        return $this->setData(self::HOME_NO, $homeNo);
    }

    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    public function getSubDistrict()
    {
        return $this->getData(self::SUB_DISTRICT);
    }

    public function setSubDistrict($subDistrict)
    {
        return $this->setData(self::SUB_DISTRICT, $subDistrict);
    }

    public function getDistrict()
    {
        return $this->getData(self::DISTRICT);
    }

    public function setDistrict($district)
    {
        return $this->setData(self::DISTRICT, $district);
    }

    public function getProvince()
    {
        return $this->getData(self::PROVINCE);
    }

    public function setProvince($province)
    {
        return $this->setData(self::PROVINCE, $province);
    }

    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
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
