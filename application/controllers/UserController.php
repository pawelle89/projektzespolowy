<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

class UserController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->view->baseUrl = $this->_request->getBaseUrl();
        
        Zend_Loader::loadClass('Userticket');
        
        // DLA DANEGO UŻYTKOWNIKA TRZEBA BĘDZIE WYŚWIETLIĆ JEGO TICKETY
        
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
  $this->_redirect('/user/loginform');
  }
  else if($paswd == ''){
      // $this->view->assign('nopaswd','Brak hasła.');
  $this->_redirect('/user/loginform');
  }
  
   $authAdapter->setIdentity($uname);
   $authAdapter->setCredential(md5($paswd));

   // Perform the authentication query, saving the result
   $result = $auth->authenticate($authAdapter);


   
   if($result->isValid()){
  $data = $authAdapter->getResultRowObject(null,'password');
  $auth->getStorage()->write($data);
  $this->_redirect('/user/index');
}else{
  $this->_redirect('/user/loginform');
}
}
    
   public function addAction() 
   {
       
       //////////////////////////////////////////////////////////////////////////////////
       //       TO TRZEBA BĘDZIE DODAĆ WSZĘDZIE ŻEBY SOBIE KTOŚ NIE WLAZŁ
       //////////////////////////////////////////////////////////////////////////////////
       //
       //       TUTAJ TRZEBA DODAĆ ROLE ADMINA I USERA
       //
	$auth		= Zend_Auth::getInstance(); 
	
	if(!$auth->hasIdentity()){
	  $this->_redirect('/user/loginform');
	}
        //////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
  
    $request = $this->getRequest(); 
	$user		= $auth->getIdentity();
	//$real_name	= $user->real_name;
	$username	= $user->username;
        $userid         = $user->id;
        $usermail       = $user->e_mail;
       
        $this->view->assign('username', $username);
        
        $e_mail = $usermail;
       
      $this->view->assign('title','Dodaj Ticket');
      

   if ($this->_request->isPost()) {
      Zend_Loader::loadClass('Zend_Filter_StripTags');
      $filter = new Zend_Filter_StripTags();

      $author = $filter->filter($this->_request->getPost('author'));
      $author = trim($author);
      $cathegory = trim($filter->filter($this->_request->getPost('cathegory'))); 
      $problem_describe = trim($filter->filter($this->_request->getPost('problem_describe'))); 
      $subject_name = trim($filter->filter($this->_request->getPost('subject_name')));
      $send_data = trim($filter->filter($this->_request->getPost('send_data'))); 
      $end_data = trim($filter->filter($this->_request->getPost('end_data'))); 
      $status = trim($filter->filter($this->_request->getPost('status'))); 
      $ip_number = trim($filter->filter($this->_request->getPost('ip_number')));  
      $admin1 = trim($filter->filter($this->_request->getPost('admin1')));
      $admin2 = trim($filter->filter($this->_request->getPost('admin2')));
      $admin3 = trim($filter->filter($this->_request->getPost('admin3')));
      $choose = trim($filter->filter($this->_request->getPost('choose')));

      if ($userid != '' && $author != '' && $e_mail != '' && $cathegory != '' && $problem_describe != '' && $subject_name != ''
              && $send_data != '' && $end_data != '' && $status != '' && $ip_number != '' 
              && $admin1 != '' && $admin2 != '' && $admin3 != '' && $choose != '') {
         $data = array(
           'userid' => $userid,
           'author' => $author,
           'e_mail' => $e_mail,
           'cathegory' => $cathegory,
           'problem_describe' => $problem_describe,
           'subject_name' => $subject_name,
           'send_data' => $send_data,
           'end_data' => $end_data,
           'status' => $status,
           'ip_number' => $ip_number,
           'admin1' => $admin1,
           'admin2' => $admin2,
           'admin3' => $admin3,
           'choose' => $choose,
         );
         $userticket = new Userticket();
         $userticket->insert($data);
         $this->_redirect('./user');
         return;
      }
   }
   // set up an "empty" userticket
   $this->view->userticket = new stdClass();
   $this->view->userticket->id = null;
   $this->view->userticket->userid = '';
   $this->view->userticket->author = '';
   $this->view->userticket->e_mail = ''; 
   $this->view->userticket->cathegory = ''; 
   $this->view->userticket->problem_describe = ''; 
   $this->view->userticket->subject_name = ''; 
   $this->view->userticket->send_data = ''; 
   $this->view->userticket->end_data = ''; 
   $this->view->userticket->status = ''; 
   $this->view->userticket->ip_number = ''; 
   $this->view->userticket->admin1 = ''; 
   $this->view->userticket->admin2 = ''; 
   $this->view->userticket->admin3 = '';  
   $this->view->userticket->choose = ''; 

   // additional view fields required by form
   $this->view->action = 'add';
   $this->view->buttonText = 'Dodaj'; 
   }
    
  public function indexAction()
  {
      
          $auth		= Zend_Auth::getInstance(); 
	
	if(!$auth->hasIdentity()){
	  $this->_redirect('/user/loginform');
	}
  
    $request = $this->getRequest(); 
	$user		= $auth->getIdentity();
	$username	= $user->username;
        $userid         = $user->id;
        //$userrole       = $user->userrole; // nie chce działać
	$logoutUrl  = $request->getBaseURL().'/user/logout';

	$this->view->assign('username', $username);
        
	$this->view->assign('userid', $userid);
        
       // $this->view->assign('userrole', $userrole); // nie chce działać
	$this->view->assign('urllogout',$logoutUrl);
      
      
    $this->view->assign('name', 'Wiwit');
    $this->view->assign('title', 'Moje Tickety');
    
    
    // TO WYŚWIETLA WSZYSTKIE TICKETY, A POWINNO TYLKO TICKETY DANEGO UŻYTKOWNIKA
    $userticket = new Userticket();
    $this->view->usertickets = $userticket->fetchAll();
    
  }
  
   public function loginformAction()
 {
   $request = $this->getRequest();  
$this->view->assign('action', $request->getBaseURL()."/user/auth");  
   $this->view->assign('title', 'Logowanie');
   $this->view->assign('username', 'Nazwa użytkownika');	
   $this->view->assign('password', 'Hasło');	
    
 }
}
?>
