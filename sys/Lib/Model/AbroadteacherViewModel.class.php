<?php 
class AbroadteacherViewModel extends ViewModel {
	public $viewFields = array(
        'abroad' => array('id','status','struename','tusername1','tusername2','tusername3','tusername4','ctime', 'finishtime'),
        'user' => array('truename','_on'=>'abroad.tusername4=user.username')
    );

}
?>