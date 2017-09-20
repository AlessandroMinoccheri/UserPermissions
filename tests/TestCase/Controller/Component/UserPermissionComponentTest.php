<?php
namespace App\Test\TestCase\Controller\Component;

use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;
use Cake\Routing\Router;
use UserPermissions\Controller\Component\UserPermissionsComponent;
use UserPermissions\Exception\MissingHandlerException;

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
        unset($this->userPermissions, $this->controller);
    }

    public function testGuestWithoutPermission() {
        $userType = 'guest';
        $action = 'add';

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
	
	public function testMissingHandlerBeingIgnored() {
		$userType = "user";
		$action = "action";
		
		$rules = array(
			"user_type" => $userType,
			"redirect" => "",
			"message" => "You don't have permission to access this page",
			"action" => $action,
			"controller" => $this->controller,
			"groups" => array(
				$userType => array($action)
			),
			"views" => array(
				$action = "handlerThatDoesNotExistForSure"
			)
		);
		
		$result = $this->userPermissions->allow($rules);
		$expected = false;
		
		$this->assertEquals($expected, $result);
	}
	
	/**
	 * @expectedException \UserPermissions\Exception\MissingHandlerException
	 */
	public function testMissingHandlerThrowsException() {
		$this->userPermissions->initialize(array("throwEx" => true));
		$userType = "user";
		$action = "action";
		
		$rules = array(
			"user_type" => $userType,
			"redirect" => "",
			"message" => "You don't have permission to access this page",
			"action" => $action,
			"controller" => $this->controller,
			"groups" => array(
				$userType => array($action)
			),
			"views" => array(
				$action = "handlerThatDoesNotExistForSure"
			)
		);
		
		$result = $this->userPermissions->allow($rules);
	}
}
