<?php
namespace Powerbuy\ProductMaster\Controller\Adminhtml\ProductMaster;

use Magento\Backend\App\Action;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
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
     * @var Action\Context
     */
    private $context;
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $attributeRepository;
    /**
     * @var AttributeOptionManagementInterface
     */
    private $attributeOptionManagement;
    /**
     * @var AttributeOptionInterface
     */
    private $attributeOption;
    /**
     * @var AttributeOptionLabelInterface
     */
    private $attributeOptionLabel;
    /**
     * @var Table
     */
    private $table;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ProductAttributeRepositoryInterface $attributeRepository
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param AttributeOptionInterface $attributeOption
     * @param AttributeOptionLabelInterface $attributeOptionLabel
     * @param Table $table
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,

        ProductAttributeRepositoryInterface $attributeRepository,
        AttributeOptionManagementInterface $attributeOptionManagement,
        AttributeOptionInterface $attributeOption,
        AttributeOptionLabelInterface $attributeOptionLabel,
        Table $table
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
        $this->attributeRepository = $attributeRepository;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->attributeOption = $attributeOption;
        $this->attributeOptionLabel = $attributeOptionLabel;
        $this->table = $table;
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
                $exist = $this->getOptionId('00010');
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

    private function getOptionId($label)
    {
        $attribute = $this->attributeRepository->get('in_stores');
        if (!isset($this->attributeValues[ $attribute->getAttributeId() ])) {
            $this->attributeValues[ $attribute->getAttributeId() ] = [];

            // We have to generate a new sourceModel instance each time through to prevent it from
            // referencing its _options cache. No other way to get it to pick up newly-added values.

            /** @var \Magento\Eav\Model\Entity\Attribute\Source\Table $sourceModel */
            $sourceModel = $this->table->create();
            $sourceModel->setAttribute($attribute);

            foreach ($sourceModel->getAllOptions() as $option) {
                $this->attributeValues[ $attribute->getAttributeId() ][ $option['label'] ] = $option['value'];
            }
        }

        // Return option ID if exists
        if (isset($this->attributeValues[ $attribute->getAttributeId() ][ $label ])) {
            return $this->attributeValues[ $attribute->getAttributeId() ][ $label ];
        }
        return false;
    }
}
