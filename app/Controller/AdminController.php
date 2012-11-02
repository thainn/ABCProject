<?php
class AdminController extends AppController{
    
    function index()
    {
        
         $this->redirect(array('controller' => 'admin/index', 'action' => 'index'));
    }
    
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
