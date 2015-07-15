<?php 
class CourseteacherModel extends Model {
	protected $_validate	 =	 array(
		array('term','require','系统提示：带*为必填字段，不能为空'),
		array('coursenumber','require','系统提示：带*为必填字段，不能为空'),
        array('teacher','require','系统提示：带*为必填字段，不能为空'),
	);

}
?>