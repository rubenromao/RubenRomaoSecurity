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
            'username' => 'rubenromao1',
            'firstname' => 'Ruben1',
            'lastname' => 'Romao1',
            'email' => 'ruben@rubenromao1.com',
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


/**
 * create 2nd admin
 */
try {
    $user = Mage::getModel('admin/user')
        ->setData(array(
            'username' => 'rubenromao2',
            'firstname' => 'Ruben2',
            'lastname' => 'Romao2',
            'email' => 'ruben@rubenromao2.com',
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

echo "User {$user->getUsername()} {$user->getUserId()} created successfully..." . PHP_EOL;


/**
 * create 3rd admin
 */
try {
    $user = Mage::getModel('admin/user')
        ->setData(array(
            'username' => 'rubenromao3',
            'firstname' => 'Ruben3',
            'lastname' => 'Romao3',
            'email' => 'ruben@rubenromao3.com',
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

echo "User {$user->getUsername()} {$user->getUserId()} created successfully..." . PHP_EOL;


/**
 * create 4th admin
 */
try {
    $user = Mage::getModel('admin/user')
        ->setData(array(
            'username' => 'rubenromao4',
            'firstname' => 'Ruben4',
            'lastname' => 'Romao4',
            'email' => 'ruben@rubenromao4.com',
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

echo "User {$user->getUsername()} {$user->getUserId()} created successfully..." . PHP_EOL;


/**
 * create 5th admin
 */
try {
    $user = Mage::getModel('admin/user')
        ->setData(array(
            'username' => 'rubenromao5',
            'firstname' => 'Ruben5',
            'lastname' => 'Romao5',
            'email' => 'ruben@rubenromao5.com',
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

echo "User {$user->getUsername()} {$user->getUserId()} created successfully..." . PHP_EOL;
