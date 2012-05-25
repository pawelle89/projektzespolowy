<?php
require_once 'Zend/Controller/Action.php';
require_once 'Userticket.php';
require_once 'Guestticket.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

class AdminController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->view->baseUrl = $this->_request->getBaseUrl();
        
        Zend_Loader::loadClass('Userticket');
        Zend_Loader::loadClass('Guestticket');
        
        
  
    
    }
    
    public function logoutAction()
 {
   $auth = Zend_Auth::getInstance();
$auth->clearIdentity();
$this->_redirect('/index/index');
 }
 
 public function authAction(){
   $request 	= $this->getRequest();
   $registry 	= Zend_Registry::getInstance();
$auth		= Zend_Auth::getInstance(); 

$DB = $registry['DB'];
	
   $authAdapter = new Zend_Auth_Adapter_DbTable($DB);
   $authAdapter->setTableName('users')
               ->setIdentityColumn('username')
               ->setCredentialColumn('password');    

// Set the input credential values
$uname = $request->getParam('username');
$paswd = $request->getParam('password');

   if($uname == ''){
      // $this->view->assign('nouser','Brak nazwy użytkownika.');
  $this->_redirect('/admin/loginform');
  }
  else if($paswd == ''){
     //  $this->view->assign('nopaswd','Brak hasła.');
  $this->_redirect('/admin/loginform');
  }

   $authAdapter->setIdentity($uname);
   $authAdapter->setCredential(md5($paswd));

   // Perform the authentication query, saving the result
   $result = $auth->authenticate($authAdapter);

   if($result->isValid()){
  $data = $authAdapter->getResultRowObject(null,'password');
  $auth->getStorage()->write($data);
  $this->_redirect('/admin/index');
}else{
  $this->_redirect('/admin/loginform');
}
}

public function loginformAction()
 {
   $request = $this->getRequest();  
$this->view->assign('action', $request->getBaseURL()."/admin/auth");  
   $this->view->assign('title', 'Logowanie');
   $this->view->assign('username', 'Nazwa użytkownika');	
   $this->view->assign('password', 'Hasło');	
    
 }
    
   public function edituserAction() 
   {
       $auth		= Zend_Auth::getInstance(); 
	
	if(!$auth->hasIdentity()){
	  $this->_redirect('/admin/loginform');
	}
        else {
            $request = $this->getRequest(); 
	$role		= $auth->getIdentity();
        if(!($role->role == "admin")){
            $this->_redirect('/admin/loginform');
        }
        }
       
      $this->view->assign('title','Edytuj Ticket');
      
      $userticket = new Userticket(); 

   if ($this->_request->isPost()) {
      Zend_Loader::loadClass('Zend_Filter_StripTags');
      $filter = new Zend_Filter_StripTags(); 

      $id = (int)$this->_request->getPost('id');
      $author = $filter->filter($this->_request->getPost('author'));
      $author = trim($author);
      $cathegory = trim($filter->filter($this->_request->getPost('cathegory'))); 
      $problem_describe = trim($filter->filter($this->_request->getPost('problem_describe'))); 
      $send_data = trim($filter->filter($this->_request->getPost('send_data'))); 
      $end_data = trim($filter->filter($this->_request->getPost('end_data'))); 
      $status = trim($filter->filter($this->_request->getPost('status'))); 
      $ip_number = trim($filter->filter($this->_request->getPost('ip_number'))); 

      // jeżeli admin wybierze status zakończony to data musi się uzupełnić
      // na dzisiejsza, a jeżeli admin da dzisiejszą datę to status powninien
      // zmienić się na zakończony
      
      // aktualna data
      $obecna_data = date("Y-m-d");
      
      if($end_data == $obecna_data)
      {
          $status = 'zakończony';
      }
      else if($status == 'zakończony')
      {
          $end_data = $obecna_data;
      }
      
      
      if ($id !== false) {
         if ($author != '' && $cathegory != '' && $problem_describe != '' && $send_data != '' 
              && $end_data != '' && $status != '' && $ip_number != '') {
            $data = array(
               'author' => $author,
               'cathegory' => $cathegory,
               'problem_describe' => $problem_describe,
               'send_data' => $send_data,
               'end_data' => $end_data,
               'status' => $status,
               'ip_number' => $ip_number,
            );
            $where = 'id = ' . $id;
            $userticket->update($data, $where); 

            $this->_redirect('/admin');
            return;
         } else {
            $this->view->userticket = $userticket->fetchRow('id='.$id);
         }
      }
   } else {
      // userticket id should be $params['id']
      $id = (int)$this->_request->getParam('id', 0);
      if ($id > 0) {
         $this->view->userticket = $userticket->fetchRow('id='.$id);
      }
   }
   // additional view fields required by form
   $this->view->action = 'edituser';
   $this->view->buttonText = 'Zmień'; 
   }
   
   public function editguestAction() 
   {
       $auth		= Zend_Auth::getInstance(); 
	
	if(!$auth->hasIdentity()){
	  $this->_redirect('/admin/loginform');
	}
        else {
            $request = $this->getRequest(); 
	$role		= $auth->getIdentity();
        if(!($role->role == "admin")){
            $this->_redirect('/admin/loginform');
        }
        }
       
      $this->view->assign('title','Edytuj Ticket');
      
      $guestticket = new Guestticket(); 

   if ($this->_request->isPost()) {
      Zend_Loader::loadClass('Zend_Filter_StripTags');
      $filter = new Zend_Filter_StripTags(); 

      $id = (int)$this->_request->getPost('id');
      $author = $filter->filter($this->_request->getPost('author'));
      $author = trim($author);
      $cathegory = trim($filter->filter($this->_request->getPost('cathegory'))); 
      $problem_describe = trim($filter->filter($this->_request->getPost('problem_describe'))); 
      $send_data = trim($filter->filter($this->_request->getPost('send_data'))); 
      $end_data = trim($filter->filter($this->_request->getPost('end_data'))); 
      $status = trim($filter->filter($this->_request->getPost('status'))); 
      $ip_number = trim($filter->filter($this->_request->getPost('ip_number'))); 
      $admin1 = trim($filter->filter($this->_request->getPost('admin1')));
      $admin2 = trim($filter->filter($this->_request->getPost('admin2')));
      $admin3 = trim($filter->filter($this->_request->getPost('admin3')));

      
       // jeżeli admin wybierze status zakończony to data musi się uzupełnić
      // na dzisiejsza, a jeżeli admin da dzisiejszą datę to status powninien
      // zmienić się na zakończony
      
      // aktualna data
      $obecna_data = date("Y-m-d");
      
      if($end_data == $obecna_data)
      {
          $status = 'zakończony';
      }
      else if($status == 'zakończony')
      {
          $end_data = $obecna_data;
      }
      
      
      
      if ($id !== false) {
         if ($author != '' && $cathegory != '' && $problem_describe != '' && $send_data != '' 
              && $end_data != '' && $status != '' && $ip_number != '' && $admin1 != '' 
                 && $admin2 != '' && $admin3 != '') {
            $data = array(
               'author' => $author,
               'cathegory' => $cathegory,
               'problem_describe' => $problem_describe,
               'send_data' => $send_data,
               'end_data' => $end_data,
               'status' => $status,
               'ip_number' => $ip_number,
               'admin1' => $admin1,
               'admin2' => $admin2,
               'admin3' => $admin3,
            );
            $where = 'id = ' . $id;
            $guestticket->update($data, $where); 

            $this->_redirect('/admin');
            return;
         } else {
            $this->view->guestticket = $guestticket->fetchRow('id='.$id);
         }
      }
   } else {
      // guestticket id should be $params[’id’]
      $id = (int)$this->_request->getParam('id', 0);
      if ($id > 0) {
         $this->view->guestticket = $guestticket->fetchRow('id='.$id);
      }
   }
   // additional view fields required by form
   $this->view->action = 'editguest';
   $this->view->buttonText = 'Zmień'; 
   }

   public function deleteuserAction() 
   {
       $auth		= Zend_Auth::getInstance(); 
	
	if(!$auth->hasIdentity()){
	  $this->_redirect('/admin/loginform');
	}
        else {
            $request = $this->getRequest(); 
	$role		= $auth->getIdentity();
        if(!($role->role == "admin")){
            $this->_redirect('/admin/loginform');
        }
        }
       
      $this->view->assign('title','Usuń Ticket');
      
      $userticket = new Userticket();

   if ($this->_request->isPost()) {
      Zend_Loader::loadClass('Zend_Filter_Alpha');
      $filter = new Zend_Filter_Alpha(); 

      $id = (int)$this->_request->getPost('id');
      $del = $filter->filter($this->_request->getPost('del'));
      if ($del == 'Tak' && $id > 0) {
         $where = 'id = ' . $id;
         $rows_affected = $userticket->delete($where);
      }
   } else {
      $id = (int)$this->_request->getParam('id');
      if ($id > 0) {
         // only render if we have an id and can find the userticket.
         $this->view->userticket = $userticket->fetchRow('id='.$id);
         if ($this->view->userticket->id > 0) {
            // render template automatically
            return;
         }
      }
   }
   // redirect back to the userticket list unless we have rendered the view
   $this->_redirect('/admin'); 
   }
   
      public function deleteguestAction() 
   {
          $auth		= Zend_Auth::getInstance(); 
	
	if(!$auth->hasIdentity()){
	  $this->_redirect('/admin/loginform');
	}
        else {
            $request = $this->getRequest(); 
	$role		= $auth->getIdentity();
        if(!($role->role == "admin")){
            $this->_redirect('/admin/loginform');
        }
        }
          
      $this->view->assign('title','Usuń Ticket');
      
      $guestticket = new Guestticket();

   if ($this->_request->isPost()) {
      Zend_Loader::loadClass('Zend_Filter_Alpha');
      $filter = new Zend_Filter_Alpha(); 

      $id = (int)$this->_request->getPost('id');
      $del = $filter->filter($this->_request->getPost('del'));
      if ($del == 'Tak' && $id > 0) {
         $where = 'id = ' . $id;
         $rows_affected = $guestticket->delete($where);
      }
   } else {
      $id = (int)$this->_request->getParam('id');
      if ($id > 0) {
         // only render if we have an id and can find the guestticket.
         $this->view->guestticket = $guestticket->fetchRow('id='.$id);
         if ($this->view->guestticket->id > 0) {
            // render template automatically
            return;
         }
      }
   }
   // redirect back to the guestticket list unless we have rendered the view
   $this->_redirect('/admin'); 
   }
    
  public function indexAction()
  {
      $auth		= Zend_Auth::getInstance(); 
	
	if(!$auth->hasIdentity()){
	  $this->_redirect('/admin/loginform');
	}
        else {
            $request = $this->getRequest(); 
	$role		= $auth->getIdentity();
        if(!($role->role == "admin")){
            $this->_redirect('/admin/loginform');
        }
        }
        
        $request = $this->getRequest(); 
	$user		= $auth->getIdentity();
	$username	= $user->username;
	$logoutUrl  = $request->getBaseURL().'/admin/logout';

	$this->view->assign('username', $username);
	$this->view->assign('urllogout',$logoutUrl);
      
   // $this->view->assign('name', 'Seik');
    $this->view->assign('title', 'Lista tiketów');
    
    $userticket = new Userticket();
    $this->view->usertickets = $userticket->fetchAll();
    
    $guestticket = new Guestticket();
    $this->view->guesttickets = $guestticket->fetchAll();
  }
}

?>
