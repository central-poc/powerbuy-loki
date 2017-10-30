<?php
namespace Powerbuy\Store\Controller\Adminhtml\Store;

class NewAction extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Powerbuy_Store::stores';       
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
