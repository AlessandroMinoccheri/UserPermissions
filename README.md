UserPermissions
===============

a cakephp plugin to allow group of user or single user to view specific page

---

##Background

In cakephp manage permission to view a page for a user or a group of user can be difficult or you need to make many check to understand permission of that user.
With UserPermissions plugin you can manage in a simple array all your page for every controller, easy, simple and very quickly to apply.

---

##Requirements

* CakePHP 2.x
* PHP5

---

#Installation

To install the plugin inside your cakephp project you can do this:

_[GIT Submodule]_

* Open your console 
* Go inside the folder you have got
* Launch the command: 
```
git submodule add -f https://github.com/AlessandroMinoccheri/UserPermissions.git 
app/Plugin/UserPermissions/.
```


_[Manual]_

* Download this: [https://github.com/AlessandroMinoccheri/UserPermissions/archive/master.zip](https://github.com/AlessandroMinoccheri/UserPermissions/archive/master.zip)
* Unzip that download.
* Copy the resulting folder to `app/Plugin`
* Rename the folder you just copied to `UserPermissions`

_[GIT Clone]_

In your `Plugin` directory type:

    git clone git clone https://github.com/AlessandroMinoccheri/UserPermissions.git UserPermissions
    
---

##Enable plugin

In cakephp 2.x you need to enable the plugin your app/Config/bootstrap.php file:
```
CakePlugin::load('UserPermissions');
```

If you are already using CakePlugin::loadAll();, then this is not necessary.

---

##Usage

You can run this plugin from all your controller (excpet AppController) inside beforeFilter action, because every time that user try to load a page there is a check of permission to understand if that user can access to the next page.
You need to include the plugin component inside your controller like this:

```
public $components = array(
    'UserPermissions.UserPermissions'
);
```

If you have already declare your variable $components you can do something like this:
```
public $components = array(
    'OtherComponent.Other',
    'UserPermissions.UserPermissions',
);
```

Inside your action beforeFilter you can set rules for group of user that you want. For example:

```
$rules = array(
	'user_type' => $user_type,
	'redirect' => '/projects/',
	'message' => 'You don't have permission to access this page',
	'action' =>  $this->params['action'],
	'controller' =>  $this->params['controller'],
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
```

And to run the check function you only need this line of code:
```
$this->UserPermissions->allow($rules);
```

Now everytime that you load a page nside this controller the plugin check permission for the user that try to access to a page.

---

##Parameters
There are some parameters that you can use into this plugin:
* user_type
* redirect
* message
* action
* controller
* groups
* views
* 
#user_type
This parameter is the group name of the user (or the username if you check by username not frm user group).
You need to pass this information to the plugin to compare who runs the page to the permission that you give.
usually inside table users, every user have a group field to understand if is an admin, a normal user...
You can pass the value user_type for example in this mode:

```
public function beforeFilter () {
		parent::beforeFilter(); 
        
        //default user_type if not logged
		$user_type = 'guest';
        
        //if you have stored filed group inside session
		if($this->Session->read('is_logged')){
			$auth_user = $this->Auth->user();
			$user_type = $auth_user['group'];
		}
        
        //pass user type to the plugin
		$rules = array(
			'user_type' => $user_type,
			'redirect' => '/projects/',
			'message' => 'No permission',
			'action' =>  $this->params['action'],
			'controller' =>  $this->params['controller'],
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

		$this->UserPermissions->allow($rules);
	}
```

I advise you tu use field group inside your table and insert it inside session or use a function to retrieve this information.
if you don't want to use group field, isnt iportant. The important is , for example, would like to use username, inside the array groups you need to insert the username lis not he group list for example:

```
$user_type = $this->getUsernameOfuserLgged(); //function to get the username of the user logged in
$rules = array(
	'user_type' => $user_type,
	'redirect' => '/projects/',
	'message' => 'No permission',
	'action' =>  $this->params['action'],
	'controller' =>  $this->params['controller'],
	'groups' => array(
		'guest' => array('register', 'logout', 'login'),
		'user1' => array('*'), 
		'user2' => array('register', 'add', 'logout', 'index', 'edit'),
		'user3' => array('register', 'add', 'logout', 'index')
	),
	'views' => array(
		'edit' => 'checkEdit',
		'delete' => 'checkDelete',
	),
);
```

#Redirect
This parameter allow you to set a redirect page if the user doesn't have permission to access at the next page.
You can set this parameter like this:

```
'redirect' => '/projects/index',
```

or

```
'redirect' => '/products/test/1',
```

If you don't want to use this parameter you can leave it blank or you can omitted it like this:

```
$user_type = $this->getUsernameOfuserLgged(); //function to get the username of the user logged in
$rules = array(
	'user_type' => $user_type,
	'message' => 'No permission',
	'action' =>  $this->params['action'],
	'controller' =>  $this->params['controller'],
	'groups' => array(
		'guest' => array('register', 'logout', 'login'),
		'user1' => array('*'), 
		'user2' => array('register', 'add', 'logout', 'index', 'edit'),
		'user3' => array('register', 'add', 'logout', 'index')
	),
	'views' => array(
		'edit' => 'checkEdit',
		'delete' => 'checkDelete',
	),
);
```

#Message
This parameter allow you to set a speicifc message inside flash message session.
You can insert the string that you want, you can leave it blank or you can to omitted id if you don't want to set a specific message.

#Action
This parameter is mandatory and it's standard.
You have always to pass this parameter in this way
```
'action' =>  $this->params['action'],
```

You can't omitted it or you can't modified it, is a standard parameter.

#Controller
This parameter is mandatory and it's standard.
You have always to pass this parameter in this way:
```
'controller' =>  $this->params['controller'],
```

You can't omitted it or you can't modified it, is a standard parameter.

#Groups
This is an array of array.
Inside this array you can create list of user group to spcify which page can be ciew by that user group, or user.
You can insert inside this array, gropu name or username (in base of user_ype), inside it you can specify the action of this controller that this group can access to it for example:
```
'groups' => array(
	'guest' => array('register', 'login'),
	'admin' => array('*'), 
	'admin-team' => array('register', 'add', 'logout', 'index', 'edit'),
	'user' => array('register', 'add', 'logout', 'index')
),
```

In this case you have told to the plugin:
* guest: not logged is a standard parameter, automatically the plugin understand if the user is a guset or not. 
You have specify that not logged user can access only to register and login view. 
* admin: you have specify that user in group admin can access to all views of this controller. The character * means that this user is able to view all views of this controller
* admin-team: you have specify that user in group admin-team can access to these views: register, add, logout, index and edit.
* user: you have specify that user in group user can access to these views: register, logout and index.

Is important to know that you can specify some standard value:
* guest : isn't a group of your system, is a standard for the plugin to understand which page can access user that isn't logged in. 
*  character *: This character specify that this group can access to all views of this controller

if you omitt some user group or for example user guest, means that the user group, or guest can't access to any page of this controller.

#Views
This parameter is an array of callback function.
Example:
```
'views' => array(
	'edit' => 'checkEdit',
	'delete' => 'checkDelete',
),
```

In this case you have specify that if the next page is edit the plugin  check if that user can access to that page with array groups, after if the user can access to that page call the function with the name that you passed. 
This function must be to return a true or false value.
If return true the user can access to that page, if false can't access to it.
The function that you call must be inside the controller where you call the plugin function, have to be the same name that you passed into the string and must be to return a value true or false.

Example:
```
public function beforeFilter () {
		parent::beforeFilter(); 
        
        //default user_type if not logged
		$user_type = 'guest';
        
        //if you have stored filed group inside session
		if($this->Session->read('is_logged')){
			$auth_user = $this->Auth->user();
			$user_type = $auth_user['group'];
		}
        
        //pass user type to the plugin
		$rules = array(
			'user_type' => $user_type,
			'redirect' => '/projects/',
			'message' => 'No permission',
			'action' =>  $this->params['action'],
			'controller' =>  $this->params['controller'],
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

		$this->UserPermissions->allow($rules);
	}
	
	public function checkEdit(){
		$auth_user = $this->Auth->user();
		$user_id = $auth_user['id'];
		echo($user_id.' - '.$_GET['id']);
		if($user_id == $_GET['id']){
			return true;
		}	
		else{
			return false;
		}
	}

	public function checkDelete(){
		if($this->Session->read('id') == $_GET['id']){
			return true;
		}	
		else{
			return false;
		}
	}
	
```

In this case there are two callbacks function:
checkEdit and checkDelete.

If you are into the page add this function aren't called.
But if you try to access to the page edit, after understand if the user can access to that page by gripus array, the plugin call checkEdit function.
This function compare id of the user logged and the id passed by get: means that only the same user can access to that page. if you try to accss to the edit page of another user return an error and you redirect to another page.
Same thing to the page delete and the funciton checkDelete().
