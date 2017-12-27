<?php
namespace Admin\Model;
use Think\Model;
class ChargpileFeeModel extends Model {

    // 保存费用信息
    public function saveInfo($uid,$fee){
        // 处理充电费用
        $final_array = array();
        foreach ($fee as $k => $v) {
            $tmp_arr = explode('&',urldecode(htmlspecialchars_decode($v)));

            // 如果长度不够，说明参数错误
            if(5 != count($tmp_arr)){
                return false;
            }
            // 把参数拼装到新数组
            foreach ($tmp_arr as $value) {
                $tmp = explode('=', $value);
                $final_array[$k][$tmp[0]] = $tmp[1];
            }
        }

        // 得到正确的数组
        // 判断数组中数据是否规范
        $len = count($final_array);
        // 如果只有一个
        if($len == 1){
            // 开始就是00，结束就是24
            if($final_array[0]['start'] != '00:00' || $final_array[0]['end'] != '24:00'){
                return false;
            }
        }else{
            // 取得数组第一个和最后一个
            $last = $final_array[$len-1];
            if($final_array[0]['start'] != '00:00' || $last['end'] != '24:00'){
                return false;
            }
            // 循环数组判断
            for($i=0;$i<$len;$i++){
                if($i > 0){
                    if($final_array[$i-1]['end'] != $final_array[$i]['start']){
                        return false;
                    }
                }
            }
        }

        $time = time();
        // 开启事务
        $model = new Model();
        $model -> startTrans();
        $status = true;

        // 删除存在相同的
        $delete_where['owner_id'] = ['eq',$uid];
        $delete_where['chargpile_id'] = ['exp','is null'];
        $delete_res = $model->table('dd_chargpile_fee')->where($delete_where)->delete();
        if(false === $delete_res){
            $status = false;
        }

        // 添加数据
        if($status){
            // 批量插入
            $insert_sql = "insert into dd_chargpile_fee (owner_id,start,end,power,service,delay) value ";
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

        if($status){
            $model -> commit();
        }else{
            $model -> rollback();
        }
        return $status;
    }


}