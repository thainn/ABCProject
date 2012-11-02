<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    var $components = array('Session', 'Cookie');

     /**
	 * Before any Controller action
	 */
    function beforeFilter() {
		// if admin url requested
		if(isset($this->params['admin']) && $this->params['admin']) {

			
// check authentication			
			if($this->params['action'] != 'admin_login' && !$this->Session->read('Username')){
				$this->redirect(array('controller' => 'index', 'action' => 'login'));
				return;
			}
// end check authentication

			
            $this->layout = 'backend'; // set template layout for backend
            
            $title = array( 'news' => 'Tin tức',
            				'recruits' => 'Tuyển dụng',
		            		'index' => '',
		            		'contacts' => 'Liên hệ',
		            		'candidates' => 'Ứng viên',
            		);
            
 //prepare for breadcum bar and page title            
            $controller = strtolower($this->params['controller']);
            $this->set('breadcum_title', $title[$controller]);
            $this->set('breadcum_url', $controller);
            
            $title_for_layout = $title[$controller];
            
            if(!$title_for_layout) $title_for_layout = 'Trang chủ';
            
            $this->set('title_for_layout', $title_for_layout);
           
 // end prepare for breadcum and page title
            $this->set('current_controller', $controller);
		}
		
	}
	
	function afterFilter(){
		$this->Session->delete('Message.flash');
	}
}
