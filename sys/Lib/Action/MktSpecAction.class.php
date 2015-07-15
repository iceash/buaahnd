<?php
class MktSpecAction extends CommonAction {
    
    public function getStuName() {
		return session('username');
	} 
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
    
    public function menuRanking() {
        $menu['edit']='我录入的问卷';
        $menu['itemAdd']='新建题目';
        $menu['themeAdd']='新建主题';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
        
    //问卷编辑页面
    public function edit() {
        if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
			$map['id|theme'] =array('like','%'.$searchkey.'%');
			$this->assign('searchkey',$searchkey);
        }
		$Theme = D("Theme");
		$map['inputerId']=session('username');
		$count = $Theme -> where($map) -> count();
		if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
			$p = new Page($count, $listRows);
			$my = $Theme -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('inputime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);			
		}
        $this ->menuRanking();        
        $this -> display();
	}
    
    //问卷调查页面
    public function survey() {
        if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
			$map['id|theme'] =array('like','%'.$searchkey.'%');
			$this->assign('searchkey',$searchkey);
        }
		$Theme = D("Theme");
		$count = $Theme -> where($map) -> count();
		if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
			$p = new Page($count, $listRows);
			$my = $Theme ->limit($p -> firstRow . ',' . $p -> listRows) -> order('inputime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);			
		}
        $this -> assign("menu","所有问卷调查");        
        $this -> display();
	}
    
    //问卷调查预览
    public function preview(){
        $id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('theme');
		$map['id'] = $id;
		$my = $dao -> field("theme") -> where($map) -> find();
        $item = D("item");
        $map2['themeId'] = $id;
        $my2 = $item -> where($map2) ->select();
        $i=0;
		foreach($my2 as $value){
            $my2[$i]['options']  = $this -> getItem($my2[$i]['options'],$my2[$i]['type'],"<br />");
            $i++;
        }
        $this -> assign('my', $my);    
        $this -> assign('my2', $my2);
        $this -> display();
    }
    //点击修改弹出层内容获取
    public function themeMore() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('theme');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
            $this -> assign('my', $my);
            $this -> display();
		} else{
            $this -> error('该记录不存在');
        } 
	}
    
    //保存修改主题
    public function themeUpdate() {
        $theme = $_POST['theme'];
        $id = $_POST['themeid'];
        if (empty($theme) || empty($id)) {
            $this -> error('必填项不能为空');
        } 
        $dao = D('Theme');
        $data['theme']=$theme;
        $data['id']=$id;
        if ($dao -> create()) {
            $checked = $dao -> save($data);
            if ($checked > 0) {
                $this -> success('已成功保存');
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error('插入数据出错');
        } 
	}
    
    //新建主题
    public function themeInsert(){
        $theme = $_POST['theme'];
        $beginTime = $_POST['beginTime'];
        $endTime = $_POST['endTime'];
        if (empty($theme)||empty($beginTime)||empty($endTime)){
            $this -> error('必填项不能为空');
        } 
        $dao=D('theme');
        if ($dao -> create()) {
            $dao -> inputerId = session('username');
            $dao -> inputer = session("truename");
            $dao -> inputime = date("Y-m-d H:i:s");
			$insertID = $dao -> add($data);
			if ($insertID) {
				$this -> success("已经成功添加");
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
    }
    public function themeDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('theme');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    
    public function itemShow(){
        $themeId = $_GET['id'];
		if (!isset($themeId)) {
			$this -> error('参数缺失');
		} 
		if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
			$map['id|item'] =array('like','%'.$searchkey.'%');
			$this->assign('searchkey',$searchkey);
        }
         if (isset($_GET['category'])) {
            if($_GET['category']=="单选"){
                $map['type'] = 1;
            } else if($_GET['category']=="多选"){
                $map['type'] = 2;
            }else{
                $map['type'] =3;
            }
			$this -> assign('category_current', $_GET['category']);
		}
		$Item = D("Item");
		$map['themeId']=$themeId;
		$count = $Item -> where($map) -> count();
		if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
			$p = new Page($count, $listRows);
			$my = $Item -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('inputime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
            $this -> assign("themeId",$themeId);
            $this->assign('category_fortag',$this -> getSystem("itemtype"));
			$this -> assign('my', $my);			
		}
        $this ->menuRanking();        
        $this -> display();
    }
    
    public function itemDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('item');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function itemMore(){
        $id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('ItemView');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
        $type = $my["type"];
		if ($my) {
            $my["options"] = $this -> getItem($my['options'],$type,"<br />");
            $this -> assign('my', $my);
            $this -> display();
		} else{
            $this -> error('该记录不存在');
        } 
    }
     public function getItem($item,$type,$separator='') {
		$a = array();
        $kkk = explode('<br />', nl2br($item));
        $parseStr   = '';
		foreach($kkk as $key => $value) {
            if($type==1){//单选
               $parseStr .= '<label><input type="radio" disabled>'.$value.'</label>;'.$separator; 
            }else if($type==2){//多选
                $parseStr .= '<label><input type="checkbox">'.$value.'</label>'.$separator;
            }else{
                $parseStr="";
            }
		} 
        return $parseStr;
	} 
    
    //点击修改弹出层内容获取
    public function itemDisplay() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('item');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
            $this -> assign('my', $my);
            $this -> display();
		} else{
            $this -> error('该记录不存在');
        } 
	}
    
    //保存修改题目
    public function itemUpdate() {
        $item = $_POST['item'];
        if (empty($item)) {
            $this -> error('必填项不能为空');
        } 
        $dao = D('item');
        $data['item']=$item;
        $data['id']=$id;
        if ($dao -> create()) {
            $checked = $dao -> save($data);
            if ($checked > 0) {
                $this -> success('已成功保存');
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error('插入数据出错');
        } 
	}
    
    public function themeAdd(){
        $this->menuRanking();
        $this -> display();
    }
    
     public function itemAdd(){
        $dao = D("theme");
        $theme = $dao ->field("id,theme") ->order('inputime desc')->select();
        $this->assign('category_fortag',$this -> getSystem("itemtype"));
        $dao_class = D('Class');
        $map_class['isbiye']=0;
        $dtree_class = $dao_class->where($map_class)->order('year desc,name asc')-> select();
        $dtree_year=$dao_class ->where($map_class)->field('year')-> group('year')->order('year desc')->select();
        $dao2 = D('ClassstudentView');
        $dtree_stu = $dao2->order('student asc')-> select();
        foreach($dtree_stu as $value){
            $dtree_stu["parent"] = "JZ".substr($dtree_stu["student"],1,0);
            $dtree_stu["pname"] = $dtree_stu["name"]."的家长";
        }
        $dao_tea = D("TeacherView");
        $all_tea = $dao_tea->field("teacher,truename")->group("truename")->select();
        $theme_all = array();
        $i=0;
        foreach($theme as $value){
            $theme_all[$theme[$i]['id']] = $theme[$i]['theme']; 
            $i++;
        }
        $this -> assign('theme_all', $theme_all);
        $this -> assign('theme',$theme);
        $this->assign('dtree_year',$dtree_year);
        $this -> assign('dtree_class', $dtree_class);
        $this -> assign('dtree_stu', $dtree_stu);
        $this -> assign('teacher',$all_tea);
        $this->menuRanking();
        $this -> display();
    }
    
    //新建题目
    public function itemInsert(){
        $themeId = $_POST['themeId'];
        $item = $_POST['item'];
        $type = $_POST['type'];
        if($type=="单选"){
            $type=1;
        }else if($type=="多选"){
            $type=2;
        }else{
            $type=3;
        }
        $options = $_POST['options'];
        $beginTime = $_POST['beginTime'];
        $endTime = $_POST['endTime'];
        if($type==3){
            if (empty($themeId)||empty($item)||empty($type)||empty($beginTime)||empty($endTime)) {
                $this -> error('必填项不能为空');
            } 
        }else{
            if (empty($themeId)||empty($item)||empty($type)||empty($options)||empty($beginTime)||empty($endTime)) {
            $this -> error('必填项不能为空');
            } 
            $myoptions = nl2br($options);
            $kkk = explode('<br />', $myoptions);
            if (count($kkk) !== 4) {
                $this -> error('选项个数不为4！请确保每个选项1行，共4行。可能的原因：第5行有个回车');
		} 
        }
        
        $dao=D('item');
        if ($dao -> create()) {
			$dao -> inputime = date("Y-m-d H:i:s");
			$dao -> inputId = session('username');
			$dao -> inputer = session('truename');
            $dao -> type = $type;
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
    
    public function insertTarget(){
        $target=$_GET['targetId'];
        $themeId=$_GET['themeId'];
        $target_array = array();
        $target_array = explode(",",$target);
        $dao = D("themetarget");
        $data['themeId'] = $themeId;
        foreach($target_array as $value){
            $data['targetId']=$value;
            $insertID = $dao->add($data);
        }
        if ($insertID) {
            $this -> success('已成功保存');
        } else {
            $this -> error('没有更新任何数据');
        } 
        
    }
    public function autoMenu($menu,$id='') {
		$path = array();
		foreach($menu as $key => $value) {
			$is_on = ($key == ACTION_NAME)?' class="on" ':'';
            $url_plus=empty($id)?'':'/id/'.$id;
			$path[] = '<a href="__URL__/' . $key.$url_plus . '" ' . $is_on . '>' . $value . '</a>';
		} 
		return implode(' | ', $path);
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
        
    //问卷列表
    public function themeList() {
        if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
			$map['id|theme'] =array('like','%'.$searchkey.'%');
			$this->assign('searchkey',$searchkey);
        }
		$Theme = D("Theme");
		$count = $Theme -> count();
		if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
			$p = new Page($count, $listRows);
			$my = $Theme ->limit($p -> firstRow . ',' . $p -> listRows) -> order('inputime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);			
		}
        $this -> assign("menu","问卷列表首页");        
        $this -> display();
	}
    //问卷统计
    public function result() {
        if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
			$map['id|theme'] =array('like','%'.$searchkey.'%');
			$this->assign('searchkey',$searchkey);
        }
		$Theme = D("Theme");
		$count = $Theme -> count();
		if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
			$p = new Page($count, $listRows);
			$my = $Theme ->limit($p -> firstRow . ',' . $p -> listRows) -> order('inputime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);			
		}
        $this -> assign("menu","问卷统计首页");        
        $this -> display();
	}
} 
    


?>