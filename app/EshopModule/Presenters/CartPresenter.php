<?php


namespace App\EshopModule\Presenters;


use App\Model\ItemManager;
use Nette\Http\Session;

class CartPresenter extends BaseEshopPresenter
{
    private Session $session;

    private ItemManager $itemManager;

    private $itemsInCart = [];

    public $cart;

    public $prices = [];


    public function __construct(Session $session, ItemManager $itemManager)
    {
        parent::__construct();
        $this->session = $session;
        $this->itemManager = $itemManager;
    }

    public function renderDefault()
    {
        $this->cart = $this->session->getSection('cart');
        foreach ($this->cart->items as $item) {
            $cartItem = $this->itemManager->getItemFromId($item['itemId']);
            $this->itemsInCart[] = $cartItem;
            $prices[] = $cartItem->price;
        }

        $totalPrice = array_sum($prices);


        $this->template->itemsInCart = $this->itemsInCart;
        $this->template->totalPrice = $totalPrice;

    }


    public function handleRemoveFromCart($i)
    {
        $this->cart = $this->session->getSection('cart');

        $this->cart->items = array_values($this->cart->items);
        if (isset($this->cart->items[$i])) {
            unset($this->cart->items[$i]);
            $this->flashMessage('Položka odstraněna z košíku');
        }
        $this->redirect('this');

    }


}