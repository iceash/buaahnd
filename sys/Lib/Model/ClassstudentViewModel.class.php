<?php 
class ClassstudentViewModel extends ViewModel {
	public $viewFields = array(
        'classstudent' => array('id','classid','student','studentname','ename','enamesimple'),
        'class' => array('name','year','major','majore','ctime','isbiye','_on'=>'class.id=classstudent.classid'),
        'enroll' => array('idcard','_on' => 'enroll.id=classstudent.student')
    );

}
?>