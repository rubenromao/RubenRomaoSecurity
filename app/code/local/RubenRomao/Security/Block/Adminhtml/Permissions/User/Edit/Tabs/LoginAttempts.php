<?php

/**
 * @category  RubenRomao
 * @package   RubenRomao/Security
 * @author    Ruben Romao <ruben@rubenromao.com>
 * @copyright Copyright (c) 2017 Ruben Romao (http://rubenromao.com)
 * @license   RubenRomao/Security by Ruben Romao Licensed Under: MIT License
 */
class RubenRomao_Security_Block_Adminhtml_Permissions_User_Edit_Tabs_LoginAttempts
    extends Mage_Adminhtml_Block_Template
        implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    const MAX_RECORDS = 30;

    public function __construct()
    {
        parent::_construct();
        $this->setTemplate('rubenromao/security/login-attempts.phtml');
    }

    /**
     * Return User Failed Login Attempts
     *
     * @return array
     */
    public function getAdminLoginAttempts()
    {

        /**
         * Get admin user login attempts data
         */
        $attempts = Mage::getSingleton('admin/user')->getCollection()
            ->addFieldToSelect(array(
                    'username', 'firstname', 'lastname', 'is_active', 'locked_until'
                ))
            ->addFieldToFilter('main_table.user_id', array(
                    'eq' => Mage::getSingleton('admin/session')->getUser()->getUserId()
                ))
            ->setOrder('login_attempt', 'DESC');

        $attempts->getSelect()->join(array(
            'login' => $attempts->getResource()->getTable('rubenromao_security/admin_user_login_attempt'
            )),
            'login.user_id = main_table.user_id',
            array(
                'login_attempt' => 'login.login_attempt',
            ));

        /**
         * retrieve only the last 30 entries
         */
        $attempts->getSelect()->limit(self::MAX_RECORDS);

        return $attempts->getData();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Login Attempts');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Last 30 Login Attempts');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'roles_section';
    }
}
