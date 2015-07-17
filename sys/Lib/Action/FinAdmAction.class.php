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

}