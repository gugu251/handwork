<?php
namespace Admin\Model;
use Think\Model;
class BuildingPileUserModel extends Model {

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

    // phpcms查找单个数据
    public function getCmsUserOne($table,$where,$field=''){
        return M("cmsv3.{$table}",'v2_')->field($field)->where($where)->find();
    }

    // phpcms查找全部数据
    public function getCmsUserAll($table,$where,$field=''){
        return M("cmsv3.{$table}",'v2_')->field($field)->where($where)->select();
    }

    // phpcms添加数据
    public function addCmsUser($table,$data){
        return M("cmsv3.{$table}",'v2_')->add($data);
    }

    // phpcms更新数据
    public function saveCmsUser($table,$where,$data){
        return M("cmsv3.{$table}",'v2_')->where($where)->save($data);
    }

    // phpcms处理用户，用户uid做key,昵称nickname做键
    public function getCmsUserNicknameArray($array){
        $return_array = array();
        foreach ($array as $k => $v) {
            $return_array[$v['userid']] = $v['nickname'];
        }
        return $return_array;
    }

    // phpcms 添加用户
    public function addUserMember($mobile,$name,$pw=''){
        $time = time();
        $ip = get_client_ip();
        $password = $pw ? password($pw) : password($mobile);

        // 不存在，注册
        $data['appname'] = 'phpcms v9';
        $data['username'] = $mobile;
        $data['password'] = $password['password'];
        $data['random'] = $password['encrypt'];
        $data['regdate'] = $time;
        $data['lastdate'] = $time;
        $data['regip'] = $ip;
        $data['lastip'] = $ip;
        $sso_members = M('cmsv3.sso_members','v2_')->add($data);
        if($sso_members){

            // 判断来源
            $HTTP_USER_AGENT = strtolower($_SERVER['HTTP_USER_AGENT']);
            if(strpos($HTTP_USER_AGENT, 'iphone')||strpos($HTTP_USER_AGENT, 'ipad')){
                $data['from'] = 2;
            }else if(strpos($HTTP_USER_AGENT, 'java')){
                $data['from'] = 3;
            }

            unset($data['appname'],$data['random']);

            $data['phpssouid'] = $sso_members;
            $data['encrypt'] = $password['encrypt'];
            $data['nickname'] = $name;
            $data['mobile'] = $mobile;
            $data['groupid'] = 2;
            $data['modelid'] = 10;
            $members_uid = M('cmsv3.member','v2_')->add($data);

            $detail_data['userid'] = $members_uid;
            M('cmsv3.member_detail','v2_')->add($detail_data);

            // 添加一条数据到钱包
            M('user_wallet')->add(array('user_id'=>$members_uid));

            return $members_uid;
        }else{
            return false;
        }
    }


}