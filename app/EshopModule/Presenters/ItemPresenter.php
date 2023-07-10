<?php


namespace App\EshopModule\Presenters;


use App\Model\ItemManager;
use Nette\Http\Session;

class ItemPresenter extends BaseEshopPresenter
{

    private $item_id;

    private ItemManager $itemManager;

    private Session $session;

    public $cart;

    public function __construct(ItemManager $itemManager, Session $session)
    {
        parent::__construct();
        $this->itemManager = $itemManager;
        $this->session = $session;
    }

    public function actionDetail(int $id)
    {
        $this->item_id = $id;
    }

    public function handleAddToCart(array $itemId)
    {
        $this->cart = $this->session->getSection('cart');
        $this->cart->items[] = $itemId;
        $this->flashMessage('Položka přidána do košíku');
        bdump($this->cart->items);
        $this->redirect('this');
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