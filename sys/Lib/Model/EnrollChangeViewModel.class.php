<?php
class EnrollChangeViewModel extends ViewModel {
	public $viewFields = array(
        'enroll' => array('id','truename','sex','nationality','birthday','idcard','address','nativeprovince','nativecity','mobile','qq','email','counselorname','status','statustext','fill'),
        'enrollchange' => array('fromcounselor','fromcounselorname','tocounselor','tocounselorname','ctime','_on'=>'enroll.id=enrollchange.enrollid')
    );
} 

?>