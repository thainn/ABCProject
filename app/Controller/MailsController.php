


<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MailsController
 *
 * @author IAHT
 */
class MailsController extends AppController{
    //put your code here
    var $uses=array('Contact','Candidates','news','recruit');
     var $helpers = array('Paginator','Html');
      public $components = array('RequestHandler', 'Email');
	var $paginate = array();
                
    function admin_index()
    {
         App::uses('AppHelper', 'View/Helper');
           $this->paginate = array(
            'limit' => 10,
            'conditions'=>array('flag'=>'0'),
        );
        $data = $this->paginate("Contact");
        $this->set("contact", $data);
    }
    function admin_getlength()
    {
        Configure::write('debug', 0);
         $contact = $this->Contact->find('all',array('conditions'=>array('flag'=>'0')));
        $this->layout = 'ajax';
        $this->autoLayout = false;
        $this->autoRender = false;
        $this->header('Content-Type: application/json');
        $result = array();
        $result['data'] = $contact;
        
        
        echo json_encode($result);
    }
    
    
     function admin_getlengthMail()
    {
        Configure::write('debug', 0);
         $contact = $this->Contact->find('all',array('conditions'=>array('flag'=>'0','checked'=>'0')));
        $this->layout = 'ajax';
        $this->autoLayout = false;
        $this->autoRender = false;
        $this->header('Content-Type: application/json');
        $result = array();
        $result['data'] = $contact;
        echo json_encode($result);
    }
    
    
    function admin_getlengthGarbage()
    {
        Configure::write('debug', 0);
         $contact = $this->Contact->find('all',array('conditions'=>array('flag'=>'1')));
        $this->layout = 'ajax';
        $this->autoLayout = false;
        $this->autoRender = false;
        $this->header('Content-Type: application/json');
        $result = array();
        $result['data'] = $contact;
     
        echo json_encode($result);
    }
    
    
      function admin_updateContact($id = null) {
        if ($id != null) {
            $sql = 'UPDATE contacts SET flag = 1 WHERE id =' . $id;
            //$this->Contact->query($sql);
            $this->Contact->query($sql);
            $this->Session->write('contact', '1');
            Configure::write('debug', 0);
            $this->layout = 'ajax';
            $this->autoLayout = false;
            $this->autoRender = false;
            $this->header('Content-Type: application/json');
            $data = array();
            $result = array();
            echo json_encode($result);
        }
       
    }
    function  admin_sendmail($mail,$subject,$content)
    {
          Configure::write('debug', 0);
        $this->Email->smtpOptions = array(
            'port' => 465,
            'timeout' => 100,
            'host' => 'ssl://smtp.gmail.com',
            'username' => 'tanngocson854@gmail.com',
            'password' => 'ngocthai',
        );
        $this->Email->delivery = 'smtp';
        if($path && $file)
        {
        $this->Email->attachments = array(
            $file => $path);
        }
        $this->Email->send = 'debug';
        $this->Email->to = $mail;
        $this->Email->subject = $subject;
        $this->Email->from = 'Công Ty Leverages <noreply@example.com>';
        if ($this->Email->send($content)) {
            $this->layout = 'ajax';
            $this->autoLayout = false;
            $this->autoRender = false;
            $this->header('Content-Type: application/json');
            $data = array();
            $result = array();
            echo json_encode($result);
            
          //  $this->Session->setFlash('Simple email sent');
        } else {
           // $this->Session->setFlash('Simple email not sent');
           // $this->set('smtp_errors', $this->Email->smtpError);
        }
    }


    function admin_deleteContact($id = null) {
        if ($id != null) {
           if ($this->Contact->delete($id)) {
            Configure::write('debug', 0);
            $this->layout = 'ajax';
            $this->autoLayout = false;
            $this->autoRender = false;
            $this->header('Content-Type: application/json');
            $data = array();
            $result = array();
            echo json_encode($result);
            }
        }
    }
    
    
    
        function admin_viewDetail($id)
    {
        Configure::write('debug', 0);
        
        if ($id != null) {
            $data = $this->Contact->findById($id);
            if ($data == null || $data['Contact']['flag'] == '1') {
                $this->set('error', 'true');
            }
            //$this->set('contact', $data);
            $contact = $data;
            $this->layout = 'ajax';
            $this->autoLayout = false;
            $this->autoRender = false;
            $this->header('Content-Type: application/json');
            $result = array();
            $result['data'] = $contact;
            echo json_encode($result);
        }
        
         
    }
    
    
      function admin_updatechecked($id)
    {
       if ($id != null) {
            $sql = 'UPDATE contacts SET checked = 1 WHERE id =' . $id;
            $this->Contact->query($sql);
            $this->Session->write('contact', '1');
            Configure::write('debug', 0);
            $this->layout = 'ajax';
            $this->autoLayout = false;
            $this->autoRender = false;
            $this->header('Content-Type: application/json');
            $data = array();
            $result = array();
            echo json_encode($result);
        }
        
         
    }
    
    
}

?>
