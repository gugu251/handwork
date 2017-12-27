<?php
namespace Admin\Controller;

use Think\Controller;

class StarwordController extends Controller
{
    public function index()
    {
        $starword = M('starword')->select();
        $this->assign('data', $starword);
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
            if (array_filter($datas['parameter'])) {
                $datas['parameter'] = json_encode($datas['parameter']);
            }
            if (array_filter($datas['returncode'])) {
                $datas['returncode'] = json_encode($datas['returncode']);
            }
            if ($datas['id']) {

                $res = M('starword')->where("id={$datas['id']}")->save($datas);
            } else {
                $res = M('starword')->add($datas);
            }
            if (isset($res)) {
                $this->success('设置成功');
            } else {
                $this->error('设置失败');
            }
        } else {
            $id                    = I('get.id');
            $starword               = M('starword')->where("id='{$id}' ")->find();
            $starword['parameter']  = json_decode($starword['parameter'], true);
            $starword['returncode'] = json_decode($starword['returncode'], true);

            $starword['pnum1'] = count($starword['parameter']) + 1;
            $starword['pnum2'] = count($starword['parameter']) + 2;
            $starword['rnum1'] = count($starword['returncode']) + 1;
            $starword['rnum2'] = count($starword['returncode']) + 2;

            $this->assign('data', $starword);
            $this->assign('data', $starword);

            $this->display();

        }

    }
}
