<?php
class OfficeAction extends CommonAction {
	public function index() {
        $User = D('User');
		$map['username'] = session('username');
		$photo = $User -> where($map) -> getField('photo');
		$this -> assign('photo', $photo);
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
    public function tool() {
		$this -> display();
	}
    public function menuteacher() {
        $menu['teacher']='所有账号';
        $menu['addTeacher']='新增账号';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function saveUkey(){
        $username=$_GET['username'];
        $ukeysn=$_GET['ukeysn'];
        $ukeypwd=$_GET['ukeypwd'];
        if(!isset($username)||!isset($username)||!isset($ukeypwd)){
            $this->error('参数缺失');
        }
        $User=D('User');
        $data['username']=$username;
        $id=$User->where($data)->getField('id');
        if($id){
            $data['id']=$id;
            $data['ukeysn']=$ukeysn;
            $data['ukeypwd']=$ukeypwd;
            $result=$User->save($data);
            if($result){
                $this->success('已成功刻录');
            }else{
                $this->error('数据库影响行数为0');
            }
        }else{
            $this->error('该人员在数据库中不存在');
        }
    }
    public function checkUser(){
        $username=$_GET['username'];
        if(!isset($username)){
            $this->error('参数缺失');
        }
        $User=D('User');
        $data['username']=$username;
        $id=$User->where($data)->getField('id');
        if($id){
           
            $this->success('');
        }else{
            $this->error('该人员在数据库中不存在,不能进行刻录');
        }
    }
	public function student() {
        if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
			$map['username|truename'] =array('like','%'.$searchkey.'%');
			$this->assign('searchkey',$searchkey);
        }
		$User = D("User");
		$map['role'] =array('IN','EduStu,EduPar');
		$count = $User -> where($map) -> count();
		if ($count > 0) {
            import("@.ORG.Page");
            $listRows=D('System')->where("category='office' and name='listrow'")->getField("content");
			$p = new Page($count, $listRows);
			$my = $User -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);			
		} 
        $this -> display();
	} 
    public function teacher() {
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
        $this->menuteacher();
        $this -> display();
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
    public function sys() {
		$Sys = D("System");
		$map['category'] = 'office';
		$count = $Sys -> where($map) -> count();
		if ($count > 0) {
			$my = $Sys -> where($map) -> order('id desc') -> select();
			$this -> assign('my', $my);			
		} 
        $this -> display();
	} 
    public function saveSys(){
        $id=$_POST['id'];
        $content=$_POST['content'];
        if(empty($id)||empty($content)){
            $this->error('必填项不能为空');
        }
        $Sys = D("System");
        $data['id']=$id;
        $data['content']=$content;
        $result=$Sys->save($data);
        if($result>0){
            $this->success('已成功保存');
        }
    }
	public function addStudent() {
		$this -> display();
	}
    public function addTeacher() {
        $User=D('User');
        $map['role']= array('not in','EduStu,EduPar');
        $map['ispermit']=1;
        $my=$User->where($map)->field('username,truename')->order('username asc')->select();
        $role=array('Zero'=>'零权限');
        $this->assign('role',$role);
        $this->assign('my',$my);
        $this->menuteacher();
		$this -> display();
	}
    public function delStudent() {
        $studentid=$_GET['studentid'];
        if(!isset($studentid)){
            $this->error('参数缺失');
        }
		//补充填写判断该帐号是否已有活动记录
        $map['username']=array('in',$studentid);
        $result=D('User')->where($map)->delete();
        if($result>0){
            $this->success('已成功删除');
        }
	} 
    public function delTeacher() {
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
		//补充填写判断该帐号是否已有活动记录
        $map['username']=array('in',$id);
        $result=D('User')->where($map)->delete();
        if($result>0){
            $this->success('已成功删除');
        }
	} 
    public function doPermit() {
        $studentid=$_GET['studentid'];
        $ispermit=$_GET['ispermit'];
        if(!isset($studentid)||!isset($ispermit)){
            $this->error('参数缺失');
        }
        $map['username']=array('in',$studentid);
        $User=D('User');
        $data['ispermit']=$ispermit;
        $result=$User->where($map)->setField($data);
        if($result>0){
            $tips=$ispermit==1?'已成功恢复':'已成功冻结';
            $this->success($tips);
        }
	} 
    public function resetPwd() {
        $studentid=$_GET['studentid'];
        $ispermit=$_GET['role'];      //role=1表示该角色为教师,默认密码为999999   role=0表示该角色为学生 默认密码为888888
        if(!isset($studentid)||!isset($ispermit)){
            $this->error('参数缺失');
        }
                $User=D('User');
                $ids=explode(',',$studentid);
                if(count($ids)>1) {
                    $this->error('系统提示：为避免误操作，每次只可选择一个用户');
                }
                if(in_array('0',$ids)){
                    $this->error('非法操作');
                } 
                if($ispermit=='1'){
                    $password='52c69e3a57331081823331c4e69d3f2e';
                }else{
                    $password='21218cca77804d2ba1922c33e0151105';
                }
                $result=$User->where("username = '".$studentid."'")->setField('pwd',$password);
				if($result!==false){
                    if($ispermit=='1'){
                        $this->success('已成功重置密码，新密码为999999');
                    }else{
                       $this->success('已成功重置密码，新密码为888888'); 
                    }
					
				}else {
					$this->error('重置密码失败');
				}
	} 
    public function getEnroll(){
        $truename=$_POST['truename'];
        if(!isset($truename)){
            $this->error('参数缺失');
        }
        $Enroll=D('Enroll');
        $map['truename']=$truename;
        $map['status']='3';
        $my=$Enroll->where($map)->select();
        $this->assign('my',$my);
        $this->assign('count',count($my));
        $this->display();
    }
    public function insertStudent(){
        if(empty($_POST['username'])||empty($_POST['truename'])||empty($_POST['enrollid'])){
            $this->error('必填项不能为空');
        }
        $username=$_POST['username'];
        $truename=$_POST['truename'];
        $enrollid=$_POST['enrollid'];
        if(strlen($username)!==11){
            $this->error('帐号位数不对，含GJ总长应为11位');
        }
        $User=D('User');
        $map['username']=$username;
        $count=$User->where($map)->count();
        if($count>0){
            $this->error('该帐号已存在于数据库中，请使用其它帐号');
        }
        if(!empty($_POST['enrollid'])){
            $Enroll=D('Enroll');
            $data['id']=$enrollid;
            $data['username']=$username;
            $result=$Enroll->save($data);
        }
        $data_a['username']=$username;
        $data_a['truename']=$truename;
        $data_a['pwd']='21218cca77804d2ba1922c33e0151105';//6个8
        $data_a['role']='EduStu';
        $data_a['ctime']=date("Y-m-d H:i:s");
        $insertID=$User->add($data_a);
        $data_a['username']='JZ'.substr($username,2,strlen($username)-1);//家长帐号为JZ20120808
        $data_a['truename']=$truename.'的家长';
        $data_a['role']='EduPar';
        $insertID=$User->add($data_a);
        
        if($insertID){
            $this->success('已成功保存');
        }else{
            $this->error('数据库影响行数为0');
        }
    }
    public function insertTeacher(){
        if(empty($_POST['username'])||empty($_POST['truename'])||empty($_POST['role'])){
            $this->error('必填项不能为空');
        }
        $username=$_POST['username'];
        $truename=$_POST['truename'];
        $role=$_POST['role'];
        if(strlen($username)!==5){
            //$this->error('帐号位数不对，含T总长应为5位');
        }
        if(($username=='T0000')||($username=='T0001')||($username=='T0002')||($username=='T0003')||($username=='T0004')||($username=='T0005')||($username=='T0006')||($username=='T0007')||($username=='T0008')||($username=='T0009')){
            $this->error('该帐号为系统保留，不予注册使用');
        }
        $User=D('User');
        $map['username']=$username;
        $count=$User->where($map)->count();
        if($count>0){
            $this->error('该帐号已存在于数据库中，请使用其它帐号');
        }
       
        $data_a['username']=$username;
        $data_a['truename']=$truename;
        $data_a['pwd']='52c69e3a57331081823331c4e69d3f2e';//6个9
        $data_a['role']=$role;
        $data_a['ctime']=date("Y-m-d H:i:s");
        $insertID=$User->add($data_a);
        if($insertID){
            $this->success('已成功保存');
        }else{
            $this->error('数据库影响行数为0');
        }
    }
    public function menuright() {
    $menu['right']='分配权限首页';
    $this->assign('menu',$this ->autoMenu($menu));  
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
                $my[$key]['gradeName']=$value['grade'];
            }
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        }
        $this -> menuright();
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
            $roles = R('Index/getRole');
            unset($roles["Zero"]);
            $this ->assign('my',$my);
            $this->assign('role',$roles);//角色权限
            $this->assign('role_hava',explode(',',$my['role']));
            $year = date("Y");//年份权限
            for ($i = $year-5; $i <= $year+5; $i++) { 
                $grade[$i] = $i;
            }
            $this->assign('grade',$grade);
            $this->assign('grade_hava',explode(',',$my['grade']));
            $this -> menuright();
            $this -> display();
        }
    }
    public function rightUpdate() {
        if (empty($_POST['role'])) {
            $this -> error('必填项不能为空');
        }

        $dao = D('user');
        if ($dao -> create()) {
            $dao ->role = implode(',', $_POST['role']);
            $dao ->grade = implode(',', $_POST['grade']);
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
} 

?>