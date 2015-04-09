<?php
namespace App\Test\TestCase\Controller\Component;

use UserPermissions\Controller\Component\UserPermissionsComponent;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;

class UserPermissionComponentTest extends TestCase {

    public $CurrencyConverter = null;
    public $controller = null;

    public function setUp() {
        parent::setUp();

        $this->UserPermissions = new UserPermissionsComponent(new ComponentRegistry(new Controller));
    }

    public function tearDown() {
        parent::tearDown();
        unset($this->UserPermission, $this->controller);
    }

    public function testGuestWithoutPermission() {
        //$this->UserPermissions->initialize($this->controller);

        $user_type = 'guest';
        $action = 'add';
        $controller = 'TestPermissionController';

        $rules = array(
            'user_type' => $user_type,
            'redirect' => '',
            'message' => 'You don\'t have permission to access this page',
            'action' =>  $action,
            'controller' =>  $this->controller,
            'groups' => array(
                'guest' => array('register', 'logout', 'login'),
                'admin' => array('*'), 
                'admin-team' => array('register', 'add', 'logout', 'index', 'edit'),
                'user' => array('register', 'add', 'logout', 'index')
            )
        );

        $result = $this->UserPermissions->allow($rules);
        $expected = '0';

        $this->assertEquals($expected, $result);
    }

    public function testUserWithoutPermission() {
        //$this->UserPermissions->initialize($this->controller);

        $user_type = 'user';
        $action = 'edit';
        $controller = 'TestPermissionController';

        $rules = array(
            'user_type' => $user_type,
            'redirect' => '',
            'message' => 'You don\'t have permission to access this page',
            'action' =>  $action,
            'controller' =>  $this->controller,
            'groups' => array(
                'guest' => array('register', 'logout', 'login'),
                'admin' => array('*'), 
                'admin-team' => array('register', 'add', 'logout', 'index', 'edit'),
                'user' => array('register', 'add', 'logout', 'index')
            )
        );

        $result = $this->UserPermissions->allow($rules);
        $expected = '0';

        $this->assertEquals($expected, $result);
    }

    public function testUserWithPermission() {
        //$this->UserPermissions->initialize($this->controller);

        $user_type = 'user';
        $action = 'add';
        $controller = 'TestPermissionController';

        $rules = array(
            'user_type' => $user_type,
            'redirect' => '',
            'message' => 'You don\'t have permission to access this page',
            'action' =>  $action,
            'controller' =>  $this->controller,
            'groups' => array(
                'guest' => array('register', 'logout', 'login'),
                'admin' => array('*'), 
                'admin-team' => array('register', 'add', 'logout', 'index', 'edit'),
                'user' => array('register', 'add', 'logout', 'index')
            )
        );

        $result = $this->UserPermissions->allow($rules);
        $expected = '1';

        $this->assertEquals($expected, $result);
    }

    public function testUserWithPermissionButFalseCallback() {
        //$this->UserPermissions->initialize($this->controller);

        $user_type = 'user';
        $action = 'add';
        $controller = 'TestPermissionController';

        $rules = array(
            'user_type' => $user_type,
            'redirect' => '',
            'message' => 'You don\'t have permission to access this page',
            'action' =>  $action,
            'controller' =>  $this->controller,
            'groups' => array(
                'guest' => array('register', 'logout', 'login'),
                'admin' => array('*'), 
                'admin-team' => array('register', 'add', 'logout', 'index', 'edit'),
                'user' => array('register', 'add', 'logout', 'index')
            ),
            'views' => array(
                'add' => 'firstCallback'
            ),
        );

        $result = $this->UserPermissions->allow($rules);
        $expected = '0';

        $this->assertEquals($expected, $result);
    }

    public function testUserWithPermissionAndTrueCallback() {
        //$this->UserPermissions->initialize($this->controller);

        $user_type = 'user';
        $action = 'add';
        $controller = 'TestPermissionController';

        $rules = array(
            'user_type' => $user_type,
            'redirect' => '',
            'message' => 'You don\'t have permission to access this page',
            'action' =>  $action,
            'controller' =>  $this->controller,
            'groups' => array(
                'guest' => array('register', 'logout', 'login'),
                'admin' => array('*'), 
                'admin-team' => array('register', 'add', 'logout', 'index', 'edit'),
                'user' => array('register', 'add', 'logout', 'index')
            ),
            'views' => array(
                'add' => 'secondCallback'
            ),
        );

        $result = $this->UserPermissions->allow($rules);
        $expected = '1';

        $this->assertEquals($expected, $result);
    }
}