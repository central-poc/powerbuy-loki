<?php
namespace Powerbuy\Store\Api\Data;
interface StoreInterface 
{
    const STORE_ID              = 'powerbuy_store_store_id';
    const STORE_CODE            = 'store_code';
    const NAME                  = 'name';
    const DESCRIPTION           = 'description';
    const ADDRESS               = 'address';
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