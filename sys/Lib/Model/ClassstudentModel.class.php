<?php 
class ClassstudentModel extends Model {
    protected $_validate     =   array(
        array('student','','学号已经存在！学号必须是唯一的',0,'unique',3), 
        array('idcard','','身份证号码已经存在！身份证号码必须是唯一的',0,'unique',3), 
        array('idcard','require','系统提示：身份证号码为必填字段，不能为空'),
        array('classid','require','系统提示：班级为必填字段，不能为空'),
        array('student','require','系统提示：学号为必填字段，不能为空'),
        array('studentname','require','系统提示：学生姓名为必填字段，不能为空'),
        array('ename','require','系统提示：学生英文名为必填字段，不能为空')
    );

}
?>