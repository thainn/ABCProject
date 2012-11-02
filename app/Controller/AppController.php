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
	var $components = array('i18');
	 
	function beforeFilter(){
	$this->setLanguage();
		print "<pre>";
		print_r($this->Session->read('Config.language'));
		print "</pre>";
// 		Configure::write('Config.language', 'jp');
	}
	
	function setLanguage() {
		if(!isset($this->params['lang'])) $this->params['lang'] = 'jp';
		$lang = $this->params['lang'];
		App::import('Core', 'i18n');
		$I18n =& I18n::getInstance();
		$I18n->l10n->get($lang);
		foreach (Configure::read('Config.languages') as $lang => $locale) {
			if($lang == $this->params['lang'])
				$this->params['locale'] = $locale['locale'];
		}
	}
}