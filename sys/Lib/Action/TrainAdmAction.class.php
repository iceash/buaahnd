<?php
class TrainAdmAction extends CommonAction {
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
    public function sys() {
		$Sys = D("System");
		$map['category'] = 'exam_enroll';
		$count = $Sys -> where($map) -> count();
		if ($count > 0) {
			$my = $Sys -> where($map) -> order('id asc') -> select();
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
    public function computeSys() {
		$content = $_POST['content'];
        $aaa=explode(',',$content);
        $num=0;
		foreach($aaa as $key=>$value){
            $bbb=explode(':',$value);
            $num+=$bbb[1];
        }
		$this -> success('设定了'.$num.'道题目');
	} 
    public function exam() {
        $Exam = D('Exam');
        $map['type']='enroll';
        $testtime_all=$Exam ->field('testtime')-> where($map) -> group('testtime')->order('testtime desc')->select();
            $testtime_fortag=array();
            foreach($testtime_all as $key=>$value){
                date_Default_TimeZone_set("PRC");
                $temp=date('Y-m-d',strtotime($value['testtime']));
                $testtime_fortag[$temp]=$temp;
            }
        $this->assign('testtime_fortag',$testtime_fortag);
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['name'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
        if (isset($_GET['date'])) {
			$searchkey = $_GET['date'];
			$map['testtime'] = $searchkey;
			$this -> assign('testtime_current', $searchkey);
		}
		$count = $Exam -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $Exam -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('testtime desc,id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> display('');
	} 
    public function getRule(){
        $dao= D('Examrule');
        $map['category']='enrolltest';
        $my=$dao->where($map)->select();
        $rule=array();
        foreach($my as $key=>$value){
            $rule[$value['id']]=$value['name'];
        }
        return $rule;
    }
    public function addExam() {
        $this->assign('rule',$this->getRule());
		$this -> display();
	} 
    public function insertExam() {
		$testtime = $_POST['testtime'];
		$name = $_POST['name'];
		$mobile = $_POST['mobile'];
		$code = $_POST['code'];
        $ruleid = $_POST['ruleid'];
		if (empty($testtime) || empty($name) || empty($mobile)|| empty($code)|| empty($ruleid)) {
			$this -> error('必填项不能为空');
		} 
		$dao=D('Exam');
		if ($dao -> create()) {
            $dao ->type='enroll';
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
    public function delExam() {
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $result=D('Exam')->where("subtime is null and id=" . $id)->delete();
        if($result>0){
            $this->success('已成功删除');
        }else{
            $this->success('已提交试卷，无法删除');
        }
	} 
     public function viewExam() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Exam');
		$map['id']=$id;
        $my=$dao->where($map)->find();
        $this -> assign('my', $my);
        $this->assign('rule',$this->getRule());
		$this -> display('');
	} 
    public function updateExam() {
		$pscore = $_POST['pscore'];
		if (strlen($pscore)<1) {
			$this -> error('请填写分数');
		} 
		$dao = D('Exam');
        $count=$dao->where("mscore is not null and id=" . $_POST['id'])->find();
        if($count){
            $mscore=$count['mscore'];
        }else{
            $this -> error('机改分出来后，才可录入作文分');
        }
		if ($dao -> create()) {
            $dao -> score =$pscore+$mscore;
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
    public function menurule() {
        $menu['rule']='所有规则';
        $menu['ruleAdd']='新建规则';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function rule() {
        if (isset($_GET['searchkey'])) {
            $map['name'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        $dao = D('Examrule');
        $map['category'] ='enrolltest';
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
        $this -> menurule();
        $this -> display();
    } 
    public function ruleAdd() {
        $this -> menurule();
        $this -> display();
    } 
    public function ruleDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('Examrule');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function ruleEdit() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Examrule');
        $map['id'] = $id;
        $map['category'] ='enrolltest';
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> menurule();
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function ruleUpdate() {
        $dao = D('Examrule');
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
    public function ruleInsert() {
        $dao = D('Examrule');
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
    public function menustudent() {
        $menu['student']='全部学生';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function student(){
        if (isset($_GET['searchkey'])) {
            $map['susername|struename'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['username'])) {
            $map['tusername'] =  $_GET['username'];
            $this -> assign('username', $_GET['username']);
        } 
        if (isset($_GET['truename'])) {
            $this -> assign('truename', $_GET['truename']);
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
        $this->menustudent();
        $this->display();
    }
    public function studentDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Train');
        $map['id'] = array('in', $id);
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function getHND(){
        $truename=$_POST['struename'];
        if(!isset($truename)){
            $this->error('参数缺失');
        }
        $dao=D('User');
        $map['truename']=$truename;
        $my=$dao->where($map)->select();
        $this->assign('my',$my);
        $this->assign('count',count($my));
        $this->display();
    }
    public function auto1(){
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('User');
        $map['username']=$id;
        $my=$dao->field('username')->where($map)->find();
        if($my){
            $this->ajaxReturn($my,'',1);
        }else{
            $this->error('未找到记录');
        }
    }
    public function auto2(){
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('StudentDirView');
        $map['student']=$id;
        $my=$dao->field('truename')->where($map)->find();
        if($my){
            $this->ajaxReturn($my,'',1);
        }else{
            $this->error('未找到记录');
        }
    }
    public function detail(){
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Train');
        $map['id']=$id;
        $my=$dao->where($map)->find();
        $this->assign('teacher_selected',$my['ttruename'].'-'.$my['tusername']);
        $this->assign('teachers', $this->getTea());
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
    public function change(){
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Train');
        $map['id']=$id;
        $my=$dao->where($map)->find();
        if($my){
            $this -> assign('my', $my);
			$User = D('User');
			$map_a['role'] = array('like','%TrainTea%');
			$map_a['ispermit'] = '1';
			$map_a['username'] = array('neq', $my['tusername']);
			$teacher = $User -> Field('username,truename') -> where($map_a) -> order('username asc') -> select();
            
            $a = array();
			foreach($teacher as $key => $value) {
				$map_b['tusername'] = $value['username'];
				$count = $dao -> where($map_b)-> count();
				$a[$value['truename'].'-'.$value['username']] = $value['truename'] . '（' . $count . '人）';
			} 
			$this -> assign('a', $a);
        }else {
			$this -> error('未找到该学生或对该学生没有操作权限');
		} 
		$this -> display();
    }
    public function changeTeacher(){
        $teacher=$_POST['teacher'];
        $id = $_POST['id'];
        if (empty($teacher)) {
			$this -> error('请选择招生咨询老师');
		} 
        $dao = D('Train');
        $a = explode('-',$teacher);
        $data['tusername'] = $a[1];
        $data['ttruename'] = $a[0];
        $data['id'] = $id;
        $result = $dao -> save($data);

        if($result>0){
            $this->success('转移成功');
        }else{
            $this->error('转移失败');
        }
    }
    public function test(){//培训之平时测试
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Train');
        $map['id']=$id;
        $my=$dao->where($map)->find();
        if($my){
            $this->assign('my',$my);
            $test=D('Traintest')->where("trainid=$id")->order('traintime desc')->select();
            $this -> assign('test', $test);
            $this->display();
        }else{
            $this->error('未找到记录或权限不足');
        }
    }
    public function getTea(){
        $dao = D("User");
        $map['ispermit']=1;
        $map['role']=array('like','%TrainTea%');
        $my=$dao->Field('username,truename')->where($map)->select();
        foreach($my as $key => $value) {
			$a[$value['truename'].'-'.$value['username']] = $value['truename'].'('.$value['username'].')';
		}
        return $a;
    }
    public function getSystem($name) {
		$system = D("System");
		$temp = explode(',', $system -> where("category='train' and name='" . $name . "'") -> getField("content"));
		$a = array();
		foreach($temp as $key => $value) {
			$a[$value] = $value;
		} 
		return $a;
	} 
    public function getTrainTea() {
		$dao = D("User");
        $map['ispermit']=1;
        $map['role']=array('like','%TrainTea%');
        $my=$dao->Field('username,truename')->where($map)->select();
		$a = array();
		foreach($my as $key => $value) {
			$a[$value['username']] = $value['truename'];
		} 
		return $a;
	} 
    public function menuteacher() {
        $menu['teacher']='所有教师';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function teacher() {
		$dao = D("User");
        $map['ispermit']=1;
        $map['role']=array('like','%TrainTea%');
        $my=$dao->Field('username,truename')->where($map)->select();
		
        $dao2 = D("Train");
		foreach($my as $key => $value) {
            $map2['tusername']=$value['username'];
			$my[$key] ['count']= $dao2->field('id')->where($map2)->count();
		} 
		$this->assign('my',$my);
		$this->menuteacher();
        $this->display();
	} 
    public function detailUpdate() {
        $dao = D('Train');
        $teacher=$_POST['ttruename'];
        $a = explode('-',$teacher);
        if ($dao -> create()) {
            $dao ->project=implode(',',$_POST['project']);
            $dao -> ttruename=$a[0];
            $dao -> tusername=$a[1];
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
    public function testInsert() {
		$dao = D("Traintest");
		if ($dao -> create()) {
			$checked = $dao -> add();
			if ($checked > 0) {
				$this -> success('已成功保存');
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error($dao -> getError());
		} 
	} 
    public function testDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = $id;
        $dao = D('Traintest');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function testUpdate() { 
		$dao = D('Traintest');
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
    public function downStu(){
        $dao = D('Train');
        $my = $dao -> where($map) -> order('ctime desc') -> select();
        include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("NJU-HND auto generate Document");
        $a=array();
        $a['A']='姓名';
        $a['B']='学号';
        $a['C']='培训项目';
        $a['D']='培训费收据号';
        $a['E']='培训费用';
        $a['F']='入库时间';
        foreach($a as $key=>$value){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($key.'1', $value);
        }
        foreach($my as $my_key=>$my_value){
                $temp=$my_key+2;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$temp, $my_value['struename'])
                    ->setCellValue('B'.$temp, $my_value['susername'])
                    ->setCellValue('C'.$temp, $my_value['project'])
                    ->setCellValue('D'.$temp, $my_value['receipt'])
                    ->setCellValue('E'.$temp, $my_value['fee'])
                    ->setCellValue('F'.$temp, $my_value['ctime']);
        }
        $objPHPExcel-> getActiveSheet() -> getColumnDimension('A') -> setwidth(15);
        $objPHPExcel-> getActiveSheet() -> getColumnDimension('B') -> setwidth(15);
        $objPHPExcel-> getActiveSheet() -> getColumnDimension('C') -> setwidth(15);
        $objPHPExcel-> getActiveSheet() -> getColumnDimension('D') -> setwidth(15);
        $objPHPExcel-> getActiveSheet() -> getColumnDimension('E') -> setwidth(15);
        $objPHPExcel-> getActiveSheet() -> getColumnDimension('F') -> setwidth(25);

        $objPHPExcel->setActiveSheetIndex(0);

        $filename='培训学生名单['.session('truename').'].xls';
        $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
} 

?>