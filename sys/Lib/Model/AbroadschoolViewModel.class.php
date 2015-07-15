<?php 
class AbroadschoolViewModel extends ViewModel {
	public $viewFields = array(
        'abroad' => array('id','status'=>'astatus','struename','from'=>'sfrom','passport','birth','score1','score2','score3','score4','score5','tusername1','tusername2','tusername3','tusername4','ctime','country'),
        'abroadschool' => array('abroadid','school','major','isenroll','enroll','_on'=>'abroad.id=abroadschool.abroadid')
    );

}
?>