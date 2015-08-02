<?php 
class ClassstudentpaymentViewModel extends ViewModel {
	public $viewFields = array(
		'payment' => array('id','name','stunum','idcard','feename','feeid','standard','paid','status','period'),
		'classstudent'=>array('classid','_on'=>'classstudent.idcard=payment.idcard'),
		'class'=>array('name'=>'classname','major','_on'=>'class.id=classstudent.classid'),
		'fee'=>array('item','type','_on'=>'fee.id=payment.feeid')
    );

}
?>