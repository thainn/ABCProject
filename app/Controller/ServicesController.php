<?php
/**
* Leverages Vietnam Co., Ltd
* 
* @author              Nguyen Ngoc Hoang
* @date created        23/10/2012
* @modified by         
* @date modified      
*
* ServicesController display services of company
*/
class ServicesController extends AppController {
    function index()
    {
        $this->set('page_title','Dịch vụ');
    }
    function hc()
    {
        $this->set('page_title','Dịch vụ Human capital');
    }
    function media()
    {
        $this->set('page_title','Dịch vụ truyền thông');
    }
    function se()
    {
        $this->set('page_title','System engineering');
    }
    function medical()
    {
        $this->set('page_title','Dịch vụ medical');
    }
}
