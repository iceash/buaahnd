<?php 
class CourseModel extends Model {
	protected $_validate	 =	 array(
        // array('number','','课程号已经存在！课程号必须是唯一的',0,'unique',3), 
		// array('number','require','系统提示：带*为必填字段，不能为空'),
		array('name','require','系统提示：带*为必填字段，不能为空'),
        array('category2','require','系统提示：带*为必填字段，不能为空'),
        array('ename','require','系统提示：带*为必填字段，不能为空'),
        array('credit','require','系统提示：带*为必填字段，不能为空'),
        // array('coursetime','require','系统提示：带*为必填字段，不能为空')
	);

}
?>