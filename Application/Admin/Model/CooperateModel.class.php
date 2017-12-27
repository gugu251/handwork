<?php
namespace Admin\Model;
use Think\Model;
class CooperateModel extends Model {

    public function get_one($where,$field=''){
        return $this -> field($field) -> where($where) -> find();
    }

    public function get_all($where,$field=''){
        return $this -> field($field) -> where($where) -> select();
    }

    //
    public function get_count($where=''){
        return $this -> where($where) ->count();
    }

    // 查询用户
    public function get_list($p=1,$limit=10,$field='',$where='',$order=''){
        return $this -> field($field) -> where($where) -> page($p,$limit) -> order($order) -> select();
    }

    /**
     * 更新提现的状态
     * $id id 月账单表
     * $old_status 状态  上一次的状态
     * $new_status  状态  更新的状态
     */
    public function updateWithdraw($id,$old_status,$new_status){
        if(!$id || !is_numeric($old_status) || !is_numeric($new_status)){
            return false;
        }

        // 事务
        $time = time();
        // 开启事务
        $model = new Model();
        $model -> startTrans();
        $final = true;

        // 月账单表
        $where['id'] = ['eq',$id];
        $where['status'] = ['eq',$old_status];
        $data['status'] = $new_status;
        if($new_status == 1){
            $data['apply_time'] = $time;
        }else if($new_status == 2){
            $data['collect_time'] = $time;
        }

        // 月结账单总表
        $tabe_whole = $model -> table("dd_whole_clearing") -> where($where) -> save($data);
        if(false === $tabe_whole){
            $final = false;
        }

        if($final){
            // 单笔账单
            $bill_where['whole_clearing_id'] = ['eq',$id];
            $table_whole_bill = $model -> table("dd_whole_bill") -> where($bill_where) -> setField('status',$new_status);
            if(false === $table_whole_bill){
                $final = false;
            }
        }

        if($final){
            $model -> commit();
        }else{
            $model -> rollback();
        }
        return $final;

    }

}