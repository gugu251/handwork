<?php
namespace Admin\Controller;

use Think\Controller;

class MarkwordController extends AdminController
{
    public function index()
    {
        $mark_word = M('mark_word')->select();
        $this->assign('data', $mark_word);
        $this->display();
    }

    public function info()
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
            
            if ($datas['id']) {

                $res = M('mark_word')->where("id={$datas['id']}")->save($datas);
            } else {
                $res = M('mark_word')->add($datas);
            }
            if (isset($res)) {
                $this->success('设置成功');
            } else {
                $this->error('设置失败');
            }
        } else {
            $id                   = I('get.id');
            $mark_word               = M('mark_word')->where("id='{$id}' ")->find();
            $mark_word['parameter']  = json_decode($mark_word['parameter'], true);
            $mark_word['returncode'] = json_decode($mark_word['returncode'], true);

            $mark_word['pnum1'] = count($mark_word['parameter']) + 1;
            $mark_word['pnum2'] = count($mark_word['parameter']) + 2;
            $mark_word['rnum1'] = count($mark_word['returncode']) + 1;
            $mark_word['rnum2'] = count($mark_word['returncode']) + 2;

            $this->assign('data', $mark_word);
            $this->assign('data', $mark_word);

            $this->display();

        }

    }
}
