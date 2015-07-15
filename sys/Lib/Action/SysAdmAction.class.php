<?php
class SysAdmAction extends CommonAction {
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
    public function enrollMenu() {
        $menu=array();
        $menu['enroll']='删除学生';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function Menu() {
        $menu=array();
        $menu['change']='学分学时修改';
        $menu['score']='成绩修改';
        $menu['record']='操作日志';
        $menu['mailteacher']='信件往来';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function menuteacher() {
        $menu['trainproject']='项目统计';
        $menu['paststatus']='培训档案';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function menustudent($id) {
        if($id==1){
            $menu['paststatus']='培训档案';
        }else{
            $menu['trainproject']='所有项目';
        }
        
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function menuProcess() {
        $menu['enrollproject']='申请国家统计';
        $menu['enrollpaststatus']='留学档案';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function menumail() {
        $menu['mailteacher']='收件箱';
        $menu['mailsend']='已发信件';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function enroll() {
        $this ->enrollMenu();
        $this -> display(); 
	}
    public function score(){
        $name=$_POST['name'];
        $number=$_POST['number'];
        
       
        $this->assign('name',$name);
        $this->assign('number',$number);
        $this ->Menu();
        $this -> display();         
        
        
    }
    public function getAllTerm() {
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
    public function change(){
        $this -> assign('category_fortag', $this->getAllTerm());
        $this ->Menu();
        $this -> display();         
    }
    public function scoreTimeEdit(){
        $term=$_GET['term'];
        $number=$_GET['number'];
        if(empty($term)||empty($number)){
            $this->error("请选择学期并填写课程编号");
        }
        $dao = D('ScoreclassView');
        $map['coursenumber']=$number;
        $map['term']=$term;
        $info=$dao->where($map)->select();
        $count=$dao->where($map)->count();
        $coursename=$info['0']['coursename'];
        $teacher['tusername']=$info['0']['tusername'];
        $teacher['ttruename']=$info['0']['ttruename'];
        $this->assign('term',$term);
        $this->assign('number',$number);
        $this->assign('coursename',$coursename);
        $this->assign('teacher',$teacher);
        $this->assign('my',$info);
        $this->assign('count',$count);
        $this ->Menu();
        $this->display();
    }
    public function scoreTimeUpdate(){
        $coursename=$_POST['coursename'];
        $courseename=$_POST['courseename'];
        $coursetime=$_POST['coursetime'];
        $credit=$_POST['credit'];
        $number=$_POST['number'];
        $term=$_POST['term'];
        if(empty($number)||empty($number)){
            $this->error('参数缺失');
        }
        $map['coursenumber']=$number;
        $map['term']=$term;
        $dao=D("Score");
        $info=$dao->where($map)->find();
        $records='将'.$info['ttruename'].'老师的'.$info['coursename'].'课程的 ';
        if(!empty($coursename)){
            $data['coursename']=$coursename;
            $records.='课程名称由'.$info['coursename'].'改为'.$coursename.'   ';
        }
         if(!empty($courseename)){
            $data['courseename']=$courseename;
             $records.='课程英文名称由'.$info['courseename'].'改为'.$courseename.'   ';
        }
         if(!empty($coursetime)){
            $data['coursetime']=$coursetime;
             $records.='学时由'.$info['coursetime'].'改为'.$coursetime.'   ';
        }
         if(!empty($credit)){
            $data['credit']=$credit;
            $records.='学分由'.$info['credit'].'改为'.$credit.'   ';
        }
        
       
        $checked=$dao->where($map)->save($data);
        if($checked > 0){
            $map1['content']=$records;
            $map1['operater']=session('truename');
            $record=D("Record");
            $a=$record->add($map1);
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }
    public function getTerm($username) {
        $a=array();
        $Score = D("Score");
		$map['susername']=$username;
		$map['isvisible']=1;
        $term=$Score -> where($map) ->field('term')->group('term')->order('term asc')-> select();
        foreach($term as $vo){
            $a[$vo['term']]=$vo['term'];
        }
        return $a; 
	}
    public function changeScore(){
        $username=$_GET['id'];
        if(empty($username)){
            $this->error("参数缺失");
        }
        if (isset($_GET['category'])) {
            $map['term'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        }
        $User=D("User");
        $name=$User->where("username = '".$username."'")->getField('truename');
        $Score = D("Score");
		$map['susername']=$username;
		$map['isvisible']=1;
        $term=$Score -> where($map) ->field('term')->group('term')->order('term asc')-> select();
		$term_num=count($term);
        if($term_num>0){
            foreach($term as $key=>$value){
                $map['term']=$value['term'];
                $my[$key]=$Score -> where($map) -> select();
            }
            $this->assign('my',$my);
        }
        $this -> assign('category_fortag',$this->getTerm($username));
        $this->assign("name",$name);
        $this->assign("username",$username);
        $this ->Menu();  
        $this->display();
        
    }
    public function scoreUpdate(){
        $id=$_POST['id'];
        $score=$_POST['score'];
        $plus=$_POST['plus'];
        $bscore=$_POST['bscore'];
        $levelscore=$_POST['levelscore'];
        $blevelscore=$_POST['blevelscore'];
        if(!isset($id)){
           $this->error('参数缺失'); 
        }
        $map['score']=$score;
        $map['plus']=$plus;
        $map['bscore']=$bscore;
        $map['levelscore']=$levelscore;
        $map['blevelscore']=$blevelscore;
        $S=D("Score");
        $scoreBefore=$S->where("id =".$id)->Field('score,plus,bscore,levelscore,blevelscore')->find();
        $scoreLatest['score']=$score;
        $scoreLatest['plus']=$plus;
        $scoreLatest['bscore']=$bscore;
        $scoreLatest['levelscore']=$levelscore;
        $scoreLatest['blevelscore']=$blevelscore;
        $result=$S->where("id =".$id)->save($map);
        $u=$S->where("id =".$id)->Field('struename,coursename')->find();
        if($result > 0){
            $this->operateRecord($u,$scoreBefore,$scoreLatest);
            $this->success("修改成功");
        }else{
            $this->error("未更新记录");
        }
      
    }
    public function operateRecord($name='',$scoreBefore='',$scoreLatest='',$actionname=''){
        if(empty($actionname)){
            $actionname=ACTION_NAME;
        }
		$targetDir = LANG_PATH . 'zh_cn/action.php';
        require($targetDir);
        foreach($_LANG['action'] as $key=>$vo){
            if(stristr($actionname,$key)!=false){
                $action=$vo;break;
            }
        }
        foreach($scoreBefore as $key=>$vo){
            if(empty($vo)&&($key=='score'||$key=='bscore')){
                $scoreBefore[$key]=0;
            }
        }
        foreach($scoreLatest as $key=>$vo){
            if(empty($vo)&&($key=='score'||$key=='bscore')){
                $scoreLatest[$key]=0;
            }
        }
        
        $content=$action.'了'.$name['struename'].'的"'.$name['coursename'].'"课程'.$_LANG[$modulename][$actionname];
       if($scoreBefore['score']!=$scoreLatest['score']){
           $content.='&nbsp;&nbsp;&nbsp;&nbsp;成绩由:"'.$scoreBefore['score'].'"改为"'.$scoreLatest['score'].'"';
       }
       if($scoreBefore['bscore']!=$scoreLatest['bscore']){
           $content.='&nbsp;&nbsp;&nbsp;&nbsp;补考成绩由:"'.$scoreBefore['bscore'].'"改为"'.$scoreLatest['bscore'].'"';
       }
       if((string)$scoreBefore['plus']!=$scoreLatest['plus']){
           $content.='&nbsp;&nbsp;&nbsp;&nbsp;备注由:"'.$scoreBefore['plus'].'"改为"'.$scoreLatest['plus'].'"';
       }
       if((string)$scoreBefore['levelscore']!=$scoreLatest['levelscore']){
           $content.='&nbsp;&nbsp;&nbsp;&nbsp;等级考试成绩由:"'.$scoreBefore['levelscore'].'"改为"'.$scoreLatest['levelscore'].'"';
       }
       if((string)$scoreBefore['blevelscore']!=$scoreLatest['blevelscore']){
           $content.='&nbsp;&nbsp;&nbsp;&nbsp;等级考试补考成绩由:"'.$scoreBefore['blevelscore'].'"改为"'.$scoreLatest['blevelscore'].'"';
       }
        
        $modulename=MODULE_NAME;
        
        $map['content']=$content;
        $map['operater']=session('truename');
        $record=D("Record");
        $a=$record->add($map);
 
    }
    public function record(){
        $map['id'] = array(array('gt',0)) ;
        if (isset($_GET['searchkey'])) {
            $map['content'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $times=date('Y-m-d H:i:s',strtotime($_GET['category']));
            $map['time'] = array(array('gt',$times)) ;
            $this -> assign('category_current', $_GET['category']);
        } 
        $dao = D('Record');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('time desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $category=array('last week'=>'上一周记录','last month'=>'上一月记录','last year'=>'去年记录');
        $this -> assign('category_fortag', $category);
        $this -> menu();
        $this -> display();
    }
    public function getHND(){
        $name=$_POST['name'];
        $number=$_POST['number'];
        if(!isset($name)&&!isset($number)){
            $this->error('参数缺失');
        }
        $dao=D('User');
        $map['truename|username'] =array($name,$number,'_multi'=>true);
        $my=$dao->where($map)->select();
        $this->assign('my',$my);
        $this->assign('count',count($my));
        $this->display();
    }
    public function enrollDel() {
        $name=$_POST['name'];
        $ctime=$_POST['ctime'];
        $mobile=$_POST['mobile'];
        if(empty($name)||empty($ctime)||empty($mobile)){
            $this->error('必填字段不能留空');
        }
        $map['truename']=$name;
        $map['mobile']=$mobile;
        $map['ctime']=array('like',"$ctime%");
        $result=D('Enroll')->where($map)->delete();
        if($result>0){
            $this->success('已成功删除');
        }else{
            $this->error('查无匹配的学生');
        }
	}
    public function getRoleName($aaa){
        $roles=explode(',',$aaa);
        $all_role=R('Index/getRole');
        $my_role_name=array();
        foreach($roles as $key=>$value){
            $my_role_name[]=$all_role[$value];
        }
        $bbb=implode(',',$my_role_name);
        return $bbb;
    }
    public function right() {
        if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
			$map['username|truename'] =array('like','%'.$searchkey.'%');
			$this->assign('searchkey',$searchkey);
        }
        if(isset($_GET['role'])) {
            $searchkey = $_GET['role'];
			$map['role'] =array('like','%'.$searchkey.'%');
			$this->assign('myrole',$searchkey);
        }else{
            $map['role'] = array('not in','EduStu,EduPar');
        }
		$User = D("User");
		$count = $User -> where($map) -> count();
        $role=R('Index/getRole');
        $this->assign('role',$role);
		if ($count > 0) {
            import("@.ORG.Page");
            $listRows=D('System')->where("category='office' and name='listrow'")->getField("content");
			$p = new Page($count, $listRows);
			$my = $User -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
            foreach($my as $key=>$value){
                $my[$key]['roleName']=$this->getRoleName($value['role']);
            }
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);			
		} 
        $this -> display();
	}
    public function rightSet() {
        if(!isset($_GET['username'])) {
			$this->error('参数缺失');
        }
        $dao=D('user');
        $map['username']=$_GET['username'];
        $map['role'] = array('not in','EduStu,EduPar');
        $my=$dao->where($map)->find();
        if($my){
            $this ->assign('my',$my);
            $this->assign('role',R('Index/getRole'));
            $this->assign('role_hava',explode(',',$my['role']));
            $this -> display();
        }   
	}
    public function rightUpdate() {
        if (empty($_POST['role'])) {
            $this -> error('必填项不能为空');
        } 
       
        $dao = D('user');
        if ($dao -> create()) {
            $dao -> role = implode(',', $_POST['role']);
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
    public function trainproject() {
		$dao = D("System");
        $map['name']='project';
        $map['category']='train';        
        $my=$dao->Field('content')->where($map)->find();
		$arr=explode(',',$my['content']);
        $dao2 = D("Train");
		foreach($arr as $key => $value) {
            $map2['project']=array('like', '%' .$value. '%');
			$list[$value] ['count']= $dao2->field('id')->where($map2)->count();
		} 
		$this->assign('my',$list);
		$this->menuteacher();
        $this->display();
	}
    public function enrollproject() {
		$dao = D("System");
        $map['name']='country';
        $map['category']='abroad';        
        $my=$dao->Field('content')->where($map)->find();
		$arr=explode(',',$my['content']);
        $dao2 = D("Abroad");
		foreach($arr as $key => $value) {
            $map2['country']=array('like', '%' .$value. '%');
			$list[$value] ['count']= $dao2->field('id')->where($map2)->count();
		} 
		$this->assign('my',$list);
		$this ->menuProcess();
        $this->display();
	}
    public function paststatus() {
		
        $dao2 = D("Train");
		$list = $dao2 -> field( 'year(ctime) as year') -> group('year') -> order('year desc') -> select();
        foreach($list as $key=>$vo){
            $list[$key]['count'] = $dao2->where('year(ctime) ='.$vo['year']) -> count();
        }
		
		$this->assign('my',$list);
		$this->menuteacher();
        $this->display();
	} 
    public function enrollpaststatus() {
		
        $dao2 = D("Abroad");
		$list = $dao2 -> field( 'year(ctime) as year') -> group('year') -> order('year desc') -> select();
        foreach($list as $key=>$vo){
            $list[$key]['count'] = $dao2->where('year(ctime) ='.$vo['year']) -> count();
        }
		
		$this->assign('my',$list);
		$this->menuProcess();
        $this->display();
	}
    public function mailteacher() {
		
        $dao2 = D("User");
        $map2['role']=array('like', '%EduDir%');
		$list = $dao2->Field('username,truename') ->where($map2)-> select();
        $dao=D("Mail");
        foreach($list as $key=>$vo){
            $map['a1|b1']=$vo['username'];
            $list[$key]['count'] = $dao->where($map) -> count();
        }
		$this->assign('my',$list);
		$this ->Menu();
        $this->display();
	}
    public function mail() {
        if (isset($_GET['searchkey'])) {
            $map['title'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        }
        if(empty($_GET['username'])){
            $this->error('参数缺失');
        }
        $dao = D('mail');
        $map['a1'] = $_GET['username'];
        $map['isdela'] = 0;
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
        $this -> assign("username", $_GET['username']);
        $this -> display();
    }
    public function mailsend() {
        if (isset($_GET['searchkey'])) {
            $map['title'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if(empty($_GET['username'])){
            $this->error('参数缺失');
        }
        $dao = D('mail');
        $map['b1'] = $_GET['username'];
        $map['isdelb'] = 0;
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
        $this -> assign("username", $_GET['username']);
        $this -> display();
    } 
    public function mailView() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('mail');
        $map['id'] = $id;
        $map['isdela'] = 0;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> display();
        } else {
            $this -> error('该记录不存在或权限不足');
        } 
    } 
    public function mailsendView() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('mail');
        $map['id'] = $id;
        $map['isdelb'] = 0;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> display();
        } else {
            $this -> error('该记录不存在或权限不足');
        } 
    }     
    public function student(){
        if (isset($_GET['searchkey'])) {
            $map['susername|struename'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['username'])) {
            $map['project'] = array('like', '%' .$_GET['username']. '%');
            $this -> assign('username', $_GET['username']);
        } 
        if (isset($_GET['year'])) {
           $map['_string'] = 'year(ctime) ='.$_GET['year'];
            $this -> assign('username', $_GET['year']);
        } 
        $dao = D('Train');
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
        $this->menustudent(1);
        $this->display();
    }
    public function processCategory() {
		if (isset($_GET['searchkey'])) {
			$map['struename'] = array('like', '%' . $_GET['searchkey'] . '%');
			$this -> assign('searchkey', $_GET['searchkey']);
		} 
        if (isset($_GET['category'])) {
			$map['status'] = $_GET['category'];
			$this -> assign('category_current', $_GET['category']);
		}
            if (isset($_GET['year'])) {
            $map['_string'] = 'year(ctime) ='.$_GET['year'];
            $this -> assign('username', $_GET['year']);
        } 
        if (isset($_GET['username'])) {
            $map['country'] = array('like', '%' . $_GET['username'] . '%');
            $this->assign('username',$_GET['username']);    
		} 
        $dao = D('Abroad');
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
        $this ->menuProcess();
        $this -> display(); 
	}
    public function getSystem($name,$category='train') {
		$system = D("System");
		$temp = explode(',', $system -> where("category='".$category."' and name='" . $name . "'") -> getField("content"));
		$a = array();
		foreach($temp as $key => $value) {
			$a[$value] = $value;
		} 
		return $a;
	} 
    public function traindetail(){
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Train');
        $map['id']=$id;
        $my=$dao->where($map)->find();
        if($my){
            $this->assign('my',$my);
            $this->assign('project',$this -> getSystem("project"));
            $this -> assign('project_selected', explode(',', $my['project']));
            $test=D('Traintest')->where("trainid=$id")->order('traintime desc')->select();
            $this -> assign('test', $test);
            $this->display();
        }else{
            $this->error('未找到记录或权限不足');
        }
    }
    public function getStatus(){
        $a=array();
        $a['1']='1、咨询中';
        $a['2']='2、已签约，选校中';
        $a['3']='3、已定校，材料制作中';
        $a['4']='4、材料全部递交，等待录取中';
        $a['5']='5、已录取，签证准备中';
        $a['6']='6、签证已递交';
        $a['7']='7、签证通过，等待开学';
        return $a;
    }
    public function getAbroadTea() {
		$dao = D("User");
        $map['ispermit']=1;
        $map['role']=array('like','%AbroadTea%');
        $my=$dao->Field('username,truename')->where($map)->select();
		$a = array();
		foreach($my as $key => $value) {
			$a[$value['username']] = $value['truename'];
		} 
		return $a;
	} 
    public function detail(){
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Abroad');
        $map['id']=$id;
        $my=$dao->where($map)->find();
        $leave=array('0'=>'否','1'=>'是');
        if($my){
            $this->assign('my',$my);
            $this->assign('leave',$leave);
            $this->assign('method',$this -> getSystem("method","abroad"));
            $this->assign('status',$this->getStatus());
            $this->assign('from',$this -> getSystem("from","abroad"));
            $this->assign('country',$this -> getSystem("country","abroad"));
            $this->assign('degree',$this -> getSystem("degree","abroad"));
            $this->assign('AbroadTea',$this -> getAbroadTea());
            $this -> assign('status_selected', explode(',', $my['status']));
            $this -> assign('from_selected', explode(',', $my['from']));
            $this -> assign('country_selected', explode(',', $my['country']));
            $this -> assign('degree_selected', explode(',', $my['degree']));
            $school=D('Abroadschool')->where("abroadid=$id")->select();
            $this -> assign('school', $school);
            $this->display();
        }else{
            $this->error('未找到记录或权限不足');
        }
    }
} 

?>