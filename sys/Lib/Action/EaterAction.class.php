<?php
class EaterAction extends CommonAction {
	public function index() {
        $User = D('User');
        $dao = D('mail');
        $map1['a1'] = session('username');
        $map1['isdela'] = 0;
        $map1['isread'] = 0;
        $mail_num=$dao->where($map1)->count();
        $this->assign('mail_num',$mail_num);
        $map['username'] = session('username');
        $photo = $User->where($map)->getField('photo');
        $this->assign('photo',$photo);
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
/**********************总结*****************************/
    public function menusummary() {
    $menu['summary']='总结记录';
    $this->assign('menu',$this ->autoMenu($menu));  
    }
    /*public function menusummary() {
    $menu['summary']='总结记录';
    $this->assign('menu',$this ->autoMenu($menu));  
    }*/
    public function summary(){
        $m["item"] = 'HND';//分项目的限制开始
        $major = M("major")->where($m)->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $mbp["major"] = array("in",$majors);//分项目的限制结束
        $classList=M('class')->where($mbp)->Field('name')->select();
        foreach ($classList as $k => $va) {
            $class_forlist[$va["name"]] = $va["name"];
        }
        $map["classname"] = array("in",$class_forlist);
        $this->assign('classList',$classList);    
        if (isset($_GET['searchkey'])) {
            $map['content'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        }
        if($_GET['classname']){$map['classname']=$_GET['classname'];}
        if($_GET['datefrom']&&$_GET['dateto']){
            $f=$_GET['datefrom'];$t=$_GET['dateto'];
            $map['_string']='NOT((sbdate<='.'"'.$f.'"'.' AND sedate<='.'"'.$f.'"'.')OR('.'"'.$t.'"'.'<=sbdate AND '.'"'.$t.'"'.'<=sedate))';
        }
        if($_GET['type']){$map['type']=$_GET['type'];}
        $dao = D('summary');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 100;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('date desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        }
        $this -> menusummary();
        $this -> display();
    }
    public function summaryEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        $m["item"] = 'HND';//分项目的限制开始
        $major = M("major")->where($m)->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $mbp["major"] = array("in",$majors);//分项目的限制结束
        $classList=M('class')->where($mbp)->Field('name')->select();
        foreach ($classList as $k => $va) {
            $class_forlist[$va["name"]] = $va["name"];
        }
        $map["classname"] = array("in",$class_forlist);
        $this->assign('classList',$classList);
        $dao = D('summary');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> menusummary();
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    }
    public function getAttendInfo(){
        $classname=$_POST['classname'];
        $map['classname']=$_POST['classname'];
        $map['timezone']=array(array('egt',$_POST['sbdate']),array('elt',$_POST['sedate']));
        $Attend=M('attend');
        $truantSum=$Attend->where($map)->sum("truant");
        $tvacateSum=$Attend->where($map)->sum("tvacate");
        $svacateSum=$Attend->where($map)->sum("svacate");
        $lateSum=$Attend->where($map)->sum("late");
        if(!($truantSum||$tvacateSum||$svacateSum||$lateSum)){$info="无考勤记录";}
        else{$info="从".$_POST['sbdate']."至".$_POST['sedate']."期间，".$classname."学生共计旷课".$truantSum."次，事假".$tvacateSum."次，病假".$svacateSum."次，迟到".$lateSum."次";}
        $this->ajaxReturn($info);
    }
    public function summaryUpdate() {
        $classname = $_POST['classname'];
        $tusername = session('username');
        $ttruename = session('truename');
        $title = $_POST['title'];
        $content = $_POST['content'];
        $type = $_POST['type'];
        $sbdate = $_POST['sbdate'];
        $sedate = $_POST['sedate'];
        $date = date("Y-m-d");
        if (empty($classname) || empty($content)|| empty($type)|| empty($sbdate)|| empty($sedate)) {
            $this -> error('必填项不能为空');
        } 
        $dao = D('summary');
        if ($dao -> create()) {
            $checked = $dao -> save();
            if ($checked > 0) {
                $this -> success('已成功保存');
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error($dao->getError());
        }
    }
    public function summaryDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        $map['id'] = array('in', $id);
        $dao = D('summary');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    }
/************************分配宿舍**********************************/
    public function menudistribute() {
        $menu['distribute']='宿舍情况';
        $menu['distributeAdd']='分配宿舍';
        $menu['houseparent']='宿舍条例';
        $menu['reward']='奖惩记录';
        $menu['rewardAdd']='新建奖惩记录';
        $menu['broken']='损坏记录';
        $menu['brokenAdd']='新增损坏记录';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function distribute() {
        $m["item"] = 'HND';//分项目的限制开始
        $major = M("major")->where($m)->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $mbp["major"] = array("in",$majors);//分项目的限制结束
        $classList=M('class')->where($mbp)->select();
        $this->assign('classList',$classList);
        foreach ($classList as $k => $va) {
            $class_forlist[$va["id"]] = $va["id"];
        }
        $dao = D('classstudent');
        if(isset($_GET['searchkey'])){$map['student']=$_GET['searchkey'];}
        if($_GET['classname']){
            $mapCl['name']=$_GET['classname'];
            $map['classid']=M('class')->where($mapCl)->getField('id');
        }else{
            $map["classid"] = array("in",$class_forlist);
        }
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 100;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('student') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            for ($i=0; $i < count($my); $i++) {
                $mapC['id']=$my[$i]['classid'];
                $my[$i]['classname']=M('class')->where($mapC)->getField('name');
            }
            $this -> assign('my', $my);
        }
        $this -> menudistribute();
        $this -> display();
    }
    public function distributeAdd() {
        $this -> menudistribute();
        $this -> display();
    }
    public function downloadRoomStu(){
        // Vendor('PHPExcel'); 
        $titlepic = '/buaahnd/sys/Tpl/Public/download/distribute.xls';
        $php_path = dirname(__FILE__) . '/';
        $excelurl = $php_path .'../../../..'.$titlepic;
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $p = PHPExcel_IOFactory::load($excelurl);
        $p -> setActiveSheetIndex(0);
        $map['house']=array('EXP','IS NULL');
        $map['room']=array('EXP','IS NULL');
        $map['cell']=array('EXP','IS NULL');
        $map['bed']=array('EXP','IS NULL');
        $m["item"] = '美国2+2';//分项目的限制开始
        $major = M("major")->where($m)->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $map["major"] = array("in",$majors);//分项目的限制结束
        $stuinfo = D("ClassstudentView")->where($map)->select();
        $p->getActiveSheet()->getStyle('H')->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        foreach ($stuinfo as $i => $vs) {
            $p  ->setActiveSheetIndex(0)
                ->setCellValue('A'.($i+3), $vs["name"])
                ->setCellValue('F'.($i+3), $vs["studentname"])
                ->setCellValueExplicit('G'.($i+3), $vs["student"],PHPExcel_Cell_DataType::TYPE_STRING);

        }
          ob_end_clean();
        header("Pragma: public");
          header("Expires: 0");
          header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
          header("Pragma: no-cache");
          header("Content-Type:application/octet-stream");
          header('content-Type:application/vnd.ms-excel;charset=utf-8');
          header('Content-Disposition:attachment;filename=所有未分配学生(可用作模板).xls');//设置文件的名称
          header("Content-Transfer-Encoding:binary");
          $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
          $objWriter->save('php://output');
          return true;
          exit;
    }
    public function distributeInsert() {
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
        if ($count < 3) {
            $this->ajaxReturn($count, "请填写信息", 0);
        }
        for ($i=3; $i <=$count; $i++) { 
            for ($j=1; $j <= 7; $j++) {             
               if(strlen($sheetData[$i][chr(64+$j)])==0){
                    $emptys[]=chr(64+$j).$i;
                }   
            }//检查非空项
            $map1['studentname']=$sheetData[$i]['F'];
            $map1['student']=$sheetData[$i]['G'];
            $check=M('classstudent')->where($map1)->select();
            if(!$check){
                $errors[]='F'.$i;
                $errors[]='G'.$i;
            }
            $mapR['house']=$sheetData[$i]['B'];
            $mapR['room']=$sheetData[$i]['C'];
            $mapR['cell']=$sheetData[$i]['D'];
            $mapR['bed']=$sheetData[$i]['E'];
            $theSameOne=M('classstudent')->where($mapR)->select();
            if($theSameOne>0){
                $errors[]='B'.$i;
                $errors[]='C'.$i;
                $errors[]='D'.$i;
                $errors[]='E'.$i;
            }
            $thisLive=$sheetData[$i]['B'].$sheetData[$i]['C'].$sheetData[$i]['D'].$sheetData[$i]['E'];
            for($k=$i+1;$k <=$count;$k++){
                $thatLive=$sheetData[$k]['B'].$sheetData[$k]['C'].$sheetData[$k]['D'].$sheetData[$k]['E'];
                if($thatLive==$thisLive&&strlen($thisLive)!=0){
                    $conflicts[]='B'.$i;
                    $conflicts[]='C'.$i;
                    $conflicts[]='D'.$i;
                    $conflicts[]='E'.$i;
                    $conflicts[]='B'.$k;
                    $conflicts[]='C'.$k;
                    $conflicts[]='D'.$k;
                    $conflicts[]='E'.$k;
                }
            }
        }
        if (count($emptys) > 0) {
            excelwarning($inputFileName,$emptys,'FF00B0F0');
        }
        if (count($conflicts) > 0) {
            excelwarning($inputFileName,$conflicts,'FFFFC000');
        }
        if (count($errors) > 0) {
            excelwarning($inputFileName,$errors);
        }
        if (count($errors) > 0 || count($emptys) > 0 || count($conflicts) > 0) {
            $this->ajaxReturn($titlepic, "信息不正确", 0);
        }
        for ($j=3; $j <=$count; $j++) {
           $map['studentname']=$sheetData[$j]['F'];
           $map['student']=$sheetData[$j]['G'];
           $data_a['house']=$sheetData[$j]['B'];
           $data_a['room']=$sheetData[$j]['C'];           
           $data_a['cell']=$sheetData[$j]['D'];
           $data_a['bed']=$sheetData[$j]['E'];
           M('classstudent')->where($map)->save($data_a);
        }
        $this -> success("已成功保存");

    }
    public function distributeEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('classstudent');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $mapC['id']=$my['classid'];
            $my['classname']=M('class')->where($mapC)->getField('name');
            $this -> assign('my', $my);
            $this -> menudistribute();
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function distributeUpdate() {
        $house = $_POST['house'];
        $room = $_POST['room'];
        $cell = $_POST['cell'];
        $bed = $_POST['bed'];
        if (empty($house)||empty($room)||empty($cell)||empty($bed)) {
            $this -> error('必填项不能为空');
        } 
        $dao = D('classstudent');
        if ($dao -> create()) {
            $ckB['house']=$house;
            $ckB['room']=$room;
            $ckB['cell']=$cell;
            $ckB['bed']=$bed;
            $resB=$dao->where($ckB)->find();
            if($resB&&$resB['id']!=$_POST['id']){
                $this->ajaxReturn($resB,'该床位已有同学',0);
            }
            $checked = $dao -> save();
            if ($checked > 0) {
                $this -> success('已成功保存');
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error($dao->getError());
        } 
    }
    public function change(){
        $classstudent=M('classstudent');
        $a['id']=$_POST['id'];
        $dataA=$classstudent->where($a)->find();
        $b['house']=$_POST['house'];
        $b['room']=$_POST['room'];
        $b['cell']=$_POST['cell'];
        $b['bed']=$_POST['bed'];
        $dataB=$classstudent->where($b)->find();
        $bb['id']=$classstudent->where($b)->getField('id');
        $saveForA['house']=$dataB['house'];
        $saveForA['room']=$dataB['room'];
        $saveForA['cell']=$dataB['cell'];
        $saveForA['bed']=$dataB['bed'];
        $saveForB['house']=$dataA['house'];
        $saveForB['room']=$dataA['room'];
        $saveForB['cell']=$dataA['cell'];
        $saveForB['bed']=$dataA['bed'];
        $ca=$classstudent->where($a)->save($saveForA);
        $cb=$classstudent->where($bb)->save($saveForB);
        if($ca&&$cb){$this->success('更换成功');}else{$this->error('更换失败');}
    }
/*******************************宿舍管理**********************************/
    public function houseparent(){
        $rules=M('system')->where('name="rules"')->getField('content');
        $this->assign('rules',$rules);
        $this->menudistribute();
        $this->display();
    }
    public function houseparentEdit(){
        $rules=M('system')->where('name="rules"')->getField('content');
        $r=str_replace("<br>",' \n',$rules);
        $this->assign('rules',$r);
        $this->menudistribute();
        $this->display();
    }
    public function updateRules(){
        $data['content']=$_POST['rules'];

        if (empty($data['content'])) {
            $this -> error('必填项不能为空');
        }
        $dao = M('system');
        $checked = $dao -> where('name="rules"')->save($data);
        if ($checked) {
            $this -> success('已成功保存');
        } else {
            $this -> error('没有更新任何数据');
        }
    }
    public function out(){
        $map['id']=$_POST['id'];
        $data['house']=NULL;
        $data['room']=NULL;
        $data['cell']=NULL;
        $data['bed']=NULL;
        $check=M('classstudent')->where($map)->save($data);
        if($check){$this->success('退宿成功');}else{$this->error('退宿失败');}
    }
/*****************************学生信息*******************************************/
    public function students(){
        $a = array('name'=>'姓名','ename'=>'ename','birthday'=>'出生日期','sex'=>'性别','gender'=>'Gender','address'=>'家庭住址','HomeAddress'=>'HomeAddress','postaladdress'=>'通信地址','CorrespondenceAddress'=>'CorrespondenceAddress','phone'=>'固定电话','mobile'=>'手机','email'=>'Email1','Email2'=>'Email2','MSN'=>'MSN','qq'=>'OICQ','nativeprovince'=>'省份','Province'=>'Province','nativecity'=>'城市','City'=>'City','idcardpassport'=>'身份证护照号码','project'=>'就读国内项目名称','HNDCenter'=>'HNDCenter','year'=>'入学时间','grade'=>'所属年级','entrancescore'=>'高考成绩总分','englishscore'=>'英语单科成绩','entrancefull'=>'高考分数标准','major'=>'HND专业','drop'=>'是否退学','repeat'=>'是否留级','SCN'=>'SCN号','listeningscore'=>'听力得分','readingscore'=>'阅读得分','writingscore'=>'写作得分','speakingscore'=>'口语得分','testscore'=>'进入专业课英语成绩总分','score1'=>'最优有效雅思成绩','score1id'=>'雅思考试号','plus'=>'其他','HNDtime'=>'获得HND证书时间','quit'=>'是否留学','country'=>'留学国家','Country'=>'Country','school'=>'国外院校名称','ForeignUniversityApplied'=>'ForeignUniversityApplied','fmajor'=>'留学所学专业','together'=>'出国所经过中介名称','employ'=>'是否就业','enterprise'=>'就业企业名称','workaddress'=>'就业企业所在省市','enterprisenature'=>'就业企业性质','individualorientationandspecialty'=>'个人情况介绍及特长','professionalcertificate'=>'所获得职业资格证书','xuben'=>'续本','xubensch'=>'续本国内院校名称','degreesch'=>'将获得哪所院校颁发学位','xubenmajor'=>'续本专业');
        return $a;
    }
    public function getClassStudent($classid){
        $map['id'] = $classid;
        $map['isbiye'] = 0;
        $dao = D('StudentView');
        $my = $dao->where($map)->order('student asc')->group('student')->select();
        foreach($my as $key=>$value){
            $my[$key]['name'] = $value['studentname'];
            $my[$key]['ename'] = $value['ename'];
            if($value['sex'] == '男'){
                $my[$key]['gender'] = 'male';
            }
            if($value['sex'] == '女'){
                $my[$key]['gender'] = 'female';
            }
            $my[$key]['idcardpassport'] = '身份证：'.$value['idcard'].'　护照：'.$value['passport'];
            $my[$key]['grade'] = '大'.$this->getGrade(date('Y-m-d',time()),$value['year']);
            //$my[$key]['birthday'] = substr($value['birthday'],0,10);
            $my[$key]['birthday'] = $value['birthday']?substr($value['birthday'],0,10):'';
            $my[$key]['major'] = $value['major'].'　'.$value['majore'];
        }
        return $my; 
    }
    public function getGradeStudent($year){
        $map['year'] = $year;
        $map['isbiye'] = 0;
        $dao = D('StudentView');
        $list = $dao->where($map)->group('student')->order('year desc,name asc,student asc')->select();
        foreach($list as $k => $v){
            $my[$v['name']][]=$v;
        }
        foreach($my as $k1=>$v1){
            foreach($v1 as $k2=>$v2){
            $my[$k1][$k2]['name']=$v2['studentname'];
            $my[$k1][$k2]['ename']=$v2['ename'];
            if($v2['sex'] == '男'){
                $my[$k1][$k2]['gender'] = 'male';
            }
            if($v2['sex'] == '女'){
                $my[$k1][$k2]['gender'] = 'female';
            }
            $my[$k1][$k2]['idcardpassport'] = '身份证：'.$v2['idcard'].'　护照：'.$v2['passport'];
            $my[$k1][$k2]['grade'] = '大'.$this->getGrade(date('Y-m-d',time()),$v2['year']);
            //$my[$k1][$k2]['birthday'] = substr($v2['birthday'],0,10);
            $my[$k1][$k2]['birthday'] = $v2['birthday']?substr($v2['birthday'],0,10):'';
            $my[$k1][$k2]['major'] = $v2['major'].'　'.$v2['majore']; 
            }
        }
        return $my; 
    }
    public function headers(){
       $a=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG');
            return $a;
    }
    public function getCompoundSurname(){
        $a=array('欧阳','太史','端木','上官','司马','东方','独孤','南宫','万俟','闻人','夏侯','诸葛','尉迟','公羊','赫连','澹台','皇甫','宗政','濮阳','公冶','太叔','申屠','公孙','慕容','仲孙','钟离','长孙','宇文','司徒','鲜于','司空','闾丘','子车','亓官','司寇','巫马','公西','颛孙','壤驷','公良','漆雕','乐正','宰父','谷梁','拓跋','夹谷','轩辕','令狐','段干','百里','呼延','东郭','南门','羊舌','微生','公户','公玉','公仪','梁丘','公仲','公上','公门','公山','公坚','左丘','公伯','西门','公祖','第五','公乘','贯丘','公皙','南荣','东里','东宫','仲长','子书','子桑','即墨','达奚','褚师');
        return $a;
    }
    public function getSex($stusex){
        $allsex=array('male','female');
        $sex='';
        if($stusex == '男'){
            $sex=$allsex[0];
        }elseif($stusex == '女'){
            $sex=$allsex[1];
        }
        return $sex;
    }
   public function getGrade($now,$enrollyear){
        $grade=array('一','二','三','四');
        $a='';
        if((intval(substr($now,0,4))-intval($enrollyear)) == 0){
            $a=$grade[0];
        }elseif((intval(substr($now,5,2)) < 9)){
            $a=$grade[((intval(substr($now,0,4))-intval($enrollyear))-1)];
        }else{
            $a=$grade[(intval(substr($now,0,4))-intval($enrollyear))];    
        }
        return $a;
    }
    public function getEgrade($now,$enrollyear){
        $egrade=array('First','Second','Third','Fourth');
        $b='';
        if((intval(substr($now,0,4))-intval($enrollyear)) == 0){
            $b=$egrade[0];
        }elseif((intval(substr($now,5,2)) < 9)){
            $b=$egrade[((intval(substr($now,0,4))-intval($enrollyear))-1)];
        }else{
            $b=$egrade[(intval(substr($now,0,4))-intval($enrollyear))];    
        }
        return $b;
    }
    public function getStandards($term,$enrollyear,$coursename,$coursetime,$credit,$score){
        $a='';
        if(strlen($score) == 0){
                              $a='U';  
                            }
                       else{
                            $a=$score;
                        }
        return $a;
    }
      public function getBrotherCity($cityname) {
        $Area = D("Area");
        $map['region_name']=$cityname;
        $map['region_type']=2;
        $parent = $Area ->field('parent_id')-> where($map) -> find();
        $map_a['parent_id']=$parent['parent_id'];
        $brother = $Area-> where($map_a) -> select();
        $a = array();
        foreach($brother as $key => $value) {
            $a[$value['region_name']] = $value['region_name'];
        }
        return $a;
    } 
    public function stuCommon() {
        $this -> display();
    } 
    public function stuCommonIsOwnByTeacher(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('ClassstudentView');
        $map['isbiye']=0;
        $map['student']=$id;
        $my=$dao->where($map)->find();
        if(!$my){
            $this -> error('该学号不存在或权限不足');
        }
        return $id;
    }
    public function stuCommonMenu($id) {
        $menu['stuCommonScore']=' 成绩单';
        $menu['stuCommonCertification']='在读证明';
        $menu['stuCommonAttend']='考勤记录';
        $menu['stuCommonReward']='奖惩记录';
        $menu['stuCommonProcess']='留学进程';
        $menu['stuCommonInfo']='基本信息';
        $this->assign('menu',$this ->autoMenu($menu,$id));  
    }
    public function stuCommonScore() {
        $id =$this->stuCommonIsOwnByTeacher();
        $Score = D("Score");
        $map['susername']=$id;
        $map['isvisible']=1;
                                                                                                                                                                                                                                                                                       
        $term=$Score -> where($map) ->field('term')->group('term')->order('term asc')-> select();
        $term_num=count($term);
        // if($term_num>0){
            foreach($term as $key=>$value){
                $map['term']=$value['term'];
                $my[$key]=$Score -> where($map) -> select();
            }
            foreach($term as $vo){
                $sterm[$vo['term']]=$vo['term'];
            }
            $this->assign('sterm',$sterm);
            $this->assign('id',$id);
            $this->assign('my',$my);
        // } 
        $this->stuCommonMenu($id);
        $this -> display();
    }
    public function downPreScore(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        // Vendor('PHPExcel'); 
        $titlepic = '/buaahnd/sys/Tpl/Public/download/prescore.xls';
        $php_path = dirname(__FILE__) . '/';
        $excelurl = $php_path .'../../../..'.$titlepic;
        $stuinfo = D("ClassstudentView")->where(array("student"=>$id))->find();
        if (!$stuinfo) {
            $this -> error('无此学生'.$id);
        }
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $p = PHPExcel_IOFactory::load($excelurl);//载入Excel
        $p  ->setActiveSheetIndex(0)
            ->setCellValue('B3', $stuinfo["studentname"]) 
            ->setCellValue('B4', $stuinfo["ename"]) ;//写上学生姓名
        $scores = M("pregrade")->where(array("stunum"=>$id))->select();
        $styleArray = array(  
            'borders' => array(  
                'allborders' => array(  
                    'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框  
                    'color' => array('argb' => '000'),  
                ),  
            ),  
        );  
        $line = 6;
        for ($i=0; $i < count($scores); $i++) { 
            $p  ->setActiveSheetIndex(0)
                ->setCellValue('A'.$line, $scores[$i]["examname"]) 
                ->setCellValue('A'.($line+1), ' 听 力 :') 
                ->setCellValue('B'.($line+1), $scores[$i]["listening"]."分")
                ->setCellValue('A'.($line+2), ' Listening:') 
                ->setCellValue('B'.($line+2), $scores[$i]["listening"])
                ->setCellValue('A'.($line+3), ' 口 语 :') 
                ->setCellValue('B'.($line+3), $scores[$i]["speaking"]."分")
                ->setCellValue('A'.($line+4), 'Speaking:') 
                ->setCellValue('B'.($line+4), $scores[$i]["speaking"])
                ->setCellValue('D'.($line+1), ' 阅 读 :') 
                ->setCellValue('E'.($line+1), $scores[$i]["reading"]."分") 
                ->setCellValue('D'.($line+2), 'Reading:') 
                ->setCellValue('E'.($line+2), $scores[$i]["reading"]) 
                ->setCellValue('D'.($line+3), ' 总 分 :') 
                ->setCellValue('E'.($line+3), $scores[$i]["total"]."分") 
                ->setCellValue('D'.($line+4), 'Total:') 
                ->setCellValue('E'.($line+4), $scores[$i]["total"]) 
                ->setCellValue('G'.($line+1), ' 写 作 :') 
                ->setCellValue('H'.($line+1), $scores[$i]["writing"]."分") 
                ->setCellValue('G'.($line+2), 'Writing:')
                ->setCellValue('H'.($line+2), $scores[$i]["writing"])
                ->setCellValue('A'.($line+5), 'Remark:Listening 9,Reading 9,Writing 9,Speaking 9,Total 9')
                ->getStyle('A'.($line+5).':I'.($line+5))->applyFromArray($styleArray);
            $p  ->getActiveSheet()->mergeCells( 'A'.$line.':I'.$line);
            $p  ->getActiveSheet()->mergeCells( 'A'.($line+5).':I'.($line+5));
            $p  ->getActiveSheet()->getStyle('A'.$line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            for ($j=1; $j <= 4; $j++) { 
                $p  ->getActiveSheet()->getStyle('A'.($line+$j).':C'.($line+$j))->applyFromArray($styleArray);
                $p  ->getActiveSheet()->getStyle('D'.($line+$j).':F'.($line+$j))->applyFromArray($styleArray);
                $p  ->getActiveSheet()->getStyle('G'.($line+$j).':I'.($line+$j))->applyFromArray($styleArray);
                $p  ->getActiveSheet()->mergeCells( 'B'.($line+$j).':C'.($line+$j));
                $p  ->getActiveSheet()->mergeCells( 'E'.($line+$j).':F'.($line+$j));
                $p  ->getActiveSheet()->mergeCells( 'H'.($line+$j).':I'.($line+$j));
            }
            $line += 7;
        }
        
        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Content-Type:application/octet-stream");
        header('content-Type:application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition:attachment;filename='.$stuinfo["student"].'-'.$stuinfo["studentname"].'-预科成绩单.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    public function downProScoreA(){
        $php_path = dirname(__FILE__) . '/';
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';

        //导出字母制成绩
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        // Vendor('PHPExcel');
        $titlepic = '/buaahnd/sys/Tpl/Public/download/proscore.xls';
        $excelurl = $php_path .'../../../..'.$titlepic;
        $stuinfo = D("ClassstudentView")->where(array("student"=>$id))->find();
        if (!$stuinfo) {
            $this -> error('无此学生'.$id);
        }
        $p = PHPExcel_IOFactory::load($excelurl);//载入Excel
        if ($stuinfo["sex"] == "男") {
            $stuinfo["sex"] = "male";
        }elseif($stuinfo["sex"] == "女") {
            $stuinfo["sex"] = "female";
        }
        $p  ->setActiveSheetIndex(0)
            ->setCellValue('A3',"SCN：".$stuinfo["scnid"]. '        Name：'.$stuinfo["ename"]."      Major：".$stuinfo["majore"]."\nCollege(Department）: Beihang  University  HND  Programme");
        $scores = M("prograde")->order("term,courseid")->where(array("stunum"=>$id))->select();//选出所有考试的分数
        foreach ($scores as $vs) {//对数据进行初步处理
            if ($willwrite[$vs["term"]][$vs["course"]]["isrepair"] == "repair") {
                continue;
            }
            $willwrite[$vs["term"]][$vs["course"]]["count"]++;
            $willwrite[$vs["term"]][$vs["course"]]["hundred"] += $vs["hundred"];
            if ($vs["letter"] == "U" || $vs["hundred"] == 0) {//接下来判断这是重修或是导入时输入字母还是数字
                $willwrite[$vs["term"]][$vs["course"]]["isrepair"] = "repair";
                $willwrite[$vs["term"]][$vs["course"]]["hundred"] = 0;
            }else{
                if ($vs["letter"] == "P" || $vs["letter"] == "F") {
                    $willwrite[$vs["term"]][$vs["course"]]["standard"] = "hundred";
                }else{
                    $willwrite[$vs["term"]][$vs["course"]]["standard"] = "letter";
                }
            }
        }
        $line = 8;//从第8行开始写，每门课加1
        $row = 64;//从B列开始写，每学期加2
        $termnum = 1;
        $course = M("course");
        $termyear = 0;
            $p  ->setActiveSheetIndex(0)
                ->setCellValue("B6","2nd Year")
                ->setCellValue("D6","3rd Year")
                ->mergeCells("B6:C6")
                ->mergeCells("B7:E7")
                ->setCellValue("B7","HND Course")
                ->setCellValue("B8","Marks")
                ->setCellValue("C8","Credits")
                ->mergeCells("D6:E6")
                ->setCellValue("D8","Marks")
                ->setCellValue("E8","Credits");
        $hascourse = '';
        foreach ($willwrite as $termname => $vw) {
            $tmpyear = substr($termname, 0,9);
            if ($termyear != $tmpyear) {
                $termnum++;
                $row = $row + 2;
                $termyear = $tmpyear;
            }
            foreach ($vw as $coursename => $vs) {
                $line++;
                $map["classid"] = $stuinfo["classid"];
                $map["name|ename"] = $coursename;
                if ($hascourse == '') {
                    $hascourse .= $coursename;
                }else{
                    $hascourse .=",$coursename";
                }
                $credit = $course->where($map)->getField("credit");//获取学分
                if ($vs["hundred"] == 0) {//这里开始处理转化为字母的问题
                    $hundred = 0;
                    $letter = " ";
                }else{
                    $hundred = $vs["hundred"]/$vs["count"];
                    if (strstr($coursename, "Graded Unit")) {
                        switch (true) {
                            case $hundred > 70:
                                $letter = "A";
                                break;
                            case $hundred >60:
                                $letter = "B";
                                break;
                            case $hundred > 50:
                                $letter = "C";
                                break;
                            case $hundred > 1:
                                $letter = " ";
                                break;
                        }
                    }else{
                        if ($hundred >= 60) {
                            $letter = "P";
                        }else{
                            $letter = " ";
                        }
                    }
                }
                $p  ->setActiveSheetIndex(0)
                    ->setCellValue("A".$line,$coursename)
                    ->setCellValue(chr($row).$line,$letter)
                    ->setCellValue(chr($row+1).$line,$credit);
            }
        }
        $condition["classid"] = $stuinfo["classid"];
        $condition["ename"] = array("not in",$hascourse);
        $lastcourse = $course->where($condition)->order("id")->select();
        foreach ($lastcourse as $key => $va) {
            $line++;
            $p  ->setActiveSheetIndex(0)
                ->setCellValue("A".$line,$va["ename"])
                ->setCellValue("E".$line,$va["credit"]);
        }
        $styleThinBlackBorderOutline = array(
            'borders' => array (
                'allborders' => array (
                    'style' => PHPExcel_Style_Border::BORDER_THIN, //设置border样式
                    'color' => array ('argb' => 'FF000000'), //设置border颜色
                ),
            ),
        );
        $p->getActiveSheet()->getStyle('A6:'.'E'.$line)->applyFromArray($styleThinBlackBorderOutline);
        $p  ->setActiveSheetIndex(0)
            ->setCellValue("A".($line+2),'Note： 1. Year 2&Year 3,Unit Result of HND Course: P= Pass   F= Fail, U=Undertaken  ；2. Keys to Graded Unit:  A= 100%~70%         B=69%~60%       C=59%~50%          D（Fail）=  49% and below.')
            ->setCellValue("A".($line+3),'Entrepreneurship Management ＆ Training School     Beihang University')
            ->setCellValue("B".($line+4), date("Y-m-d" ,time()))
            ->mergeCells( "A".($line+2).':E'.($line+2))
            ->mergeCells( "A".($line+3).':E'.($line+3))
            ->mergeCells( "B".($line+4).':E'.($line+4))
            ->getRowDimension($line+2)->setRowHeight(32);
        $p  ->getActiveSheet()->getStyle("A".($line+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $p  ->getActiveSheet()->getStyle("A".($line+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $p  ->getActiveSheet()->getStyle("B".($line+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $p  ->getActiveSheet()->getStyle("A".($line+2))->getFont()->setName('Times New Roman');
        $p  ->getActiveSheet()->getStyle("A".($line+3))->getFont()->setName('Times New Roman');

        $filename=$stuinfo["student"].'-'.$stuinfo["studentname"].'-专业课成绩单（字母制）.xls';
        $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");
        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Content-Type:application/octet-stream");
        header('content-Type:application/vnd.ms-excel;charset=UTF-8');
        header('Content-Disposition:attachment;filename='.$filename);//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function downProScoreB(){
        $php_path = dirname(__FILE__) . '/';
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';

        //导出字母制成绩
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        // Vendor('PHPExcel');
        $titlepic = '/buaahnd/sys/Tpl/Public/download/proscore.xls';
        $excelurl = $php_path .'../../../..'.$titlepic;
        $stuinfo = D("ClassstudentView")->where(array("student"=>$id))->find();
        if (!$stuinfo) {
            $this -> error('无此学生'.$id);
        }
        $p = PHPExcel_IOFactory::load($excelurl);//载入Excel
        if ($stuinfo["sex"] == "男") {
            $stuinfo["sex"] = "male";
        }elseif($stuinfo["sex"] == "女") {
            $stuinfo["sex"] = "female";
        }
        $p  ->setActiveSheetIndex(0)
            ->setCellValue('A3',"SCN：".$stuinfo["scnid"]. '        Name：'.$stuinfo["ename"]."      Major：".$stuinfo["majore"]."\nCollege(Department）: Beihang  University  HND  Programme");
        $scores = M("prograde")->order("term,courseid")->where(array("stunum"=>$id))->select();//选出所有考试的分数
        foreach ($scores as $vs) {//对数据进行初步处理
            if ($willwrite[$vs["term"]][$vs["course"]]["isrepair"] == "repair") {
                continue;
            }
            $willwrite[$vs["term"]][$vs["course"]]["count"]++;
            $willwrite[$vs["term"]][$vs["course"]]["hundred"] += $vs["hundred"];
            if ($vs["letter"] == "U" || $vs["hundred"] == 0) {//接下来判断这是重修或是导入时输入字母还是数字
                $willwrite[$vs["term"]][$vs["course"]]["isrepair"] = "repair";
                $willwrite[$vs["term"]][$vs["course"]]["hundred"] = 0;
            }else{
                if ($vs["letter"] == "P" || $vs["letter"] == "F") {
                    $willwrite[$vs["term"]][$vs["course"]]["standard"] = "hundred";
                }else{
                    $willwrite[$vs["term"]][$vs["course"]]["standard"] = "letter";
                }
            }
        }
        $line = 8;//从第8行开始写，每门课加1
        $row = 64;//从B列开始写，每学期加2
        $termnum = 1;
        $course = M("course");
        $termyear = 0;
            $p  ->setActiveSheetIndex(0)
                ->setCellValue("B6","2nd Year")
                ->setCellValue("D6","3rd Year")
                ->mergeCells("B6:C6")
                ->mergeCells("B7:E7")
                ->setCellValue("B7","HND Course")
                ->setCellValue("B8","Marks")
                ->setCellValue("C8","Credits")
                ->mergeCells("D6:E6")
                ->setCellValue("D8","Marks")
                ->setCellValue("E8","Credits");
        $hascourse = '';
        foreach ($willwrite as $termname => $vw) {
            $tmpyear = substr($termname, 0,9);
            if ($termyear != $tmpyear) {
                $termnum++;
                $row = $row + 2;
                $termyear = $tmpyear;
            }
            foreach ($vw as $coursename => $vs) {
                $line++;
                $map["classid"] = $stuinfo["classid"];
                $map["name|ename"] = $coursename;
                if ($hascourse == '') {
                    $hascourse .= $coursename;
                }else{
                    $hascourse .=",$coursename";
                }
                $credit = $course->where($map)->getField("credit");//获取学分
                if ($vs["hundred"] == 0) {//这里开始处理转化为字母的问题
                    $hundred = 0;
                    $letter = "U";
                }else{
                    $hundred = $vs["hundred"]/$vs["count"];
                    $hundred = round($hundred,1);
                }
                if ($hundred < 60) {
                    $hundred = "";
                }
                $p  ->setActiveSheetIndex(0)
                    ->setCellValue("A".$line,$coursename)
                    ->setCellValue(chr($row).$line,$hundred)
                    ->setCellValue(chr($row+1).$line,$credit);
            }
        }
        $condition["classid"] = $stuinfo["classid"];
        $condition["ename"] = array("not in",$hascourse);
        $lastcourse = $course->where($condition)->order("id")->select();
        foreach ($lastcourse as $key => $va) {
            $line++;
            $p  ->setActiveSheetIndex(0)
                ->setCellValue("A".$line,$va["ename"])
                ->setCellValue("E".$line,$va["credit"]);
        }
        $styleThinBlackBorderOutline = array(
            'borders' => array (
                'allborders' => array (
                    'style' => PHPExcel_Style_Border::BORDER_THIN, //设置border样式
                    'color' => array ('argb' => 'FF000000'), //设置border颜色
                ),
            ),
        );
        $p->getActiveSheet()->getStyle('A6:'.'E'.$line)->applyFromArray($styleThinBlackBorderOutline);
        $p  ->setActiveSheetIndex(0)
            ->setCellValue("A".($line+2),'Note： 1. Year 2&Year 3,Unit Result of HND Course: P= Pass   F= Fail, U=Undertaken  ；2. Keys to Graded Unit:  A= 100%~70%         B=69%~60%       C=59%~50%          D（Fail）=  49% and below.')
            ->setCellValue("A".($line+3),'Entrepreneurship Management ＆ Training School     Beihang University')
            ->setCellValue("B".($line+4), date("Y-m-d" ,time()))
            ->mergeCells( "A".($line+2).':E'.($line+2))
            ->mergeCells( "A".($line+3).':E'.($line+3))
            ->mergeCells( "B".($line+4).':E'.($line+4))
            ->getRowDimension($line+2)->setRowHeight(32);
        $p  ->getActiveSheet()->getStyle("A".($line+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $p  ->getActiveSheet()->getStyle("A".($line+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $p  ->getActiveSheet()->getStyle("B".($line+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $p  ->getActiveSheet()->getStyle("A".($line+2))->getFont()->setName('Times New Roman');
        $p  ->getActiveSheet()->getStyle("A".($line+3))->getFont()->setName('Times New Roman');

        $filename=$stuinfo["student"].'-'.$stuinfo["studentname"].'-专业课成绩单（百分制）.xls';
        $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");
        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Content-Type:application/octet-stream");
        header('content-Type:application/vnd.ms-excel;charset=UTF-8');
        header('Content-Disposition:attachment;filename='.$filename);//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    public function stuCommonCertification(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        $student=D('classstudent');
        $enroll=D('enroll');
        $class=D('class');
        $map['student']=$id;
        $map1['username']=$id;
        $stu=$student->where($map)->select();

        $mystuename = $stu[0][ename];
        $mystuename_begin = ucfirst(strtolower($mystuename));
        $len = strlen($mystuename);
        $position = 0;
        for($i=0;$i<$len;$i++){
            if($mystuename[$i]!=$mystuename_begin[$i]){
                $position = $i;
                break;
            }
        }
        $my_xing = substr($mystuename_begin,0,$position);
        $my_ming = substr($mystuename_begin,$position);
        $mystuename = $my_xing." ".ucfirst(strtolower($my_ming));

        $map2[id]=$stu[0]['classid'];
        $zsex='';
        $year='';
        $month='';
        $day='';
        $major='';
        $sex='';
        $birthday='';
        $majore='';
        $classinfo=$class->where($map2)->select();
        $stuinfo=$enroll->where($map1)->select();
        $current_grade=$this->getGrade(date('Y-m-d',time()),$classinfo[0][year]);
        $current_egrade=$this->getEgrade(date('Y-m-d',time()),$classinfo[0][year]);
        if($stuinfo[0][sex]==''){
            $zsex='______'.'(性别)';$sex='______'.'(sex)';
        }else{
            $zsex=$stuinfo[0][sex];$sex=$this->getSex($stuinfo[0][sex]);
        }
        if($stuinfo[0][birthday] == '' || $stuinfo[0][birthday] == '0000-00-00 00:00:00'){
            $year='____';$month='____';$day='____';$birthday='____________________';
        }else{
            $year=substr($stuinfo[0][birthday],0,4);
            $month=substr($stuinfo[0][birthday],5,2);
            $day=substr($stuinfo[0][birthday],8,2);
            $birthday=date('M.j,Y',strtotime($stuinfo[0][birthday]));
        }
        if($classinfo[0][major] == ''){
            $major='______________________________________';
            $item = '_________________';
            $iteme = '_________________';
        }else{
            $major=$classinfo[0][major];
            $item = M("major")->where(array("major"=>$major))->find();
            if ($item["item"] == "HND") {
                $item = "英国高等教育文凭";
                $iteme = "SQA HND programme";
            }elseif ($item["item"] == "美国2+2") {
                $item = "美国高等教育项目";
                $iteme = "American Higher Education Project(AHEP)";
            }
        }
        if($classinfo[0][majore] == ''){
            $majore='_____________________________________';
        }else{
            $majore=$classinfo[0][majore];
        }
        $this->assign("item",$item);
        $this->assign("iteme",$iteme);
        $this->assign('zsex',$zsex);
        $this->assign('year',$year);
        $this->assign('month',$month);
        $this->assign('day',$day);
        $this->assign('major',$major);
        $this->assign('sex',$sex);
        $this->assign('majore',$majore);
        $this->assign('current_grade',$current_grade);
        $this->assign('now',date('Y-m-d',time()));
        $this->assign('current_time',date('M.j,Y',time()));
        $this->assign('birthday',$birthday);
        $this->assign('current_egrade',$current_egrade);
        $this->assign('stu',$stu);
        $this->assign('mystuename',$mystuename);
        $this->assign('stuinfo',$stuinfo);
        $this->assign('classinfo',$classinfo);
        $this->assign('id',$id);
        $this->stuCommonMenu($id);
        if($stuinfo[0][sex]==''||$stuinfo[0][birthday]=='0000-00-00 00:00:00'||$classinfo[0][major]==''||$classinfo[0][majore]==''){
            $tips='下划线处信息不全，请下载后补全!';
        }
        $this->assign('tips',$tips);
        $this -> display();
    }
    public function stuCommonHomework() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
            $map['title|coursename|ttruename'] =array('like','%'.$searchkey.'%');
            $this->assign('searchkey',$searchkey);
        }
        $HomeworkView = D("HomeworkView");
        $map['susername']=$id;
        $count = $HomeworkView -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
            $p = new Page($count, $listRows);
            $my = $HomeworkView -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
             $this->stuCommonMenu($id);
            $this -> assign('my', $my);         
            $this -> assign('uid', $id);            
        } 
        $this -> display();
    }
    public function stuCommonHomeworkSub() {
        $id = $_GET['id'];
        $userid=$_GET['userid'];
        if (!isset($id)||!isset($userid)) {
            $this -> error('参数缺失');
        } 
        $dao = D('HomeworkView');
        $map['id'] = $id;
        $map['susername']=$userid;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this->HomeworkSubMenu($userid,'stuCommonHomework');
            $this -> assign('my', $my);
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    function HomeworkSubMenu($id,$on){
        $menu['stuCommonScore']=' 成绩单';
        $menu['stuCommonCertification']='在读证明';
        $menu['stuCommonHomework']='作业情况';
        $menu['stuCommonAttend']='考勤记录';
        $menu['stuCommonReward']='奖惩记录';
        $menu['stuCommonTalk']='谈话记录';
        $menu['stuCommonProcess']='留学进程';
        $menu['stuCommonInfo']='基本信息';
        $path = array();
        foreach($menu as $key => $value) {
            $is_on = ($key == $on)?' class="on" ':'';
            $url_plus=empty($id)?'':'/id/'.$id;
            $path[] = '<a href="__URL__/' . $key.$url_plus . '" ' . $is_on . '>' . $value . '</a>';
        } 
        $in= implode(' | ', $path);
        $this->assign('menu',$in);  
    }
    public function stuCommonAttend() {
        $id =$this->stuCommonIsOwnByTeacher();
        $Attend = D("Attend");
        $map['susername']=$id;
        $count = $Attend -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
            $p = new Page($count, $listRows);
            $my = $Attend -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $truantSum=$Attend->where($map)->sum("truant");
            $tvacateSum=$Attend->where($map)->sum("tvacate");
            $svacateSum=$Attend->where($map)->sum("svacate");
            $lateSum=$Attend->where($map)->sum("late");
            $this -> assign("truantSum", $truantSum);
            $this -> assign("tvacateSum", $tvacateSum);
            $this -> assign("svacateSum", $svacateSum);
            $this -> assign("lateSum", $lateSum);
            $this -> assign("page", $page);
            $this -> assign('my', $my);         
        } 
        $this->stuCommonMenu($id);
        $this -> display();
    }
    public function attendUpdate() {
        $susername = $_POST['susername'];
        $struename = $_POST['struename'];
        $timezone = $_POST['timezone'];
        $content = $_POST['content'];
        if (empty($susername) || empty($struename)|| empty($timezone)) {
            $this -> error('必填项不能为空');
        } 
        $dao = D('attend');
        if ($dao -> create()) {
            $checked = $dao -> save();
            if ($checked > 0) {
                $this -> success('已成功保存');
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error($dao->getError());
        } 
    } 
    public function stuCommonTalk() {
        $id =$this->stuCommonIsOwnByTeacher();
        $Talk = D("Talk");
        $map['student']=$id;
        $count = $Talk -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
            $p = new Page($count, $listRows);
            $my = $Talk -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('time desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);     
        } 
        $this->stuCommonMenu($id);
        $this->assign('sid',$id);
        $this -> display();
    }
    public function stuCommonTalkInsert() {
        $sid=$_POST['sid'];
        $time=$_POST['time'];
        $talkContent=$_POST['talkContent'];
        $teacher=$_POST['teacher'];
        if(empty($time)||empty($talkContent)){
            $this->error("你还有必填项未填！");
        }
        if (!isset($sid)) {
            $this -> error('参数缺失');
        }
        $tusername=session('username');
        $ttruename=session('truename');
        if(!empty($teacher)){
            $user=D("User");
            $userinfo=$user->where("truename='".$teacher."'")->Field('username')->find();
            if($userinfo){
               $tusername=$userinfo['username'];
                $ttruename=$teacher;
            }else{
                 $this->error("未在资料库中找到该教师的信息，请确认后再尝试！");
            }
        }
                
        $Talk=D("Talk");
        $data['content']=$talkContent;
        $data['tusername']=$tusername;
        $data['ttruename']=$ttruename;
        $data['student']=$sid;
        $data['time']=$time;
        $check=$Talk->add($data);
        if($check!==false){
            $this->success("添加成功");
        }else{
            $this->error("添加失败");
        }
        
    }
    public function StuCommonTalkEdit(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Talk');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this->assign('time',substr($my['time'],0,10));
            $this -> assign('my', $my);
            $this->HomeworkSubMenu($my['student'],'stuCommonTalk');
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
        
    }
    public function StuCommonAttendEdit(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('attend');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $my['time']=
            $this -> assign('my', $my);
            $this->HomeworkSubMenu($my['susername'],'stuCommonAttend');
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
        
    }
    public function stuCommonReward() {
        $id =$this->stuCommonIsOwnByTeacher();
        $Reward = D("Reward");
        $map['susername']=$id;
        $count = $Reward -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
            $p = new Page($count, $listRows);
            $my = $Reward -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);         
        } 
        $this->stuCommonMenu($id);
        $this -> display();
    }
    public function stuCommonGetSystem($category,$name) {
        $system = D("System");
        $temp = explode(',', $system -> where("category='" . $category . "' and name='" . $name . "'") -> getField("content"));
        $a = array();
        foreach($temp as $key => $value) {
            $a[$value] = $value;
        } 
        return $a;
    } 
        public function stuCommonGrade(){
        $this->assign('id',$_GET['id']);
        $this->assign('info',$this->students());
        $this->display();
    }
     public function stuCommonClass(){
        $this->assign('id',$_GET['id']);
        $this->assign('class',$_GET['class']);
        $this->assign('info',$this->students());
        $this->display();
    }
    public function stuCommonInfo() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        $area = D("Area");
        $province = $area -> where("parent_id = 1") -> Field("region_name") -> select();
        $a = array();
        foreach($province as $key => $value) {
            $a[$value['region_name']] = $value['region_name'];
        }
        $is_or_not = array('是' => '是', '否' => '否');
        $education = $this -> stuCommonGetSystem("enroll","education");
        $entrancefull = $this -> stuCommonGetSystem("enroll","entrancefull");
        $englishfull = $this -> stuCommonGetSystem("enroll","englishfull");
        $mathfull = $this -> stuCommonGetSystem("enroll","mathfull");
        $abroad = $this -> stuCommonGetSystem("enroll","abroad");
        $coursewant = $this -> stuCommonGetSystem("enroll","coursewant");
        $englishtrain = $this -> stuCommonGetSystem("enroll","englishtrain");
        $sourcenewspaper = $this -> stuCommonGetSystem("enroll","sourcenewspaper");
        $sourcenet = $this -> stuCommonGetSystem("enroll","sourcenet");
        $nationality = $this -> stuCommonGetSystem("enroll","nationality");

        $this -> assign('a', $a);
        $this -> assign('is_or_not', $is_or_not);
        $this -> assign('education', $education);
        $this -> assign('entrancefull', $entrancefull);
        $this -> assign('englishfull', $englishfull);
        $this -> assign('mathfull', $mathfull);
        $this -> assign('abroad', $abroad);
        $this -> assign('coursewant', $coursewant);
        $this -> assign('englishtrain', $englishtrain);
        $this -> assign('sourcenewspaper', $sourcenewspaper);
        $this -> assign('sourcenet', $sourcenet);
        $this -> assign('nationality', $nationality);

        $Enroll = D('Enroll');
        $map['username'] = $id;
        $my = $Enroll -> where($map) -> find();
        if ($my) {
            $this -> assign('nativecity', $this -> getBrotherCity($my['nativecity']));
            $this -> assign('fcity', $this -> getBrotherCity($my['fcity']));
            $this -> assign('mcity', $this -> getBrotherCity($my['mcity']));
            $this -> assign('ocity', $this -> getBrotherCity($my['ocity']));
            $this -> assign('schoolcity', $this -> getBrotherCity($my['schoolcity']));
            $this -> assign('abroad_selected', explode(',', $my['abroad']));
            $this -> assign('newspaper_selected', explode(',', $my['sourcenewspaper']));
            $this -> assign('net_selected', explode(',', $my['sourcenet']));
            $this -> assign('infosource_selected', explode(',', $my['infosource']));
            $this -> assign('try', $my['try']);
            $this -> assign('paystatus',$my["paystatus"]);
        }
        $mbp["stunum"] = $id;
        $allpay = M("payment")->where($mbp)->select();
        $this->assign('allpay',$allpay);
        $allgrade = M("prograde")->where("stunum=$id")->select();
        foreach ($allgrade as $va) {
            $willgrade[$va["term"]][] = $va;
        }
        $this->assign("willgrade",$willgrade);
        $this -> assign('my', $my);
        $mapJ['susername']=$id;
        $list=M('judge')->where($mapJ)->select();
        $this->assign('list',$list);
        $this->stuCommonMenu($id);
        $this -> display();
    }
    public function city() {
        $bigid = $_GET["province"];
        if (isset($bigid)) {
            $area = D("Area");
            $bigid = $area -> where("parent_id = 1 and region_name = '$bigid'") -> getField('region_id');
            $province = $area -> where("parent_id = " . $bigid) -> Field("region_name") -> select();
            $this -> ajaxReturn($province, '成功', 1);
        } else {
            $this -> ajaxReturn(0, '获取信息失败', 0);
        } 
    } 
    public function checkEnrollPlus() {
        load("@.idcard");
        load("@.check");
        if (empty($_POST['truename'])) {
            $this -> error('Part1 学生姓名不能为空');
        }
        if (empty($_POST['username'])) {
            // $this -> error('Part1 学生学号不能为空');
        }elseif(!isusername($_POST['username'])){
          $this -> error('Part1 学生学号校验失败');  
        }
        if (empty($_POST['cyear'])) {
            // $this -> error('Part1 入学年份不能为空');
        }elseif (!isctime($_POST['cyear'])) {
            $this -> error('Part1 入学年份校验失败');
        }
        if (empty($_POST['sex'])) {
            // $this -> error('Part1 性别不能为空');
        }elseif (!issex($_POST['sex'])) {
            $this -> error('Part1 学生性别校验失败');
        }
        if (empty($_POST['bankcard'])) {
            // $this->error("Part1 学生银行卡号不能为空");
        }
        if (empty($_POST["onecard"])) {
            // $this->error("Part1 学生一卡通号不能为空");
        }
        if (empty($_POST['mobile'])) {
            // $this -> error('Part1 学生手机号不能为空');
        } elseif (!ismobile($_POST['mobile'])) {
            $this -> error('Part1 学生手机号校验失败');
        }
        if (!empty($_POST['qq'])) {
            if (!is_numeric($_POST['qq']))
                $this -> error('Part1 学生QQ号码校验失败');
        }
        if (!empty($_POST['email'])) {
            if (!isemail($_POST['email']))
                $this -> error('Part1 学生Email校验失败');
        }
        if (!empty($_POST['idcard'])) {
            if (!validation_filter_id_card($_POST['idcard']))
                $this -> error('Part1 身份证号码校验失败');
        }else{
            $this->error("Part1 身份证号码不能为空");
        }
        if (!empty($_POST['fmobile'])) {
            if (!ismobile($_POST['fmobile']))
                $this -> error('Part2 父亲手机号码校验失败');
        }
        if (!empty($_POST['mmobile'])) {
            if (!ismobile($_POST['mmobile']))
                $this -> error('Part2 母亲手机号码校验失败');
        }
        if (!empty($_POST['omobile'])) {
            if (!ismobile($_POST['omobile']))
                $this -> error('Part2 其他联系人手机号码校验失败');
        }         
        $this -> success('数据校验成功');
    }    

    public function insertPlus() {
        $dao = D("Enroll");
        $map["student"] = $dao->where(array("id"=>$_POST["id"]))->getField("username");
        if ($dao -> create()) {
            $dao -> abroad = implode(',', $_POST['abroad']);
            $dao -> infosource = implode(',', $_POST['infosource']);
            $dao -> sourcenewspaper = implode(',', $_POST['sourcenewspaper']);
            $dao -> sourcenet = implode(',', $_POST['sourcenet']);
            $checked = $dao->save();
            if ($checked>0) {
                if (!empty($_POST["username"])) {
                    M("classstudent")->where($map)->save(array("student"=>$_POST["username"]));
                }
                $this -> success('已成功保存');
            } else{
                $this -> error('没有更新任何数据');
            }
        } else {
            $this -> error($dao -> getError());
        }
    } 
    
    public function stuCommonProcess(){
        $id =$this->stuCommonIsOwnByTeacher();
        $dao=D('Abroad');
        $map['susername']=$id;
        $my=$dao->where($map)->find();
        $this->assign('my',$my);
        $school=D('Abroadschool')->where("abroadid=".$my['id'])->select();
        $this -> assign('school', $school);
        $this->stuCommonMenu($id);
        $this->display();

    }
    public function stuCommonLeft(){
        $searchkey = $_GET['searchkey'];
        if (isset($searchkey)) {
            $dao2 = D('ClassstudentView');
            $map2['studentname|ename|enamesimple']=array('like',"%$searchkey%");
            //$stu = $dao2->where($map2)->order('student asc')-> select();
            $stu = $dao2->query('SELECT u_classstudent.student, u_class.name, u_classstudent.studentname from u_classstudent, u_class where u_classstudent.classid=u_class.id and ( u_classstudent.enamesimple like "%'.$searchkey.'%" or u_classstudent.ename like "%'.$searchkey.'%" or  u_classstudent.studentname like "%'.$searchkey.'%" ) ORDER BY u_classstudent.student asc');
            $count=count($stu);
            $this -> assign('searchkey', $searchkey);
            $this -> assign('stu', $stu);
            $this -> assign('count', $count);
            $this->display();
        } else{
            $m["item"] = 'HND';//分项目的限制开始
            $major = M("major")->where($m)->select();
            foreach ($major as $vm) {
                $majors[$vm["major"]]=$vm["major"];
            }
            $map_class["major"] = array("in",$majors);//分项目的限制结束
            $grades = explode(",", $_SESSION["grade"]);
            $map_class["year"] = array("in",$grades);
            $dao_class = D('Class');
            $map_class['isbiye']=0;
            $dtree_class = $dao_class->where($map_class)->order('year desc,name asc')-> select();
            $dtree_year=$dao_class ->where($map_class)->field('year')-> group('year')->order('year desc')->select();
            $dao2 = D('ClassstudentView');
            $dtree_stu = $dao2->group("id")->order('student asc')-> select();
            $this->assign('dtree_year',$dtree_year);
            $this -> assign('dtree_class', $dtree_class);
            foreach ($dtree_stu as $k => $va) {
                if (empty($va["student"]) || empty($va["scnid"]) || empty($va["sex"]) || empty($va["bankcard"]) || empty($va["onecard"]) || empty($va["mobile"])) {
                    $dtree_stu[$k]["studentname"] .= '<span style="color:red;">*</span>';
                }
            }
            $this -> assign('dtree_stu', $dtree_stu);
            $this->display();
        }
    }
    public function stuCommonRight(){
        $test=$_GET['id'];
        $this -> assign('test', $test);
        $this->display();
    }
    
    public function courseware() {
        $this->assign('category_fortag',$this -> getSystem("courseware"));
        if (isset($_GET['searchkey'])) {
            $map['filename'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['category'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        }
        $dao = D('courseware');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> display(); 
    }
    public function getSystem($name) {
        $system = D("System");
        $temp = explode(',', $system -> where("category='source' and name='" . $name . "'") -> getField("content"));
        $a = array();
        foreach($temp as $key => $value) {
            $a[$value] = $value;
        } 
        return $a;
    } 
    public function download(){
        $id = $_GET['id'];
        $type = $_GET['type'];
        if (!isset($id)||!isset($type)) {
            $this -> error('参数缺失');
        } 
        $allow_type=array('music','courseware','software','ebook');
        if(in_array($type,$allow_type)==false){
            $this -> error('非法下载链接');
        }
        $dao = D($type);
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if($my){
            $filename=$my['filename'];//用户端显示的文件名 
            $php_path = dirname(__FILE__) . '/../../..'; //转化为物理路径
            $fileurl=$php_path.$my['fileurl'];
            $encoded_filename = urlencode($filename);
            $encoded_filename = str_replace("+", "%20", $encoded_filename);
            $downfile=$fileurl;//物理路径 
            $size=filesize($downfile);
            $file = @fopen($downfile,"r");
            if (!$file) {
            echo "file not found";
            } else {
            $HTTP_USER_AGENT=$_SERVER["HTTP_USER_AGENT"];
            $now = gmdate('D, d M Y H:i:s') . ' GMT';
            $mime_type='application/lrcfile'; 
            header('Content-Type: ' . $mime_type);
            header('Expires: ' . $now);
            Header("Accept-Ranges: bytes"); 
            header('Content-Transfer-Encoding: binary');
            Header("Accept-Length: ".$size);
            header('Content-Length: '.$size);
            if (strstr($HTTP_USER_AGENT, 'compatible; MSIE ') !== false && strstr($HTTP_USER_AGENT, 'Opera') === false) {
               header("Content-Disposition: inline; filename=$encoded_filename");
               header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
               header('Pragma: public');
            } else {
               header("Content-Disposition: attachment; filename=\"$filename\"");
               header("Content-Type: $mime_type; name=\"$filename\"");
            }
            while (!feof ($file)) {
               echo fread($file,1000);
            }
            fclose ($file);
            $dao->where($map)->setInc('hit'); 
            }
        }
    }
    public function downScore(){
        $id = $_GET['id'];
        $term = $_GET['term'];
        $a=array('Fall','Spring');
        $b=array('一','二');
        $c=array('1st','2nd');
        $printtime=date('M.j,Y',time());
        $printztime=date("Y年n月j日",time());
        if (!isset($id)||!isset($term)) {
            $this -> error('参数缺失');
        }
        $classstudent=D("ClassstudentView");
        $student=$classstudent->Field("name,studentname,major,majore,ename,year,student")->where("student ='".$id."'")->find();

        $mystuename = $student[ename];
        $mystuename_begin = ucfirst(strtolower($mystuename));
        $len = strlen($mystuename);
        $position = 0;
        for($i=0;$i<$len;$i++){
            if($mystuename[$i]!=$mystuename_begin[$i]){
                $position = $i;
                break;
            }
        }
        $my_xing = substr($mystuename_begin,0,$position);
        $my_ming = substr($mystuename_begin,$position);
        $mystuename = $my_xing." ".ucfirst(strtolower($my_ming));



        $Score = D("Score");
        $map['susername']=$id;
        $map['isvisible']=1;
//      $map['ispublic']=1;
        $allTerm = $Score -> where($map)->field('term')->group('term')-> select();
        foreach($allTerm as $k => $v){
            $year[$v['term']] = ceil(($k+1)/2)-1;
        }
        $map['term']=$term;
        $myclass=$Score -> where($map)-> select();
        $Hours_num =$Score -> where($map)-> sum('coursetime');
        $credit_num =$Score -> where($map)-> sum('credit');
        
       
       
        include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.class.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("NJU-HND auto generated Document");
        $styleA1= array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold' => true,
                'size' => 14,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
               'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleA2=array(
            'font'=>array(
                'name'=>'Times New Roman',
                'size'=>10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
            
        );
        $styleCourse= array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 14,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleYear= array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleCommon= array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 9,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
         $styleEnd=array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 9,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleLast=array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $zstyleA1= array(
            'font' => array(
                'name'=>'宋体',
                'bold' => true,
                'size' => 18,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
               'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $zstyle23=array(
            'font'=>array(
                'name'=>'宋体',
                'size'=>10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
            
        );
        $zstyle45=array(
            'font'=>array(
                'name'=>'宋体',
                'size'=>11,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
            
        );
        $zstyleCourse= array(
            'font' => array(
                'name'=>'宋体',
                'size' => 14,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $zstyleCommon= array(
            'font' => array(
                'name'=>'宋体',
                'size' => 9,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
          $zstyleEnd=array(
            'font' => array(
                'name'=>'宋体',
                'size' => 9,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $zstyleLast=array(
            'font' => array(
                'name'=>'宋体',
                'size' => 12,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $fstyleA2=array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold'=>true,
                'size' => 14,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
         $fstyleA3=array(
            'font' => array(
                'name'=>'宋体',
                'bold'=>true,
                'size' => 14,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $fstyleA4=array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold'=>true,
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $fstyleA5=array(
            'font' => array(
                'name'=>'宋体',
                'bold'=>true,
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $fstyleCommon=array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $fstyleHead=array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold'=>true,
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $fstyleCourse=array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $fstyleBottom4Left=array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold'=>true,
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
         $fstyleBottom4Right=array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold'=>true,
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $fstyleBottom2=array(
            'font' => array(
                'name'=>'宋体',
                'bold'=>true,
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
         $fstyleLast=array(
            'font' => array(
                'name'=>'宋体',
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $fstyleOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                ),
            ),
        );

            
        $objPHPExcel->createSheet();
        if(substr($student['name'],7,3) =='2+2'){
            $actSheet=$objPHPExcel->getSheet(0);
            $actSheet->setTitle("英文");
            $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
            $actSheet->getPageMargins()->setTop(0.4/2.54);
            $actSheet->getPageMargins()->setRight(0.5/2.54);
            $actSheet->getPageMargins()->setLeft(0.8/2.54);
            $actSheet->getPageMargins()->setBottom(0.4/2.54);
            $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $actSheet->getPageSetup()->setHorizontalCentered(true);
            $actSheet-> getColumnDimension('A') -> setwidth(41.75);
            $actSheet-> getColumnDimension('B') -> setwidth(14);
            $actSheet-> getColumnDimension('C') -> setwidth(14);
            $actSheet-> getColumnDimension('D') -> setwidth(14);
            $actSheet->getRowDimension('1')->setRowHeight(15.75);
            $actSheet->getRowDimension('2')->setRowHeight(18.75);
            $actSheet->getRowDimension('3')->setRowHeight(15.75);
            $actSheet->getRowDimension('4')->setRowHeight(15.75);
            $actSheet->getRowDimension('5')->setRowHeight(16.5);
            $actSheet->getRowDimension('6')->setRowHeight(14);
            $actSheet->getRowDimension('7')->setRowHeight(14);
            $actSheet->getRowDimension('8')->setRowHeight(14);
            $actSheet->mergeCells('A2:D2');
            $actSheet->mergeCells('A3:D3');
            $actSheet->mergeCells('A4:D4');
            $actSheet->mergeCells('A5:D5');
            $actSheet->mergeCells('A6:A8');
            $actSheet->mergeCells('B6:D6');
            $actSheet->mergeCells('B7:D7');
            $actSheet->setCellValue('A2',"Academic Record of Nanjing University");
            $actSheet->setCellValue('A3','南京大学学生成绩单');
            $actSheet->setCellValue('A4','Name:'.$mystuename.'   Student No.:'.$student['student'].'  Major: '.$student['majore'].'       Department: DAFLS');
            $actSheet->setCellValue('A5','姓名:'.$student['studentname'].'    学号：'.$student['student'].'                 专业：'.$student['major'].'                科系：大学外语部');
            $actSheet->setCellValue('A6','Subject of Course　（课程）');
            $actSheet->setCellValue('B6',$c[$year[$term]].' year  '.'（第'.$b[$year[$term]].'学年）');
            $actSheet->setCellValue('B7','Semester '.substr($term,-7,1).' 第'.$b[intval(substr($term,-7,1))-1].'学期');
            $actSheet->setCellValue('B8','Grade成绩');
            $actSheet->setCellValue('C8','Credit学分');
            $actSheet->setCellValue('D8','Hours学时');
            $temp = 9; //第9行开始写
            foreach($myclass as $my_key=>$my_value){
                $temp=$my_key+9;
                $actSheet->setCellValue('A'.$temp, $my_value['courseename'].'  （'.$my_value['coursename'].'）')
                     ->setCellValue('B'.$temp, $this->getStandards($term,$student['year'],$my_value['name'],$my_value['coursetime'],$my_value['credit'],$my_value['score']))
                    ->setCellValue('C'.$temp, $my_value['credit'])
                    ->setCellValue('D'.$temp, $my_value['coursetime']);
                $actSheet->getRowDimension($temp)->setRowHeight(14);
                if(strlen($my_value['courseename']) + 1.9*mb_strlen('  （'.$my_value['coursename'].'）','utf-8')> 62){
                                $actSheet->getRowDimension($temp)->setRowHeight(21);
                                $actSheet ->getStyle('A'.$temp)->getAlignment()->setWrapText(true);//自动换行
                            }
            }
            $end_row=count($myclass)+8;
            $bottom4=$end_row+1;
            $bottom3=$end_row+2;
            $bottom2=$end_row+3;
            $foot = $end_row + 4;
            $actSheet->setCellValue('A'.$bottom4, 'Total hours:　'.'（总学时：'.$Hours_num.'）');
            $actSheet->mergeCells('B'.$bottom4.':D'.$bottom4);
            $actSheet->mergeCells('A'.$bottom3.':D'.$bottom3);
            $actSheet->mergeCells('A'.$bottom2.':D'.$bottom2);
            $actSheet->mergeCells('A'.$foot.':D'.$foot);
            $actSheet->setCellValue('B'.$bottom4, 'Total credits:　'.'（总学分：'.$credit_num.'）');
            $actSheet->setCellValue('A'.$bottom3, 'Note: the full mark of each subject is 100（注：每门课总分为100分）');
            $actSheet->setCellValue('A'.$bottom2, '南京大学 大学外语部 Department of Applied Foreign Language Studies, Nanjing University');
            $actSheet->setCellValue('A'.$foot, '日期：'.$printztime.'      Date: '.$printtime);
            $actSheet->getStyle('A2')->applyFromArray($fstyleA2);
            $actSheet->getStyle('A3')->applyFromArray($fstyleA3);
            $actSheet->getStyle('A4')->applyFromArray($fstyleA4);
            $actSheet->getStyle('A5')->applyFromArray($fstyleA5);
            $actSheet->getStyle('A9:A'.$end_row)->applyFromArray($fstyleCourse);
            $actSheet->getStyle('A6:D8')->applyFromArray($fstyleHead);
            $actSheet->getStyle('B9:D'.$end_row)->applyFromArray($fstyleCommon);
            $actSheet->getStyle('A6:D'.$end_row)->applyFromArray($fstyleOutline);
            $actSheet->getStyle('A'.$bottom4)->applyFromArray($fstyleBottom4Left);
            $actSheet->getStyle('B'.$bottom4)->applyFromArray($fstyleBottom4Right);
            $actSheet->getStyle('A'.$bottom3)->applyFromArray($fstyleBottom4Left);
            $actSheet->getStyle('A'.$bottom2)->applyFromArray($fstyleBottom2);
            $actSheet->getStyle('A'.$foot)->applyFromArray($fstyleLast);
            
            
            }else{
            $actSheet=$objPHPExcel->getSheet(0);
            $actSheet->setTitle("英文");
            $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
            $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $actSheet->getPageMargins()->setTop(1.2/2.54);
            $actSheet->getPageSetup()->setHorizontalCentered(true);
            $actSheet-> getColumnDimension('A') -> setwidth(42);
            $actSheet-> getColumnDimension('B') -> setwidth(19);
            $actSheet-> getColumnDimension('C') -> setwidth(19);
            $actSheet-> getColumnDimension('D') -> setwidth(19);
            $actSheet->getRowDimension('1')->setRowHeight(19);
             $actSheet->getRowDimension('2')->setRowHeight(19);
            $actSheet->mergeCells('A1:D1');
            $actSheet->mergeCells('A2:D2');
            $actSheet->mergeCells('A3:A5');
            $actSheet->mergeCells('B3:D3');
            $actSheet->mergeCells('B4:D4');
            $actSheet->setCellValue('A1',"Nanjing University Student's Academic Record");
            $actSheet->setCellValue('A2',"No:".$student['student']."       "."Name:".$mystuename."        "."Major:".$student[majore]."        "."Department:DAFLS"."        "."Higher National Diploma Program");
            $actSheet->setCellValue('B3',"Academic Year ".substr($term,0,9));
            $actSheet->setCellValue('B4','The '.$a[intval(substr($term,-7,1))-1].' Term');
            $actSheet->getStyle('A2')->applyFromArray($styleA2);
            $actSheet->getStyle('A1:D1')->applyFromArray($styleA1);
            $actSheet->setCellValue('A3', 'Course')
                    ->setCellValue('B5', 'Score')
                    ->setCellValue('C5', 'Period')
                    ->setCellValue('D5', 'Credit');

            foreach($myclass as $my_key=>$my_value){
                $temp=$my_key+6;//从第6行开始写
                $actSheet->setCellValue('A'.$temp, $my_value['courseename'])
                     ->setCellValue('B'.$temp, $this->getStandards($term,$student['year'],$my_value['name'],$my_value['coursetime'],$my_value['credit'],$my_value['score']))
                    ->setCellValue('C'.$temp, $my_value['coursetime'])
                    ->setCellValue('D'.$temp, $my_value['credit']);
                    
                $actSheet->getRowDimension($temp)->setRowHeight(14);
            }
            $end_row=count($myclass)+6;
            $row=$end_row-1;
            $foot = $end_row + 1;
            $actSheet->getRowDimension($end_row)->setRowHeight(45);
            $actSheet->mergeCells('A'.$end_row.':'.'D'.$end_row);
            $actSheet->mergeCells('A'.$foot.':'.'D'.$foot);
            $actSheet ->getStyle('A'.$end_row)->getAlignment()->setWrapText(true);//自动换行 
            $actSheet->setCellValue('A'.$end_row,'Note:  The full mark of each course is 100,60 equals Pass.   Year 2 and 3, Credit Value requires at least 40 periods per semester for compulsory units. U= undertaken.');
            $actSheet->setCellValue('A'.$foot,"Department of Applied Foreign Language Studies\n                                                                                Nanjing University\n"                                                                               .$printtime);
            
            $actSheet->getStyle('A2:D'.$row)->applyFromArray($styleCommon);
            $actSheet->getStyle('A3')->applyFromArray($styleCourse);
            $actSheet->getRowDimension($foot)->setRowHeight(39.75);
            $actSheet->mergeCells('A'.$foot.':D'.$foot);
            $actSheet ->getStyle('A'.$foot)->getAlignment()->setWrapText(true);//自动换行 
            $actSheet->getStyle('A'.$foot)->applyFromArray($styleLast);
            $actSheet->getStyle('A'.$end_row.':D'.$end_row)->applyFromArray($styleEnd);
            
            $actSheet=$objPHPExcel->getSheet(1);
            $actSheet->setTitle("中文");
            $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
            //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
            $actSheet->getPageMargins()->setTop(1.2/2.54);
            $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $actSheet->getPageSetup()->setHorizontalCentered(true);
            $actSheet-> getColumnDimension('A') -> setwidth(21);
            $actSheet-> getColumnDimension('B') -> setwidth(16);
            $actSheet-> getColumnDimension('C') -> setwidth(16);
            $actSheet-> getColumnDimension('D') -> setwidth(16);
            $actSheet->getRowDimension('1')->setRowHeight(19);
            $actSheet->getRowDimension('2')->setRowHeight(19);
            
            $actSheet->mergeCells('A1:D1');
            $actSheet->mergeCells('A2:D2');
            $actSheet->mergeCells('A3:A5');
            $actSheet->mergeCells('B3:D3');
            $actSheet->mergeCells('B4:D4');
            $actSheet->setCellValue('B3',substr($term,0,9).'学年');
            $actSheet->setCellValue('A1','南京大学学生成绩单');
            $actSheet->setCellValue('B4','第'.$b[intval(substr($term,-7,1))-1].'学期');
            $actSheet->getStyle('A2')->applyFromArray($zstyle23);
            $actSheet->getStyle('A1:D1')->applyFromArray($styleA1);
            $actSheet->setCellValue('A3', '课程')
                    ->setCellValue('B5', '成绩')
                    ->setCellValue('C5', '学时')
                    ->setCellValue('D5', '学分');
            $actSheet->setCellValue('A2',"学号：".$student['student']."   "."姓名：".$student['studentname']."    "."专业：".$student[major]."   "."学院：大学外语部"."   "."英国高等教育文凭项目");
                        $actSheet->getStyle('A1:D1')->applyFromArray($zstyleA1);

            foreach($myclass as $my_key=>$my_value){
                $temp=$my_key+6;//从第6行开始写
                $actSheet->setCellValue('A'.$temp, $my_value['coursename'])
                     ->setCellValue('B'.$temp, $this->getStandards($term,$student['year'],$my_value['name'],$my_value['coursetime'],$my_value['credit'],$my_value['score']))
                    ->setCellValue('C'.$temp, $my_value['coursetime'])
                    ->setCellValue('D'.$temp, $my_value['credit']);
                    
                $actSheet->getRowDimension($temp)->setRowHeight(14);
            }
            $end_row=count($myclass)+6;
            $row=$end_row-1;
            $foot=$end_row+1;
            $actSheet->getRowDimension($end_row)->setRowHeight(50);
            $actSheet->mergeCells('A'.$end_row.':'.'D'.$end_row);
            $actSheet->mergeCells('A'.$foot.':'.'D'.$foot);
            $actSheet->getRowDimension($foot)->setRowHeight(29.25);
            $actSheet ->getStyle('A'.$end_row)->getAlignment()->setWrapText(true);//自动换行 
            $actSheet->setCellValue('A'.$end_row, '注：以上各科成绩评分标准为百分制，60分及格，100分为满分.第二学年和第三学年，必修课程每个学期1个学分至少40课时，U=在读。');
            
            $actSheet->getRowDimension('5')->setRowHeight(14);
            $actSheet->getStyle('B4:D4')->applyFromArray($zstyle45);
            $actSheet->getStyle('B3:D3')->applyFromArray($zstyle45);
            $actSheet->getStyle('A3')->applyFromArray($zstyleCourse);
            $actSheet->getStyle('A5:A'.$row)->applyFromArray($zstyle45);
            $actSheet->getStyle('B5:D'.$row)->applyFromArray($zstyleCommon);
            $actSheet->getStyle('A'.$end_row.':'.'D'.$end_row)->applyFromArray($zstyleEnd);
            $actSheet ->getStyle('A'.$foot)->getAlignment()->setWrapText(true);//自动换行 
            $actSheet->setCellValue('A'.$foot, "南京大学 大学外语部      \n".                                                                              $printztime);
            $actSheet->getStyle('A'.$foot.':'.'D'.$foot)->applyFromArray($zstyleLast);
        }
        $filename='成绩登记表['.$student['studentname'].'--'.$term.'].xls';
        $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
        public function downAllscore(){
            $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        $classstudent=D("ClassstudentView");
        $student=$classstudent->Field("name,studentname,majore,major,ename,year,student")->where("student ='".$id."'")->find();

            $mystuename = $student[ename];
            $mystuename_begin = ucfirst(strtolower($mystuename));
            $len = strlen($mystuename);
            $position = 0;
            for($i=0;$i<$len;$i++){
                if($mystuename[$i]!=$mystuename_begin[$i]){
                    $position = $i;
                    break;
                }
            }
            $my_xing = substr($mystuename_begin,0,$position);
            $my_ming = substr($mystuename_begin,$position);
            $mystuename = $my_xing." ".ucfirst(strtolower($my_ming));



            $Score=D("Score");
        $map['susername']=$id;
        $map['isvisible']=1;
    $a=array('一','二');//中文学期
    $b=array('Fall','Spring');//英文学期
    $d=array('1st','2nd');
    $printtime=date('M.j,Y',time());
    $printztime=date("Y年n月j日",time());
    $c=$this->headers();
    $list = $Score -> where($map) -> order('term asc') -> select();
    $Hours_num =$Score -> where($map)-> sum('coursetime');
    $credit_num =$Score -> where($map)-> sum('credit');
    foreach($list as $k => $v){
        $my[$v['term']][]=$v;
        
    }
    $term_num=count($my);
    $term_key = array_keys($my);
    foreach($term_key as $k => $v){
        $academicYear['Academic Year '.substr($v,0,9)][]= $v;
        $zacademicYear[substr($v,0,9).'学年'][]=$v;
        $facademicYear[$d[ceil(($k+1)/2)-1].' year  '.'（第'.$a[ceil(($k+1)/2)-1].'学年）'][]=$v;
    }
    $academicYear_key=array_keys($academicYear);
    $zacademicYear_key=array_keys($zacademicYear);
    $facademicYear_key=array_keys($facademicYear);
    $academicYear_num=count($academicYear);
    $fYearTerm_num=count($my[$term_key[0]])+count($my[$term_key[1]]);
    for($i=0;$i<$term_num;$i++){
        $allRecords +=  count($my[$term_key[$i]]);
    }
        include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.class.php';
        
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("NJU-HND auto generated Document");
       $styleA1= array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold' => true,
                'size' => 14,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
               'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleA2=array(
            'font'=>array(
                'name'=>'Times New Roman',
                'size'=>10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
            
        );
        $styleCourse= array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 14,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleYear= array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleCommon= array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 9,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
         $styleEnd=array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 9,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleLast=array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $zstyleA1= array(
            'font' => array(
                'name'=>'宋体',
                'bold' => true,
                'size' => 18,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
               'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $zstyle2=array(
            'font'=>array(
                'name'=>'宋体',
                'size'=>10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
            
        );
        $zstyle3=array(
            'font'=>array(
                'name'=>'宋体',
                'size'=>10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
            
        );
        $zstyle45=array(
            'font'=>array(
                'name'=>'宋体',
                'size'=>11,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
            
        );
        $zstyleCourse= array(
            'font' => array(
                'name'=>'宋体',
                'size' => 14,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $zstyleCommon= array(
            'font' => array(
                'name'=>'宋体',
                'size' => 9,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
          $zstyleEnd=array(
            'font' => array(
                'name'=>'宋体',
                'size' => 9,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $zstyleLast=array(
            'font' => array(
                'name'=>'宋体',
                'size' => 12,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $fstyleA2=array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold'=>true,
                'size' => 14,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
         $fstyleA3=array(
            'font' => array(
                'name'=>'宋体',
                'bold'=>true,
                'size' => 14,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $fstyleA4=array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold'=>true,
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $fstyleA5=array(
            'font' => array(
                'name'=>'宋体',
                'bold'=>true,
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $fstyleCommon=array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $fstyleHead=array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold'=>true,
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $fstyleCourse=array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $fstyleBottom4Left=array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold'=>true,
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
         $fstyleBottom4Right=array(
            'font' => array(
                'name'=>'Times New Roman',
                'bold'=>true,
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $fstyleBottom2=array(
            'font' => array(
                'name'=>'宋体',
                'bold'=>true,
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
         $fstyleLast=array(
            'font' => array(
                'name'=>'宋体',
                'size' => 8,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
         $fstyleBottom = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
                ),
            ),
        );
        $fstyleOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                ),
            ),
        );

        
            
        $objPHPExcel->createSheet();
        if(substr($student['name'],7,3) =='2+2'){
            $actSheet=$objPHPExcel->getSheet(0);
            $actSheet->setTitle("英文");
            $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
            //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
            $actSheet->getPageMargins()->setTop(0.4/2.54);
            $actSheet->getPageMargins()->setRight(0.5/2.54);
            $actSheet->getPageMargins()->setLeft(0.8/2.54);
            $actSheet->getPageMargins()->setBottom(0.4/2.54);
            $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $actSheet->getPageSetup()->setHorizontalCentered(true);
            $actSheet-> getColumnDimension('A') -> setwidth(42.4);
            for($i=1;$i<7;$i++){
                   $actSheet-> getColumnDimension($c[$i]) -> setwidth(7.6);
            } 
            for($i=2;$i<6;$i++){
                $actSheet-> mergeCells('A'.$i.':G'.$i);
            }
                
            $actSheet->getRowDimension('1')->setRowHeight(15.75);
            $actSheet->getRowDimension('2')->setRowHeight(18.75);
            $actSheet->getRowDimension('3')->setRowHeight(15.75);
            $actSheet->getRowDimension('4')->setRowHeight(15.75);
            $actSheet->getRowDimension('5')->setRowHeight(16.5);
            $actSheet->getRowDimension('6')->setRowHeight(14);
            $actSheet->getRowDimension('7')->setRowHeight(14);
            $actSheet->getRowDimension('8')->setRowHeight(14);
            $actSheet->setCellValue('A2',"Academic Record of Nanjing University");
            $actSheet->setCellValue('A3','南京大学学生成绩单');
            $actSheet->setCellValue('A4','Name:'.$mystuename.'   Student No.:'.$student['student'].'  Major: '.$student['majore'].'       Department: DAFLS');
            $actSheet->setCellValue('A5','姓名:'.$student['studentname'].'    学号：'.$student['student'].'                 专业：'.$student['major'].'                科系：大学外语部');
            $actSheet->getStyle('A2')->applyFromArray($fstyleA2);
            $actSheet->getStyle('A3')->applyFromArray($fstyleA3);
            $actSheet->getStyle('A4')->applyFromArray($fstyleA4);
            $actSheet->getStyle('A5')->applyFromArray($fstyleA5);
            $temp1 = 9;
            $temp2 = $fYearTerm_num+12;
            $recordCount = 8;
            $end_row = 8;
            if($term_num < 3){
                for($i=0;$i<$term_num;$i++){
                    $end_row += count($my[$term_key[$i]]);
                }
            }else{
                $end_row = count($my[$term_key[0]]) + count($my[$term_key[1]]) + 11;
                for($i=2;$i<$term_num;$i++){
                    $end_row += count($my[$term_key[$i]]);
                }
            }
             for($j=0;$j<$term_num;$j++){
                    $actSheet->mergeCells($c[3*($j-2*floor($j/2))+1].(7+($fYearTerm_num+3)*floor($j/2)).':'.$c[3*($j-2*floor($j/2))+3].(7+($fYearTerm_num+3)*floor($j/2)));
                    $actSheet->setCellValue($c[3*($j-2*floor($j/2))+1].(7+($fYearTerm_num+3)*floor($j/2)), 'Semester '.($j-2*floor($j/2)+1).' 第'.$a[$j-2*floor($j/2)].'学期');
                    $actSheet->setCellValue($c[3*($j-2*floor($j/2))+1].(8+($fYearTerm_num+3)*floor($j/2)),"Grade成绩");
                    $actSheet->setCellValue($c[3*($j-2*floor($j/2))+2].(8+($fYearTerm_num+3)*floor($j/2)),"Credit学分");
                    $actSheet->setCellValue($c[3*($j-2*floor($j/2))+3].(8+($fYearTerm_num+3)*floor($j/2)),"Hours学时");
                        foreach($my[$term_key[$j]] as $my_key=>$my_value){
                            if($j < 2){
                            $actSheet->setCellValue('A'.$temp1, $my_value['courseename'].'  （'.$my_value['coursename'].'）')
                            ->setCellValue($c[3*($j-2*floor($j/2))+1].$temp1, $this->getStandards($my_value['term'],$student['year'],$my_value['name'],$my_value['coursetime'],$my_value['credit'],$my_value['score']))
                            ->setCellValue($c[3*($j-2*floor($j/2))+2].$temp1, $my_value['credit'])
                            ->setCellValue($c[3*($j-2*floor($j/2))+3].$temp1, $my_value['coursetime']);
                            $actSheet->getRowDimension($temp1)->setRowHeight(14);
                            if(strlen($my_value['courseename']) + 1.9*mb_strlen('  （'.$my_value['coursename'].'）','utf-8')> 62){
                                $actSheet->getRowDimension($temp1)->setRowHeight(21);
                                $actSheet ->getStyle('A'.$temp1)->getAlignment()->setWrapText(true);//自动换行
                            }
                            $temp1++;
                        }
                    else{
                        $actSheet->setCellValue('A'.$temp2, $my_value['courseename'].'  （'.$my_value['coursename'].'）')
                            ->setCellValue($c[3*($j-2*floor($j/2))+1].$temp2, $this->getStandards($my_value['term'],$student['year'],$my_value['name'],$my_value['coursetime'],$my_value['credit'],$my_value['score']))
                            ->setCellValue($c[3*($j-2*floor($j/2))+2].$temp2, $my_value['credit'])
                            ->setCellValue($c[3*($j-2*floor($j/2))+3].$temp2, $my_value['coursetime']);
                            $actSheet->getRowDimension($temp2)->setRowHeight(14);
                            if(strlen($my_value['courseename']) + 1.9*mb_strlen('  （'.$my_value['coursename'].'）','utf-8')> 62){
                                $actSheet->getRowDimension($temp2)->setRowHeight(21);
                                $actSheet ->getStyle('A'.$temp2)->getAlignment()->setWrapText(true);//自动换行
                            }
                            $temp2++;
                    }
                }
            }
         for($i = 0;$i<$academicYear_num;$i++){
                $actSheet->mergeCells('B'.(6+$i*($fYearTerm_num+3)).':G'.(6+$i*($fYearTerm_num+3)));
                $actSheet->mergeCells('A'.(6+$i*($fYearTerm_num+3)).':A'.(8+$i*($fYearTerm_num+3)));
                $actSheet->setCellValue('B'.(6+$i*($fYearTerm_num+3)),$facademicYear_key[$i]);
                $actSheet->setCellValue('A'.(6+$i*($fYearTerm_num+3)),'Subject of Course　（课程）');
                $actSheet->getStyle('A'.(6+$i*($fYearTerm_num+3)).':G'.(8+$i*($fYearTerm_num+3)))->applyFromArray($fstyleHead);
                $actSheet->getStyle('A'.(9+$i*($fYearTerm_num+3)).':A'.($fYearTerm_num+8+$i*($allRecords-$fYearTerm_num+3)))->applyFromArray($fstyleCourse);
                $actSheet->getStyle('B'.(9+$i*($fYearTerm_num+3)).':G'.($fYearTerm_num+8+$i*($allRecords-$fYearTerm_num+3)))->applyFromArray($fstyleCommon);
                $actSheet->getStyle('A'.($recordCount+count($my[$term_key[0]])+$i*(count($my[$term_key[1]])+count($my[$term_key[2]]))).':G'.($recordCount+count($my[$term_key[0]])+$i*(3+count($my[$term_key[1]])+count($my[$term_key[2]]))))->applyFromArray($fstyleBottom);
                $actSheet->getStyle('A'.(6+($fYearTerm_num+3)*$i).':G'.(8+$fYearTerm_num+($allRecords-$fYearTerm_num+3)*$i))->applyFromArray($fstyleOutline);
            }
            $bottom4=$end_row+1;
            $bottom3=$end_row+2;
            $bottom2=$end_row+3;
            $foot = $end_row + 4;
            $actSheet->setCellValue('A'.$bottom4, 'Total hours:　'.'（总学时：'.$Hours_num.'）');
            $actSheet->mergeCells('B'.$bottom4.':G'.$bottom4);
            $actSheet->mergeCells('A'.$bottom3.':G'.$bottom3);
            $actSheet->mergeCells('A'.$bottom2.':G'.$bottom2);
            $actSheet->mergeCells('A'.$foot.':G'.$foot);
            $actSheet->setCellValue('B'.$bottom4, 'Total credits:　'.'（总学分：'.$credit_num.'）');
            $actSheet->setCellValue('A'.$bottom3, 'Note: the full mark of each subject is 100（注：每门课总分为100分）');
            $actSheet->setCellValue('A'.$bottom2, '南京大学 大学外语部 Department of Applied Foreign Language Studies, Nanjing University');
            $actSheet->setCellValue('A'.$foot, '日期：'.$printztime.'      Date: '.$printtime);
             $actSheet->getStyle('A'.$bottom4)->applyFromArray($fstyleBottom4Left);
            $actSheet->getStyle('B'.$bottom4)->applyFromArray($fstyleBottom4Right);
            $actSheet->getStyle('A'.$bottom3)->applyFromArray($fstyleBottom4Left);
            $actSheet->getStyle('A'.$bottom2)->applyFromArray($fstyleBottom2);
            $actSheet->getStyle('A'.$foot)->applyFromArray($fstyleLast);
            
        }else{
            $actSheet=$objPHPExcel->getSheet(0);
            $actSheet->setTitle("英文");
            $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
            $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $actSheet->getPageMargins()->setTop(0);
            $actSheet->getPageMargins()->setBottom(0);
            $actSheet->getPageSetup()->setHorizontalCentered(true);
            $actSheet-> getColumnDimension('A') -> setwidth(42);
            $actSheet->getRowDimension('1')->setRowHeight(18.75);
            $actSheet->getRowDimension('2')->setRowHeight(14.25);
            $actSheet->getRowDimension('3')->setRowHeight(14.25);
            $actSheet->getRowDimension('4')->setRowHeight(15.5);
            $actSheet->getRowDimension('5')->setRowHeight(14.25);
            $actSheet->mergeCells('A1:'.$c[3*$term_num].'1');
            $actSheet->mergeCells('A2:'.$c[3*$term_num].'2');
            $actSheet->mergeCells('A3:A5');
            $actSheet->setCellValue('A3', 'Course');
            
            $actSheet->setCellValue('A1',"Nanjing University Student's Academic Record");
             $actSheet->setCellValue('A2',"No:".$student['student']."         "."Name:".$mystuename."          "."Major:".$student['majore']."          "."Department:DAFLS"."          "."Higher National Diploma Program");
            $actSheet->getStyle('A1:'.$c[3*$term_num].'1')->applyFromArray($styleA1);
            $actSheet->getStyle('A2')->applyFromArray($styleA2);
            $temp=6;//从第6行开始写
                
                if($term_num % 2 == 0){
                    for($i=0;$i<$academicYear_num;$i++){
                       $actSheet->mergeCells($c[6*$i+1].'3:'.$c[6*$i+6].'3');
                        $actSheet->setCellValue($c[6*$i+1].'3', $academicYear_key[$i]);
                    }
                }else{
                    for($i=0;$i<$academicYear_num-1;$i++){
                       $actSheet->mergeCells($c[6*$i+1].'3:'.$c[6*$i+6].'3');
                        $actSheet->setCellValue($c[6*$i+1].'3', $academicYear_key[$i]);
                    }
                    $actSheet->mergeCells($c[6*$academicYear_num-5].'3:'.$c[6*$academicYear_num-3].'3');
                    $actSheet->setCellValue($c[6*$academicYear_num-5].'3', $academicYear_key[ceil($academicYear_num/2)]);
                }
                for($i=0;$i<$term_num;$i++){
                     if($term_num < 3){
                            $actSheet-> getColumnDimension(chr(65+(3*($i+1)-2))) -> setwidth(76.34/(3*$term_num));
                            $actSheet-> getColumnDimension(chr(65+(3*($i+1)-1))) -> setwidth(76.34/(3*$term_num));
                            $actSheet-> getColumnDimension(chr(65+(3*($i+1)))) -> setwidth(76.34/(3*$term_num));
                        }else{
                               $actSheet-> getColumnDimension(chr(65+(3*($i+1)-2))) -> setwidth(68/(3*$term_num-2)); 
                            $actSheet-> getColumnDimension(chr(65+(3*($i+1)-1))) -> setwidth(68/(3*$term_num-2));
                            $actSheet-> getColumnDimension(chr(65+(3*($i+1)))) -> setwidth(68/(3*$term_num-2));    
                        }
                    
                    foreach($my[$term_key[$i]] as $my_key=>$my_value){
                        $actSheet->mergeCells($c[3*$i+1].'4:'.$c[3*$i+3].'4');
                    $actSheet->setCellValue($c[3*$i+1].'4', 'The '.$b[$i%2].' Term');
                                    $actSheet->setCellValue(chr(65+(3*($i+1)-2)).'5',"Score");
                                    $actSheet->setCellValue(chr(65+(3*($i+1)-1)).'5',"Period");
                                    $actSheet->setCellValue(chr(65+(3*($i+1))).'5',"Credit");
                    $actSheet->setCellValue('A'.$temp, $my_value['courseename'])
                        ->setCellValue(chr(65+(3*($i+1)-2)).$temp, $this->getStandards($my_value['term'],$student['year'],$my_value['name'],$my_value['coursetime'],$my_value['credit'],$my_value['score']))
                        ->setCellValue(chr(65+(3*($i+1)-1)).$temp, $my_value['coursetime'])
                        ->setCellValue(chr(65+3*($i+1)).$temp, $my_value['credit']);
                        
                    $actSheet->getRowDimension($temp)->setRowHeight(14);
                    $temp++;
                }
                }
                $end_row=6;
                for($i=0;$i<$term_num;$i++){
                                 $end_row=$end_row+count($my[$term_key[$i]]);   
                }
                $row=$end_row-1;
                $foot = $end_row + 1;
                 $actSheet->getRowDimension($end_row)->setRowHeight(16);
            $actSheet->mergeCells('A'.$end_row.':'.chr(65+3*$term_num).$end_row);
            $actSheet ->getStyle('A'.$end_row)->getAlignment()->setWrapText(true);//自动换行 
            $actSheet->setCellValue('A'.$end_row, 'Note:  The full mark of each course is 100,60 equals Pass.   Year 2 and 3, Credit Value requires at least 40 periods per semester for compulsory units. U= undertaken.');
            $actSheet ->getStyle('A'.$end_row.':'.chr(65+3*$term_num).$end_row)->applyFromArray($styleEnd);
            for($i=2;$i<=$row;$i++){
                if($term_num == 1){
                   $actSheet->getRowDimension($i)->setRowHeight(14); 
                }elseif($term_num < 6){
                    $actSheet->getRowDimension($i)->setRowHeight(12);
                }else{
                   $actSheet->getRowDimension($i)->setRowHeight(11);  
                }
            }
            $actSheet->getStyle('A2:'.chr(65+3*$term_num).$row)->applyFromArray($styleCommon);
            $actSheet->getStyle('A3')->applyFromArray($styleCourse);
            $actSheet->getRowDimension($foot)->setRowHeight(36);
            $actSheet->mergeCells('A'.$foot.':'.chr(65+3*$term_num).$foot);
            $actSheet ->getStyle('A'.$foot)->getAlignment()->setWrapText(true);//自动换行 
            $actSheet->setCellValue('A'.$foot, "Department of Applied Foreign Language Studies\n                                                                                Nanjing University\n"                                                                               .$printtime);
            $actSheet->getStyle('A'.$foot)->applyFromArray($styleLast);
            
           
           
            $actSheet=$objPHPExcel->getSheet(1);
            $actSheet->setTitle("中文");
            $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
            $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $actSheet->getPageSetup()->setHorizontalCentered(true);
            $actSheet-> getColumnDimension('A') -> setwidth(17.13);
            $actSheet->getRowDimension('1')->setRowHeight(20.25);
            $actSheet->getRowDimension('2')->setRowHeight(14.25);
            $actSheet->getRowDimension('3')->setRowHeight(14.25);
            $actSheet->getRowDimension('4')->setRowHeight(15.5);
            $actSheet->getRowDimension('5')->setRowHeight(14.25);
            $actSheet->mergeCells('A1:'.$c[3*$term_num].'1');
            $actSheet->mergeCells('A2:'.$c[3*$term_num].'2');
            $actSheet->mergeCells('A3:A5');
            $actSheet->setCellValue('A3', '课程');
            $actSheet->setCellValue('A1',"南京大学学生成绩单");
             $actSheet->setCellValue('A2',"学号：".$student['student']."         "."姓名：".$student['studentname']."          "."专业：".$student['major']."          "."学院：大学外语部"."          "."英国高等教育文凭项目");
            $actSheet->getStyle('A1:'.$c[3*$term_num].'1')->applyFromArray($zstyleA1);
            $actSheet->getStyle('A2')->applyFromArray($zstyle2);
            $temp=6;//从第6行开始写
                
                if($term_num % 2 == 0){
                    for($i=0;$i<$academicYear_num;$i++){
                       $actSheet->mergeCells($c[6*$i+1].'3:'.$c[6*$i+6].'3');
                        $actSheet->setCellValue($c[6*$i+1].'3', $zacademicYear_key[$i]);
                    }
                }else{
                    for($i=0;$i<$academicYear_num-1;$i++){
                       $actSheet->mergeCells($c[6*$i+1].'3:'.$c[6*$i+6].'3');
                        $actSheet->setCellValue($c[6*$i+1].'3', $zacademicYear_key[$i]);
                    }
                    $actSheet->mergeCells($c[6*$academicYear_num-5].'3:'.$c[6*$academicYear_num-3].'3');
                    $actSheet->setCellValue($c[6*$academicYear_num-5].'3', $zacademicYear_key[ceil($academicYear_num/2)]);
                }
                for($i=0;$i<$term_num;$i++){
                     if($term_num < 3){
                            $actSheet-> getColumnDimension(chr(65+(3*($i+1)-2))) -> setwidth(96.34/(3*$term_num));
                            $actSheet-> getColumnDimension(chr(65+(3*($i+1)-1))) -> setwidth(96.34/(3*$term_num));
                            $actSheet-> getColumnDimension(chr(65+(3*($i+1)))) -> setwidth(96.34/(3*$term_num));
                        }else{
                               $actSheet-> getColumnDimension(chr(65+(3*($i+1)-2))) -> setwidth(82.08/(3*$term_num-2)); 
                               $actSheet-> getColumnDimension('B') -> setwidth(7.13); 
                               $actSheet-> getColumnDimension('E') -> setwidth(7.13); 
                            $actSheet-> getColumnDimension(chr(65+(3*($i+1)-1))) -> setwidth(82.08/(3*$term_num-2));
                            $actSheet-> getColumnDimension(chr(65+(3*($i+1)))) -> setwidth(82.08/(3*$term_num-2));    
                        }
                    
                    foreach($my[$term_key[$i]] as $my_key=>$my_value){
                        $actSheet->mergeCells($c[3*$i+1].'4:'.$c[3*$i+3].'4');
                    $actSheet->setCellValue($c[3*$i+1].'4', '第'.$a[$i%2].'学期');
                                    $actSheet->setCellValue(chr(65+(3*($i+1)-2)).'5',"成绩");
                                    $actSheet->setCellValue(chr(65+(3*($i+1)-1)).'5',"学时");
                                    $actSheet->setCellValue(chr(65+(3*($i+1))).'5',"学分");
                    $actSheet->setCellValue('A'.$temp, $my_value['coursename'])
                        ->setCellValue(chr(65+(3*($i+1)-2)).$temp, $this->getStandards($my_value['term'],$student['year'],$my_value['name'],$my_value['coursetime'],$my_value['credit'],$my_value['score']))
                        ->setCellValue(chr(65+(3*($i+1)-1)).$temp, $my_value['coursetime'])
                        ->setCellValue(chr(65+3*($i+1)).$temp, $my_value['credit']);
                        
                    $actSheet->getRowDimension($temp)->setRowHeight(11);
                    $temp++;
                }
                }
                $end_row=6;
                for($i=0;$i<$term_num;$i++){
                                 $end_row=$end_row+count($my[$term_key[$i]]);   
                }
                $row=$end_row-1;
                $foot = $end_row + 1;
                 $actSheet->getRowDimension($end_row)->setRowHeight(18);
            $actSheet->mergeCells('A'.$end_row.':'.chr(65+3*$term_num).$end_row);
            $actSheet ->getStyle('A'.$end_row)->getAlignment()->setWrapText(true);//自动换行 
            $actSheet->setCellValue('A'.$end_row, '注：以上各科成绩评分标准为百分制，60分及格，100分为满分.第二学年和第三学年，必修课程每个学期1个学分至少40课时，U=在读。');
            $actSheet ->getStyle('A'.$end_row.':'.chr(65+3*$term_num).$end_row)->applyFromArray($zstyleEnd);
            for($i=2;$i<=$row;$i++){
                if($term_num == 1){
                   $actSheet->getRowDimension($i)->setRowHeight(14); 
                }elseif($term_num < 6){
                    $actSheet->getRowDimension($i)->setRowHeight(12);
                }else{
                   $actSheet->getRowDimension($i)->setRowHeight(11);  
                }
            }
            $actSheet->getStyle('A2:'.chr(65+3*$term_num).'2')->applyFromArray($zstyle2);
            $actSheet->getStyle('B2:'.chr(65+3*$term_num).'3')->applyFromArray($zstyle3);
            $actSheet->getStyle('A3')->applyFromArray($zstyleCourse);
            $actSheet->getStyle('A4:'.chr(65+3*$term_num).'5')->applyFromArray($zstyle45);
            $actSheet->getStyle('A6:'.chr(65+3*$term_num).$row)->applyFromArray($zstyleCommon);
            $actSheet->getRowDimension($foot)->setRowHeight(28);
            $actSheet->mergeCells('A'.$foot.':'.chr(65+3*$term_num).$foot);
            $actSheet ->getStyle('A'.$foot)->getAlignment()->setWrapText(true);//自动换行 
            $actSheet->setCellValue('A'.$foot, "南京大学 大学外语部      \n".                                                                              $printztime);
            $actSheet->getStyle('A'.$foot)->applyFromArray($zstyleLast);
        }
                
            
        
        $filename='成绩登记表['.$student['studentname'].'--所有学期].xls';
        $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    public function downCertification(){
        /*$truename = $_GET['name'];
        if (!empty($truename)) {
            $this -> error('参数缺失');
        }*/
        $id = $_GET['id'];
        if (empty($id)) {
            $this -> error('参数缺失');
        }
        $student=D('classstudent');
        $enroll=D('enroll');
        $class=D('class');
        $map['student']=$id;
        $map1['username']=$id;
        $stu=$student->where($map)->select();

        $mystuename = $stu[0][ename];
        $mystuename_begin = ucfirst(strtolower($mystuename));
        $len = strlen($mystuename);
        $position = 0;
        for($i=0;$i<$len;$i++){
            if($mystuename[$i]!=$mystuename_begin[$i]){
                $position = $i;
                break;
            }
        }
        $my_xing = substr($mystuename_begin,0,$position);
        $my_ming = substr($mystuename_begin,$position);
        $mystuename = $my_xing." ".ucfirst(strtolower($my_ming));

        $map2[id]=$stu[0]['classid'];
        $classinfo=$class->where($map2)->select();
        $stuinfo=$enroll->where($map1)->select();
        $name=$stu[0][studentname];
        $zsex='';
        $year='';
        $month='';
        $day='';
        $major='';
        $sex='';
        $birthday='';
        $majore='';
        if($stuinfo[0][sex]==''){
            $zsex='______'."(性别)";$sex='____'."(sex)";
        }else{
            $zsex=$stuinfo[0][sex];$sex=$this->getSex($stuinfo[0][sex]);
        }
        if($stuinfo[0][birthday] == '' || $stuinfo[0][birthday] == '0000-00-00 00:00:00'){
            $year='____';$month='____';$day='____';$birthday='__________';
        }else{
            $year=substr($stuinfo[0][birthday],0,4);
            $month=substr($stuinfo[0][birthday],5,2);
            $day=substr($stuinfo[0][birthday],8,2);
            $birthday=date('M.j,Y',strtotime($stuinfo[0][birthday]));
        }
        if($classinfo[0][major] == ''){
            $major='________________________';
        }else{
            $major=$classinfo[0][major];
            $item = M("major")->where(array("major"=>$major))->find();
            if ($item["item"] == "HND") {
                $item = "英国高等教育文凭";
                $iteme = "SQA HND programme";
            }elseif ($item["item"] == "美国2+2") {
                $item = "美国高等教育项目";
                $iteme = "American Higher Education Project(AHEP)";
            }
        }
        if($classinfo[0][majore] == ''){
            $majore='__________________';
        }else{
            $majore=$classinfo[0][majore];
        }
        $enrollyear=$classinfo[0][year];
        $nowyear=substr(date('Y-m-d',time()),0,4);
        $nowmonth=substr(date('Y-m-d',time()),5,2);
        $nowday=substr(date('Y-m-d',time()),8,2);
        $current_time=date('M.j,Y',time());
        $current_grade=$this->getGrade(date('Y-m-d',time()),$classinfo[0][year]);
        $current_egrade=$this->getEgrade(date('Y-m-d',time()),$classinfo[0][year]);
        $ename=$stu[0][ename];
        include dirname(__FILE__).'/../../Lib/ORG/PHPWord.php';
        $PHPWord = new PHPWord();
        $section = $PHPWord->createSection();
        $zhongzhengwen = array('name'=>'Arial','size'=>'12');
        $PHPWord->addFontStyle('rStyle', array('bold'=>true,'name'=>'Arial','size'=>'15'));
        $PHPWord->addFontStyle('cStyle', array('name'=>'Arial','size'=>'12'));
        $PHPWord->addFontStyle('aStyle', array('name'=>'Times New Roman','size'=>'12'));
        $PHPWord->addParagraphStyle('pStyle', array('align'=>'center', 'spaceAfter'=>100));
        $PHPWord->addParagraphStyle('lStyle', array('align'=>'left', 'spaceAfter'=>100));
        $PHPWord->addParagraphStyle('YStyle', array('align'=>'right', 'spaceAfter'=>100));
        $section->addTextBreak(3);
        $section->addText('在读证明', array('bold'=>true,'name'=>'Arial','size'=>'15'), 'pStyle');
        $section->addTextBreak(2);
        $textrun = $section->createTextRun();
        $textrun->addText("　　"."兹证明",$zhongzhengwen);
        $textrun->addText($name,array('bold'=>true,'name'=>'Arial','size'=>'12'));
        $textrun->addText("同学，".$zsex."，".$year."年".$month."月".$day."日生，自".$enrollyear."年9月开始在我校学习“".$item."”课程就读专业为“".$major."”。",$zhongzhengwen);
        $section->addTextBreak(1);
        $section->addText('特此证明',$zhongzhengwen);
        $section->addTextBreak(2);
        $section->addText('北京航空航天大学创业管理培训学院',$zhongzhengwen,'YStyle');
        $section->addText($nowyear."年".$nowmonth."月".$nowday."日",$zhongzhengwen,'YStyle');
        $section->addTextBreak(3);
        $section->addText('On-Studying Certificate',array('bold'=>true,'name'=>'Times New Roman','size'=>'15'), 'pStyle');
        $section->addText("$current_time",array('name'=>'Times New Roman','size'=>'12'),'YStyle');
        $section->addTextBreak(3);
        $textrun = $section->createTextRun();
        $textrun->addText("This is to certify that student ",array('name'=>'Times New Roman','size'=>'12'));
        $textrun->addText($mystuename,array('bold'=>true,'name'=>'Times New Roman','size'=>'12'));
        $textrun->addText("," .$sex.", born on"." ".$birthday.", has been a student of ".$iteme." in the specialty of ".$majore." in the Entrepreneurship Management and Training School at our university since Sep".".".$enrollyear.".",array('name'=>'Times New Roman','size'=>'12'));
        $section->addTextBreak(1);
        $section->addText('Entrepreneurship Management and Training School',array('name'=>'Times New Roman','size'=>'12'), 'YStyle');
        $section->addText('Beihang University',array('name'=>'Times New Roman','size'=>'12'), 'YStyle');
        $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $filename='HND'.''.'在读证明-'.$name;
        $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition:attachment;filename=".$filename.".docx");
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit;
    }
    public function downGradeInfo(){
        $id=$_GET['id'];
        $info=explode(',',$_GET['key']);
        include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.class.php';
        
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("NJU-HND auto generated Document");
         $styleHead= array(
            'font' => array(
                'name'=>'宋体',
                'bold' => true,
                'size' => 12,
                'color'  => array(
                    'rgb' => 'FF0000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
               'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleClass= array(
            'font' => array(
                'name'=>'宋体',
                'bold' => true,
                'size' => 12
                ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
               'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
            $objPHPExcel->createSheet();
            $actSheet=$objPHPExcel->getSheet(0);
            $actSheet->setTitle("学员信息");
            $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
            $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $actSheet->getPageSetup()->setHorizontalCentered(true);
        $a=$this->students();
        $b=$this->headers();
        $info_num=count($info);
        $temp = 2;//第二行开始写 班级名称
        $stu=$this->getGradeStudent($id);
            foreach($info as $key=>$value){
                   $actSheet-> getColumnDimension($b[$key]) -> setwidth(51);
                    $actSheet->setCellValue($b[$key].'1',$a[$value]);
               
                }
            if($info_num < 27){
                $actSheet->getStyle('A1:'.chr(65+$info_num).'1')->applyFromArray($styleHead);
            }elseif($info_num <53){
              $actSheet->getStyle('A1:A'.chr(39+$info_num).'1')->applyFromArray($styleHead);  
            }else{
                $actSheet->getStyle('A1:B'.chr(13+$info_num).'1')->applyFromArray($styleHead);
            }
        foreach($stu as $v){
            $n += count($v);
        }
        foreach($stu as $k1 => $v1){
             if($temp != $n+2){
            $actSheet->mergeCells('A'.$temp.':'.$b[$info_num-1].$temp);
            $actSheet->setCellValue('A'.$temp,$k1);
            $actSheet->getStyle('A'.$temp.':'.$b[$info_num-1].$temp)->applyFromArray($styleClass);
            $temp++;
            }
            foreach($v1 as $k2=>$v2){
               foreach($info as $k3 => $v3){
                    $actSheet->setCellValue($b[$k3].$temp,$v2[$v3]);
                }
                $temp++;  
            }
        }
        $filename=$id.'级学员信息.xls';
        $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    public function downClassInfo(){
        $id=$_GET['id'];
        $class = $_GET['class'];
        $info=explode(',',$_GET['key']);
        include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.class.php';
        
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("NJU-HND auto generated Document");
         $styleHead= array(
            'font' => array(
                'name'=>'宋体',
                'bold' => true,
                'size' => 12,
                'color'  => array(
                    'rgb' => 'FF0000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
               'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleCell= array(
            'font' => array(
                'name'=>'宋体',
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
               'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
            $objPHPExcel->createSheet();
            $actSheet=$objPHPExcel->getSheet(0);
            $actSheet->setTitle("学员信息");
            $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
            $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $actSheet->getPageSetup()->setHorizontalCentered(true);
        $a=$this->students();
        $b=$this->headers();
        $info_num=count($info);
        $stu=$this->getClassStudent($id);
        $stu_num = count($stu);
        $temp = 2; //从第二行开始写
            foreach($info as $key=>$value){
                   $actSheet-> getColumnDimension($b[$key]) -> setwidth(51);
                    $actSheet->setCellValue($b[$key].'1',$a[$value]);
                }
            if($info_num < 27){
                $actSheet->getStyle('A1:'.chr(65+$info_num).'1')->applyFromArray($styleHead);
            }elseif($info_num <53){
              $actSheet->getStyle('A1:A'.chr(39+$info_num).'1')->applyFromArray($styleHead);  
            }else{
                $actSheet->getStyle('A1:B'.chr(13+$info_num).'1')->applyFromArray($styleHead);
            }
        
            foreach($stu as $k1 => $v1){
                foreach($info as $k2 => $v2){
                    $actSheet->setCellValue($b[$k2].$temp,$v1[$v2]);
                }
                $temp++;
            }
        $end_row=$stu_num = count($stu)+1;
        $actSheet->getStyle('A2:'.$b[$info_num-1].$end_row)->applyFromArray($styleCell);    
        $filename=$class.'学员信息.xls';
        $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit; 
    }
    public function menuReward() {
        $menu['reward']='所有奖惩记录';
        $menu['rewardAdd']='新建记录';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function reward() {
        if (isset($_GET['searchkey'])) {
            $map['struename|susername|tusername|ttruename|house|room|cell|bed|content'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        $map = array();
        $dao = D('eaterreward');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,susername asc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        }
        $this -> menudistribute();
        $this -> display();
    }     
    public function rewardEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('eaterreward');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> menudistribute();
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function rewardUpdate() {
        $dao = D('eaterreward');
        if ($dao -> create()) {
            $checked = $dao -> save();
            if ($checked > 0) {
                $this -> success('已成功保存');
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error($dao->getError());
        } 
    } 
    public function rewardAdd() {
        /*$dao1 = D('Class');
        $map1['isbiye']=0;
        $dtree_class = $dao1->where($map1)->order('year desc,name asc')-> select();
        $dtree_year=$dao1 ->where($map1)->field('year')-> group('year')->order('year desc')->select();
        $dao2 = D('ClassstudentView');
        $map2['isbiye']=0;
        $dtree_stu = $dao2->where($map2)->order('student asc')-> select();*/
        /*$dao1 = D("classstudent");
        $dao2 = D("Eaterreward");
        $dtree_house = $dao1->field("house")->group("house")->select();
        $dtree_home = $dao1->field("house,home")->group("house,home")->select();
        $dtree_cell = $dao1->field("home,cell")->group("house,home,cell")->select();

        $this->assign('dtree_year',$dtree_year);
        $this -> assign('dtree_class', $dtree_class);
        $this -> assign('dtree_stu', $dtree_stu);*/
        $this -> assign('ctime', date("Y-m-d H:i:s"));
        $this -> menudistribute();
        $this -> display();
    } 
    public function rewardDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('eaterreward');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function rewardInsert() {
        if(empty($_POST['content'])){
            $this -> error('内容不能为空');
        }
        $map["house"] = $_POST["house"];
        $map["room"] = $_POST["room"];
        $map["cell"] = $_POST["cell"];
        $map["bed"] = $_POST["bed"];
        $stuinfo = M("classstudent")->where($map)->find();
        if (!$stuinfo) {
            $this->error("此位置无学生");
        }
        $dao = D('eaterreward');
        $i=0;
        $j=0;
        // foreach($stu as $key=>$value){
            // if(substr($value,0,2)=='GJ'){         
                $i++;
                if ($dao -> create()) { 
                    $dao -> susername=$stuinfo["student"];
                    $dao -> struename=$stuinfo["studentname"];
                    $dao -> house = $stuinfo["house"];
                    $dao -> room = $stuinfo["room"];
                    $dao -> cell = $stuinfo["cell"];
                    $dao -> bed = $stuinfo["bed"];
                    $dao -> tusername=session('username');
                    $dao -> ttruename=session('truename');
                    $insertID = $dao -> add();
                    if ($insertID) {
                        $j++;
                    } else {
                        $this -> error('没有更新任何数据');
                    } 
                } else {
                    $this -> error($dao->getError());
                } 
            // }
        // }
        if($i==$j){
            $this -> success($stuinfo["studentname"].'的奖惩记录已记录');
        }else{
            $this -> error("新增记录失败");
        }
        
    } 
    public function menuBroken() {
        $menu['broken']='所有损坏记录';
        $menu['brokenAdd']='新增损坏记录';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function broken(){
        if (isset($_GET['searchkey'])) {
            $map['truename|username|tusername|ttruename|money|content'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        $map = array();
        $dao = D('broken');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,username asc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        }
        $this -> menudistribute();
        $this->display();
    }
    public function brokenAdd(){
        $this -> assign('ctime', date("Y-m-d H:i:s"));
        $this->menudistribute();
        $this->display();
    }
    public function brokenInsert() {
        if(empty($_POST['content'])){
            $this -> error('内容不能为空');
        }
        $map["student"] = $_POST["username"];
        $map["studentname|ename"] = $_POST["truename"];
        $stuinfo = M("classstudent")->where($map)->find();
        if (!$stuinfo) {
            $this->error("学生姓名/学号错误");
        }
        $dao = D('broken');
        $i=0;
        $j=0;
        // foreach($stu as $key=>$value){
            // if(substr($value,0,2)=='GJ'){         
                $i++;
                if ($dao -> create()) { 
                    $dao -> susername=$stuinfo["student"];
                    $dao -> struename=$stuinfo["studentname"];
                    $dao -> tusername=session('username');
                    $dao -> ttruename=session('truename');
                    $insertID = $dao -> add();
                    if ($insertID) {
                        $j++;
                    } else {
                        $this -> error('没有更新任何数据');
                    } 
                } else {
                    $this -> error($dao->getError());
                } 
            // }
        // }
        if($i==$j){
            $this -> success($stuinfo["studentname"].'的损坏记录已记录');
        }else{
            $this -> error("新增记录失败");
        }
        
    } 
    public function brokenDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('broken');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    }    
    public function brokenEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('broken');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> menudistribute();
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function brokenUpdate() {
        $dao = D('broken');
        if ($dao -> create()) {
            $checked = $dao -> save();
            if ($checked > 0) {
                $this -> success('已成功保存');
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error($dao->getError());
        } 
    } 
}
?>