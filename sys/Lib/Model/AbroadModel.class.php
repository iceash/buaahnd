<?php 
class AbroadModel extends Model {
	protected $_validate	 =	 array(
		array('struename','require','系统提示：带*为必填字段，不能为空')
	);

}
?>