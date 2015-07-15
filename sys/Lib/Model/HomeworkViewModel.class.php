<?php
class HomeworkViewModel extends ViewModel {
	public $viewFields = array(
        'homeworkcreate' => array('title','content','coursename','tusername','ttruename','ctime'),
        'homework' => array('id','homeworkid','correct','comment','susername','struename','score','content'=>'subcontent','ctime'=>'subtime','_on'=>'homework.homeworkid=homeworkcreate.id')
    );
} 

?>