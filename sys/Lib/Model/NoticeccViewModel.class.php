<?php
class NoticeccViewModel extends ViewModel {
	public $viewFields = array(
        'noticecreate' => array('title','content','tusername','ttruename','ctime','istop'),
        'noticecc' => array('id','noticeid','readusername','readtruename','readtime','_on'=>'noticecc.noticeid=noticecreate.id')
    );
} 

?>