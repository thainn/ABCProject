<?php
/**
 * Leverages Vietnam Co., Ltd
 *
 * @author             Nguyen Ngoc Thai
 * @date created       10/19/2012
 * @modified by        Minh Hai Truong
 * @date modified      10/26/2012
 *
 * Manage account information
 */

class IndexController extends AppController{
    var $uses=array('Contact','Recruit','News','Users','Candidate');
    function index()
    {
      App::uses('AppHelper', 'View/Helper');
      $data = $this->News->query('select id,title,publishdate,url,content from news where status=0 and publishdate<=now() order by publishdate desc limit '.Configure::read('LIMIT_NEWS_HOME'));
      $data2 = $this->News->query('select id,title,startdate from recruits as recruit where status=0 and startdate<=now() order by startdate desc limit '.Configure::read('LIMIT_NEWS_RECRUIT'));
      $data3 = array();
      foreach($data as $tdata)
      {
          $data3[] = array(
              'type'         =>'news',
              'id'           => $tdata['news']['id'],
              'title'        => AppHelper::cutString($tdata['news']['title'],  Configure::read('LIMIT_CHART_TITLE_NEWS'),'...'),
              'url'          => $tdata['news']['url'],
              'content'      => $tdata['news']['content'],
              'startdate'    => $tdata['news']['publishdate'],
          );
      }
      foreach($data2 as $tdata)
      {
          $data3[] = array(
              'type'         =>'recruit',
              'id'           => $tdata['recruit']['id'],
              'title'        =>  AppHelper::cutString($tdata['recruit']['title'],  Configure::read('LIMIT_CHART_TITLE_RECRUIT'),'...'),
              'url'          => '',
              'content'      => '',
              'startdate'    => $tdata['recruit']['startdate'],
          );
      }
      $n = count($data3);
      $temp='';
      for($i=0;$i<$n;++$i)
      {
           for($j=$i+1;$j<$n;++$j)
          {
               if(strtotime($data3[$i]['startdate'])< strtotime($data3[$j]['startdate']))
               {
                    $temp = $data3[$j]['startdate'];
                    $data3[$j]['startdate'] = $data3[$i]['startdate'];
                    $data3[$i]['startdate']= $temp;
               }
          }
      }
       $this->set('news',$data3);
       $this->set('page_title','Leverages VietNam');
    }

	public  function  admin_index()
	{
             App::uses('AppHelper', 'View/Helper');
		$data = $this->Contact->find('all', 
                        array( 'limit' => Configure::read('LIMIT_ADMINCONTACT'),
                                'order'=>'send_date desc',
                            'conditions' => array('flag' => '0')));
		$this->set('contact',$data);
                
		$data = $this->News->find('all', array( 'limit' => Configure::read('LIMIT_ADMINNEWS'),
                                    'order'=>'publishdate desc',
                            'conditions' => array('status' => '0')));
		$this->set('news',$data);
                
                
		$data = $this->Recruit->find('all',  array( 'limit' => Configure::read('LIMIT_ADMINRECRUIT'),
                            'order'=>'startdate desc',
                            'conditions' => array('status' => '0')));
                
		$this->set('recruit',$data);
	}

	public  function  admin_login()
	{
		$this->Session->write('login','3');
                 $loginError = 0;
		try {
			if ($this->request->is('post')) {
				$username=$this->request->data['username'];
				$password=$this->request->data['password'];
                               $username= addslashes($username);
                                 $username= htmlspecialchars($username);
				$password=  md5($password);
				$sql = 'select * from users where username="'.$username.'"';
				$sql.= 'and password="'.$password.'"';
				$data= $this->Users->query($sql);
                                
                                if (strlen($this->request->data['username'])<6) {
                                    $this->set('enterUsername', ' UserName ít nhất 6 kí tự');
                                    $loginError=1;
                                }
                                if (empty($this->request->data['username'])) {
                                    $this->set('enterUsername', 'Username không được trống');
                                    $loginError=1;
                                }
                                 if (strlen($this->request->data['password'])<6) {
                                    $this->set('enterPassword', ' Password ít nhất 6 kí tự');
                                    $loginError=1;
                                }
                                
                                
                                if (empty($this->request->data['password'])) {
                                    $this->set('enterPassword', 'Password không được trống');
                                    $loginError=1;
                                   
                                }
                                
                                if($loginError==1)
                                {
                                    $this->layout = false;
                                    return;
                                   
                                }
                                
				if($data!=null)
				{
					$this->Session->write('login','1');
					$this->Session->write('Username', $username);
					$this->redirect(array('controller' => 'index', 'action' => 'index'));
				}
				if($data==null){
					$this->Session->write('login','0');
				}
			}

		} catch (Exception $exc) {
		}
		$this->layout = false;
	}
        
	function admin_logout()
	{
		$this->Session->delete('Username');
		$this->redirect(array('controller' => 'index', 'action' => 'login'));
	}

	function admin_changepassword()
	{
		if($this->Session->read('Username')==null)
		{
			$this->redirect(array('controller' => 'index', 'action' => 'login'));
		}
			$changPassword=0;
		$this->Session->write('changpassword','3');
		if ($this->request->is('post')) {
			$username=  $this->Session->read('Username');
			// $username='thainn';
			$password=$this->request->data['passwordOld'];
                        
                        
                         if (strlen($this->request->data['passwordOld'])<6) {
                                    $this->set('enterPaswordOld','Password hiện tại ít nhất 6 kí tự');
                                    $changPassword=1;
                                }
                                if (empty($this->request->data['passwordOld'])) {
                                    $this->set('enterPaswordOld', 'Password hiện tại không được trống');
                                    
                                    $changPassword=1;
                                }
                                 if (strlen($this->request->data['passwordNew'])<6) {
                                     
                                    $this->set('enterPaswordNew', ' Password mới ít nhất 6 kí tự');
                                    $changPassword=1;
                                }
                                
                                
                                if (empty($this->request->data['passwordNew'])) {
                                    $this->set('enterPaswordNew', 'Password mới không được trống');
                                    $changPassword=1;
                                }
                                
                                   if (strlen($this->request->data['ConfirmpasswordNew'])<6) {
                                    $this->set('ConfirmenterPaswordNew', 'Xác nhận password  ít nhất 6 kí tự');
                                    $changPassword=1;
                                }
                                
                                 if ($this->request->data['ConfirmpasswordNew'] !=$this->request->data['passwordNew'] ) {
                                    $this->set('ConfirmenterPaswordNew', 'Xác nhận password không trùng với password mới');
                                    $changPassword=1;
                                }
                                
                                
                                if (empty($this->request->data['ConfirmpasswordNew'])) {
                                    $this->set('ConfirmenterPaswordNew', 'Xác nhận password không được trống');
                                    $changPassword=1;
                                }
                                
                                
                                if($changPassword==1)
                                {
                                   // var_dump('sdsd');
                                   // $this->Session->write('changpassword','0');
                                    return;
                                   
                                }
                        
                        
                        
                        
			$password=  md5($password);
			$sql = 'select * from users where username="'.$username.'"';
			$sql.= 'and password="'.$password.'"';
			$data= $this->Users->query($sql);
			foreach ($data as $data)
			{
				$id=$data['users']['id'];
			}
			if($data!=null)
			{
				$passwordNew=$this->request->data['passwordNew'];
				$passwordNew=md5($passwordNew);
				$sql='UPDATE users SET password ="'. $passwordNew.'"'.' WHERE id ='.$id;
				$this->Users->query($sql);
				$this->Session->write('Username', $username);
				$this->Session->write('changpassword','1');
			}

			else
			{
				$this->Session->write('changpassword','0');
			}

		}
	}
}