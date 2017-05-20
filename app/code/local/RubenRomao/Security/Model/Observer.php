<?php

/**
 * @category  RubenRomao
 * @package   RubenRomao/Security
 * @author    Ruben Romao <ruben@rubenromao.com>
 * @copyright Copyright (c) 2017 Ruben Romao (http://rubenromao.com)
 * @license   RubenRomao/Security by Ruben Romao Licensed Under: MIT License
 */
class RubenRomao_Security_Model_Observer
{
    const MINUTES = 60;
    const MAXATTEMPTS = 3;
    const SYSTEM_SECURITY_MODULE_LOCK_STATUS = 'rubenromao_security_config/rubenromao_security_system_config/is_active';
    const SYSTEM_SECURITY_MODULE_LOCK_TIME = 'rubenromao_security_config/rubenromao_security_system_config/lock_for';

    /**
     * Check if admin user fails to login more than 3 times and lock his account
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminSessionUserLoginFailed($observer)
    {

        /**
         * check if module is active and has time set
         */
        if(Mage::getStoreConfig(self::SYSTEM_SECURITY_MODULE_LOCK_STATUS) == '1'
            && Mage::getStoreConfig(self::SYSTEM_SECURITY_MODULE_LOCK_TIME) > 0)
        {

            /**
             * get admin user by username
             */
            $user = Mage::getSingleton('admin/user')->getCollection()
                ->addFieldToSelect(array(
                        'user_id', 'locked_until'
                    ))
                ->addFieldToFilter('username', array(
                        'eq' => $observer->getUserName()
                    ))
                ->load();

            $user_data = $user->getFirstItem()->getData();

            /**
             * avoid non existing users
             */
            if ($user->getSize()) {

                /**
                 * Save failed attempt data into login attempts
                 */
                $add_attempt = Mage::getSingleton('rubenromao_security/loginattempts')
                    ->addData(array(
                        'user_id' => $user_data['user_id'],
                        'login_attempt' => now()
                    ));
                try {
                    $add_attempt->save();
                } catch (Exception $e) {
                    Mage::logException($e);
                }

                /**
                 * get last minute(s)
                 */
                $last_minutes = date("Y-m-d H:i:s", (strtotime(now()) - self::MINUTES));

                /**
                 * get admin user attempts
                 * filtered by last 3 attempts during the last minute
                 */
                $attempts = Mage::getModel('rubenromao_security/loginattempts')->getCollection()
                    ->addFieldToSelect(array(
                            'user_id', 'login_attempt', 'authentication_status'
                        ))
                    ->addFieldToFilter('login_attempt', array(
                            'gteq' => $last_minutes
                        ))
                    ->addFieldToFilter('login_attempt', array(
                            'lteq' => now()
                        ))
                    ->setOrder('login_attempt', 'DESC')
                    ->load();

                /**
                 * join tables
                 */
                $attempts->getSelect()->join(
                    array(
                        'admin_user' => 'admin_user'
                    ),'admin_user.user_id = main_table.user_id'
                );

                /**
                 * set limit
                 */
                $attempts->getSelect()->limit(3);

                /**
                 * check attempts number
                 */
                if ($attempts->getSize() === self::MAXATTEMPTS) {

                    /**
                     * get system lock for value
                     */
                    $lock_for = Mage::getStoreConfig(self::SYSTEM_SECURITY_MODULE_LOCK_TIME);
                    $lock = strtotime(date("Y-m-d H:i:s")) + $lock_for;
                    $lock_until = date("Y-m-d H:i:s", $lock);

                    /**
                     * lock user access
                     */
                    $lock_access = Mage::getModel('admin/user')
                        ->load($user_data['user_id'])
                        ->addData(array(
                                'is_active' => 0,
                                'locked_until' => $lock_until
                            ));
                    try {
                        $lock_access->setId($user_data['user_id'])->save();
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }

                    /**
                     * set loginattempts status to 0 (locked)
                     */
                    $lock_status = Mage::getModel('rubenromao_security/loginattempts')->getCollection()
                        ->addFieldToFilter('user_id', array(
                                'eq' => $user_data['user_id']
                            ))
                        ->setDataToAll('authentication_status', 0);
                    try {
                        $lock_status->save();
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
            }
        }
    }

    /**
     * Check if admin user has locked until timestamp set and unlocks it or not
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminUserAuthenticateBefore($observer)
    {

        /**
         * check if module is active
         */
        if (Mage::getStoreConfig(self::SYSTEM_SECURITY_MODULE_LOCK_STATUS) == '1')
        {

            /**
             * get admin user by username
             */
            $user = Mage::getSingleton('admin/user')->getCollection()
                ->addFieldToSelect(array(
                        'user_id', 'locked_until'
                    ))
                ->addFieldToFilter('username', array(
                        'eq' => $observer['username']
                    ))
                ->load();

            $user_data = $user->getFirstItem()->getData();

            /**
             * avoid non existing users
             */
            if ($user->getSize()) {

                /**
                 * get collection to check if user is locked
                 */
                $user_status = Mage::getSingleton('admin/user')->getCollection()
                    ->addFieldToSelect(array(
                        'is_active', 'locked_until'
                    ))
                    ->addFieldToFilter('user_id', array(
                        'eq' => $user_data['user_id']
                    ))
                    ->addFieldToFilter('is_active', array(
                        'eq' => 0
                    ))
                    ->addFieldToFilter('locked_until', array(
                        'lteq' => now()
                    ));

                /**
                 * if exists unlock
                 */
                if ($user_status->getSize()) {

                    /**
                     * unlock user access
                     */
                    $unlock_access = Mage::getModel('admin/user')
                        ->load($user_data['user_id'])
                        ->addData(array(
                                'is_active' => 1,
                                'locked_until' => null
                            ));
                    try {
                        $unlock_access->setId($user_data['user_id'])->save();
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }

                    /**
                     * set loginattempts status to 1 (unlocked)
                     */
                    $unlock_status = Mage::getModel('rubenromao_security/loginattempts')->getCollection()
                        ->addFieldToFilter('user_id', array(
                                'eq' => $user_data['user_id']
                            ))
                        ->setDataToAll('authentication_status', 1);
                    try {
                        $unlock_status->save();
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
            }
        }
    }
}
