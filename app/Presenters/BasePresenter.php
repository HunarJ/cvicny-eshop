<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Presenter;
use Nette\Application\AbortException;
use App\Forms\FormFactory;



class BasePresenter extends Presenter
{
    /** Zpráva typu informace. */
    const MSG_INFO = 'info';
    /** Zpráva typu úspěch. */
    const MSG_SUCCESS = 'success';
    /** Zpráva typy chyba. */
    const MSG_ERROR = 'danger';

    protected FormFactory $formFactory;

    public function injectFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isAllowed($this->getName(), $this->getAction())){
            $this->flashMessage('Pro tuto akci je nutné se přihlásit');
            $this->redirect(':Core:Administration:login');

        }
    }

    protected function beforeRender()
    {
        parent::beforeRender();
        $this->template->admin = $this->getUser()->isInRole('admin');
        $this->template->domain = $this->getHttpRequest()->getUrl()->getHost();
        $this->template->formPath = __DIR__ . '/../templates/forms/form.latte';
    }

}
