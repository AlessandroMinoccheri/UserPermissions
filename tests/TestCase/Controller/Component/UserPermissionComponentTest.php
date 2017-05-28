<?php
namespace App\Test\TestCase\Controller\Component;

use UserPermissions\Controller\Component\UserPermissionsComponent;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;
use Cake\Routing\Router;

class UserPermissionsTestController extends Controller
{
    public function __construct($request = null, $response = null)
    {
        $request->webroot = '/';
        Router::setRequestInfo($request);
        parent::__construct($request, $response);
    }
     
    public function firstCallback(){
        return '0';
    }

    public function secondCallback(){
        return '1';
    }
}

class UserPermissionComponentTest extends TestCase {
    private $userPermissions;
    public $controller = null;

    public function setUp() {
        parent::setUp();

        $this->request = $this->getMockBuilder('Cake\Network\Request')
            ->setMethods(['is', 'method'])
            ->getMock();

        $this->response = $this->getMockBuilder('Cake\Network\Response')
            ->setMethods(['stop'])
            ->getMock();

        $this->controller = new UserPermissionsTestController($this->request, $this->response);
        $this->userPermissions = new UserPermissionsComponent($this->controller->components());
    }

    public function tearDown() {
        parent::tearDown();
        unset($this->userPermission, $this->controller);
    }

    public function testGuestWithoutPermission() {
        $userType = 'guest';
        $action = 'add';
        $controller = 'TestPermissionController';

        $rules = array(
            'user_type' => $userType,
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

        $result = $this->userPermissions->allow($rules);
        $expected = false;

        $this->assertEquals($expected, $result);
    }

    public function testUserWithoutPermission() {
        $userType = 'user';
        $action = 'edit';
        $controller = 'TestPermissionController';

        $rules = array(
            'user_type' => $userType,
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

        $result = $this->userPermissions->allow($rules);
        $expected = false;

        $this->assertEquals($expected, $result);
    }

    public function testUserWithPermission() {
        $userType = 'user';
        $action = 'add';
        $controller = 'TestPermissionController';

        $rules = array(
            'user_type' => $userType,
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

        $result = $this->userPermissions->allow($rules);
        $expected = true;

        $this->assertEquals($expected, $result);
    }

    public function testUserWithPermissionButFalseCallback() {
        $userType = 'user';
        $action = 'add';
        $controller = 'TestPermissionController';

        $rules = array(
            'user_type' => $userType,
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

        $result = $this->userPermissions->allow($rules);
        $expected = false;

        $this->assertEquals($expected, $result);
    }

    public function testUserWithPermissionAndTrueCallback() {
        $userType = 'user';
        $action = 'add';
        $controller = 'TestPermissionController';

        $rules = array(
            'user_type' => $userType,
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

        $result = $this->userPermissions->allow($rules);
        $expected = true;

        $this->assertEquals($expected, $result);
    }
}