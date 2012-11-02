<?php

/**
 * Leverages Vietnam Co., Ltd
 *
 * @author             Nguyen Ngoc Thai
 * @date created       10/19/2012
 * @modified by        Minh Hai Truong
 * @date modified      10/26/2012
 *
 * Manage candidate's information
 */
class CandidatesController extends AppController {

    var $uses = array('Contact', 'Candidates', 'news', 'recruit');

    public function admin_index() {
        App::uses('AppHelper', 'View/Helper');
        if ($this->Session->read('candidate') == 1) {
            $this->Session->write('candidateResult', '1');
            $this->Session->delete('candidate');
        } else if ($this->Session->read('candidate') == 0) {
            $this->Session->write('candidateResult', '0');
            $this->Session->delete('candidate');
        } else {
            $this->Session->write('candidateResult', '2');
            $this->Session->delete('candidate');
        }
        $this->paginate = array(
            'limit' => Configure::read('LIMIT_CANDIDATE'),
            'conditions' => array('flag' => '0'),
            'order'=>'senddate desc'
            
        );
        $data = $this->paginate("Candidates");
        $this->set("candidates", $data);

        $data = $this->Candidates->find('all', array('conditions' => array('flag' => '0')));
        $count = 0;
        foreach ($data as $data) {
            $count = $count + 1;
        }
        $this->set('lengthCandidates', $count);
         // $d = Router::url('/', true);
        $path = $this->here;
        $string = $path;
        $part = strtok($string, '/');
        while ($part != false) {
            $buff = $part;
            $part = strtok('/');
        }
        $part = strtok($buff, ':');
        while ($part != false) {
            $buff = $part;
            $part = strtok('/');
        }
        $NumberPaging = $count / Configure::read('LIMIT_CANDIDATE');
        $page = $count % Configure::read('LIMIT_CANDIDATE');
        if ($page != 0) {
            $NumberPaging = $NumberPaging + 1;
        }
        $Maxpage = $NumberPaging;
        $pageCurrent = $buff;
        settype($pageCurrent, "integer");
        settype($Maxpage, "integer");
        if ($pageCurrent > $Maxpage  && $pageCurrent >1) {
            $this->redirect(array('controller' => 'Candidates', 'action' => 'index/page:'.$Maxpage));
        }
    }

    public function admin_view($id = null) {
        if ($id != null) {
            // $data = $this->Contact->find('all',array('conditions' => array('Contact.id' => 1)));
            $data = $this->Candidates->findById($id);
            if ($data == null || $data['Candidates']['flag'] == '1') {
                $this->set('error', 'true');
                // $this->redirect(array('controller' => 'Error404', 'action' => 'index'));
            }
            $this->set('candidate', $data);
        }
    }

    public function admin_delete($id = null) {
        if ($id != null) {
            $sql = 'UPDATE candidates SET flag = 1 WHERE id =' . $id;
            $this->Candidates->query($sql);
            Configure::write('debug', 0);
            $this->Session->write('candidate', '1');
            $this->layout = 'ajax';
            $this->autoLayout = false;
            $this->autoRender = false;
            $this->header('Content-Type: application/json');
            $data = array();
            $result = array();
            echo json_encode($result);
        }
    }
    
    
        public function admin_deleteNotAjax($id = null,$page=null) {
        if ($id != null) {
            $sql = 'UPDATE candidates SET flag = 1 WHERE id =' . $id;
                 if(!$this->Candidates->query($sql)){
                          $this->Session->write('candidate', '1');
                        $this->redirect(array('controller'=>'candidates', 'action'=>'index/'.$page));
    		}
        }
           $this->Session->write('candidate', '1');
    	  $this->redirect(array('controller'=>'candidates', 'action'=>'index/'.$page));
    }
    

}