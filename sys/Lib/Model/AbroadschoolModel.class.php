<?php 
class AbroadschoolModel extends Model {
	protected $_validate	 =	 array(
		array('school','require','系统提示： 学校名称不能为空'),
	);

}
?>