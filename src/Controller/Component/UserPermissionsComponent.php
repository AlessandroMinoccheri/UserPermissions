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
    }

/**
* Initialization to get controller variable
*
* @param array $rules Array of rules for permissions.
* @return string '0' if user / group doesn't have permission, 1 if has permission
*/
    public function allow ($rules) {
    	$user_id = $this->session->read('Auth.User.id');

		$actions 	= array();
		$bool 		= '1';
		$redirect 	= '';
		$params 	= '';
		$controller = '';
		$message 	= '';
		$userType 	= '';
		$find 		= 0;

		//setting default options
		foreach($rules as $key => $value){
			switch($key){
				case "user_type":
			        $userType = $value;
			        break;
			    case "redirect":
			        $redirect = $value;
			        break;
			    case "action":
			        $action = $value;
			        break;
			    case "controller":
			        $controller = $value;
			        break;
			    case "message":
			        $message = $value;
			        break;
			}
		}

		//push into array group actions
		foreach($rules['groups']  as $key => $value){
			if($key == $userType){
				foreach($value as $v){
					array_push($actions, $v);
				}
			}
		}

		if(!isset($userId)){
			$userType = 'guest';
		}

		if(isset($rules['groups'])){
			foreach($rules['groups'] as $key => $value){
				if($key == $userType){
					if(!in_array('*', $actions)){
						if(!in_array($action, $actions)){
							$find = 1;
							if($redirect != ''){
								if($message != ''){
									$this->Flash->set($message);
								}

								header("Location: " . $redirect);
								exit;
							}
							else{
								$bool = '0';
							}
						}
					}
				}
			}
		}

		if(($find == 0) && (isset($rules['views']))){
			foreach($rules['views'] as $key => $value){
				if($key == $action){
					if(!$this->controller->$value()){
						if($redirect != ''){
							if($message != ''){
								$this->Flash->set($message);
							}
							
							header("Location: " . $redirect);
							exit;
						}
						else{
							$bool = '0';
						}
					}
				}
			}
		}

		return $bool;
    }
}