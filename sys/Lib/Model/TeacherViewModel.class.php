<?php
class TeacherViewModel extends ViewModel {
	public $viewFields = array(
        'classteacher' => array('teacher'),
        'user' => array('truename','_on'=>'user.username=classteacher.teacher')
    );
} 

?>