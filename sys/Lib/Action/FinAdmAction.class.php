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
                $all[$reback["feeid"]]["rate"] .= " ".$reback["partner"]."(".$reback["value"].")";
            }elseif($reback["type"] == 3){
                $all[$reback["feeid"]]["rate"] .= " ".$reback["partner"]."(剩余)";
            }else{
                $all[$reback["feeid"]]["rate"] .= " ".$reback["partner"]."(".$reback["value"]."%)";
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
        $project = M("system")->where("name='items'")->find();
        $items = explode(",", $project["content"]);

        $paytypes = M("system")->where("name='paytype'")->find();
        $paytype = explode(",", $paytypes["content"]);
        foreach ($items as $item => $va) {
            foreach ($all as $one => $vo) {
                if ($vo["item"] == $va) {
                    $fees[$va][$vo["id"]] = $vo;
                }
            }
        }
        $this->assign("items",$items);
        $this->assign("paytype",$paytype);
        $this->assign("fees",$fees);
        $tmppartners = M("system")->where("name='partners'")->find();
        $partner = explode(",", $tmppartners["content"]);
        $this->assign("partner",$partner);
        $this->display();
    }
    public function apply(){
        $tmppartners = M("system")->where("name='partners'")->find();
        $partner = explode(",", $tmppartners["content"]);
        $this->assign("partner",$partner);//合作方
        $fees = M("fee")->where("id > 0")->select();
        $this->assign("fees",$fees);//其他收费项
        $vo = M("fee")->where("name='报名费' and period=0")->find();
        $this->assign("vo",$vo);
        $this->display();
    }
    public function rebuilt(){
        $tmppartners = M("system")->where("name='partners'")->find();
        $partner = explode(",", $tmppartners["content"]);
        $this->assign("partner",$partner);//合作方
        $fees = M("fee")->where("id > 0")->select();
        $this->assign("fees",$fees);//其他收费项
        $tuition = M("system")->where("name='tuition'")->getField("content");
        $this->assign("tuition",$tuition);
        $this->display();
    }
    public function updaterebuilt(){
        $tuition = $_POST["tuition"];
        if (!is_numeric($tuition)) {
            $this->error("请输入学分价格");
        }
        if (M("system")->where("name='tuition'")->setField("content",$tuition)) {
            $this->success("成功");
        }else{
            $this->error("失败");
        }
    }
    public function addfee(){
        $fee = $_POST["fee"];
        if (count($fee) == 1) {
            $feename = $fee[0]["name"];
        }else{
            // $feename = explode("|",$fee[0]["name"])[0];
            $tmpfee = explode("|",$fee[0]["name"]);
            $feename = $tmpfee[0];
            for ($i = 0, $le = count($fee); $i < $le; $i++) { 
                // $thename = explode("|",$fee[$i]["name"])[0];
                $tmpthe = explode("|",$fee[$i]["name"]);
                $thename = $tmpthe[0];
                if ($thename != $feename) {
                    $this->ajaxReturn(0,"收费项名称不一致",0);
                }
            }
        }
        $map["name"] = array("like",$feename."%");
        $map["period"] = 0;
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
        $id = $fee[0]["id"];
        unset($fee[0]["id"]);
        if (count($fee) == 1) {
            $feename = $fee[0]["name"];
        }else{
            // $feename = explode("|",$fee[0]["name"])[0];
            $tmpfee = explode("|",$fee[0]["name"]);
            $feename = $tmpfee[0];
            for ($i = 0, $le = count($fee); $i < $le; $i++) { 
                // $thename = explode("|",$fee[$i]["name"])[0];
                $tmpthe = explode("|",$fee[$i]["name"]);
                $thename = $tmpthe[0];
                if ($thename != $feename) {
                    $this->ajaxReturn(0,"收费项名称不一致",0);
                }
            }
        }
        // $map["name"] = array("like",$feename."%");
        $map["id|parent"] = $id;
        $map["period"] = 0;
        $willsave = M("fee")->where($map)->select();
        if (count($willsave)  == 0) {
            $this->ajaxReturn(0, "无此收费项！", 0);
        }
        foreach ($willsave as $num => $ws) {
            $ids[] = $ws["id"];
        }
        if (M("payment")->where(array('feeid'=>array('in',$ids)))->count() > 0) {
            $this->error("已绑定学生，禁止修改");
        }
        $b = 0;
        $b = M("fee")->where(array('id'=>array('in',$ids)))->delete();
        if (!$b) {
            $this->ajaxReturn($b,"删除旧收费项失败",0);
        }
        M("reback")->where(array('feeid'=>array('in',$ids)))->delete();
        if (count($fee) > 1) {
            $fee[0]["haschildren"] = 1;
        }
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
    public function updatefeename(){
        $fee = $_POST["fee"];
        $id = $fee[0]["id"];
        $newname = $fee[0]["name"];
        $newtype = $fee[0]["type"];
        $map["period"] = 0;
        $map["id|parent"] = $id;
        $allfee = M("fee")->where($map)->select();
        foreach ($allfee as $va) {
            $name = explode($va["name"], "|");
            $name[0] = $newname;
            $va["name"] = implode("", $name);
            $va["type"] = $newtype;
            M("fee")->save($va);
        }
        $this->ajaxReturn($fee,"success",1);
    }
    public function updateapply(){
        $map["period"] = 0;
        $map["feename"] = "报名费";
        if (M("payment")->where($map)->count() >0) {
            $this->error("报名费已绑定学生，禁止修改");
        }
        $fee = $_POST["fee"];
        M("fee")->where("name like '报名费%'")->delete();
        foreach ($fee as $num => $va) {
            $fee[$num]["item"] = "报名";
            $fee[$num]["name"] = "报名费";
            $fee[$num]["type"] = "报名费";
        }
        if (count($fee) > 1) {
            $fee[0]["haschildren"] = 1;
        }
        $b = M("fee")->add($fee[0]);
        if (count($fee) > 1) {
            for ($i=1; $i < count($fee); $i++) { 
                $childfee[$i-1] = $fee[$i];
                $childfee[$i-1]["name"] .= "|$i";
                $childfee[$i-1]["parent"] = $b;
            }
            $b = M("fee")->addAll($childfee);
        }
        if ($b) {
            $this->success("成功");
        }else{
            $this->error("失败");
        }
    }
    public function delfee(){
        $feename = $_POST["name"];
        if (M("payment")->where(array('feename'=>$feename))->count() > 0) {
            $this->error("此消费项已绑定学生，禁止删除");
        }
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
        $this->ajaxReturn($info,"success",1);
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
            // $newlist[$f["name"]][] = [];
            $newlist[$f["name"]][] = array();
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
    public function rebuiltlist(){
        $map["feename"] = '重修费';
        $map["period"] = 0;
        $list = M("reback")->where($map)->select();
        // $newlist["重修费"][] = [];
        $newlist["重修费"][] = array();
        foreach ($list as $key => $value) {
            $newlist["重修费"][] = $value;
        }
        $newlist["count"] = 1;
        $this->ajaxReturn($newlist,"reback list",1);
    }
    public function rebackdel(){
        $id = $_POST["id"];
        $info = M("reback")->where("id=".$id)->find();
        if (M("reback")->where("id=".$id)->delete()) {
            //输出比例
            $feeid = $info["feeid"];
            $parent = M("fee")->where("id=".$feeid)->getfield("parent");
            $oldall = M("fee")->where("period=0")->order("id")->select();
            $rebacks = M("reback")->where("period=0")->select();
            foreach ($oldall as $one) {
                $all[$one["id"]] = $one;
            }
            foreach ($rebacks as $reback) {
                if ($reback["type"] == 1) {
                    $all[$reback["feeid"]]["rate"] .= " ".$reback["partner"]."(".$reback["value"].")";
                }elseif($reback["type"] == 3){
                    $all[$reback["feeid"]]["rate"] .= " ".$reback["partner"]."(剩余)";
                }else{
                    $all[$reback["feeid"]]["rate"] .= " ".$reback["partner"]."(".$reback["value"]."%)";
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
            $tmp1 = $parent == 0 ? $feeid : $parent;
            $this->ajaxReturn($tmp,$all[$tmp1]["rate"],1);
        }else{
            $this->ajaxReturn(0,"删除失败",0);
        }
    }
    public function rebacksave(){
        $fo = $_POST["reinfo"];
        $b = 1; $count = 0;
        $map["name"] = $fo["feename"];
        $feeid = M("fee")->where($map)->getfield("id");
        $parent = M("fee")->where($map)->getfield("parent");
            if ($fo["status"] == "new") {
                unset($fo["status"]);
                $newlist = $fo;
                $newlist["feeid"] = $feeid;
                $tmp = M("reback")->add($newlist);
            }elseif ($fo["status"] == "update") {
                unset($fo["status"]);
                $updatelist = $fo;
                M("reback")->where("id=".$fo["id"])->save($fo);
                $tmp = $fo["id"];
                if ($tmp) {
                    $count++;
                }
            }
        if (!$tmp && $count == 0) {
            $this->ajaxReturn($newlist,"无更新",0);
        }
        //输出比例
        $oldall = M("fee")->where("period=0")->order("id")->select();
        $rebacks = M("reback")->where("period=0")->select();
        foreach ($oldall as $one) {
            $all[$one["id"]] = $one;
        }
        foreach ($rebacks as $reback) {
            if ($reback["type"] == 1) {
                $all[$reback["feeid"]]["rate"] .= " ".$reback["partner"]."(".$reback["value"].")";
            }elseif($reback["type"] == 3){
                $all[$reback["feeid"]]["rate"] .= " ".$reback["partner"]."(剩余)";
            }else{
                $all[$reback["feeid"]]["rate"] .= " ".$reback["partner"]."(".$reback["value"]."%)";
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
        $tmp1 = $parent == 0 ? $feeid : $parent;
        $this->ajaxReturn($tmp,$all[$tmp1]["rate"],1);
    }
    public function rebuiltreback(){
        $fo = $_POST["reinfo"];
        $b = 1; $count = 0;
            if ($fo["status"] == "new") {
                unset($fo["status"]);
                $newlist = $fo;
                $map["name"] = $fo["feename"];
                $newlist["feeid"] = 0;
            }elseif ($fo["status"] == "update") {
                unset($fo["status"]);
                $updatelist = $fo;
                $tmp = M("reback")->where("id=".$fo["id"])->save($fo);
                if ($tmp) {
                    $count++;
                }
            }
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
        $project = M("system")->where("name='items'")->find();
        $items = explode(",", $project["content"]);
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
        $feeinfo = M("fee")->where('id='.$id)->find();
        $this->assign("feeid",$id);
        $this->assign("feename",$feeinfo["name"]);
        $this->assign("theyear",$year);
        $this->assign("all",$all);
        $this->display();
    }
    public function boundApply(){
        // $year = $_GET["year"] ? $_GET["year"] : date("Y");//年份
        // $this->assign("theyear",$year);
        $info = M("fee")->where("name='报名费' and parent=0")->find();//报名费信息
        $this->assign("feeid",$info["id"]);
        $map["year"] = $year;
        $enroll = M("enroll")->where("enrollstatus=1")->select();
        $classstudent = M("classstudent")->select();
        foreach ($classstudent as $vc) {
            $idcards[] = $vc["idcard"];
        }
        foreach ($enroll as $ve) {
            if (!in_array($ve["idcard"], $idcards)) {
                $mbp["feename"] = "报名费";
                $mbp["idcard"] = $ve["idcard"];
                if (M("payment")->where($mbp)->count() > 0) {
                    $ve["bound"] = true;
                }else{
                    $ve["bound"] = false;
                }
                $all[] = $ve;
            }
        }
        $this->assign("all",$all);
        $this->display();
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
            $map["idcard"] = array("in",$dels);
            $map["feeid"] = $feeid;
            $a = M("payment")->where($map)->delete();
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
    public function applySave(){
        $addlist = $_POST["addlist"];
        $dellist = $_POST["dellist"];
        $feeinfo = M("fee")->where("name='报名费'")->find();
        if (count($dellist) > 0) {
            $map["idcard"] = array("in",$dellist);
            $map["feename"] = "报名费";
            $a = M("payment")->where($map)->delete();
        }
        if (count($addlist) > 0) {
            $mbp["idcard"] = array("in",$addlist);
            $enroll = M("enroll")->where($mbp)->select();
            foreach ($enroll as $num => $ve) {
                $willadd[$num]["feeid"] = $feeinfo["id"];
                $willadd[$num]["feename"] = $feeinfo["name"];
                $willadd[$num]["name"] = $ve["truename"];
                $willadd[$num]["idcard"] = $ve["idcard"];
                $willadd[$num]["standard"] = $feeinfo["standard"];
            }
            $b = M("payment")->addAll($willadd);
        }
        if (!$a && !$b) {
            $this->ajaxReturn(1,"无修改项",0);
            
        }
        $this->ajaxReturn(1,"成功",1);
    }
    public function menupay() {
    $menu['verify']='审核交易';
    $menu['submit']='提交交易';
    $menu['audit']='查看交易';
    $menu['view']='查看交费情况';
    $menu['viewentry']='查看报名费情况';
    //$menu['viewre']='查看重修费情况';
    $this->assign('menu',$this ->autoMenu($menu));  
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
        $this->menupay();
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
        $feename=$deal->where($mapDe)->getField('feename');
        $idcard=$deal->where($mapDe)->getField('idcard');
        updatePaymentStatus($isRefund,$feename,$idcard);
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
        $this->menupay();
        $this->display();
    }
    public function subGo(){
        if(empty($_POST['submitdate'])||empty($_POST['singlemoney'])){$this->error('必填项不能为空');}
        if(!is_numeric($_POST['singlemoney'])){$this->error('金额必须是数字');}
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
        $project=M('system')->where('name="items"')->getField('content');
        $projectArr=explode(',',$project);
        $periodArr=M('period')->field('id')->select();
        $this->assign('periodList',$periodArr);
        $this->assign('way',$wayArr);
        $this->assign('project',$projectArr);
        $this->menupay();
        $this->display();
    }
    public function change(){
        $deal=M('deal');
        $map['id']=$_POST['id'];
        $idcard=$_POST['idcard'];
        $feename=$_POST['feename'];
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
        updatePaymentStatus($isRefund,$feename,$idcard);
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
        if($_GET['fee']){$map['feename']=array('like','%'.$_GET['fee'].'%');}
        if($_GET['type']){$map['type']=$_GET['type'];}
        if($_GET['status']){$map['status']=$_GET['status']-1;}
        if($_GET['period']){$map['period']=$_GET['period'];}else{$map['period']=0;}
        if($_GET['name']){$map['name']=$_GET['name'];}
        if($_GET['stunum']){$map['stunum']=$_GET['stunum'];}
        if($_GET['idcard']){$map['idcard']=$_GET['idcard'];}
        import("ORG.Util.Page");
        $count= $paymentV->where($map)->count();
        $Page= new Page($count,20);
        $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
        $show= $Page->show();
        $list = $paymentV->where($map)->order('status')->limit($Page->firstRow.','.$Page->listRows)->select(); 
        $this->assign('page',$show);
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
        $type=$system->where('name="paytype"')->getField('content');
        $typeArr=explode(',',$type);
        $this->assign('type',$typeArr);
        $this->assign('periodList',$periodArr);
        $this->assign('project',$projectArr);
        $this->menupay();
        $this->display();

    }
    public function viewentry(){
        $payment=M('payment');
        $map['feename']='报名费';
        if($_GET['status']){$map['status']=$_GET['status']-1;}
        if($_GET['period']){$map['period']=$_GET['period'];}else{$map['period']=0;}
        if($_GET['name']){$map['name']=$_GET['name'];}
        if($_GET['idcard']){$map['idcard']=$_GET['idcard'];}
        import("ORG.Util.Page");
        $count= $payment->where($map)->count();
        $Page= new Page($count,20);
        $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
        $show= $Page->show();
        $list = $payment->where($map)->order('name')->limit($Page->firstRow.','.$Page->listRows)->select(); 
        $this->assign('page',$show);
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
        $this->menupay();
        $this->display();  
    }
    public function viewre(){
        $payment=M('payment');
        $map['feename']='重修费';
        if($_GET['status']){$map['status']=$_GET['status']-1;}
        if($_GET['period']){$map['period']=$_GET['period'];}else{$map['period']=0;}
        if($_GET['name']){$map['name']=$_GET['name'];}
        if($_GET['idcard']){$map['idcard']=$_GET['idcard'];}
        $count = $payment-> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $list = $payment -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) ->order('status')-> select();
            $page = $p -> show();
            $this -> assign("page", $page);
        }
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
        $this->menupay();
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
      $oldall = M("fee")->where("period=0")->order("id")->group('item')->select();
      $B = M('period');
      $C =M('class');
      $D =D('ClassstudentView');
      $major = $C ->group('major')->select();
      $yearnum = $C ->group('year')->select();
      $classses = $C ->group('name')->select();
      $periodid = $B->select();
      if($item){$where['item']  = array('like','%'.$item.'%');}
      if($grade){
        $mapccd['year']=$grade;
         $mapin2=  $D ->where($mapccd) ->Field('idcard')->select();
         for ($ll=0; $ll <count($mapin2); $ll++) { 
             $mapi2[]=$mapin2[$ll]['idcard'];
         }
         $where['idcard'] = array('in',$mapi2);
     }
     if($majorname){
        $mapcce['major']=$majorname;
         $mapin3=  $D ->where($mapcce) ->Field('idcard')->select();
         for ($ll1=0; $ll1 <count($mapin3); $ll1++) { 
             $mapi3[]=$mapin3[$ll1]['idcard'];
         }
         $where['idcard'] = array('in',$mapi3);
     }
      if($classes){
        $mapcc['name']=$classes;
         $mapin1 =  $D ->where($mapcc) ->Field('studentname')->select();
         for ($ll2=0; $ll2 <count($mapin1); $ll2++) { 
             $mapi1[]=$mapin1[$ll2]['studentname'];
         }
          $where['truename'] = array('in',$mapi1);

        }
      if($_GET['datefrom']&&$_GET['dateto']){$where['date']=array(array('egt',$_GET['datefrom']),array('elt',$_GET['dateto']));}
      if($_GET['sbfrom']&&$_GET['sbto']){$where['submitdate']=array(array('egt',$_GET['sbfrom']),array('elt',$_GET['sbto']));}
      $where['period']=0;
      $where['check']='已提交';
      if($period){$where['period']  = $period;}
      if($status1){
        if($status1=='交费'){
            $where['money'] = array('gt',0);
            
        }
        if($status1=='退费'){
            $where['money'] = array('lt',0);
            
        }
      }
      $Form  =  M('statistics');

      if(!$_GET['searchkey']){
      import("ORG.Util.Page");
      $count=  $Form ->where($where) ->count();
      $Page= new Page($count,20);
        $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
        $show= $Page->show();
        $Model =  $Form ->where($where)->order('date desc,id desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 

      for ($i=0; $i <count($Model);$i++) {
          if($Model[$i]['money']>0){
              $statusname='交费';
          }
          if($Model[$i]['money']<0){
            $statusname='退费';
          }
        $mapa['studentname']=$Model[$i]['truename'];
        $stugrade= $D ->where($mapa)->select();
        $Model[$i]['grade']=$stugrade[0]['year'];
        $Model[$i]['class']=$stugrade[0]['name'];
        $Model[$i]['majorname']=$stugrade[0]['major'];
        $Model[$i]['statusname']=$statusname;
       }
        $this->assign('grade',$grade);
        $this->assign('item',$item);
        $this->assign('classses',$classses);
        $this->assign('majorname',$majorname);
        $this->assign('period',$period);
        $this->assign('status1',$status1);
        $this->assign('list',$Model);
        $this->assign('page',$show);
        $this->assign('oldall',$oldall);

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
          if($Model[$i]['money']>0){
              $statusname='交费';
          }
          if($Model[$i]['money']<0){
            $statusname='退费';
          }
        $mapa['studentname']=$Model[$i]['truename'];
        $stugrade= $D ->where($mapa)->select();
        $Model[$i]['grade']=$stugrade[0]['year'];
        $Model[$i]['class']=$stugrade[0]['name'];
        $Model[$i]['majorname']=$stugrade[0]['major'];
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
      $this->classes =$classes;  
      $this->major =$major;
      $this->yearnum =$yearnum;
      $this->periodid =$periodid;
      $this->display();


    }
    public function download()
    {
    // Vendor('PHPExcel');
    $php_path = dirname(__FILE__) . '/';
    include $php_path .'../../Lib/ORG/PHPExcel.class.php';
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getActiveSheet()->mergeCells('A1:P1'); 
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
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(16); 
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
    $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
    $objPHPExcel->getActiveSheet()->getStyle('A2:P2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A2:P2')->getFill()->getStartColor()->setARGB('FFF0FFF0');
    $objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode('0');
    $objPHPExcel->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode('0');
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont() ->setSize(20);
    $list=M("statistics");
    $rs=downloads();
    for ($i=0; $i <count($rs);$i++) {
      if($rs[$i]['money']>0){
          $statusname='交费';
      }
      if($rs[$i]['money']<0){
        $statusname='退费';
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
                ->setCellValue('P2', '校财务交费日期');
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
                ->setCellValue('I'.$i, $v['class'])
                ->setCellValue('J'.$i, $v['subject'])
                ->setCellValue('K'.$i, $v['type'])
                ->setCellValue('L'.$i, $v['date'])
                ->setCellValue('M'.$i, $v['money'])
                ->setCellValue('N'.$i, $v['invoice'])
                ->setCellValue('O'.$i, $v['statusname'])
                ->setCellValue('P'.$i, $v['submitdate']);
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
      $tmppartners = M("period")->where('id='.$Period.'')->find();
      $partner = explode(",", $tmppartners["partners"]);
      $dato   =   $A->where('getorgive = 0 AND period = '.$Period)->select();
      $dato2   =  $A->where('getorgive = 1 AND period = '.$Period)->select();
      foreach ($dato as $mo => $va) {
        $list=explode(',', $dato[$mo]["returns"]);
        for ($aa=0; $aa <count($list) ; $aa++) { 
            $dato[$mo]['part'.($aa+1).'']=$list[$aa];
        }
      };
      foreach ($dato as $mo => $va) {
        $dato[$mo]["give"] = doubleval($dato[$mo]["give"]);
        $dato[$mo]["gets"] = doubleval($dato[$mo]["gets"]);
        $dato[$mo]["realincome"] = $dato[$mo]["gets"] + $dato[$mo]["give"];
      };

      foreach ($dato2 as $mo => $va) {
        $list=explode(',', $dato2[$mo]["returns"]);
        for ($ab=0; $ab <count($list) ; $ab++) { 
            $dato2[$mo]['part'.($ab+1).'']=$list[$ab];
        }
      };
      foreach ($dato2 as $mo => $va) {
        $dato2[$mo]["give"] = doubleval($dato2[$mo]["give"]);
        $dato2[$mo]["gets"] = doubleval($dato2[$mo]["gets"]);
        $dato2[$mo]["realincome"] = $dato2[$mo]["gets"] + $dato2[$mo]["give"];
        //dump($dato[$mo]);
        
      };
      for ($ef=0; $ef <count($partner) ; $ef++) { 
        $partarrr = explode(",",$lastperiod["partall"]);
        $lastperiod2[$ef]=$partarrr[$ef];
    }
      $this->assign('partners',$partner);
      $this->assign('partnernum',count($partner));
      $this ->assign('lastperiod',$lastperiod);
      $this ->assign('lastperiod2',$lastperiod2);
      $this->data2 =  $dato2;
      $this->periodid =$periodid;
      $this->data =  $dato;
    }
    else{
    $tmppartners = M("system")->where("name='partners'")->find();
    $partner = explode(",", $tmppartners["content"]);  
    $reback=M('reback');
    $deal=M('deal');
    $fee=M('fee');
    $period =M('period');
    $lastperiod=$period->order('id desc')->find();
    for ($ee=0; $ee <count($partner) ; $ee++) { 
        $partarrr = explode(",",$lastperiod["partall"]);
        $lastperiod2[$ee]=$partarrr[$ee];
    }  
    $mapU['period']=0;
    $mapU['haschildren']=0;
    $data=$fee->where($mapU)->Field('id,name,parent')->select();
    $datall=$fee->where('period=0')->select();
    $data2=$data;
    foreach ($datall as $dataarray ) {
        $dataarr[$dataarray["id"]]=$dataarray;
    }
      $mapV1['period']=0;
      $mapV1['money']=array('gt',0);
      $mapV1['check']='已提交';
      $deals=$deal->where($mapV1)->select();
    for ($i=0; $i < count($data); $i++) { 
      $mapV['period']=0;
      $mapV['money']=array('gt',0);
      $mapV['feeid']=$data[$i]['parent'];
      $mapV['check']='已提交';
      $allPay=$deal->where($mapV)->field('money')->select();
      $dealsdetail=$deal->where($mapV)->select();
      if(!$allPay){
      $mapVv['period']=0;
      $mapVv['money']=array('gt',0);
      $mapVv['feename']=$data[$i]['name'];
      $mapVv['check']='已提交';
      $allPay=$deal->where($mapVv)->field('money')->select();
      $dealsdetail=$deal->where($mapVv)->select();
      }
      $paymentnum=count($dealsdetail);
      $sum=0;
      for ($j=0; $j < count($allPay); $j++) { 
        $sum+=$allPay[$j]['money'];
      }
      $id=$data[$i]['parent'];

      // dump($id);
      if($dataarr[$id]['haschildren']==0){
        for ($ja=0; $ja < count($allPay); $ja++) { 
        $sumd[$i]+=$allPay[$ja]['money'];
        }
        $data[$i]['gets']=$sumd[$i];
      }
     if($dataarr[$id]['haschildren']==1){
        $son=$fee->where('parent='.$id)->select(); 
        for ($deals1=0; $deals1 <(count($son)-1) ; $deals1++) {
            if($deals1==0){
                $pie[$i][0]=$son[0]['standard'];
            }else{
            $pie[$i][$deals1]=$pie[$i][$deals1-1]+$son[$deals1]['standard'];
        }}
            for ($iii=0; $iii < $paymentnum; $iii++) { 
                    for ($iiii=0; $iiii < count($pie[$i]); $iiii++){ 
                        if($allPay[$iii]['money']>$pie[$i][$iiii]){
                            $fengenum[$i][$iii]=$iiii+1;
                        }
                        if($allPay[$iii]['money']<$pie[$i][0]){
                            $fengenum[$i][$iii]=0;
                        }}
                   if($fengenum[$i][$iii]==0){
                      $money[$id][$i][0]+=$allPay[$iii]['money'];
                   }else{
                   for ($ls=0; $ls <$fengenum[$i][$iii] ; $ls++) { 
                       $money[$id][$i][$ls]+=$son[$ls]['standard'];
                   }
                     $piemax=count($pie[$i])-1;
                     $money[$id][$i][$fengenum[$i][$iii]]+=$allPay[$iii]['money']-$pie[$i][$piemax];
                 }
                }   
                // dump($money);
               $whichid = $data[$i]['id']-$id-1;
            $data[$i]['gets']=$money[$id][$i][$whichid];
      }
    
      
      $data[$i]['feename']=$data[$i]['name'];
      $data0[$i]['gets']=$sum;
      $data0[$i]['id']=$data[$i]['parent'];
      $data[$i]['give']=0;
      $othersum[$data[$i]["id"]]=$data[$i]['gets'];
    }
    
    foreach ($data0 as $datasum ) {
        $othersum[$datasum["id"]]=$datasum['gets'];
    }
    for ($a=0; $a < count($data); $a++) { 
        for ($bb=0; $bb <count($partner) ; $bb++) { 
            $mapP['feename']=$data[$a]['name'];
            $mapP['partner']=$partner[$bb];        
            $middle=$reback->where($mapP)->select();
            $type = $middle[0]['type'];
             switch ($type) {
                case '0':
                    $data[$a]['part'.($bb+1).'']=$data[$a]['gets']*$middle[0]['value']*0.01;
                    break;
                case '1':
                    $data[$a]['part'.($bb+1).'']=$middle[0]['value'];
                    break;
                case '2':
                    $otherid =intval($middle[0]['otherid']);
                    $data[$a]['part'.($bb+1).'']=$middle[0]['value']*$othersum[$otherid]['gets']*0.01;
                    break;
                case '3':

                        $reid = $bb + 1 ;
                    break;
                default:
                   $data[$a]['part'.($bb+1).'']= 0;
                   break;
            }
          }
           for($partid=0;$partid<count($partner);$partid++){
               $sumthisitem[$a] +=$data[$a]['part'.($partid+1).''];
            }
            $data[$a]['part'.($reid).''] = $data[$a]['gets'] - $sumthisitem[$a];
    }
    // dump($othersum);
    foreach ($data as $mo => $va) {
            $data[$mo]["give"] = doubleval($data[$mo]["give"]);
            $data[$mo]["gets"] = doubleval($data[$mo]["gets"]);
            $data[$mo]["realincome"] = $data[$mo]["gets"] + $data[$mo]["give"];
    };
    for ($i=0; $i < count($data2); $i++) { 
      $mapV1['period']=0;
      $mapV1['money']=array('lt',0);
      $mapV1['feeid']=$data2[$i]['parent'];
      $mapV1['check']='已提交';
      $allPay=$deal->where($mapV1)->field('money')->select();
      $dealsdetail2=$deal->where($mapV1)->select();
      if(!$allPay){
      $mapVv['period']=0;
      $mapVv['money']=array('lt',0);
      $mapVv['feename']=$data2[$i]['name'];
      $mapVv['check']='已提交';
      $allPay=$deal->where($mapVv)->field('money')->select();
      $dealsdetail2=$deal->where($mapVv)->select();
      }
      $paymentnum2=count($dealsdetail2);
      $sum=0;
      for ($j=0; $j < count($allPay); $j++) { 
        $sum+=$allPay[$j]['money'];
      }
      $id2=$data2[$i]['parent'];
       if($dataarr[$id2]['haschildren']==0){
        for ($ja=0; $ja < count($allPay); $ja++) { 
        $sumd2[$i]+=$allPay[$ja]['money'];
        }
        $data2[$i]['give']=$sumd2[$i];

      }
           if($dataarr[$id2]['haschildren']==1){
        $son=$fee->where('parent='.$id2)->select(); 
        for ($deals1=0; $deals1 <(count($son)-1) ; $deals1++) {
            if($deals1==0){
                $pie[$i][0]=$son[0]['standard'];
            }else{
            $pie[$i][$deals1]=$pie[$i][$deals1-1]+$son[$deals1]['standard'];
        }}
            for ($iii=0; $iii < $paymentnum2; $iii++) { 
                    for ($iiii=0; $iiii < count($pie[$i]); $iiii++){ 
                        $pievalue=(-$pie[$i][$iiii]);
                        $pievalue0=(-$pie[$i][0]);
                        if($allPay[$iii]['money']<$pievalue){
                            $fengenum[$i][$iii]=$iiii+1;
                        }
                        if($allPay[$iii]['money']<$pievalue0){
                            $fengenum[$i][$iii]=0;
                        }}
                        
                   if($fengenum[$i][$iii]==0){
                      $money2[$id2][$i][0]+=$allPay[$iii]['money'];

                   }else{
                   for ($ls=0; $ls <$fengenum[$i][$iii] ; $ls++) { 
                       $money2[$id2][$i][$ls]+=(-$son[$ls]['standard']);
                       
                   }
                     $piemax=count($pie[$i])-1;
                     $money2[$id2][$i][$fengenum[$i][$iii]]+=($allPay[$iii]['money']-$pie[$i][$piemax]);
                 }
                }   
                // dump($money2);
            $whichid = $data2[$i]['id']-$id2-1;
            $data2[$i]['give']=$money2[$id2][$i][$whichid];
      }

      $data2[$i]['feename']=$data2[$i]['name'];
      $data02[$i]['give']=$sum;
      $data02[$i]['id']=$data2[$i]['parent'];      
      $data2[$i]['gets']=0;
      $othersum[$data2[$i]["id"]]=$data2[$i]['give'];
    }
        foreach ($data02 as $datasum ) {
        $othersum[$datasum["id"]]=$datasum['give'];
    }
    for ($a=0; $a < count($data2); $a++) { 
        for ($bc=0; $bc <count($partner) ; $bc++) { 
            $mapP['feename']=$data2[$a]['name'];
            $mapP['partner']=$partner[$bc];        
            $middle=$reback->where($mapP)->select();
            $type = $middle[0]['type'];
             switch ($type) {
                  case '0':
                    $data2[$a]['part'.($bc+1).'']=$data2[$a]['give']*$middle[0]['value']*0.01;
                 break;
                  case '1':
                    $data2[$a]['part'.($bc+1).'']=0;
                    break;
                  case '2':
                    $otherid =intval($middle[0]['otherid']);
                    $data2[$a]['part'.($bc+1).'']=$middle[0]['value']*$othersum[$otherid]['give']*0.01;
                      break;
                  case '3':
                      $reid = $bc + 1;
                      break;
                   default:
                    $data2[$a]['part'.($bc+1).'']=0;
                 break;
            }
          }    
          for($partid=0;$partid<count($partner);$partid++){
               $sumthisitem2[$a] +=$data2[$a]['part'.($partid+1).''];
            }
            $data2[$a]['part'.($reid).''] = $data2[$a]['give'] - $sumthisitem2[$a];    
    }
    foreach ($data2 as $mo => $va) {
        $data2[$mo]["give"] = doubleval($data2[$mo]["give"]);
        $data2[$mo]["gets"] = doubleval($data2[$mo]["gets"]);
        $data2[$mo]["realincome"] = $data2[$mo]["gets"] + $data2[$mo]["give"];
      };
      $data[0]['startdate']= $lastperiod['enddate'];
      $data[0]['enddate']= date('Y-m-d');
      $this ->assign('lastperiod',$lastperiod);
      $this ->assign('lastperiod2',$lastperiod2);
      $this ->assign('data2',$data2);
      $this ->assign('data',$data);
      $this->assign('partners',$partner);
      $this->assign('partnernum',count($partner));
    }
      $this ->assign('choose',$Period);
      $this->periodid =$periodid;
      $this ->display(period);
    }    
    public function endperiod(){
    $tmppartners = M("system")->where("name='partners'")->find();
    $partner = explode(",", $tmppartners["content"]);       
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
    $data=$fee->where($mapU)->Field('id,name,parent')->select();
    $datall=$fee->where('period=0')->select();
    $data2=$data;
    $mappp['period']=0;
    $mappp['check']=array(array('eq','审核中'),array('eq','已审核'),'or');
    $checkarr=$deal->where($mappp)->select();
    if($checkarr){
         $this->ajaxReturn(0,"还有未提交的交易记录，结算失败！",0);
    }else{
    foreach ($datall as $dataarray ) {
        $dataarr[$dataarray["id"]]=$dataarray;
    }
      $mapV1['period']=0;
      $mapV1['money']=array('gt',0);
      $mapV1['check']='已提交';
      $deals=$deal->where($mapV1)->select();
    for ($i=0; $i < count($data); $i++) { 
      $mapV['period']=0;
      $mapV['money']=array('gt',0);
      $mapV['feeid']=$data[$i]['parent'];
      $mapV['check']='已提交';
      $allPay=$deal->where($mapV)->field('money')->select();
      $dealsdetail=$deal->where($mapV)->select();
      if(!$allPay){
      $mapVv['period']=0;
      $mapVv['money']=array('gt',0);
      $mapVv['feename']=$data[$i]['name'];
      $mapVv['check']='已提交';
      $allPay=$deal->where($mapVv)->field('money')->select();
      $dealsdetail=$deal->where($mapVv)->select();
      }
      $paymentnum=count($dealsdetail);
      $sum=0;
      for ($j=0; $j < count($allPay); $j++) { 
        $sum+=$allPay[$j]['money'];
      }
      $id=$data[$i]['parent'];

      // dump($id);
      if($dataarr[$id]['haschildren']==0){
        for ($ja=0; $ja < count($allPay); $ja++) { 
        $sumd[$i]+=$allPay[$ja]['money'];
        }
        $data[$i]['gets']=$sumd[$i];
      }
     if($dataarr[$id]['haschildren']==1){
        $son=$fee->where('parent='.$id)->select(); 
        for ($deals1=0; $deals1 <(count($son)-1) ; $deals1++) {
            if($deals1==0){
                $pie[$i][0]=$son[0]['standard'];
            }else{
            $pie[$i][$deals1]=$pie[$i][$deals1-1]+$son[$deals1]['standard'];
        }}
            for ($iii=0; $iii < $paymentnum; $iii++) { 
                    for ($iiii=0; $iiii < count($pie[$i]); $iiii++){ 
                        if($allPay[$iii]['money']>$pie[$i][$iiii]){
                            $fengenum[$i][$iii]=$iiii+1;
                        }
                        if($allPay[$iii]['money']<$pie[$i][0]){
                            $fengenum[$i][$iii]=0;
                        }}
                   if($fengenum[$i][$iii]==0){
                      $money[$id][$i][0]+=$allPay[$iii]['money'];
                   }else{
                   for ($ls=0; $ls <$fengenum[$i][$iii] ; $ls++) { 
                       $money[$id][$i][$ls]+=$son[$ls]['standard'];
                   }
                     $piemax=count($pie[$i])-1;
                     $money[$id][$i][$fengenum[$i][$iii]]+=$allPay[$iii]['money']-$pie[$i][$piemax];
                 }
                }   
                // dump($money);
               $whichid = $data[$i]['id']-$id-1;
            $data[$i]['gets']=$money[$id][$i][$whichid];
      }
    
      
      $data[$i]['feename']=$data[$i]['name'];
      $data0[$i]['gets']=$sum;
      $data0[$i]['id']=$data[$i]['parent'];
      $data[$i]['give']=0;
      $othersum[$data[$i]["id"]]=$data[$i]['gets'];
    }
    
    foreach ($data0 as $datasum ) {
        $othersum[$datasum["id"]]=$datasum['gets'];
    }
    for ($a=0; $a < count($data); $a++) { 
        for ($bb=0; $bb <count($partner) ; $bb++) { 
            $mapP['feename']=$data[$a]['name'];
            $mapP['partner']=$partner[$bb];        
            $middle=$reback->where($mapP)->select();
            $type = $middle[0]['type'];
             switch ($type) {
                case '0':
                    $data[$a]['part'.($bb+1).'']=$data[$a]['gets']*$middle[0]['value']*0.01;
                    break;
                case '1':
                    $data[$a]['part'.($bb+1).'']=$middle[0]['value'];
                    break;
                case '2':
                    $otherid =intval($middle[0]['otherid']);
                    $data[$a]['part'.($bb+1).'']=$middle[0]['value']*$othersum[$otherid]['gets']*0.01;
                    break;
                case '3':

                        $reid = $bb + 1 ;
                    break;
                default:
                   $data[$a]['part'.($bb+1).'']= 0;
                   break;
            }
          }
           for($partid=0;$partid<count($partner);$partid++){
               $sumthisitem[$a] +=$data[$a]['part'.($partid+1).''];
            }
            $data[$a]['part'.($reid).''] = $data[$a]['gets'] - $sumthisitem[$a];
    }
    // dump($othersum);
    foreach ($data as $mo => $va) {
            $data[$mo]["give"] = doubleval($data[$mo]["give"]);
            $data[$mo]["gets"] = doubleval($data[$mo]["gets"]);
            $data[$mo]["realincome"] = $data[$mo]["gets"] + $data[$mo]["give"];
    };
    for ($i=0; $i < count($data2); $i++) { 
      $mapV1['period']=0;
      $mapV1['money']=array('lt',0);
      $mapV1['feeid']=$data2[$i]['parent'];
      $mapV1['check']='已提交';
      $allPay=$deal->where($mapV1)->field('money')->select();
      $dealsdetail2=$deal->where($mapV1)->select();
      if(!$allPay){
      $mapVv['period']=0;
      $mapVv['money']=array('lt',0);
      $mapVv['feename']=$data2[$i]['name'];
      $mapVv['check']='已提交';
      $allPay=$deal->where($mapVv)->field('money')->select();
      $dealsdetail2=$deal->where($mapVv)->select();
      }
      $paymentnum2=count($dealsdetail2);
      $sum=0;
      for ($j=0; $j < count($allPay); $j++) { 
        $sum+=$allPay[$j]['money'];
      }
      $id2=$data2[$i]['parent'];
       if($dataarr[$id2]['haschildren']==0){
        for ($ja=0; $ja < count($allPay); $ja++) { 
        $sumd2[$i]+=$allPay[$ja]['money'];
        }
        $data2[$i]['give']=$sumd2[$i];

      }

           if($dataarr[$id2]['haschildren']==1){
        $son=$fee->where('parent='.$id2)->select(); 
        for ($deals1=0; $deals1 <(count($son)-1) ; $deals1++) {
            if($deals1==0){
                $pie[$i][0]=$son[0]['standard'];
            }else{
            $pie[$i][$deals1]=$pie[$i][$deals1-1]+$son[$deals1]['standard'];
        }}
            for ($iii=0; $iii < $paymentnum2; $iii++) { 
                    for ($iiii=0; $iiii < count($pie[$i]); $iiii++){ 
                        $pievalue=(-$pie[$i][$iiii]);
                        $pievalue0=(-$pie[$i][0]);
                        if($allPay[$iii]['money']<$pievalue){
                            $fengenum[$i][$iii]=$iiii+1;
                        }
                        if($allPay[$iii]['money']<$pievalue0){
                            $fengenum[$i][$iii]=0;
                        }}
                        
                   if($fengenum[$i][$iii]==0){
                      $money2[$id2][$i][0]+=$allPay[$iii]['money'];

                   }else{
                   for ($ls=0; $ls <$fengenum[$i][$iii] ; $ls++) { 
                       $money2[$id2][$i][$ls]+=(-$son[$ls]['standard']);
                       
                   }
                     $piemax=count($pie[$i])-1;
                     $money2[$id2][$i][$fengenum[$i][$iii]]+=($allPay[$iii]['money']-$pie[$i][$piemax]);
                 }
                }   
                // dump($money2);
            $whichid = $data2[$i]['id']-$id2-1;
            $data2[$i]['give']=$money2[$id2][$i][$whichid];
      }

      $data2[$i]['feename']=$data2[$i]['name'];
      $data02[$i]['give']=$sum;
      $data02[$i]['id']=$data2[$i]['parent'];      
      $data2[$i]['gets']=0;
      $othersum[$data2[$i]["id"]]=$data2[$i]['give'];
    }
        foreach ($data02 as $datasum ) {
        $othersum[$datasum["id"]]=$datasum['give'];
    }
    for ($a=0; $a < count($data2); $a++) { 
        for ($bc=0; $bc <count($partner) ; $bc++) { 
            $mapP['feename']=$data2[$a]['name'];
            $mapP['partner']=$partner[$bc];        
            $middle=$reback->where($mapP)->select();
            $type = $middle[0]['type'];
             switch ($type) {
                  case '0':
                    $data2[$a]['part'.($bc+1).'']=$data2[$a]['give']*$middle[0]['value']*0.01;
                 break;
                  case '1':
                    $data2[$a]['part'.($bc+1).'']=0;
                    break;
                  case '2':
                    $otherid =intval($middle[0]['otherid']);
                    $data2[$a]['part'.($bc+1).'']=$middle[0]['value']*$othersum[$otherid]['give']*0.01;
                      break;
                  case '3':
                      $reid = $bc + 1;
                      break;
                   default:
                    $data2[$a]['part'.($bc+1).'']=0;
                 break;
            }
          }    
          for($partid=0;$partid<count($partner);$partid++){
               $sumthisitem2[$a] +=$data2[$a]['part'.($partid+1).''];
            }
            $data2[$a]['part'.($reid).''] = $data2[$a]['give'] - $sumthisitem2[$a];    
    }
    foreach ($data2 as $mo => $va) {
        $data2[$mo]["give"] = doubleval($data2[$mo]["give"]);
        $data2[$mo]["gets"] = doubleval($data2[$mo]["gets"]);
        $data2[$mo]["realincome"] = $data2[$mo]["gets"] + $data2[$mo]["give"];
      };
      $data[0]['startdate']= $lastperiod['enddate'];
      $data[0]['enddate']= date('Y-m-d');
      //rachel
        for ($h=0; $h <count($data) ; $h++) { 
          $newstatics[$h]['feename']=$data[$h]['feename'];
          $newstatics[$h]['gets']=$data[$h]['gets'];
          $newstatics[$h]['give']=$data[$h]['give'];
          for ($cc=0; $cc <count($partner) ; $cc++) { 
              $partarr[$h][] = $data[$h]['part'.($cc+1).''];
          }
          $newstatics[$h]['returns']=implode(",",$partarr[$h]);
          $newstatics[$h]['period']=$lastperiodid+1;
          $newstatics[$h]['getorgive']=0;
        }
        for ($h1=0; $h1 <count($data2) ; $h1++) { 
          $newstatics2[$h1]['feename']=$data2[$h1]['feename'];
          $newstatics2[$h1]['gets']=$data2[$h1]['gets'];
          $newstatics2[$h1]['give']=$data2[$h1]['give'];
          for ($cd=0; $cd <count($partner) ; $cd++) { 
              $partarr2[$h1][] = $data2[$h1]['part'.($cd+1).''];
          }
          $newstatics2[$h1]['returns']=implode(",",$partarr2[$h1]);
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
          for ($dd=0; $dd <count($partner) ; $dd++) { 
              $part[$iforgive][$dd]=$data2[$iforgive]['part'.($dd+1).''];
          }

        }
        for ($kkk=0; $kkk <count($partner) ; $kkk++) { 
            for ($kk=0; $kk <count($data2) ; $kk++) { 
            $pat[$kkk][$kk]=$part[$kk][$kkk];
            } 
            $parts[$kkk]=array_sum($pat[$kkk]);
        }
                 
        
        for ($iforgive1=0; $iforgive1 < count($data); $iforgive1++) { 
          $gets1[$iforgive1]=$data[$iforgive1]['gets'];
          $give1[$iforgive1]=$data[$iforgive1]['give'];
          $realincome1[$iforgive1]=$data[$iforgive1]['realincome'];
          for ($da=0; $da <count($partner) ; $da++) { 
              $part2[$iforgive1][$da]=$data[$iforgive1]['part'.($da+1).''];
          }
        }
        for ($kkk1=0; $kkk1 <count($partner) ; $kkk1++) { 
            for ($kk1=0; $kk1 <count($data) ; $kk1++) { 
                $pat2[$kkk1][$kk1]=$part2[$kk1][$kkk1];
            } 
            $parts2[$kkk1]=array_sum($pat2[$kkk1]);
        }
        $newperiod['getall']=array_sum($gets)+array_sum($gets1);
        $newperiod['giveall']=array_sum($give)+array_sum($give1);
        $newperiod['realincomeall']=array_sum($realincome)+array_sum($realincome1);
        for ($db=0; $db <count($partner) ; $db++) { 
             $newperiod['part'.($db+1).'all']=$parts[$db]+$parts2[$db];
        }
        for ($dc=0; $dc <count($partner) ; $dc++) { 
            $partalll[]=$newperiod['part'.($dc+1).'all'];
        }
        $newperiod['partall']=implode(",",$partalll);
        $newperiod['partners']=$tmppartners['content'];
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
        $tmppartners = M("period")->where('id='.$Period.'')->find();
      $partner = explode(",", $tmppartners["partners"]);
      $data   =   $A->where('getorgive = 0 AND period = '.$Period)->select();
      $data2   =  $A->where('getorgive = 1 AND period = '.$Period)->select();
      
      foreach ($data as $mo => $va) {
        $list=explode(',', $data[$mo]["returns"]);
        for ($ac=0; $ac <count($list) ; $ac++) { 
            $data[$mo]['part'.($ac+1).'']=$list[$ac];
        }
      };
      foreach ($data as $mo => $va) {
        $data[$mo]["give"] = doubleval($data[$mo]["give"]);
        $data[$mo]["gets"] = doubleval($data[$mo]["gets"]);
        $data[$mo]["realincome"] = $data[$mo]["gets"] + $data[$mo]["give"];
      };

      foreach ($data2 as $mo => $va) {
        $list=explode(',', $data2[$mo]["returns"]);
        for ($ad=0; $ad <count($list) ; $ad++) { 
            $data2[$mo]['part'.($ad+1).'']=$list[$ad];
        }
      };
      foreach ($data2 as $mo => $va) {
        $data2[$mo]["give"] = doubleval($data2[$mo]["give"]);
        $data2[$mo]["gets"] = doubleval($data2[$mo]["gets"]);
        $data2[$mo]["realincome"] = $data2[$mo]["gets"] + $dato2[$mo]["give"];
        //dump($dato[$mo]);
        
      };
        for ($ef=0; $ef <count($partner) ; $ef++) { 
        $partarrr = explode(",",$lastperiod["partall"]);
        $lastperiod2[$ef]=$partarrr[$ef];
    }
    }
    else{
    $tmppartners = M("system")->where("name='partners'")->find();
    $partner = explode(",", $tmppartners["content"]);  
    $reback=M('reback');
    $deal=M('deal');
    $fee=M('fee');
    $period =M('period');
    $lastperiod=$period->order('id desc')->find();
    for ($ee=0; $ee <count($partner) ; $ee++) { 
        $partarrr = explode(",",$lastperiod["partall"]);
        $lastperiod2[$ee]=$partarrr[$ee];
    }  
    $mapU['period']=0;
    $mapU['haschildren']=0;
    $data=$fee->where($mapU)->Field('id,name,parent')->select();
    $datall=$fee->where('period=0')->select();
    $data2=$data;
    foreach ($datall as $dataarray ) {
        $dataarr[$dataarray["id"]]=$dataarray;
    }
      $mapV1['period']=0;
      $mapV1['money']=array('gt',0);
      $mapV1['check']='已提交';
      $deals=$deal->where($mapV1)->select();
    for ($i=0; $i < count($data); $i++) { 
      $mapV['period']=0;
      $mapV['money']=array('gt',0);
      $mapV['feeid']=$data[$i]['parent'];
      $mapV['check']='已提交';
      $allPay=$deal->where($mapV)->field('money')->select();
      $dealsdetail=$deal->where($mapV)->select();
      if(!$allPay){
      $mapVv['period']=0;
      $mapVv['money']=array('gt',0);
      $mapVv['feename']=$data[$i]['name'];
      $mapVv['check']='已提交';
      $allPay=$deal->where($mapVv)->field('money')->select();
      $dealsdetail=$deal->where($mapVv)->select();
      }
      $paymentnum=count($dealsdetail);
      $sum=0;
      for ($j=0; $j < count($allPay); $j++) { 
        $sum+=$allPay[$j]['money'];
      }
      $id=$data[$i]['parent'];

      // dump($id);
      if($dataarr[$id]['haschildren']==0){
        for ($ja=0; $ja < count($allPay); $ja++) { 
        $sumd[$i]+=$allPay[$ja]['money'];
        }
        $data[$i]['gets']=$sumd[$i];
      }
     if($dataarr[$id]['haschildren']==1){
        $son=$fee->where('parent='.$id)->select(); 
        for ($deals1=0; $deals1 <(count($son)-1) ; $deals1++) {
            if($deals1==0){
                $pie[$i][0]=$son[0]['standard'];
            }else{
            $pie[$i][$deals1]=$pie[$i][$deals1-1]+$son[$deals1]['standard'];
        }}
            for ($iii=0; $iii < $paymentnum; $iii++) { 
                    for ($iiii=0; $iiii < count($pie[$i]); $iiii++){ 
                        if($allPay[$iii]['money']>$pie[$i][$iiii]){
                            $fengenum[$i][$iii]=$iiii+1;
                        }
                        if($allPay[$iii]['money']<$pie[$i][0]){
                            $fengenum[$i][$iii]=0;
                        }}
                   if($fengenum[$i][$iii]==0){
                      $money[$id][$i][0]+=$allPay[$iii]['money'];
                   }else{
                   for ($ls=0; $ls <$fengenum[$i][$iii] ; $ls++) { 
                       $money[$id][$i][$ls]+=$son[$ls]['standard'];
                   }
                     $piemax=count($pie[$i])-1;
                     $money[$id][$i][$fengenum[$i][$iii]]+=$allPay[$iii]['money']-$pie[$i][$piemax];
                 }
                }   
                // dump($money);
               $whichid = $data[$i]['id']-$id-1;
            $data[$i]['gets']=$money[$id][$i][$whichid];
      }
    
      
      $data[$i]['feename']=$data[$i]['name'];
      $data0[$i]['gets']=$sum;
      $data0[$i]['id']=$data[$i]['parent'];
      $data[$i]['give']=0;
      $othersum[$data[$i]["id"]]=$data[$i]['gets'];
    }
    
    foreach ($data0 as $datasum ) {
        $othersum[$datasum["id"]]=$datasum['gets'];
    }
    for ($a=0; $a < count($data); $a++) { 
        for ($bb=0; $bb <count($partner) ; $bb++) { 
            $mapP['feename']=$data[$a]['name'];
            $mapP['partner']=$partner[$bb];        
            $middle=$reback->where($mapP)->select();
            $type = $middle[0]['type'];
             switch ($type) {
                case '0':
                    $data[$a]['part'.($bb+1).'']=$data[$a]['gets']*$middle[0]['value']*0.01;
                    break;
                case '1':
                    $data[$a]['part'.($bb+1).'']=$middle[0]['value'];
                    break;
                case '2':
                    $otherid =intval($middle[0]['otherid']);
                    $data[$a]['part'.($bb+1).'']=$middle[0]['value']*$othersum[$otherid]['gets']*0.01;
                    break;
                case '3':

                        $reid = $bb + 1 ;
                    break;
                default:
                   $data[$a]['part'.($bb+1).'']= 0;
                   break;
            }
          }
           for($partid=0;$partid<count($partner);$partid++){
               $sumthisitem[$a] +=$data[$a]['part'.($partid+1).''];
            }
            $data[$a]['part'.($reid).''] = $data[$a]['gets'] - $sumthisitem[$a];
    }
    // dump($othersum);
    foreach ($data as $mo => $va) {
            $data[$mo]["give"] = doubleval($data[$mo]["give"]);
            $data[$mo]["gets"] = doubleval($data[$mo]["gets"]);
            $data[$mo]["realincome"] = $data[$mo]["gets"] + $data[$mo]["give"];
    };
    for ($i=0; $i < count($data2); $i++) { 
      $mapV1['period']=0;
      $mapV1['money']=array('lt',0);
      $mapV1['feeid']=$data2[$i]['parent'];
      $mapV1['check']='已提交';
      $allPay=$deal->where($mapV1)->field('money')->select();
      $dealsdetail2=$deal->where($mapV1)->select();
      if(!$allPay){
      $mapVv['period']=0;
      $mapVv['money']=array('lt',0);
      $mapVv['feename']=$data2[$i]['name'];
      $mapVv['check']='已提交';
      $allPay=$deal->where($mapVv)->field('money')->select();
      $dealsdetail2=$deal->where($mapVv)->select();
      }
      $paymentnum2=count($dealsdetail2);
      $sum=0;
      for ($j=0; $j < count($allPay); $j++) { 
        $sum+=$allPay[$j]['money'];
      }
      $id2=$data2[$i]['parent'];
       if($dataarr[$id2]['haschildren']==0){
        for ($ja=0; $ja < count($allPay); $ja++) { 
        $sumd2[$i]+=$allPay[$ja]['money'];
        }
        $data2[$i]['give']=$sumd2[$i];

      }
           if($dataarr[$id2]['haschildren']==1){
        $son=$fee->where('parent='.$id2)->select(); 
        for ($deals1=0; $deals1 <(count($son)-1) ; $deals1++) {
            if($deals1==0){
                $pie[$i][0]=$son[0]['standard'];
            }else{
            $pie[$i][$deals1]=$pie[$i][$deals1-1]+$son[$deals1]['standard'];
        }}
            for ($iii=0; $iii < $paymentnum2; $iii++) { 
                    for ($iiii=0; $iiii < count($pie[$i]); $iiii++){ 
                        $pievalue=(-$pie[$i][$iiii]);
                        $pievalue0=(-$pie[$i][0]);
                        if($allPay[$iii]['money']<$pievalue){
                            $fengenum[$i][$iii]=$iiii+1;
                        }
                        if($allPay[$iii]['money']<$pievalue0){
                            $fengenum[$i][$iii]=0;
                        }}
                        
                   if($fengenum[$i][$iii]==0){
                      $money2[$id2][$i][0]+=$allPay[$iii]['money'];

                   }else{
                   for ($ls=0; $ls <$fengenum[$i][$iii] ; $ls++) { 
                       $money2[$id2][$i][$ls]+=(-$son[$ls]['standard']);
                       
                   }
                     $piemax=count($pie[$i])-1;
                     $money2[$id2][$i][$fengenum[$i][$iii]]+=($allPay[$iii]['money']-$pie[$i][$piemax]);
                 }
                }   
                // dump($money2);
            $whichid = $data2[$i]['id']-$id2-1;
            $data2[$i]['give']=$money2[$id2][$i][$whichid];
      }

      $data2[$i]['feename']=$data2[$i]['name'];
      $data02[$i]['give']=$sum;
      $data02[$i]['id']=$data2[$i]['parent'];      
      $data2[$i]['gets']=0;
      $othersum[$data2[$i]["id"]]=$data2[$i]['give'];
    }
        foreach ($data02 as $datasum ) {
        $othersum[$datasum["id"]]=$datasum['give'];
    }
    for ($a=0; $a < count($data2); $a++) { 
        for ($bc=0; $bc <count($partner) ; $bc++) { 
            $mapP['feename']=$data2[$a]['name'];
            $mapP['partner']=$partner[$bc];        
            $middle=$reback->where($mapP)->select();
            $type = $middle[0]['type'];
             switch ($type) {
                  case '0':
                    $data2[$a]['part'.($bc+1).'']=$data2[$a]['give']*$middle[0]['value']*0.01;
                 break;
                  case '1':
                    $data2[$a]['part'.($bc+1).'']=0;
                    break;
                  case '2':
                    $otherid =intval($middle[0]['otherid']);
                    $data2[$a]['part'.($bc+1).'']=$middle[0]['value']*$othersum[$otherid]['give']*0.01;
                      break;
                  case '3':
                      $reid = $bc + 1;
                      break;
                   default:
                    $data2[$a]['part'.($bc+1).'']=0;
                 break;
            }
          }    
          for($partid=0;$partid<count($partner);$partid++){
               $sumthisitem2[$a] +=$data2[$a]['part'.($partid+1).''];
            }
            $data2[$a]['part'.($reid).''] = $data2[$a]['give'] - $sumthisitem2[$a];    
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


    // Vendor('PHPExcel');
    $php_path = dirname(__FILE__) . '/';
    include $php_path .'../../Lib/ORG/PHPExcel.class.php';
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

                ->setCellValue('A7', '上年度期末余额')
                ->setCellValue('C7', $lastperiod['getall'])
                ->setCellValue('D7', $lastperiod['giveall'])
                ->setCellValue('E7', $lastperiod['realincomeall'])

                ->setCellValue('A8', '本年度收入和分配')
                ->setCellValue('A'.$shourunum.'', '本年度小计')
                ->setCellValue('C'.$shourunum.'', '=SUM(C8:C'.$datanum.')')
                ->setCellValue('D'.$shourunum.'', '=SUM(D8:D'.$datanum.')')
                ->setCellValue('E'.$shourunum.'', '=SUM(E8:E'.$datanum.')')

                ->setCellValue('A'.($datanum+2).'', '本年度分配后退费')
                ->setCellValue('A'.$shourunum2.'', '本年度分配后退费小计')
                ->setCellValue('C'.$shourunum2.'', '=SUM(C'.($datanum+2).':C'.$data2num.')')
                ->setCellValue('D'.$shourunum2.'', '=SUM(D'.($datanum+2).':D'.$data2num.')')
                ->setCellValue('E'.$shourunum2.'', '=SUM(E'.($datanum+2).':E'.$data2num.')')

                ->setCellValue('A'.($data2num+2).'', ' 本年度拨付款和支出')
                ->setCellValue('B'.($data2num+2).'', 'HND')
                ->setCellValue('B'.($data2num+3).'', '2+2')
                ->setCellValue('B'.($data2num+4).'', '其他')
                ->setCellValue('A'.($data2num+5).'', ' 本年度拨付款和支出')


                ->setCellValue('C'.($shourunum2+4).'', '=SUM(C'.($datanum+1).':C'.$data2num.')')
                ->setCellValue('D'.($shourunum2+4).'', '=SUM(D'.($datanum+1).':D'.$data2num.')')
                ->setCellValue('E'.($shourunum2+4).'', '=SUM(E'.($datanum+1).':E'.$data2num.')');
                for ($excel1=0; $excel1 <count($partner) ; $excel1++) { 
                    $li = chr(70+$excel1);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue(''.$li.'6',$partner[$excel1]);
                }
                for ($excel2=0; $excel2 <count($partner) ; $excel2++) { 
                    $la = chr(70+$excel2);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue(''.$la.'7',$lastperiod2[$excel2]);
                }
                for ($excel3=0; $excel3 <count($partner) ; $excel3++) { 
                    $la = chr(70+$excel3);
                    $le = chr(70+$excel3);
                    $lf = chr(70+$excel3);
                    $lf.=$datanum;
                    $le.=$shourunum;
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($le, '=SUM('.$la.'8:'.$lf.')');
                }
                for ($excel4=0; $excel4 <count($partner) ; $excel4++) { 
                    $la = chr(70+$excel4);
                    $le = chr(70+$excel4);
                    $lf = chr(70+$excel4);
                    $la.=($datanum+2);
                    $lf.=$shourunum2;
                    $le.=$data2num;
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($lf, '=SUM('.$la.':'.$le.')');
                }
                for ($excel5=0; $excel5 <count($partner) ; $excel5++) { 
                    $la = chr(70+$excel5);
                    $le = chr(70+$excel5);
                    $lf = chr(70+$excel5);
                    $la.=($datanum+1);
                    $lf.=($shourunum2+4);
                    $le.=$data2num;
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($lf, '=SUM('.$la.':'.$le.')');
                }


                
    $objPHPExcel->setActiveSheetIndex(0);
    $i=8;
    foreach($data as $k=>$v){
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, $v['feename'])
                ->setCellValue('C'.$i, $v['gets'])
                ->setCellValue('D'.$i, $v['give'])
                ->setCellValue('E'.$i, $v['realincome']);
    for ($ec=0; $ec <count($partner) ; $ec++) { 
        $la = chr(70+$ec);
        $la.=$i;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($la, $v['part'.($ec+1).'']);
    }
    $i++;
    }
    $j=$datanum+2;
    foreach($data2 as $k=>$v){
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B'.$j, $v['feename'])
                ->setCellValue('C'.$j, $v['gets'])
                ->setCellValue('D'.$j, $v['give'])
                ->setCellValue('E'.$j, $v['realincome']);
    for ($ec2=0; $ec2 <count($partner) ; $ec2++) { 
        $la = chr(70+$ec2);
        $la.=$j;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($la, $v['part'.($ec2+1).'']);
    }            
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
    exit;}

}