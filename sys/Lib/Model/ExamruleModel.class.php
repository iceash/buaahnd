<?php 
class ExamruleModel extends Model {
	protected $_validate	 =	 array(
		array('name','require','系统提示：带*为必填字段，不能为空')
	);

}
?>