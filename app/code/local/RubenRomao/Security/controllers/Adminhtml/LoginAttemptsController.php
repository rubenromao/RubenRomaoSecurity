<?php

/**
 * @category  RubenRomao
 * @package   RubenRomao/Security
 * @author    Ruben Romao <ruben@rubenromao.com>
 * @copyright Copyright (c) 2017 Ruben Romao (http://rubenromao.com)
 * @license   RubenRomao/Security by Ruben Romao Licensed Under: MIT License
 */
class RubenRomao_Security_Adminhtml_LoginAttemptsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * indexAction
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
