<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';


class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->view->baseUrl = $this->_request->getBaseUrl();
        
        Zend_Loader::loadClass('Guestticket');
    }
    
  public function indexAction()
  {
      
      
    $this->view->assign('title', 'Home');
	$this->view->assign('wellcome','Witaj na naszej stronie! Użytkownik niezalogowany może TUTAJ wysłać ticket. Aby wysłać ticket ze swojego konta musisz się zalogować.');
	$this->view->assign('webmaster','Seik');
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
         $guestticket = new Guestticket();
         $guestticket->insert($data);
         $this->_redirect('./');
         return;
      }
   }
   // set up an "empty" guestticket
   $this->view->guestticket = new stdClass();
   $this->view->guestticket->id = null;
   $this->view->guestticket->author = '';
   $this->view->guestticket->cathegory = ''; 
   $this->view->guestticket->problem_describe = ''; 
   $this->view->guestticket->send_data = ''; 
   $this->view->guestticket->end_data = ''; 
   $this->view->guestticket->status = ''; 
   $this->view->guestticket->ip_number = ''; 

   // additional view fields required by form
   $this->view->action = 'add';
   $this->view->buttonText = 'Dodaj'; 
   }
    
}
?>
