<?php
namespace Powerbuy\Store\Api\Data;
interface StoreInterface 
{
    const STORE_ID              = 'powerbuy_store_store_id';
    const STORE_CODE            = 'store_code';
    const NAME                  = 'name';
    const DESCRIPTION           = 'description';
    const ADDRESS               = 'address';
    const VILLAGES              = 'villages';
    const HOME_NO               = 'home_no';
    const STREET                = 'street';
    const SUB_DISTRICT          = 'sub_district';
    const DISTRICT              = 'district';
    const PROVINCE              = 'province';
    const POSTCODE              = 'postcode';
    const OPEN_TIME             = 'open_time';
    const TELEPHONE             = 'telephone';
    const EMAIL                 = 'email';
    const IS_ACTIVE             = 'is_active';
    const CREATION_TIME         = 'creation_time';
    const UPDATE_TIME           = 'update_time';


    public function getStoreCode();
    public function setStoreCode($storeCode);

    public function getName();
    public function setName($name);

    public function getDescription();
    public function setDescription($description);

    public function getAddress();
    public function setAddress($address);

    public function getVillages();
    public function setVillages($villages);

    public function getHomeNo();
    public function setHomeNo($homeNo);

    public function getStreet();
    public function setStreet($street);

    public function getSubDistrict();
    public function setSubDistrict($subDistrict);

    public function getDistrict();
    public function setDistrict($district);

    public function getProvince();
    public function setProvince($province);

    public function getPostcode();
    public function setPostcode($postcode);

    public function getOpenTime();
    public function setOpenTime($openTime);

    public function getTelephone();
    public function setTelephone($telephone);

    public function getEmail();
    public function setEmail($email);

    public function getIsActive();
    public function setIsActive($isActive);

    public function getCreateTime();
    public function setCreateTime($createTime);

    public function getUpdateTime();
    public function setUpdateTime($updateTime);

}