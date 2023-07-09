<?php


namespace App\EshopModule\Presenters;


use App\Model\ItemManager;

class ItemPresenter extends BaseEshopPresenter
{

    private $item_id;

    private ItemManager $itemManager;

    public function __construct(ItemManager $itemManager)
    {
        parent::__construct();
        $this->itemManager = $itemManager;
    }

    public function actionDetail(int $id)
    {
        $this->item_id = $id;
    }

    public function renderDefault(string $url)
    {
        $category = $this->categoryManager->getCategory($url);
        $products = $this->itemManager->getItemsByCategory($category->id);
        $this->template->categoryName = $category->name;
        $this->template->items = $products;
    }

    public function renderDetail(int $id)
    {
        try {
            $this->template->item = $this->itemManager->getItemFromId($id);
        } catch (\Exception $e) {
            $this->error('Položka nebyla nalezena.');
        }

    }
}