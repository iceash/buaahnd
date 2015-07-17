<?php
class FinAdmAction extends CommonAction{
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

    public function sys() {
        $Sys = D("System");
        $map['category'] = 'fin';
        $count = $Sys -> where($map) -> count();
        if ($count > 0) {
            $my = $Sys -> where($map) -> order('id desc') -> select();
            $this -> assign('my', $my);
        }
        $this -> display();
    }
    public function saveSys() {
        $id = $_POST['id'];
        $content = $_POST['content'];
        if (empty($id) || empty($content)) {
            $this -> error('必填项不能为空');
        }
        $Sys = D("System");
        $data['id'] = $id;
        $data['content'] = $content;
        $result = $Sys -> save($data);
        if ($result > 0) {
            $this -> success('已成功保存');
        }
    }

    public function project() {
        $paytype=D('System')->where("category='fin' and name='paytype'")->getField("content");
        $types=explode(',',$paytype);
        if(count($types)>1){
            $this->assign('paytype',$types);
        }
        $this -> display();
    }
    public function intercalate(){
        header("Content-Type: text/html; charset=UTF-8");
        $oldall = M("fee")->where("period=0")->order("id")->select();
        $rebacks = M("reback")->where("period=0")->select();
        foreach ($oldall as $one) {
            $all[$one["id"]] = $one;
        }
        foreach ($rebacks as $reback) {
            if ($reback["type"] == 1) {
                $all[$reback["feeid"]]["rate"] .= " ".$reback["partner"].$reback["value"];
            }else{
                $all[$reback["feeid"]]["rate"] .= " ".$reback["partner"].$reback["value"]."%";
            }
        }
        foreach ($all as $num => $vn) {
            if ($vn["parent"] != 0) {
                if ($vn["rate"]) {
                    $all[$vn["parent"]]["rate"] .= $vn["rate"].";";
                }
                unset($all[$num]);
            }
        }
        $items = ["美帝一周游","英伦自由行","欧盟一日行"];//项目列表，这里定死;
        foreach ($items as $item => $va) {
            foreach ($all as $one => $vo) {
                if ($vo["item"] == $va) {
                    $fees[$va][$vo["id"]] = $vo;
                }
            }
        }
        $this->assign("fees",$fees);
        $partner = ["南邮","百度推广","青梦家","东方航空","阿里去啊"];//合作方列表，这里定死；
        $this->assign("partner",$partner);
        $this->display();
    }
    public function addfee(){
        $fee = $_POST["fee"];
        if (count($fee) == 1) {
            $feename = $fee[0]["name"];
        }else{
            $feename = explode("-",$fee[0]["name"])[0];
            for ($i = 0, $le = count($fee); $i < $le; $i++) { 
                $thename = explode("-",$fee[$i]["name"])[0];
                if ($thename != $feename) {
                    $this->ajaxReturn(0,"收费项名称不一致",0);
                }
            }
        }
        $map["name"] = array("like",$feename."%");
        $count = M("fee")->where($map)->count();
        if ($count > 0) {
            $this->ajaxReturn(0,"检测到收费项重名",0);
        }
        if (count($fee) > 1) {
            $fee[0]["haschildren"] = 1;
            $b = M("fee")->add($fee[0]);
            for ($i = 1; $i < count($fee); $i++) { 
                $fee[$i]["parent"] = $b;
                $childfee[$i - 1] = $fee[$i];
            }
            $b = M("fee")->addAll($childfee);
        }else{
            $b = M("fee")->add($fee[0]);
        }
        if($b) {
            $this->ajaxReturn($fee,"success",1);
        }
    }
    public function updatefee(){
        $fee = $_POST["fee"];
        if (count($fee) == 1) {
            $feename = $fee[0]["name"];
        }else{
            $feename = explode("-",$fee[0]["name"])[0];
            for ($i = 0, $le = count($fee); $i < $le; $i++) { 
                $thename = explode("-",$fee[$i]["name"])[0];
                if ($thename != $feename) {
                    $this->ajaxReturn(0,"收费项名称不一致",0);
                }
            }
        }
        $map["name"] = array("like",$feename."%");
        $map["period"] = 0;
        $willsave = M("fee")->where($map)->select();
        if (count($willsave)  == 0) {
            $this->ajaxReturn(0, "无此收费项！", 0);
        }
        foreach ($willsave as $num => $ws) {
            $ids[] = $ws["id"];
        }
        $b = 0;
        $b = M("fee")->where(array('id'=>array('in',$ids)))->delete();
        if (!$b) {
            $this->ajaxReturn($b,"删除旧收费项失败",0);
        }
        M("reback")->where(array('feeid'=>array('in',$ids)))->delete();
        $b = M("fee")->add($fee[0]);
        if (count($fee) > 1) {
            for ($i = 1; $i < count($fee); $i++) { 
                $fee[$i]["parent"] = $b;
                $childfee[$i - 1] = $fee[$i];
            }
            $b = M("fee")->addAll($childfee);
        }
        if($b) {
            $this->ajaxReturn($fee,"success",1);
        }else{
           $this->ajaxReturn($fee,"新增新收费项失败",0); 
        }
    }
    public function delfee(){
        $feename = $_POST["name"];
        $count = M("deal")->where(array('feename'=>array('like',$feename."%")))->count();
        if ($count > 0) {
            $this->ajaxReturn($count,"已有".$count."条交易记录，禁止删除",0);
        }
        if (M("fee")->where(array('name'=>array('like',$feename."%")))->delete()) {
            M("reback")->where(array('feename'=>array('like',$feename."%")))->delete();
            $this->ajaxReturn(1,"成功",1);
        }else{
            $this->ajaxReturn(0,"删除失败",0);
        }

    }
    public function feeinfo(){
        $map["id"] = $_POST["id"];
        $info = M("fee")->where($map)->find();
        $childfee = M("fee")->where("parent=".$map["id"])->order("id")->select();
        foreach ($childfee as $num => $vf) {
            $info["separte"][$num] = $info["separte"][$num - 1] ? $info["separte"][$num - 1] + $vf["standard"] : $vf["standard"] + 0;
        }
        $this->ajaxReturn($info,"success",0);
    }
    public function rebacklist(){
        $id = $_POST["id"];
        $map["feename"] = array("like",$_POST["name"]."%");
        $map["period"] = 0;
        $list = M("reback")->where($map)->select();
        $mbp["name"] = array("like",$_POST["name"]."%");
        $mbp["period"] = 0;
        $fees = M("fee")->where($mbp)->select();
        if (count($fees) > 1) {
            for ($i = 0; $i < count($fees); $i++) { 
                if ($fees[$i]["parent"] == 0) {
                    unset($fees[$i]);
                }
            }
        }
        foreach ($fees as $f) {
            $newlist[$f["name"]][] = [];
            foreach ($list as $value) {
                if ($f["name"] == $value["feename"]) {
                    $newlist[$f["name"]][] = $value;
                }
            }
            if (count($newlist[$f["name"]]) > 1){
                array_shift($newlist[$f["name"]]);
            }
        }
        $newlist["count"] = count($fees);
        $this->ajaxReturn($newlist,"reback list",1);
        
        
    }
    public function rebackdel(){
        $id = $_POST["id"];
        if (M("reback")->where("id=".$id)->delete()) {
            $this->ajaxReturn(0,"删除成功",1);
        }else{
            $this->ajaxReturn(0,"删除失败",0);
        }
    }
    public function rebacksave(){
        $fo = $_POST["reinfo"];
        $b = 1; $count = 0;
        // foreach ($info as $num => $fo) {
            if ($fo["status"] == "new") {
                unset($fo["status"]);
                $newlist = $fo;
                $map["name"] = $fo["feename"];
                $newlist["feeid"] = M("fee")->where($map)->getfield("id");
            }elseif ($fo["status"] == "update") {
                unset($fo["status"]);
                $updatelist = $fo;
                $tmp = M("reback")->where("id=".$fo["id"])->save($fo);
                if ($tmp) {
                    $count++;
                }
            }
        // }
        $tmp = M("reback")->add($newlist);
        if (!$tmp && $count == 0) {
            $this->ajaxReturn($newlist,"无更新",0);
        }
        $this->ajaxReturn($tmp,"更新成功",1);
    }
    public function bound(){
        $this->display();
    }
    public function boundLeft(){
        $items = ["美帝一周游","英伦自由行","欧盟一日行"];//项目列表，这里定死;
        $fees = M("fee")->where("period=0 and parent=0")->order("id")->select();
        foreach ($fees as $fee) {
            foreach ($items as $itemname) {
                if ($fee["item"] == $itemname) {
                    $all[$itemname][] = $fee;
                }
            }
        }
        $this->assign("all",$all);
        $this -> display();
    }
    public function boundRight(){
        $year = $_GET["year"] ? $_GET["year"] : date("Y");
        $id = $_GET["id"] ? $_GET["id"] : 0;
        $id = intval($id);
        if ($id) {
            $allstudent = D("ClassstudentView")->where('year='.$year)->select();
        }
        foreach ($allstudent as $one) {
            $stu["stunum"] = $one["student"];
            $stu["name"] = $one["studentname"];
            $stu["idcard"] = $one["idcard"];
            $all[$one["major"]][$one["name"]]["has"]++;
            $map["feeid"] = $id;
            $map["idcard"] = $one["idcard"];
            if (M("payment")->where($map)->count() > 0){
                $all[$one["major"]][$one["name"]]["num"]++;
            }
        }
        foreach ($all as $a => $major) {
            foreach ($major as $b => $class) {
                if ($class["num"] >= $class["has"]) {
                    $all[$a][$b]["bound"] = true;
                }else{
                    $all[$a][$b]["bound"] = false;
                }
            }
        }
        $this->assign("feeid",$id);
        $this->assign("theyear",$year);
        $this->assign("all",$all);
        $this->display();
        // dump($allstudent);
    }
    public function boundsave(){
        $addlist = $_POST["addlist"];
        $dellist = $_POST["dellist"];
        $feeid = $_POST["feeid"];
        if (count($dellist) > 0) {
            $delstu = D("ClassstudentView")->where(array("name"=>array("in",$dellist)))->select();
            foreach ($delstu as $delone) {
                $dels[] = $delone["idcard"];
            }
            $a = M("payment")->where(array("idcard"=>array("in",$dels)))->delete();
        }
        if (count($addlist) > 0) {
            $addstu = D("ClassstudentView")->where(array("name"=>array("in",$addlist)))->select();
            $feeinfo = M("fee")->where("id=".$feeid)->find();
            foreach ($addstu as $num => $addone) {
                $willadd[$num]["feeid"] = $feeinfo["id"];
                $willadd[$num]["feename"] = $feeinfo["name"];
                $willadd[$num]["name"] = $addone["studentname"];
                $willadd[$num]["stunum"] = $addone["student"];
                $willadd[$num]["idcard"] = $addone["idcard"];
                $willadd[$num]["standard"] = $feeinfo["standard"];
            }
            $b = M("payment")->addAll($willadd);
        }
        if (!$a && !$b) {
            $this->ajaxReturn(1,"无修改项",0);
            
        }
        $this->ajaxReturn(1,"成功",1);
    }
    public function verify(){
        $way=M('system')->where('name="paymode"')->getField('content');
        $wayArr=explode(',',$way);
        $this->assign('way',$wayArr);
        $deal=M('deal');
        $mapDe['period']=0;
        $mapDe['check']="审核中";
        import("ORG.Util.Page");
        $count= $deal->where($mapDe)->count();
        $Page= new Page($count,20);
        $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
        $show= $Page->show();
        $list = $deal->where($mapDe)->order('date desc,id desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }
    public function pass(){
        $deal=M('deal');
        $mapDe['id']=$_POST['id'];
        $data['check']="已审核";
        $check=$deal->where($mapDe)->save($data);
        if ($check) {$this->success("已通过审核");}else{$this->error("发生错误");}
    }
    public function passEdit(){
        $deal=M('deal');
        $mapDe['id']=$_POST['id'];
        $data['check']="已审核";
        $data['way']=$_POST['way'];
        $data['money']=$_POST['money'];
        $data['invoice']=$_POST['invoice'];
        $data['operator']=$_POST['operator'];
        $data['date']=$_POST['date'];
        $check=$deal->where($mapDe)->save($data);
        /**以上将修改后的数据更新deal表并更改审核状态**/
        /**以下重新计算该生在此收费项的总交费**/
        if ($_POST['money']<0) {$isRefund=1;}else{$isRefund=0;}
        $feeid=$deal->where($mapDe)->getField('feeid');
        $idcard=$deal->where($mapDe)->getField('idcard');
        updatePaymentStatus($isRefund,$feeid,$idcard);
        if ($check) {$this->success("已通过审核");}else{$this->error("发生错误");}
    }
    public function submit(){
        $deal=M('deal');
        $mapDe['period']=0;
        $mapDe['check']="已审核";
        import("ORG.Util.Page");
        $count= $deal->where($mapDe)->count();
        $Page= new Page($count,20);
        $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
        $show= $Page->show();
        $list = $deal->where($mapDe)->order('date desc,id desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }
    public function subGo(){
        $deal=M('deal');
        $id=$_POST['id'];
        $map['id']=array('in',$id);
        $dataD['submitdate']=$_POST['submitdate'];
        $dataD['Voucher']=$_POST['Voucher'];
        $dataD['tmpstorage']=$_POST['tmpstorage'];
        $dataD['tmpindex']=$_POST['tmpindex'];
        $dataD['singlemoney']=$_POST['singlemoney'];
        $dataD['check']='已提交';
        $check=$deal->where($map)->save($dataD);
        if ($check) {$this->success("提交成功");}else{$this->error("发生错误");}
    }
    public function audit(){
        $statistics=M('statistics');
        import("ORG.Util.Page");
        if($_GET['item']){$mapA['item']=$_GET['item'];}
        if($_GET['way']){$mapA['way']=$_GET['way'];}
        if($_GET['period']){$mapA['period']=$_GET['period'];}else{$mapA['period']='0';}
        if($_GET['check']){$mapA['check']=$_GET['check'];}else{$mapA['check']='已提交';}
        if($_GET['invoice']){$mapA['invoice']=$_GET['invoice'];}
        if($_GET['operator']){$mapA['operator']=$_GET['operator'];}
        if($_GET['name']){$mapA['name']=$_GET['name'];}
        if($_GET['stunum']){$mapA['stunum']=$_GET['stunum'];}
        if($_GET['idcard']){$mapA['idcard']=$_GET['idcard'];}
        if($_GET['Voucher']){$mapA['Voucher']=$_GET['Voucher'];}
        if($_GET['tmpstorage']){$mapA['tmpstorage']=$_GET['tmpstorage'];}
        if($_GET['tmpindex']){$mapA['tmpindex']=$_GET['tmpindex'];}
        if($_GET['datefrom']&&$_GET['dateto']){$mapA['date']=array(array('egt',$_GET['datefrom']),array('elt',$_GET['dateto']));}
        if($_GET['sbfrom']&&$_GET['sbto']){$mapA['submitdate']=array(array('egt',$_GET['sbfrom']),array('elt',$_GET['sbto']));}
        $count= $statistics->where($mapA)->count();
        $Page= new Page($count,20);
        $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
        $show= $Page->show();
        $list = $statistics->where($mapA)->order('date desc,id desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
        $this->assign('list',$list);
        $this->assign('page',$show);
        $way=M('system')->where('name="paymode"')->getField('content');
        $wayArr=explode(',',$way);
        $project=M('system')->where('name="project"')->getField('content');
        $projectArr=explode(',',$project);
        $periodArr=M('period')->field('id')->select();
        $this->assign('periodList',$periodArr);
        $this->assign('way',$wayArr);
        $this->assign('project',$projectArr);
        $this->display();
    }
    public function change(){
        $deal=M('deal');
        $map['id']=$_POST['id'];
        $idcard=$_POST['idcard'];
        $feeid=$_POST['feeid'];
        $money=$_POST['money'];
        $dataC['way']=$_POST['way'];
        $dataC['money']=$_POST['money'];
        $dataC['invoice']=$_POST['invoice'];
        $dataC['operator']=$_POST['operator'];
        $dataC['date']=$_POST['date'];
        $dataC['submitdate']=$_POST['submitdate'];
        $dataC['Voucher']=$_POST['Voucher'];
        $dataC['tmpstorage']=$_POST['tmpstorage'];
        $dataC['tmpindex']=$_POST['tmpindex'];
        $dataC['singlemoney']=$_POST['singlemoney'];
        $checkD=$deal->where($map)->save($dataC);
        if ($_POST['money']<0) {$isRefund=1;}else{$isRefund=0;}
        updatePaymentStatus($isRefund,$feeid,$idcard);
        if ($checkD) {$this->success("修改成功");}else{$this->error("数据没有变化");}
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
        public function stat(){
      $mapEn=$_GET['searchkey'];
      $mapFn=$_GET['searchtype'];
      $item =$_GET['item'];
      $grade =$_GET['grade'];
      $classes =$_GET['classes'];
      $majorname =$_GET['majorname'];
      $period =$_GET['period'];
      $status1 =$_GET['status1'];
      if($item){$where['item']  = array('like','%'.$item.'%');}
      if($grade){$where['grade']  = array('like','%'.$grade.'%');}
      if($classes){$where['classes']  = array('like','%'.$classes.'%');}
      if($majorname){$where['majorname']  = array('like','%'.$majorname.'%');}
      if($_GET['datefrom']&&$_GET['dateto']){$where['date']=array(array('egt',$_GET['datefrom']),array('elt',$_GET['dateto']));}
      if($_GET['sbfrom']&&$_GET['sbto']){$where['submitdate']=array(array('egt',$_GET['sbfrom']),array('elt',$_GET['sbto']));}
      $where['period']=0;
      $where['check']='已提交';
      if($period){$where['period']  = $period;}
      if($status1){$where['status']  = $status1-1;}
      $Form  =  M('statistics');

      if(!$_GET['searchkey']){
      import("ORG.Util.Page");
      $count=  $Form ->where($where) ->count();
      $Page= new Page($count,20);
        $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
        $show= $Page->show();
        $Model =  $Form ->where($where)->order('date desc,id desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 

      for ($i=0; $i <count($Model);$i++) {
      $status=$Model[$i]['status'];
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
        $Model[$i]['statusname']=$statusname;
       }
      foreach ($Model as $mo => $va) {
        $Model[$mo]["standard"] = doubleval($Model[$mo]["standard"]);
        $Model[$mo]["paid"] = doubleval($Model[$mo]["paid"]);
        $Model[$mo]["needpay"] = $Model[$mo]["standard"] - $Model[$mo]["paid"];
        //dump($Model[$mo]);
      };
        $this->assign('grade',$grade);
        $this->assign('item',$item);
        $this->assign('classes',$classes);
        $this->assign('majorname',$majorname);
        $this->assign('period',$period);
        $this->assign('status1',$status1);
        $this->assign('list',$Model);
        $this->assign('page',$show);

      }
      else{
        switch ($mapFn) {
          case '1':
          import("ORG.Util.Page");
          $count=  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND enroll.truename ="'.$mapEn.'"')->order('date desc')->count();
          $Page= new Page($count,20);
          $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
          $show= $Page->show();
          $Model =  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND enroll.truename ="'.$mapEn.'"')->order('date desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
            break;
          case '2':
          import("ORG.Util.Page");
          $count=  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND deal.stunum='.$mapEn)->order('date desc')->count();
          $Page= new Page($count,20);
          $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
          $show= $Page->show();
          $Model =  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND deal.stunum='.$mapEn)->order('date desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
            break;
          case '3':
          import("ORG.Util.Page");
          $count=  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND deal.idcard='.$mapEn)->order('date desc')->count();
          $Page= new Page($count,20);
          $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
          $show= $Page->show();
          $Model =  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND deal.idcard='.$mapEn)->order('date desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
            break;
        }
      
      
      }
      //dump($Model);
      for ($i=0; $i <count($Model);$i++) {
            $status=$Model[$i]['status'];
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
            $Model[$i]['statusname']=$statusname;
         }
      foreach ($Model as $mo => $va) {
        $Model[$mo]["standard"] = doubleval($Model[$mo]["standard"]);
        $Model[$mo]["paid"] = doubleval($Model[$mo]["paid"]);
        $Model[$mo]["needpay"] = $Model[$mo]["standard"] - $Model[$mo]["paid"];
        //dump($Model[$mo]);
      };
      
      if($Model[0]["idcard"]==0){
        $this->assign('thead','该同学不存在!');
      }
        else{        
        $this->assign('list',$Model);
        $this->assign('page',$show);
        }// 模板变量赋值
        
      

        $this->display();


    }
    public function download()
    {
    Vendor('PHPExcel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getActiveSheet()->mergeCells('A1:S1'); 
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
    $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(22); //设置行高
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15); 
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10); 
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(9); 
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(9);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10); 
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(11); 
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20); 
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10); 
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10); 
    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(16);  
    //设置宽
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
    $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
    $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
    $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('S')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
    $objPHPExcel->getActiveSheet()->getStyle('A2:S2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A2:S2')->getFill()->getStartColor()->setARGB('FFF0FFF0');
    $objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode('0');
    $objPHPExcel->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode('0');
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont() ->setSize(20);
    $list=M("statistics");
    $rs=downloads();
    for ($i=0; $i <count($rs);$i++) {
      $status=$rs[$i]['status'];
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
        $rs[$i]['statusname']=$statusname;
       }
    $i=3;
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '交易记录统计表') 
                ->setCellValue('A2', '学员姓名')
                ->setCellValue('B2', '学号')
                ->setCellValue('C2', '身份证号')
                ->setCellValue('D2', '项目')
                ->setCellValue('E2', '收费项')
                ->setCellValue('F2', '年级')
                ->setCellValue('G2', '预科班次')
                ->setCellValue('H2', '专业')
                ->setCellValue('I2', '班次')
                ->setCellValue('J2', '课程')
                ->setCellValue('K2', '收费类别')
                ->setCellValue('L2', '交费日期')
                ->setCellValue('M2', '交费金额')
                ->setCellValue('N2', '发票号')
                ->setCellValue('O2', '交费状态')
                ->setCellValue('P2', '总应交费')
                ->setCellValue('Q2', '已交费')
                ->setCellValue('R2', '总欠费')
                ->setCellValue('S2', '校财务交费日期');
    $objPHPExcel->setActiveSheetIndex(0);
    foreach($rs as $k=>$v){
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $v['name'])
                ->setCellValue('B'.$i, $v['stunum'])
                ->setCellValue('C'.$i, $v['idcard'])
                ->setCellValue('D'.$i, $v['item'])
                ->setCellValue('E'.$i, $v['feename'])
                ->setCellValue('F'.$i, $v['grade'])
                ->setCellValue('G'.$i, $v['yukeclass'])
                ->setCellValue('H'.$i, $v['majorname'])
                ->setCellValue('I'.$i, $v['classes'])
                ->setCellValue('J'.$i, $v['subject'])
                ->setCellValue('K'.$i, $v['type'])
                ->setCellValue('L'.$i, $v['date'])
                ->setCellValue('M'.$i, $v['money'])
                ->setCellValue('N'.$i, $v['invoice'])
                ->setCellValue('O'.$i, $v['statusname'])
                ->setCellValue('P'.$i, $v['standard'])
                ->setCellValue('Q'.$i, $v['paid'])
                ->setCellValue('R'.$i, $v['needpay'])
                ->setCellValue('S'.$i, $v['submitdate']);
    $i++;
    }
    $objPHPExcel->getActiveSheet()->setTitle('sheet1');//设置sheet标签的名称
    $objPHPExcel->setActiveSheetIndex(0);
    ob_end_clean();  //清空缓存 
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");
    header('Content-Disposition:attachment;filename=交易记录统计表.xls');//设置文件的名称
    header("Content-Transfer-Encoding:binary");
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
    }
    public function period(){
      $Period =$_GET['period'];
      $A = M('periodall');
      $B =M('period');
      $period =M('period');
      $lastperiodid =$Period -1;
      $lastperiod=$period->where('id='.$lastperiodid)->find();
      $periodid = $B->select();
      if($Period){
      $dato   =   $A->where('getorgive = 0 AND period = '.$Period)->select();
      $dato2   =  $A->where('getorgive = 1 AND period = '.$Period)->select();
      
      foreach ($dato as $mo => $va) {
        $list=explode(',', $dato[$mo]["returns"]);
        $dato[$mo]["part1"] = $list[0];
        $dato[$mo]["part2"] = $list[1];
        $dato[$mo]["part3"] = $list[2];
        $dato[$mo]["part4"] = $list[3];
        $dato[$mo]["part5"] = $list[4];
      };
      foreach ($dato as $mo => $va) {
        $dato[$mo]["give"] = doubleval($dato[$mo]["give"]);
        $dato[$mo]["gets"] = doubleval($dato[$mo]["gets"]);
        $dato[$mo]["realincome"] = $dato[$mo]["gets"] + $dato[$mo]["give"];
      };

      foreach ($dato2 as $mo => $va) {
        $list=explode(',', $dato2[$mo]["returns"]);
        $dato2[$mo]["part1"] = $list[0];
        $dato2[$mo]["part2"] = $list[1];
        $dato2[$mo]["part3"] = $list[2];
        $dato2[$mo]["part4"] = $list[3];
        $dato2[$mo]["part5"] = $list[4];
      };
      foreach ($dato2 as $mo => $va) {
        $dato2[$mo]["give"] = doubleval($dato2[$mo]["give"]);
        $dato2[$mo]["gets"] = doubleval($dato2[$mo]["gets"]);
        $dato2[$mo]["realincome"] = $dato2[$mo]["gets"] + $dato2[$mo]["give"];
        //dump($dato[$mo]);
        
      };
      $this ->assign('lastperiod',$lastperiod);
      $this->data2 =  $dato2;
      $this->periodid =$periodid;
      $this->data =  $dato;
    }
    else{

    $reback=M('reback');
    $deal=M('deal');
    $fee=M('fee');
    $period =M('period');
    $lastperiod=$period->order('id desc')->find();
    
    $mapU['period']=0;
    $mapU['haschildren']=0;
    $data=$fee->where($mapU)->Field('id,name')->select();
    $data2=$data;
    for ($i=0; $i < count($data); $i++) { 
      $mapV['period']=0;
      $mapV['money']=array('gt',0);
      $mapV['feename']=$data[$i]['name'];
      $allPay=$deal->where($mapV)->field('money')->select();
      $sum=0;
      for ($j=0; $j < count($allPay); $j++) { 
        $sum+=$allPay[$j]['money'];
      }
      $data[$i]['feename']=$data[$i]['name'];
      $data[$i]['gets']=$sum;
      $data[$i]['give']=0;
    }
    for ($a=0; $a < count($data); $a++) { 
      $mapP['feename']=$data[$a]['name'];
      $mapP['partner']='南邮';        
      $middle=$reback->where($mapP)->select();
      $type = $middle[0]['type'];
        switch ($type) {
          case '0':
            $data[$a]['part1']=$data[$a]['gets']*$middle[0]['value'];
            break;
          case '1':
            $data[$a]['part1']=$middle[0]['value'];
            break;
          default:
            $otherid =intval($middle[0]['otherid'])-1;

            $data[$a]['part1']=$middle[0]['value']*$data[$otherid]['gets'];
            break;
          }
      $mapP1['feename']=$data[$a]['name'];
      $mapP1['partner']='百度推广';        
      $middle1=$reback->where($mapP1)->select();
      $type = $middle1[0]['type'];
        switch ($type) {
          case '0':
            $data[$a]['part2']=$data[$a]['gets']*$middle1[0]['value'];
            break;
          case '1':
            $data[$a]['part2']=$middle1[0]['value'];
            break;
          default:
           $otherid =intval($middle1[0]['otherid'])-1;

            $data[$a]['part2']=$middle1[0]['value']*$data[$otherid]['gets'];
            break;      
        }
      $mapP2['feename']=$data[$a]['name'];
      $mapP2['partner']='阿里去啊';        
      $middle2=$reback->where($mapP2)->select();
      $type = $middle2[0]['type'];
        switch ($type) {
          case '0':
            $data[$a]['part3']=$data[$a]['gets']*$middle2[0]['value'];
            break;
          case '1':
            $data[$a]['part3']=$middle2[0]['value'];
            break;
          default:
            $otherid =intval($middle2[0]['otherid'])-1;

            $data[$a]['part3']=$middle2[0]['value']*$data[$otherid]['gets'];
            break;      
        }
      $mapP3['feename']=$data[$a]['name'];
      $mapP3['partner']='东方航空';        
      $middle3=$reback->where($mapP3)->select();
      $type = $middle3[0]['type'];
        switch ($type) {
          case '0':
            $data[$a]['part4']=$data[$a]['gets']*$middle3[0]['value'];
            break;
          case '1':
            $data[$a]['part4']=$middle3[0]['value'];
            break;
          default:
            $otherid =intval($middle3[0]['otherid'])-1;

            $data[$a]['part4']=$middle3[0]['value']*$data[$otherid]['gets'];
            break;        
        } 
        $mapP4['feename']=$data[$a]['name'];
        $mapP4['partner']='青梦家';        
        $middle4=$reback->where($mapP4)->select();
        $type = $middle4[0]['type'];
        switch ($type) {
          case '0':
            $data[$a]['part5']=$data[$a]['gets']*$middle4[0]['value'];
            break;
          case '1':
            $data[$a]['part5']=$middle4[0]['value'];
            break;
          default:
            $otherid =intval($middle4[0]['otherid'])-1;

            $data[$a]['part5']=$middle4[0]['value']*$data[$otherid]['gets'];
            break;      
        }


    }
    foreach ($data as $mo => $va) {
            $data[$mo]["give"] = doubleval($data[$mo]["give"]);
            $data[$mo]["gets"] = doubleval($data[$mo]["gets"]);
            $data[$mo]["realincome"] = $data[$mo]["gets"] + $data[$mo]["give"];
    };
    for ($i=0; $i < count($data2); $i++) { 
      $mapV['period']=0;
      $mapV['money']=array('lt',0);
      $mapV['feename']=$data2[$i]['name'];
      $allPay=$deal->where($mapV)->field('money')->select();
      $sum=0;
      for ($j=0; $j < count($allPay); $j++) { 
        $sum+=$allPay[$j]['money'];
      }
      $data2[$i]['feename']=$data2[$i]['name'];
      $data2[$i]['give']=$sum;
      $data2[$i]['gets']=0;
    }
    for ($a=0; $a < count($data2); $a++) { 
      $mapP['feename']=$data2[$a]['name'];
      $mapP['partner']='南邮';        
      $middle=$reback->where($mapP)->select();
      $type = $middle[0]['type'];
        switch ($type) {
          case '0':
            $data2[$a]['part1']=$data2[$a]['give']*$middle[0]['value'];
            break;
          case '1':
            $data2[$a]['part1']=0;
            break;
          default:
            $otherid =intval($middle[0]['otherid'])-1;
            $data2[$a]['part1']=$middle[0]['value']*$data2[$otherid]['give'];
            break;
          }
      $mapP1['feename']=$data2[$a]['name'];
      $mapP1['partner']='百度推广';        
      $middle1=$reback->where($mapP1)->select();
      $type = $middle1[0]['type'];
        switch ($type) {
          case '0':
            $data2[$a]['part2']=$data2[$a]['give']*$middle1[0]['value'];
            break;
          case '1':
            $data2[$a]['part2']=0;
            break;
          default:
           $otherid =intval($middle1[0]['otherid'])-1;
            $data2[$a]['part2']=$middle1[0]['value']*$data2[$otherid]['give'];
            break;      
        }
      $mapP2['feename']=$data2[$a]['name'];
      $mapP2['partner']='阿里去啊';        
      $middle2=$reback->where($mapP2)->select();
      $type = $middle2[0]['type'];
        switch ($type) {
          case '0':
            $data2[$a]['part3']=$data2[$a]['give']*$middle2[0]['value'];
            break;
          case '1':
            $data2[$a]['part3']=0;
            break;
          default:
            $otherid =intval($middle2[0]['otherid'])-1;
            $data2[$a]['part3']=$middle2[0]['value']*$data2[$otherid]['give'];
            break;      
        }
      $mapP3['feename']=$data2[$a]['name'];
      $mapP3['partner']='东方航空';        
      $middle3=$reback->where($mapP3)->select();
      $type = $middle3[0]['type'];
        switch ($type) {
          case '0':
            $data2[$a]['part4']=$data2[$a]['give']*$middle3[0]['value'];
            break;
          case '1':
            $data2[$a]['part4']=0;
            break;
          default:
            $otherid =intval($middle3[0]['otherid'])-1;
            $data2[$a]['part4']=$middle3[0]['value']*$data2[$otherid]['give'];
            break;        
        } 
        $mapP4['feename']=$data2[$a]['name'];
        $mapP4['partner']='青梦家';        
        $middle4=$reback->where($mapP4)->select();
        $type = $middle4[0]['type'];
        switch ($type) {
          case '0':
            $data2[$a]['part5']=$data2[$a]['give']*$middle4[0]['value'];
            break;
          case '1':
            $data2[$a]['part5']=0;
            break;
          default:
            $otherid =intval($middle4[0]['otherid'])-1;
            $data2[$a]['part5']=$middle4[0]['value']*$data2[$otherid]['give'];
            break;      
        }
    }
    foreach ($data2 as $mo => $va) {
        $data2[$mo]["give"] = doubleval($data2[$mo]["give"]);
        $data2[$mo]["gets"] = doubleval($data2[$mo]["gets"]);
        $data2[$mo]["realincome"] = $data2[$mo]["gets"] + $data2[$mo]["give"];
      };
      $data[0]['startdate']= $lastperiod['enddate'];
      $data[0]['enddate']= date('Y-m-d');
      $this ->assign('lastperiod',$lastperiod);
      $this ->assign('data2',$data2);
      $this ->assign('data',$data);
    }
      $this ->assign('choose',$Period);
      $this->periodid =$periodid;
      $this ->display(period);
    }    
    public function endperiod(){
      
      $A = M('periodall');
      $B =M('period');
      $period =M('period');
      $lastperiod=$B->order('id desc')->find();
      $lastperiodid=$lastperiod['id'];
      $periodid = $B->select();  
      $reback=M('reback');
      $deal=M('deal');
      $fee=M('fee');
      $period =M('period');
      $lastperiod=$period->order('id desc')->find();
      $mapU['period']=0;
      $mapU['haschildren']=0;
      $data=$fee->where($mapU)->Field('id,name')->select();
      $data2=$data;
       for ($i=0; $i < count($data); $i++) { 
          $mapV['period']=0;
          $mapV['money']=array('gt',0);
          $mapV['feename']=$data[$i]['name'];
          $allPay=$deal->where($mapV)->field('money')->select();

          $sum=0;
          for ($j=0; $j < count($allPay); $j++) { 
            $sum+=$allPay[$j]['money'];
          }
          $data[$i]['feename']=$data[$i]['name'];
          $data[$i]['gets']=$sum;
          $data[$i]['give']=0;
        }
        
      for ($a=0; $a < count($data); $a++) { 
        $mapP['feename']=$data[$a]['name'];
        $mapP['partner']='南邮';        
        $middle=$reback->where($mapP)->select();
        $type = $middle[0]['type'];
          switch ($type) {
            case '0':
              $data[$a]['part1']=$data[$a]['gets']*$middle[0]['value'];
              break;
            case '1':
              $data[$a]['part1']=$middle[0]['value'];
              break;
            default:
              $otherid =intval($middle[0]['otherid'])-1;
              $data[$a]['part1']=$middle[0]['value']*$data[$otherid]['gets'];
              break;
            }
        $mapP1['feename']=$data[$a]['name'];
        $mapP1['partner']='百度推广';        
        $middle1=$reback->where($mapP1)->select();
        $type = $middle1[0]['type'];
          switch ($type) {
            case '0':
              $data[$a]['part2']=$data[$a]['gets']*$middle1[0]['value'];
              break;
            case '1':
              $data[$a]['part2']=$middle1[0]['value'];
              break;
            default:
             $otherid =intval($middle1[0]['otherid'])-1;
              $data[$a]['part2']=$middle1[0]['value']*$data[$otherid]['gets'];
              break;      
          }
        $mapP2['feename']=$data[$a]['name'];
        $mapP2['partner']='阿里去啊';        
        $middle2=$reback->where($mapP2)->select();
        $type = $middle2[0]['type'];
          switch ($type) {
            case '0':
              $data[$a]['part3']=$data[$a]['gets']*$middle2[0]['value'];
              break;
            case '1':
              $data[$a]['part3']=$middle2[0]['value'];
              break;
            default:
              $otherid =intval($middle2[0]['otherid'])-1;
              $data[$a]['part3']=$middle2[0]['value']*$data[$otherid]['gets'];
              break;      
          }
        $mapP3['feename']=$data[$a]['name'];
        $mapP3['partner']='东方航空';        
        $middle3=$reback->where($mapP3)->select();
        $type = $middle3[0]['type'];
          switch ($type) {
            case '0':
              $data[$a]['part4']=$data[$a]['gets']*$middle3[0]['value'];
              break;
            case '1':
              $data[$a]['part4']=$middle3[0]['value'];
              break;
            default:
              $otherid =intval($middle3[0]['otherid'])-1;
              $data[$a]['part4']=$middle3[0]['value']*$data[$otherid]['gets'];
              break;        
          } 
          $mapP4['feename']=$data[$a]['name'];
          $mapP4['partner']='青梦家';        
          $middle4=$reback->where($mapP4)->select();
          $type = $middle4[0]['type'];
          switch ($type) {
            case '0':
              $data[$a]['part5']=$data[$a]['gets']*$middle4[0]['value'];
              break;
            case '1':
              $data[$a]['part5']=$middle4[0]['value'];
              break;
            default:
              $otherid =intval($middle4[0]['otherid'])-1;
              $data[$a]['part5']=$middle4[0]['value']*$data[$otherid]['gets'];
              break;      
          }
      }
        foreach ($data as $mo => $va) {
                $data[$mo]["give"] = doubleval($data[$mo]["give"]);
                $data[$mo]["gets"] = doubleval($data[$mo]["gets"]);
                $data[$mo]["realincome"] = $data[$mo]["gets"] + $data[$mo]["give"];
        };
      for ($i=0; $i < count($data2); $i++) { 
        $mapV['period']=0;
        $mapV['money']=array('lt',0);
        $mapV['feename']=$data2[$i]['name'];
        $allPay=$deal->where($mapV)->field('money')->select();
        $sum=0;
        for ($j=0; $j < count($allPay); $j++) { 
          $sum+=$allPay[$j]['money'];
        }
        $data2[$i]['feename']=$data2[$i]['name'];
        $data2[$i]['give']=$sum;
        $data2[$i]['gets']=0;
      }
      for ($a=0; $a < count($data2); $a++) { 
        $mapP['feename']=$data2[$a]['name'];
        $mapP['partner']='南邮';        
        $middle=$reback->where($mapP)->select();
        $type = $middle[0]['type'];
          switch ($type) {
            case '0':
              $data2[$a]['part1']=$data2[$a]['give']*$middle[0]['value'];
              break;
            case '1':
              $data2[$a]['part1']=0;
              break;
            default:
              $otherid =intval($middle[0]['otherid'])-1;
              $data2[$a]['part1']=$middle[0]['value']*$data2[$otherid]['give'];
              break;
            }
        $mapP1['feename']=$data2[$a]['name'];
        $mapP1['partner']='百度推广';        
        $middle1=$reback->where($mapP1)->select();
        $type = $middle1[0]['type'];
          switch ($type) {
            case '0':
              $data2[$a]['part2']=$data2[$a]['give']*$middle1[0]['value'];
              break;
            case '1':
              $data2[$a]['part2']=0;
              break;
            default:
             $otherid =intval($middle1[0]['otherid'])-1;
              $data2[$a]['part2']=$middle1[0]['value']*$data2[$otherid]['give'];
              break;      
          }
        $mapP2['feename']=$data2[$a]['name'];
        $mapP2['partner']='阿里去啊';        
        $middle2=$reback->where($mapP2)->select();
        $type = $middle2[0]['type'];
          switch ($type) {
            case '0':
              $data2[$a]['part3']=$data2[$a]['give']*$middle2[0]['value'];
              break;
            case '1':
              $data2[$a]['part3']=0;
              break;
            default:
              $otherid =intval($middle2[0]['otherid'])-1;
              $data2[$a]['part3']=$middle2[0]['value']*$data2[$otherid]['give'];
              break;      
          }
        $mapP3['feename']=$data2[$a]['name'];
        $mapP3['partner']='东方航空';        
        $middle3=$reback->where($mapP3)->select();
        $type = $middle3[0]['type'];
          switch ($type) {
            case '0':
              $data2[$a]['part4']=$data2[$a]['give']*$middle3[0]['value'];
              break;
            case '1':
              $data2[$a]['part4']=0;
              break;
            default:
              $otherid =intval($middle3[0]['otherid'])-1;
              $data2[$a]['part4']=$middle3[0]['value']*$data2[$otherid]['give'];
              break;        
          } 
          $mapP4['feename']=$data2[$a]['name'];
          $mapP4['partner']='青梦家';        
          $middle4=$reback->where($mapP4)->select();
          $type = $middle4[0]['type'];
          switch ($type) {
            case '0':
              $data2[$a]['part5']=$data2[$a]['give']*$middle4[0]['value'];
              break;
            case '1':
              $data2[$a]['part5']=0;
              break;
            default:
              $otherid =intval($middle4[0]['otherid'])-1;
              $data2[$a]['part5']=$middle4[0]['value']*$data2[$otherid]['give'];
              break;      
          }
        }
        foreach ($data2 as $mo => $va) {
            $data2[$mo]["give"] = doubleval($data2[$mo]["give"]);
            $data2[$mo]["gets"] = doubleval($data2[$mo]["gets"]);
            $data2[$mo]["realincome"] = $data2[$mo]["gets"] + $data2[$mo]["give"];
          };
        $data[0]['startdate']= $lastperiod['enddate'];
        $data[0]['enddate']= date('Y-m-d');
        for ($h=0; $h <count($data) ; $h++) { 
          $newstatics[$h]['feename']=$data[$h]['feename'];
          $newstatics[$h]['gets']=$data[$h]['gets'];
          $newstatics[$h]['give']=$data[$h]['give'];
          $newstatics[$h]['partners']='南邮-百度推广-阿里去啊-东方航空-青梦家';
          $newstatics[$h]['returns']=implode(",",array($data[$h]['part1'],$data[$h]['part2'],$data[$h]['part3'],$data[$h]['part4'],$data[$h]['part5']));
          $newstatics[$h]['period']=$lastperiodid+1;
          $newstatics[$h]['getorgive']=0;
        }
        for ($h1=0; $h1 <count($data2) ; $h1++) { 
          $newstatics2[$h1]['feename']=$data2[$h1]['feename'];
          $newstatics2[$h1]['gets']=$data2[$h1]['gets'];
          $newstatics2[$h1]['give']=$data2[$h1]['give'];
          $newstatics2[$h1]['partners']='南邮-百度推广-阿里去啊-东方航空-青梦家';
          $newstatics2[$h1]['returns']=implode(",",array($data2[$h1]['part1'],$data2[$h1]['part2'],$data2[$h1]['part3'],$data2[$h1]['part4'],$data2[$h1]['part5']));
          $newstatics2[$h1]['period']=$lastperiodid+1;
          $newstatics2[$h1]['getorgive']=1;
        }
        $add = M('statics') -> addall($newstatics);
        $add2 = M('statics') -> addall($newstatics2);
        $newperiod['startdate']=$lastperiod['enddate'];
        $newperiod['enddate']=date('Y-m-d');
        for ($iforgive=0; $iforgive < count($data2); $iforgive++) { 
          $gets[$iforgive]=$data2[$iforgive]['gets'];
          $give[$iforgive]=$data2[$iforgive]['give'];
          $realincome[$iforgive]=$data2[$iforgive]['realincome'];
          $part1[$iforgive]=$data2[$iforgive]['part1'];
          $part2[$iforgive]=$data2[$iforgive]['part2'];
          $part3[$iforgive]=$data2[$iforgive]['part3'];
          $part4[$iforgive]=$data2[$iforgive]['part4'];
          $part5[$iforgive]=$data2[$iforgive]['part5'];

        }
        for ($iforgive1=0; $iforgive1 < count($data); $iforgive1++) { 
          $gets1[$iforgive1]=$data[$iforgive1]['gets'];
          $give1[$iforgive1]=$data[$iforgive1]['give'];
          $realincome1[$iforgive1]=$data[$iforgive1]['realincome'];
          $part11[$iforgive1]=$data[$iforgive1]['part1'];
          $part21[$iforgive1]=$data[$iforgive1]['part2'];
          $part31[$iforgive1]=$data[$iforgive1]['part3'];
          $part41[$iforgive1]=$data[$iforgive1]['part4'];
          $part51[$iforgive1]=$data[$iforgive1]['part5'];

        }

        $newperiod['getall']=array_sum($gets)+array_sum($gets1);
        $newperiod['giveall']=array_sum($give)+array_sum($give1);
        $newperiod['realincomeall']=array_sum($realincome)+array_sum($realincome1);
        $newperiod['part1all']=array_sum($part1)+array_sum($part11);
        $newperiod['part2all']=array_sum($part2)+array_sum($part21);
        $newperiod['part3all']=array_sum($part3)+array_sum($part31);
        $newperiod['part4all']=array_sum($part4)+array_sum($part41);
        $newperiod['part5all']=array_sum($part5)+array_sum($part51);
        $newperiod['partners']='南邮-百度推广-阿里去啊-东方航空-青梦家';
        $result =$add3 = M('period') -> add($newperiod);
        $update['period']=$lastperiodid + 1;
         M('deal')->where('period=0')->save($update);
         M('fee')->where('period=0')->save($update);
         M('payment')->where('period=0')->save($update);
         M('reback')->where('period=0')->save($update);
         M('deal')->where('period=0')->save($update);
        if ($result){
           $this->ajaxReturn($result,"结算成功！",1);
        }else{
           $this->ajaxReturn(0,"结算失败！",0);
        }
      }

      public function downloadperiod(){
      $Period =$_GET['period'];
      $A = M('periodall');
      $B =M('period');
      $period =M('period');
      $lastperiodid =$Period -1;
      $lastperiod=$period->where('id='.$lastperiodid)->find();
      $periodid = $B->select();
      if($Period){
      $data   =   $A->where('getorgive = 0 AND period = '.$Period)->select();
      $data2   =  $A->where('getorgive = 1 AND period = '.$Period)->select();
      
      foreach ($data as $mo => $va) {
        $list=explode(',', $data[$mo]["returns"]);
        $data[$mo]["part1"] = $list[0];
        $data[$mo]["part2"] = $list[1];
        $data[$mo]["part3"] = $list[2];
        $data[$mo]["part4"] = $list[3];
        $data[$mo]["part5"] = $list[4];
      };
      foreach ($data as $mo => $va) {
        $data[$mo]["give"] = doubleval($data[$mo]["give"]);
        $data[$mo]["gets"] = doubleval($data[$mo]["gets"]);
        $data[$mo]["realincome"] = $data[$mo]["gets"] + $data[$mo]["give"];
      };

      foreach ($data2 as $mo => $va) {
        $list=explode(',', $data2[$mo]["returns"]);
        $data2[$mo]["part1"] = $list[0];
        $data2[$mo]["part2"] = $list[1];
        $data2[$mo]["part3"] = $list[2];
        $data2[$mo]["part4"] = $list[3];
        $data2[$mo]["part5"] = $list[4];
      };
      foreach ($data2 as $mo => $va) {
        $data2[$mo]["give"] = doubleval($data2[$mo]["give"]);
        $data2[$mo]["gets"] = doubleval($data2[$mo]["gets"]);
        $data2[$mo]["realincome"] = $data2[$mo]["gets"] + $dato2[$mo]["give"];
        //dump($dato[$mo]);
        
      };

    }
    else{

    $reback=M('reback');
    $deal=M('deal');
    $fee=M('fee');
    $period =M('period');
    $lastperiod=$period->order('id desc')->find();
    
    $mapU['period']=0;
    $mapU['haschildren']=0;
    $data=$fee->where($mapU)->Field('id,name')->select();
    $data2=$data;
    for ($i=0; $i < count($data); $i++) { 
      $mapV['period']=0;
      $mapV['money']=array('gt',0);
      $mapV['feename']=$data[$i]['name'];
      $allPay=$deal->where($mapV)->field('money')->select();
      $sum=0;
      for ($j=0; $j < count($allPay); $j++) { 
        $sum+=$allPay[$j]['money'];
      }
      $data[$i]['feename']=$data[$i]['name'];
      $data[$i]['gets']=$sum;
      $data[$i]['give']=0;
    }
    for ($a=0; $a < count($data); $a++) { 
      $mapP['feename']=$data[$a]['name'];
      $mapP['partner']='南邮';        
      $middle=$reback->where($mapP)->select();
      $type = $middle[0]['type'];
        switch ($type) {
          case '0':
            $data[$a]['part1']=$data[$a]['gets']*$middle[0]['value'];
            break;
          case '1':
            $data[$a]['part1']=$middle[0]['value'];
            break;
          default:
            $otherid =intval($middle[0]['otherid'])-1;

            $data[$a]['part1']=$middle[0]['value']*$data[$otherid]['gets'];
            break;
          }
      $mapP1['feename']=$data[$a]['name'];
      $mapP1['partner']='百度推广';        
      $middle1=$reback->where($mapP1)->select();
      $type = $middle1[0]['type'];
        switch ($type) {
          case '0':
            $data[$a]['part2']=$data[$a]['gets']*$middle1[0]['value'];
            break;
          case '1':
            $data[$a]['part2']=$middle1[0]['value'];
            break;
          default:
           $otherid =intval($middle1[0]['otherid'])-1;

            $data[$a]['part2']=$middle1[0]['value']*$data[$otherid]['gets'];
            break;      
        }
      $mapP2['feename']=$data[$a]['name'];
      $mapP2['partner']='阿里去啊';        
      $middle2=$reback->where($mapP2)->select();
      $type = $middle2[0]['type'];
        switch ($type) {
          case '0':
            $data[$a]['part3']=$data[$a]['gets']*$middle2[0]['value'];
            break;
          case '1':
            $data[$a]['part3']=$middle2[0]['value'];
            break;
          default:
            $otherid =intval($middle2[0]['otherid'])-1;

            $data[$a]['part3']=$middle2[0]['value']*$data[$otherid]['gets'];
            break;      
        }
      $mapP3['feename']=$data[$a]['name'];
      $mapP3['partner']='东方航空';        
      $middle3=$reback->where($mapP3)->select();
      $type = $middle3[0]['type'];
        switch ($type) {
          case '0':
            $data[$a]['part4']=$data[$a]['gets']*$middle3[0]['value'];
            break;
          case '1':
            $data[$a]['part4']=$middle3[0]['value'];
            break;
          default:
            $otherid =intval($middle3[0]['otherid'])-1;

            $data[$a]['part4']=$middle3[0]['value']*$data[$otherid]['gets'];
            break;        
        } 
        $mapP4['feename']=$data[$a]['name'];
        $mapP4['partner']='青梦家';        
        $middle4=$reback->where($mapP4)->select();
        $type = $middle4[0]['type'];
        switch ($type) {
          case '0':
            $data[$a]['part5']=$data[$a]['gets']*$middle4[0]['value'];
            break;
          case '1':
            $data[$a]['part5']=$middle4[0]['value'];
            break;
          default:
            $otherid =intval($middle4[0]['otherid'])-1;

            $data[$a]['part5']=$middle4[0]['value']*$data[$otherid]['gets'];
            break;      
        }


    }
    foreach ($data as $mo => $va) {
            $data[$mo]["give"] = doubleval($data[$mo]["give"]);
            $data[$mo]["gets"] = doubleval($data[$mo]["gets"]);
            $data[$mo]["realincome"] = $data[$mo]["gets"] + $data[$mo]["give"];
    };
    for ($i=0; $i < count($data2); $i++) { 
      $mapV['period']=0;
      $mapV['money']=array('lt',0);
      $mapV['feename']=$data2[$i]['name'];
      $allPay=$deal->where($mapV)->field('money')->select();
      $sum=0;
      for ($j=0; $j < count($allPay); $j++) { 
        $sum+=$allPay[$j]['money'];
      }
      $data2[$i]['feename']=$data2[$i]['name'];
      $data2[$i]['give']=$sum;
      $data2[$i]['gets']=0;
    }
    for ($a=0; $a < count($data2); $a++) { 
      $mapP['feename']=$data2[$a]['name'];
      $mapP['partner']='南邮';        
      $middle=$reback->where($mapP)->select();
      $type = $middle[0]['type'];
        switch ($type) {
          case '0':
            $data2[$a]['part1']=$data2[$a]['give']*$middle[0]['value'];
            break;
          case '1':
            $data2[$a]['part1']=0;
            break;
          default:
            $otherid =intval($middle[0]['otherid'])-1;
            $data2[$a]['part1']=$middle[0]['value']*$data2[$otherid]['give'];
            break;
          }
      $mapP1['feename']=$data2[$a]['name'];
      $mapP1['partner']='百度推广';        
      $middle1=$reback->where($mapP1)->select();
      $type = $middle1[0]['type'];
        switch ($type) {
          case '0':
            $data2[$a]['part2']=$data2[$a]['give']*$middle1[0]['value'];
            break;
          case '1':
            $data2[$a]['part2']=0;
            break;
          default:
           $otherid =intval($middle1[0]['otherid'])-1;
            $data2[$a]['part2']=$middle1[0]['value']*$data2[$otherid]['give'];
            break;      
        }
      $mapP2['feename']=$data2[$a]['name'];
      $mapP2['partner']='阿里去啊';        
      $middle2=$reback->where($mapP2)->select();
      $type = $middle2[0]['type'];
        switch ($type) {
          case '0':
            $data2[$a]['part3']=$data2[$a]['give']*$middle2[0]['value'];
            break;
          case '1':
            $data2[$a]['part3']=0;
            break;
          default:
            $otherid =intval($middle2[0]['otherid'])-1;
            $data2[$a]['part3']=$middle2[0]['value']*$data2[$otherid]['give'];
            break;      
        }
      $mapP3['feename']=$data2[$a]['name'];
      $mapP3['partner']='东方航空';        
      $middle3=$reback->where($mapP3)->select();
      $type = $middle3[0]['type'];
        switch ($type) {
          case '0':
            $data2[$a]['part4']=$data2[$a]['give']*$middle3[0]['value'];
            break;
          case '1':
            $data2[$a]['part4']=0;
            break;
          default:
            $otherid =intval($middle3[0]['otherid'])-1;
            $data2[$a]['part4']=$middle3[0]['value']*$data2[$otherid]['give'];
            break;        
        } 
        $mapP4['feename']=$data2[$a]['name'];
        $mapP4['partner']='青梦家';        
        $middle4=$reback->where($mapP4)->select();
        $type = $middle4[0]['type'];
        switch ($type) {
          case '0':
            $data2[$a]['part5']=$data2[$a]['give']*$middle4[0]['value'];
            break;
          case '1':
            $data2[$a]['part5']=0;
            break;
          default:
            $otherid =intval($middle4[0]['otherid'])-1;
            $data2[$a]['part5']=$middle4[0]['value']*$data2[$otherid]['give'];
            break;      
        }
    }
    foreach ($data2 as $mo => $va) {
        $data2[$mo]["give"] = doubleval($data2[$mo]["give"]);
        $data2[$mo]["gets"] = doubleval($data2[$mo]["gets"]);
        $data2[$mo]["realincome"] = $data2[$mo]["gets"] + $data2[$mo]["give"];
      };
      $data[0]['startdate']= $lastperiod['enddate'];
      $data[0]['enddate']= date('Y-m-d');
    }




    $datanum = count($data)+ 7;
    $data2num = count($data2) + $datanum + 1;
    $shourunum = $datanum + 1;
    $shourunum2 = $data2num + 1;


    Vendor('PHPExcel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getActiveSheet()->mergeCells('A1:J1'); 
    $objPHPExcel->getActiveSheet()->mergeCells('A2:J2'); 
    $objPHPExcel->getActiveSheet()->mergeCells('B5:E5');
    $objPHPExcel->getActiveSheet()->mergeCells('F5:J5');
    $objPHPExcel->getActiveSheet()->mergeCells('A5:A6');
    $objPHPExcel->getActiveSheet()->mergeCells('A8:A'.$datanum.'');
    $objPHPExcel->getActiveSheet()->mergeCells('A'.($datanum+2).':A'.$data2num.'');
    $objPHPExcel->getActiveSheet()->mergeCells('A'.($data2num+2).':A'.($data2num+4).'');
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(23);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20); 
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20); 
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);  
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20); 
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);  

    //设置宽
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
    $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
    $objPHPExcel->getActiveSheet()->getStyle('A5:J6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
    $objPHPExcel->getActiveSheet()->getStyle('A5:J6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
    $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 

    $objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode('0');
    $objPHPExcel->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode('0');
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont() ->setSize(16);
    if(!$Period){
      $Period = '当前期';
    }
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '学院收入及分配情况表') 
                ->setCellValue('A2', '（'.$data[0]['startdate'].'-'.$data[0]['enddate'].'止）') 
                ->setCellValue('G4', '制表人：')
                ->setCellValue('I4', '制表日期：')
                ->setCellValue('A4', '期数：'.$Period.'')
                ->setCellValue('A5', '年度')
                ->setCellValue('B5', '收入')
                ->setCellValue('F5', '拨款')
                ->setCellValue('B6', '项目')
                ->setCellValue('C6', '收入')
                ->setCellValue('D6', '退费')
                ->setCellValue('E6', '实际收入')
                ->setCellValue('F6', '南邮')
                ->setCellValue('G6', '百度推广')
                ->setCellValue('H6', '阿里去啊')
                ->setCellValue('I6', '东方航空')
                ->setCellValue('J6', '青梦家')
                ->setCellValue('A7', '上年度期末余额')
                ->setCellValue('C7', $lastperiod['getall'])
                ->setCellValue('D7', $lastperiod['giveall'])
                ->setCellValue('E7', $lastperiod['realincomeall'])
                ->setCellValue('F7', $lastperiod['part1all'])
                ->setCellValue('G7', $lastperiod['part2all'])
                ->setCellValue('H7', $lastperiod['part3all'])
                ->setCellValue('I7', $lastperiod['part4all'])
                ->setCellValue('J7', $lastperiod['part5all'])
                ->setCellValue('A8', '本年度收入和分配')
                ->setCellValue('A'.$shourunum.'', '本年度小计')
                ->setCellValue('C'.$shourunum.'', '=SUM(C8:C'.$datanum.')')
                ->setCellValue('D'.$shourunum.'', '=SUM(D8:D'.$datanum.')')
                ->setCellValue('E'.$shourunum.'', '=SUM(E8:E'.$datanum.')')
                ->setCellValue('F'.$shourunum.'', '=SUM(F8:F'.$datanum.')')
                ->setCellValue('G'.$shourunum.'', '=SUM(G8:G'.$datanum.')')
                ->setCellValue('H'.$shourunum.'', '=SUM(H8:H'.$datanum.')')
                ->setCellValue('I'.$shourunum.'', '=SUM(I8:I'.$datanum.')')
                ->setCellValue('J'.$shourunum.'', '=SUM(J8:J'.$datanum.')')
                ->setCellValue('A'.($datanum+2).'', '本年度分配后退费')
                ->setCellValue('A'.$shourunum2.'', '本年度分配后退费小计')
                ->setCellValue('C'.$shourunum2.'', '=SUM(C'.($datanum+2).':C'.$data2num.')')
                ->setCellValue('D'.$shourunum2.'', '=SUM(D'.($datanum+2).':D'.$data2num.')')
                ->setCellValue('E'.$shourunum2.'', '=SUM(E'.($datanum+2).':E'.$data2num.')')
                ->setCellValue('F'.$shourunum2.'', '=SUM(F'.($datanum+2).':F'.$data2num.')')
                ->setCellValue('G'.$shourunum2.'', '=SUM(G'.($datanum+2).':G'.$data2num.')')
                ->setCellValue('H'.$shourunum2.'', '=SUM(H'.($datanum+2).':H'.$data2num.')')
                ->setCellValue('I'.$shourunum2.'', '=SUM(I'.($datanum+2).':I'.$data2num.')')
                ->setCellValue('J'.$shourunum2.'', '=SUM(J'.($datanum+2).':J'.$data2num.')')
                ->setCellValue('A'.($data2num+2).'', ' 本年度拨付款和支出')
                ->setCellValue('B'.($data2num+2).'', 'HND')
                ->setCellValue('B'.($data2num+3).'', '2+2')
                ->setCellValue('B'.($data2num+4).'', '其他')
                ->setCellValue('A'.($data2num+5).'', ' 本年度拨付款和支出')


                ->setCellValue('C'.($shourunum2+4).'', '=SUM(C'.($datanum+1).':C'.$data2num.')')
                ->setCellValue('D'.($shourunum2+4).'', '=SUM(D'.($datanum+1).':D'.$data2num.')')
                ->setCellValue('E'.($shourunum2+4).'', '=SUM(E'.($datanum+1).':E'.$data2num.')')
                ->setCellValue('F'.($shourunum2+4).'', '=SUM(F'.($datanum+1).':F'.$data2num.')')
                ->setCellValue('G'.($shourunum2+4).'', '=SUM(G'.($datanum+1).':G'.$data2num.')')
                ->setCellValue('H'.($shourunum2+4).'', '=SUM(H'.($datanum+1).':H'.$data2num.')')
                ->setCellValue('I'.($shourunum2+4).'', '=SUM(I'.($datanum+1).':I'.$data2num.')')
                ->setCellValue('J'.($shourunum2+4).'', '=SUM(J'.($datanum+1).':J'.$data2num.')');
    $objPHPExcel->setActiveSheetIndex(0);
    $i=8;
    foreach($data as $k=>$v){
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, $v['feename'])
                ->setCellValue('C'.$i, $v['gets'])
                ->setCellValue('D'.$i, $v['give'])
                ->setCellValue('E'.$i, $v['realincome'])
                ->setCellValue('F'.$i, $v['part1'])
                ->setCellValue('G'.$i, $v['part2'])
                ->setCellValue('H'.$i, $v['part3'])
                ->setCellValue('I'.$i, $v['part4'])
                ->setCellValue('J'.$i, $v['part5']);  

    $i++;
    }
    $j=$datanum+2;
    foreach($data2 as $k=>$v){
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B'.$j, $v['feename'])
                ->setCellValue('C'.$j, $v['gets'])
                ->setCellValue('D'.$j, $v['give'])
                ->setCellValue('E'.$j, $v['realincome'])
                ->setCellValue('F'.$j, $v['part1'])
                ->setCellValue('G'.$j, $v['part2'])
                ->setCellValue('H'.$j, $v['part3'])
                ->setCellValue('I'.$j, $v['part4'])
                ->setCellValue('J'.$j, $v['part5']);  

    $j++;
    }
    $objPHPExcel->getActiveSheet()->setTitle('sheet1');//设置sheet标签的名称
    $objPHPExcel->setActiveSheetIndex(0);
    ob_end_clean();  //清空缓存 
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");
    header('Content-Disposition:attachment;filename=学院收入及分配情况表.xls');//设置文件的名称
    header("Content-Transfer-Encoding:binary");
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;

      }
}