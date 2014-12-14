<?php
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('UserPermissionsComponent', 'UserPermissions.Controller/Component');

class TestPermissionController extends Controller {
    // empty
}

class UserPermissionComponentTest extends CakeTestCase {

    public $UserPermissionsComponent = null;
    public $Controller = null;

    public function setUp() {
        parent::setUp();
        $Collection = new ComponentCollection();
        $this->UserPermissions = new UserPermissionsComponent($Collection);
        $CakeRequest = new CakeRequest();
        $CakeResponse = new CakeResponse();
        $this->Controller = new TestPermissionController($CakeRequest, $CakeResponse);
    }

    public function tearDown() {
        parent::tearDown();
        unset($this->UserPermissionComponent);
    }

    public function test() {
        $this->UserPermissions->initialize($this->Controller);

        $user_type = 'guest';
        $action = 'add';
        $controller = 'TestPermissionController';

        $rules = array(
            'user_type' => $user_type,
            'redirect' => '',
            'message' => 'You don\'t have permission to access this page',
            'action' =>  $action,
            'controller' =>  $this->Controller,
            'groups' => array(
                'guest' => array('register', 'logout', 'login'),
                'admin' => array('*'), 
                'admin-team' => array('register', 'add', 'logout', 'index', 'edit'),
                'user' => array('register', 'add', 'logout', 'index')
            ),
            'views' => array(
                'edit' => 'checkEdit',
                'delete' => 'checkDelete',
            ),
        );

        $result = $this->UserPermissions->allow($rules);
        debug($result);

        assert(true);
    }
}