<?php
require_once 'Zend/Controller/Action.php';
require_once 'Userticket.php';
require_once 'Guestticket.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Mail/Storage/Pop3.php';
require_once 'Zend/Mail/Transport/Sendmail.php';
require_once 'Zend/Mail.php';


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
  
   
   //// odbieranie ticketów ze skrzynki pocztowej
   $mail = new Zend_Mail_Storage_Pop3(array('host'     => 'o2.pl',
                                         'user'     => 'awariauwm',
                                         'password' => 'awariauwm123',
                                         'ssl'      => 'SSL'));
   
   $obecna_data = date("Y-m-d");
   
   $i = 1;
   
    foreach ($mail as $message) {
     
         $data = array(
           'author' => 'Mail',
           'cathegory' => $message->subject,
           'problem_describe' => $message->getContent(),
           'subject_name' => '-',
           'send_data' => $obecna_data,
           'end_data' => '-',
           'status' => 'oczekujący',
           'ip_number' => '-',
           'admin1' => '-',
           'admin2' => '-',
           'admin3' => '-',
         );
         $guestticket = new Guestticket();
         $guestticket->insert($data);
         
         $mail->removeMessage($i);
         $i++; 
   
   // set up an "empty" userticket
   $this->view->guestticket = new stdClass();
   $this->view->guestticket->id = null;
   $this->view->guestticket->author = '';
   $this->view->guestticket->cathegory = ''; 
   $this->view->guestticket->problem_describe = ''; 
   $this->view->guestticket->subject_name = ''; 
   $this->view->guestticket->send_data = ''; 
   $this->view->guestticket->end_data = ''; 
   $this->view->guestticket->status = ''; 
   $this->view->guestticket->ip_number = ''; 
   $this->view->guestticket->admin1 = ''; 
   $this->view->guestticket->admin2 = ''; 
   $this->view->guestticket->admin3 = '';    
}
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
        
        $request = $this->getRequest(); 
	$user		= $auth->getIdentity();
	$username	= $user->username;
	$usermail	= $user->e_mail;

      $this->view->assign('title','Edytuj Ticket');

      $userticket = new Userticket(); 

   if ($this->_request->isPost()) {
      Zend_Loader::loadClass('Zend_Filter_StripTags');
      $filter = new Zend_Filter_StripTags(); 

      $id = (int)$this->_request->getPost('id');
      //$author = $filter->filter($this->_request->getPost('author'));
      //$author = trim($author);
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

      $tempstatus = $request->getParam('status');
      
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
         if ($cathegory != '' && $problem_describe != '' && $subject_name != '' 
                 && $send_data != '' && $end_data != '' && $status != '' && $ip_number != '' 
                 && $admin1 != ''  && $admin2 != '' && $admin3 != '' && $choose != '') {
            $data = array(
               //'author' => $author,
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
            $where = 'id = ' . $id;
            $userticket->update($data, $where); 
            
            
            
            if(($choose == 'Nie') && ($status == 'zakończony'))
            {
                // wysyłanie e maila
                    $mail = new Zend_Mail();
                    $mail->setBodyText('My Nice Test Text');
                    $mail->setBodyHtml('My Nice <b>Test</b> Text');
                    $mail->setFrom('somebody@example.com', 'Some Sender');
                    $mail->addTo('lukasz-segin@o2.pl', 'Some Recipient');
                    $mail->setSubject('TestSubject');
                    $mail->send();
              //  mail($usermail, 'Zmiana stasusu zgłoszenia', 'Witaj '.$username. '! Status twojego zgłoszenia
               //     został zmieniony na '.$status.'.');
                //break;
            }
            
            

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
      $subject_name = trim($filter->filter($this->_request->getPost('subject_name')));
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
         if ($author != '' && $cathegory != '' && $problem_describe != '' && $subject_name != '' 
                 && $send_data != '' && $end_data != '' && $status != '' && $ip_number != '' 
                 && $admin1 != '' && $admin2 != '' && $admin3 != '') {
            $data = array(
               'author' => $author,
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
      $auth = Zend_Auth::getInstance(); 
	
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
	$usermail	= $user->e_mail;
      //  $userrole       = $user->userrole; // nie chce działać
	$logoutUrl  = $request->getBaseURL().'/admin/logout';
        $searchUrl  = $request->getBaseURL().'/admin/search';

	$this->view->assign('username', $username);
	$this->view->assign('usermail', $usermail);
       // $this->view->assign('userrole', $userrole); // nie chce działać
	$this->view->assign('urllogout',$logoutUrl);
        $this->view->assign('urlsearch',$searchUrl); 
        
    
    $this->view->assign('title', 'Lista tiketów');
    
    $userticket = new Userticket();
    $this->view->usertickets = $userticket->fetchAll();
    
    $guestticket = new Guestticket();
    $this->view->guesttickets = $guestticket->fetchAll();
  }
  
  public function searchAction()
  {
      $auth = Zend_Auth::getInstance(); 
	
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
        $userrole       = $user->userrole; // nie chce działać
	$logoutUrl  = $request->getBaseURL().'/admin/logout';

	$this->view->assign('username', $username);
        $this->view->assign('userrole', $userrole); // nie chce działać
	$this->view->assign('urllogout',$logoutUrl);
        
        $request = $this->getRequest();  
    $this->view->assign('action', $request->getBaseURL()."/admin/search");
    $this->view->assign('search', 'Szukana fraza'); 
   
   if ($this->_request->isPost()) {
      Zend_Loader::loadClass('Zend_Filter_StripTags');
      $filter = new Zend_Filter_StripTags(); 


      $label_search = trim($filter->filter($this->_request->getPost('label_search')));
   
   
   $this->view->assign('label_search', $label_search);
   }
      
   // $this->view->assign('name', 'Seik');
    $this->view->assign('title', 'Wyszukaj');
    
    $userticket = new Userticket();
    $this->view->usertickets = $userticket->fetchAll();
    
    $guestticket = new Guestticket();
    $this->view->guesttickets = $guestticket->fetchAll();
  }
}
?>
