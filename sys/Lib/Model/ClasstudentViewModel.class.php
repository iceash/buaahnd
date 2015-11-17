<?php 
class ClasstudentViewModel extends ViewModel {
	public $viewFields = array(
        'classstudent' => array('id','classid','student','idcard','studentname','ename'),
        'class' => array('name','year','_on'=>'class.id=classstudent.classid'),
        'enroll' => array('idcard','sex','scnid','birthday','_on' => 'enroll.username=classstudent.student'),

    );

}
?>