<?php 
class ClassModel extends Model {
	protected $_validate	 =	 array(
		array('year','require','系统提示：带*为必填字段，不能为空'),
		array('name','require','系统提示：带*为必填字段，不能为空'),
		// array('ename','require','系统提示：带*为必填字段，不能为空'),
	);

}
?>