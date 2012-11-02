<?php
/**
 * Leverages Vietnam Co., Ltd
 *
 * @author             Nguyen Ngoc Thai
 * @date created       10/19/2012
 * @modified by        Minh Hai Truong
 * @date modified      10/26/2012
 *
 * Manage news' information
 */
class NewsController extends AppController {

    var $uses = array('News', 'news');
    var $helpers = array('Time');
    
    function  index()
    {
        $this->set('page_title','Danh sách tin tức');
        $time=date('Y-m-d H:i:s',time());
        $this->paginate = array(
            'conditions' => array('status' => '0','publishdate <= '=>$time),
            'limit'         => Configure::read('LIMIT_NEWS'),
            'order' => array('publishdate' => 'desc')                                 
        );
        $data = $this->paginate("news",array());
        
        if(isset($this->params['paging']['news']['options']['page']))
			if($this->params['paging']['news']['options']['page'] > $this->params['paging']['news']['pageCount']){
		         return $this->redirect(array('action' => 'index',
		                'controller'=>'news',
		                'page'=>$this->params['paging']['news']['pageCount']));
	        }
        $this->set('data',$data);
    }
    
    function  view($id)
    {
         
          $id = intval($id);
         $data = $this->news->query("select title,content from news where id={$id} and status=0 and publishdate <= now()");
         if($data){
              $this->set('page_title','Chi tiết tin tức');
              $this->set('data',$data[0]['news']);
         }else
         {
              $this->redirect('/error404/');
         }
    }
    
    
    /*
    * listing all of the news item
    * 
    *
    */
    
    public function admin_index() {
   		App::uses('AppHelper', 'View/Helper');
        $this->paginate = array('conditions' => array('`status` ' => '0'),
					            'limit' => Configure::read('PAGINATION_LIMIT'),
					        	'order' => array('id' => 'DESC')
        					);
        $data = $this->paginate("News");
      
        if(isset($this->params['paging']['News']['options']['page']))
	        if($this->params['paging']['News']['options']['page'] > $this->params['paging']['News']['pageCount']){
	        	return $this->redirect(array('action' => 'index',
	        			'controller'=>'news',
	        			'page'=>$this->params['paging']['Recruit']['pageCount']));
	        }
        $this->set("items", $data);
        
        if($this->request->is('ajax'))
            $this->render('admin_index' , 'ajax');
    }

    
    /*
    * display the add form, allow user to fill and add a news item
    *
    *
    */
    public function admin_add(){
        if ($this->request->is('post')) {

        	$this->_formatInputData(); // format input data to db structure
        	$validresult = $this->_validInput();
            if(!$validresult){
            	$item['News'] = $this->request->data;
            	$this->_formatOutputData(&$item['News']);
            	 
            	$this->set('item',  $item);
            	
            	$this->autoRender = false;
            	$this->render('admin_edit');
            	
            	return;
            }
// end validate title is empty          
  
			
           
            $this->News->create();
            if ($this->News->save($this->request->data)) {
            	$this->Session->setFlash(Configure::read('UPDATE_SUCCESS'));
                $this->redirect(array('action' => 'index'));
            } else {
            	$this->Session->setFlash(Configure::read('ERR_SAVING_DATA'));
            	
            	$item['News'] = $this->request->data;
            	$this->_formatOutputData(&$item['News']);
            	$this->set('item',  $item);
            	
               	$this->autoRender = false;
               	$this->render('admin_edit');
               	return;
            }
        }
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
    
    	if(!$this->request->data['publishdate']){ // validate publish date is empty
    		$result = false;
    		$flash .= '- '.Configure::read('ERR_PUBLISHDATE_BLANK').'<br />';
    	}
    	
    	if($this->request->data['url']){// validate url is valid or not.
    		$headers = @get_headers($this->request->data['url']);
    		
    		if(!$headers || strpos($headers[0], '404')){
    			$result = false;
    			$flash .= '- '.Configure::read('ERR_NOTVALID_URL').'<br />';
    		}
    	}
    	if(!$result) $this->Session->setFlash($flash);
    	return $result;
    }
    
   /*
    * display the edit form, allow user to fill and edit a news item
    * $id: id of the edited item.
    *
    */
    
    
    public function admin_edit($id){
    	$this->News->id = $id;
    	
    	if ($this->request->is('get')) {
    		$data = $this->News->read();
			if (!$data) {
			   $this->redirect(array( 'controller' => 'news',
					 'action' => 'index'));
			   return;
			  }
    		$this->_formatOutputData(&$data['News']);
    		$this->set('item',  $data);
    	}else {
    		$this->_formatInputData(false);
   
    		$validresult = $this->_validInput();
    		if(!$validresult){
    			$this->_formatOutputData(&$this->request->data);
    			
    			$item['News'] = $this->request->data;
    			$this->set('item',  $item);
    			
    			return ;
    		}
    		
    		if ($this->News->save($this->request->data)) {
    			$this->Session->setFlash(Configure::read('UPDATE_SUCCESS'));
    			$this->redirect(array('action' => 'index'));
    		} else {
    			$this->Session->setFlash(Configure::read('ERR_SAVING_DATA'));
    			
    			$item['News'] = $this->request->data;
            	$this->_formatOutputData(&$item['News']);
            	
            	$this->set('item',  $item);
    		}
    	}
    }
    
    /*
    * view a news's detail information
    * $id: id of the listing item.
    *
    */
   public function admin_view($id = null) {
        if ($id != null) {

            $data = $this->News->findById($id);
           
            if ($data) {
             $this->set('item', $data);
             return;
            }
        }
        $this->redirect(array(  'controller' => 'news',
              'action' => 'index'));
    }
    /*
    * set news item's status to disable (logical delete).
    * $id: id of the deleted item.
    *
    */
    public function admin_delete($id=null) {
    	if($this->request->is('ajax')){
    		$this->_ajax_delete();
    		return;
    	}
    
    	if ($id) {
    		$query = 'UPDATE news SET `status` = 1 WHERE id = '.$id;
    		 
    		if(!$this->News->query($query)){
    			$this->Session->setFlash(Configure::read('ERR_DELETING_DATA'));
    		}
    	}
    
    	$paging = explode(':',$this->request->params['pass'][1]); // get current page index
    
    	$this->Session->setFlash(Configure::read('DELETE_SUCCESS'));
    	$this->redirect(array('controller'=>'news', 'action'=>'index', 'page'=>$paging[1]));
    }
    
    
    /*
     * set multiple recruitment's status to disable (logical delete) via ajax.
    *
    *
    */
    
    public function _ajax_delete($id=null) {
    	if($this->request->data){
    		$delete = implode(',', $this->request->data['item']);
    	}
    
    	if ($delete) {
    		$query = 'UPDATE news SET `status` = 1 WHERE id IN ('.$delete.')';
    		 
    		if(!$this->News->query($query)){
    			$this->Session->setFlash(Configure::read('ERR_DELETING_DATA'));
    		}
    	}
    	$this->autoLayout = false;
    	$this->autoRender = false;
    	$this->Session->setFlash(Configure::read('DELETE_SUCCESS'));
    	echo  $this->requestAction('admin/news/index/page:'.$this->request->data['currentpage']);
    }
    
    
    /*
     * Format database data to form data
    * $data: database data
    * return $data array
    */
    
    private function _formatOutputData(&$data){
    	
    	//parse publishdate from db datetime - begin
    	if($data['publishdate']){
    		$temp = explode(' ', $data['publishdate']);
    		$date = explode('-', $temp[0]);
    		$time = explode(':', $temp[1]);
    		
    		$data['publishdate'] = $date[2] . '/' . $date[1] . '/' . $date[0] . ' ' . $time[0] . ':' . $time[1];
    		
    	}
    	
    	//parse publishdate from db datetime - begin
    	if($data['regdate']){
    		$temp = explode(' ', $data['regdate']);
    		$date = explode('-', $temp[0]);
    		$data['regdate'] = $date[2].'/'.$date[1].'/'.$date[0];
    	}
    }
    
    
    /*
     * Format form data to database data
    *
    *
    */
    
    private function _formatInputData($flagadd = true){
    	
    	//parse publishdate to db datetime - begin
    	if($this->request->data['publishdate']){
    		$temp = explode(' ', $this->request->data['publishdate']);
    		$date = explode('/', $temp[0]);
    		
    		$this->request->data['publishdate'] = $date[2] . '-' . $date[1] . '-' . $date[0] . ' '.$temp[1];
    	}
    	//parse publishdate to db datetime - end
    
    
    	// generate create time - beign
    	if($flagadd)
    		$this->request->data['regdate'] = date('Y-m-d H:m', time());
    	// generate create time - end
    	$this->request->data['title'] = strip_tags($this->request->data['title']);
    }
}