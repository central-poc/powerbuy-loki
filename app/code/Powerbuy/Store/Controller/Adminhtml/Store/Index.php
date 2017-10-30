<?php
namespace Powerbuy\Store\Controller\Adminhtml\Store;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Powerbuy_Store::stores';  
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/index/index');
        return $resultRedirect;
    }     
}
