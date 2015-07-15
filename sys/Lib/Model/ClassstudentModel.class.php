<?php 
class ClassstudentModel extends Model {
	protected $_validate	 =	 array(
        array('student','','学号已经存在！学号必须是唯一的',0,'unique',3), 
		array('classid','require','系统提示：带*为必填字段，不能为空'),
		array('student','require','系统提示：带*为必填字段，不能为空'),
        array('studentname','require','系统提示：带*为必填字段，不能为空')
	);

}
?>