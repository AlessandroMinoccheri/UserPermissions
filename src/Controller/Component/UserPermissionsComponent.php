<?php
namespace UserPermissions\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Component\FlashComponent;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use UserPermissions\Exception\MissingHandlerException;

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
	 * Boolean value which holds the configuration for the behavior in case of
	 * missing handlers.
	 */
	private $throwEx;

    /**
    * Initialization to get controller variable
    *
	* For this component available settings:
	* 	bool throwEx - default false - if set to true, an exception will be
	*		thrown, if a handler is about to be called but does not exist.
	*
    * @param array $config Configuration array for the component.
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
		$this->throwEx      = isset($config["throwEx"]) && $config["throwEx"];
    }

    /**
    * Initialization to get controller variable
    *
    * @param array $rules Array of rules for permissions.
    * @return bool false if user / group doesn't have permission, true if has permission
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
					if(!is_object($value)) {
						Log::write("warning", sprintf("controller is not an object (%s)", gettype($value)));
					}
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
    	if($key == $this->action) {
			if(!$this->checkForHandler($this->controller, $value) || !$this->controller->$value()){
				$this->redirectIfIsSet();
				
				$this->allow = false;
			}
		}
    }
	
	private function checkForHandler($controller, $handler)
	{
		if(!method_exists($controller, $handler)) {
			$msg = sprintf(
				"Controller %s=%s has no method called '%s'",
				is_object($controller) ? "class" : "type",
				is_object($controller) ? get_class($controller) : gettype($controller),
				$handler
			);
			Log::write("debug", $msg);
			if($this->throwEx) {
				throw new MissingHandlerException($msg);
			}
			return false;
		}
		
		return true;
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