<?php
namespace Admin\Model;
use Think\Model;
class ChargpileProportionBaseModel extends Model {
    public function get_one($where,$field=''){
        return $this -> field($field) -> where($where) -> find();
    }
}