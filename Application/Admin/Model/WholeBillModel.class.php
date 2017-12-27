<?php
namespace Admin\Model;
use Think\Model;
class WholeBillModel extends Model {

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

}