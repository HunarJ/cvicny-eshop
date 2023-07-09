<?php


namespace App\EshopModule\Presenters;


use App\Model\CategoryManager;
use App\Presenters\BasePresenter;
use Nittro\Bridges\NittroUI\Presenter;

class BaseEshopPresenter extends Presenter
{
    protected CategoryManager $categoryManager;

    public function injectManagerDependencies(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    protected function beforeRender() {
        parent::beforeRender();
        $catogories2 = $this->categoryManager->getCategories();
        $this->template->domain = $this->getHttpRequest()->getUrl()->getHost();
        $this->template->categories = $this->categoryManager->getCategories()->fetchAll();
        //bdump($catogories2->fetchAll());
    }



}