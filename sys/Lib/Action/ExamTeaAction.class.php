<?php
class ExamTeaAction extends CommonAction {
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
	public function getLevel() {
		$a = array();
		for($i = 1;$i < 21;$i++) {
			$a[$i] = 'level ' . $i;
		} 
		return $a;
	} 
	public function getAnswer() {
		$a = array('A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D');
		return $a;
	} 
    public function getItem($item,$checked,$name,$separator='') {
		$a = array();
        $kkk = explode('<br />', nl2br($item));
		$j = 'A';
		$a = array();
        $parseStr   = '';
		foreach($kkk as $key => $value) {
            $isCheck=$j==$checked?'checked="checked" ':''; 
            $parseStr .= '<label><input type="radio" '.$isCheck.'name="'.$name.'" value="'.$j.'">'.'[' . $j . '] ' . $value.'</label>&nbsp;&nbsp;'.$separator;
			$j++;
		} 
        return $parseStr;
	} 
	public function addSelect() {
		$this -> assign('level', $this -> getLevel());
		$this -> assign('answer', $this -> getAnswer());
		$this -> display();
	} 
    public function addRead() {
		$this -> assign('level', $this -> getLevel());
		$this -> display();
	} 
    public function addReadNext() {
		$id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Examread');        
        $map['id']=$id;
        $my=$dao->where($map)->find();
        $dao_a=D('Examreaditem');
        $map_a['articleid']=$id;
        $my_a=$dao_a->where($map_a)->order('id asc')->select();
        foreach($my_a as $key=>$value){
            $my_a[$key]['itemRadio']=$this->getItem($value['item'],$value['answer'],'read'.$value['id'],'<br />');
        }
        $this -> assign('answer', $this -> getAnswer());
        $this -> assign('my', $my);
        $this -> assign('my_a', $my_a);
		$this -> display('');
	} 
    public function viewRead() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Examread');
		$map['id']=$id;
        $my=$dao->where($map)->find();
        $dao_a=D('Examreaditem');
        $map_a['articleid']=$id;
        $my_a=$dao_a->where($map_a)->order('id asc')->select();
        foreach($my_a as $key=>$value){
            $my_a[$key]['itemRadio']=$this->getItem($value['item'],$value['answer'],'read'.$value['id'],'<br />');
        }
        $this -> assign('answer', $this -> getAnswer());
        $this -> assign('my', $my);
        $this -> assign('my_a', $my_a);
		$this -> display('');
	} 
    public function editRead() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Examread');
		$map['id']=$id;
        $my=$dao->where($map)->find();
        $dao_a=D('Examreaditem');
        $map_a['articleid']=$id;
        $my_a=$dao_a->where($map_a)->order('id asc')->select();
        foreach($my_a as $key=>$value){
            $my_a[$key]['itemRadio']=$this->getItem($value['item'],$value['answer'],'read'.$value['id'],'<br />');
        }
        $this -> assign('answer', $this -> getAnswer());
        $this -> assign('my', $my);
        $this -> assign('my_a', $my_a);
		$this -> display('');
	} 
	public function select() {
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['id|title'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		if (isset($_GET['level'])) {
			$searchkey = $_GET['level'];
			$map['level'] = $searchkey;
			$this -> assign('mylevel', $searchkey);
		}
		$level = $this -> getLevel();
		$this -> assign('level', $level);
		$Select = D('Examselect');
		$count = $Select -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Select -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> display();
	} 
	public function selectMy() {
		$map['username'] = session('username');
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['id|title'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		if (isset($_GET['level'])) {
			$searchkey = $_GET['level'];
			$map['level'] = $searchkey;
			$this -> assign('mylevel', $searchkey);
		} 
		$level = $this -> getLevel();
		$this -> assign('level', $level);
		$Select = D('Examselect');
		$count = $Select -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Select -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> display('');
	} 
	public function insertSelect() {
		$title = $_POST['title'];
		$item = $_POST['item'];
		$answer = $_POST['answer'];
		$level = $_POST['level'];
		if (empty($title) || empty($item) || empty($answer) || empty($level)) {
			$this -> error('必填项不能为空');
		} 
		$myitem = nl2br($item);
		$kkk = explode('<br />', $myitem);
		if (count($kkk) !== 4) {
			$this -> error('选项个数不为4！请确保每个选项1行，共4行。可能的原因：第5行有个回车');
		} 
		$dao = D('Examselect');
		$map['title'] = $title;
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$this -> error('该题目已存在于数据库中，请勿重复录入');
		} 
		if ($dao -> create()) {
			$dao -> ctime = date("Y-m-d H:i:s");
			$dao -> username = session('username');
			$dao -> truename = session('truename');
			$insertID = $dao -> add();
			if ($insertID) {
				$this -> success('已成功保存');
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
    public function insertRead() {
		$article = $_POST['article'];
		$level = $_POST['level'];
		if (empty($article) ||empty($level)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('Examread');
		$map['article'] = $article;
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$this -> error('该题目已存在于数据库中，请勿重复录入');
		} 
        
		if ($dao -> create()) {
			$dao -> ctime = date("Y-m-d H:i:s");
			$dao -> username = session('username');
			$dao -> truename = session('truename');
			$insertID = $dao -> add();
			if ($insertID) {
				$this -> ajaxReturn($insertID,'已成功保存！',1);
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
    public function insertReadNext() {
		$articleid = $_POST['articleid'];
		$title = $_POST['title'];
		$item = $_POST['item'];
		$answer = $_POST['answer'];
		if (empty($title) ||empty($item)||empty($answer)) {
			$this -> error('必填项不能为空');
		} 
        $myitem = nl2br($item);
		$kkk = explode('<br />', $myitem);
		if (count($kkk) !== 4) {
			$this -> error('选项个数不为4！请确保每个选项1行，共4行。可能的原因：第5行有个回车');
		} 
		$dao = D('Examreaditem');
		$map['articleid'] = $articleid;
		$map['title'] = $title;
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$this -> error('该题目已存在于数据库中，请勿重复录入');
		} 
		if ($dao -> create()) {
			$insertID = $dao -> add();
			if ($insertID) {
				$this -> success('已成功保存');
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
    public function updateSelect() {
		$title = $_POST['title'];
		$item = $_POST['item'];
		$answer = $_POST['answer'];
		$level = $_POST['level'];
		if (empty($title) || empty($item) || empty($answer) || empty($level)) {
			$this -> error('必填项不能为空');
		} 
		$myitem = nl2br($item);
		$kkk = explode('<br />', $myitem);
		if (count($kkk) !== 4) {
			$this -> error('选项个数不为4！请确保每个选项1行，共4行。可能的原因：第5行有个回车');
		} 
		$dao = D('Examselect');
		if ($dao -> create()) {
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
	public function editSelect() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Examselect');
		$map['id'] = $id;
		$map['username'] = session('username');
		$my = $dao -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
            $this -> assign('level', $this -> getLevel());
            $this -> assign('answer', $this -> getAnswer());
            $this -> display();
		} else{
            $this -> error('该题目不存在或权限不足');
        }
	} 
	public function viewSelect() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Examselect');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		$myitem = nl2br($my['item']);
		$kkk = explode('<br />', $myitem);
		$j = 'A';
		$a = array();
		foreach($kkk as $key => $value) {
			$a[$j] = '[' . $j . '] ' . $value;
			$j++;
		} 
		$this -> assign('a', $a);
		$this -> assign('my', $my);
		$this -> display();
	} 
    
    public function addFill() {
		$this -> assign('level', $this -> getLevel());
		$this -> display();
	} 
    public function addWrite() {
		$this -> assign('level', $this -> getLevel());
		$this -> display();
	}
	public function fill() {
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['id|title'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		if (isset($_GET['level'])) {
			$searchkey = $_GET['level'];
			$map['level'] = $searchkey;
			$this -> assign('mylevel', $searchkey);
		} 
		$level = $this -> getLevel();
		$this -> assign('level', $level);
		$Fill = D('Examfill');
		$count = $Fill -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Fill -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> display();
	} 
	public function fillMy() {
		$map['username'] = session('username');
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['id|title'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		if (isset($_GET['level'])) {
			$searchkey = $_GET['level'];
			$map['level'] = $searchkey;
			$this -> assign('mylevel', $searchkey);
		} 
		$level = $this -> getLevel();
		$this -> assign('level', $level);
		$Fill = D('Examfill');
		$count = $Fill -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Fill -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> display('');
	} 
    public function write() {
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['id|title'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		if (isset($_GET['level'])) {
			$searchkey = $_GET['level'];
			$map['level'] = $searchkey;
			$this -> assign('mylevel', $searchkey);
		}
		$level = $this -> getLevel();
		$this -> assign('level', $level);
		$Fill = D('Examwrite');
		$count = $Fill -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Fill -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> display();
	} 
	public function writeMy() {
		$map['username'] = session('username');
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['id|title'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		if (isset($_GET['level'])) {
			$searchkey = $_GET['level'];
			$map['level'] = $searchkey;
			$this -> assign('mylevel', $searchkey);
		}
		$level = $this -> getLevel();
		$this -> assign('level', $level);
		$Fill = D('Examwrite');
		$count = $Fill -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Fill -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> display('');
	} 
    public function read() {
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['id|article'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		if (isset($_GET['level'])) {
			$searchkey = $_GET['level'];
			$map['level'] = $searchkey;
			$this -> assign('mylevel', $searchkey);
		}
		$level = $this -> getLevel();
		$this -> assign('level', $level);
		$Fill = D('Examread');
		$count = $Fill -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Fill -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
		}
        foreach($my as $key=>$value){              
            $a[$key] = preg_replace("/<\/?[^>]+>/i",'',$value);
        }
        $this -> assign('my', $a);
		$this -> display('');
	} 
    public function readMy() {
		$map['username'] = session('username');
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['id|article'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		if (isset($_GET['level'])) {
			$searchkey = $_GET['level'];
			$map['level'] = $searchkey;
			$this -> assign('mylevel', $searchkey);
		} 
		$level = $this -> getLevel();
		$this -> assign('level', $level);
		$Fill = D('Examread');
		$count = $Fill -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Fill -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
		} 
        foreach($my as $key=>$value){              
            $a[$key] = preg_replace("/<\/?[^>]+>/i",'',$value);
        }
        $this -> assign('my', $a);
		$this -> display();
	} 
	public function insertFill() {
		$title = $_POST['title'];
		$answer = $_POST['answer'];
		$level = $_POST['level'];
		if (empty($title) || empty($answer) || empty($level)) {
			$this -> error('必填项不能为空');
		} 
        $pos = strpos($title, '__________');
        if($pos!==false){      
        }else{
            $this -> error('题目中未插入填空符');
        }
		$dao = D('Examfill');
		$map['title'] = $title;
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$this -> error('该题目已存在于数据库中，请勿重复录入');
		} 
		if ($dao -> create()) {
			$dao -> answer = trim($answer);
			$dao -> ctime = date("Y-m-d H:i:s");
			$dao -> username = session('username');
			$dao -> truename = session('truename');
			$insertID = $dao -> add();
			if ($insertID) {
				$this -> success('已成功保存');
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
    public function insertWrite() {
		$title = $_POST['title'];
		$answer = $_POST['answer'];
		$level = $_POST['level'];
		if (empty($title) || empty($level)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('Examwrite');
		$map['title'] = $title;
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$this -> error('该题目已存在于数据库中，请勿重复录入');
		} 
		if ($dao -> create()) {
			$dao -> ctime = date("Y-m-d H:i:s");
			$dao -> username = session('username');
			$dao -> truename = session('truename');
			$insertID = $dao -> add();
			if ($insertID) {
				$this -> success('已成功保存');
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
    public function updateFill() {
		$title = $_POST['title'];
		$answer = $_POST['answer'];
		$level = $_POST['level'];
		if (empty($title) || empty($answer) || empty($level)) {
			$this -> error('必填项不能为空');
		} 
        $pos = strpos($title, '__________');
        if($pos!==false){      
        }else{
            $this -> error('题目中未插入填空符');
        }
		$dao = D('Examfill');
		if ($dao -> create()) {
            $dao -> answer = trim($answer);
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
     public function updateWrite() {
		$title = $_POST['title'];
		$answer = $_POST['answer'];
		$level = $_POST['level'];
		if (empty($title) || empty($level)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('Examwrite');
		if ($dao -> create()) {
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
	public function editFill() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Examfill');
		$map['id'] = $id;
		$map['username'] = session('username');
		$my = $dao -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
            $this -> assign('level', $this -> getLevel());
            $this -> display();
		} else{
            $this -> error('该题目不存在或权限不足');
        }
	} 
    public function editWrite() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Examwrite');
		$map['id'] = $id;
		$map['username'] = session('username');
		$my = $dao -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
            $this -> assign('level', $this -> getLevel());
            $this -> display();
		} else{
            $this -> error('该题目不存在或权限不足');
        }
	} 
	public function viewFill() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Examfill');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		$this -> assign('my', $my);
		$this -> display();
	}
    public function viewWrite() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Examwrite');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		$this -> assign('my', $my);
		$this -> display();
	}
    public function delReadItem() {
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $map['id']=$id;
        $result=D('Examreaditem')->where($map)->delete();
        if($result>0){
            $this->success('已成功删除');
        }
	} 
    public function editReadArticle() {
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Examread');        
        $map['id']=$id;
        $map['username']=session('username');
        $my=$dao->where($map)->find();
        $this -> assign('level', $this -> getLevel());
        $this -> assign('my', $my);
		$this -> display('');
	} 
    public function updateReadArticle() {
        $article = $_POST['article'];
		$level = $_POST['level'];
		if (empty($article) ||empty($level)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('Examread');
		if ($dao -> create()) {
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
     public function editReadItem() {
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Examreaditem');        
        $map['id']=$id;
        $my=$dao->where($map)->find();
        $this -> assign('answer', $this -> getAnswer());
        $this -> assign('my', $my);
        $this -> display('');
	} 
    public function updateReadItem() {
        $title = $_POST['title'];
        $item = $_POST['item'];
        $answer = $_POST['answer'];
		if (empty($title) ||empty($item) ||empty($answer)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('Examreaditem');
		if ($dao -> create()) {
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
    public function cloze() {
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['id|title'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		if (isset($_GET['level'])) {
			$searchkey = $_GET['level'];
			$map['level'] = $searchkey;
			$this -> assign('mylevel', $searchkey);
		} 
		$level = $this -> getLevel();
		$this -> assign('level', $level);
		$Fill = D('Examcloze');
		$count = $Fill -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Fill -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> display();
	} 
    public function addCloze() {
		$this -> assign('level', $this -> getLevel());
		$this -> display();
	}
    public function insertCloze() {
		$article = $_POST['article'];
		$score = $_POST['score'];
		$level = $_POST['level'];
		if (empty($article) || empty($score) || empty($level)) {
			$this -> error('必填项不能为空');
		} 
        $pos = strpos($article, '____');
        if($pos!==false){      
        }else{
            $this -> error('题目中未插入填空符');
        }
		$dao = D('Examcloze');
		$map['article'] = $article;
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$this -> error('该题目已存在于数据库中，请勿重复录入');
		} 
		if ($dao -> create()) {		
			$dao -> username = session('username');
			$dao -> truename = session('truename');
            $dao -> ctime = date("Y-m-d H:i:s");
			$insertID = $dao -> add();
			if ($insertID) {
				$this -> ajaxReturn($insertID,'已成功保存！',1);
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	}
    public function addClozeNext() {
		$id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Examcloze');        
        $map['id']=$id;
        $my=$dao->where($map)->find();
        $dao_a=D('Examclozeitem');
        $map_a['articleid']=$id;
        $my_a=$dao_a->where($map_a)->order('id asc')->select();
        foreach($my_a as $key=>$value){
            $my_a[$key]['itemRadio']=$this->getItem($value['item'],$value['answer'],'read'.$value['id'],'<br />');
        }
        $this -> assign('answer', $this -> getAnswer());
        $this -> assign('my', $my);
        $this -> assign('my_a', $my_a);
		$this -> display('');
	} 
    public function insertClozeNext() {
		$articleid = $_POST['articleid'];
		$title = trim($_POST['title']);
		$item = $_POST['item'];
		$answer = $_POST['answer'];
		if (empty($title) ||empty($item)||empty($answer)) {
			$this -> error('必填项不能为空');
		} 
        $myitem = nl2br($item);
		$kkk = explode('<br />', $myitem);
		if (count($kkk) !== 4) {
			$this -> error('选项个数不为4！请确保每个选项1行，共4行。可能的原因：第5行有个回车');
		} 
		$dao = D('Examclozeitem');
		$map['articleid'] = $articleid;
		$map['title'] = $title;
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$this -> error('该题目已存在于数据库中，请勿重复录入');
		} 
		if ($dao -> create()) {
			$insertID = $dao -> add();
			if ($insertID) {
				$this -> success('已成功保存');
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
    public function clozeMy() {
		$map['username'] = session('username');
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['id|article'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		if (isset($_GET['level'])) {
			$searchkey = $_GET['level'];
			$map['level'] = $searchkey;
			$this -> assign('mylevel', $searchkey);
		} 
		$level = $this -> getLevel();
		$this -> assign('level', $level);
		$Cloze = D('Examcloze');
		$count = $Cloze -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Cloze -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> display('');
	} 
    public function viewCloze() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Examcloze');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
        $dao_a=D('Examclozeitem');
        $map_a['articleid']=$id;
        $my_a=$dao_a->where($map_a)->order('id asc')->select();
        foreach($my_a as $key=>$value){
            $my_a[$key]['itemRadio']=$this->getItem($value['item'],$value['answer'],'read'.$value['id'],'<br />');
        }
        $this -> assign('answer', $this -> getAnswer());
        $this -> assign('my', $my);
        $this -> assign('my_a', $my_a);
		$this -> display();
	}
    public function editCloze() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Examcloze');
		$map['id'] = $id;
		$map['username'] = session('username');
		$my = $dao -> where($map) -> find();
        
        $dao_a=D('Examclozeitem');
        $map_a['articleid']=$id;
        $my_a=$dao_a->where($map_a)->order('id asc')->select();
        foreach($my_a as $key=>$value){
            $my_a[$key]['itemRadio']=$this->getItem($value['item'],$value['answer'],'read'.$value['id'],'<br />');
        }
        $this -> assign('answer', $this -> getAnswer());
        $this -> assign('my', $my);
        $this -> assign('my_a', $my_a);
		$this -> display('');
// 		if ($my) {
// 			$this -> assign('my', $my);
//             $this -> assign('level', $this -> getLevel());
//             $this -> display();
// 		} else{
//             $this -> error('该题目不存在或权限不足');
//         }
	}
    public function updateCloze() {
		$article = $_POST['article'];
		$answer = $_POST['answer'];
		$level = $_POST['level'];
		if (empty($article) || empty($answer) || empty($level)) {
			$this -> error('必填项不能为空');
		} 
        $pos = strpos($article, '__________');
        if($pos!==false){      
        }else{
            $this -> error('题目中未插入填空符');
        }
		$dao = D('Examcloze');
		if ($dao -> create()) {
            $dao -> answer = trim($answer);
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
    public function editClozeArticle() {
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Examcloze');        
        $map['id']=$id;
        $map['username']=session('username');
        $my=$dao->where($map)->find();
        $this -> assign('level', $this -> getLevel());
        $this -> assign('my', $my);
		$this -> display('');
	} 
    public function updateClozeArticle() {
        $article = $_POST['article'];
		$level = $_POST['level'];
		if (empty($article) ||empty($level)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('Examcloze');
		if ($dao -> create()) {
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
     public function editClozeItem() {
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Examclozeitem');        
        $map['id']=$id;
        $my=$dao->where($map)->find();
        $this -> assign('answer', $this -> getAnswer());
        $this -> assign('my', $my);
        $this -> display('');
	} 
    public function updateClozeItem() {
        $title = $_POST['title'];
        $item = $_POST['item'];
        $answer = $_POST['answer'];
		if (empty($title) ||empty($item) ||empty($answer)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('Examclozeitem');
		if ($dao -> create()) {
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
    public function delClozeItem() {
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $map['id']=$id;
        $result=D('Examclozeitem')->where($map)->delete();
        if($result>0){
            $this->success('已成功删除');
        }
	} 
} 

?>