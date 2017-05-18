<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'admin_user_login_attempt'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('admin_user_login_attempt'))
    ->addColumn('login_attempt_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'identity' => true,
        ),
        'Login Attempt ID')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
            'nullable' => false,
        ),
        'User Id')
    ->addColumn('login_attempt', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null,
        array(
            'nullable' => false,
        ),
        'Login Attempt Time')
    ->addColumn('authentication_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '1',
        ),
        'Authentication Status')
    ->addForeignKey(
        $installer->getFkName('admin_user_login_attempt', 'user_id',
            'admin/user', 'user_id'), 'user_id',
        $installer->getTable('admin/user'), 'user_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Login Attempts');

$installer->getConnection()->createTable($table);

/**
 * Add col locked_until to admin_user
 */
$installer->getConnection()
    ->addColumn($installer->getTable('admin/user'), 'locked_until',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => true,
            'comment' => 'Locked Until'
        )
    );

$installer->endSetup();
