<?php
namespace UserPermissions\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Controller\Component\FlashComponent;

class UserPermissionsComponent extends Component {

    /**
     * Controller name
     *
     * @var string
     */
	public $controller = null;

    /**
     * Session
     *
     * @var string
     */
	public $session = null;

    /**
     * Components array
     *
     * @var array
     */
   	public $components = ['Flash'];

    private $actions;

    private $allow;

    private $redirect;

    private $params;

    private $message;

    private $userType;

    private $action;

    /**
    * Initialization to get controller variable
    *
    * @param string $event The event to use.
    */
    public function initialize(array $config)
    {
        parent::initialize($config);
        
        $this->controller = $this->_registry->getController();
        $this->session = $this->controller->request->session();

        $this->actions 		= array();
		$this->allow 		= true;
		$this->redirect 	= '';
		$this->params 		= '';
		$this->message 		= '';
		$this->userType 	= '';
		$this->action   	= null;
    }

    /**
    * Initialization to get controller variable
    *
    * @param array $rules Array of rules for permissions.
    * @return string '0' if user / group doesn't have permission, 1 if has permission
    */
    public function allow ($rules) {
    	$this->setUserValues();
    	$this->bindConfiguration($rules);

		if (!$this->applyGroupsRules($rules)) {
			$this->applyViewsRules($rules);
		}

		return $this->allow;
    }

    private function setUserValues()
    {
    	$userId = $this->session->read('Auth.User.id');

    	if (!isset($userId)) {
			$this->userType = 'guest';
		}
    }

    private function bindConfiguration(array $rules) 
    {
    	foreach($rules as $key => $value){
			switch($key){
				case "user_type":
			        $this->userType = $value;
			        break;
			    case "redirect":
			        $this->redirect = $value;
			        break;
			    case "action":
			        $this->action = $value;
			        break;
			    case "controller":
			        $this->controller = $value;
			        break;
			    case "message":
			        $this->message = $value;
			        break;
			}
		}

		foreach($rules['groups']  as $key => $value){
			if($key == $this->userType){
				foreach($value as $v){
					array_push($this->actions, $v);
				}
			}
		}
    }

    private function applyGroupsRules(array $rules)
    {
    	$existRulesForGroups = false;

    	if(isset($rules['groups'])){
			foreach($rules['groups'] as $key => $value){
				$this->searchForApplyGroupRules($key, $value);
			}
		}

		return $existRulesForGroups;
    }

    private function searchForApplyGroupRules($key)
    {
    	if($key == $this->userType){
    		if ($this->notInArrayAction()) {
				$this->redirectIfIsSet();
				
				$this->allow = false;
			}
		}
    }

    private function notInArrayAction()
    {
    	return ((!in_array('*', $this->actions)) && (!in_array($this->action, $this->actions)));
    }

    private function applyViewsRules(array $rules)
    {
    	if(isset($rules['views'])){
			foreach($rules['views'] as $key => $value){
				$this->searchForApplyViewRules($key, $value);
			}
		}
    }

    private function searchForApplyViewRules($key, $value)
    {
    	if($key == $this->action){
			if(!$this->controller->$value()){
				$this->redirectIfIsSet();
				
				$this->allow = false;
			}
		}
    }

    private function redirectIfIsSet()
    {
    	if($this->redirect != ''){
			if($this->message != ''){
				$this->Flash->set($this->message);
			}
			
			header("Location: " . $this->redirect);
			exit;
		}
	}
}