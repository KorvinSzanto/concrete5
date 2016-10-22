<?php

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Entity\Attribute\Category as AttributeCategory;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\UserKey;
use Concrete\Core\Entity\Attribute\Key\UserValue;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Entity\User\UserSignup;

abstract class UserTestCase extends ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = array(
        'UserGroups', 'Groups',
        'TreeTypes', 'TreeNodes', 'TreeNodePermissionAssignments',
        'PermissionKeyCategories', 'PermissionKeys', 'TreeNodeTypes', 'Trees',
        'TreeGroupNodes',
    ); // so brutal

    protected $metadatas = array(
        User::class,
        UserSignup::class,
        AttributeCategory::class,
        Key::class,
        UserValue::class,
        UserKey::class,
    );

    protected function setUp()
    {
        parent::setUp();
        Category::add('user');
    }

    protected static function createUser($uName, $uEmail)
    {
        $user = \Concrete\Core\User\UserInfo::add(
            array('uName' => $uName, 'uEmail' => $uEmail)
        );

        return $user;
    }
}
