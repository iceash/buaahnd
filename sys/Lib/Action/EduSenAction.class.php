<?php
class EduSenAction extends CommonAction {
    public function index() {
        $User = D('User');
        $map['username'] = session('username');
        $photo = $User->where($map)->getField('photo');
        $this->assign('photo',$photo);
        $Notice = D("Notice");
        $map="readusername='".session('username')."' and readtime is NULL";
        $count = $Notice -> where($map) -> count();
        $this->assign('count',$count);
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
    public function menunotice() {
        $menu['notice']='所有通知';
        $this->assign('menu',$this ->autoMenu($menu));  
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
    public function getEduDir($m){
        $num=$m->count();
        $str='';
        $list = $m->order('id DESC')->select();
        for($i=0;$i<$num-1;$i++){
            $str.=$list[$i][ttruename].',';
        }
        $str=$str.$list[$num-1][ttruename];
        $a=explode(',',$str);
        $allc=array();
        foreach($a as $key => $value) {
            $allc[$value] = $value;
        } 
        return $allc;
        
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
    public function students(){
        $a = array('name'=>'姓名','ename'=>'ename','birthday'=>'出生日期','sex'=>'性别','gender'=>'Gender','address'=>'家庭住址','HomeAddress'=>'HomeAddress','postaladdress'=>'通信地址','CorrespondenceAddress'=>'CorrespondenceAddress','phone'=>'固定电话','mobile'=>'手机','email'=>'Email1','Email2'=>'Email2','MSN'=>'MSN','qq'=>'OICQ','nativeprovince'=>'省份','Province'=>'Province','nativecity'=>'城市','City'=>'City','idcardpassport'=>'身份证护照号码','project'=>'就读国内项目名称','HNDCenter'=>'HNDCenter','year'=>'入学时间','grade'=>'所属年级','entrancescore'=>'高考成绩总分','englishscore'=>'英语单科成绩','entrancefull'=>'高考分数标准','major'=>'HND专业','drop'=>'是否退学','repeat'=>'是否留级','SCN'=>'SCN号','listeningscore'=>'听力得分','readingscore'=>'阅读得分','writingscore'=>'写作得分','speakingscore'=>'口语得分','testscore'=>'进入专业课英语成绩总分','score1'=>'最优有效雅思成绩','score1id'=>'雅思考试号','plus'=>'其他','HNDtime'=>'获得HND证书时间','quit'=>'是否留学','country'=>'留学国家','Country'=>'Country','school'=>'国外院校名称','ForeignUniversityApplied'=>'ForeignUniversityApplied','fmajor'=>'留学所学专业','together'=>'出国所经过中介名称','employ'=>'是否就业','enterprise'=>'就业企业名称','workaddress'=>'就业企业所在省市','enterprisenature'=>'就业企业性质','individualorientationandspecialty'=>'个人情况介绍及特长','professionalcertificate'=>'所获得职业资格证书','xuben'=>'续本','xubensch'=>'续本国内院校名称','degreesch'=>'将获得哪所院校颁发学位','xubenmajor'=>'续本专业');
        return $a;
    }
        public function getClassStudent($classid){
        $map['id'] = $classid;
        $dao = D('StudentView');
        $my = $dao->where($map)->order('student asc')->group('student')->select();
        //$my = $dao->where($map)->order('student asc')->select();
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
        $list = $dao->where($map)->group('student')->order('year desc, name asc,student asc')->select();
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
    public function notice() {
        if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
            $map['title|content'] =array('like','%'.$searchkey.'%');
            $this->assign('searchkey',$searchkey);
        }
        $Notice = D("NoticeView");
        $map['readusername']=session('username');
        $count = $Notice -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
            $p = new Page($count, $listRows);
            $my = $Notice -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);         
        } 
        $this -> display();
    }
    /*
    public function notice() {
        if (isset($_GET['searchkey'])) {
            $map['title|content'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        }
         if (isset($_GET['EduDir'])) {
            $map['ttruename'] = $_GET['EduDir'];
            $this -> assign('EduDir_current', $_GET['EduDir']);
        }    
        $dao = D('Noticecreate');
        $count = $dao ->where($map) ->count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
           // $my = $dao ->where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
           $my = $dao ->where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('istop desc,ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this->assign('allEduDir',$this->getEduDir($dao));
            $this -> assign('my', $my);
        } 
        $this -> menunotice();
        $this -> display();
    } */
    public function noticeDetails() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Noticecreate');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> menunotice();
            $this -> display();
        } else {
            $this -> error('该记录不存在或权限不足');
        } 
    } 
    public function getYear() {
        $a=array();
        $current_year=Date('Y');
        for($i=$current_year;$i>=2005;$i--){
            $a[$i]=$i;
        }
        return $a; 
    }
    public function getTerm() {
        $a=array();
        $current_year=Date('Y');
        for($i=$current_year;$i>=2005;$i--){
            for($j=2;$j>0;$j--){
                $temp=($i).'-';
                $temp.=($i+1).'学年第';
                $temp.=$j.'学期';
                $a[$temp]=$temp;
            }
        }
        return $a; 
    }
    public function menuClass() {
        $menu['uclass']='所有班级';
        $menu['uploadStu'] = '上传学生分班信息';
        //$menu['uclassAdd']='新建班级';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function menuCourse() {
        // $menu['subject']='课程与教师';
        // $menu['subjectAdd']='新建课程与教师的关联';
        $menu['course']='课程总库';
        // $menu['courseAdd']='新建课程';
        $menu["courseUpload"]='上传课程';
        $menu["schedule"]='查看课表';
        $menu["uploadSchedule"]='上传课表';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function menuGrade() {
        $menu['grade']='查看专业成绩';
        $menu['uploadProGrade']='导入专业成绩';
        $menu['usualGrade']='查看预科成绩(英语)';
        $menu['uploadPreGrade']='导入预科成绩(英语)';
        $menu['usualGrade2']='查看预科成绩(必修)';
        $menu['uploadPreGrade2']='导入预科成绩(必修)';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function menuReward() {
        $menu['reward']='所有奖惩记录';
        $menu['rewardAdd']='新建记录';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function menuStu() {
        $menu['stu']='学生首页';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function uclass() {
        $dao = D('Class');
        $dao2 = D('Classstudent');
        $this -> assign('category_fortag', $this->getYear());
        if (isset($_GET['searchkey'])) {
            $map['name'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['year'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        } 
        //$major = $dao->group("major")->field("major")->select();//专业列表
        $m["item"] = 'HND';
        $major = M("major")->where($m)->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $this->assign('major_fortag',$majors);

        if (isset($_GET['major'])) {
            $map['major'] = $_GET['major'];
            $this -> assign('major_current', $_GET['major']);
        } 
        $map["major"] = array("in",$majors);
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('year desc,ctime asc') -> select();
            foreach($my as $key=>$value){
                $map2['classid']=$value['id'];
                $my[$key]['count']=$dao2->where($map2)->count();
            }
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> menuClass();
        $this -> display();
    } 
    public function stuCommon() {
        $this -> display();
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
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $Score = D("prograde");
        $map['stunum']=$id;
        // $map['isvisible']=1;
        $term=$Score -> where($map) ->field('term')->group('term')->order('term asc')-> select();
        $term_num=count($term);
        // if($term_num>0){
            foreach($term as $key=>$value){
                $map['term']=$value['term'];
                $my[$key]=$Score -> where($map) -> select();
            }
            $this->assign('id',$id);//这里的id指的是学号
            $this->assign('my',$my);
        // }
        $this->stuCommonMenu($id);
        $this -> display();
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
    public function stuCommonAttend() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $Attend = D("Attend");
        $map['susername']=$id;
        $map1['susername']=$id;
        $count = $Attend -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
            $p = new Page($count, $listRows);
            $my = $Attend -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);         
        } 
         $rid=$Attend->where($map)->field('id')->order('ctime DESC') ->select();
            $rid_num=count($rid);
            $a=array();
            if($rid_num>0){
                foreach($rid as $key=>$value){
                $map['id']=$value['id'];
                $my[$key]=$Attend -> where($map)-> find();
                        if(!in_array(substr($my[$key]['timezone'],0,strripos($my[$key]['timezone'], '第')),$a)){
                         array_push($a,substr($my[$key]['timezone'],0,strripos($my[$key]['timezone'], '第')));
                     }
                }
            }
            $map1['timezone']=array('like',$a[0].'%');
            $truant_num=$Attend->where($map1)->sum('truant');
            $tvacate_num=$Attend->where($map1)->sum('tvacate');
            $svacate_num=$Attend->where($map1)->sum('svacate');
            $late_num=$Attend->where($map1)->sum('late');
            $this->assign('term',$a[0]);
            $this->assign('truant',$truant_num);
            $this->assign('tvacate',$tvacate_num);
            $this->assign('svacate',$svacate_num);
            $this->assign('late',$late_num);
        $this->stuCommonMenu($id);
        $this -> display();
    }
    public function stuCommonReward() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
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
        } 
        $this -> assign('my', $my);
        $mapJ['susername']=$id;
        $list=M('judge')->where($mapJ)->select();
        $this->assign('list',$list);
        $this->stuCommonMenu($id);
        $this -> display();
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
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
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
            $dtree_stu = $dao2->order('student asc')-> select();
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
    public function uclassAdd() {
        $this -> assign('category_fortag', $this->getYear());
        $this -> assign('mydate', date('Y-m-d H:i:s'));
        $isbiye_fortag=array();
        $isbiye_fortag['0']='未毕业';
        $isbiye_fortag['1']='已毕业';
        $this -> assign('isbiye_fortag',$isbiye_fortag);
        $this -> menuClass();
        $this -> display();
    } 
    public function subjectStuAdd() {
        $dao1 = D('Class');
        $dtree_class = $dao1->order('year desc,name asc')-> select();
        $dtree_year=$dao1 ->field('year')-> group('year')->order('year desc')->select();
        $dao2 = D('Classstudent');
        $dtree_stu = $dao2->order('student asc')-> select();
        $this->assign('dtree_year',$dtree_year);
        $this -> assign('dtree_class', $dtree_class);
        $this -> assign('dtree_stu', $dtree_stu);
        $this -> display();
    }
    public function uclassDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao1 = D('Classstudent');
        $map1['classid'] = array('in', $id);
        $my1=$dao1->where($map1)->select();
        if($my1){
            $this->error('删除失败！删除前请先将本班学生全部删除或转移到别的班级');
        }
        $dao2 = D('Classteacher');
        $my2=$dao2->where($map1)->select();
        if($my2){
            $this->error('删除失败！删除前请先将解除本班班主任的关联');
        }
        $dao = D('Class');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function uclassEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Class');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> assign('category_fortag', $this->getYear());
            // $isbiye_fortag=array();
            // $isbiye_fortag['0']='未毕业';
            // $isbiye_fortag['1']='已毕业';
            // $this -> assign('isbiye_fortag',$isbiye_fortag);
            $this -> menuClass();
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function uclassUpdate() {
        $dao = D('Class');
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
    public function rewardEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Reward');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> menuReward();
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function rewardUpdate() {
        $dao = D('Reward');
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
    public function uclassInsert() {
        $dao = D('Class');
        if ($dao -> create()) {
            $insertID = $dao -> add();
            if ($insertID) {
                $this -> ajaxReturn($insertID, '已成功保存！', 1);
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error($dao->getError());
        } 
    }   
    public function uclassTeacher() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $classname=D('Class')->Field('id,name')->where("id=$id")->find();
        $this -> assign('classname', $classname);
        $dao = D('Classteacher');
        $map['classid'] = $id;
        $my = $dao -> where($map) -> select();
        if ($my) {
            $b=array();
            foreach($my as $key => $value) {
                $b[] = $value['teacher'];
            } 
            $this -> assign('b', $b);
        } 
        $dao2=D('User');
        $map2['role']=array('like','%EduDir%');
        $teacher=$dao2-> where($map2) -> select();
        $a = array();
        foreach($teacher as $key => $value) {
            $a[$value['username']] = $value['truename'];
        } 
        $this -> assign('a', $a);
        $this -> assign('my2', $my2);
        $this -> menuClass();
        $this -> display();
    } 
    public function uclassTeacherUpdate() {
        $teacher = $_POST['teacher'];
        $id=$_POST['id'];
        $map['classid']=$id;
        $dao=D('Classteacher');
        $result=$dao->where($map)->delete();
        $i=0;
        if(!empty($teacher)){
            foreach($teacher as $key=>$value){
                $data['classid'] = $id;
                $data['teacher'] = $value;
                $dao->add($data);
                $i++;
            }
        }
        if($i==count($teacher)){
            $this -> success('已成功保存');
        }
    } 
    public function uclassStudent() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $classname=D('Class')->Field('id,name')->where("id=$id")->find();
        $this -> assign('classname', $classname);
        $dao = D('Classstudent');
        $map['classid'] = $id;
        $count = $dao -> where($map) -> count();
        $this -> assign("count", $count);
        if ($count>0) {
            import("@.ORG.Page");
            $listRows = 100;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('student asc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> menuClass();
        $this -> display();
    } 
    public function uclassStuInsert(){
        $dao = D('Classstudent');
        $studentname=$_POST['studentname'];
        if ($dao -> create()) {
            $map["idcard"] = $_POST["idcard"];
            $map["truename"] = $_POST["studentname"];
            if (M("enroll")->where($map)->count() == 0) {
                $this -> error('无此学生基本信息');
            }
            M("enroll")->where($map)->setField("username",$_POST["student"]);
            $insertID = $dao -> add();
            if ($insertID) {
                $this -> ajaxReturn($insertID, '已成功保存！', 1);
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error($dao->getError());
        } 
    }
    public function uclassStuInsertALot(){
        
        $titlepic = $_POST['titlepic'];
        $classid = $_POST['classidALot'];
        if (empty($titlepic) ) {
            $this -> error('未上传文件');
        } 
        if (substr($titlepic,-3,3) !=='xls') {
            $this -> error('上传的不是xls文件');
        } 
       
        $php_path = dirname(__FILE__) . '/';
        
        include $php_path .'../../Lib/ORG/PHPExcel.php';
       
        $inputFileName = $php_path .'../../..'.$titlepic;
        
        
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
      
        
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $count=count($sheetData);
        $dao=D('Classstudent');
        for($i=2;$i<=$count;$i++){
            $studentname=$sheetData[$i]['B'];
            $studentname=str_replace(" ","",$studentname);
            $map['student']=$sheetData[$i]['A'];
            $find=$dao->where($map)->find();
            if($find){
                $this->error('学号'.$sheetData[$i]['A'].'已经存在！学号必须是唯一的');
            }
            $pinyin=$this->getPinyin($studentname);
            $data_a[$i-2]['classid'] = $classid;
            $data_a[$i-2]['student'] = $sheetData[$i]['A'];
            $data_a[$i-2]['studentname'] = $studentname;
            $data_a[$i-2]['ename'] = $pinyin;
            $data_a[$i-2]['enamesimple'] = $this->getPinyinSimple($pinyin);
        }
        $dao -> addAll($data_a);
        $howmany=count($data_a);
        $this -> success("已成功保存$howmany条记录");
        
    }
    public function getPinyin($name){
        import("@.ORG.pinyin");
        $r = pinyin($name, true, true);
        return $r;
    }
    public function getPinyinSimple($name='LiSi'){//获得拼音的首字母
        $result='';
        $a=str_split($name);
        foreach($a as $chr){
            $temp=ord($chr);
            if($temp>64&&$temp<91){
                $result.=$chr;
            }
        }
        $result=strtolower($result);
        return $result;
    }
    public function uclassStuDel(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('Classstudent');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    }
    public function subjectStuDel(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('Score');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    }
     public function uclassStuEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Classstudent');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $all_class=D('Class')->order('year desc,name asc')->select();
            $category_fortag=array();
            foreach($all_class as $key=>$value){
                $category_fortag[$value['id']]='['.$value['year'].']'.$value['name'];
            }
            $this -> assign('category_fortag', $category_fortag);   
            $this -> menuClass();
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function uclassStuUpdate() {
        $dao = D('Classstudent');
        if ($dao -> create()) {
            $map["idcard"] = $_POST["idcard"];
            if (M("enroll")->where($map)->count() == 0) {
                $this -> error('无此学生基本信息');
            }
            $student = M("enroll")->where($map)->setField("username",$_POST["student"]);
            //$dao->student = $student;
            $checked = $dao -> save();
            if ($checked > 0) {
                $this -> success("更新成功");
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error($dao->getError());
        } 
    } 
    public function subject() {
        $this -> assign('category_fortag', $this->getTerm());
        if (isset($_GET['searchkey'])) {
            $map['coursenumber|teacher|truename|name'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['term'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        } 
        $dao = D('CourseteacherView');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('term desc,coursenumber asc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> menuCourse();
        $this -> display();
    } 
    public function grade() {
        if (isset($_GET["p"])) {
            $p = $_GET["p"];
        }else{
            $p = 1;
        }
        $dao = D('ProgradeView');
        $map["item"] = "HND";
        $term = $dao->where($map)->group("term")->field("term")->select();
        foreach ($term as $vc) {
            $terms[$vc["term"]]=$vc["term"];
            if (!$te) {
                $te = $vc["term"];
            }
        }
        $this->assign('category_fortag',$terms);
        // $this -> assign('category_fortag', $this->getTerm());//学期列表
        $class = D('ProgradeView')->where($map)->group("classid")->field("classid,classname")->select();
        foreach ($class as $vc) {
            $classes[$vc["classid"]]=$vc["classname"];
        }
        $this->assign('class_fortag',$classes);//班级列表
        $major = D('ProgradeView')->where($map)->group("major")->field("major")->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $this->assign('major_fortag',$majors);//专业列表
        $course = D('ProgradeView')->where($map)->group("coursename")->field("coursename,courseename")->select();
        foreach ($course as $va) {
            $courses[$va["coursename"]]=$va["coursename"].$va["courseename"];
        }
        $this->assign('course_fortag',$courses);//课程列表
        if (isset($_GET['searchkey'])) {
            $map['stuname|stunum'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['major'])) {
            $map['major'] = $_GET['major'];
            $this -> assign('major_current', $_GET['major']);
        } 
        if (isset($_GET['classid'])) {
            $map['classid'] = $_GET['classid'];
            $this -> assign('class_current', $_GET['classid']);
        } 
        if (isset($_GET['category'])) {
            $map['term'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        } else{
            $map['term'] = $te;
        }
        if (isset($_GET['course'])) {
            $map['course.name|course.ename'] = $_GET['course'];
            $this -> assign('course_current', $_GET['course']);
        } 
        $letters = array("S"=>"S","RA"=>"RA","RD"=>"RD","U"=>"U","U(A)"=>"U(A)","U(D)"=>"U(D)");
        $this->assign("grade_fortag",$letters);
        if (isset($_GET['grade'])) {
            // $map['letter'] = $_GET['grade'];
            $this -> assign('grade_current', $_GET['grade']);
        } 
        $final = array(0=>"0-59",1=>"60-69",2=>"70-79",3=>"80-89",4=>"90-100");
        if (isset($_GET["final"])) {
            //最终成绩，待定
            for ($i=0; $i < count($final); $i++) { 
                $finalarr[$i] = explode("-",$final[$i]);
            }
            $this->assign("final_current",$_GET["final"]);
        }
        $this->assign("final_fortag",$final);
        //以上为检索条件
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            $my = $dao -> where($map)->group("id")-> select();
            foreach ($my as $vmy) {
                $willfinal[$vmy["stunum"]][$vmy["coursename"]][] = $vmy["hundred"];
            }
            foreach ($willfinal as $stunum => $a) {
                foreach ($a as $key => $va) {
                    $num = count($va);
                    $truefinal[$stunum][$key] = 0;
                    for ($i=0; $i < $num; $i++) { 
                        if ($va[$i] == 0) {
                            $truefinal[$stunum][$key] = 0;
                            break;
                        }
                        $truefinal[$stunum][$key] += (int)$va[$i];
                    }
                    $truefinal[$stunum][$key] = round($truefinal[$stunum][$key] / $num,2);
                }
            }
            $i = 0;
            while($i < count($my)) { 
                $my[$i]["hundred"] = $truefinal[$my[$i]["stunum"]][$my[$i]["coursename"]];
                if (isset($_GET["final"])) {
                    if ((int)$truefinal[$my[$i]["coursename"]] < (int)$finalarr[$_GET["final"]][0] || (int)$truefinal[$my[$i]["coursename"]] > (int)$finalarr[$_GET["final"]][1]) {
                        array_splice($my,$i,1);
                        continue;
                    }
                }
                if (isset($_GET["grade"])) {
                    if ($my[$i]["letter"] != $_GET["grade"]) {
                        array_splice($my,$i,1);
                        continue;
                    }
                }
                $i++;
            }
            $i = 0;
            $listRows = 50;
            while ($i < count($my)) {
                if ($i>=((int)$p-1)*$listRows && $i<(int)$p*$listRows) {
                    $newmy[] = $my[$i];
                }//echo($i.$listRows."<br/>");
                $i++;
            }
            $this -> assign('my', $newmy);
        }
            import("@.ORG.Page");
            $p = new Page($i, $listRows);
            // $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows)-> select();
            $page = $p -> show();
            $this -> assign("page", $page);
        $this -> menuGrade();
        $this -> display();
    } 
    public function downloadGradeList() {
        $dao = D('ProgradeView');
        $map["item"] = "HND";
        $class = D('ProgradeView')->where($map)->group("classid")->field("classid,classname")->select();
        foreach ($class as $vc) {
            $classes[$vc["classid"]]=$vc["classname"];
        }
        $major = D('ProgradeView')->where($map)->group("major")->field("major")->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $course = D('ProgradeView')->where($map)->group("coursename")->field("coursename,courseename")->select();
        foreach ($course as $va) {
            $courses[$va["coursename"]]=$va["coursename"].$va["courseename"];
        }
        if (isset($_GET['searchkey'])) {
            $map['stuname|stunum'] = array('like', '%' . $_GET['searchkey'] . '%');
        } 
        if (isset($_GET['major'])) {
            $map['major'] = $_GET['major'];
        } 
        if (isset($_GET['classid'])) {
            $map['classid'] = $_GET['classid'];
        } 
        if (isset($_GET['category'])) {
            $map['term'] = $_GET['category'];
        } 
        if (isset($_GET['course'])) {
            $map['course.name|course.ename'] = $_GET['course'];
        } 
        $letters = array("S"=>"S","RA"=>"RA","RD"=>"RD","U"=>"U","U(A)"=>"U(A)","U(D)"=>"U(D)");
        /*if (isset($_GET['grade'])) {
            $map['letter'] = $_GET['grade'];
        } */
        $final = array(0=>"0-59",1=>"60-69",2=>"70-79",3=>"80-89",4=>"90-100");
        if (isset($_GET["final"])) {
            //最终成绩，待定
            for ($i=0; $i < count($final); $i++) { 
                $finalarr[$i] = explode("-",$final[$i]);
            }
        }
        //以上为检索条件
        $count = $dao -> where($map) -> count();
        $my = $dao -> where($map)-> select();
        foreach ($my as $vmy) {
            $willfinal[$vmy["coursename"]][] = $vmy["hundred"];
        }
        foreach ($willfinal as $key => $va) {
            $num = count($va);
            $truefinal[$key] = 0;
            for ($i=0; $i < $num; $i++) { 
                if ($va[$i] == 0) {
                    $truefinal[$key] = 0;
                    break;
                }
                $truefinal[$key] += $va[$i];
            }
            $truefinal[$key] = round($truefinal[$key] / $num);
        }
        $tmp = count($my);
        for ($i=0; $i < $tmp; $i++) { 
           $my[$i]["hundred"] = $truefinal[$my[$i]["coursename"]];
            if (isset($_GET["final"])) {
                if ((int)$truefinal[$my[$i]["coursename"]] < (int)$finalarr[$_GET["final"]][0] || (int)$truefinal[$my[$i]["coursename"]] > (int)$finalarr[$_GET["final"]][1]) {
                    unset($my[$i]);continue;
                }
            }
            if (isset($_GET["grade"])) {
                if ($my[$i]["letter"] != $_GET["grade"]) {
                    unset($my[$i]);continue;
                }
            }
        }
        $titlepic = '/buaahnd/sys/Tpl/Public/download/scorelist.xls';
        $php_path = dirname(__FILE__) . '/';
        $excelurl = $php_path .'../../../..'.$titlepic;
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $p = PHPExcel_IOFactory::load($excelurl);//载入Excel
        $i = 2;
        foreach ($my as $va) {
            if ($va["isrepair"] == 0) {
                $va["isrepair1"] = "";
            }else{
                $va["isrepair1"] = "重修";
            }
            $p  ->setActiveSheetIndex(0)
                ->setCellValue("A".$i,$va["classname"])
                ->setCellValue("B".$i,$va["major"])
                ->setCellValue("C".$i,$va["stuname"])
                ->setCellValue("D".$i,$va["stunum"])
                ->setCellValue("E".$i,$va["scnid"])
                ->setCellValue("F".$i,$va["coursename"])
                ->setCellValue("G".$i,$va["courseename"])
                ->setCellValue("H".$i,$va["examname"])
                ->setCellValue("I".$i,$va["credit"])
                ->setCellValue("J".$i,$va["letter"])
                ->setCellValue("K".$i,$va["isrepair1"])
                ->setCellValue("L".$i,$va["hundred"]);
            $i++;
        }
        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Content-Type:application/octet-stream");
        header('content-Type:application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition:attachment;filename=成绩列表.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    public function proGradeDel(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = M('prograde');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    }
    public function subjectStu() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        $dao = D('ScoreclassView');
        $map['subjectid']=$result1['id'];
        $map['tusername']=$result1['teacher'];
        $map['coursenumber']=$result1['coursenumber'];
        $map['term']=$result1['term'];
         $count = $dao -> where($map) -> order('susername asc') -> count();
        $this->assign('num',$count);
        $list = $dao -> where($map) -> order('year desc,classname asc,susername asc') -> select();
        foreach($list as $k => $v){
            $my[$v['classid']][] = $v;
        }
        $this -> assign('result1', $result1);
        $this -> assign('my', $my);
        $dao_class = D('Class');
        $map_class['isbiye']=0;
        $dtree_class = $dao_class->where($map_class)->order('year desc,name asc')-> select();
        $dtree_year=$dao_class ->where($map_class)->field('year')-> group('year')->order('year desc')->select();
        $dao2 = D('ClassstudentView');
        $map2['isbiye']=0;
        $dtree_stu = $dao2->where($map2)->order('student asc')-> select();
        $this->assign('dtree_year',$dtree_year);
        $this -> assign('dtree_class', $dtree_class);
        $this -> assign('dtree_stu', $dtree_stu);
        $this -> menuCourse();
        $this -> display();
    } 
    public function rewardAdd() {
        $dao1 = D('Class');
        $map1['isbiye']=0;
        $dtree_class = $dao1->where($map1)->order('year desc,name asc')-> select();
        $dtree_year=$dao1 ->where($map1)->field('year')-> group('year')->order('year desc')->select();
        $dao2 = D('ClassstudentView');
        $map2['isbiye']=0;
        $dtree_stu = $dao2->where($map2)->order('student asc')-> select();
        $this->assign('dtree_year',$dtree_year);
        $this -> assign('dtree_class', $dtree_class);
        $this -> assign('dtree_stu', $dtree_stu);
        $this -> assign('ctime', date("Y-m-d H:i:s"));
        $this -> menuReward();
        $this -> display();
    } 
    public function gradeEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        $dao = D('ScoreclassView');
        $map['tusername']=$result1['teacher'];
        $map['coursenumber']=$result1['coursenumber'];
        $map['term']=$result1['term'];
        $count = $dao -> where($map) -> order('susername asc') -> count();
        $list = $dao -> where($map) -> order('year desc,classname asc,susername asc') -> select();
        foreach($list as $k => $v){
            $my[$v['classid']][]=$v;
        }

        $this -> assign('result1', $result1);
        $this -> assign('num',$count);
        $this -> assign('my', $my);
        $this -> menuGrade();
        $this -> display();
    } 
    public function subjectAdd() {
        $dao2=D('User');
        $map2['role']=array('like','%EduTea%');
        $teacher=$dao2-> where($map2) ->order('username asc')-> select();
        $teacher_fortag = array();
        foreach($teacher as $key => $value) {
            //$teacher_fortag[$value['username']] =$value['username']. ' ['.$value['truename']. ']';
            $teacher_fortag[$value['username']] = $value['truename'];
        } 
        $this -> assign('teacher_fortag', $teacher_fortag);
        $dao1=D('Course');
        $course=$dao1 ->order('length(number) asc')-> select();
        $course_fortag = array();
        foreach($course as $key => $value) {
            $course_fortag[$value['number']] = $value['category2'].'&nbsp;'.$value['number']. ' ['.$value['name']. ']';
            //$course_fortag[$value['number']] = (strlen($value['number']) == 5?'基础课 ':'专业课 ').$value['number']. ' ['.$value['name']. ']';
        } 
        $dao3=D('Courseteacher');
        $result3=$dao3->Field('term')->order('id desc')->find();
        $this -> assign('term_latest', $result3['term']);
        $course=$dao1 ->order('number asc')-> select();
        $this -> assign('course_fortag', $course_fortag);
        $this -> assign('term_fortag', $this->getTerm());
        $this -> assign('mydate', date('Y-m-d H:i:s'));
        $this -> menuCourse();
        $this -> display();
    } 
    public function subjectDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao1 = D('Score');
        $map1['subjectid'] = array('in', $id);
        $my1=$dao1->where($map1)->select();
        if($my1){
            $this->error('删除失败！删除前请先将选该课程的学生全部删除');
        }
        $dao = D('Courseteacher');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function rewardDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('Reward');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function gradePub() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('Score');
        $result = $dao -> where($map)->setField('ispublic','1');
        if ($result > 0) {
            $this -> success('已成功保存');
        } else {
            $this -> error('影响的记录行数为0');
        } 
    } 
    public function gradePubb() { //发布补考成绩
        $id = $_POST['id'];
        $bscore=$_POST['score'];
        $blevelscore=$_POST['blevelscore']; 
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $ids=explode(',',$id);
        $bls=explode(',',$blevelscore);
        $bs=explode(',',$bscore);
        $list1=array_combine($ids,$bs);
        $list2=array_combine($ids,$bls);
        $dao = D('Score');
        foreach($ids as $k=>$v){
            $map['id']=$v;
            $my=$dao->where($map)->find();
            if(!(empty($my['bscore']) && empty($my['blevelscore']))){
                if(!(empty($list1[$v])||$list1[$v] =='undefined')){
                    $newscore=$list1[$v];
                }else{
                    if($my['bscore'] >$my['score']){
                        $newscore=$my['bscore'];
                    }else{
                        $newscore=$my['score'];
                    }
                }
                if(!(empty($list2[$v])||$list2[$v] =='undefined')){
                    $newlevelscore=$list2[$v];
                }
                else{
                    $newlevelscore=$my['levelscore'];
                }
//                 $plus_old='';
//                 if(!empty($my['plus'])) {
//                     $plus_old='('.$my['plus'].')';
//                 }
                $plus='初考:'.$my['score'].' '.$my['levelscore'].' 补考:'.$my['bscore'].' '.$my['blevelscore'];
                $data = array('score'=>$newscore,'levelscore'=>$newlevelscore,'plus'=>$plus); 
                $result = $dao -> where($map)->setField($data);
            }
        }
        if ($result !== false) {
            $this -> success('已成功保存');
        } else {
            $this -> error('影响的记录行数为0');
        } 
    } 
    public function gradeFlagb(){
        $id = $_POST['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $ids=explode(',',$id);
        $map['id'] = array('in',$ids);
        $map['isb'] = 1;
        $dao = D('Score');
        $my=$dao->where($map)->order('susername asc')->select();
        $sflag='';
        $bflag='';
        $flag='';
        foreach($my as $k=>$v){
            if(strlen($v['bscore']) > 0){
                $sflag=1;
                break;
            }else{
                $sflag=0;
            }
        }
        foreach($my as $k=>$v){
            if(strlen($v['blevelscore']) > 0){
                $bflag=2;
                break;
            }else{
                $bflag=0;
            }
        }
        $flag=$sflag+$bflag;
        //$this->assign('flag',$flag);
        $this->assign('my',$my);
        if($flag == 1){
            $this->display('gradeBscore');
        }elseif($flag == 2){
            $this->display('gradeBlscore');
        }else{
           $this->display('gradeBtscore'); 
        }
    }
    public function gradeFreeze() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('Score');
        $result = $dao -> where($map)->setField('ispublic','0');
        if ($result > 0) {
            $this -> success('已成功保存');
        } else {
            $this -> error('影响的记录行数为0');
        } 
    } 
    public function gradeIsb() {

        $subjectid=$_GET['subjectid'];
        if(!isset($subjectid)){
            $this -> error('参数缺失');
        } 

        $map2['id'] = $subjectid;

        $dao2 = D('courseteacher');
        $result2 = $dao2 -> where($map2)->setField('issubb','0');
        if ($result2 !==false) {
            $this -> success('已成功保存');
        } else {
            $this -> error('影响的记录行数为0');
        } 
    } 
    public function gradeIssub() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        $dao = D('ScoreclassView');
        $map['tusername']=$result1['teacher'];
        $map['coursenumber']=$result1['coursenumber'];
        $map['term']=$result1['term'];
        $subtime = $dao -> where($map) ->Field('subtime')->group('subtime')-> order('susername asc') ->select();
        $dao2 = D('courseteacher');
//         if(strlen($subtime[0]['subtime']) == 0){
//             $this->error('该门课还未提交');
//         }elseif(time()-strtotime($subtime[0]['subtime'])<1209601){
//             $result = $dao2 -> where($map1)->setField('issub','0');
//                  if ($result > 0) {
//                     $this -> success('已成功保存');
//                 } else {
//                     $this -> error('这门课已开启可录成绩');
//                 } 
//         }else{
//             $this->error('这门课离成绩录入时间已超过14天，此操作不可继续！');
//         }
        $result = $dao2 -> where($map1)->setField('issub','0');
                 if ($result > 0) {
                    $this -> success('已成功保存');
                } else {
                    $this -> error('这门课已开启可录成绩');
                } 
    } 
    public function gradeIsbnot() {
        $subjectid=$_GET['subjectid'];
        if(!isset($subjectid)) {
            $this -> error('参数缺失');
        } 

        $dao2 =D('courseteacher');
        $map2['id']=$subjectid;
        $result1=$dao2->where($map2)->setField('issubb','1');

        if ($result1 !==false) {
            $this -> success('已成功保存');
        } else {
            $this -> error('操作失败');
        } 
    } 
    public function dovisiblenot() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('Score');
        $result = $dao -> where($map)->setField('isvisible','0');
        if ($result > 0) {
            $this -> success('已成功保存');
        } else {
            $this -> error('影响的记录行数为0');
        } 
    } 
    public function dovisible() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('Score');
        $result = $dao -> where($map)->setField('isvisible','1');
        if ($result > 0) {
            $this -> success('已成功保存');
        } else {
            $this -> error('影响的记录行数为0');
        } 
    } 
    public function subjectEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Courseteacher');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> assign('term_fortag', $this->getTerm());
            $dao2=D('User');
            $map2['role']=array('like','%EduTea%');
            $teacher=$dao2-> where($map2) ->order('username asc')-> select();
            $teacher_fortag = array();
            foreach($teacher as $key => $value) {
                $teacher_fortag[$value['username']] =$value['username']. ' ['.$value['truename']. ']';
            } 
            $this -> assign('teacher_fortag', $teacher_fortag);
            $dao1=D('Course');
            $course=$dao1 ->order('number asc')-> select();
            $course_fortag = array();
            foreach($course as $key => $value) {
                $course_fortag[$value['number']] = $value['number']. ' ['.$value['name']. ']';
            } 
            $this -> assign('course_fortag', $course_fortag);
            $this -> menuCourse();
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function subjectUpdate() {
        $dao = D('Courseteacher');
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
    public function subjectInsert() {
        if(strlen($_POST['term']) == 0){
                   $this->error('未选择学期'); 
        }else{
            $term = $_POST['term'];  
            }
        if(strlen($_POST['coursenumber']) == 0){
                   $this->error('未选择课程'); 
        }else{
                $coursenumber = $_POST['coursenumber']; 
            }
        if(!isset($_POST['teacher'])){
            $this->error('未选择任课老师');
        }else{
            $teacher = $_POST['teacher']; 
        }
        $i=0;
        $j=0;
        $dao = D('Courseteacher');
        if(!empty($teacher)){
            $i++;
            foreach($teacher as $key=>$value){
                $data['coursenumber'] = $coursenumber;
                $data['term']=$term;
                $data['teacher'] = $value;
                $insertID=$dao->add($data);
                if($insertID){
                    $j++;
                }else{
                  $this -> error('没有更新任何数据');   
                }
            }
        }else{
           $this -> error($dao->getError());  
        }
        if($j==count($teacher)){
            $this -> success('已成功保存');
        }else{
            $this->error("勾选了$i个老师,只增加了$j个老师");
        }
    } 
    public function subjectStuInsert() {
        if(empty($_POST['stu'])){
            $this -> error('请至少选择一项');
        }
        $stu=explode(',',$_POST['stu']);
        $dao = D('Score');
        $i=0;
        $j=0;
        foreach($stu as $key=>$value){
            if(substr($value,0,2)=='GJ'){         
                $i++;
                if ($dao -> create()) { 
                    $temp=array();
                    $temp=explode('-',$value);
                    $dao -> susername=$temp[0];
                    $dao -> struename=$temp[1];
                    $insertID = $dao -> add();
                    if ($insertID) {
                        $j++;
                    } else {
                        $this -> error('没有更新任何数据');
                    } 
                } else {
                    $this -> error($dao->getError());
                } 
            }
        }
        if($i==$j){
            $this -> success('已成功新增'.$j.'个学生');
        }else{
            $this -> error("勾选了$i个学生,只增加了$j个学生");
        }
        
    } 
    public function subjectStuBinsert(){
        $titlepic = $_POST['titlepic'];
        if (empty($titlepic) ) {
            $this -> error('未上传文件');
        } 
        if (substr($titlepic,-3,3) !=='xls') {
            $this -> error('上传的不是xls文件');
        } 
        $php_path = dirname(__FILE__) . '/';
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $inputFileName = $php_path .'../../..'.$titlepic;
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $count=count($sheetData);
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',    
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
        $dao=D('classstudent');
        $dao1=D('score');
        $my=$dao->field('student,studentname')->select();
        $student=array();
        foreach($my as $v){
            $student[$v['student']]=$v['studentname'];
        }
        for($i=4;$i<=$count;$i++){
           for($j=0;$j<3;$j++){
                if(strlen($sheetData[$i][chr(65+$j)]) == 0){
                  $this->error(chr(65+$j).$i."必填项未填全，请填全后提交");
                }
            }
            if(!preg_match("/^GJ[0-9]{9}$/",strtr($sheetData[$i]['B'], $arr))){
                $this->error("第".$i."行学号格式不正确");
            }else{
                if(!in_array(strtr($sheetData[$i]['B'],$arr),array_keys($student))){
                    $this->error('第'.$i.'行学号'.strtr($sheetData[$i]['B'],$arr).'当前库中不存在');
                }else{
                if($student[strtr($sheetData[$i]['B'], $arr)] !=strtr($sheetData[$i]['C'], $arr)){
                    $this->error('第'.$i.'行学号姓名不对应');
                }
                }
            }
            $map['susername']=$sheetData[$i]['B'];
            $map['coursenumber']=$_POST['coursenumber'];
            $map['term']=$_POST['term'];
            $find=$dao1->where($map)->find();
            if($find){
                $this->error('学号'.strtr($sheetData[$i]['B'], $arr).'课程编号'.$_POST['coursenumber'].'学期'.$_POST['term'].'已经存在！学号，课程编号和学期必须是唯一的');
            }
            
            $data_a[$i-4]['susername'] = strtr($sheetData[$i]['B'], $arr);
            $data_a[$i-4]['struename'] = strtr($sheetData[$i]['C'], $arr);
            $data_a[$i-4]['subjectid'] = $_POST['subjectid'];
            $data_a[$i-4]['tusername'] = $_POST['tusername'];
            $data_a[$i-4]['ttruename'] = $_POST['ttruename'];
            $data_a[$i-4]['coursenumber'] = $_POST['coursenumber'];
            $data_a[$i-4]['coursename'] = $_POST['coursename'];
            $data_a[$i-4]['courseename'] = $_POST['courseename'];
            $data_a[$i-4]['coursetime'] = $_POST['coursetime'];
            $data_a[$i-4]['credit'] = $_POST['credit'];
            $data_a[$i-4]['term'] = $_POST['term'];
        }
        
        $dao1 -> addAll($data_a);
         $this -> success("已成功保存");
    }
    public function rewardInsert() {
        if(empty($_POST['content'])){
            $this -> error('内容不能为空');
        }
        if(empty($_POST['stu'])){
            $this -> error('请至少选择一个学生');
        }
        $stu=explode(',',$_POST['stu']);//dump($stu);return;
        $dao = D('Reward');
        $i=0;
        $j=0;
        foreach($stu as $key=>$value){
            // if(substr($value,0,2)=='GJ'){         
                $i++;
                if ($dao -> create()) { 
                    $temp=array();
                    $temp=explode('-',$value);
                    $dao -> susername=$temp[0];
                    $dao -> struename=$temp[1];
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
        }
        if($i==$j){

            $Noticecreate = D('Noticecreate');
            $Notice = D('Notice');
            $data['title'] = "奖惩通知";
            $data['content'] = $_POST['content'];
            $data['tusername'] = session('username');
            $data['ttruename'] = D('User') -> where("username='" . session('username') . "'") -> getField('truename');
            $data['ctime'] = date("Y-m-d H:i:s");
            $insertID = $Noticecreate -> add($data);
            if ($insertID) {
                $reader = M("user")->select();
                foreach($reader as $key => $value) {
                    $data_a[$key]['noticeid'] = $insertID;
                    $data_a[$key]['readusername'] = $value["username"];
                    $data_a[$key]['readtruename'] = $value["truename"];
                } 
                $Notice -> addAll($data_a);
            } 
            $this -> success('已成功保存'.$j.'个学生的奖惩记录');
        }else{
            $this -> error("勾选了$i个学生,只增加了$j个学生");
        }
        
    } 
    public function setNoticeRead(){
        if(!isset($_GET['id'])) {
            $this->error('参数缺失');
        }
        $id=$_GET['id'];
        $Notice = D("Notice");
        $map['readusername']=session('username');
        $map['id']=$id;
        $my=$Notice->where($map)->select();
        if($my){
            $data['id']=$id;
            $data['readtime']=date("Y-m-d H:i:s");
            $result=$Notice->save($data); 
            if($result>0){
                $this->success('已成功标记');
            }
        }  
    }
    public function course() {
        if (isset($_GET['searchkey'])) {
            $condition["course.name"] = array('like', '%' . $_GET['searchkey'] . '%');
            $condition["course.ename"] = array('like', '%' . $_GET['searchkey'] . '%');
            $condition["course.category2"] = array('like', '%' . $_GET['searchkey'] . '%');
            $condition["_logic"] = "OR";
            $map["_complex"] = $condition;
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET["classid"])) {
            $map["classid"] = $_GET["classid"];
            $this->assign("class_current",$_GET["classid"]);
        }
        $m["item"] = 'HND';//分项目的限制开始
        $major = M("major")->where($m)->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $map["major"] = array("in",$majors);
        $mbp["major"] = array("in",$majors);//分项目的限制结束
        $all_class=D('Class')->where($mbp)->order('year desc,name asc')->select();
        $class_fortag=array();
        foreach($all_class as $key=>$value){
            $class_fortag[$value['id']]='['.$value['year'].']'.$value['name'];
        }
        $this->assign("class_fortag",$class_fortag);//显示班级列表
        $dao = D('CourseView');
        $count = $dao ->where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) ->order("classid") -> limit($p -> firstRow . ',' . $p -> listRows) -> order('number asc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        }
        $this->assign("all_class",$all_class);
        $this -> menuCourse();
        $this -> display();
    }
    public function usualGradeDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = M('pregrade');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function usualGrade(){
        $dao = D('PregradeView');
        $map["isrequired"] = 0;
        $all_exam = $dao->where($map)->group("examname")->select();
        foreach ($all_exam as $va) {
            $exams[$va["examname"]] = $va["examname"];
        }
        $this->assign("exam_fortag",$exams);//考试列表
        $class = $dao->group("classid")->field("classid,classname")->select();
        foreach ($class as $vc) {
            $classes[$vc["classid"]]=$vc["classname"];
        }
        $this->assign('class_fortag',$classes);//班级列表
        $major = $dao->group("major")->field("major")->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $this->assign('major_fortag',$majors);//专业列表
        if (isset($_GET['searchkey'])) {
            $map['stuname|stunum'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET["exam"])) {
            $map['examname']=$_GET["exam"];
            $this->assign("exam_current",$_GET["exam"]);
        }
        if (isset($_GET['major'])) {
            $map['major'] = $_GET['major'];
            $this -> assign('major_current', $_GET['major']);
        } 
        if (isset($_GET['classid'])) {
            $map['classid'] = $_GET['classid'];
            $this -> assign('class_current', $_GET['classid']);
        } 
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> menuGrade();
        $this -> display();
    } 
    
    public function usualResultAdd() {
        $this -> menuGrade();
        $this -> display();
    }

    public function usualResultInsert() {
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
        $dao=D('Score');
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',    
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
        for($i=3;$i<=$count;$i++){
            if(!preg_match("/^GJ[0-9]{9}$/",strtr($sheetData[$i]['A'], $arr))){
                $this->error("第".$i."行学号格式不正确");
            }
            $reg='/^[0-9]{4}-[0-9]{4}学年第[1-2]{1}学期$/';
            if(!preg_match($reg,  strtr($sheetData[$i]['J'], $arr))){
                $this->error("第".$i."行学期格式不正确");
            }
            $map['susername']=$sheetData[$i]['A'];
            $map['coursenumber']=$sheetData[$i]['G'];
            $map['term']=$sheetData[$i]['J'];
            $find=$dao->where($map)->find();
            if($find){
                $this->error('学号'.strtr($sheetData[$i]['A'], $arr).'课程编号'.strtr($sheetData[$i]['G'], $arr).'学期'.strtr($sheetData[$i]['J'], $arr).'已经存在！学号，课程编号和学期必须是唯一的');
            }
            
            $data_a[$i-3]['susername'] = strtr($sheetData[$i]['A'], $arr);
            $data_a[$i-3]['struename'] = $sheetData[$i]['B'];
            $data_a[$i-3]['tusername'] = strtr($sheetData[$i]['C'], $arr);
            $data_a[$i-3]['ttruename'] = $sheetData[$i]['D'];
            $data_a[$i-3]['coursename'] = $sheetData[$i]['E'];
            $data_a[$i-3]['courseename'] = strtr($sheetData[$i]['F'], $arr);
            $data_a[$i-3]['coursenumber'] = strtr($sheetData[$i]['G'], $arr);
            $data_a[$i-3]['coursetime'] = strtr($sheetData[$i]['H'], $arr);
            $data_a[$i-3]['credit'] = strtr($sheetData[$i]['I'], $arr);
            $data_a[$i-3]['term'] = strtr($sheetData[$i]['J'], $arr);
            $data_a[$i-3]['score'] = strtr($sheetData[$i]['K'], $arr);
            $data_a[$i-3]['plus'] = strtr($sheetData[$i]['L'], $arr);
            $data_a[$i-3]['bscore'] = strtr($sheetData[$i]['M'], $arr);
            $data_a[$i-3]['ispublic'] = 1;
            $data_a[$i-3]['subtime'] = '1888-08-08 08:08:08';
        }
        
        $dao -> addAll($data_a);
        $this -> success("已成功保存");
    }  
    public function usualGrade2(){
        $dao = D('PregradeView');
        $map["isrequired"] = 1;
        $all_exam = $dao->where($map)->group("examname")->select();
        foreach ($all_exam as $va) {
            $exams[$va["examname"]] = $va["examname"];
        }
        $this->assign("exam_fortag",$exams);//考试列表
        $class = $dao->group("classid")->field("classid,classname")->select();
        foreach ($class as $vc) {
            $classes[$vc["classid"]]=$vc["classname"];
        }
        $this->assign('class_fortag',$classes);//班级列表
        $major = $dao->group("major")->field("major")->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $this->assign('major_fortag',$majors);//专业列表
        if (isset($_GET['searchkey'])) {
            $map['stuname|stunum'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET["exam"])) {
            $map['examname']=$_GET["exam"];
            $this->assign("exam_current",$_GET["exam"]);
        }
        if (isset($_GET['major'])) {
            $map['major'] = $_GET['major'];
            $this -> assign('major_current', $_GET['major']);
        } 
        if (isset($_GET['classid'])) {
            $map['classid'] = $_GET['classid'];
            $this -> assign('class_current', $_GET['classid']);
        } 
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> menuGrade();
        $this -> display();
    } 
    public function resultComplemented(){
        $this -> menuGrade();
        $this -> display();
    }
    public function getHND(){
        if((isset($_POST['struename'])) || (isset($_POST['susername']))){
                $map['struename|susername']=array($_POST['struename'],$_POST['susername'],'_multi'=>true);
        }else{
            $this->error('参数缺失');
        }
        $dao=D('Score');
        $my=$dao->where($map)->group(susername)->select();
        $this->assign('my',$my);
        $this->assign('count',count($my));
//         $this -> menuGrade();
        $this->display();
    }
    public function getStuInfo(){
        $id=$_POST['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $score=D('score');
        $map['susername']=$id;
        $my=$score->where($map)->group(susername)->select();
        $this->assign('my',$my);
        $this->display();
    }
     public function getScoreInfo() {
        $id = $_POST['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $score=D('score');
        $map['coursename']=array(array('notlike','%月考'),array('notlike','%补考'),'AND');
        $map['susername']=$id;
        $my=$score->where($map)->select();
        $this->assign('my',$my);
        $this->display();
    }
    
    public function scoreEdit(){
        $dao = D('score');
        $condition['id']=$_POST['id'];
        $data['courseename']=$_POST['courseename'];
        $data['credit']=$_POST['credit'];
        $data['tusername']=$_POST['tusername'];
        $data['ttruename']=$_POST['ttruename'];
            $checked = $dao->where($condition)->save($data);
            if($checked !== false){
                $this->success('数据库更新成功！');
            }else{
                $this->error('数据更新失败！');
            }
    }
    

    public function reward() {
        if (isset($_GET['searchkey'])) {
            $map['struename|ttruename|content'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        $dao = D('Reward');
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
        $this -> menuReward();
        $this -> display();
    }     
    public function courseAdd() {
        $this -> menuCourse();
        $this -> display();
    } 
    public function courseDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('Course');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function courseEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Course');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> menuCourse();
            $all_class=D('Class')->order('year desc,name asc')->select();
            $category_fortag=array();
            foreach($all_class as $key=>$value){
                $category_fortag[$value['id']]='['.$value['year'].']'.$value['name'];
            }
            $this -> assign('category_fortag', $category_fortag);
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function courseUpdate() {
        $dao = D('Course');
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
    // public function courseInsert() {
    //     $dao = D('Course');
    //     if ($dao -> create()) {
    //         $insertID = $dao -> add();
    //         if ($insertID) {
    //             $this -> ajaxReturn($insertID, '已成功保存！', 1);
    //         } else {
    //             $this -> error('没有更新任何数据');
    //         } 
    //     } else {
    //         $this -> error($dao->getError());
    //     } 
    // }      
    public function stuGrade() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map1['student']=$id;
        $stuname= D("Classstudent")->where($map1)->getField('studentname');
        $this->assign('id',$id);
        $this->assign('stuname',$stuname);
        $Score = D("Score");
        $map['susername']=$id;
        $term=$Score -> where($map) ->field('term')->group('term')->order('term asc')-> select();
        $term_num=count($term);
        if($term_num>0){
            foreach($term as $key=>$value){
                $map['term']=$value['term'];
                $my[$key]=$Score -> where($map) -> select();
            }
            $this->assign('my',$my);
        } 
        $this -> menuStu();
        $this -> display();
    }
    
    public function downScore(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        $classstudent=D("ClassstudentView");
        $student=$classstudent->Field("name,studentname,classid,ename,year,student")->where("student ='".$id."'")->find();

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

        $classid = $student["classid"];
        $classtab = D("Class");
        $majorinfo = $classtab->Field("major,majore")->where("id='".$classid."'")->find();


        $Score=D("Score");
        $map['susername']=$id;
        $map['isvisible']=1;
//        $map['ispublic']=1;
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
                'bold' => true,
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
                'bold' => true,
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
                'bold' => true,
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
                'bold' => true,
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
                'bold' => true,
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
                'bold' => true,
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
                'bold' => true,
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
            $actSheet->setCellValue('A4','Name:'.$mystuename.'   Student No.:'.$student['student'].'  Major: '.$majorinfo[majore].'       Department: DAFLS');
            $actSheet->setCellValue('A5','姓名:'.$student['studentname'].'    学号：'.$student['student'].'                 专业：'.$majorinfo[major].'                科系：大学外语部');
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
            $actSheet->getPageMargins()->setTop(0);
            $actSheet->getPageMargins()->setBottom(0);
            $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
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
             $actSheet->setCellValue('A2',"No:".$student['student']."         "."Name:".$mystuename."          "."Major:".$majorinfo['majore']."          "."Department:DAFLS"."          "."Higher National Diploma Program");
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
            $actSheet->getPageMargins()->setTop(0);
            $actSheet->getPageMargins()->setBottom(0);
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
             $actSheet->setCellValue('A2',"学号：".$student['student']."         "."姓名：".$student['studentname']."          "."专业：".$majorinfo['major']."          "."学院：大学外语部"."          "."英国高等教育文凭项目");
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
        //dump($stu);
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
        //dump($stu);
        $stu_num = count($stu);
        $end_row = $stu_num + 1;
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
    public function downStu(){
        $course=$_GET['course'];
        $teacher=$_GET['teacher'];
        $term=$_GET['term'];
        include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.class.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("NJU-HND auto generated Document");
        $styleHead= array(
            'font' => array(
                'name'=>'宋体',
                'bold' => true,
                'size' => 14
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
        $style3= array(
            'font' => array(
                'name'=>'宋体',
                'size' => 12,
                 'color'     => array(
                    'rgb' => 'FF0000'
                )
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
        $objPHPExcel->createSheet();
        $actSheet=$objPHPExcel->getSheet(0);
        $actSheet->setTitle("学生名单");
        $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
        $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $actSheet->getPageSetup()->setHorizontalCentered(true);
        $actSheet-> getColumnDimension('A') -> setwidth(20.63);
        $actSheet-> getColumnDimension('B') -> setwidth(20.63);
        $actSheet-> getColumnDimension('C') -> setwidth(20.63);
        $actSheet->getRowDimension('1')->setRowHeight(51.75);
        $actSheet->getRowDimension('2')->setRowHeight(24);
        $actSheet->getRowDimension('3')->setRowHeight(14.25);
        $actSheet->mergeCells('A1:C1');
        $actSheet->mergeCells('A2:C2');
        $actSheet->setCellValue('A1',"课程：".$course." 任课教师：".$teacher."\n".
        "学期：".$term);
        $actSheet ->getStyle('A1')->getAlignment()->setWrapText(true);//自动换行 
        $actSheet->setCellValue('A2',"学生名单");
        $actSheet->setCellValue('A3',"*班级");
        $actSheet->setCellValue('B3',"*学号");
        $actSheet->setCellValue('C3',"*姓名");
        $actSheet->getStyle('A1:C2')->applyFromArray($styleHead);
        $actSheet->getStyle('A3:C3')->applyFromArray($style3);
        $filename=substr($course,strpos($course, '[')+1,strpos($course, ']')-strpos($course, '[')-1).'_'.substr($teacher,strpos($teacher, '[')+1,strpos($teacher, ']')-strpos($teacher, '[')-1).'学生名单.xls';
         $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit; 
    }
    public function uploadStu(){
        $this->menuClass();
        $this->display();
    }
    public function downloadallstudent(){
        // Vendor('PHPExcel'); 
        $titlepic = '/buaahnd/sys/Tpl/Public/download/classstudent.xls';
        $php_path = dirname(__FILE__) . '/';
        $excelurl = $php_path .'../../../..'.$titlepic;
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $p = PHPExcel_IOFactory::load($excelurl);//dump($p);return;
        $p -> setActiveSheetIndex(0);
        /*for ($errornum=0; $errornum <count($errorarr) ; $errornum++) { 
        $p->getActiveSheet()->getStyle($errorarr[$errornum])->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $p->getActiveSheet()->getStyle($errorarr[$errornum])->getFill()->getStartColor()->setARGB($color); 
        }*/
        $stuinfo = M("enroll")->where("username='z'")->select();
        $p->getActiveSheet()->getStyle('H')->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        foreach ($stuinfo as $i => $vs) {
            $p  ->setActiveSheetIndex(0)
                ->setCellValue('E'.($i+3), $vs["truename"]) 
                // ->setCellValue('H'.($i+3), "233333333333333333") ;
                ->setCellValueExplicit('G'.($i+3), $vs["idcard"],PHPExcel_Cell_DataType::TYPE_STRING);

        }
          ob_end_clean();
        header("Pragma: public");
          header("Expires: 0");
          header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
          header("Pragma: no-cache");
          header("Content-Type:application/octet-stream");
          header('content-Type:application/vnd.ms-excel;charset=utf-8');
          header('Content-Disposition:attachment;filename=所有未分班学生(可用作模板).xls');//设置文件的名称
          header("Content-Transfer-Encoding:binary");
          $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
          $objWriter->save('php://output');
          return true;
          exit;
    }
    public function classStudentInsert() {
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
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',    
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
'＂'=>'"','年'=>''); 
        $count = count($sheetData);//一共有多少行
        if ($count < 3) {
            $this->ajaxReturn($titlepic, "请填写信息", 0);
        }
        //到此为止都是可以复制的，$sheetdata里面存着所有信息，$inputFileName为文件完整路径
        $class = M("class");
        //$map["year"] = Date('Y');
        //$classlistall = M("class")->where($map)->select();
        foreach ($classlistall as $vc) {
            $classlist[$vc["name"]] = $vc["id"];
        }
        for($i = 3; $i <= $count; $i++){
            $b = true;
            for($j = 1; $j <= 7; $j++){
                if(strlen($sheetData[$i][chr(64+$j)]) == 0){
                    $emptys[] = chr(64+$j).$i;
                    $b = false;
                }
            }
            for ($k=$i+1; $k <= $count; $k++) { 
                if(strtr($sheetData[$i]['G'], $arr) == strtr($sheetData[$k]['G'], $arr)){
                    $conflicts[] = 'G'.$i;
                    $conflicts[] = 'G'.$k;
                    $b = false;
                }
                if(strtr($sheetData[$i]['D'], $arr) == strtr($sheetData[$k]['D'], $arr)){
                    $conflicts[] = 'D'.$i;
                    $conflicts[] = 'D'.$k;
                    $b = false;
                }
                if (strtr($sheetData[$i]['A'], $arr) == strtr($sheetData[$k]['A'], $arr) && strtr($sheetData[$i]['B'], $arr) == strtr($sheetData[$k]['B'], $arr) && strtr($sheetData[$i]['C'], $arr) != strtr($sheetData[$k]['C'], $arr)) {
                    $errors[] = 'C'.$i;
                    $errors[] = 'C'.$k;
                    $b = false;
                }
            }
            if (!$b) {
                continue;
            }
            $map["year"] = strtr($sheetData[$i]['A'], $arr);
            $map["name"] = strtr($sheetData[$i]['B'], $arr);
            $classinfo = M("class")->where($map)->find();
            if (!$classinfo) {
                $classdata[$i]["year"] = strtr($sheetData[$i]['A'], $arr);
                $classdata[$i]["major"] = strtr($sheetData[$i]['C'], $arr);
                if (!M("major")->where($classdata[$i])->find()) {
                    $errors[] = 'A'.$i;
                    $errors[] = 'B'.$i;
                    $errors[] = 'C'.$i;
                    continue;
                }
                $classdata[$i]["name"] = strtr($sheetData[$i]['B'], $arr);
                $classdata[$i]["ctime"] = date('y-m-d h:i:s',time());
                $data_a[$i-3]["classid"] = $class->add($classdata[$i]);
                $justadd[] = $data_a[$i-3]["classid"];
            }else{
                if ($classinfo["major"] != strtr($sheetData[$i]['C'], $arr)) {
                    $errors[] = 'C'.$i;
                    continue;
                }
                $data_a[$i-3]["classid"] = $classinfo["id"];
            }
            $data_a[$i-3]['student'] = strtr($sheetData[$i]['D'], $arr);
            $data_a[$i-3]['studentname'] = strtr($sheetData[$i]['E'], $arr);
            $data_a[$i-3]['ename'] = strtr($sheetData[$i]['F'], $arr);
            $search["idcard"] = strtr($sheetData[$i]['G'], $arr);
            $data_a[$i-3]['idcard'] = $search["idcard"];
            $search["truename"] = strtr($sheetData[$i]['E'], $arr);
            // $mbp["student"] = $data_a[$i-3]['student'];
            // $mbp["idcard"] = $search["idcard"];
            // $mbp["_logic"] = "or";
            if (M("enroll")->where($search)->count() == 0 ) {
                $errors[] = 'G'.$i;
            }else if (M("classstudent")->where(array("idcard"=>$search["idcard"]))->count() > 0){
                $errors[] = 'G'.$i;
            }else if (M("classstudent")->where(array("student"=>$data_a[$i-3]['student']))->count() > 0) {
                $errors[] = 'D'.$i;
            } else{
                M("enroll")->where($search)->setField("username",$data_a[$i-3]['student']);
            }
        }//for循环结束
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
            M("class")->where(array("id" => array("in",$justadd)))->delete();
            $this->ajaxReturn($titlepic, "信息不正确", 0);
            // $this->ajaxReturn(array($errors,$emptys,$conflicts),'测试',0);
        }
        $classstudent = M('classstudent');//连接数据库
        $classstudent -> addAll($data_a);
        $this -> success("已成功保存");
    }  
    public function courseUpload(){
        $this->menuCourse();
        $this->display();
    }
    public function courseInsert(){
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
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',    
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
'＂'=>'"', '年' => ''); 
        $count = count($sheetData);//一共有多少行
        if ($count < 3) {
            $this->ajaxReturn($titlepic, "请填写信息", 0);
        }
        //到此为止都是可以复制的，$sheetdata里面存着所有信息，$inputFileName为文件完整路径
        for ($i = 3; $i <= $count; $i++) { 
            $b = true;
            for ($j = 1; $j <= 6; $j++){
                if(strlen($sheetData[$i][chr(64+$j)]) == 0){
                    $emptys[] = chr(64+$j).$i;
                    $b = false;
                }
            }
            $map["year"] = strtr($sheetData[$i]['A'], $arr);
            $map["name"] = strtr($sheetData[$i]['B'], $arr);
            $classid = M("class")->where($map)->getField("id");
            if (!$classid) {
                $errors[] = 'B'.$i;
                $b = false;
            }
            $con1 = strtr($sheetData[$i]['A'], $arr).strtr($sheetData[$i]['B'], $arr).strtr($sheetData[$i]['C'], $arr);
            $con2 = strtr($sheetData[$i]['A'], $arr).strtr($sheetData[$i]['B'], $arr).strtr($sheetData[$i]['D'], $arr);
            for ($k=$i+1; $k <= $count; $k++) { 
                if ($con1 == strtr($sheetData[$k]['A'], $arr).strtr($sheetData[$k]['B'], $arr).strtr($sheetData[$k]['C'], $arr)) {
                    $conflicts[] = "A".$i;
                    $conflicts[] = "A".$k;
                    $conflicts[] = "B".$i;
                    $conflicts[] = "B".$k;
                    $conflicts[] = "C".$i;
                    $conflicts[] = "C".$k;
                    $b = false;
                }
                if ($con2 == strtr($sheetData[$k]['A'], $arr).strtr($sheetData[$k]['B'], $arr).strtr($sheetData[$k]['D'], $arr)) {
                    $conflicts[] = "A".$i;
                    $conflicts[] = "A".$k;
                    $conflicts[] = "B".$i;
                    $conflicts[] = "B".$k;
                    $conflicts[] = "D".$i;
                    $conflicts[] = "D".$k;
                    $b = false;
                }
            }
            if (!is_numeric(strtr($sheetData[$i]['F'], $arr))) {
                $errors[] = "F".$i;
                $b = false;
            }
            if (!$b) {
                continue;
            }
            $data_a[$i-3]['classid'] = $classid;
            $data_a[$i-3]['name'] = trim(strtr($sheetData[$i]['C'], $arr));
            $data_a[$i-3]['ename'] = trim(strtr($sheetData[$i]['D'], $arr));
            $data_a[$i-3]['category2'] = trim(strtr($sheetData[$i]['E'], $arr));
            $data_a[$i-3]['credit'] = trim(strtr($sheetData[$i]['F'], $arr));
            $data_a[$i-3]['school'] = trim(strtr($sheetData[$i]['G'], $arr));
            $data_a[$i-3]['category1'] = trim(strtr($sheetData[$i]['H'], $arr));
            $data_a[$i-3]['coursetime'] = trim(strtr($sheetData[$i]['I'], $arr));
            $data_a[$i-3]['exammethod'] = trim(strtr($sheetData[$i]['J'], $arr));
            $data_a[$i-3]['category3'] = trim(strtr($sheetData[$i]['K'], $arr));
            $data_a[$i-3]['level'] = trim(strtr($sheetData[$i]['L'], $arr));
            $data_a[$i-3]['master'] = trim(strtr($sheetData[$i]['M'], $arr));
            $data_a[$i-3]['teachers'] = trim(strtr($sheetData[$i]['N'], $arr));
            $data_a[$i-3]['book'] = trim(strtr($sheetData[$i]['O'], $arr));
            $data_a[$i-3]['intro'] = trim(strtr($sheetData[$i]['P'], $arr));
            $data_a[$i-3]['eintro'] = trim(strtr($sheetData[$i]['Q'], $arr));
            $data_a[$i-3]['plus'] = trim(strtr($sheetData[$i]['R'], $arr));
        }
        if (count($errors) > 0) {
            excelwarning($inputFileName,$errors);
        }
        if (count($conflicts) > 0) {
            excelwarning($inputFileName,$conflicts,'FFFFC000');
        }
        if (count($emptys) > 0) {
            excelwarning($inputFileName,$emptys,'FF00B0F0');
        }
        if (count($errors) > 0 || count($emptys) > 0 || count($conflicts) > 0) {
            $this->ajaxReturn($titlepic, "信息不正确", 0);
        }
        $result = M("course")->addAll($data_a);
        $this -> success("已成功保存");
    }
    public function schedule(){
        $this -> assign('term_fortag', $this->getTerm());
        if (isset($_GET['term'])) {
            $map['term'] = $_GET['term'];
            $this -> assign('term_current', $_GET['term']);
        } 
        if (isset($_GET["classid"])) {
            $this->assign('class_current',$_GET['classid']);
        }
        $m["item"] = 'HND';//分项目的限制开始
        $major = M("major")->where($m)->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $mbp["major"] = array("in",$majors);//分项目的限制结束
        $all_class=D('Class')->where($mbp)->order('year desc,name asc')->select();
        $class_fortag=array();
        foreach($all_class as $key=>$value){
            $class_fortag[$value['id']]='['.$value['year'].']'.$value['name'];
        }
        if (isset($_GET['term']) && isset($_GET["classid"])) {
            $condition["term"] = $_GET["term"];
            $condition["classid"] = $_GET["classid"];
            $table = M("schedule")->where($condition)->find();
            $this->assign("table",$table["table"]);
            $this->assign("id",$table["id"]);
        }
        $this -> assign('class_fortag', $class_fortag); 
        $this->menuCourse();
        $this->display();
    }
    public function uploadSchedule(){
        $this->menuCourse();
        $this->display();
    }
    public function downloadSchedule(){
        $id = $_GET["id"];
        if (!$id) {
            $this->error("未选择课表");
        }
        // Vendor('PHPExcel'); 
        $info = M("schedule")->where(array("id"=>$id))->find();
        $info["classname"] = M("class")->where(array("id"=>$info["classid"]))->getField("name");
        $titlepic = $info["savepath"];
        $php_path = dirname(__FILE__) . '/';
        $excelurl = $php_path .'../../../..'.$titlepic;
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $info["name"] = $info["classname"]."_".$info["term"].'课程表.xls';
        $p = PHPExcel_IOFactory::load($excelurl);//载入Excel
        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Content-Type:application/octet-stream");
        header('content-Type:application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition:attachment;filename='.$info["name"]);//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
        $objWriter->save('php://output');
    }
    public function scheduleInsert(){
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
        $sheetCount = $objPHPExcel->getSheetCount();
        for ($sheetnum=0; $sheetnum < $sheetCount; $sheetnum++) { 
            $objPHPExcel->setActiveSheetIndex($sheetnum);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
            $arr = array(); 
            $count = count($sheetData);//一共有多少行
            if ($count < 3) {
                $this->ajaxReturn($titlepic, "请填写信息", 0);
            }
            //到此为止都是可以复制的，$sheetdata里面存着所有信息，$inputFileName为文件完整路径
            $reg='/^[0-9]{4}-[0-9]{4}学年第[1-2]{1}学期$/';
            $term = $sheetData[1]["A"];
            if (strlen($sheetData[1]["A"]) == 0) {
                $emptys[$sheetnum][] = "A1";
            }
            if (strlen($sheetData[2]["A"]) == 0) {
                $emptys[$sheetnum][] = "A2";
            }
            if(!preg_match($reg,  strtr($term, $arr))){
                $errors[$sheetnum][] = "A1";
            }
            // $map["year"] = explode("-", $term)[0];
            $tmp = explode("-", $term);
            $map["year"] = $tmp[0];
            $map["name"] = $sheetData[2]["A"];
            if (!$map["name"]) {
                $errors[$sheetnum][] = "A2";
            }
            $data[$sheetnum]["term"] = $term;
            $data[$sheetnum]["classid"] = M("class")->where($map)->getField("id");
            $b = M("schedule")->where($data[$sheetnum])->count();
            if (!$data[$sheetnum]["classid"] || $b > 0) {
                $errors[$sheetnum][] = "A1";
                $errors[$sheetnum][] = "A2";
            }//dump($errors[0]);return;
            $data[$sheetnum]["savepath"] = $titlepic;
            if (count($errors[$sheetnum]) > 0) {
                excelwarning($inputFileName,$errors[$sheetnum],'FFFF7F50',$sheetnum);
            }
            if (count($conflicts[$sheetnum]) > 0) {
                excelwarning($inputFileName,$conflicts[$sheetnum],'FFFFC000',$sheetnum);
            }
            if (count($emptys[$sheetnum]) > 0) {
                excelwarning($inputFileName,$emptys[$sheetnum],'FF00B0F0',$sheetnum);
            }
            if (count($errors[$sheetnum]) == 0 && count($conflicts[$sheetnum]) == 0 && count($emptys[$sheetnum]) == 0) {
                $notice[$sheetnum]["title"] = '通知：新增课程表——'.$sheetData[2]["A"];
                $notice[$sheetnum]["content"] = '新增课程表，'.$sheetData[2]["A"].'，'.$sheetData[1]["A"].'，来自'.session('username').'['.session('truename').']，';
                $data[$sheetnum]["table"] = '';
                $data[$sheetnum]["table"] ='<tr height="29">
                        <td height="29" width="87">节</td>
                        <td width="81">时间/周</td>
                        <td width="165">周一</td>
                        <td width="165">周二</td>
                        <td width="165">周三</td>
                        <td width="165">周四</td>
                        <td width="165">周五</td>
                      </tr>
                      <tr height="46">
                        <td height="46" width="87">第一节</td>
                        <td width="81">8：00—8：50</td>
                        <td rowspan="2" width="165">'.$sheetData[4]["C"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[4]["D"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[4]["E"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[4]["F"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[4]["G"].'</td>
                      </tr>
                      <tr height="46">
                        <td height="46" width="87">第二节</td>
                        <td width="81">8：55—9：45</td>
                      </tr>
                      <tr height="46">
                        <td height="46" width="87">第三节</td>
                        <td width="81">10：00—10：50</td>
                        <td rowspan="2" width="165">'.$sheetData[6]["C"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[6]["D"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[6]["E"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[6]["F"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[6]["G"].'</td>
                      </tr>
                      <tr height="46">
                        <td height="46" width="87">第四节</td>
                        <td width="81">10：55—11：45</td>
                      </tr>
                      <tr height="46">
                        <td height="46" width="87">第五-六节</td>
                        <td width="81">12：00—13：50</td>
                        <td width="165">'.$sheetData[8]["C"].'</td>
                        <td width="165">'.$sheetData[8]["D"].'</td>
                        <td width="165">'.$sheetData[8]["E"].'</td>
                        <td width="165">'.$sheetData[8]["F"].'</td>
                        <td width="165">'.$sheetData[8]["G"].'</td>
                      </tr>
                      <tr height="46">
                        <td height="46" width="87">第七节</td>
                        <td width="81">14：00-14:50</td>
                        <td rowspan="2" width="165">'.$sheetData[9]["C"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[9]["D"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[9]["E"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[9]["F"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[9]["G"].'</td>
                      </tr>
                      <tr height="46">
                        <td height="46" width="87">第八节</td>
                        <td width="81">14：55—15：45</td>
                      </tr>
                      <tr height="46">
                        <td height="46" width="87">第九节</td>
                        <td width="81">16：00—16：50</td>
                        <td rowspan="2" width="165">'.$sheetData[11]["C"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[11]["D"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[11]["E"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[11]["F"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[11]["G"].'</td>
                      </tr>
                      <tr height="46">
                        <td height="46" width="87">第十节</td>
                        <td width="81">16：:55-17:45</td>
                      </tr>
                      <tr height="46">
                        <td rowspan="2" height="92" width="87">晚上</td>
                        <td rowspan="2" width="81">18：00-21：45</td>
                        <td rowspan="2" width="165">'.$sheetData[13]["C"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[13]["D"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[13]["E"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[13]["F"].'</td>
                        <td rowspan="2" width="165">'.$sheetData[13]["G"].'</td>
                      </tr>
                      <tr height="46"> </tr>
                      <tr height="19">
                        <td colspan="7" rowspan="2" height="38" width="993">'.$sheetData[15]["A"].'</td>
                      </tr>';
            }
        }
        if (count($errors) > 0 || count($emptys) > 0 || count($conflicts) > 0) {
            // excelwarning($inputFileName,$errors,'FFFF7F50');
            $this->ajaxReturn($titlepic,"信息不正确",0);
        }else{
            $schedule = M("schedule");
            foreach ($data as $sheetnum => $va) {
                $id = $schedule->add($va);
                $notice[$sheetnum]["content"] .= "下载地址：<a href='".U("downloadSchedule/id/".$id)."'>".U("downloadSchedule/id/".$id)."</a>";
                $notice[$sheetnum]["tusername"]=session('username');
                $notice[$sheetnum]["ttruename"]=session('truename');
                $notice[$sheetnum]["ctime"]=date('Y-m-d H:i:s');
            }
            M('Noticecreate')->addAll($notice);
            // M("schedule")->addAll($data);
            $this->success("保存成功");
        }
    }
    public function scheduleDel(){
        $map["id"] = $_POST["id"];
        $b = M("schedule")->where($map)->delete();
        if ($b) {
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
    public function menuExam() {
        $menu['exam']='考试安排';
        $menu["examUpload"]='上传考试安排';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function exam(){
        //考试列表
        $this -> assign('term_fortag', $this->getTerm());
        if (isset($_GET['term'])) {
            $map['term'] = $_GET['term'];
            $this -> assign('term_current', $_GET['term']);
        } 
        if (isset($_GET["classid"])) {
            $map["classid"] = $_GET["classid"];
            $this->assign('class_current',$_GET['classid']);
        }
        $m["item"] = 'HND';//分项目的限制开始
        $major = M("major")->where($m)->select();
        foreach ($major as $vm) {
            $majors[$vm["major"]]=$vm["major"];
        }
        $mbp["major"] = array("in",$majors);//分项目的限制结束
        $all_class=D('Class')->where($mbp)->order('year desc,name asc')->select();
        $class_fortag=array();
        foreach($all_class as $key=>$value){
            $class_fortag[$value['id']]='['.$value['year'].']'.$value['name'];
            $class_forlist[$value['id']]=$value['id'];
        }
        $map["classid"] = array("in",$class_forlist);
        $examlist = M("examlist")->where($map)->select();
        foreach ($examlist as $num => $ve) {
            foreach ($all_class as $vc) {
                if ($ve["classid"] == $vc["id"]) {
                    $examlist[$num]["classname"] = $vc["name"];
                    break;
                }
            }
        }
        $this->assign("examlist",$examlist);
        //此处还需要查询考试列表
        $this -> assign('class_fortag', $class_fortag); 
        $this->menuExam();
        $this->display();
    }
    public function examUpload(){
        $this->menuExam();
        $this->display();
    }
    public function examInsert(){
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
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',    
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
            $this->ajaxReturn($titlepic, "请填写信息", 0);
        }
        //到此为止都是可以复制的，$sheetdata里面存着所有信息，$inputFileName为文件完整路径
        for($i = 3; $i <= $count; $i++){
            $b = true;
            for($j = 1; $j <= 4; $j++){
                if(strlen($sheetData[$i][chr(64+$j)]) == 0){
                    $emptys[] = chr(64+$j).$i;
                    $b = false;
                }
            }
            $tmp = strtr($sheetData[$i]['A'], $arr).strtr($sheetData[$i]['B'], $arr).strtr($sheetData[$i]['C'], $arr).strtr($sheetData[$i]['D'], $arr);
            for ($k=$i+1; $k <=$count ; $k++) { 
                $tmp1 = strtr($sheetData[$k]['A'], $arr).strtr($sheetData[$k]['B'], $arr).strtr($sheetData[$k]['C'], $arr).strtr($sheetData[$k]['D'], $arr);
                if ($tmp == $tmp1) {
                    $conflicts[] = "A".$i;
                    $conflicts[] = "B".$i;
                    $conflicts[] = "C".$i;
                    $conflicts[] = "D".$i;
                    $conflicts[] = "A".$k;
                    $conflicts[] = "B".$k;
                    $conflicts[] = "C".$k;
                    $conflicts[] = "D".$k;
                    $b = false;
                }
            }
            if (!$b) {
                continue;
            }
            $reg='/^[0-9]{4}-[0-9]{4}学年第[1-2]{1}学期$/';
            $term = strtr($sheetData[$i]['A'], $arr);
            if(!preg_match($reg,  strtr($term, $arr))){
                $errors[] = "A".$i;
            }
            // $map["year"] = explode("-", $term)[0];
            $tmpmap = explode("-", $term);
            $map["year"] = $tmpmap[0];
            $map["name"] = strtr($sheetData[$i]['B'], $arr);
            if (!$map["name"]) {
                $errors[] = "B".$i;
            }
            $data[$i-3]["term"] = $term;
            $data[$i-3]["classid"] = M("class")->where($map)->getField("id");
            if (!$data[$i-3]["classid"]) {
                $errors[] = "A".$i;
                $errors[] = "B".$i;
            }else{
                $data[$i-3]["course"] = strtr($sheetData[$i]['C'], $arr);
                $data[$i-3]["time"] = strtr($sheetData[$i]['D'], $arr);
                $mbp["classid"] = $data[$i-3]["classid"];
                $mbp["name|ename"] = $data[$i-3]["course"];
                if (M("course")->where($mbp)->count() == 0) {
                    $errors[] = "B".$i;
                    $errors[] = "C".$i;
                }
                if (M("examlist")->where($data[$i-3])->count() > 0) {
                    $errors[] = "A".$i;
                    $errors[] = "B".$i;
                    $errors[] = "C".$i;
                    $errors[] = "D".$i;
                }
                $data[$i-3]["teacher"] = strtr($sheetData[$i]['E'], $arr);
                $data[$i-3]["form"] = strtr($sheetData[$i]['F'], $arr);
                $data[$i-3]["description"] = strtr($sheetData[$i]['G'], $arr);
            }
        }
        if (count($errors) > 0) {
            excelwarning($inputFileName,$errors);
        }
        if (count($conflicts) > 0) {
            excelwarning($inputFileName,$conflicts,'FFFFC000');
        }
        if (count($emptys) > 0) {
            excelwarning($inputFileName,$emptys,'FF00B0F0');
        }
        if (count($errors) > 0 || count($emptys) > 0 || count($conflicts) > 0) {
            $this->ajaxReturn($titlepic, "信息不正确", 0);
        }
        $result = M("examlist")->addAll($data);
        $notice["title"] = '通知：新增考试安排';
        $notice["content"] = strtr($sheetData[3]['A'], $arr).'考试安排，下载地址：<a href:"'.$titlepic.'">'.$titlepic.'</a>';
        $notice["tusername"]=session('username');
        $notice["ttruename"]=session('truename');
        $notice["ctime"]=date('Y-m-d H:i:s');
        M('Noticecreate')->add($notice);
        $this -> success("已成功保存");
    }
    public function examDel(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = M("examlist");
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    }
    public function uploadPreGrade(){
        //显示预科成绩
        $this->menuGrade();
        $this->display();
    }
    public function PreGradeInsert(){
        //上传预科成绩
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
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',    
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
            $this->ajaxReturn($titlepic, "请填写信息", 0);
        }
        $exam = $sheetData[1]["C"];//考试名称
        if (strlen($exam) == 0) {
            $errors[] = "C1";
            excelwarning($inputFileName,$errors);
            $this->ajaxReturn($titlepic, "请在C1中填写考试名称", 0);
        }
        $cs = M("classstudent");
        for($i = 3; $i <= $count; $i++){
            $b = true;
            for($j = 1; $j <= 7; $j++){
                if(strlen($sheetData[$i][chr(64+$j)]) == 0){
                    $errors[] = chr(64+$j).$i;
                    $b = false;
                }
                if ($j > 2) {
                    if (!is_numeric($sheetData[$i][chr(64+$j)])) {
                        $errors[] = chr(64+$j).$i;
                        $b = false;
                    }
                }
            }
            $map["studentname"] = strtr($sheetData[$i]['A'], $arr);
            $map["student"] = strtr($sheetData[$i]['B'], $arr);
            if ($cs->where($map)->count() == 0) {
                $errors[] = "A".$i;
                $errors[] = "B".$i;
                $b = false;
            }
            if (!$b) {
                continue;
            }
            $grade[$i-3]["examname"] = $exam;
            $grade[$i-3]["stuname"] = $map["studentname"];
            $grade[$i-3]["stunum"] = $map["student"];
            $grade[$i-3]["listening"] = strtr($sheetData[$i]['C'], $arr);
            $grade[$i-3]["reading"] = strtr($sheetData[$i]['D'], $arr);
            $grade[$i-3]["writing"] = strtr($sheetData[$i]['E'], $arr);
            $grade[$i-3]["speaking"] = strtr($sheetData[$i]['F'], $arr);
            $grade[$i-3]["total"] = strtr($sheetData[$i]['G'], $arr);
        }
        if (count($errors) > 0) {
            excelwarning($inputFileName,$errors);
            $this->ajaxReturn($titlepic, "信息不正确", 0);
        }
        $result = M("pregrade")->addAll($grade);
        $this -> success("已成功保存");
    }
    public function uploadPreGrade2(){
        //显示预科成绩
        $this->menuGrade();
        $this->display();
    }
    public function PreGradeInsert2(){
        //上传预科成绩
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
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',    
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
            $this->ajaxReturn($titlepic, "请填写信息", 0);
        }
        $exam = $sheetData[1]["C"];//考试名称
        if (strlen($exam) == 0) {
            $errors[] = "C1";
            excelwarning($inputFileName,$errors);
            $this->ajaxReturn($titlepic, "请在C1中填写课程名称", 0);
        }
        $cs = M("classstudent");//选出所有以及存有的课程
        $allcourse = M("course")->select();
        foreach ($allcourse as $vc) {
            $course[] = $vc["name"];
            $course[] = $vc["ename"];
        }
        if (!in_array(strtr($sheetData[1]['C'], $arr), $course)) {
            $errors[] = 'C1';
        }
        for($i = 3; $i <= $count; $i++){
            $b = true;
            for($j = 1; $j <= 3; $j++){
                if(strlen($sheetData[$i][chr(64+$j)]) == 0){
                    $errors[] = chr(64+$j).$i;
                    $b = false;
                }
                if ($j > 2) {
                    if (!is_numeric($sheetData[$i][chr(64+$j)])) {
                        $errors[] = chr(64+$j).$i;
                        $b = false;
                    }
                }
            }
            $map["studentname"] = strtr($sheetData[$i]['A'], $arr);
            $map["student"] = strtr($sheetData[$i]['B'], $arr);
            if ($cs->where($map)->count() == 0) {
                $errors[] = "A".$i;
                $errors[] = "B".$i;
                $b = false;
            }
            if (!$b) {
                continue;
            }
            $grade[$i-3]["examname"] = $exam;
            $grade[$i-3]["stuname"] = $map["studentname"];
            $grade[$i-3]["stunum"] = $map["student"];
            $grade[$i-3]["listening"] = 0;
            $grade[$i-3]["reading"] = 0;
            $grade[$i-3]["writing"] = 0;
            $grade[$i-3]["speaking"] = 0;
            $grade[$i-3]["total"] = strtr($sheetData[$i]['C'], $arr);
            $grade[$i-3]["isrequired"] = 1;
        }
        if (count($errors) > 0) {
            excelwarning($inputFileName,$errors);
            $this->ajaxReturn($titlepic, "信息不正确", 0);
        }
        $result = M("pregrade")->addAll($grade);
        $this -> success("已成功保存");
    }
    public function uploadProGrade(){
        //显示专业成绩
        $this->menuGrade();
        $this->display();
    }
    public function ProGradeInsert(){
        set_time_limit(120);
        //上传专业成绩
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
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',    
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
        $arr2 = array("a"=>"A","r"=>"R","d"=>"D","u"=>"U","c"=>"C","s"=>"S");
        $count = count($sheetData);//一共有多少行
        if ($count < 5) {
            $this->ajaxReturn($titlepic, "请填写信息", 0);
        }
        if (!$sheetData[2]['E']) {//检测E2中必须有课程名
            $this->ajaxReturn($titlepic, "请填写课程", 0);
        }else{
            $course = $sheetData[2]['E'];
        }
        //判断学期
        $reg='/^[0-9]{4}-[0-9]{4}学年第[1-2]{1}学期$/';
        $term = strtr($sheetData[1]['E'], $arr);
        if(!preg_match($reg,  strtr($term, $arr))){
            $errors[] = "E1";
        }
        //判断是否重修
        $isrepair = strtr($sheetData[1]['K'], $arr);
        if ($isrepair == "否") {
            $isrepair = 0;
        }else if ($isrepair == "是") {
            $isrepair = 1;
        }else{
            $errors[] = "K1";
        }
        //选出所有以及存有的课程
        $allcourse = M("course")->select();
        foreach ($allcourse as $vc) {
            $courses[] = $vc["name"];
            $courses[] = $vc["ename"];
        }
        $tmp = strtr($sheetData[2]["E"],$arr);
        $c = false;
        for ($i=0; $i < count($courses); $i++) { 
            if (strcmp($tmp,trim($courses[$i])) == 0) {
                $c = $courses[$i];
                break;
            }
        }
        if (!$c) {
            $errors[] = "E2";
        }

        $row = 0;//检查一共次考试
        $i = 67;//从第C列开始检查第四行的考试，非空则填充上第二行的课程名称
        while (strlen($sheetData[3][chr($i)]) != 0) {
            $i += 3;
            $row++;
        }
        $cs = D("ClassstudentView");
        $num = 0;//用num来计数，表示将要写入数据库的数组的下标
        for($i = 5; $i <= $count; $i++){
            $b = false;
            //检查学生信息是否正确
            // $map["studentname|ename"] = strtr($sheetData[$i]['A'], $arr);
            // $map["student|scnid"] = strtr($sheetData[$i]['B'], $arr);
            $map["studentname"] = strtr($sheetData[$i]['A'], $arr);
            $map["ename"] = strtr($sheetData[$i]['A'], $arr);
            $map["student"] = strtr($sheetData[$i]['B'], $arr);
            $map["scnid"] = strtr($sheetData[$i]['B'], $arr);
            $map["_logic"] = "or";
            $info = $cs->where($map)->find();
            if (!$info) {
                $errors[] = "A".$i;
                $errors[] = "B".$i;
                continue;
            }
            //对这一行的所有分数遍历
            $renum = 0;
            for ($m = 1; $m <= $row*3; $m+=3) { 
                $grade[$num]["course"] = $c;//课程名称
                $grade[$num]["examname"] = strtr($sheetData[3][chr(66+$m)], $arr);//考试名称
                $grade[$num]["stuname"] = strtr($sheetData[$i]['A'], $arr);
                $grade[$num]["stunum"] =  $info["student"];
                $grade[$num]["term"] = $term;
                $grade[$num]["isrepair"] = $isrepair;
                $b = false;
                for ($k = 0; $k < 3; $k++) {
                    $the = strtr($sheetData[$i][chr(66+$m+$k)], $arr);
                    $the = strtr($the, $arr2);
                    if ($the == "S") {
                        $b = true;
                        switch ($k) {
                            case 0:
                                $grade[$num]["letter"] = "S";
                                $grade[$num]["hundred"] = 90;
                                break;
                            case 1:
                                $grade[$num]["letter"] = "RD";
                                $grade[$num]["hundred"] = 80;
                                break;
                            case 2:
                                $grade[$num]["letter"] = "RA";
                                $grade[$num]["hundred"] = 70;
                                break;
                        }
                        break;
                    }else if ($the == "U" || $the == "U(C)" || $the == "U(A)") {
                        $b = true;
                        $grade[$num]["letter"] = $the;
                        $grade[$num]["hundred"] = 0;
                        break;
                    }
                }
                if (!$b) {
                    /*$errors[] = chr(66+$m).$i;
                    $errors[] = chr(67+$m).$i;
                    $errors[] = chr(68+$m).$i;*/
                    unset($grade[$num]);
                }
                $num++;
            }
        }
        if (count($errors) > 0) {
            // dump($errors);return;
            excelwarning($inputFileName,$errors);
            $this->ajaxReturn($titlepic, "信息不正确", 0);
        }
        if (count($repairs) > 0) {
            $this->repair($repairs);
        }
        $result = M("prograde")->addAll($grade);
        $this -> success("已成功保存");

    }
    public function downPreScore(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
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
        $scores = M("pregrade")->where(array("stunum"=>$id,"isrequired"=>0))->select();//英语成绩
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
        $scores = M("pregrade")->where(array("stunum"=>$id,"isrequired"=>1))->select();//必修课成绩
        $course = M("course");
        $p  ->setActiveSheetIndex(0)
            ->setCellValue('A'.$line, "必修课成绩") ;
        $newline = $line+1;
        foreach ($scores as $k => $va) {
            $tmp = $course->where(array("name|ename"=>$va['examname']))->find();
            $p  ->setActiveSheetIndex(0)
                ->setCellValue("A".$newline,$tmp["name"]."：")
                ->setCellValue("D".$newline,$va['total']."分")
                ->setCellValue("E".$newline,$tmp["ename"].":")
                ->setCellValue("H".$newline,$va['total'])
                ->getStyle('A'.($newline).':H'.($newline))->applyFromArray($styleArray);
                $p  ->getActiveSheet()->mergeCells( 'A'.$newline.':C'.$newline);
                $p  ->getActiveSheet()->mergeCells( 'E'.$newline.':G'.$newline);
            $newline++;
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
        //导出字母制成绩
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        // Vendor('PHPExcel'); 
        $titlepic = '/buaahnd/sys/Tpl/Public/download/proscore.xls';
        $php_path = dirname(__FILE__) . '/';
        $excelurl = $php_path .'../../../..'.$titlepic;
        $stuinfo = D("ClassstudentView")->where(array("student"=>$id))->find();
        if (!$stuinfo) {
            $this -> error('无此学生'.$id);
        }
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $p = PHPExcel_IOFactory::load($excelurl);//载入Excel
        $p  ->setActiveSheetIndex(0)
            ->setCellValue('A2', '学号：'.$stuinfo["student"].'        姓名：'.$stuinfo["studentname"].'      专业：'.$stuinfo["major"]);//写上名字
        $scores = M("prograde")->where(array("stunum"=>$id))->select();//选出所有考试的分数
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
        $line = 6;//从第7行开始写，每门课加1
        $row = 64;//从B列开始写，每学期加2
        $course = M("course");
        foreach ($willwrite as $termname => $vw) {
            $row = $row + 2;
            $p  ->setActiveSheetIndex(0)
                ->setCellValue(chr($row)."5",$termname)
                ->mergeCells(chr($row)."5:".chr($row+1)."5")
                ->setCellValue(chr($row)."6","Marks")
                ->setCellValue(chr($row+1)."6","Credits");
            foreach ($vw as $coursename => $vs) {
                $line++;
                $map["classid"] = $stuinfo["classid"];
                $map["name|ename"] = $coursename;
                $credit = $course->where($map)->getField("credit");//获取学分
                if ($vs["hundred"] == 0) {//这里开始处理转化为字母的问题
                    $hundred = 0;
                    $letter = "U";
                }else{
                    $hundred = $vs["hundred"]/$vs["count"];
                    if ($vs["standard"] == "letter") {
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
                                $letter = "D";
                                break;
                        }
                    }else{
                        if ($hundred >= 60) {
                            $letter = "P";
                        }else{
                            $letter = "F";
                        }
                    }
                }
                $p  ->setActiveSheetIndex(0)
                    ->setCellValue("A".$line,$coursename)
                    ->setCellValue(chr($row).$line,$letter)
                    ->setCellValue(chr($row+1).$line,$credit);
            }
        }
        $styleThinBlackBorderOutline = array( 
            'borders' => array ( 
                'allborders' => array ( 
                    'style' => PHPExcel_Style_Border::BORDER_THIN, //设置border样式 
                    'color' => array ('argb' => 'FF000000'), //设置border颜色 
                ), 
            ),
        );
        $p->getActiveSheet()->getStyle('A5:'.chr($row+1).$line)->applyFromArray($styleThinBlackBorderOutline);
        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Content-Type:application/octet-stream");
        header('content-Type:application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition:attachment;filename='.$stuinfo["student"].'-'.$stuinfo["studentname"].'-专业课成绩单（字母制）.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function downProScoreB(){
        //导出百分制成绩
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        // Vendor('PHPExcel'); 
        $titlepic = '/buaahnd/sys/Tpl/Public/download/proscore.xls';
        $php_path = dirname(__FILE__) . '/';
        $excelurl = $php_path .'../../../..'.$titlepic;
        $stuinfo = D("ClassstudentView")->where(array("student"=>$id))->find();
        if (!$stuinfo) {
            $this -> error('无此学生'.$id);
        }
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $p = PHPExcel_IOFactory::load($excelurl);//载入Excel
        $p  ->setActiveSheetIndex(0)
            ->setCellValue('A2', '学号：'.$stuinfo["student"].'        姓名：'.$stuinfo["studentname"].'      专业：'.$stuinfo["major"]);//写上名字
        $scores = M("prograde")->where(array("stunum"=>$id))->select();//选出所有考试的分数
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
        $line = 6;//从第7行开始写，每门课加1
        $row = 64;//从B列开始写，每学期加2
        $course = M("course");
        foreach ($willwrite as $termname => $vw) {
            $row = $row + 2;
            $p  ->setActiveSheetIndex(0)
                ->setCellValue(chr($row)."5",$termname)
                ->mergeCells(chr($row)."5:".chr($row+1)."5")
                ->setCellValue(chr($row)."6","Marks")
                ->setCellValue(chr($row+1)."6","Credits");
            foreach ($vw as $coursename => $vs) {
                $map["classid"] = $stuinfo["classid"];
                $map["name|ename"] = $coursename;
                $credit = $course->where($map)->getField("credit");//获取学分
                if (!$credit) {
                    continue;
                }
                $line++;
                if ($vs["hundred"] == 0) {//这里开始处理转化为字母的问题
                    $hundred = 0;
                    $letter = "U";
                }else{
                    $hundred = $vs["hundred"]/$vs["count"];
                    $hundred = round($hundred,2);
                }
                $p  ->setActiveSheetIndex(0)
                    ->setCellValue("A".$line,$coursename)
                    ->setCellValue(chr($row).$line,$hundred)
                    ->setCellValue(chr($row+1).$line,$credit);
            }
        }
        $styleThinBlackBorderOutline = array( 
            'borders' => array ( 
                'allborders' => array ( 
                    'style' => PHPExcel_Style_Border::BORDER_THIN, //设置border样式 
                    'color' => array ('argb' => 'FF000000'), //设置border颜色 
                ), 
            ),
        );
        $p->getActiveSheet()->getStyle('A5:'.chr($row+1).$line)->applyFromArray($styleThinBlackBorderOutline);
        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Content-Type:application/octet-stream");
        header('content-Type:application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition:attachment;filename='.$stuinfo["student"].'-'.$stuinfo["studentname"].'-专业课成绩单（百分制）.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    public function repair($all){
        //这里专门处理重修
        $tuition = (int)M("system")->where("name='tuition'")->getField("content");
        $classstudent = D("ClassstudentView");
        $course = M("course");
        foreach ($all as $num => $va) {
            $stuinfo = $classstudent->where("student=".$va["stunum"])->find();
            $map["classid"] = $stuinfo["classid"];
            $map["name|ename"] = $va["course"];
            $courseinfo = $course->where($map)->find();
            $willpay[$num]["feename"] = $courseinfo["name"].'重修费';
            $willpay[$num]["name"] = $stuinfo["studentname"];
            $willpay[$num]["stunum"] = $stuinfo["student"];
            $willpay[$num]["idcard"] = $stuinfo["idcard"];
            $willpay[$num]["standard"] = $courseinfo["credit"]*$tuition;
        }
        M("payment")->addAll($willpay);
    }
} 

?>