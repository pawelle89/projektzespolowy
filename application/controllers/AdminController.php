<?php
require_once 'Zend/Controller/Action.php';
require_once 'Userticket.php';
require_once 'Guestticket.php';

class AdminController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->view->baseUrl = $this->_request->getBaseUrl();
        
        Zend_Loader::loadClass('Userticket');
        Zend_Loader::loadClass('Guestticket');
    }
    
   public function edituserAction() 
   {
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
      // userticket id should be $params[’id’]
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
    $this->view->assign('name', 'Seik');
    $this->view->assign('title', 'Lista tiketów');
    
    $userticket = new Userticket();
    $this->view->usertickets = $userticket->fetchAll();
    
    $guestticket = new Guestticket();
    $this->view->guesttickets = $guestticket->fetchAll();
  }
}

?>
