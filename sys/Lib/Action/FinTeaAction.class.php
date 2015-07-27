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
        $mapPa['idcard']=$idcard;
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
        $dataD['operator']=session('truename');
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
        $project=$system->where('name="items"')->getField('content');
        $projectArr=explode(',',$project);
        $periodArr=M('period')->field('id')->select();
        $this->assign('periodList',$periodArr);
        $this->assign('project',$projectArr);
        $this->display();

    }
    public function exceladd(){
        $this->display();
    }
    public function payInsert() {
        $titlepic = $_POST['titlepic'];
        if (empty($titlepic)) {
            $this -> error('未上传文件');
        } 
        if (substr($titlepic,-3,3) !=='xls') {
            $this -> error('上传的不是xls文件');
        } 
        $php_path = dirname(__FILE__) . '/';
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $inputFileName = $php_path .'../../../..'.$titlepic;
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);     
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);        
        $count=count($sheetData);
        $arr = array('/'=>'-','０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',    
        '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',    
        'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',    
        'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',    
        'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',    
        'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',    
        'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',    
        'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',    
        'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',    
        'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',    
        'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',    
        'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',    
        'ｙ' => 'y', 'ｚ' => 'z',    
        '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',    
        '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',    
        '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',    
        '》' => '>',    
        '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',    
        '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',    
        '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',    
        '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',    
        '　' => ' ','＄'=>'$','＠'=>'@','＃'=>'#','＾'=>'^','＆'=>'&','＊'=>'*', 
        '＂'=>'"'); 
        $count = count($sheetData);//一共有多少行
        if ($count < 2) {
            $this->ajaxReturn($count, "请填写信息", 0);
        }
        for ($i=2; $i <=$count; $i++) {
            for ($j=1; $j <= 9; $j++) {
                if($j==6||$j==9){continue;}else{
                   if(strlen($sheetData[$i][chr(64+$j)])==0){
                    $errors[]=chr(64+$j).$i;
                    } 
                }    
            }//检查非空项
            $map['name']=$sheetData[$i]['A'];
            $feeid=M('fee')->where($map)->getField('id');
            if($feeid){
                $data_a[$i-2]['feeid']=$feeid;
                $data_a[$i-2]['feename']=$sheetData[$i]['A'];
            }
            $way=M('system')->where('name="paymode"')->getField('content');
            $wayArr=explode(',',$way);
            if(in_array($sheetData[$i]['B'],$wayArr)){
                $data_a[$i-2]['way'] = $sheetData[$i]['B'];
            }else{$errors[]='B'.$i;}
            if(is_numeric($sheetData[$i]['D'])){
                if($sheetData[$i]['C']=='收费'){
                    $data_a[$i-2]['money']=$sheetData[$i]['D'];
                }elseif ($sheetData[$i]['C']=='退费') {
                    $data_a[$i-2]['money']=-$sheetData[$i]['D'];
                }else{$errors[]='C'.$i;}
            }else{$errors[]='D'.$i;}
            $mapP['name']=$sheetData[$i]['E'];
            $feename1=M('payment')->where($mapP)->getField('feename');
            if($feename1==$sheetData[$i]['A']){
                $data_a[$i-2]['name']=$sheetData[$i]['E'];
            }else{$errors[]='E'.$i;}
            if(isset($sheetData[$i]['F'])){
                $map1['username']=$sheetData[$i]['F'];
                $name1=M('enroll')->where($map1)->getField('truename');
                if($name1==$sheetData[$i]['E']){
                    $data_a[$i-2]['stunum'] = $sheetData[$i]['F'];
                }else{$errors[]='F'.$i;}
            }
            $map2['idcard']=$sheetData[$i]['G'];
            $name2=M('enroll')->where($map2)->getField('truename');
            if($name2==$sheetData[$i]['E']){
                $data_a[$i-2]['idcard'] = $sheetData[$i]['G'];
            }else{$errors[]='G'.$i;}
            if(isDate($sheetData[$i]['H'])){
                $data_a[$i-2]['date'] = $sheetData[$i]['H'];
            }else{$errors[]='H'.$i;}
            $data_a[$i-2]['invoice'] = $sheetData[$i]['I'];
            $data_a[$i-2]['check'] = '审核中';
            $data_a[$i-2]['operator'] = session('truename');
            $data_a[$i-2]['date'] = date("Y-m-d");
        }
        if(count($errors) > 0){
            excelwarning($inputFileName,$errors);
            $this->ajaxReturn($titlepic,"信息不正确",0);
        }
        $dao=D('deal');
        $dao -> addAll($data_a);
        for ($k=0; $k <=$count; $k++) { 
            if($sheetData[$k]['C']=='收费'){$isRefund=0;}else{$isRefund=1;}
            $mapf['name']=$sheetData[$k]['A'];
            $feeid=M('fee')->where($mapf)->getField('id');
            $idcard=$sheetData[$k]['G'];
            updatePaymentStatus($isRefund,$feeid,$idcard);
        }
        $this -> success("已成功保存");

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