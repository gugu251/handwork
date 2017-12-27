<?php
namespace Admin\Controller;

use Think\Controller;

class AdminController extends Controller
{

    // 权限控制
    protected function _initialize()
    {

        $list = M('adminmenu')->where('pid=0')->select();
        foreach ($list as $key => $value) {
            $pid               = $value['id'];
            $list[$key]['son'] = M('adminmenu')->where('pid='.$pid)->select();
        }
        // var_dump($list);
        $this->assign('menus', $list);

    }

}
