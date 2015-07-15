<?php 
class EduteaClassViewModel extends ViewModel {
	public $viewFields = array(
        'score' => array('subjectid','susername', 'struename'),
        'classstudent' => array('classid','_on'=>'classstudent.student=score.susername'),
        'class' => array('name','ename','id','_on'=>'class.id=classstudent.classid'),
        'courseteacher' => array('teacher', 'issub', '_on'=>'courseteacher.id=score.subjectid')
    );

}
?>