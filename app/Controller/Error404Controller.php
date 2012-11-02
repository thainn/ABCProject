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
class error404Controller extends AppController {

     function index()
     {
         $this->set('page_title','Lỗi - Không tìm thấy trang');
     }
}