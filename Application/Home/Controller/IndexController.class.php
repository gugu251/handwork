<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$list = D('News')->getList($where);
    	$this->assign('newslist', $list);
        $this->display();
    }
}