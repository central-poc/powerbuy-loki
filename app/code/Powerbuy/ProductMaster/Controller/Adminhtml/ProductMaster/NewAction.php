<?php
namespace Powerbuy\ProductMaster\Controller\Adminhtml\ProductMaster;

class NewAction extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Powerbuy_ProductMaster::productmasters';       
    protected $resultPageFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;        
        parent::__construct($context);
    }
    
    public function execute()
    {
        return $this->resultPageFactory->create();  
    }    
}
