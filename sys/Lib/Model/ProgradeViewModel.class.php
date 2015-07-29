<?php 
class ProgradeViewModel extends ViewModel {
	public $viewFields = array(
        'prograde' => array('id','term','stuname','stunum','examname','isrepair','letter','hundred'),
        'course' => array('name'=>'coursename','ename'=>'courseename','credit','_on'=>'course.name=prograde.course or course.ename=prograde.course'),
        'classstudent' =>array('classid','_on'=>'classstudent.student=prograde.stunum'),
        'class' => array('name'=>'classname','major','majore','year','_on'=>'class.id=classstudent.classid')
    );

}
?>