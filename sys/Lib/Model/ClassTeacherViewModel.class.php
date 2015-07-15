<?php 
class ClassTeacherViewModel extends ViewModel {
	public $viewFields = array(
        'classteacher' => array('teacher'),
        'class' => array('id','name','year','ctime','isbiye','_on'=>'class.id=classteacher.classid')
    );

}
?>