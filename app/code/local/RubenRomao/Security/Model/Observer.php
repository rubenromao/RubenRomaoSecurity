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
    const ONEMINUTE = 60;
    const MAXATTEMPTS = 3;

    public function adminSessionUserLoginFailed($observer)
    {

        /*
         * get admin user by username
         */
        $user = Mage::getSingleton('admin/user')->getCollection()
            ->addFieldToSelect(array('user_id', 'locked_until'))
            ->addFieldToFilter('username', array('eq' => $observer->getUserName()))
            ->load();

        $user_data = $user->getFirstItem()->getData();

        /*
         * avoid non existing users
         */
        if ($user->getSize()) {

            /*
             * Save failed attempt data into login attempts
             */
            $add_attempt = Mage::getSingleton('rubenromao_security/loginattempts')
                ->addData(array(
                    'user_id' => $user_data['user_id'],
                    'login_attempt' => now()
                ));
            try {
                $add_attempt->save();
            }
            catch (Exception $e) {
                Mage::logException($e);
            }

            /*
             * get last minute
             */
            $last_minute = date("Y-m-d H:i:s", (strtotime(now()) - self::ONEMINUTE));

            /*
             * get admin user attempts
             * filtered by last 3 attempts during the last minute
             */
            $attempts = Mage::getModel('rubenromao_security/loginattempts')->getCollection()
                ->addFieldToSelect(array('user_id', 'login_attempt', 'authentication_status'))
                ->addFieldToFilter('login_attempt', array('gteq' => $last_minute))
                ->addFieldToFilter('login_attempt', array('lteq' => now()))
                ->setOrder('login_attempt', 'DESC')
                ->load();

            /*
             * join tables
             */
            $attempts->getSelect()->join(
                array(
                    'admin_user' => 'admin_user'
                ),
                'admin_user.user_id = main_table.user_id'
            );

            /*
             * set limit
             */
            $attempts->getSelect()->limit(3);

            /*
             * check attempts number
             */
            if ($attempts->getSize() === self::MAXATTEMPTS) {

                /*
                 * get system lock for value
                 */
                $lock_for = Mage::getStoreConfig('rubenromao_security_config/rubenromao_security_system_config/lock_for');
                $lock = strtotime(date("Y-m-d H:i:s")) + $lock_for;
                $lock_until = date("Y-m-d H:i:s", $lock);

                /*
                 * lock access
                 */
                $lock_access = Mage::getModel('admin/user')
                    ->load($user_data['user_id'])
                    ->addData(
                        array(
                            'is_active' => 0,
                            'locked_until' => $lock_until
                        )
                    );

                $lock_status = Mage::getModel('rubenromao_security/loginattempts')->getCollection()
                    ->addFieldToFilter('user_id', array('eq' => $user_data['user_id']))
                    ->setDataToAll('authentication_status', 0);

                try {
                    $lock_access->setId($user_data['user_id'])->save();
                    $lock_status->save();
                }
                catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
    }

    public function adminUserAuthenticateBefore($observer)
    {
        /*
          * get admin user by username
          */
        $user = Mage::getSingleton('admin/user')->getCollection()
            ->addFieldToSelect(array('user_id', 'locked_until'))
            ->addFieldToFilter('username', array('eq' => $observer['username']))
            ->load();

        $user_data = $user->getFirstItem()->getData();

        /*
         * avoid non existing users
         */
        if ($user->getSize()) {

            /*
             * get collection and join tables check if user is locked
             */
            $user_status = Mage::getSingleton('admin/user')->getCollection()
                ->addFieldToSelect(array('is_active', 'locked_until'))
                ->addFieldToFilter('is_active', array('eq' => 1))
                ->addFieldToFilter(
                    array('locked_until','locked_until'),
                    array(
                        array('null' => true),
                        array('lt' => now())
                    )
                );

            /*
             * Join admin_user with admin_user_login_attempt -> FK user_id
             */
            $user_status->getSelect()->join(
                array(
                    'admin_user_login_attempt' => 'admin_user_login_attempt'
                ),
                'admin_user_login_attempt.user_id = main_table.user_id'
            );

            /*
             * if exists unlock
             */
            if ($user_status->getSize()) {

                /*
                 * unlock access
                 */
                $unlock_access = Mage::getModel('admin/user')
                    ->load($user_data['user_id'])
                    ->addData(
                        array(
                            'is_active' => 1,
                            'locked_until' => null
                        )
                    );

                $unlock_status = Mage::getModel('rubenromao_security/loginattempts')->getCollection()
                    ->addFieldToFilter('user_id', array('eq' => $user_data['user_id']))
                    ->setDataToAll('authentication_status', 1);

                try {
                    $unlock_access->setId($user_data['user_id'])->save();
                    $unlock_status->save();
                }
                catch (Exception $e) {
                    Mage::logException($e);
                }

            } else {

                //$this->adminSessionUserLoginFailed($observer);

            }
        }
    }
}
