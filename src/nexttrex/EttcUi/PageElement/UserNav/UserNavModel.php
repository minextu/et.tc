<?php namespace nexttrex\EttcUi\PageElement\UserNav;
use nexttrex\EttcUi\PageElement\AbstractPageElementModel;
use nexttrex\Ettc\Account\Account;

class UserNavModel extends AbstractPageElementModel
{
    function checkLogin()
    {
        return Account::checkLogin($this->mainModel->getDb());
    }
}
