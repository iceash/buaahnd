<?php 
class CourseViewModel extends ViewModel {
    public $viewFields = array(
        'course' => array('id','name','ename','category2','credit','coursetime'),
        'class' => array('name'=>'classname','_on'=>'class.id=course.classid'),
        'major' => array('major'=>'major','item'=>'item','_on'=>'major.major=class.major')
    );
}
?>