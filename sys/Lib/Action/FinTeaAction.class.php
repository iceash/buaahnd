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
        if(isset($_GET['num'])){
        $mapEn['username|idcard']=$_GET['num']; 
        $enroll=M('enroll');
        $name=$enroll->where($mapEn)->getField('truename');
        if($name){
            $idcard=$enroll->where($mapEn)->getField('idcard');
            $stunum=$enroll->where($mapEn)->getField('username');
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
        $dataD['operator']=session('truename');
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
        $dataD['operator']=session('username');
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
    public function view(){
        $paymentV=D('ClassstudentpaymentView');$class=M('class');$system=M('system');
        if($_GET['major']){
            $map['major']=$_GET['major'];
            $mapcl['major']=$_GET['major'];
            $classList=$class->where($mapcl)->Field('name')->select();
        }
        if($_GET['class']){$map['classname']=$_GET['class'];}
        if($_GET['item']){
            $map['item']=$_GET['item'];
            $mapfe['item']=$_GET['item'];
            $mapfe['parent']=0;
            $feeList=M('fee')->where($mapfe)->select();
        }
        if($_GET['fee']){$map['feename']=$_GET['fee'];}
        if($_GET['status']){$map['status']=$_GET['status']-1;}
        if($_GET['period']){$map['period']=$_GET['period'];}
        if($_GET['name']){$map['name']=$_GET['name'];}
        if($_GET['stunum']){$map['stunum']=$_GET['stunum'];}
        if($_GET['idcard']){$map['idcard']=$_GET['idcard'];}
        $list=$paymentV->where($map)->order('name')->select();
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
        $this->assign('list',$list);
        $this->assign('classList',$classList);
        $this->assign('feeList',$feeList);
        $major=$class->group('major')->Field('major')->select();
        $this->assign('major',$major);
        $project=$system->where('name="project"')->getField('content');
        $projectArr=explode(',',$project);
        $periodArr=M('period')->field('id')->select();
        $this->assign('periodList',$periodArr);
        $this->assign('project',$projectArr);
        $this->display();

    }
    public function getClass(){
        $map['major']=$_POST['major'];
        $classname=M('class')->where($map)->Field('name')->select();
        $this->ajaxReturn($classname);   
    }
    public function getFee(){
        $map2['item']=$_POST['item'];
        $map2['parent']=0;
        $feename=M('fee')->where($map2)->Field('name')->select();
        $this->ajaxReturn($feename);
    }

}