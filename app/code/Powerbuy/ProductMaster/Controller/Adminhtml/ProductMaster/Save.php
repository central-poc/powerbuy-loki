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
use Powerbuy\ProductMaster\Helper\Attribute;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;

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
     * @var Helper\Attribute
     */
    private $attributeHelper;

    private $productFactory;

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
        Table $table,
        Attribute $attributeHelper,
        ProductFactory $productFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);

        $this->attributeRepository       = $attributeRepository;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->attributeOption           = $attributeOption;
        $this->attributeOptionLabel      = $attributeOptionLabel;
        $this->table                     = $table;
        $this->attributeHelper           = $attributeHelper;
        $this->productFactory            = $productFactory;
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
            $model   = $this->_objectManager->create('Powerbuy\ProductMaster\Model\ProductMaster');
            $id      = $this->getRequest()->getParam($entityId);
            $product = [];

            if ($id) {
                $model->load($id);
                $product = $model->getData();
            }

            $model->setData($data);

            try {
                $model->save();

                $data['store_id'] = $this->tranformStoreId($data['store_id']);

                $this->attributeHelper->createOrGetId('in_stores', $data['store_id']);
                $this->updateProduct($data, $product);

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

    private function updateProduct($data, $productMaster = [])
    {
        $product = $this->productFactory->create()->loadByAttribute('sku', $data['sku']);
        $storeId = 0;

        if(!empty($product)) {
            $optionId = $this->attributeHelper->getOptionId('in_stores', $data['store_id']);

            if(!empty($optionId)) {
                $productAttrValues = explode(',', $product->getAttributeDefaultValue('in_stores'));

                if(!empty($productMaster['store_id']))
                {
                    $masterStoreId     = $this->tranformStoreId($productMaster['store_id']);
                    $masterOptionId    = $this->attributeHelper->getOptionId('in_stores', $masterStoreId);
                    $productAttrValues = array_diff($productAttrValues, [$masterOptionId]);
                }

                if(!in_array($optionId, $productAttrValues))
                {
                    $productAttrValues[] = $optionId;
                    $attrString          = implode(',', $productAttrValues);

                    $product->setStoreId($storeId);
                    $product->setData('in_stores', $attrString);

                    return $product->getResource()->saveAttribute($product, 'in_stores');
                }
            }
        }

        return false;
    }

    private function tranformStoreId($storeId)
    {
        $defaultDigit = 5;
        return strlen($storeId) < $defaultDigit ? sprintf('%05d', $storeId) : $storeId;
    }
}
