<?php
/**
* Leverages Vietnam Co., Ltd
* 
* @author              Nguyen Ngoc Hoang
* @date created        23/10/2012
* @modified by         
* @date modified      
*
* VisionsController display profile of company
*/
class VisionsController extends AppController {

     function index()
     {
         $this->set('page_title','Mục tiêu của công ty');
     }
     function profiles()
     {
         $this->set('page_title','Giới thiệu công ty');
     }
     function philosophy()
     {
         $this->set('page_title','Philosophys');
     }
}
