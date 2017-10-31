<?php
namespace Powerbuy\Catalog\Model;

use Powerbuy\Catalog\Api\CategoryRepositoryInterface;
use Powerbuy\Catalog\Api\Data\CategoryInterface;
use Powerbuy\Catalog\Model\CategoryFactory;
use Powerbuy\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Powerbuy\Catalog\Model\ResourceModel\Category;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
class CategoryRepository implements \Powerbuy\Catalog\Api\CategoryRepositoryInterface
{
    protected $objectFactory;
    protected $collectionFactory;

    public function __construct(
        CategoryFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        Category $resourceModel
    )
    {
        $this->objectFactory = $objectFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resourceModel        = $resourceModel;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getTree()
    {
        $result = array();

        $categoriesRoot = $this->resourceModel->getRootCategory();
        if(!$categoriesRoot)
        {
            throw new NoSuchEntityException(__('Category Root doesn\'t exist'));
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        foreach ($categoriesRoot as $category)
        {
            $cate = $objectManager->create('Magento\Catalog\Model\Category')->load($category);
            if(!$cate->getIsActive() || $cate['include_in_menu'] == "0")
                continue;

            $imageUrl = "";
            if($cate->getImageUrl() == "false")
            {
                $imageUrl = $cate->getImageUrl();
            }
            $categoryItem = [
                'entity_id' => $cate['entity_id'],
                'level' => $cate['level'],
                'name' => $cate['name'],
                'image' => $imageUrl,
                'children' => $this->getChildrenCategoryByCate($cate)
            ];
            $result[] = $categoryItem;
        }
        return $result;
    }

    /**
     * @param $cate
     * @return array
     */
    private function getChildrenCategoryByCate($cate)
    {
        $result = array();
        $childrenCategories = $cate->getChildrenCategories();
        if(count($childrenCategories) > 0)
        {
            foreach ($childrenCategories as $childrenCate)
            {
                $imageUrl = "";
                if($childrenCate->getImageUrl() == "false")
                {
                    $imageUrl = $childrenCate->getImageUrl();
                }
                $categoryItem = [
                    'entity_id' => $childrenCate['entity_id'],
                    'level' => $childrenCate['level'],
                    'name' => $childrenCate['name'],
                    'image' => $imageUrl,
                    'children' => $this->getChildrenCategoryByCate($childrenCate)
                ];
                $result[] = $categoryItem;
            }
        }
        return $result;
    }
}
