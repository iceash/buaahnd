<?php
class HomeworkstudentViewModel extends ViewModel {
	public $viewFields = array(
        'homework' => array('id','homeworkid','susername','struename','score','ctime','content'),
        'classstudent' => array('classid', 'ename'=>'sename', '_on'=>'homework.susername=classstudent.student'),
        'class' => array('name','ename','year','_on'=>'classstudent.classid=class.id')
    );
} 

?>