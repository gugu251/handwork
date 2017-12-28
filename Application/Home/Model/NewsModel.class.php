<?php
namespace Home\Model;
use Think\Model;

class NewsModel extends Model
{
	/**
	 * 获取数据
	 * @param int $page
	 * @param int $limit
	 * @return mixed
	 */
	public function getList($where,$page=1,$limit=20){
		$list = $this->where($where)->page($page,$limit)->select();
		return $list;
	}

}