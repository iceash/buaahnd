<?php 
class ClasstudentViewModel extends ViewModel {
	public $viewFields = array(
        'classstudent' => array('id','classid','studentname'),
        'class' => array('name','year','_on'=>'class.id=classstudent.classid'),


    );

}
?>