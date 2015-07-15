<?php 
class TraintestModel extends Model {
	protected $_validate	 =	 array(
		array('traintime','require','系统提示：带*为必填字段，不能为空'),
		array('score','require','系统提示：带*为必填字段，不能为空')
	);

}
?>