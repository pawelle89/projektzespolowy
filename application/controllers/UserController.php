<?php
require_once 'Zend/Controller/Action.php';

class UserController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->view->baseUrl = $this->_request->getBaseUrl();
        
        Zend_Loader::loadClass('Userticket');
        
        // DLA DANEGO UŻYTKOWNIKA TRZEBA BĘDZIE WYŚWIETLIĆ JEGO TICKETY
    }
    
   public function addAction() 
   {
      $this->view->assign('title','Dodaj Ticket');
      

   if ($this->_request->isPost()) {
      Zend_Loader::loadClass('Zend_Filter_StripTags');
      $filter = new Zend_Filter_StripTags();

      $author = $filter->filter($this->_request->getPost('author'));
      $author = trim($author);
      $cathegory = trim($filter->filter($this->_request->getPost('cathegory'))); 
      $problem_describe = trim($filter->filter($this->_request->getPost('problem_describe'))); 
      $send_data = trim($filter->filter($this->_request->getPost('send_data'))); 
      $end_data = trim($filter->filter($this->_request->getPost('end_data'))); 
      $status = trim($filter->filter($this->_request->getPost('status'))); 
      $ip_number = trim($filter->filter($this->_request->getPost('ip_number'))); 

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
         $userticket = new Userticket();
         $userticket->insert($data);
         $this->_redirect('./user');
         return;
      }
   }
   // set up an "empty" userticket
   $this->view->userticket = new stdClass();
   $this->view->userticket->id = null;
   $this->view->userticket->author = '';
   $this->view->userticket->cathegory = ''; 
   $this->view->userticket->problem_describe = ''; 
   $this->view->userticket->send_data = ''; 
   $this->view->userticket->end_data = ''; 
   $this->view->userticket->status = ''; 
   $this->view->userticket->ip_number = ''; 

   // additional view fields required by form
   $this->view->action = 'add';
   $this->view->buttonText = 'Dodaj'; 
   }
    
  public function indexAction()
  {
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
  
  public function nameAction()
  {
  
    $request = $this->getRequest();
    $this->view->assign('name', $request->getParam('username'));
    $this->view->assign('gender', $request->getParam('gender'));	  
		
    $this->view->assign('title', 'User Name');
  }  
}
?>
