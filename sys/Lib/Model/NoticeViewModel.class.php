<?php
class NoticeViewModel extends ViewModel {
	public $viewFields = array(
        'noticecreate' => array('title','content','tusername','ttruename','ctime','istop'),
        'notice' => array('id','noticeid','readusername','readtruename','readtime','_on'=>'notice.noticeid=noticecreate.id')
    );
} 

?>