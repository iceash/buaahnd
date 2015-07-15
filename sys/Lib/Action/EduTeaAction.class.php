<?php
class EduTeaAction extends CommonAction {
	public function index() {
        if(isset($_GET['isenglish']) && $_GET['isenglish'] == '0') {
            session('isenglish', '0');
        }
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
        if(session('isenglish') == '1') {
            $this->display('eindex');
        } else {
            $this -> display();
        }
		
	} 
    public function eindex() {
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
        session('isenglish', '1');
        $ename = D('Teacher')->where($map)->getField('ename');
        session('ename', $ename);
		$this -> display();
    }
    public function menuCourse() {
        $menu['course']='任课情况首页';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function emenuCourse() {
        $menu['course']='Home page of Courses';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function menuAsn() {
        $menu['asn']='所有作业';
        $menu['asnAdd']='新建作业';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function emenuAsn() {
        $menu['asn']='Total Homework';
        $menu['asnAdd']='New Homework';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function menuScore() {
        $menu['score']='成绩录入首页';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function emenuScore() {
        $menu['score']='Results Input Homepage';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function menuInfo() {
        $menu['info']='个人资料首页';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function getTerm() {
        $a=array();
        $dao1 = D('Courseteacher');
        $term=$dao1 ->field('term')-> group('term')->order('term desc')->select();
        foreach($term as $key=>$value){
            $a[$value['term']]=$value['term'];
        }
        return $a; 
	}
    public function course() {
        $this -> assign('category_fortag', $this->getTerm());
        if (isset($_GET['searchkey'])) {
            $map['coursenumber|name'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['term'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        } 
        $dao = D('CourseteacherView');
        $map['teacher'] = session('username');
        $count = $dao -> where($map) -> count();
        
        
        if(session('isenglish') == '1') {
            if ($count > 0) {
                import("@.ORG.Page");
                $listRows = 20;
                $p = new Page($count, $listRows);
                $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('term desc,coursenumber asc,id desc') -> select();
                $p->setConfig('header','records');
                $p->setConfig('theme', '%totalRow% %header%  %nowPage%/%totalPage% Pages');
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
            } 
            $this -> emenuCourse();
            $this->display('ecourse');
        } else {
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
    } 
     public function score() {
        $this -> assign('category_fortag', $this->getTerm());
        if (isset($_GET['searchkey'])) {
            $map['coursenumber|name'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['term'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        } 
        $dao = D('CourseteacherView');
        $map['teacher'] = session('username');
        $count = $dao -> where($map) -> count();
         
        
        if(session('isenglish') == '1') {
            if ($count > 0) {
                import("@.ORG.Page");
                $listRows = 20;
                $p = new Page($count, $listRows);
                $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('term desc,coursenumber asc,id desc') -> select();
                $p->setConfig('header','records');
                $p->setConfig('theme', '%totalRow% %header%  %nowPage%/%totalPage% Pages');
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
            }
            $this -> emenuScore();
            $this->display('escore');
        } else {
            if ($count > 0) {
                import("@.ORG.Page");
                $listRows = 20;
                $p = new Page($count, $listRows);
                $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('term desc,coursenumber asc,id desc') -> select();
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
            } 
            $this -> menuScore();
            $this -> display();
        }
    } 
    public function courseStu() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        $dao = D('ScoreclassView');
        $map['subjectid']=$result1['id'];
        $map['tusername']=$result1['teacher'];
        $map['coursenumber']=$result1['coursenumber'];
        $map['term']=$result1['term'];
        $count = $dao -> where($map) -> order('susername asc') -> count();
        
        $this -> assign('result1', $result1);        
        $this -> assign('my', $my);
        
        if(session('isenglish') == '1') {
            if ($count > 0) {
                import("@.ORG.Page");
                $listRows = 1000;
                $p = new Page($count, $listRows);
                $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('year desc,classname asc,susername asc') -> select();
                $p->setConfig('header','records');
                $p->setConfig('theme', '%totalRow% %header%  %nowPage%/%totalPage% Pages');
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
            } 
            foreach($my as $vo){
                $arr[]=$vo['classename'];
            }
            $class=array_unique($arr);
            $this -> assign('class', $class);
            $this -> emenuCourse();
            $this->display('ecourseStu');
        } else {
            if ($count > 0) {
                import("@.ORG.Page");
                $listRows = 1000;
                $p = new Page($count, $listRows);
                $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('year desc,classname asc,susername asc') -> select();
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
            } 
            foreach($my as $vo){
                $arr[]=$vo['classname'];
            }
            $class=array_unique($arr);
            $this -> assign('class', $class);
            $this -> menuCourse();
            $this -> display();
        }
    } 
    public function scoreAdd() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        $dao = D('ScoreclassView');
        $map['subjectid']=$result1['id'];
        $map['tusername']=$result1['teacher'];
        $map['coursenumber']=$result1['coursenumber'];
        $map['term']=$result1['term'];
        $count = $dao -> where($map) -> order('susername asc') -> count();
        if ($count > 0) {
            $my = $dao -> where($map) -> order('year desc,classname asc,susername asc') -> select();
            $my1 = $dao -> field('ordinaryscore')->where($map) ->find();
            $my2 = $dao -> field('qimoscore')->where($map) ->find();
            $c=explode(':',$my1['ordinaryscore']);
            $this -> assign('ordtime',$c[0]);
            $this -> assign('my1', $my1);
            $this -> assign('my2', $my2);
            
            $this -> assign('my', $my);
            $this -> assign('result1', $result1); 
            
            $this -> assign('uploadid', $id); 
        }
        
        //$this -> menuScoreadd($id);
        if($result1['issub']=='1'){
            if(session('isenglish') == '1') {
                $this -> assign('plus', $plus); 
                foreach($my as $key=>$value){
                    $class[$value['classename']][]=$value;
                }
                $this -> assign('class', $class);
                $this -> emenuScore();
                $this->display('escoreAddNotChange');
            } else {
                $this -> assign('plus', $plus); 
                foreach($my as $key=>$value){
                    $class[$value['classname']][]=$value;
                }
                $this -> assign('class', $class);
                $this -> menuScore();
                $this -> display('scoreAddNotChange');
            }
        }else{
            if(session('isenglish') == '1') {
                $plus=array();
                $plus['Transaction']='Transaction';
                $plus['Absence']='Absence';
                $plus['Exemption']='Exemption';
                $plus['Cheating']='Cheating';
                $plus['Delay']='Delay';
                $this -> assign('plus', $plus);
                
                foreach($my as $key=>$value){
                    $class[$value['classename']][]=$value;
                }
                $this -> assign('class', $class);
                $this -> emenuScore();
                $this->display('escoreAdd');
            } else {
                $plus=array();
                $plus['异动']='异动';
                $plus['旷考']='旷考';
                $plus['免修']='免修';
                $plus['作弊']='作弊';
                $plus['缓考']='缓考';
                $this -> assign('plus', $plus); 
                foreach($my as $key=>$value){
                    $class[$value['classname']][]=$value;
                }
                $this -> assign('class', $class);
                $this -> menuScore();
                $this -> display();
            }
        }
       
    } 
    public function scoreAddupload() 
    {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        $etime=array("0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7");
        foreach($etime as $ek=>$ev){ 
            $etime[$ev]=$ev;
        }
        $this -> assign('etime', $etime);
        foreach($my as $key=>$value){
            $class[$value['classname']][]=$value;
        }
        $this -> assign('result1', $result1); 
        $this -> assign('uploadid', $id); 
        
        if(session('isenglish') == '1') {
            $this -> emenuScore();
            $this->display('escoreAddupload');
        } else {
            $this -> menuScore();
            $this -> display();
        }
    }
    public function getordscore(){
        $id = $_REQUEST['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        $dao2 = D('ScoreclassView');
        $map2['subjectid']= $id;
        $result2=$dao2 -> where($map2)-> order('year desc,classname asc,susername asc')->select();
        if($result2){
            $this->ajaxReturn($result2,'',1);
        }
    }
    public function scoreAddPlus() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        $this -> assign('result1', $result1); 
        if($result1['issub']==1){  //提交了初录成绩才可录入补考成绩
            $dao = D('ScoreclassView');
            $map['subjectid']=$result1['id'];
            $map['tusername']=$result1['teacher'];
            $map['coursenumber']=$result1['coursenumber'];
            $map['term']=$result1['term'];
            $map['isb']=1;
            $count = $dao -> where($map) -> order('susername asc') -> count();
            if ($count > 0) {
                $my = $dao -> where($map) ->order('year desc,classname asc,susername asc') -> select();
                
                $this -> assign('my', $my);
            }
        }
        
        if($result1['issubb']=='1'){
            if(session('isenglish') == '1') {
                foreach($my as $key=>$value){
                    $class[$value['classename']][]=$value;
                }
                $this -> assign('class', $class);
                $this -> emenuScore();
                $this->display('escoreAddPlusNotChange');
            } else {
                foreach($my as $key=>$value){
                    $class[$value['classname']][]=$value;
                }
                $this -> assign('class', $class);
                $this -> menuScore();
                $this -> display('scoreAddPlusNotChange');
            }
        }else{
            if(session('isenglish') == '1') {
                foreach($my as $key=>$value){
                    $class[$value['classename']][]=$value;
                }
                $this -> assign('class', $class);
                $this -> emenuScore();
                $this->display('escoreAddPlus');
            } else {
                foreach($my as $key=>$value){
                    $class[$value['classname']][]=$value;
                }
                $this -> assign('class', $class);
                $this -> menuScore();
                $this -> display();
            }
        }
    } 
   
    
    public function scoreInsert() { 
        $ordtime=$_REQUEST['ordtime']; 
        $qimoname=$_REQUEST['qimoname'];
        $examname=$_REQUEST['examname'];
        if(empty($_REQUEST['id'])){
            $this -> error('参数缺失');
        }
        $dao1 = D('Courseteacher');
        $id=$_POST['id'];
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        if($result1){
            $dao2 = D('Score');
            $map2['subjectid']= $id;
            $result2=$dao2 -> where($map2)-> select();
            $i=0;
            $j=0;
            $issubmit=$_POST['issubmit'];
            foreach($result2 as $key=>$value){
                $i++;
                if ($dao2 -> create()) { 
                    $ordscore=$_POST['ordscore'.$value['id']];
                    $ordbuscore=$_POST['ordbuscore'.$value['id']];
                    for($k=0;$k<count($_REQUEST['ordtime']);$k++){ 
                       if(substr($examname,3,2)==substr($ordtime[$k],3,2)){
                            $ordtime[$k]=$examname;
                        }
                        $ordinary.=$ordtime[$k].':'.$ordscore[$k].':'.$ordbuscore[$k].',';
                    }
                    $ordinary=substr($ordinary,0,-1);
                    $qimoscore=$_POST['qimoscore'.$value['id']];
                    if(substr($examname,0,6)=='期末'){
                        $qimoname=$examname;        
                        $qimo=$qimoname.':'.$qimoscore;
                    }else{
                        $qimo=$qimoname.':'.$qimoscore;
                    }
                    $dao2 -> id=$value['id'];
                    $dao2 -> plus=$_POST['plus'.$value['id']];
                    $dao2 -> ordinaryscore=$ordinary;
                    $ordinary='';
                    $dao2-> qimoscore=$qimo;
                    $temp=$_POST['score'.$value['id']];
                    $dao2 -> score=$temp;
                    $dao2-> levelscore=$_POST['levelscore'.$value['id']];
                    if($temp<60){
                        $dao2 ->isb=1;//开启补考
                    }else{
                        $dao2 ->isb=0;//不可补考
                    }
                    $dao2 -> savetime=date('Y-m-d H:i:s');
                    if($issubmit=='1'){
                        $dao2 -> subtime=date('Y-m-d H:i:s');
                    }
                    $insertID = $dao2 -> save();
                    if ($insertID) {
                        $j++;
                    }else{ //添加出错提示信息
                        $uid=$dao2 -> where('id='.$value['id']) -> getField('susername');
                        $this -> error('学号为'.$uid.'的成绩暂存出错');
                    }    
                } else {
                    $this -> error($dao2->getError());
                }   
            }
            if($i==$j){
                if($issubmit=='1'){
                    $dao1 -> where($map1)->setField('issub','1');
                    $this -> success('已成功提交');
                }else{
                    $this -> success('已成功暂存');
                }
                
            }   
        }
    } 
    public function delscoreInsert() {
        $ordtime=$_REQUEST['ordtime'];       
        $qimoname=$_REQUEST['qimoname'];
        $examname=$_REQUEST['examname'];
        $substrname=substr($examname,3,2);  
        if(empty($_REQUEST['id'])){
            $this -> error('参数缺失');
        }
        $dao1 = D('Courseteacher');
        $id=$_POST['id'];
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        if($result1){
            $dao2 = D('Score');
            $map2['subjectid']= $id;
            $result2=$dao2 -> where($map2)-> select();
            $i=0;
            $j=0;
            $issubmit=$_POST['issubmit'];
            foreach($result2 as $key=>$value){
                $i++;
                if ($dao2 -> create()) { 
                    $ordscore=$_POST['ordscore'.$value['id']];
                    $ordbuscore=$_POST['ordbuscore'.$value['id']];
                    for($k=0;$k<count($_REQUEST['ordtime']);$k++){  
                        if($substrname==substr($ordtime[$k],3,2)){
                            if(count($_REQUEST['ordtime'])==1){
                               $ordinary=':::,';
                              continue;
                            }else{
                                if($k<(count($_REQUEST['ordtime'])-1)){
                                   $k++;
                                }else{ 
                                  continue;
                                } 
                            }
                        }
                        $ordinary.=$ordtime[$k].':'.$ordscore[$k].':'.$ordbuscore[$k].',';
                    }
                    $ordinary=substr($ordinary,0,-1);
                    $qimoscore=$_POST['qimoscore'.$value['id']];
                    if(substr($examname,0,6)=='期末'){
                        $qimoname=$examname;
                        $qimo=$qimoname.':'.$qimoscore;
                    }else{
                        $qimo=$qimoname.':'.$qimoscore;
                    }
                    $dao2 -> id=$value['id'];
                    $dao2 -> plus=$_POST['plus'.$value['id']];
                    $dao2 -> ordinaryscore=$ordinary;
                    $ordinary='';
                    $dao2-> qimoscore=$qimo;
                    $temp=$_POST['score'.$value['id']];
                    $dao2 -> score=$temp;
                    $dao2-> levelscore=$_POST['levelscore'.$value['id']];
                    if($temp<60){
                        $dao2 ->isb=1;//开启补考
                    }else{
                        $dao2 ->isb=0;//不可补考
                    }
                    $dao2 -> savetime=date('Y-m-d H:i:s');
                    if($issubmit=='1'){
                        $dao2 -> subtime=date('Y-m-d H:i:s');
                    }
                    $insertID = $dao2 -> save();
                    if ($insertID) {
                        $j++;
                    }else{ //添加出错提示信息
                        $uid=$dao2 -> where('id='.$value['id']) -> getField('susername');
                        $this -> error('学号为'.$uid.'的成绩暂存出错');
                    }  
                } else {
                    $this -> error($dao2->getError());
                } 
            }
            if($i==$j){
                if($issubmit=='1'){
                    $dao1 -> where($map1)->setField('issub','1');
                }
                $this -> success('已成功保存');
            }   
        }
    } 
    public function scorePlusInsert() {
        if(empty($_POST['id'])){
            $this -> error('参数缺失');
        }
        $dao1 = D('Courseteacher');
        $id=$_POST['id'];
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        if($result1){
            $dao2 = D('Score');
            $map2['subjectid']= $id;
            $map2['isb']= 1;
            $result2=$dao2 -> where($map2)-> select();
            $i=0;
            $j=0;
            $issubmit=$_POST['issubmit'];
            foreach($result2 as $key=>$value){
                $i++;
                if ($dao2 -> create()) { 
                    $dao2 -> id=$value['id'];
                    $dao2 -> bscore=$_POST['bscore'.$value['id']];
                    $dao2 -> blevelscore=$_POST['blevelscore'.$value['id']];
                    $dao2 -> bsavetime=date('Y-m-d H:i:s');
                    if($issubmit=='1'){
                        $dao2 -> bsubtime=date('Y-m-d H:i:s');
                    }
                    $insertID = $dao2 -> save();
                    if ($insertID) {
                        $j++;
                    }else{ //添加出错提示信息
                        $uid=$dao2 -> where('id='.$value['id']) -> getField('susername');
                        $this -> error('学号为'.$uid.'的成绩保存出错');
                    } 
                } else {
                    $this -> error($dao2->getError()); //注意变量名是否写错
                } 
            }
            if($i==$j){
                if($issubmit=='1'){
                    $dao1 -> where($map1)->setField('issubb','1');
                    $this -> success('已成功提交');
                }else{
                    $this -> success('已成功暂存');
                }
            }   
        }
    } 
    public function info() {
        $dao = D('Teacher');
        $map['username']= session('username');
        $count=$dao -> where($map)-> select();
        if($count==0){
            $dao -> create();
            $dao -> username=session('username');
            $dao -> truename=session('truename');
            $dao -> ctime=date('Y-m-d H:i:s');
            $insertID=$dao->add();
        }
        $my=$dao -> where($map)-> find();
        $gender=array();
        $gender['男']='男';
        $gender['女']='女';
        $this->assign('my',$my);
        $this->assign('gender',$gender);
        $this -> menuInfo();
        if(session('isenglish') == '1') {
            $this->display('einfo');
        } else {
            $this -> display();
        }
    } 
    public function infoUpdate() {
        $dao = D('teacher');
        if ($dao -> create()) {
            $dao ->ctime=date('Y-m-d H:i:s');
            $checked = $dao -> save();
            if ($checked > 0) {
                $this -> success('已成功保存');
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error('插入数据出错');
        } 
    } 
    public function getCourse($current) {
        $dao = D('CourseteacherView');
        $map['teacher']= session('username');
        if($current=='0'){
            $map['issub']= 0;
        }
        $my=$dao -> where($map)->order('term desc')-> select();
        $a=array();
        foreach($my as $key=>$value){
            $a[$value['id']]=$value['term'].':'.$value['name'];
        }
        return $a;
    } 
    public function egetCourse($current) {
        $dao = D('CourseteacherView');
        $map['teacher']= session('username');
        if($current=='0'){
            $map['issub']= 0;
        }
        $my=$dao -> where($map)->order('term desc')-> select();
        $a=array();
        foreach($my as $key=>$value){
            $a[$value['id']]=$value['term'].':'.$value['ename'];
        }
        return $a;
    } 
    public function asnAdd() {
        $courses = $this -> egetCourse('0');
        $this -> assign('category_fortag', $courses);
        $dao2=D('Homeworkcreate');
        $map2['tusername'] = session('username');
        $subjectid_latest=$dao2->where($map2)->order('id desc')->getField('subjectid');
        $this -> assign('subjectid_latest', $subjectid_latest);
        
        
        $dao = D('EduteaClassView');
        foreach($courses as $key=>$value){
            $a[]=$key;
        }
        //确保上次布置作业的课程是可选的课程之一
        
       
        if(session('isenglish') == '1') {
            if (in_array($subjectid_latest, $a)){  
            $map['subjectid']=$subjectid_latest; 
            $my = $dao ->field('ename,id') -> distinct(true) -> where($map) -> order('name asc') -> select();
            $this -> assign('my', $my);
        }
            $this -> emenuAsn();
            $this->display('easnAdd');
        } else {
            if (in_array($subjectid_latest, $a)){  
            $map['subjectid']=$subjectid_latest; 
            $my = $dao ->field('name,id') -> distinct(true) -> where($map) -> order('name asc') -> select();
            $this -> assign('my', $my);
        }
            $this -> menuAsn();
            $this -> display();
        }
    } 
    public function getClass() {
        $subjectid = $_GET['subjectid'];
        if (!isset($subjectid)) {
            $this -> error('参数缺失');
        }
        $dao = D('EduteaClassView');
        $map['subjectid'] = $subjectid;
        
        if(session('isenglish') == '1') {
            $my1 = $dao->where($map)->field('ename,id')-> distinct(true)->order('name asc')->select();
            $this->assign('my1', $my1);
            $this->display('egetClass');
        } else {
            $my1 = $dao->where($map)->field('name,id')-> distinct(true)->order('name asc')->select();
        $this->assign('my1', $my1);
            $this -> display();
        }
    }
    public function asnDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $map['tusername'] = session('username');
        $dao = D('Homeworkcreate');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $ids=explode(',',$id);
            $dao1=D('Homework');
            foreach($ids as $key=>$value){
                $map1['homeworkid']=$value;
                $count = $dao1 -> where($map1) -> delete();
            }
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function asnGetScore() {
        $id = $_GET['id'];
        $score = $_GET['score'];
        if (!isset($id)||!isset($score)) {
            $this -> error('参数缺失');
        } 
        if ($score>5||$score<0) {
            $this -> error('非法传递参数');
        } 
        $map['id'] = array('in', $id);
        $dao = D('Homework');
        $result = $dao -> where($map)->setField('score',$score);
        if ($result > 0) {
            $this -> success('已成功保存');
        } else {
            $this -> error('影响的记录行数为0');
        } 
    } 
    public function commentInsert() {
        $id = $_POST['id'];
        $correct = $_POST['correct'];//dump(stripslashes($correct));
        $comment = $_POST['comment'];
        $score = $_POST['score'];
        $content = $_POST['content'];
        if (!isset($id)||empty($comment)) {
            $this -> error('请填写评语！');
        } 
        if ($score<0){
            $this -> error('请选择评分!');
        }
        if (get_magic_quotes_gpc()){
            $correct = stripslashes($correct); //stripslashes() 函数删除由 addslashes() 函数添加的反斜杠。

        }

        $map['id'] = $id;
        $dao = D('Homework');
        $data = array('comment'=>$comment, 'score'=>$score);
        $result = $dao -> where($map)->setField($data);

        if($content !== $correct){
            $result2 = $dao->where($map)->setField('correct', $correct);
        }
        if ($result > 0 || $result2>0) {
            $this -> success('已成功保存');
        } else {
            $this -> error('没有更新内容');
        } 
    } 
    public function asnCheck() {
        $id = $_GET['id'];
        $order = $_GET['order'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Homeworkcreate');
        $map['id']= $id;
        $map['tusername']= session('username');
        $my=$dao->where($map)->find();
        if(!$my){
            $this->error('权限不足');
        }
        $this -> assign('my', $my);
        
        $dao2 = D('HomeworkstudentView');
        $map2['homeworkid']= $id;
        $count = $dao2 -> where($map2) -> count();
        if ($count > 0) {
            if($order){
                $my2 = $dao2->where($map2)->order('score '.$order.', year desc, name asc, susername asc') -> select();
               //dump($dao2->getLastSql());
            } else{
                $my2 = $dao2->where($map2)->order('year desc, name asc, susername asc') -> select();
            }
            
            $this->assign('count', $count);
            
            $this -> assign('id', $id);
        } 
        
        if(session('isenglish') == '1') {
            foreach($my2 as $key=>$value){
                $class[$value['ename']][]=$value;
            }
            $this->assign('class', $class);
            $this -> emenuAsn();
            $this->display('easnCheck');
        } else {
            foreach($my2 as $key=>$value){
                $class[$value['name']][]=$value;
            }
            $this->assign('class', $class);
            $this -> menuAsn();
            $this -> display();
        }
    } 
    public function asn() {
        $this -> assign('category_fortag', $this ->  egetCourse());
        if (isset($_GET['searchkey'])) {
            $map['title|content'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['subjectid'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        } 
        
        
        if(session('isenglish') == '1') {
            $dao = D('HomeworkcourseView');
            $map['tusername']= session('username');
            $count = $dao -> where($map) -> count();
            if ($count > 0) {
                import("@.ORG.Page");
                $listRows = 20;
                $p = new Page($count, $listRows);
                $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
                $p->setConfig('header','records');
                $p->setConfig('theme', '%totalRow% %header%  %nowPage%/%totalPage% Pages');
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
            } 
            $this -> emenuAsn();
            $this->display('easn');
        } else {
            $dao = D('Homeworkcreate');
            $map['tusername']= session('username');
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
            $this -> menuAsn();
            $this -> display();
        }
    } 
    public function asnInsert() {
        $dao = D('Homeworkcreate');
        $subjectid=$_POST['subjectid'];
        $classids = $_POST['classid'];
        if ($dao -> create()) {
            $dao2=D('CourseteacherView');
            $map2['id']=$subjectid;
            $result2=$dao2->where($map2)->getField('name');
            $dao ->coursename=$result2;
            $dao ->ctime=date('Y-m-d H:i:s');
            $dao ->tusername=session('username');
            $dao ->ttruename=session('truename');
            $insertID = $dao -> add();//将作业内容保存在表Homeworkcreate
            if ($insertID) {
                $dao3=D('EduteaClassView');
                $map3['subjectid']=$subjectid;
                $map3['classid'] = array('in', $classids);              
                $result3=$dao3->field('susername,struename')->where($map3)->select();
                $dao4=D('Homework');
                foreach($result3 as $key=>$value){
                    $dao4 -> create();
                    $dao4->homeworkid=$insertID;
                    $dao4->susername=$value['susername'];
                    $dao4->struename=$value['struename'];
                    $dao4->content=NULL;
                    $insertID4 = $dao4 -> add(); //将每个学生与作业关联，存在表Homework
                }
                $this -> ajaxReturn($insertID, '已成功保存！', 1);
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error($dao->getError());
        } 
    } 
    public function asnEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Homeworkcreate');
        $map['id'] = $id;
        $map['tusername'] = session('username');
        $my = $dao -> where($map) -> find();
        
        if ($my) {
            $this -> assign('my', $my);
            $this -> assign('category_fortag', $this -> egetCourse());
            
            if(session('isenglish') == '1') {
                $dao1 = D('EduteaClassView');
                $map_b['subjectid'] = $my['subjectid'];
                $my1 = $dao1->where($map_b)->field('ename,id')-> distinct(true)->order('name asc')->select();
                $this->assign('my1', $my1);
                
                $dao2 = D('HomeworkstudentView');
                $map_a['homeworkid'] = $id;
                $class = $dao2 ->field('ename,classid') -> distinct(true) -> where($map_a) -> order('name asc') -> select();
                $this -> assign('class', $class);
                
                $this -> emenuAsn();
                $this->display('easnEdit');
            } else {
                $dao1 = D('EduteaClassView');
                $map_b['subjectid'] = $my['subjectid'];
                $my1 = $dao1->where($map_b)->field('name,id')-> distinct(true)->order('name asc')->select();
                $this->assign('my1', $my1);
                
                $dao2 = D('HomeworkstudentView');
                $map_a['homeworkid'] = $id;
                $class = $dao2 ->field('name,classid') -> distinct(true) -> where($map_a) -> order('name asc') -> select();
                $this -> assign('class', $class);

                $this -> menuAsn();
                $this -> display();
            }
            
            
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function asnScoreView() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Homework');
        $map['id'] = $id;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $dao1 = D('Homeworkcreate');
            $map1['id'] = $my['homeworkid'];
            $map1['tusername'] = session('username');
            $my1 = $dao1 -> where($map1) -> find();
            if(!$my){
                $this -> error('权限不足');
            }
            $this -> assign('my', $my);
            if(session('isenglish') == '1') {
                $this->display('easnScoreView');
            } else {
                $this -> display();
            }
        } else {
            $this -> error('该记录不存在');
        } 
    }
    public function asnUpdate() {
        $oldclassids = $_POST['oldclassid'];
        $classids = $_POST['classid'];
        if(!isset($classids)){
            $dao = D('Homeworkcreate');
            if ($dao -> create()) {
                $dao2=D('CourseteacherView');
                $map2['id']=$_POST['subjectid'];
                $result2=$dao2->where($map2)->getField('name');
                $dao ->coursename=$result2;
                $dao ->ctime=date('Y-m-d H:i:s');
                $checked = $dao -> save();
                if ($checked > 0) {
                    $this -> success('已成功保存');
                } else {
                    $this -> error('没有更新任何数据');
                } 
            } else {
                $this -> error('更新数据出错');
            } 
        }else {
            foreach($classids as $k=>$v){
                if(in_array($v, $oldclassids)){
                    $this->error('班级重复选择，请重新选择班级，不要勾选已布置过的班级');
                }
            }
            $dao = D('Homeworkcreate');
            if ($dao -> create()) {
                $dao2=D('CourseteacherView');
                $map2['id']=$_POST['subjectid'];
                $result2=$dao2->where($map2)->getField('name');
                $dao ->coursename=$result2;
                $dao ->ctime=date('Y-m-d H:i:s');
                $checked = $dao -> save();
            }
            $dao3=D('EduteaClassView');           
            $map3['subjectid']=$_POST['subjectid'];
            $map3['classid'] = array('in', $classids); 
            $result3=$dao3->field('susername,struename')->where($map3)->select(); 
            $dao4=D('Homework');
            foreach($result3 as $key=>$value){
                $dao4 -> create();
                $dao4->id=NULL;
                $dao4->homeworkid=$_POST['id'];
                $dao4->susername=$value['susername'];
                $dao4->struename=$value['struename'];
                $dao4->content=NULL;
                $insertID4 = $dao4 -> add(); //将每个学生与作业关联，存在表Homework
                //$this->success($dao4->getlastSQL());
            }
            $this -> ajaxReturn($insertID4, '已成功保存！', 1);
        }
    } 
    public function downCourseStu(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        if(session('isenglish') == '1') {
            $dao = D('ScoreclassView');
            $map['subjectid']=$id;
            $myclass = $dao -> where($map) ->field('classename')-> group('classename')->order('year desc,classname asc') -> select();
            //dump($myclass);
            include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.php';
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setTitle("NJU-HND auto generate Document");
            $a=array();
            $a['A']='Class';
            $a['B']='Student Number';
            $a['C']='Student Name';
            $a['D']='Course Code';
            $a['E']='Courses';
            $a['F']='Course English Name';
            $a['G']='Teacher';
            $a['H']='Class Hour';
            $a['I']='Credit';
            $a['J']='Semester';

            foreach($myclass as $myclass_key=>$myclass_value){
                if($myclass_key>0){
                    $objPHPExcel->createSheet();
                }
                $actSheet=$objPHPExcel->getSheet($myclass_key);
                $actSheet->setTitle($myclass_value['classename']);
                $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
                $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                $actSheet->getPageSetup()->setHorizontalCentered(true);
                foreach($a as $key=>$value){
                    $actSheet->setCellValue($key.'1', $value);
                }
                $map['classename']=$myclass_value['classename'];
                $my = $dao -> where($map) -> order('year desc,classname asc,susername asc') -> select();
                
                foreach($my as $my_key=>$my_value){
                    $temp=$my_key+2;
                    $actSheet->setCellValue('A'.$temp, $my_value['classename'])
                        ->setCellValue('B'.$temp, $my_value['susername'])
                        ->setCellValue('C'.$temp, $my_value['sename'])
                        ->setCellValue('D'.$temp, $my_value['coursenumber'])
                        ->setCellValue('E'.$temp, $my_value['coursename'])
                        ->setCellValue('F'.$temp, $my_value['courseename'])
                        ->setCellValue('G'.$temp, $result1['tename'])
                        ->setCellValue('H'.$temp, $my_value['coursetime'])
                        ->setCellValue('I'.$temp, $my_value['credit'])
                        ->setCellValue('J'.$temp, $my_value['term']);
                }
                $actSheet -> getColumnDimension('A') -> setwidth(20);
                $actSheet -> getColumnDimension('B') -> setwidth(15);
                $actSheet -> getColumnDimension('C') -> setwidth(15);
                $actSheet -> getColumnDimension('D') -> setwidth(20);
                $actSheet -> getColumnDimension('E') -> setwidth(30);
                $actSheet -> getColumnDimension('F') -> setAutoSize(true);
                $actSheet -> getColumnDimension('G') -> setwidth(20);
                $actSheet -> getColumnDimension('H') -> setwidth(10);
                $actSheet -> getColumnDimension('I') -> setwidth(10);
                $actSheet -> getColumnDimension('J') -> setwidth(25);
            }

            $filename='studentList['.$result1['term'].'--'.$result1['name'].'].xls';
            $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        } else {
             $dao = D('ScoreclassView');
            $map['subjectid']=$id;
    //         $map['tusername']=$result1['teacher'];
    //         $map['coursenumber']=$result1['coursenumber'];
    //         $map['term']=$result1['term'];
            $myclass = $dao -> where($map) ->field('classname')-> group('classname')->order('year desc,classname asc') -> select();
            
            include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.php';
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setTitle("NJU-HND auto generate Document");
            $a=array();
            $a['A']='班级';
            $a['B']='学号';
            $a['C']='姓名';
            $a['D']='课程编号';
            $a['E']='课程名称';
            $a['F']='课程英文名称';
            $a['G']='任课教师';
            $a['H']='学时';
            $a['I']='学分';
            $a['J']='学期';

            foreach($myclass as $myclass_key=>$myclass_value){
                if($myclass_key>0){
                    $objPHPExcel->createSheet();
                }
                $actSheet=$objPHPExcel->getSheet($myclass_key);
                $actSheet->setTitle($myclass_value['classname']);
                $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
                $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                $actSheet->getPageSetup()->setHorizontalCentered(true);
                foreach($a as $key=>$value){
                    $actSheet->setCellValue($key.'1', $value);
                }
                $map['classname']=$myclass_value['classname'];
                $my = $dao -> where($map) -> order('year desc,classname asc,susername asc') -> select();
                
                foreach($my as $my_key=>$my_value){
                    $temp=$my_key+2;
                    $actSheet->setCellValue('A'.$temp, $my_value['classname'])
                        ->setCellValue('B'.$temp, $my_value['susername'])
                        ->setCellValue('C'.$temp, $my_value['struename'])
                        ->setCellValue('D'.$temp, $my_value['coursenumber'])
                        ->setCellValue('E'.$temp, $my_value['coursename'])
                        ->setCellValue('F'.$temp, $my_value['courseename'])
                        ->setCellValue('G'.$temp, $my_value['ttruename'])
                        ->setCellValue('H'.$temp, $my_value['coursetime'])
                        ->setCellValue('I'.$temp, $my_value['credit'])
                        ->setCellValue('J'.$temp, $my_value['term']);
                }
                $actSheet -> getColumnDimension('A') -> setwidth(20);
                $actSheet -> getColumnDimension('B') -> setwidth(15);
                $actSheet -> getColumnDimension('C') -> setwidth(15);
                $actSheet -> getColumnDimension('D') -> setwidth(20);
                $actSheet -> getColumnDimension('E') -> setwidth(30);
                $actSheet -> getColumnDimension('F') -> setAutoSize(true);
                $actSheet -> getColumnDimension('G') -> setwidth(20);
                $actSheet -> getColumnDimension('H') -> setwidth(10);
                $actSheet -> getColumnDimension('I') -> setwidth(10);
                $actSheet -> getColumnDimension('J') -> setwidth(25);
            }

            $filename='学生名单['.$result1['term'].'--'.$result1['name'].'].xls';
            $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        
    }
    public function downScore(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
                
        include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("NJU-HND auto generated Document");
        $styleA1= array(
            'font' => array(
                'bold' => true,
                'size' => 16,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $styleA5= array(
            'font' => array(
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
        $styleA6= array(
            'font' => array(
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
        $styleA7= array(
            'font' => array(
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
        
        if(session('isenglish') == '1') {
            $dao = D('ScoreclassView');
            $map['subjectid']=$result1['id'];
            $myclass = $dao -> where($map) ->field('classename')-> group('classename')->order('year desc,classname asc') -> select();
            foreach($myclass as $myclass_key=>$myclass_value) {
                if($myclass_key>0){
                    $objPHPExcel->createSheet();
                }
                $actSheet=$objPHPExcel->getSheet($myclass_key);
                $actSheet->setTitle($myclass_value['classename']);
                $map['classename']=$myclass_value['classename'];
                $my0 = $dao ->where($map) -> find();
                $a0=explode(',',$my0['ordinaryscore']);
                $examlen=count($a0);
                $len=chr(ord('E')+$examlen*2+2); 
                $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
                $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                $actSheet->getPageSetup()->setHorizontalCentered(true);
                $actSheet->getPageMargins()->setFooter(1.0);
                $actSheet->getHeaderFooter()->setOddFooter('&LTeacher:_______________(Sign, Stamp)' . '&CPage &P of &N'.'&RDate:________Year____Month____Day');
                $actSheet-> getColumnDimension('A') -> setwidth(6);
                $actSheet-> getColumnDimension('B') -> setwidth(12);
                $actSheet-> getColumnDimension('C') -> setwidth(8);
                $actSheet-> getColumnDimension('D') -> setwidth(10);
                $actSheet-> getColumnDimension('E') -> setwidth(10);
                $actSheet-> getColumnDimension('F') -> setwidth(12);
                $actSheet-> getColumnDimension($len) -> setwidth(8);
                $actSheet->getRowDimension('1')->setRowHeight(30);
                $actSheet->mergeCells('A1:'.$len.'1');
                $actSheet->mergeCells($len.'5:'.$len.'6');
                $actSheet->mergeCells('A2:C2');
                $actSheet->mergeCells('A3:C3');
                $actSheet->mergeCells('D3:E3');
                $actSheet->mergeCells('A5:A6');
                $actSheet->mergeCells('B5:B6');
                $actSheet->mergeCells('C5:C6');
                $actSheet->mergeCells('D5:E5');
                $actSheet->mergeCells('F5:F6');
                $actSheet->setCellValue('A1',"Nanjing University".$result1['term']."Scoring Sheet");
                $actSheet->setCellValue('A2',"Course Name:".$result1['ename']);
                $actSheet->setCellValue('A3',"Class Name:".$myclass_value['classename']);
                $actSheet->setCellValue('D2',"Class Hour:".$result1['coursetime']);
                $actSheet->setCellValue('E2',"Credits:".$result1['credit']);
                $actSheet->setCellValue('D3',"Course Code:".$result1['coursenumber']);
                $actSheet->getStyle('A1')->applyFromArray($styleA1);
                $map['classename']=$myclass_value['classename'];
                $my8 = $dao ->where($map) -> find();
                $a8=explode(':',$my8['qimoscore']);
                $actSheet->setCellValue('A5', 'Serial Number')
                        ->setCellValue('B5', 'Student Number')
                        ->setCellValue('C5', 'Student Name')
                        ->setCellValue('A4', 'Notes would be allowed to fill out with one of transaction, absent, exemption, cheat, delay.')
                        ->setCellValue('D5', 'Final Results')
                        ->setCellValue('D6', 'according centesimal system')
                        ->setCellValue('E6', 'according hierarchical system')
                        ->setCellValue('F5', $a8[0]=($a8[0]!='')?$a8[0]:'Final Exam Results')
                        ->setCellValue($len.'5', 'Notes');  
                $map['classename']=$myclass_value['classename'];
                $my = $dao -> field('susername,sename,score,qimoscore,ordinaryscore,plus,levelscore')->where($map) -> order('susername asc') -> select();
                foreach($my as $my_key=>$my_value){
                    $temp=$my_key+7;//从第7行开始写 
                    $a=explode(':',$my_value['qimoscore']);
                    $actSheet->setCellValue('A'.$temp, $my_key+1)
                        ->setCellValue('B'.$temp, $my_value['susername'])
                        ->setCellValue('C'.$temp, $my_value['sename'])
                        ->setCellValue('D'.$temp, $my_value['score'])
                        ->setCellValue('E'.$temp,$my_value['levelscore'])
                        ->setCellValue('F'.$temp,$a[1])
                        ->setCellValue($len.$temp, $my_value['plus']);
                    $b[$my_value['susername']]=explode(',',$my_value['ordinaryscore']);
                                                                  
                    foreach($b as $k=>$v) {
                        foreach($v as $key=>$value){
                            $c[$k][$key]=explode(':',$value);
                        }  
                    }
                    for($i=1;$i<=$examlen;$i++) {
                        $j=chr(ord('E')+$i*2);
                        $m=chr(ord('E')+$i*2+1);
                        $actSheet->mergeCells($j.'5:'.$m.'5');
                        $actSheet-> getColumnDimension($j) -> setwidth(6);
                        $actSheet-> getColumnDimension($m) -> setwidth(6);
                        $actSheet->setCellValue($j.$temp,$c[$k][$i-1][1])
                                ->setCellValue($j.'5', $c[$k][$i-1][0]=($c[$k][$i-1][0]!='')?substr($c[$k][$i-1][0],7):'Examination')
                                ->setCellValue($j.'6', 'First-time Exam')
                                ->setCellValue($m.'6', 'Make-up Exam')
                                ->setCellValue($m.$temp, $c[$k][$i-1][2]); 
                    }
                    $actSheet->getRowDimension($temp)->setRowHeight(14);
                }
                $end_row=count($my)+6;
                $actSheet->getStyle('A5:'.$len.'5')->applyFromArray($styleA5);
                $actSheet->getRowDimension('5')->setRowHeight(14);
                $actSheet->getStyle('A6:'.$len.$end_row)->applyFromArray($styleA6);   
            }
            $filename='scoringSheet['.$result1['term'].'--'.$result1['name'].'].xls';
            $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit; 
        } else {
            $dao = D('ScoreclassView');
            $map['subjectid']=$result1['id'];
            $myclass = $dao -> where($map) ->field('classname')-> group('classname')->order('year desc,classname asc') -> select();
            foreach($myclass as $myclass_key=>$myclass_value){
                if($myclass_key>0){
                    $objPHPExcel->createSheet();
                }
                $actSheet=$objPHPExcel->getSheet($myclass_key);
                $actSheet->setTitle($myclass_value['classname']);
                $map['classname']=$myclass_value['classname'];
                $my0 = $dao ->where($map) -> find();
                $a0=explode(',',$my0['ordinaryscore']);
                $examlen=count($a0);
                $len=chr(ord('E')+$examlen*2+2); 
                $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
                $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                $actSheet->getPageSetup()->setHorizontalCentered(true);
                $actSheet->getPageMargins()->setFooter(1.0);
                $actSheet->getHeaderFooter()->setOddFooter('&L教师：_______________(签，章)' . '&CPage &P of &N'.'&R日期：________年____月____日');
                $actSheet-> getColumnDimension('A') -> setwidth(6);
                $actSheet-> getColumnDimension('B') -> setwidth(12);
                $actSheet-> getColumnDimension('C') -> setwidth(8);
                $actSheet-> getColumnDimension('D') -> setwidth(10);
                $actSheet-> getColumnDimension('E') -> setwidth(10);
                $actSheet-> getColumnDimension('F') -> setwidth(12);
                $actSheet-> getColumnDimension($len) -> setwidth(8);
                $actSheet->getRowDimension('1')->setRowHeight(30);
                $actSheet->mergeCells('A1:'.$len.'1');
                $actSheet->mergeCells($len.'5:'.$len.'6');
                $actSheet->mergeCells('A2:C2');
                $actSheet->mergeCells('A3:C3');
                $actSheet->mergeCells('D3:E3');
                $actSheet->mergeCells('A5:A6');
                $actSheet->mergeCells('B5:B6');
                $actSheet->mergeCells('C5:C6');
                $actSheet->mergeCells('D5:E5');
                $actSheet->mergeCells('F5:F6');
                $actSheet->setCellValue('A1',"南京大学".$result1['term']."成绩登记表");
                $actSheet->setCellValue('A2',"课程名称:".$result1['name']);
                $actSheet->setCellValue('A3',"班级名称:".$myclass_value['classname']);
                $actSheet->setCellValue('D2',"学时:".$result1['coursetime']);
                $actSheet->setCellValue('E2',"学分:".$result1['credit']);
                $actSheet->setCellValue('D3',"课程代码:".$result1['coursenumber']);
                $actSheet->getStyle('A1')->applyFromArray($styleA1);
                $map['classname']=$myclass_value['classname'];
                $my8 = $dao ->where($map) -> find();
                $a8=explode(':',$my8['qimoscore']);
                $actSheet->setCellValue('A5', '序号')
                        ->setCellValue('B5', '学号')
                        ->setCellValue('C5', '姓名')
                        ->setCellValue('A4', '备注填写：异动,旷考,免修,作弊,缓考,任选一个')
                        ->setCellValue('D5', '最终成绩')
                        ->setCellValue('D6', '百分制成绩')
                        ->setCellValue('E6', '等级成绩')
                        ->setCellValue('F5', $a8[0]=($a8[0]!='')?$a8[0]:'期末成绩')
                        ->setCellValue($len.'5', '备注');  
               $map['classname']=$myclass_value['classname'];
                $my = $dao -> field('susername,struename,score,qimoscore,ordinaryscore,plus,levelscore')->where($map) -> order('susername asc') -> select();
                foreach($my as $my_key=>$my_value){
                    $temp=$my_key+7;//从第7行开始写 
                    $a=explode(':',$my_value['qimoscore']);
                    $actSheet->setCellValue('A'.$temp, $my_key+1)
                        ->setCellValue('B'.$temp, $my_value['susername'])
                        ->setCellValue('C'.$temp, $my_value['struename'])
                        ->setCellValue('D'.$temp, $my_value['score'])
                        ->setCellValue('E'.$temp,$my_value['levelscore'])
                        ->setCellValue('F'.$temp,$a[1])
                        ->setCellValue($len.$temp, $my_value['plus']);
                    $b[$my_value['susername']]=explode(',',$my_value['ordinaryscore']);
                                                                  
                    foreach($b as $k=>$v) {
                        foreach($v as $key=>$value) {
                            $c[$k][$key]=explode(':',$value);
                        }  
                    }
                    for($i=1;$i<=$examlen;$i++) {
                        $j=chr(ord('E')+$i*2);
                        $m=chr(ord('E')+$i*2+1);
                        $actSheet->mergeCells($j.'5:'.$m.'5');
                        $actSheet-> getColumnDimension($j) -> setwidth(6);
                        $actSheet-> getColumnDimension($m) -> setwidth(6);
                        $actSheet->setCellValue($j.$temp,$c[$k][$i-1][1])
                                ->setCellValue($j.'5', $c[$k][$i-1][0]=($c[$k][$i-1][0]!='')?substr($c[$k][$i-1][0],7):'考试')
                                ->setCellValue($j.'6', '初考')
                                ->setCellValue($m.'6', '补考')
                                ->setCellValue($m.$temp, $c[$k][$i-1][2]); 
                    }
                    $actSheet->getRowDimension($temp)->setRowHeight(14);
                }
                $end_row=count($my)+6;
                $actSheet->getStyle('A5:'.$len.'5')->applyFromArray($styleA5);
                $actSheet->getRowDimension('5')->setRowHeight(14);
                $actSheet->getStyle('A6:'.$len.$end_row)->applyFromArray($styleA6);   
            }
            $filename='成绩登记表['.$result1['term'].'--'.$result1['name'].'].xls';
            $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit; 
        }
    }
    public function downScore2(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        
        include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("NJU-HND auto generated Document");
        $styleA1= array(
            'font' => array(
                'bold' => true,
                'size' => 16,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $styleA5= array(
            'font' => array(
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
        $styleA6= array(
            'font' => array(
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
        $styleA7= array(
            'font' => array(
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
        if(session('isenglish') == '1') {
            $dao = D('ScoreclassView');
            $map['subjectid']=$result1['id'];
            $myclass = $dao -> where($map) ->field('classename')-> group('classename')->order('year desc,classname asc') -> select();
            
            foreach($myclass as $myclass_key=>$myclass_value){
                if($myclass_key>0){
                    $objPHPExcel->createSheet();
                }
                $actSheet=$objPHPExcel->getSheet($myclass_key);
                $actSheet->setTitle($myclass_value['classename']);
                $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
                $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                $actSheet->getPageSetup()->setHorizontalCentered(true);
                $actSheet->getPageMargins()->setFooter(1.0);
                $actSheet->getHeaderFooter()->setOddFooter('&LTeacher:_______________(Sign, Stamp)' . '&CPage &P of &N'.'&RDate:________Year____Month____Day');
                $actSheet-> getColumnDimension('A') -> setwidth(6);
                $actSheet-> getColumnDimension('B') -> setwidth(12);
                $actSheet-> getColumnDimension('C') -> setwidth(8);
                $actSheet-> getColumnDimension('D') -> setwidth(12);
                $actSheet-> getColumnDimension('E') -> setwidth(12);
                $actSheet-> getColumnDimension('F') -> setwidth(8);
                $actSheet->getRowDimension('1')->setRowHeight(30);
                $actSheet->mergeCells('A1:F1');
                $actSheet->mergeCells('A2:C2');
                $actSheet->mergeCells('A3:C3');
                $actSheet->mergeCells('D3:E3');
                $actSheet->mergeCells('A5:A6');
                $actSheet->mergeCells('B5:B6');
                $actSheet->mergeCells('C5:C6');
                $actSheet->mergeCells('D5:E5');
                $actSheet->mergeCells('F5:F6');
                $actSheet->setCellValue('A1',"Nanjing University".$result1['term']."Scoring Sheet");
                $actSheet->setCellValue('A2',"Course Name:".$result1['ename']);
                $actSheet->setCellValue('A3',"Class Name:".$myclass_value['classename']);
                $actSheet->setCellValue('D2',"Class Hour:".$result1['coursetime']);
                $actSheet->setCellValue('E2',"Credits:".$result1['credit']);
                $actSheet->setCellValue('D3',"Course Code:".$result1['coursenumber']);
                $actSheet->getStyle('A1')->applyFromArray($styleA1);
                $actSheet->setCellValue('A5', 'Serial Number')
                    ->setCellValue('B5', 'Student Number')
                    ->setCellValue('C5', 'Student Name')
                    ->setCellValue('A4', 'Notes would be allowed to fill out with one of transaction, absent, exemption, cheat, delay. ')
                    ->setCellValue('D5', 'Final Results')
                    ->setCellValue('D6', 'according centesimal system')
                    ->setCellValue('E6', 'according hierarchical system')
                    ->setCellValue('F5', 'Notes');  
                $map['classename']=$myclass_value['classename'];
                $my = $dao -> field('susername,sename,score,plus,levelscore')->where($map) -> order('susername asc') -> select();
                foreach($my as $my_key=>$my_value){
                    $temp=$my_key+7;//从第7行开始写 
                    $actSheet->setCellValue('A'.$temp, $my_key+1)
                        ->setCellValue('B'.$temp, $my_value['susername'])
                        ->setCellValue('C'.$temp, $my_value['sename'])
                        ->setCellValue('D'.$temp, $my_value['score'])
                        ->setCellValue('E'.$temp,$my_value['levelscore'])
                        ->setCellValue('F'.$temp, $my_value['plus']);
                    $actSheet->getRowDimension($temp)->setRowHeight(14);
                }
                $end_row=count($my)+6;
                $actSheet->getStyle('A5:F5')->applyFromArray($styleA5);
                $actSheet->getRowDimension('5')->setRowHeight(14);
                $actSheet->getStyle('A6:F'.$end_row)->applyFromArray($styleA6);   
            }
            $filename='scoringSheet['.$result1['term'].'--'.$result1['name'].'].xls';
            $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        } else {
            $dao = D('ScoreclassView');
            $map['subjectid']=$result1['id'];
            $myclass = $dao -> where($map) ->field('classname')-> group('classname')->order('year desc,classname asc') -> select();
            foreach($myclass as $myclass_key=>$myclass_value){
                if($myclass_key>0){
                    $objPHPExcel->createSheet();
                }
                $actSheet=$objPHPExcel->getSheet($myclass_key);
                $actSheet->setTitle($myclass_value['classname']);
                $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
                $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                $actSheet->getPageSetup()->setHorizontalCentered(true);
                $actSheet->getPageMargins()->setFooter(1.0);
                $actSheet->getHeaderFooter()->setOddFooter('&L教师：_______________(签，章)' . '&CPage &P of &N'.'&R日期：________年____月____日');
                $actSheet-> getColumnDimension('A') -> setwidth(6);
                $actSheet-> getColumnDimension('B') -> setwidth(12);
                $actSheet-> getColumnDimension('C') -> setwidth(8);
                $actSheet-> getColumnDimension('D') -> setwidth(12);
                $actSheet-> getColumnDimension('E') -> setwidth(12);
                $actSheet-> getColumnDimension('F') -> setwidth(8);
                $actSheet->getRowDimension('1')->setRowHeight(30);
                $actSheet->mergeCells('A1:F1');
                $actSheet->mergeCells('A2:C2');
                $actSheet->mergeCells('A3:C3');
                $actSheet->mergeCells('D3:E3');
                $actSheet->mergeCells('A5:A6');
                $actSheet->mergeCells('B5:B6');
                $actSheet->mergeCells('C5:C6');
                $actSheet->mergeCells('D5:E5');
                $actSheet->mergeCells('F5:F6');
                $actSheet->setCellValue('A1',"南京大学".$result1['term']."成绩登记表");
                $actSheet->setCellValue('A2',"课程名称:".$result1['name']);
                $actSheet->setCellValue('A3',"班级名称:".$myclass_value['classname']);
                $actSheet->setCellValue('D2',"学时:".$result1['coursetime']);
                $actSheet->setCellValue('E2',"学分:".$result1['credit']);
                $actSheet->setCellValue('D3',"课程代码:".$result1['coursenumber']);
                $actSheet->getStyle('A1')->applyFromArray($styleA1);
                $actSheet->setCellValue('A5', '序号')
                    ->setCellValue('B5', '学号')
                    ->setCellValue('C5', '姓名')
                    ->setCellValue('A4', '备注填写：异动,旷考,免修,作弊,缓考,任选一个')
                    ->setCellValue('D5', '最终成绩')
                    ->setCellValue('D6', '百分制成绩')
                    ->setCellValue('E6', '等级成绩')
                    ->setCellValue('F5', '备注');  
                $map['classname']=$myclass_value['classname'];
                $my = $dao -> field('susername,struename,score,plus,levelscore')->where($map) -> order('susername asc') -> select();
                foreach($my as $my_key=>$my_value){
                    $temp=$my_key+7;//从第7行开始写 
                    $actSheet->setCellValue('A'.$temp, $my_key+1)
                        ->setCellValue('B'.$temp, $my_value['susername'])
                        ->setCellValue('C'.$temp, $my_value['struename'])
                        ->setCellValue('D'.$temp, $my_value['score'])
                        ->setCellValue('E'.$temp,$my_value['levelscore'])
                        ->setCellValue('F'.$temp, $my_value['plus']);
                    $actSheet->getRowDimension($temp)->setRowHeight(14);
                }
                $end_row=count($my)+6;
                $actSheet->getStyle('A5:F5')->applyFromArray($styleA5);
                $actSheet->getRowDimension('5')->setRowHeight(14);
                $actSheet->getStyle('A6:F'.$end_row)->applyFromArray($styleA6);   
            }
            $filename='成绩登记表['.$result1['term'].'--'.$result1['name'].'].xls';
            $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        
    }
    public function downScorenull() {
        $examlen=$_GET['num'];
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        
        include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("NJU-HND auto generated Document");
        $styleA1= array(
            'font' => array(
                'bold' => true,
                'size' => 16,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $styleA5= array(
            'font' => array(
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
        $styleA6= array(
            'font' => array(
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
        $styleA7= array(
            'font' => array(
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
        if(session('isenglish') == '1') {
            $dao = D('ScoreclassView');
            $map['subjectid']=$result1['id'];
            $myclass = $dao -> where($map) ->field('classename')-> group('classename')->order('year desc,classname asc') -> select();
            foreach($myclass as $myclass_key=>$myclass_value){
                if($myclass_key>0){
                    $objPHPExcel->createSheet();
                }
                $actSheet=$objPHPExcel->getSheet($myclass_key);
                $actSheet->setTitle($myclass_value['classename']);
                $len=chr(ord('E')+$examlen*2+2); 
                $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
                $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                $actSheet->getPageSetup()->setHorizontalCentered(true);
                $actSheet->getPageMargins()->setFooter(1.0);
                $actSheet->getHeaderFooter()->setOddFooter('&LTeacher:_______________(Sign, Stamp)' . '&CPage &P of &N'.'&RDate:________Year____Month____Day');
                $actSheet-> getColumnDimension('A') -> setwidth(6);
                $actSheet-> getColumnDimension('B') -> setwidth(12);
                $actSheet-> getColumnDimension('C') -> setwidth(8);
                $actSheet-> getColumnDimension('D') -> setwidth(10);
                $actSheet-> getColumnDimension('E') -> setwidth(10);
                $actSheet-> getColumnDimension('F') -> setwidth(12);
                $actSheet-> getColumnDimension($len) -> setwidth(8);
                $actSheet->getRowDimension('1')->setRowHeight(30);
                $actSheet->mergeCells('A1:'.$len.'1');
                $actSheet->mergeCells($len.'5:'.$len.'6');
                $actSheet->mergeCells('A2:C2');
                $actSheet->mergeCells('A3:C3');
                $actSheet->mergeCells('D3:E3');
                $actSheet->mergeCells('A5:A6');
                $actSheet->mergeCells('B5:B6');
                $actSheet->mergeCells('C5:C6');
                $actSheet->mergeCells('D5:E5');
                $actSheet->mergeCells('F5:F6');
                $actSheet->setCellValue('A1',"Nanjing University".$result1['term']."Scoring Sheet");
                $actSheet->setCellValue('A2',"Course Name:".$result1['ename']);
                $actSheet->setCellValue('A3',"Class Name:".$myclass_value['classename']);
                $actSheet->setCellValue('D2',"Class Hour:".$result1['coursetime']);
                $actSheet->setCellValue('E2',"Credits:".$result1['credit']);
                $actSheet->setCellValue('D3',"Course Code:".$result1['coursenumber']);
                $actSheet->getStyle('A1')->applyFromArray($styleA1);

                $actSheet->setCellValue('A5', 'Serial Number')
                    ->setCellValue('B5', 'Student Number')
                    ->setCellValue('C5', 'Student Name')
                    ->setCellValue('A4', 'Notes would be allowed to fill out with one of transaction, absent, exemption, cheat, delay.')
                    ->setCellValue('D5', 'Final Results')
                    ->setCellValue('D6', 'according centesimal system')
                    ->setCellValue('E6', 'according hierarchical system')
                    ->setCellValue('F5', 'Final Exam Results')
                    ->setCellValue($len.'5', 'Notes');  
                $map['classename']=$myclass_value['classename'];
                $my = $dao -> field('susername,sename,score,qimoscore,ordinaryscore,plus')->where($map) -> order('susername asc') -> select();
                foreach($my as $my_key=>$my_value) {
                    $temp=$my_key+7;//从第7行开始写 
                    $actSheet->setCellValue('A'.$temp, $my_key+1)
                        ->setCellValue('B'.$temp, $my_value['susername'])
                        ->setCellValue('C'.$temp, $my_value['sename'])
                        ->setCellValue('D'.$temp, '')
                        ->setCellValue('E'.$temp, '')
                        ->setCellValue($len.$temp, '');
                    for($i=1;$i<=$examlen;$i++) {
                        $j=chr(ord('E')+$i*2);
                        $m=chr(ord('E')+$i*2+1);
                        $actSheet->mergeCells($j.'5:'.$m.'5');
                        $actSheet-> getColumnDimension($j) -> setwidth(6);
                        $actSheet-> getColumnDimension($m) -> setwidth(6);
                        $actSheet->setCellValue($j.$temp,'')
                                ->setCellValue($j.'5', 'Examination')
                                ->setCellValue($j.'6', 'First-time Exam')
                                ->setCellValue($m.'6', 'Make-up Exam')
                                ->setCellValue($m.$temp, ''); 
                    }
                    $actSheet->getRowDimension($temp)->setRowHeight(14);
                }
                $end_row=count($my)+6;
                $actSheet->getStyle('A5:'.$len.'5')->applyFromArray($styleA5);
                $actSheet->getRowDimension('5')->setRowHeight(14);
                $actSheet->getStyle('A6:'.$len.$end_row)->applyFromArray($styleA6);   
            }
            $filename='scoringSheet['.$result1['term'].'--'.$result1['name'].'].xls';
            $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit; 
        } else {
            $dao = D('ScoreclassView');
            $map['subjectid']=$result1['id'];
            $myclass = $dao -> where($map) ->field('classname')-> group('classname')->order('year desc,classname asc') -> select();
            foreach($myclass as $myclass_key=>$myclass_value){
                if($myclass_key>0){
                    $objPHPExcel->createSheet();
                }
                $actSheet=$objPHPExcel->getSheet($myclass_key);
                $actSheet->setTitle($myclass_value['classname']);
                $len=chr(ord('E')+$examlen*2+2); 
                $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
                $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                $actSheet->getPageSetup()->setHorizontalCentered(true);
                $actSheet->getPageMargins()->setFooter(1.0);
                $actSheet->getHeaderFooter()->setOddFooter('&L教师：_______________(签，章)' . '&CPage &P of &N'.'&R日期：________年____月____日');
                $actSheet-> getColumnDimension('A') -> setwidth(6);
                $actSheet-> getColumnDimension('B') -> setwidth(12);
                $actSheet-> getColumnDimension('C') -> setwidth(8);
                $actSheet-> getColumnDimension('D') -> setwidth(10);
                $actSheet-> getColumnDimension('E') -> setwidth(10);
                $actSheet-> getColumnDimension('F') -> setwidth(12);
                $actSheet-> getColumnDimension($len) -> setwidth(8);
                $actSheet->getRowDimension('1')->setRowHeight(30);
                $actSheet->mergeCells('A1:'.$len.'1');
                $actSheet->mergeCells($len.'5:'.$len.'6');
                $actSheet->mergeCells('A2:C2');
                $actSheet->mergeCells('A3:C3');
                $actSheet->mergeCells('D3:E3');
                $actSheet->mergeCells('A5:A6');
                $actSheet->mergeCells('B5:B6');
                $actSheet->mergeCells('C5:C6');
                $actSheet->mergeCells('D5:E5');
                $actSheet->mergeCells('F5:F6');
                $actSheet->setCellValue('A1',"南京大学".$result1['term']."成绩登记表");
                $actSheet->setCellValue('A2',"课程名称:".$result1['name']);
                $actSheet->setCellValue('A3',"班级名称:".$myclass_value['classname']);
                $actSheet->setCellValue('D2',"学时:".$result1['coursetime']);
                $actSheet->setCellValue('E2',"学分:".$result1['credit']);
                $actSheet->setCellValue('D3',"课程代码:".$result1['coursenumber']);
                $actSheet->getStyle('A1')->applyFromArray($styleA1);

                $actSheet->setCellValue('A5', '序号')
                    ->setCellValue('B5', '学号')
                    ->setCellValue('C5', '姓名')
                    ->setCellValue('A4', '备注填写：异动,旷考,免修,作弊,缓考,任选一个')
                    ->setCellValue('D5', '最终成绩')
                    ->setCellValue('D6', '百分制成绩')
                    ->setCellValue('E6', '等级成绩')
                    ->setCellValue('F5', '期末成绩')
                    ->setCellValue($len.'5', '备注');  
                $map['classname']=$myclass_value['classname'];
                $my = $dao -> field('susername,struename,score,qimoscore,ordinaryscore,plus')->where($map) -> order('susername asc') -> select();
                foreach($my as $my_key=>$my_value) {
                    $temp=$my_key+7;//从第7行开始写 
                    $actSheet->setCellValue('A'.$temp, $my_key+1)
                        ->setCellValue('B'.$temp, $my_value['susername'])
                        ->setCellValue('C'.$temp, $my_value['struename'])
                        ->setCellValue('D'.$temp, '')
                        ->setCellValue('E'.$temp, '')
                        ->setCellValue($len.$temp, '');
                    for($i=1;$i<=$examlen;$i++) {
                        $j=chr(ord('E')+$i*2);
                        $m=chr(ord('E')+$i*2+1);
                        $actSheet->mergeCells($j.'5:'.$m.'5');
                        $actSheet-> getColumnDimension($j) -> setwidth(6);
                        $actSheet-> getColumnDimension($m) -> setwidth(6);
                        $actSheet->setCellValue($j.$temp,'')
                                ->setCellValue($j.'5', '考试')
                                ->setCellValue($j.'6', '初考')
                                ->setCellValue($m.'6', '补考')
                                ->setCellValue($m.$temp, ''); 
                    }
                    $actSheet->getRowDimension($temp)->setRowHeight(14);
                }
                $end_row=count($my)+6;
                $actSheet->getStyle('A5:'.$len.'5')->applyFromArray($styleA5);
                $actSheet->getRowDimension('5')->setRowHeight(14);
                $actSheet->getStyle('A6:'.$len.$end_row)->applyFromArray($styleA6);   
            }
            $filename='成绩登记表['.$result1['term'].'--'.$result1['name'].'].xls';
            $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;  
        }
    }
    public function downScoreB(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        
        include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("NJU-HND auto generated Document");
        $styleA1= array(
            'font' => array(
                'bold' => true,
                'size' => 16,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $styleA5= array(
            'font' => array(
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
        $styleA6= array(
            'font' => array(
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
        if(session('isenglish') == '1') {
            $dao = D('ScoreclassView');
            $map['subjectid']=$result1['id'];
            $myclass = $dao -> where($map) ->field('classename')-> group('classename')->order('year desc,classname asc') -> select();
            
            foreach($myclass as $myclass_key=>$myclass_value){
                if($myclass_key>0){
                    $objPHPExcel->createSheet();
                }
                $actSheet=$objPHPExcel->getSheet($myclass_key);
                $actSheet->setTitle($myclass_value['classename']);
                $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
                $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                $actSheet->getPageSetup()->setHorizontalCentered(true);
                $actSheet->getPageMargins()->setFooter(1.0);
                $actSheet->getHeaderFooter()->setOddFooter('&LTeacher:_______________(Sign, Stamp)' . '&CPage &P of &N'.'&RDate:________Year____Month____Day');
                $actSheet-> getColumnDimension('A') -> setwidth(6);
                $actSheet-> getColumnDimension('B') -> setwidth(12);
                $actSheet-> getColumnDimension('C') -> setwidth(12);
                $actSheet-> getColumnDimension('D') -> setwidth(12);
                $actSheet-> getColumnDimension('E') -> setwidth(12);
                $actSheet-> getColumnDimension('F') -> setwidth(12);
                $actSheet-> getColumnDimension('G') -> setwidth(12);
                $actSheet->getRowDimension('1')->setRowHeight(30);
                $actSheet->mergeCells('A1:G1');
                $actSheet->mergeCells('A2:C2');
                $actSheet->mergeCells('A3:C3');
                $actSheet->mergeCells('A5:A6');
                $actSheet->mergeCells('B5:B6');
                $actSheet->mergeCells('C5:C6');
                $actSheet->mergeCells('D3:E3');
                $actSheet->mergeCells('D5:E5');
                $actSheet->mergeCells('F5:G5');
                $actSheet->setCellValue('A1',"Nanjing University".$result1['term']."Scoring Sheet");
                $actSheet->setCellValue('A2',"Course Name：".$result1['ename']);
                $actSheet->setCellValue('A3',"Class Name：".$myclass_value['classename']);
                $actSheet->setCellValue('D2',"Class Hour：".$result1['coursetime']);
                $actSheet->setCellValue('E2',"Credits：".$result1['credit']);
                $actSheet->setCellValue('D3',"Course Code：".$result1['coursenumber']);
                $actSheet->getStyle('A1')->applyFromArray($styleA1);
                $actSheet->setCellValue('A5', 'Serial Number')
                    ->setCellValue('B5', 'Student Number')
                    ->setCellValue('C5', 'Student Name')
                    ->setCellValue('D5', 'First-time Exam')
                    ->setCellValue('D6', 'according centesimal system')
                    ->setCellValue('E6', 'according hierarchical system')
                    ->setCellValue('F5', 'Make-up Exam')
                    ->setCellValue('F6', 'according centesimal system')
                    ->setCellValue('G6', 'according hierarchical system');
                $map['classename']=$myclass_value['classename'];
                $my = $dao -> field('susername,sename,bscore,plus,score,bscore,levelscore,blevelscore')->where($map) -> order('susername asc') -> select();
                foreach($my as $my_key=>$my_value){
                    $temp=$my_key+7;//从第7行开始写
                    $actSheet->setCellValue('A'.$temp, $my_key+1)
                        ->setCellValue('B'.$temp, $my_value['susername'])
                        ->setCellValue('C'.$temp, $my_value['sename'])
                        ->setCellValue('D'.$temp, $my_value['score'])
                        ->setCellValue('E'.$temp, $my_value['levelscore'])
                        ->setCellValue('F'.$temp, $my_value['bscore'])
                        ->setCellValue('G'.$temp, $my_value['blevelscore']);
                    $actSheet->getRowDimension($temp)->setRowHeight(14);
                }
                $end_row=count($my)+6;
                $actSheet->getStyle('A5:G5')->applyFromArray($styleA5);
                $actSheet->getRowDimension('5')->setRowHeight(14);
                $actSheet->getStyle('A6:G'.$end_row)->applyFromArray($styleA6);   
            }
            $filename='scoringSheet['.$result1['term'].'--'.$result1['name'].'].xls';
            $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit; 
        } else {
            $dao = D('ScoreclassView');
            $map['subjectid']=$result1['id'];
            $myclass = $dao -> where($map) ->field('classname')-> group('classname')->order('year desc,classname asc') -> select();
            foreach($myclass as $myclass_key=>$myclass_value){
                if($myclass_key>0){
                    $objPHPExcel->createSheet();
                }
                $actSheet=$objPHPExcel->getSheet($myclass_key);
                $actSheet->setTitle($myclass_value['classname']);
                $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
                $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                $actSheet->getPageSetup()->setHorizontalCentered(true);
                $actSheet->getPageMargins()->setFooter(1.0);
                $actSheet->getHeaderFooter()->setOddFooter('&L教师：_______________(签，章)' . '&CPage &P of &N'.'&R日期：________年____月____日');
                $actSheet-> getColumnDimension('A') -> setwidth(6);
                $actSheet-> getColumnDimension('B') -> setwidth(12);
                $actSheet-> getColumnDimension('C') -> setwidth(12);
                $actSheet-> getColumnDimension('D') -> setwidth(12);
                $actSheet-> getColumnDimension('E') -> setwidth(12);
                $actSheet-> getColumnDimension('F') -> setwidth(12);
                $actSheet-> getColumnDimension('G') -> setwidth(12);
                $actSheet->getRowDimension('1')->setRowHeight(30);
                $actSheet->mergeCells('A1:G1');
                $actSheet->mergeCells('A2:C2');
                $actSheet->mergeCells('A3:C3');
                $actSheet->mergeCells('A5:A6');
                $actSheet->mergeCells('B5:B6');
                $actSheet->mergeCells('C5:C6');
                $actSheet->mergeCells('D3:E3');
                $actSheet->mergeCells('D5:E5');
                $actSheet->mergeCells('F5:G5');
                $actSheet->setCellValue('A1',"南京大学".$result1['term']."成绩登记表");
                $actSheet->setCellValue('A2',"课程名称：".$result1['name']);
                $actSheet->setCellValue('A3',"班级名称：".$myclass_value['classname']);
                $actSheet->setCellValue('D2',"学时：".$result1['coursetime']);
                $actSheet->setCellValue('E2',"学分：".$result1['credit']);
                $actSheet->setCellValue('D3',"课程代码：".$result1['coursenumber']);
                $actSheet->getStyle('A1')->applyFromArray($styleA1);
                $actSheet->setCellValue('A5', '序号')
                    ->setCellValue('B5', '学号')
                    ->setCellValue('C5', '姓名')
                    ->setCellValue('D5', '初考成绩')
                    ->setCellValue('D6', '百分制成绩')
                    ->setCellValue('E6', '等级成绩')
                    ->setCellValue('F5', '补考成绩')
                    ->setCellValue('F6', '百分制成绩')
                    ->setCellValue('G6', '等级成绩');
                $map['classname']=$myclass_value['classname'];
                $my = $dao -> field('susername,struename,bscore,plus,score,bscore,levelscore,blevelscore')->where($map) -> order('susername asc') -> select();
                foreach($my as $my_key=>$my_value){
                    $temp=$my_key+7;//从第7行开始写
                    $actSheet->setCellValue('A'.$temp, $my_key+1)
                        ->setCellValue('B'.$temp, $my_value['susername'])
                        ->setCellValue('C'.$temp, $my_value['struename'])
                        ->setCellValue('D'.$temp, $my_value['score'])
                        ->setCellValue('E'.$temp, $my_value['levelscore'])
                        ->setCellValue('F'.$temp, $my_value['bscore'])
                        ->setCellValue('G'.$temp, $my_value['blevelscore']);
                    $actSheet->getRowDimension($temp)->setRowHeight(14);
                }
                $end_row=count($my)+6;
                $actSheet->getStyle('A5:G5')->applyFromArray($styleA5);
                $actSheet->getRowDimension('5')->setRowHeight(14);
                $actSheet->getStyle('A6:G'.$end_row)->applyFromArray($styleA6);   
            }
            $filename='成绩登记表['.$result1['term'].'--'.$result1['name'].'].xls';
            $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;   
        }
        
    }
    public function menuCourseware() {
        $menu['courseware']='所有课件';
        $menu['coursewareAdd']='新建课件';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function emenuCourseware() {
        $menu['courseware']='Total Courseware';
        $menu['coursewareAdd']='New Courseware';
        $this->assign('menu',$this ->autoMenu($menu));  
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
    public function coursewareAdd() {
        $this->assign('category_fortag',$this -> getSystem("courseware"));
        
        if(session('isenglish') == '1') {
            $this -> emenuCourseware();
            $this -> display('ecoursewareAdd');
        } else {
            $this -> menuCourseware();
            $this -> display();
        }
	}
    public function coursewareInsert(){
		$category = $_POST['category'];
		if (empty($category)) {
			$this -> error('必填项不能为空');
		} 
        $uploader_count = $_POST['uploader_count'];
        if ($uploader_count>0) {
			$dao = D('courseware');
            $data['category']=$category;
            for($i=0;$i<$uploader_count;$i++){
                $data['filename']=$_POST['uploader_'.$i.'_name'];
                $data['fileurl']='/sys/upload_pl/'.date ('Ym/d/').$_POST['uploader_'.$i.'_tmpname'];
                $data['filesize']=$_POST['uploader_'.$i.'_size'];
                $data['ctime']=date("Y-m-d H:i:s");
                $data['author']=session('username');
                $dao->add($data);
            }
            $this->success('已成功保存');
		} else{
            $this -> error('未上传任何文件');
        }
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
        $map['author']=session('username');
        $dao = D('courseware');
		$count = $dao -> where($map) -> count();
		 
        
        if(session('isenglish') == '1') {
            if ($count > 0) {
                import("@.ORG.Page");
                $listRows = 20;
                $p = new Page($count, $listRows);
                $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
                $p->setConfig('header','records');
                $p->setConfig('theme', '%totalRow% %header%  %nowPage%/%totalPage% Pages');
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
            }
            $this -> emenuCourseware();
            $this->display('ecourseware');
        } else {
            if ($count > 0) {
                import("@.ORG.Page");
                $listRows = 20;
                $p = new Page($count, $listRows);
                $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
            }
            $this ->menuCourseware();
            $this -> display();
        }
	}
    public function coursewareEdit() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('courseware');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
            $this -> assign('category_fortag',$this -> getSystem("courseware"));
            
            if(session('isenglish') == '1') {
                $this -> emenuCourseware();
                $this->display('ecoursewareEdit');
            } else {
                $this -> menuCourseware();
                $this -> display();
            }
		} else{
            $this -> error('该记录不存在');
        }
	}
    public function coursewareDel() {
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
       
        $map['id']=array('in',$id);
        $dao=D('courseware');
        $count=$dao->where($map)->count();
        if($count>0){
            $ids=explode(',',$id);
            $i=0;
            foreach($ids as $value){
                $map2['id']=$value;
                $old=$dao->where($map2)->find();
                $oldurl = dirname(__FILE__) . '/../../..'; //转化为物理路径
                $oldurl.=$old['fileurl'];
                $isDelete=true;
                if(file_exists($oldurl)){
                    $isDelete=@unlink($oldurl); 
                } 
                if($isDelete){
                    $result=$dao->where($map2)->delete();
                    $i++;
                } 
            }
            if($i==$count){
                $this->success('已成功删除');
            }else{
                $this->error('已删除部分文件。某些文件正在被下载，暂无法删除。请稍后再试。');
            }
        }else{
            $this->error('该记录不存在');
        }
	} 
    public function coursewareUpdate() {
		$filename = $_POST['filename'];
		$category = $_POST['category'];
		if (empty($filename) ||empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('courseware');
		if ($dao -> create()) {
            $dao -> ctime = date("Y-m-d H:i:s") ;
			$checked = $dao -> save();
			if ($checked>0) {
				$this -> success('已成功保存');
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
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
        
        $filename=$my['filename'];//用户端显示的文件名 
        $php_path = dirname(__FILE__) . '/../../..'; //转化为物理路径
        $fileurl=$php_path.$my['fileurl'];
        $encoded_filename = urlencode($filename);
        $encoded_filename = str_replace("+", "%20", $encoded_filename);
        $downfile=$fileurl;//物理路径 
        $size=filesize($downfile);
        $file = @ fopen($downfile,"r");
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
        }
    }
    public function loadexcel()
    {
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
        $id = $_POST['resultid'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao1 = D('CourseteacherView');
        $map1['id']= $id;
        $result1=$dao1 -> where($map1)-> find();
        if($result1['teacher']!==session('username')){
            $this->error('权限不足');
        }
        $dao2 = D('ScoreclassView');
        $map2['subjectid']=$result1['id'];
        $myclass = $dao2 -> where($map2) ->field('classname')-> group('classname')->order('year desc,classname asc') -> select();
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
        for($k=0;$k<count($myclass);$k++)
        {
            $sheetData = $objPHPExcel->getSheet($k)->toArray(null,true,true,true);
            $count=count($sheetData);
            $count1=(count($sheetData[6])-7)/2;
            $len=chr(ord('E')+($count1+1)*2);
             for($i=7;$i<=$count;$i++){
               $str='';
                $data_a[$i-7]['score'] = $sheetData[$i]['D'];
                $data_a[$i-7]['levelscore'] = $sheetData[$i]['E'];
                $data_a[$i-7]['plus'] = $sheetData[$i][$len];
                $data_a[$i-7]['qimoscore'] = $sheetData[5]['F'].':'.$sheetData[$i]['F'];
                for($j=1;$j<=$count1;$j++)
                {
                    $len1=chr(ord('E')+$j*2);
                    $len2=chr(ord('E')+$j*2+1);
                 $str.='第'.$j.'次'.$sheetData[5][$len1].':'.$sheetData[$i][$len1].':'.$sheetData[$i][$len2].',';
                }
                if($str)
                $data_a[$i-7]['ordinaryscore']=substr($str,0,-1);
                else
                  $data_a[$i-7]['ordinaryscore']=null;  
                $dao=D('score');
                $dao->where('susername="'.$sheetData[$i]['B'].'" and subjectid='.$id)->save($data_a[$i-7]);
            }
        }
        $this->success('提交成功');
    }
} 

?>