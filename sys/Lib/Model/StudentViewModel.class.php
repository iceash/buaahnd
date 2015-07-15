<?php 
class StudentViewModel extends ViewModel {
	public $viewFields = array(
        'classteacher' => array('teacher'),
        'class' => array('id','name','year','major','majore','isbiye','_on'=>'classteacher.classid = class.id'),
        'classstudent' => array('student','studentname','ename','_on'=>'class.id=classstudent.classid','_type'=>'LEFT'),
        'enroll' => array('sex','birthday','idcard','address','plus','nativeprovince','nativecity','mobile','qq','email','entrancescore','entrancefull','englishscore','testscore','_on'=>'classstudent.student=enroll.username','_type'=>'LEFT'),
         'abroad' => array('country','score1','passport','_on'=>'enroll.username=abroad.susername','_type'=>'LEFT'),
         'abroadschool'=>array('school','major'=>'fmajor','enroll','together','_on'=>'abroad.id=abroadschool.abroadid')
    );
}
?>