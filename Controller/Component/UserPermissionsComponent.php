<?php
App::uses('Component', 'Controller', 'Session');

class UserPermissionsComponent extends Component {
	var $controller = '';
    var $components = array('RequestHandler');

	function initialize(Controller $controller, $settings = array()) { 
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

		$find = 0;

		foreach($rules as $key => $value){
			if($key == 'user_type'){
				$user_type = $value;
			}
			if($key == 'redirect'){
				$redirect = $value;
			}
			if($key == 'action'){
				$action = $value;
			}
			if($key == 'controller'){
				$controller = $value;
			}
			if($key == 'message'){
				$message = $value;
			}
		}
		$c = 1;
		foreach( $rules['groups'] as $key => $value )
		{
			if( in_array ( $key, $user_type ) )
			{
				foreach($value as $v){
					array_push($actions, $v);
				}
			}
		}
		
		if(!isset($user_id))
			$user_type = array( 'guest' );

		if(isset($rules['groups'])){
			foreach($rules['groups'] as $key => $value){
				if(in_array($key, $user_type)){
					if(!in_array('*', $actions)){
						if(!in_array($action, $actions)){
							$find = 1;
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

		if(($find == 0) && (isset($rules['views']))){
			foreach($rules['views'] as $key => $value){
				if($key == $action){
					if(!$this->controller->$value()){
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

		return $bool;
    }
}