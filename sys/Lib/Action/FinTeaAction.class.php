<?php
class FinTeaAction extends CommonAction{
    public function index() {
        $User = D('User');
        $map['username'] = session('username');
        $photo = $User -> where($map) -> getField('photo');
        $this -> assign('photo', $photo);
        $roles=explode(',',session('role'));
        if(count($roles)>1){
            $all_role=R('Index/getRole');
            $my_role=array();
            foreach($roles as $key=>$value){
                $my_role[$value]=$all_role[$value];
            }
            $this->assign('my_role',$my_role);
            $this->assign('select_role',$this -> getActionName());
        }
        $this -> display();
    }
    public function paylist(){
        if($_GET['num']!=0){
        $mapEn['id|idcard']=$_GET['num']; 
        $enroll=M('enroll');
        $name=$enroll->where($mapEn)->getField('truename');     
        if($name){
            $idcard=$enroll->where($mapEn)->getField('idcard');
            $stunum=$enroll->where($mapEn)->getField('id');
            $info="<span>姓名：</span><span id='name'>".$name."</span><span>学号：</span><span id='stunum'>".$stunum."</span><span>身份证号:</span><span id='idcard'>".$idcard."</span></tr>";        
            $this->assign('info',$info);
        }else{
            $this->assign('info','<span>该同学不存在</span>');
        }
        $mapPa['stunum|idcard']=$_GET['num'];
        $mapPa['period']=0;
        $payment=M('payment');
        $list=$payment->where($mapPa)->select();
        for ($i=0; $i <count($list);$i++) {
            $status=$list[$i]['status'];
            switch ($status) {
                case '0':
                    $statusname='未交费';
                    break;
                case '1':
                    $statusname='费用未交清';
                    break;
                case '2':
                    $statusname='已交齐费用';
                    break;
                case '3':
                    $statusname='退费';
                    break;
            }
            $list[$i]['statusname']=$statusname;
         }
        $way=M('system')->where('name="paymode"')->getField('content');
        $wayArr=explode(',',$way);
        $this->assign('way',$wayArr);
        $this->assign('list',$list);
        }
        $this->display();
    }
    public function pay(){
        $payment=M('payment');$deal=M('deal');
        $dataD['feeid']=$_POST['feeid'];
        $dataD['feename']=$_POST['feename'];
        $dataD['name']=$_POST['name'];
        $dataD['stunum']=$_POST['stunum'];
        $dataD['idcard']=$_POST['idcard'];
        $dataD['way']=$_POST['way'];
        $dataD['invoice']=$_POST['invoice'];
        $dataD['money']=$_POST['money'];
        $dataD['operator']="测试";
        if ($_POST['date']) {
           $dataD['date']=$_POST['date'];
        }else{$dataD['date']=date('Y-m-d');}
        $dataD['check']="审核中";
        $checkD=$deal->add($dataD);
        /**以上是对deal表新增一条收费记录，以下是对payment表修改**/
        $isRefund=0;$feeid=$_POST['feeid'];$idcard=$_POST['idcard'];
        $checkU=updatePaymentStatus($isRefund,$feeid,$idcard);
        if ($checkD&&$checkU) {
            $this->success("收费成功","json");
        }else{$this->error("收费出错");}
    }
    public function refund(){
       $payment=M('payment');$deal=M('deal');
        $dataD['feeid']=$_POST['feeid'];
        $dataD['feename']=$_POST['feename'];
        $dataD['name']=$_POST['name'];
        $dataD['stunum']=$_POST['stunum'];
        $dataD['idcard']=$_POST['idcard'];
        $dataD['way']=$_POST['way'];
        $dataD['money']=-$_POST['money'];
        $dataD['operator']="测试";
        if ($_POST['date']) {
           $dataD['date']=$_POST['date'];
        }else{$dataD['date']=date('Y-m-d');}
        $dataD['check']="审核中";
        $checkD=$deal->add($dataD);
        $isRefund=1;$feeid=$_POST['feeid'];$idcard=$_POST['idcard'];
        $checkU=updatePaymentStatus($isRefund,$feeid,$idcard);
        if ($checkD&&$checkU) {
            $this->success("退费成功","json");
        }else{$this->error("退费出错");}
    }

}