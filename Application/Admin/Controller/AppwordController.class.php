<?php
namespace Admin\Controller;

use Think\Controller;

class AppwordController extends AdminController
{
    public function index()
    {
        $cate_id  = I('get.cate_id');
        if($cate_id){
           $where['cid'] =  $cate_id;
        }
        $appword = M('app_word')->where($where)->select();
        $this->assign('data', $appword);
        $this->display();
    }

    public function info()
    {
        $this->display();
    }

    /**
     * 分类
     */
    public function cate()
    {
        $list = M('app_cate')->select();
        $this->assign('data', $list);
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

                $res = M('app_cate')->where("id={$datas['id']}")->save($datas);
            } else {
                $res = M('app_cate')->add($datas);
            }
            if (isset($res)) {
                $this->success('设置成功');
            } else {
                $this->error('设置失败');
            }
        } else {
            $id                    = I('get.id');
            $appword               = M('app_cate')->where("id='{$id}' ")->find();
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

    /**
     * 2016-12-05  修改地锁
     * @return [type] [description]
     */
    public function cateEdit()
    {
        $data  = I('post.');
        $datas = array_filter($data);
        if ($datas) {
            if ($datas['id']) {
                $res = M('app_cate')->where("id={$datas['id']}")->save($datas);
            } else {
                $res = M('app_cate')->add($datas);
            }
            if (isset($res)) {
                $this->success('设置成功');
            } else {
                $this->error('设置失败');
            }
        } else {
            $id      = I('get.id');
            $appword = M('app_cate')->where("id='{$id}' ")->find();
            $this->assign('data', $appword);
            $this->display();

        }

    }
}
