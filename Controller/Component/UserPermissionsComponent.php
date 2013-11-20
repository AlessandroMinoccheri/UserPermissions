<?php
App::uses('Component', 'Controller', 'Session');

class UserPermissionsComponent extends Component {
	var $controller = '';
    var $components = array('RequestHandler');

	function initialize(&$controller, $settings = array()) { 
        $this->controller =& $controller; 
    } 


    public function allow($rules) {
    	App::uses('CakeSession', 'Model/Datasource');
		$user_id = CakeSession::read('Auth.User.id');

		$actions = array();
		$bool = '1';
		$redirect = '';
		$params = '';
		$controller = '';
		$message = '';

		foreach($rules as $key => $value){
			if($key == 'user_type'){
				$user_type = $value;
			}
			if($key == 'redirect'){
				$redirect = $value;
			}
			if($key == 'action'){
				$params = $value;
			}
			if($key == 'controller'){
				$controller = $value;
			}
			if($key == 'message'){
				$message = $value;
			}
		}

		foreach($rules as $key => $value){
			if(($key != 'user_type') && ($key != 'redirect') && ($key == $user_type)){
				foreach($value as $v){
					array_push($actions, $v);
				}
			}
		}

		foreach($rules as $key => $value){
			if(($key != 'user_type') && ($key != 'redirect') && ($key == $user_type)){
				foreach($value as $v){
					if($user_type == 'guest'){
						if(!isset($user_id)){
							if(!in_array($params, $actions)){
								if(!in_array('*', $actions)){
									if($redirect != ''){
										if($message != '')
											$this->controller->Session->setFlash($message);
										
										$this->controller->redirect($redirect);
									}
									else{
										$bool = '0';
									}
								}
							}
						}
					}
					else{
						if((!isset($user_id)) || (!in_array($params, $actions))){
							if(!in_array( '*', $actions)){
								if($redirect != ''){
									if($message != '')
										$this->controller->Session->setFlash($message);

									$this->controller->redirect($redirect);
								}
								else{
									$bool = '0';
								}
							}
						}
					}
				}
			}
		}

		return $bool;
    }
}