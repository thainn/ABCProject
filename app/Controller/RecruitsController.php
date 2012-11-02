<?php
/**
 * Leverages Vietnam Co., Ltd
 *
 * @author             Truong Minh Hai
 * @date created       10/19/2012
 * @date modified      10/26/2012
 *
 * Manage recruitment information
 */

class  RecruitsController extends AppController {
	var $uses=array('Recruit','Candidate');
	var $helpers = array('Time');
	
	public $components = array('RequestHandler', 'Email');

	/*
	 * Frontend only.
	 * Display frontend recruit page
	 *
	*/
	function  index() {
		$this->set('page_title', 'Tuyển dụng');
		$time = date('Y-m-d H:i:s', time());

		$this->paginate = array(
				'conditions' => array('status' => '0','startdate <= '=>$time),
				'limit'         => Configure::read('LIMIT_RECRUIT'),
				'order' => array('startdate' => 'desc')
		);
		$data = $this->paginate("Recruit");
		
		if(isset($this->params['paging']['Recruit']['options']['page'])) {
			if($this->params['paging']['Recruit']['options']['page'] > $this->params['paging']['Recruit']['pageCount']) {
				return $this->redirect(array('action' => 'index',
					  'controller'=>'recruits',
					  'page'=>$this->params['paging']['Recruit']['pageCount']));
			}
		}
		$this->set('items', $data);
	}
	
	
	/*
	 * Frontend only.
	 * Display "Software Engineer" static page.
	 *
	*/
	function se() {
		$this->set('page_title','Yêu cầu tuyển dụng Software engineer');
	}
	
	
	/*
	 * Frontend only.
	* Display "BSE" static page.
	*
	*/
	function bse() {
		$this->set('page_title','Yêu cầu tuyển dụng Software BSE');
	}

	/*
	 * Frontend only.
	 * Display a recruit detail
	 * $id: the recruit's id.
	*/
	function view($id=''){
		$id = intval($id);
		$data = $this->Recruit->query("select id, title, content from recruits where id={$id} and status = 0 and startdate <= now()");
		if($data){
			$item = $data[0]['recruits'];
			$this->set('page_title',$item['title']);
			$this->set('data',$item);
		}else {
			$this->redirect('/error404/');
		}
	}
	
	function applyjob($id = null) {
        $error = 0;
        if ($id == null) {
        	$this->set('page_title', 'Tuyển dụng');
        	$time = date('Y-m-d H:i:s', time());
        	
        	$this->paginate = array(
        			'conditions' => array('status' => '0','startdate <= '=>$time),
        			'limit'         => Configure::read('LIMIT_RECRUIT'),
        			'order' => array('startdate' => 'desc')
        	);
        	$data = $this->paginate("Recruit");
        	
        	if(isset($this->params['paging']['Recruit']['options']['page'])) {
        		if($this->params['paging']['Recruit']['options']['page'] > $this->params['paging']['Recruit']['pageCount']) {
        			return $this->redirect(array('action' => 'index',
        					'controller'=>'recruits',
        					'page'=>$this->params['paging']['Recruit']['pageCount']));
        		}
        	}
        	$this->set('items', $data);
            $this->redirect(array('controller' => 'recruits', 'action' => 'index'));
        }

        $data = $this->Recruit->findById($id);
        $this->set('recruit', $data);
        $id = intval($id);
        $data = $this->Recruit->query("select title from recruits where id={$id} and status=0 and startdate <= now()");

        if ($data) {
            $item = $data[0]['recruits'];
            $this->set('page_title', 'Apply Job - ' . $item['title']);
            $this->set('data', $item);
            if ($this->request->is('post')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);

                $this->Candidate->create();
                $this->request->data['job_id'] = $id;
                $this->request->data['senddate'] = date('Y-m-d H:i', time());
                $current_date = date('Y-m-d  H:i:s');
                $this->request->data['flag'] = 0;
                $this->request->data['checked'] = 0;
                $filename = $_FILES['file']['name']; // tên file upload -> save to database
                $buffName = $filename;
                $filename = time() . '-' . $filename;
                $this->request->data['url'] = $filename ;

                if ($this->request->data['sexname'] = 'nam') {
                    $this->request->data['sex'] = 1;
                } else
                    $this->request->data['sex'] = 0;
                
                $mime = finfo_file($finfo, $_FILES['file']['tmp_name']);
                finfo_close($finfo);
                $array_header = Configure::read('FILE_TYPE_UPLOAD');
                
                if (!in_array($mime, $array_header)) {
                    $error = 1;
                    $this->set('errorFile', 'Chỉ chấp nhận file .doc, .xls, .pdf');
                }

                if (empty($this->request->data['fullname'])) {
                    $this->set('enterFullname', 'Vui lòng nhập họ tên');
                    $error = 1;
                }
               
                $birthday = $this->request->data['birthday'];
                $day      = substr($birthday, 0, 2); 
                $month	  = substr($birthday, 3, -5); 
                $year	  = substr($birthday, 6, 10); 
                 
                $this->request->data['birthday'] = $year.'/'.$month.'/'.$day;
            
                $formatEmail='/^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/';
                
				if ($this->request->data['email'] ==$formatEmail) {
					$this->set('enterEmail', ' Email không đúng định dạng');
                    $error = 1;
                }
                
                if (empty($this->request->data['email'])) {
                    $this->set('enterEmail', 'Vui lòng nhập email');
                    $error = 1;
                }
               
                $formaNumber='/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/';
                if ($this->request->data['phone'] == $formaNumber) {
                    $this->set('enterPhone', ' Phone chứa toàn kí tự số');
                    $error = 1;
                }
                
                if (empty($this->request->data['phone'])) {
                    $this->set('enterPhone', 'Vui lòng nhập phone');
                    $error = 1;
                }
                
                if ($this->request->data['birthday'] == $formaNumber) {
                    $this->set('enterSendDate', ' Năm sinh chứa toàn kí tự số');
                    $error = 1;
                }
                
                if (empty($this->request->data['birthday'])) {
                    $this->set('enterSendDate', 'Vui lòng nhập Năm sinh');
                    $error = 1;
                }
                
                if (empty($this->request->data['introduce'])) {
                    $this->set('enterIntroduce', 'Vui lòng nhập Giới thiệu bản thân');
                    $error = 1;
                }
             
                if ($error==1) {
                	$this->set('success', 'middle');
                	return;
                }
                
                if ($error == 0) {
                	if ($this->Candidate->save($this->request->data)) {
                		move_uploaded_file($_FILES['file']['tmp_name'], Configure::read('PATH_UPLOAD_CV') . $filename);
                        $this->Email->smtpOptions = array(
                            'port' => 465,
                            'timeout' => 100,
                            'host' => 'ssl://smtp.gmail.com',
                            'username' => 'tanngocson854@gmail.com',
                            'password' => 'ngocthai',
                        );

                        $this->Email->delivery 		= 'smtp';
                        $this->Email->attachments 	= array($buffName => 'files/' . $filename);
                        $this->Email->send 			= 'debug';
                        $this->Email->to 			= 'thainn@leverages.jp';
                        $this->Email->subject 		= 'Leverages.com.vn Send CV';
                        $this->Email->from 			= 'Send CV <noreply@example.com>';
                        
                        if ($this->Email->send('Công Ty Leverages Cảm ơn bạn đã quan tâm đến công ty')) {
                        	$this->Session->setFlash('Thông tin của bạn đã được gửi đi thành công.');
                            $this->set('success', 'true');
                            $this->redirect(array('controller'=>'recruits','action'=>'applyjob',$id));
                        } else {
                        	$this->Session->setFlash('Thông tin của bạn gửi đi bị lỗi.');
                            $this->set('success', 'false');
                        }
                    }
                }
            }
        } else {
            $this->set('page_title', 'Tuyển dụng');
        	$time = date('Y-m-d H:i:s', time());
        	
        	$this->paginate = array(
        			'conditions' => array('status' => '0','startdate <= '=>$time),
        			'limit'         => Configure::read('LIMIT_RECRUIT'),
        			'order' => array('startdate' => 'desc')
        	);
        	$data = $this->paginate("Recruit");
        	
        	if(isset($this->params['paging']['Recruit']['options']['page'])) {
        		if($this->params['paging']['Recruit']['options']['page'] > $this->params['paging']['Recruit']['pageCount']) {
        			return $this->redirect(array('action' => 'index',
        					'controller'=>'recruits',
        					'page'=>$this->params['paging']['Recruit']['pageCount']));
        		}
        	}
        	$this->set('items', $data);
            $this->redirect(array('controller' => 'recruits', 'action' => 'index'));
        }
    }
	/*
	 * listing all of the recruitment item
	*
	*
	*/

	public function admin_index() {
		App::uses('AppHelper', 'View/Helper');
		$this->paginate = array('conditions' => array('`status` =' => '0'),
				'limit' =>Configure::read('PAGINATION_LIMIT'),
				'order' => array('id' => 'DESC')
		);
		$data = $this->paginate("Recruit");
		
		if(isset($this->params['paging']['Recruit']['options']['page']))
		if($this->params['paging']['Recruit']['options']['page'] > $this->params['paging']['Recruit']['pageCount']){
		   return $this->redirect(array('action' => 'index',
				  'controller'=>'recruits',
				  'page'=>$this->params['paging']['Recruit']['pageCount']));
		  }
		$this->set("items", $data);
		if($this->request->is('ajax'))
			$this->render('admin_index' , 'ajax');
	}

	/*
	 * display the add form, allow user to fill and add a new recruitment item
	*
	*
	*/
	public function admin_add(){
		if ($this->request->is('post')) {

			$this->_formatInputData();
			$validdata = $this->_validInput(); // valid input data

			if(!$validdata){
				$item['Recruit'] = $this->request->data;
				$this->_formatOutputData(&$item['Recruit']);
				$this->set('item',  $item);
				$this->autoRender = false;
				$this->render('admin_edit');
				return;
			}


			$this->Recruit->create();
			// save data to database. if success, redirect to index, else, display an error.
			if ($this->Recruit->save($this->request->data)) {
				$this->Session->setFlash(Configure::read('UPDATE_SUCCESS'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(Configure::read('ERR_SAVING_DATA'));
				$item['Recruit'] = $this->request->data;
				 
				$this->_formatOutputData(&$item['Recruit']);
				 
				$this->set('item',  $item);
				 
				$this->autoRender = false;
				$this->render('admin_edit');
			}
			return;
		}
	}

	/*
	 * display the edit form, allow user to fill and edit a recruitment item
	* $id: id of the edited item.
	*
	*/

	public function admin_edit($id){
		$this->Recruit->id = $id;
		 
		if ($this->request->is('get')) {
			$data = $this->Recruit->read();
			 if (!$data){
				$this->redirect(array( 'controller' => 'recruits',
					 'action' => 'index'));
				
				return;
			   }
			$this->_formatOutputData(&$data['Recruit']);
			$this->set('item',  $data);
		}else {
			$this->_formatInputData(false);

			$validdata = $this->_validInput(); // valid input data
			if(!$validdata){
				$item['Recruit'] = $this->request->data;
				$this->_formatOutputData(&$item['Recruit']);
				$this->set('item',  $item);

				return ;
			}

			// save data to database. if success, redirect to index, else, display an error.
			if ($this->Recruit->save($this->request->data)) {
				$this->Session->setFlash(Configure::read('UPDATE_SUCCESS'));
				$this->redirect(array('action' => 'index'));
			} else {
				$item['Recruit'] = $this->request->data;
				$this->_formatOutputData(&$item['Recruit']);
				 
				$this->set('item',  $item);
				 
				$this->Session->setFlash(Configure::read('ERR_SAVING_DATA'));
			}
		}
	}

	/*
	 * view a recruitment's detail information
	* $id: id of the listing item.
	*
	*/

	public function admin_view($id = null) {
	  if ($id != null) {

	   $data = $this->Recruit->findById($id);
	   if ($data) {
		$this->set('item', $data);
		return;
	   }
	  }
	  
	  $this->redirect(array(  'controller'=> 'recruits',
			'action' => 'index'));
	 }

	/*
	 * set recruitment's status to disable (logical delete).
	* $id: id of the deleted item.
	*
	*/

	public function admin_delete($id=null) {
		// if there is ajax, tranfer to _ajax_delete
		if($this->request->is('ajax')){
			$this->_ajax_delete();
			return;
		}

		if ($id) {
			$query = 'UPDATE recruits SET `status` = 1 WHERE id = '.$id;
			 
			if(!$this->Recruit->query($query)){
				$this->Session->setFlash(Configure::read('ERR_DELETING_DATA'));
			}
		}
		 
		$paging = explode(':',$this->request->params['pass'][1]); // get current page index
		$this->Session->setFlash(Configure::read('DELETE_SUCCESS'));
		$this->redirect(array('controller'=>'recruits', 'action'=>'index', 'page'=>$paging[1]));
	}

	/*
	 * set multiple recruitment's status to disable (logical delete) via ajax.
	*
	*
	*/

	public function _ajax_delete() {
		if($this->request->data){
			$delete = implode(',', $this->request->data['item']);
		}

		if ($delete) {
			$query = 'UPDATE recruits SET `status` = 1 WHERE id IN ('.$delete.')';
			 
			if(!$this->Recruit->query($query)){
				$this->Session->setFlash(Configure::read('ERR_DELETING_DATA'));
			}
		}
		$this->autoLayout = false;
		$this->autoRender = false;
		//render to index page after deleting items.
		$this->Session->setFlash(Configure::read('DELETE_SUCCESS'));
		echo $this->requestAction('admin/recruits/index/page:'.$this->request->data['currentpage']);
	}


	/*
	 * Format database data to form data
	* $data: database data
	* return $data array
	*/

	private function _formatOutputData(&$data){
		if($data['startdate']){
			$temp = explode(' ', $data['startdate']);
			$date = explode('-', $temp[0]);
			$data['startdate'] = $date[2].'/'.$date[1].'/'.$date[0];
		}
		 
		if($data['enddate']){
			$temp = explode(' ', $data['enddate']);
			$date = explode('-', $temp[0]);
			$data['enddate'] = $date[2].'/'.$date[1].'/'.$date[0];
		}

		if($data['created']){
			$temp = explode(' ', $data['created']);
			$date = explode('-', $temp[0]);
			$data['created'] = $date[2].'/'.$date[1].'/'.$date[0];
		}
	}


	/*
	 * Format form data to database data
	*
	*
	*/

	private function _formatInputData($flagadd = true){
		//parse to db datetime - begin
		if($this->request->data['startdate']){
			$temp = explode(' ', $this->request->data['startdate']);
			$date = explode('/', $temp[0]);
			$this->request->data['startdate'] = $date[2].'-'.$date[1].'-'.$date[0];
		}
		//parse to db datetime - end

		//parse to db datetime - begin
		if($this->request->data['startdate']){
			$temp = explode(' ', $this->request->data['enddate']);
			$date = explode('/', $temp[0]);
			$this->request->data['enddate'] = $date[2].'-'.$date[1].'-'.$date[0];
		}
		//parse to db datetime - end

		// generate create time - beign
		if($flagadd)
			$this->request->data['created'] = date('Y-m-d H:m', time());
		// generate create time - end
		
		$this->request->data['title'] = strip_tags($this->request->data['title']); 
	}

	/*
	 * Valid input data
	* @return: true if data is ok.
	*
	*/
	private function _validInput(){
		$result = true; $flash = '';
		if(!$this->request->data['title']){ // validate title is empty
			$result = false;
			$flash = '- '.Configure::read('ERR_TITLE_BLANK').'<br />';
		}
		
		if(!$this->request->data['startdate']){ // validate title is empty
			$result = false;
			$flash .= '- '.Configure::read('ERR_STARTDATE_BLANK').'<br />';
		}

		$begin = str_replace('-', '', $this->request->data['startdate']);
		$end = str_replace('-', '', $this->request->data['enddate']);

		if($end & $begin > $end){// validate enddate is greater than startdate
			$result = false;
			$flash .= '- '.Configure::read('ERR_UNVALID_DATE').'<br />';
		}
		if(!$result) $this->Session->setFlash($flash);
		return $result;
	}
	 
}
