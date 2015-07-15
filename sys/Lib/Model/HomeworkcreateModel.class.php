<?php 
class HomeworkcreateModel extends Model {
	protected $_validate	 =	 array(
		array('subjectid','require','系统提示：带*为必填字段，不能为空'),
		array('title','require','系统提示：带*为必填字段，不能为空'),
        array('content','require','系统提示：带*为必填字段，不能为空')
	);

}
?>