<?php
class EnrollRecordViewModel extends ViewModel {
	public $viewFields = array(
        'enroll' => array('id','truename','sex','nationality','birthday','idcard','address','nativeprovince','nativecity','schoolprovince','schoolcity','schoolname','mobile','qq','email','fill','counselor','counselorname','status','statustext','ctime'),
        'enrollrecord' => array('id'=>'rid','enrollid','content','ctime'=>'rtime','counselor','nexttime','_on'=>'enroll.id=enrollrecord.enrollid')
    );
} 

?>