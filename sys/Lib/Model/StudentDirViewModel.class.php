<?php 
class StudentDirViewModel extends ViewModel {
	public $viewFields = array(
        'classstudent' => array('student','studentname','classid'),
        'class' => array('year','name'=>'classname','_on'=>'class.id=classstudent.classid'),
        'classteacher' => array('teacher','_on'=>'class.id=classteacher.classid'),
        'user' => array('truename','_on'=>'user.username=classteacher.teacher')
    );

}
?>