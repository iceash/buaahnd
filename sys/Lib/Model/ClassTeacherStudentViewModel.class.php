<?php 
class ClassTeacherStudentViewModel extends ViewModel {
	public $viewFields = array(
        'classstudent' => array('id','classid','student','studentname','idcard'),
        'class' => array('name','year','major','majore','majore','ctime','isbiye','_on'=>'class.id=classstudent.classid'),
        'classteacher' => array('teacher','_on' => 'classteacher.classid=class.id'),
    );

}
?>