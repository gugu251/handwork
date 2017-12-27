<?php
namespace Admin\Model;
use Think\Model;
class ChargpileModel extends Model {

    public function get_one($where,$field=''){
        return $this -> field($field) -> where($where) -> find();
    }

    //
    public function get_all($where,$field=''){
        return $this -> field($field) -> where($where) -> select();
    }

    //
    public function get_count($where=''){
        return $this -> where($where) ->count();
    }

    // 查询
    public function get_list($p=1,$limit=10,$field='',$where='',$order=''){
        return $this -> field($field) -> where($where) -> page($p,$limit) -> order($order) -> select();
    }

    // 修改充电桩信息
    public function updatePile($id,$postData)
    {
        $time = time();
        // 开启事务
        $model = new Model();
        $model -> startTrans();
        $status = true;

        // 更新本地充电桩数据
        $where['id'] = ['eq',$id];
        //$data['did'] = $postData['did'];
        $data['title'] = $postData['title'];
        $data['address'] = $postData['address'];
        $data['deduct'] = $postData['deduct'];
        $data['lng'] = $postData['lng'];
        $data['lat'] = $postData['lat'];
        $data['province'] = $postData['province'];
        $data['city'] = $postData['city'];
        $data['county'] = $postData['county'];
        $data['type'] = $postData['type'];
		$data['charging_id'] = $postData['charging_id'];
        $res_save = $model->table('dd_chargpile')->where($where)->save($data);
        if(false === $res_save){
            $status = false;
        }

        $res = M('chargpile')->field('id,did,owner_id')->where($where)->find();
        // 更新公共充电桩
        /*if($status && $res){
            $data['id'] = $res['id'];
            $data['did'] = $res['did'];
            $data['owner_id'] = $res['owner_id'];
            $free_res = $this -> setPileFree($data);
            if(!$free_res){
                $status = false;
            }
        }else{
            $status = false;
        }*/

        // 分成比例，个人
        if($status && $res['did']){
            // 提交数据存在，数字
            if($postData['price'] == 0 || is_numeric($postData['price'])){
                $proportion_base['proportion'] = $postData['price'];
            }else{
                $proportion_base['proportion'] = 60;
            }

            // 比例分成是否存在
            $proportion['chargpile_id'] = ['eq',$id];
            $chargpile_proportion_status = D('chargpile_proportion')->get_one($proportion);
            // 存在更新，否则添加
            if($chargpile_proportion_status){
                // 更新
                $proportion_res = $model->table('dd_chargpile_proportion')->where("id={$chargpile_proportion_status['id']}")->save($proportion_base);
                if(false === $proportion_res){
                    $status = false;
                }
            }else{
                // 添加
                $proportion_base['chargpile_id'] = $id;
                $proportion_base['did'] = $res['did'];
                $proportion_res = $model->table('dd_chargpile_proportion')->add($proportion_base);
                if(!$proportion_res){
                    $status = false;
                }
            }

        }else{
            $status = false;
        }

        // 远程设置充电桩
        if($status){
            $setChargpile_data['sn'] = $postData['did'];
            $setChargpile_data['owner'] = $postData['owner'];
            $setChargpile_data['title'] = $postData['title'];
            $setChargpile_data['address'] = $postData['address'];
            $setChargpile_data['shared'] = $postData['shared'] ? true : false;
            $setChargpile_data['lng'] = $postData['lng'];
            $setChargpile_data['lat'] = $postData['lat'];
            $setChargpile_result = setChargpile($setChargpile_data);
            if(!$setChargpile_result){
                $status = false;
            }
        }

        if($status){
            $model -> commit();
        }else{
            $model -> rollback();
        }
        return $status;

    }

    // 设置电费信息
    public function updateFee($id,$did,$owner,$fee)
    {
        if(!$id || !$did || !$owner){
            return false;
        }

        $final_array = setFee($fee);
        if(!$final_array){
            return false;
        }

        $time = time();
        $final_status = true;
        $model = new Model();
        // 根据充电桩did查出该充电桩所在站点的，所有充电桩表的id
        $dd_chargpile_id_array = M()->query("select id,did,charging_id from dd_chargpile where charging_id=(select charging_id from dd_chargpile where did='{$did}')");
        foreach ($dd_chargpile_id_array as $value) {
            // 开启事务
            $model -> startTrans();
            $status = true;

            // 删除以前设计
            $delete_where['chargpile_id'] = ['eq',$value['id']];
            $delete_res = $model->table('dd_chargpile_fee')->where($delete_where)->delete();
            if(false === $delete_res){
                $status = false;
            }

            // 更改充电站的费用数据
            if($status){
                $dd_charging_data['chargefree'] = $final_array[0]['power'] ? $final_array[0]['power'].'元/度' : '0';
                $dd_charging_data['servicefree'] = $final_array[0]['service'] ? $final_array[0]['service'].'元/度' : '0';
                $dd_charging_data['delayfree'] = $final_array[0]['delay'] ? $final_array[0]['delay'].'元/小时' : '0';
                $dd_charging_where['id'] = ['eq',$value['charging_id']];
                $dd_charging_result = $model->table('dd_charging')->where($dd_charging_where)->save($dd_charging_data);
                if(false === $dd_charging_result){
                    $status = false;
                }
            }

            // 添加数据
            if($status){
                // 批量插入
                $insert_sql = "insert into dd_chargpile_fee (chargpile_id,start,end,power,service,delay) values ";
                foreach ($final_array as $k => $v) {
                    $v['power'] = $v['power']*100;
                    $v['service'] = $v['service']*100;
                    $v['delay'] = $v['delay']*100;
                    $insert_sql .= "('{$value['id']}','{$v['start']}','{$v['end']}','{$v['power']}','{$v['service']}','{$v['delay']}'),";
                    $final_array[$k]['power'] = $v['power'];
                    $final_array[$k]['service'] = $v['service'];
                    $final_array[$k]['delay'] = $v['delay'];
                }
                $insert_sql = trim($insert_sql,',');
                $inser_res = $model -> execute($insert_sql);
                if(!$inser_res){
                    $status = false;
                }
            }

            // 远程设置充电桩
            if($status){
                $setChargpile_data['sn'] = $did;
                $setChargpile_data['owner'] = $owner;
                $setChargpile_data['fee'] = $final_array;
                $setChargpile_result = setChargpile($setChargpile_data);
                if(!$setChargpile_result){
                    $status = false;
                }
            }

            if($status){
                $model -> commit();
            }else{
                $final_status = false;
                $model -> rollback();
            }

        }

        return $final_status;

    }

    /* 设置公共充电桩
    *  $postData 充电桩表数据
    */
    /* 设置公共充电桩
    *  $postData 充电桩表数据
    */
    public function setPileFree($postData){
        $status = true;
        if ($postData['charging_id']) {

            $where_charging['id'] = $postData['charging_id'];
            $charging_resutl = M('charging')->where($where_charging)->find();
            $slow_count = $postData['type'] == 0 ? 1 : 0; // 慢充数量
            $slownum = $postData['type'] == 0 ? 1 : 0; //  慢充空闲数量
            $fast_count = $postData['type'] == 1 ? 1 : 0; // 快充数量
            $fastnum = $postData['type'] == 1 ? 1 : 0; //  快充空闲数量

            $charg_data_update['slow_count'] = $charging_resutl['slow_count'] + $slow_count; // 慢充数量
            $charg_data_update['slownum'] = $charging_resutl['slownum'] + $slownum; // 慢充数量
            $charg_data_update['fast_count'] = $charging_resutl['fast_count'] + $fast_count; // 慢充数量
            $charg_data_update['fastnum'] = $charging_resutl['fastnum'] + $fastnum; // 慢充数量

            $dd_charging_result = M('charging')->where($where_charging)->save($charg_data_update);
            if(false === $dd_charging_result){
                $status = false;
            }
            return $status;

        }
    }


    /**
     * 更新充电桩共享状态
     * @param  [type] $did   [充电桩did]
     * @param  [type] $share [状态：1或者0]
     * @return [type]        [bool]
     */
    public function updateDidShare($did,$share){
        $time = time();
        // 开启事务
        $model = new Model();
        $model -> startTrans();
        $final = true;

        // 查询充电桩是否正在充电或者预约中
        $where['did'] = ['eq',$did];
        $book = M('chargpile_book')->field('end_time,status')->where($where)->find();
        if($book){
            // 充电中
            if($book['status'] == 1){
                $final = false;
            // 预约中
            }else if($book['status'] == 0 && $book['end_time'] > $time){
                $final = false;
            }
        }

        // 更改充电桩共享状态
        if($final){
            $chargpile = $model->table('dd_chargpile')->where($where)->setField('shared',$share);
            if(false === $chargpile){
                $final = false;
            }
        }

        // 更改公共充电桩中共享状态
        if($final){
            $owner = $share ? 6 : 2;
            $chargpile_where['did'] = ['eq',$did];
            $charging_id = $model->where($chargpile_where)->getField('charging_id');
            if ($charging_id) {
                $charging_where['id'] = ['eq',$charging_id];
                $charging = $model->table('dd_charging')->where($charging_where)->setField('owner',$owner);
            } else {
                $charging_where['station_id'] = ['eq',$did];
                $charging = $model->table('dd_charging')->where($charging_where)->setField('owner',$owner);
            }
            if(false === $charging){
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