<?php 
class CourseteacherViewModel extends ViewModel {
	public $viewFields = array(
        'courseteacher' => array('id','coursenumber','teacher','term','issub','issubb'),
        'teacher' => array('truename','ename'=>'tename','_on'=>'teacher.username=courseteacher.teacher'),
        'course' => array('name','ename','coursetime','credit','_on'=>'course.number=courseteacher.coursenumber')
    );

}
?>