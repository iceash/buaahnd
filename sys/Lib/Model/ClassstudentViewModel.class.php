<?php 
class ClassstudentViewModel extends ViewModel {
	public $viewFields = array(
        'classstudent' => array('id','classid','student','studentname','ename','enamesimple'),
        'class' => array('name','year','major','majore','majore','ctime','isbiye','_on'=>'class.id=classstudent.classid'),
        'enroll' => array('idcard','sex','birthday','_on' => 'enroll.username=classstudent.student'),
        // 'abroad' => array('passport','_on' =>'abroad.struename=classstudent.studentname')
    );

}
?>