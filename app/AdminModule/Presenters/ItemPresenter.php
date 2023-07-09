<?php


namespace App\AdminModule\Presenters;

use App\Model\ItemManager;
use App\Model\CategoryManager;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class ItemPresenter extends BaseAdminPresenter
{
    private ItemManager $itemManager;

    private CategoryManager $categoryManager;

    protected $editation = false;

    public function __construct(ItemManager $itemManager, CategoryManager $categoryManager)
    {
        parent::__construct();
        $this->categoryManager = $categoryManager;
        $this->itemManager = $itemManager;
    }

    public function renderList()
    {
        $this->template->items = $this->itemManager->getAllItems();
        $this->template->categories = $this->categoryManager->getAllCategory();
    }

    public function actionRemove(string $url = null)
    {
        $this->itemManager->removeItem($url);
        $this->flashMessage('Položka byla úspěšně odstraněna');
        $this->redirect('Item:list');
    }

    public function actionEditor(string $url = null)
    {
        if ($url) {
            if (!($item = $this->itemManager->getItem($url))) {
                $this->flashMessage('Položka nebyla nalezena');
            } else {
                $this->editation = true;
                $this['editorForm']->setDefaults($item);

            }
        }
    }

    protected function createComponentEditorForm(): Form
    {
        $form = new Form;
        $form->setRenderer(new BootstrapVerticalRenderer);
        $form->addHidden('id');
        $form->addText('title', 'Titulek')
            ->setRequired();
        $form->addText('url', 'URL')
            ->setRequired();
        $form->addUpload('picture', 'Obrázek:')
            ->setRequired(false)
            ->addCondition(Form::FILLED)
            ->addRule(Form::IMAGE);
        $form->addText('short_description', 'Popisek')
            ->setRequired();
        $form->addTextArea('description', 'Obsah');
        $categories = $this->categoryManager->getAllCategory();
        $form->addSelect('category_id', 'Kategorie:', $categories)
            ->setPrompt('Zvolte kategorii');

        $form->addText('price', 'Cena')->setRequired();
        $form->addSubmit('save', 'Uložit položku');
        $form->onSuccess[] = function (Form $form, array $values) {
            try {
                $this->itemManager->saveItem($values);
                $this->flashMessage('Položka byla úspěšně uložena');
                $this->redirect('Item:list');
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Položka s touto URL adresou již existuje.');
            }
        };
        return $form;
    }

}