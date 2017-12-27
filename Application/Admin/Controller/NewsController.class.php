<?php
namespace Admin\Controller;

use Think\Controller;

class NewsController extends Controller
{
	/**
	 * 图文内容
	 */
    public function newslist(){
    	$list = M('news')->select();
    	$this->assign('list', $list);
    	$this->display();
    }
    
    /**
	 * 添加图文
	 */
    public function newsadd(){
    	$this->display();
    }
    
    /**
	 * 编辑图文
	 */
    public function newsedit(){
    	$this->display();
    }
    
    /**
	 * 图文分类
	 */
    public function newscate(){
    	$this->display();
    }

}
