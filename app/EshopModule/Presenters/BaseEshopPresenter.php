<?php


namespace App\EshopModule\Presenters;


use App\Model\CategoryManager;
use App\Presenters\BasePresenter;
use Nittro\Bridges\NittroUI\Presenter;
use Nette\Http\Session;

class BaseEshopPresenter extends Presenter
{
    protected CategoryManager $categoryManager;

    public function injectManagerDependencies(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    protected function beforeRender() {
        parent::beforeRender();
       // $session = $this->getSession();
       // $session->start();
        //bdump($_SESSION);
       /* if (empty($_SESSION['cart'])){
            $_SESSION['cart'] = array();
        }*/
        $this->template->domain = $this->getHttpRequest()->getUrl()->getHost();
        $this->template->categories = $this->categoryManager->getCategories()->fetchAll();
    }



}