<?php 
class PregradeViewModel extends ViewModel {
	public $viewFields = array(
        'pregrade' => array('id','stuname','stunum','examname','listening','reading','writing','speaking','total','isrequired'),
        // 'course' => array('name'=>'coursename','ename'=>'courseename','credit','_on'=>'course.name=prograde.course or course.ename=prograde.course'),
        'classstudent' =>array('classid','_on'=>'classstudent.student=pregrade.stunum'),
        'class' => array('name'=>'classname','major','majore','year','_on'=>'class.id=classstudent.classid')
    );

}
?>