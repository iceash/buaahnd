<?php
class EduSenAction extends CommonAction {
    public function index() {
        $User = D('User');
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
        $a = array('zhname'=>'名','FamilyName'=>'FamilyName','surname'=>'姓','FirstName'=>'FirstName','birthday'=>'出生日期','sex'=>'性别','gender'=>'Gender','address'=>'家庭住址','HomeAddress'=>'HomeAddress','postaladdress'=>'通信地址','CorrespondenceAddress'=>'CorrespondenceAddress','phone'=>'固定电话','mobile'=>'手机','email'=>'Email1','Email2'=>'Email2','MSN'=>'MSN','qq'=>'OICQ','nativeprovince'=>'省份','Province'=>'Province','nativecity'=>'城市','City'=>'City','idcardpassport'=>'身份证护照号码','project'=>'就读国内项目名称','HNDCenter'=>'HNDCenter','year'=>'入学时间','grade'=>'所属年级','entrancescore'=>'高考成绩总分','englishscore'=>'英语单科成绩','entrancefull'=>'高考分数标准','major'=>'HND专业','drop'=>'是否退学','repeat'=>'是否留级','SCN'=>'SCN号','listeningscore'=>'听力得分','readingscore'=>'阅读得分','writingscore'=>'写作得分','speakingscore'=>'口语得分','testscore'=>'进入专业课英语成绩总分','score1'=>'最优有效雅思成绩','score1id'=>'雅思考试号','plus'=>'其他','HNDtime'=>'获得HND证书时间','quit'=>'是否留学','country'=>'留学国家','Country'=>'Country','school'=>'国外院校名称','ForeignUniversityApplied'=>'ForeignUniversityApplied','fmajor'=>'留学所学专业','together'=>'出国所经过中介名称','employ'=>'是否就业','enterprise'=>'就业企业名称','workaddress'=>'就业企业所在省市','enterprisenature'=>'就业企业性质','individualorientationandspecialty'=>'个人情况介绍及特长','professionalcertificate'=>'所获得职业资格证书','xuben'=>'续本','xubensch'=>'续本国内院校名称','degreesch'=>'将获得哪所院校颁发学位','xubenmajor'=>'续本专业');
        return $a;
    }
        public function getClassStudent($classid){
        $map['id'] = $classid;
        $dao = D('StudentView');
        $my = $dao->where($map)->order('student asc')->group('student')->select();
        //$my = $dao->where($map)->order('student asc')->select();
        foreach($my as $key=>$value){
            $my[$key]['enamearr'] = preg_split('/(?<=[a-z])(?=[A-Z])/',$value['ename']);
            if(strlen(trim($value['studentname'])) > 6 ){
                if(in_array(substr(trim($value['studentname']),0,6),$this->getCompoundSurname())){
                    $my[$key]['surname'] = substr(trim($value['studentname']),0,6);
                    $my[$key]['zhname'] = substr(trim($value['studentname']),6);
                    $my[$key]['FamilyName'] = $my[$key]['enamearr'][0].$my[$key]['enamearr'][1];
                    $my[$key]['FirstName'] = implode('',array_slice($my[$key]['enamearr'],2));
                }else{
                    $my[$key]['surname'] = substr(trim($value['studentname']),0,3);
                    $my[$key]['zhname'] = substr(trim($value['studentname']),3);
                    $my[$key]['FamilyName'] = $my[$key]['enamearr'][0];
                    $my[$key]['FirstName'] = implode('',array_slice($my[$key]['enamearr'],1));  
                }
            }else{
                    $my[$key]['surname'] = substr(trim($value['studentname']),0,3);
                    $my[$key]['zhname'] = substr(trim($value['studentname']),3);
                    $my[$key]['FamilyName'] = $my[$key]['enamearr'][0];
                    $my[$key]['FirstName'] = implode('',array_slice($my[$key]['enamearr'],1));  
            }
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
               $my[$k1][$k2]['enamearr'] = preg_split('/(?<=[a-z])(?=[A-Z])/',$v2['ename']);
            if(strlen(trim($v2['studentname'])) > 6 ){
                if(in_array(substr(trim($v2['studentname']),0,6),$this->getCompoundSurname())){
                    $my[$k1][$k2]['surname'] = substr(trim($v2['studentname']),0,6);
                    $my[$k1][$k2]['zhname'] = substr(trim($v2['studentname']),6);
                    $my[$k1][$k2]['FamilyName'] = $my[$k1][$k2]['enamearr'][0].$my[$k1][$k2]['enamearr'][1];
                    $my[$k1][$k2]['FirstName'] = implode('',array_slice($my[$k1][$k2]['enamearr'],2));
                }else{
                    $my[$k1][$k2]['surname'] = substr(trim($v2['studentname']),0,3);
                    $my[$k1][$k2]['zhname'] = substr(trim($v2['studentname']),3);
                    $my[$k1][$k2]['FamilyName'] = $my[$k1][$k2]['enamearr'][0];
                    $my[$k1][$k2]['FirstName'] = implode('',array_slice($my[$k1][$k2]['enamearr'],1));  
                }
            }else{
                    $my[$k1][$k2]['surname'] = substr(trim($v2['studentname']),0,3);
                    $my[$k1][$k2]['zhname'] = substr(trim($v2['studentname']),3);
                    $my[$k1][$k2]['FamilyName'] = $my[$k1][$k2]['enamearr'][0];
                    $my[$k1][$k2]['FirstName'] = implode('',array_slice($my[$k1][$k2]['enamearr'],1));  
            }
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
    } 
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
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function menuGrade() {
        $menu['grade']='成绩首页';
        $menu['usualGrade']='查看导入成绩';
        $menu['usualResultAdd']='导入往年成绩';
        $menu['resultComplemented']='补全成绩信息';
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
        $this -> assign('category_fortag', $this->getYear());
        if (isset($_GET['searchkey'])) {
            $map['name'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['year'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        } 
        $dao = D('Class');
        $dao2 = D('Classstudent');
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
        $Score = D("Score");
        $map['susername']=$id;
        $map['isvisible']=1;
        $term=$Score -> where($map) ->field('term')->group('term')->order('term asc')-> select();
        $term_num=count($term);
        if($term_num>0){
            foreach($term as $key=>$value){
                $map['term']=$value['term'];
                $my[$key]=$Score -> where($map) -> select();
            }
            $this->assign('id',$id);
            $this->assign('my',$my);
        } 
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
        }else{
            $major=$classinfo[0][major];
        }
        if($classinfo[0][majore] == ''){
            $majore='_____________________________________';
        }else{
            $majore=$classinfo[0][majore];
        }
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
            $this -> error('Part1 学生学号不能为空');
        }elseif(!isusername($_POST['username'])){
          $this -> error('Part1 学生学号校验失败');  
        }
        if (empty($_POST['cyear'])) {
            $this -> error('Part1 入学年份不能为空');
        }elseif (!isctime($_POST['cyear'])) {
            $this -> error('Part1 入学年份校验失败');
        }
        if (empty($_POST['sex'])) {
            $this -> error('Part1 性别不能为空');
        }elseif (!issex($_POST['sex'])) {
            $this -> error('Part1 学生性别校验失败');
        }
        
        if (empty($_POST['mobile'])) {
            $this -> error('Part1 学生手机号不能为空');
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
        if ($dao -> create()) {
            $dao -> abroad = implode(',', $_POST['abroad']);
            $dao -> infosource = implode(',', $_POST['infosource']);
            $dao -> sourcenewspaper = implode(',', $_POST['sourcenewspaper']);
            $dao -> sourcenet = implode(',', $_POST['sourcenet']);
            $checked = $dao->save();
            if ($checked>0) {
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
            $dao_class = D('Class');
            $map_class['isbiye']=0;
            $dtree_class = $dao_class->where($map_class)->order('year desc,name asc')-> select();
            $dtree_year=$dao_class ->where($map_class)->field('year')-> group('year')->order('year desc')->select();
            $dao2 = D('ClassstudentView');
            $dtree_stu = $dao2->order('student asc')-> select();
            $this->assign('dtree_year',$dtree_year);
            $this -> assign('dtree_class', $dtree_class);
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
            if (M("enroll")->where($map)->count() == 0) {
                $this -> error('无此学生基本信息');
            }
            M("enroll")->where($map)->setField("username",$_POST["student"]);
            //$dao->student = $student;
            // $pinyin=$this->getPinyin($studentname);
            // $dao->ename=$pinyin;
            // $dao->enamesimple=$this->getPinyinSimple($pinyin);
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
            $student = M("enroll")->where($map)->setField("username",$_POST["student"]);
            if (!$student) {
                $this -> error('无此学生基本信息');
            }
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
        $this -> menuGrade();
        $this -> display();
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
        $stu=explode(',',$_POST['stu']);
        $dao = D('Reward');
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
            }
        }
        if($i==$j){
            $this -> success('已成功保存'.$j.'个学生的奖惩记录');
        }else{
            $this -> error("勾选了$i个学生,只增加了$j个学生");
        }
        
    } 
    public function course() {
        if (isset($_GET['searchkey'])) {
            $map['name|number'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        $dao = D('Course');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('number asc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> menuCourse();
        $this -> display();
    }
    public function usualGrade(){
        if (isset($_GET['searchkey'])) {
            $map['struename|susername|term|coursename'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        $map['subtime']='1888-08-08 08:08:08';
        $dao = D('Score');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc,susername asc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> menuGrade();
        $this -> display();
    } 
       public function usualGradeDel() {
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
           
//             for($j=1;$j<12;$j++){
//                 if(strlen($sheetData[$i][chr(65+$j)]) == 0){
//                   $this->error("必填项未填全，请填全后提交");
//                 }
//             }
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
        $section->addText('HND'.' '.'在读证明', array('bold'=>true,'name'=>'Arial','size'=>'15'), 'pStyle');
        $section->addTextBreak(2);
        $textrun = $section->createTextRun();
        $textrun->addText("　　"."兹证明",$zhongzhengwen);
        $textrun->addText($name,array('bold'=>true,'name'=>'Arial','size'=>'12'));
        $textrun->addText("同学，".$zsex."，生于".$year."年".$month."月".$day."日，于".$enrollyear."年9月进入南京大学大学外语部英国高等教育文凭（Higher National Diploma 简称HND）项目学习。（该项目引进自苏格兰学历管理委员会）。",$zhongzhengwen);
        $section->addTextBreak(1);
        $section->addText("　　"."该学生目前为该项目".$major."专业".$current_grade."年级学生。",$zhongzhengwen);
        $section->addTextBreak(1);
        $section->addText('特此证明',$zhongzhengwen);
        $section->addTextBreak(2);
        $section->addText('南京大学 大学外语部',$zhongzhengwen,'YStyle');
        $section->addText($nowyear."年".$nowmonth."月".$nowday."日",$zhongzhengwen,'YStyle');
        $section->addTextBreak(3);
        $section->addText("$current_time",array('name'=>'Times New Roman','size'=>'12'));
        $section->addText('HND Student Status Certificate',array('bold'=>true,'name'=>'Times New Roman','size'=>'15'), 'pStyle');
        $section->addTextBreak(3);
        $textrun = $section->createTextRun();
        $textrun->addText("This is to certify that student ",array('name'=>'Times New Roman','size'=>'12'));
        $textrun->addText($mystuename,array('bold'=>true,'name'=>'Times New Roman','size'=>'12'));
        $textrun->addText("," .$sex.", born on"." ".$birthday.", was enrolled in Department of Applied Foreign Language Studies, Nanjing University in Sep".".".$enrollyear.".",array('name'=>'Times New Roman','size'=>'12'));
        $section->addTextBreak(1);
        $section->addText("The student above is now in the"." ".$current_egrade." "."year of Higher National Diploma, which is introduced from Scottish Qualifications Authority (SQA, UK), with the major of"." ".$majore.".",array('name'=>'Times New Roman','size'=>'12'));
        $section->addTextBreak(1);
        $section->addText('Hereby Certify.',array('name'=>'Times New Roman','size'=>'12'));
        $section->addTextBreak(1);
        $section->addText('Department of Applied Foreign Language Studies',array('name'=>'Times New Roman','size'=>'12'), 'YStyle');
        $section->addText('Nanjing University',array('name'=>'Times New Roman','size'=>'12'), 'YStyle');
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
'＂'=>'"'); 
        $count = count($sheetData);//一共有多少行
        if ($count < 3) {
            $this->ajaxReturn($count, "请填写信息", 0);
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
            for($j = 1; $j < 8; $j++){
                if(strlen($sheetData[$i][chr(65+$j)]) == 0){
                    $errors[] = chr(65+$j).$i;
                    $b = false;
                }
            }
            $map["year"] = $sheetData[$i]['A'];
            $map["name"] = $sheetData[$i]['B'];
            $classinfo = M("class")->where($map)->find();
            if (!$classinfo) {
                $classdata[$i]["year"] = strtr($sheetData[$i]['A'], $arr);
                $classdata[$i]["major"] = strtr($sheetData[$i]['C'], $arr);
                if (!M("major")->where($classdata[$i])->find()) {
                    $errors[] = 'C'.$i;
                    continue;
                }
                $classdata[$i]["name"] = strtr($sheetData[$i]['B'], $arr);
                $classdata[$i]["ctime"] = date('y-m-d h:i:s',time());
                $data_a[$i-3]["classid"] = $class->add($classdata[$i]);
            }else{
                $data_a[$i-3]["classid"] = $classinfo["id"];
            }
            $data_a[$i-3]['student'] = strtr($sheetData[$i]['D'], $arr);
            $data_a[$i-3]['studentname'] = strtr($sheetData[$i]['E'], $arr);
            $data_a[$i-3]['ename'] = strtr($sheetData[$i]['F'], $arr);
            $data_a[$i-3]['enamesimple'] = strtr($sheetData[$i]['G'], $arr);
            $search["idcard"] = strtr($sheetData[$i]['H'], $arr);
            $data_a[$i-3]['idcard'] = $search["idcard"];
            
            if (!M("enroll")->where($search)->setField("username",$data_a[$i-3]['student'])) {
                $errors[] = 'H'.$i;
            }
        }//for循环结束
        if (count($errors) > 0) {
            excelwarning($inputFileName,$errors);
            $this->ajaxReturn($titlepic, "信息不正确", 0);
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
'＂'=>'"'); 
        $count = count($sheetData);//一共有多少行
        if ($count < 3) {
            $this->ajaxReturn($count, "请填写信息", 0);
        }
        //到此为止都是可以复制的，$sheetdata里面存着所有信息，$inputFileName为文件完整路径
        for ($i = 3; $i <= $count; $i++) { 
            for ($j = 1; $j <= 6; $j++){
                if(strlen($sheetData[$i][chr(65+$j)]) == 0){
                    $errors[] = chr(65+$j).$i;
                    $b = false;
                }
            }
            if (!$b) {
                continue;
            }
            $map["year"] = strtr($sheetData[$i]['A'], $arr);
            $map["name"] = strtr($sheetData[$i]['B'], $arr);
            $classid = M("class")->where($map)->getField("id");
            if (!$classid) {
                $errors[] = 'B'.$i;
                continue;
            }
            $data_a[$i-3]['classid'] = $classid;
            $data_a[$i-3]['name'] = strtr($sheetData[$i]['C'], $arr);
            $data_a[$i-3]['ename'] = strtr($sheetData[$i]['D'], $arr);
            $data_a[$i-3]['category2'] = strtr($sheetData[$i]['E'], $arr);
            $data_a[$i-3]['credit'] = strtr($sheetData[$i]['F'], $arr);
            $data_a[$i-3]['school'] = strtr($sheetData[$i]['G'], $arr);
            $data_a[$i-3]['category1'] = strtr($sheetData[$i]['H'], $arr);
            $data_a[$i-3]['coursetime'] = strtr($sheetData[$i]['I'], $arr);
            $data_a[$i-3]['exammethod'] = strtr($sheetData[$i]['J'], $arr);
            $data_a[$i-3]['category3'] = strtr($sheetData[$i]['K'], $arr);
            $data_a[$i-3]['level'] = strtr($sheetData[$i]['L'], $arr);
            $data_a[$i-3]['master'] = strtr($sheetData[$i]['M'], $arr);
            $data_a[$i-3]['teachers'] = strtr($sheetData[$i]['N'], $arr);
            $data_a[$i-3]['book'] = strtr($sheetData[$i]['O'], $arr);
            $data_a[$i-3]['intro'] = strtr($sheetData[$i]['P'], $arr);
            $data_a[$i-3]['eintro'] = strtr($sheetData[$i]['Q'], $arr);
            $data_a[$i-3]['plus'] = strtr($sheetData[$i]['R'], $arr);
        }
        if (count($errors) > 0) {
            excelwarning($inputFileName,$errors);
            $this->ajaxReturn($titlepic, "信息不正确", 0);
        }
        M("course")->addAll($data_a);
        $this -> success("已成功保存");
    }
} 

?>