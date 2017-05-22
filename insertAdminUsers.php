<?php

/**
 * Create New admin User programmatically.
 */
require_once('./app/Mage.php');
umask(0);
Mage::app();


/**
 * create 1st admin
 */
try {
    $user = Mage::getModel('admin/user')
        ->setData(array(
            'username' => 'rubenromao6',
            'firstname' => 'Ruben6',
            'lastname' => 'Romao6',
            'email' => 'ruben@rubenromao6.com',
            'password' => 'rubenromao341',
            'is_active' => 1
        ))->save();
}
catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

/**
 * Assign Role Id
 */
try {
    $user->setRoleIds(array(1))  // Administrator role id is 1 , Here we can assign other role ids
        ->setRoleUserId($user->getUserId())
        ->saveRelations();
}
catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

echo PHP_EOL . "User {$user->getUsername()} {$user->getUserId()} created successfully..." . PHP_EOL;


