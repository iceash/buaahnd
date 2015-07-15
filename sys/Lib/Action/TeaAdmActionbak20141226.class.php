<?php
class TeaAdmAction extends CommonAction {
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
    public function teacher() {
		$dao = D("User");
        $map['ispermit']=1;
        $map['role']=array('like','%EduTea%');
        $count=$dao->where($map)->count();
        import("@.ORG.Page");
        $listRows = 20;
        $p = new Page($count, $listRows);
        $my=$dao->Field('username,truename')->where($map)-> limit($p -> firstRow . ',' . $p -> listRows)->select();
        $page = $p -> show();
        $this -> assign("page", $page);
		$this->assign('my',$my);
        $this->display();
	} 
    public function asn() {
        $user= $_GET['tusername']; 
        $this -> assign('category_fortag', $this ->  getCourse($user));
        if (isset($_GET['searchkey'])) {
            $map['title|content'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['subjectid'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        }
        if(!isset($_GET['tusername'])){
            $this->error("参数缺失，请返回重试！");
        }
              
        $dao = D('Homeworkcreate');
        $map['tusername']= $user;
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
        $this -> assign("user", $user);
        $this -> assign("username",  $this->getTeacherName($user));
        $this -> display();
    } 
    public function getCourse($username,$current) {
        $dao = D('CourseteacherView');
        $map['teacher']= $username;
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
    public function getTeacherName($id){
        $user=D('User');
        $name=$user->where("username ='".$id."'")->getField('truename');
        return $name;
    }
    public function asnDetail() {
        $id = $_GET['id'];
        $username = $_GET['username'];
        if (!isset($id)||!isset($username)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Homeworkcreate');
        $map['id'] = $id;
        $map['tusername'] = $username;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('user', $username);
            $this -> assign("username",  $this->getTeacherName($username));
            $this -> assign('my', $my);
            $this -> assign('category_fortag', $this -> getCourse($username,$my['subjectid']));
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    }
    public function asnCheck() {
        $id = $_GET['id'];
        $username = $_GET['username'];
        if (!isset($id)||!isset($username)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Homeworkcreate');
        $map['id']= $id;
        $map['tusername']= $username;
        $my=$dao->where($map)->find();
        if(!$my){
            $this->error('权限不足');
        }
         $this -> assign('my', $my);
        $dao2 = D('Homework');
        $map2['homeworkid']= $id;
        $count = $dao2 -> where($map2) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 1000;
            $p = new Page($count, $listRows);
            $my2 = $dao2 -> where($map2) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('susername asc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my2', $my2);
        } 
        $this -> assign("user", $username);
        $this -> assign("username",  $this->getTeacherName($username));
        $this -> display();
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
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    }    

} 

?>