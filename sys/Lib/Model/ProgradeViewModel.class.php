<?php 
class ProgradeViewModel extends ViewModel {
	public $viewFields = array(
        'prograde' => array('id','term','stuname','stunum','examname','isrepair','letter','hundred','course'=>'courseename'),
        'classstudent' =>array('classid','_on'=>'classstudent.student=prograde.stunum'),
        'class' => array('name'=>'classname','major','majore','year','_on'=>'class.id=classstudent.classid'),
        'major' => array('item','_on'=>'class.major=major.major'),
        // 'enroll' => array('scnid',"_on"=>'enroll.username=classstudent.student'),
        // 'course' => array('name'=>'coursename','ename'=>'courseename','credit','_on'=>'course.classid=class.id and (course.name=prograde.course or course.ename=prograde.course)'),
    );

}
?>