<?php


namespace App\AdminModule\Presenters;


use App\Model\CategoryManager;
use App\Model\ItemManager;

class DashboardPresenter extends BaseAdminPresenter
{

    private ItemManager $itemManager;

    private CategoryManager $categoryManager;

    public function __construct( ItemManager $itemManager, CategoryManager $categoryManager)
    {
        parent::__construct();
        $this->itemManager = $itemManager;
        $this->categoryManager = $categoryManager;
    }

    public function renderDefault(): void
    {
        $this->template->itemsTotal = $this->itemManager->getItemsCount();
        $this->template->categoryTotal = $this->categoryManager->getCategoryCount();

    }


}