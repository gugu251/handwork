<?php if (!defined('THINK_PATH')) exit();?><!--头部 start-->
<!DOCTYPE html>
<html lang="zh-cn">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<meta name="renderer" content="webkit">
		<title></title>
		<link rel="stylesheet" href="/Public/style/admin/css/pintuer.css">
		<link rel="stylesheet" href="/Public/style/admin/css/admin.css">
		<script src="/Public/style/admin/js/jquery.js"></script>
		<script src="/Public/style/admin/js/pintuer.js"></script>
	</head>

	<body>

<!--头部 end-->

<form method="post" action="" id="listform">
	<div class="panel admin-panel">
		<div class="panel-head"><strong class="icon-reorder"> 内容列表</strong>
			<a href="" style="float:right; display:none;">添加字段</a>
		</div>
		<div class="padding border-bottom">
			<ul class="search" style="padding-left:10px;">
				<li>
					<a class="button border-main icon-plus-square-o" href="/admin/news/newsadd"> 添加内容</a>
				</li>
				<li>搜索：</li>
				<li>首页
					<select name="s_ishome" class="input" onchange="changesearch()" style="width:60px; line-height:17px; display:inline-block">
						<option value="">选择</option>
						<option value="1">是</option>
						<option value="0">否</option>
					</select>
					&nbsp;&nbsp; 推荐
					<select name="s_isvouch" class="input" onchange="changesearch()" style="width:60px; line-height:17px;display:inline-block">
						<option value="">选择</option>
						<option value="1">是</option>
						<option value="0">否</option>
					</select>
					&nbsp;&nbsp; 置顶
					<select name="s_istop" class="input" onchange="changesearch()" style="width:60px; line-height:17px;display:inline-block">
						<option value="">选择</option>
						<option value="1">是</option>
						<option value="0">否</option>
					</select>
				</li>
				<?php if($iscid == 1): ?><li>
						<select name="cid" class="input" style="width:200px; line-height:17px;" onchange="changesearch()">
							<option value="">请选择分类</option>
							<option value="">产品分类</option>
							<option value="">产品分类</option>
							<option value="">产品分类</option>
							<option value="">产品分类</option>
						</select>
					</li><?php endif; ?>
				<li>
					<input type="text" placeholder="请输入搜索关键字" name="keywords" class="input" style="width:250px; line-height:17px;display:inline-block" />
					<a href="javascript:void(0)" class="button border-main icon-search" onclick="changesearch()"> 搜索</a>
				</li>
			</ul>
		</div>
		<table class="table table-hover text-center">
			<tr>
				<th width="100" style="text-align:left; padding-left:20px;">ID</th>
				<th width="10%">排序</th>
				<th>图片</th>
				<th>名称</th>
				<th>属性</th>
				<th>分类名称</th>
				<th width="10%">更新时间</th>
				<th width="310">操作</th>
			</tr>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
					<td style="text-align:left; padding-left:20px;"><input type="checkbox" name="id[]" value="" /><?php echo ($vo["id"]); ?>
					</td>
					<td><input type="text" name="sort[1]" value="1" style="width:50px; text-align:center; border:1px solid #ddd; padding:7px 0;" /></td>
					<td width="10%"><img src="<?php echo ($vo["thumb"]); ?>" alt="" width="70" height="50" /></td>
					<td><?php echo ($vo["title"]); ?></td>
					<td>
						<font color="#00CC99">首页</font>
					</td>
					<td>产品分类</td>
					<td><?php echo ($vo["create_time"]); ?></td>
					<td>
						<div class="button-group">
							<a class="button border-main" href="add.html"><span class="icon-edit"></span> 修改</a>
							<a class="button border-red" href="javascript:void(0)" onclick="return del(1,1,1)"><span class="icon-trash-o"></span> 删除</a>
						</div>
					</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<td colspan="8">
						<div class="pagelist">
							<a href="">上一页</a> <span class="current">1</span>
							<a href="">2</a>
							<a href="">3</a>
							<a href="">下一页</a>
							<a href="">尾页</a>
						</div>
					</td>
				</tr>
		</table>
	</div>
</form>
<script type="text/javascript">
	//搜索
	function changesearch() {

	}

	//单个删除
	function del(id, mid, iscid) {
		if(confirm("您确定要删除吗?")) {

		}
	}

	//全选
	$("#checkall").click(function() {
		$("input[name='id[]']").each(function() {
			if(this.checked) {
				this.checked = false;
			} else {
				this.checked = true;
			}
		});
	})

	//批量删除
	function DelSelect() {
		var Checkbox = false;
		$("input[name='id[]']").each(function() {
			if(this.checked == true) {
				Checkbox = true;
			}
		});
		if(Checkbox) {
			var t = confirm("您确认要删除选中的内容吗？");
			if(t == false) return false;
			$("#listform").submit();
		} else {
			alert("请选择您要删除的内容!");
			return false;
		}
	}

	//批量排序
	function sorts() {
		var Checkbox = false;
		$("input[name='id[]']").each(function() {
			if(this.checked == true) {
				Checkbox = true;
			}
		});
		if(Checkbox) {

			$("#listform").submit();
		} else {
			alert("请选择要操作的内容!");
			return false;
		}
	}

	//批量首页显示
	function changeishome(o) {
		var Checkbox = false;
		$("input[name='id[]']").each(function() {
			if(this.checked == true) {
				Checkbox = true;
			}
		});
		if(Checkbox) {

			$("#listform").submit();
		} else {
			alert("请选择要操作的内容!");

			return false;
		}
	}

	//批量推荐
	function changeisvouch(o) {
		var Checkbox = false;
		$("input[name='id[]']").each(function() {
			if(this.checked == true) {
				Checkbox = true;
			}
		});
		if(Checkbox) {

			$("#listform").submit();
		} else {
			alert("请选择要操作的内容!");

			return false;
		}
	}

	//批量置顶
	function changeistop(o) {
		var Checkbox = false;
		$("input[name='id[]']").each(function() {
			if(this.checked == true) {
				Checkbox = true;
			}
		});
		if(Checkbox) {

			$("#listform").submit();
		} else {
			alert("请选择要操作的内容!");

			return false;
		}
	}

	//批量移动
	function changecate(o) {
		var Checkbox = false;
		$("input[name='id[]']").each(function() {
			if(this.checked == true) {
				Checkbox = true;
			}
		});
		if(Checkbox) {

			$("#listform").submit();
		} else {
			alert("请选择要操作的内容!");

			return false;
		}
	}

	//批量复制
	function changecopy(o) {
		var Checkbox = false;
		$("input[name='id[]']").each(function() {
			if(this.checked == true) {
				Checkbox = true;
			}
		});
		if(Checkbox) {
			var i = 0;
			$("input[name='id[]']").each(function() {
				if(this.checked == true) {
					i++;
				}
			});
			if(i > 1) {
				alert("只能选择一条信息!");
				$(o).find("option:first").prop("selected", "selected");
			} else {

				$("#listform").submit();
			}
		} else {
			alert("请选择要复制的内容!");
			$(o).find("option:first").prop("selected", "selected");
			return false;
		}
	}
</script>
<!--底部 start-->
	</body>

</html>
<!--底部 end-->