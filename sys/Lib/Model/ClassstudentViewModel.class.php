<?php 
class ClassstudentViewModel extends ViewModel {
	public $viewFields = array(
        'classstudent' => array('id','classid','student','studentname','ename','enamesimple','house','room','cell','bed'),
        'class' => array('name','year','major','majore','ctime','isbiye','_on'=>'class.id=classstudent.classid'),
        'enroll' => array('idcard','sex','scnid','bankcard','onecard','mobile','birthday','_on' => 'enroll.username=classstudent.student'),
        // 'abroad' => array('passport','_on' =>'abroad.struename=classstudent.studentname')
    );

}
?>