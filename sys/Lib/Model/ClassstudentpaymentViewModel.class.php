<?php 
class ClassstudentpaymentViewModel extends ViewModel {
	public $viewFields = array(
		'payment' => array('id','name','stunum','idcard','feename','feeid','standard','paid','status','period'),
		'classstudent'=>array('classid','_on'=>'classstudent.student=payment.stunum'),
		'class'=>array('name'=>'classname','major','_on'=>'class.id=classstudent.classid'),
		'fee'=>array('item','_on'=>'fee.id=payment.feeid')
    );

}
?>