<?php
class HomeworkcourseViewModel extends ViewModel {
	public $viewFields = array(
        'homeworkcreate' => array('id', 'subjectid', 'title', 'ctime'),
        'course' => array('ename', '_on'=>'homeworkcreate.coursename=course.name'),
    );
} 

?>