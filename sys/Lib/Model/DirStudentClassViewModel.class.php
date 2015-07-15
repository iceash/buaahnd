<?php 
class DirStudentClassViewModel extends ViewModel {
	public $viewFields = array(
        'classstudent' => array('student','studentname','ename','enamesimple','classid'),
        'class' => array('year','name'=>'classname','isbiye','_on'=>'class.id=classstudent.classid'),
        'classteacher' => array('teacher','_on'=>'class.id=classteacher.classid')
    );

}
?>