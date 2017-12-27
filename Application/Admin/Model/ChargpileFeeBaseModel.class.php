<?php
namespace Admin\Model;
use Think\Model;
class ChargpileFeeBaseModel extends Model {

    // 保存费用信息
    public function saveInfo($uid,$fee,$info,$price='',$company=''){
        $final_array = setFee($fee);
        if(!$final_array){
            return false;
        }

        $time = time();
        // 开启事务
        $model = new Model();
        $model -> startTrans();
        $status = true;

        // 删除存在相同的
        $delete_where['owner_id'] = ['eq',$uid];
        $delete_res = $model->table('dd_chargpile_fee_base')->where($delete_where)->delete();
        if(false === $delete_res){
            $status = false;
        }

        // 添加数据
        if($status){
            // 批量插入
            $insert_sql = "insert into dd_chargpile_fee_base (owner_id,start,end,power,service,delay) value ";
            foreach ($final_array as $v) {
                $v['power'] = $v['power']*100;
                $v['service'] = $v['service']*100;
                $v['delay'] = $v['delay']*100;

                $insert_sql .= "('{$uid}','{$v['start']}','{$v['end']}','{$v['power']}','{$v['service']}','{$v['delay']}'),";
            }
            $insert_sql = trim($insert_sql,',');
            $inser_res = $model -> execute($insert_sql);
            if(!$inser_res){
                $status = false;
            }
        }

        // 查找用户是否存在，存在就更新，否则添加
        $pile_info = D('building_pile_user')->get_one("user_id={$uid}",'id');
        if ($pile_info) {
            $pile_info = $model->table('dd_building_pile_user')->where("user_id={$uid}")->save($info);
            if(false === $pile_info){
                $status = false;
            }
        } else {
            $info['user_id'] = $uid;
            $info['create_time'] = $time;
            $pile_info = $model->table('dd_building_pile_user')->add($info);
            if(!$pile_info){
                $status = false;
            }
        }

        // 判断是否是企业用户
        if($status && $company){

            // 查询是否存在
            $cooperate_where['uid'] = ['eq',$uid];
            $company_status = M('cooperate') -> where($cooperate_where) -> find();

            $cooperate_data['pid'] = $company['pid'];
            $cooperate_data['path'] = $company['path'] && $company['pid'] != 0 ? $company['path'].$company['pid'].',' : $company['path'];

            if($company_status){
                $cooperate_result = $model->table('dd_cooperate')->where($cooperate_where)->save($cooperate_data);
                if(false === $cooperate_result){
                    $status = false;
                }
            }else{
                $cooperate_data['uid'] = $uid;
                // 不存在就添加
                $cooperate_result = $model->table('dd_cooperate')->add($cooperate_data);
                if(!$cooperate_result){
                    $status = false;
                }
            }

            // 添加或者更新企业用户到权限表
            if($status){
                // 如果是总公司，添加到总公司权限
                if($cooperate_data['pid'] == 0){
                    $group_id = 1;
                }else{
                // 添加到子公司权限
                    $group_id = 2;
                }
                $user_access = M('cooperate_auth_group_access')->where("uid={$uid} and group_id={$group_id}")->find();
                if(!$user_access){
                    $dd_cooperate_auth_group_access_data['group_id'] = $group_id;
                    $dd_cooperate_auth_group_access_data['uid'] = $uid;
                    $auth_group = $model->table('dd_cooperate_auth_group_access')->add($dd_cooperate_auth_group_access_data);
                    if(!$auth_group){
                        $status = false;
                    }

                }
            }

        }

        // 分成比例，个人
        if($status){
            $chargpile_proportion_base_res = D('chargpile_proportion_base')->get_one("owner_id={$uid}");
            // 存在，数字
            if($price == 0 || is_numeric($price)){
                $proportion_base['proportion'] = $price;
            }else{
                $proportion_base['proportion'] = 60;
            }

            //
            if($chargpile_proportion_base_res){
                // 更新
                $proportion_res = $model->table('dd_chargpile_proportion_base')->where("owner_id={$uid}")->save($proportion_base);
                if(false === $proportion_res){
                    $status = false;
                }
            }else{
                // 添加
                $proportion_base['owner_id'] = $uid;
                $proportion_res = $model->table('dd_chargpile_proportion_base')->add($proportion_base);
                if(!$proportion_res){
                    $status = false;
                }
            }
        }


        if($status){
            $model -> commit();
        }else{
            $model -> rollback();
        }
        return $status;
    }


}