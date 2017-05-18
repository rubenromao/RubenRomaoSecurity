<?php

/**
 * @category  RubenRomao
 * @package   RubenRomao/Security
 * @author    Ruben Romao <ruben@rubenromao.com>
 * @copyright Copyright (c) 2017 Ruben Romao (http://rubenromao.com)
 * @license   RubenRomao/Security by Ruben Romao Licensed Under: MIT License
 */
class RubenRomao_Security_Model_LoginAttempts extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('rubenromao_security/loginattempts');
    }
}
