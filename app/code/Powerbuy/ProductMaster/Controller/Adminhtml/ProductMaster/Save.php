<?php
namespace Powerbuy\ProductMaster\Controller\Adminhtml\ProductMaster;

use Magento\Backend\App\Action;
use Powerbuy\ProductMaster\Model\Page;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
            
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Powerbuy_ProductMaster::productmasters';

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
        $entityId = 'entity_id';
        if ($data) {
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Powerbuy\ProductMaster\Model\ProductMaster::STATUS_ENABLED;
            }
            if (empty($data[$entityId])) {
                $data[$entityId] = null;
            }

            /** @var Powerbuy\ProductMaster\Model\ProductMaster $model */
            $model = $this->_objectManager->create('Powerbuy\ProductMaster\Model\ProductMaster');

            $id = $this->getRequest()->getParam($entityId);
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the thing.'));
                $this->dataPersistor->clear('powerbuy_productmaster_productmaster');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [$entityId => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->dataPersistor->set('powerbuy_productmaster_productmaster', $data);
            return $resultRedirect->setPath('*/*/edit', [$entityId => $this->getRequest()->getParam($entityId)]);
        }
        return $resultRedirect->setPath('*/*/');
    }    
}
