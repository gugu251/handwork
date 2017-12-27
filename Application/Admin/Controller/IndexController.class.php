<?php
namespace Admin\Controller;

use Think\Controller;

class IndexController extends AdminController
{

    public function index()
    {
        $this->display();
    }

    public function info()
    {
        $this->display();
    }
    
    public function column()
    {
        $this->display();
    }

    /**
     * 2016-12-05  修改地锁
     * @return [type] [description]
     */
    public function edit()
    {
        $data  = I('post.');
        $datas = array_filter($data);
        if ($datas) {
            if (array_filter($datas['parameter'])) {
                $datas['parameter'] = json_encode($datas['parameter']);
            }
            if (array_filter($datas['returncode'])) {
                $datas['returncode'] = json_encode($datas['returncode']);
            }
            if ($datas['id']) {

                $res = M('appword')->where("id={$datas['id']}")->save($datas);
            } else {
                $res = M('appword')->add($datas);
            }
            if (isset($res)) {
                $this->success('设置成功');
            } else {
                $this->error('设置失败');
            }
        } else {
            $id                    = I('get.id');
            $appword               = M('appword')->where("id='{$id}' ")->find();
            $appword['parameter']  = json_decode($appword['parameter'], true);
            $appword['returncode'] = json_decode($appword['returncode'], true);

            $appword['pnum1'] = count($appword['parameter']) + 1;
            $appword['pnum2'] = count($appword['parameter']) + 2;
            $appword['rnum1'] = count($appword['returncode']) + 1;
            $appword['rnum2'] = count($appword['returncode']) + 2;

            $this->assign('data', $appword);
            $this->assign('data', $appword);

            $this->display();

        }

    }
}
