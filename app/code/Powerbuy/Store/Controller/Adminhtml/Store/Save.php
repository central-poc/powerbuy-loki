<?php
namespace Powerbuy\Store\Controller\Adminhtml\Store;

use Magento\Backend\App\Action;
use Powerbuy\Store\Model\Page;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
            
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Powerbuy_Store::stores';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Powerbuy\Store\Model\Store::STATUS_ENABLED;
            }
            if (empty($data['powerbuy_store_store_id'])) {
                $data['powerbuy_store_store_id'] = null;
            }

            /** @var Powerbuy\Store\Model\Store $model */
            $model = $this->_objectManager->create('Powerbuy\Store\Model\Store');

            $id = $this->getRequest()->getParam('powerbuy_store_store_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the thing.'));
                $this->dataPersistor->clear('powerbuy_store_store');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['powerbuy_store_store_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->dataPersistor->set('powerbuy_store_store', $data);
            return $resultRedirect->setPath('*/*/edit', ['powerbuy_store_store_id' => $this->getRequest()->getParam('powerbuy_store_store_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }    
}
