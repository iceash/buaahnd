<?php 
class StatisticsViewModel extends ViewModel {
	public $viewFields = array(
        'deal' => array('feename','name','stunum','idcard','money','date','submitdate'),
        'fee' => array('type','standard','item','period','_on'=>'fee.name=deal.feename'),
        'payment' => array('feeid','idcard','status','_on'=>'payment.feename=deal.feename'),
        'enroll' => array('id','truename','majorname','_on' => 'enroll.idcard=deal.idcard')
    );

}
?>