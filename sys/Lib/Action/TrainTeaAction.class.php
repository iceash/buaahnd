<?php
class TrainTeaAction extends CommonAction {
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
    
    public function getStuInfo(){
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $Enroll=D('Enroll');
        $map['id']=$id;
        $my=$Enroll->where($map)->select();
        if($my){
            $this->ajaxReturn($my,'',1);
        }else{
            $this->error('未找到记录');
        }
    }
    public function menuadd() {
        $menu['addToday']='今日新增记录';
        $menu['add']='新增学生';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function menustudent() {
        $menu['student']='全部学生';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function add(){
        $this->assign('teachers', $this->getTea());
        $this->assign('teacher_selected', session('truename').'-'.session('username'));
        $this->assign('project',$this -> getSystem("project"));
        $this->menuadd();
        $this->display();
    }
    public function addToday(){
        if (isset($_GET['searchkey'])) {
            $map['susername|struename'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        $dao = D('Train');
        $map['tusername'] =session('username'); 
        $map['ctime'] =array('gt',Date('Y-m-d'));
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
        $this->menuadd();
        $this->display();
    }
    public function addTodayDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('Train');
        $map['id'] = array('in', $id);
        $map['tusername'] =session('username'); 
        $map['ctime'] =array('gt',Date('Y-m-d'));
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function student(){
        if (isset($_GET['searchkey'])) {
            $map['susername|struename'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        $dao = D('Train');
        $map['tusername'] =session('username'); 
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
        $map['tusername'] =session('username'); 
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function checkAdd(){
        $dao=D('Train');
        $map['struename']=$_POST['struename'];
        $my=$dao->where($map)->find();
        if($my){
            $this->success('培训记录中已有此生记录，是否继续添加？');
        }else{
            $this->error('0');
        }
    }

    public function addInsert(){
        $dao = D('Train');
        $teacher=$_POST['ttruename'];
        $a = explode('-',$teacher);
        if ($dao -> create()) {
            $dao->ctime=date('Y-m-d H:i:s');
            $dao ->project=implode(',',$_POST['project']);
            $dao -> ttruename=$a[0];
            $dao -> tusername=$a[1];
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
    public function getTrain(){
        $train=D('Train');
        $map_a['struename']=$truename;
        $trainCount=$train->where($map_a)->select();
        if($trainCount){
            $this -> ajaxReturn($trainCount, '培训名单中已有该生记录！', 1);
        }
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
    public function auto3(){
        $id = $_GET['id'];
        if(!isset($id)){
            $this-error('参数缺失');
        }
        $dao=D('Enroll');
        $map['username'] = $id;
        $my = $dao->field('truename,mobile,fname,fmobile,mname,mmobile,oname,omobile')->where($map)->select();
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
        $map['tusername'] = session('username');
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
    public function test(){
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Train');
        $map['id']=$id;
        $map['tusername'] = session('username');
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
        $map['tusername']=session('username');
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