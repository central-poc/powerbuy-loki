<?php
namespace Powerbuy\ProductMaster\Controller\Adminhtml\ProductMaster;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Powerbuy_ProductMaster::productmasters';  
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/index/index');
        return $resultRedirect;
    }     
}
