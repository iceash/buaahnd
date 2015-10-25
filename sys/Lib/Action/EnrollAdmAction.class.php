<?php
class EnrollAdmAction extends CommonAction {
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
	public function ampieData() {
		$enroll = D('Enroll');
		for($i = 0;$i < 6;$i++) {
			$map['status'] = "$i";
			if ($i == 5) {
				$map['status'] = 'z';
			} 
            $current = @date("Y-m-d",time());
            $current_year = @date("Y",time());
            $pyear = $current_year-1;
            if($current > $current_year.'-10-31'){
                $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
            } else{
                $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
            }
			$a[$i] = $enroll -> Field('id') -> where($map) -> count();
		} 
		$this -> assign('a', $a);
		$this -> display();
	} 
    public function menustatus() {
    $menu['status']='招生进程图';
    $menu['category/statusid/5']='尚未分配';
    $menu['category/statusid/0']='新分配';
    $menu['category/statusid/1']='正在咨询';
    $menu['category/statusid/2']='已交报名费';
    $menu['category/statusid/3']='已录取';
    $menu['category/statusid/4']='不录取';
    $menu['search']='搜索';
    $menu['toExcel']='导出数据';
    $menu['']='返回';
    $this->assign('menu',$this ->autoMenu($menu));  
    }
	public function status() {
		$this -> menustatus();
		$this -> display();
	} 
	public function setTeacher() {
		if (empty($_POST['teacher'])) {
			$this -> error('请选择招生咨询老师');
		} 
		$teacher = $_POST['teacher'];
        $ids=explode(',',$_POST['enrollid']);
        $i=0;
        foreach($ids as $enrollid){
            $data['id'] = $enrollid;
            $data['counselor'] = $teacher;
            $data['status'] = '0';
            $data['statustext'] = '新分配';
            $map['username'] = $teacher;
            $truename = D('User') -> where($map) -> getField("truename");
            $data['counselorname'] = $truename;
            $reslut = D('Enroll') -> save($data);
            if ($reslut > 0) {
                $data_a['enrollid'] = $enrollid;
                $data_a['counselor'] = session('username');
                $data_a['counselorname'] = session('truename');
                $data_a['content'] = '新分配给'.$truename;
                $data_a['ctime'] = date("Y-m-d H:i:s");
                $insertID = D('Enrollrecord') -> add($data_a);
                if ($insertID) {
                    $i++;
                } 
            } 
        }
        if($i==count($ids)){
            $this->success('已成功分配');
        }else{
            $this->error('分配出错，请刷新页面再试一次');
        }
		
	} 
	public function start() {
		$id = $_GET['enrollid'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$Enroll = D('Enroll');
		$map['id'] = array('in',$id);
		$my = $Enroll -> where($map) -> select();
		if ($my) {
			$this -> assign('my', $my);
			$User = D('User');
			$map_a['role'] = array('like','%EnrollTea%');
			$map_a['ispermit'] = '1';
			$teacher = $User -> Field('username,truename') -> where($map_a) -> order('username asc') -> select();
			$a = array();
			foreach($teacher as $key => $value) {
                $current = @date("Y-m-d",time());
                $current_year = @date("Y",time());
                $pyear = $current_year-1;
                if($current > $current_year.'-10-31'){
                    $map_b['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
                } else{
                    $map_b['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
                }
				$map_b['counselor'] = $value['username'];
				$count = $Enroll -> where($map_b) -> count();
				$a[$value['username']] = $value['truename'] . '（' . $count . '人）';
			} 
			$this -> assign('enrollid', $id);
			$this -> assign('a', $a);
		} else {
			$this -> error('未找到该学生或对该学生没有操作权限');
		} 
		$this -> display();
	} 
    public function getStatus(){
        $a=array();
        $a['x']='新分配';
        $a['1']='正在咨询';
        $a['2']='已交报名费';
        $a['3']='已录取';
        $a['4']='不录取';
        $a['z']='新录入';
        return $a;
    }
    public function search() {
		$truename = $_GET['truename'];
        $mobile = $_GET['mobile'];
		$schoolname = $_GET['schoolname'];
		$school = $_GET['school'];
		$status = $_GET['status'];
		$teacher = $_GET['teacher'];
		if (isset($truename)) {
			$map['truename']=array('like',"%".$truename."%");
            $this -> assign("truename", $truename);
		}
        if (isset($mobile)) {
			$map['mobile']=array('like',"%$mobile%");
            $this -> assign("mobile", $mobile);
		}         
        if (isset($schoolname)) {
			$map['schoolname']=array('like',"%".$schoolname."%");
            $this -> assign("schoolname", $schoolname);
		} 
        if (isset($school)) {
            $this -> assign("city_select", $school);
            switch ($school) {
                case '安徽': 
                    $map['schoolprovince']=$school;
                    break;
                case '浙江': 
                    $map['schoolprovince']=$school;
                    break; 
                case '其它省或信息不全': 
                    $map['schoolprovince']=array('not in','江苏,安徽,浙江');
                    break;
                default:
                    $map['schoolcity']=$school;
            }
		} 
        $city_fortag=array('南京'=>'南京',
        '苏州'=>'苏州',
        '无锡'=>'无锡',
        '常州'=>'常州',
        '淮安'=>'淮安',
        '连云港'=>'连云港',
        '南通'=>'南通',
        '宿迁'=>'宿迁',
        '泰州'=>'泰州',
        '徐州'=>'徐州',
        '盐城'=>'盐城',
        '扬州'=>'扬州',
        '镇江'=>'镇江',
        '安徽'=>'安徽',
        '浙江'=>'浙江',
        '其它省或信息不全'=>'其它省或信息不全');
        $this -> assign("city_fortag", $city_fortag);
        
         //显示状态
        $all_status=$this->getStatus();
        $this -> assign('all_status', $all_status);

        if (isset($status)) {
            if($status == 'x'){
                $map['status'] = '0';
            }else{
                $map['status'] = $status;
            }
            			
            $this -> assign("status_select", $status);
		} 
        
        //显示咨询老师
        $User = D('Enroll');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map_a['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }
		$counselor = $User -> Distinct(true) -> Field('counselor,counselorname') -> where($map_a) -> order('counselor asc') -> select();
        $all_teacher=array();
        foreach($counselor as  $value) {
            if($value['counselorname'] != NULL){
                $all_teacher[$value['counselor']] = $value['counselorname'];
            }
        }
        $this -> assign('all_teacher', $all_teacher);
     
        if (isset($teacher)) {
			$map['counselor'] = $teacher;
            $this -> assign("teacher_select", $teacher);
		} 
            $enroll = D('Enroll');
            if($current > $current_year.'-10-31'){
                $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
            } else{
                $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
            }
            $count = $enroll -> where($map) -> count();
            if($count>0){
                $enrollRecord=D('EnrollRecordView');
                $dao=D('enrollrecord');
                $query='select max(id) as mid from u_enrollrecord group by enrollid';
                $ids=$dao->query($query);            
                $id_array=array();
                foreach($ids as $v){
                    $id_array[]= $v['mid'];
                }
                        
                $map['rid'] =array('in',$id_array);
                import("@.ORG.Page");
                $listRows = 10;
                $p = new Page($count, $listRows);
                $my = $enrollRecord -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('rtime desc') -> select();
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
            }
            $this->menustatus();
        $this -> display();
	}
	public function changeTeacher() {
		if (empty($_POST['teacher'])) {
			$this -> error('请选择招生咨询老师');
		} 
		$teacher = $_POST['teacher'];
		$enrollid = $_POST['enrollid'];
		$oldteacher = $_POST['oldteacher'];
		$oldteachername = $_POST['oldteachername'];
		$data['id'] = $enrollid;
		$data['counselor'] = $teacher;
		$map['username'] = $teacher;
		$truename = D('User') -> where($map) -> getField("truename");
		$data['counselorname'] = $truename;
		$data['status'] = '0';
		$data['statustext'] = '新分配';
		$reslut = D('Enroll') -> save($data);
		if ($reslut > 0) {
			$data_a['enrollid'] = $enrollid;
			$data_a['counselor'] = session('username');
			$data_a['counselorname'] = session('truename');
			$data_a['content'] = '从' . $oldteachername . '转移到' . $truename;
			$data_a['ctime'] = date("Y-m-d H:i:s");
			$insertID_a = D('Enrollrecord') -> add($data_a);
			$data_b['enrollid'] = $enrollid;
			$data_b['fromcounselor'] = $oldteacher;
			$data_b['fromcounselorname'] = $oldteachername;
			$data_b['tocounselor'] = $teacher;
			$data_b['tocounselorname'] = $truename;
			$data_b['ctime'] = date("Y-m-d H:i:s");
			$insertID_b = D('Enrollchange') -> add($data_b);
			$this -> success('已成功转移');
		} else {
			$this -> error('影响的数据行数为0');
		} 
	} 
	public function change() {
		$id = $_GET['enrollid'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$Enroll = D('Enroll');
		$map['id'] = $id;
		$my = $Enroll -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
			$User = D('User');
			$map_a['role'] = array('like','%EnrollTea%');
			$map_a['ispermit'] = '1';
			$map_a['username'] = array('neq', $my['counselor']);
			$teacher = $User -> Field('username,truename') -> where($map_a) -> order('username asc') -> select();

			$a = array();
			foreach($teacher as $key => $value) {
                $current = @date("Y-m-d",time());
                $current_year = @date("Y",time());
                $pyear = $current_year-1;
                if($current > $current_year.'-10-31'){
                    $map_b['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
                } else{
                    $map_b['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
                }
				$map_b['counselor'] = $value['username'];
				$count = $Enroll -> where($map_b)-> count();
				$a[$value['username']] = $value['truename'] . '（' . $count . '人）';
			} 
			$this -> assign('a', $a);
		} else {
			$this -> error('未找到该学生或对该学生没有操作权限');
		} 
		$this -> display();
	} 
	public function del() {
		$id = $_GET['enrollid'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$Enroll = D('Enroll');
		$map['id'] = $id;
		$map['status'] = 'z';
		$reslut = $Enroll -> where($map) -> delete();
		if ($reslut > 0) {
			$this -> success('已成功删除');
		} else {
			$this -> error('该记录不存在或已删除');
		} 
	} 
	public function getSystem($name) {
		$system = D("System");
		$temp = explode(',', $system -> where("category='enroll' and  name='" . $name . "'") -> getField("content"));
		$a = array();
		foreach($temp as $key => $value) {
			$a[$value] = $value;
		} 
		return $a;
	} 
	public function student() {
		$area = D("Area");
		$province = $area -> where("parent_id = 1") -> Field("region_name") -> select();
		$a = array();
		foreach($province as $key => $value) {
			$a[$value['region_name']] = $value['region_name'];
		} 
		$is_or_not = array('是' => '是', '否' => '否');
		$education = $this -> getSystem("education");
		$entrancefull = $this -> getSystem("entrancefull");
		$englishfull = $this -> getSystem("englishfull");
		$mathfull = $this -> getSystem("mathfull");
		$abroad = $this -> getSystem("abroad");
		$coursewant = $this -> getSystem("coursewant");
		$englishtrain = $this -> getSystem("englishtrain");
		$sourcenewspaper = $this -> getSystem("sourcenewspaper");
		$sourcenet = $this -> getSystem("sourcenet");
		$nationality = $this -> getSystem("nationality");

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
		$id = $_GET['enrollid'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$Enroll = D('Enroll');
		$map['id'] = $id;
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
			$this -> assign('my', $my);
			$this -> display();
		} else {
			$this -> error('未找到该学生或对该学生没有操作权限');
		} 
	} 
	public function insertPlus() {
		$dao = D("Enroll");
		if ($dao -> create()) {
			$dao -> abroad=implode(',', $_POST['abroad']);
			$dao -> infosource = implode(',', $_POST['infosource']);
			$dao -> sourcenewspaper = implode(',', $_POST['sourcenewspaper']);
			$dao -> sourcenet = implode(',', $_POST['sourcenet']);
            $checked = $dao->save(); 
			if ($checked) {
                $this -> success('已成功保存');
			} else{
                $this -> error('没有更新任何数据');
            }
		} else {
			$this -> error($dao -> getError());
		} 
	} 
	public function checkEnrollPlus() {
		load("@.idcard");
		load("@.check");
		if (empty($_POST['truename'])) {
			$this -> error('Part1 学生姓名不能为空');
		} 
		if (empty($_POST['sex'])) {
			$this -> error('Part1 性别不能为空');
		} elseif (!issex($_POST['sex'])) {
            $this -> error('第'.$i.'行 性别校验失败');
        }   
//         if (empty($_POST['birthday'])) {
// 			$this -> error('Part1 出生日期不能为空');
// 		} 
		if (empty($_POST['mobile'])) {
			$this -> error('Part1 学生手机号不能为空');
		} elseif ((ismobile($_POST['mobile'])||($_POST['mobile']=='00000000000'))==false) {
			$this -> error('Part1 学生手机号校验失败');
		} 
        
        //姓名和手机号都不能重复
        $dao = D("Enroll");
        $current = @date("Y-m-d",time());
            $current_year = @date("Y",time());
            $pyear = $current_year-1;
            if($current > $current_year.'-10-31'){
                $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
            } else{
                $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
            } //只需交验当年的记录
        if($_POST['mobile']!=='00000000000'){
            $map['_query'] = 'mobile='.$_POST['mobile'].'&truename='.$_POST['truename'].'&_logic=or';
            $map['id'] = array('neq',$_POST['id']);
            $isHava = $dao -> where($map) -> select();
            if ($isHava) {
                $this -> error("库中已有相同姓名或手机号，无法录入! 姓名:" . $isHava[0]['truename'] . " 手机号:" . $isHava[0]['mobile'] . " 时间：" . $isHava[0]['ctime'] . " 填表人：" . $isHava[0]['fill']);
            } 
        }else{
            $map['truename'] = $_POST['truename'];
            $map['id'] = array('neq',$_POST['id']);
            $isHava = $dao -> where($map) -> select();
            if ($isHava) {
                $this -> error("库中已有相同姓名，无法录入! 姓名:" . $isHava[0]['truename'] . " 时间：" . $isHava[0]['ctime'] . " 填表人：" . $isHava[0]['fill']);
            } 
        }
        
        if (!empty($_POST['qq'])) {
            if (!is_numeric($_POST['qq'])){
				$this -> error('Part1 学生QQ号码校验失败');
            }
		} 
		if (!empty($_POST['email'])) {
            if (!isemail($_POST['email'])){
				$this -> error('Part1 学生Email校验失败');
            }
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
        if (empty($_POST['schoolprovince'])||empty($_POST['schoolcity'])||empty($_POST['schoolname'])) {
			$this -> error('Part3 毕业学校必填');
		}
		$this -> success('数据校验成功');
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
	public function getBrotherCity($cityname) {
		$Area = D("Area");
		$map['region_name'] = $cityname;
		$map['region_type'] = 2;
		$parent = $Area -> field('parent_id') -> where($map) -> find();
		$map_a['parent_id'] = $parent['parent_id'];
		$brother = $Area -> where($map_a) -> select();
		$a = array();
		foreach($brother as $key => $value) {
			$a[$value['region_name']] = $value['region_name'];
		} 
		return $a;
	}
    public function menuteacher() {
    $menu['teacher']='咨询老师';
    $menu['fill']='填表人';
    $menu['school']='高中所在地区';
    $menu['']='返回';
    $this->assign('menu',$this ->autoMenu($menu));  
    }
	public function teacher() {
		$User = D('User');
		$map['role'] = array('like','%EnrollTea%');
		$map['ispermit'] = '1';
		$my = $User -> Field('username,truename') -> where($map) -> order('username asc') -> select();
		$a = array();
		$Enroll = D('Enroll');
		$Enrollchange = D('Enrollchange');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
            
		if ($my) {
			foreach($my as $key => $value) {
				$a[$key]['username'] = $value['username'];
				$a[$key]['truename'] = $value['truename'];
				$map_a['counselor'] = $value['username'];
                if($current > $current_year.'-10-31'){
                    $map_a['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
                } else{
                    $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
                }
				for($i = 0;$i < 5;$i++) {
					$map_a['status'] = $i;
					$a[$key]['status' . $i] = $Enroll -> where($map_a) -> count();
				} 
				$map_b['fromcounselor'] = $value['username'];
                if($current > $current_year.'-10-31'){
                    $map_b['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
                } else{
                    $map_b['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
                }
				$a[$key]['status5'] = $Enrollchange -> where($map_b) -> count();
				$a[$key]['status6'] = $a[$key]['status0'] + $a[$key]['status1'] + $a[$key]['status2'] + $a[$key]['status3'] + $a[$key]['status4'] + $a[$key]['status5'];
				$a[$key]['status7'] = '0%';
				if ($a[$key]['status6'] > 0) {
					$a[$key]['status7'] = round($a[$key]['status3'] * 100 / $a[$key]['status6'], 2) . '%';
				} 
			} 
			$this -> assign('a', $a);
            //sum
            for($i = 0;$i < 5;$i++) {
                $map_s['status'] = "$i";
                if($current > $current_year.'-10-31'){
                    $map_s['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
                } else{
                    $map_s['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
                }
                $b[$i] = $Enroll -> field('id') -> where($map_s) -> count();
            } 
            $this -> assign('b', $b);
            $this->menuteacher();
			$this -> display();
		} 
	} 
	public function fill() {
		$Enroll = D('Enroll');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map_a['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }
		$fills = $Enroll -> where($map_a) -> Field('fill') -> group('fill') -> select();
		$a = array();
		foreach($fills as $key => $value) {
			$a[$key]['fill'] = $value['fill'];
			$map['fill'] = $value['fill'];
			for($i = 0;$i < 6;$i++) {
				$map['status'] = "$i";
                if($current > $current_year.'-10-31'){
                    $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
                } else{
                    $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
                }
				if ($i == 5) {
					$map['status'] = 'z';
				} 
				$a[$key]['status' . $i] = $Enroll -> where($map) -> count();
			} 
			$a[$key]['status6'] = $a[$key]['status0'] + $a[$key]['status1'] + $a[$key]['status2'] + $a[$key]['status3'] + $a[$key]['status4'] + $a[$key]['status5'];
			$a[$key]['status7'] = '0%';
			if ($a[$key]['status6'] > 0) {
				$a[$key]['status7'] = round($a[$key]['status3'] * 100 / $a[$key]['status6'], 2) . '%';
			} 
		} 
		$this -> assign('a', $a);
            $this->menuteacher();
		$this -> display();
	} 
    public function school() {
		$Area = D('Area');
		$Enroll = D('Enroll');
        $map_a['parent_id']='16';
        $map_a['region_type']='2';
		$citys = $Area -> Field('region_name') ->where($map_a) -> select();
        $jiangsu_citys=array();
        foreach($citys as $key => $value){
            $jiangsu_citys[]=$value['region_name'];
        }
        $citys[]=array("region_name" => "安徽");
        $citys[]=array("region_name" => "浙江");
        $citys[]=array("region_name" => "其它省或信息不全");

		$a = array();
		foreach($citys as $key => $value) {
			$a[$key]['region_name'] = $value['region_name'];
            switch ($value['region_name']) {
                case '安徽': 
                    $map['schoolprovince']=$value['region_name'];
                    $map['schoolcity']=array('NEQ','test');
                    break;
                case '浙江': 
                    $map['schoolprovince']=$value['region_name'];
                    $map['schoolcity']=array('NEQ','test');
                    break; 
                case '其它省或信息不全': 
                    $map['schoolprovince']=array('not in','江苏,安徽,浙江');
                    $map['schoolcity']=array('NEQ','test');
                    break;
                default:
                    $map['schoolprovince']=array('NEQ','test');
                    $map['schoolcity']=$value['region_name'];
            }
			for($i = 0;$i < 6;$i++) {
				$map['status'] = "$i";
                $current = @date("Y-m-d",time());
                $current_year = @date("Y",time());
                $pyear = $current_year-1;
                if($current > $current_year.'-10-31'){
                    $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
                } else{
                    $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
                }
				if ($i == 5) {
					$map['status'] = 'z';
				} 
				$a[$key]['status' . $i] = $Enroll -> where($map) -> count();
			} 
			$a[$key]['status6'] = $a[$key]['status0'] + $a[$key]['status1'] + $a[$key]['status2'] + $a[$key]['status3'] + $a[$key]['status4'] + $a[$key]['status5'];
			$a[$key]['status7'] = '0%';
			if ($a[$key]['status6'] > 0) {
				$a[$key]['status7'] = round($a[$key]['status3'] * 100 / $a[$key]['status6'], 2) . '%';
			} 
		} 
		$this -> assign('a', $a);
            $this->menuteacher();
		$this -> display();
	} 
	public function category() {
		$statusid = $_GET['statusid'];
		if (!isset($statusid)) {
			$this -> error('参数缺失');
		} 
		$map['status'] = $statusid;
        import("@.ORG.Page");
        $listRows = 10;
        $p = new Page($count, $listRows);
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }
		if ($statusid == '5') {
			$map['status'] = 'z';
            $enroll = D('Enroll');
            $count = $enroll -> where($map) -> count();
            $p = new Page($count, $listRows);
            $my = $enroll -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
		} else{
            $enroll = D('Enroll');
            $count = $enroll -> where($map) -> count();
                        
            $enrollRecord=D('EnrollRecordView');
            $dao=D('enrollrecord');
            $query='select max(id) as mid from u_enrollrecord group by enrollid';
            $ids=$dao->query($query);            
            $id_array=array();
            foreach($ids as $v){
                $id_array[]= $v['mid'];
            }                    
            $map['rid'] =array('in',$id_array);  
            $p = new Page($count, $listRows);
            $my = $enrollRecord -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('rtime desc') -> select();
        }
        $page = $p -> show();
        $this -> assign("page", $page);
        $this -> assign('my', $my);
		$this -> assign('statusid', $statusid);
		$this -> menustatus();
		$this -> display();
	} 
	public function categoryForTeacher() {
		$statusid = $_GET['statusid'];
		$tusername = $_GET['teacher'];
		if (!isset($statusid) || !isset($tusername)) {
			$this -> error('参数缺失');
		} 
        
        //显示地区
        $city_fortag=array('南京'=>'南京',
        '苏州'=>'苏州',
        '无锡'=>'无锡',
        '常州'=>'常州',
        '淮安'=>'淮安',
        '连云港'=>'连云港',
        '南通'=>'南通',
        '宿迁'=>'宿迁',
        '泰州'=>'泰州',
        '徐州'=>'徐州',
        '盐城'=>'盐城',
        '扬州'=>'扬州',
        '镇江'=>'镇江',
        '安徽'=>'安徽',
        '浙江'=>'浙江',
        '其它省或信息不全'=>'其它省或信息不全');
        $this -> assign("city_fortag", $city_fortag);
        
         //显示状态
        $all_status=$this->getStatus();
        $this -> assign('all_status', $all_status);
        
        //显示咨询老师
        $User = D('Enroll');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map_a['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }
		$counselor = $User -> Distinct(true) -> Field('counselor,counselorname') -> where($map_a) -> order('counselor asc') -> select();
        $all_teacher=array();
        foreach($counselor as  $value) {
            if($value['counselorname'] != NULL){
                $all_teacher[$value['counselor']] = $value['counselorname'];
            }
        }
        $this -> assign('all_teacher', $all_teacher);
        $this -> assign("teacher_select", $tusername);
   
        
		if ($statusid == '5') {
			$EnrollChangeView = D('EnrollChangeView');
			$map_a['fromcounselor'] = $tusername;
			$count = $EnrollChangeView -> where($map_a) -> count();
			if ($count > 0) {
				import("@.ORG.Page");
				$listRows = 10;
				$p = new Page($count, $listRows);
				$my = $EnrollChangeView -> where($map_a) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
				$page = $p -> show();
				$this -> assign("page", $page);
				$this -> assign('my', $my);
			} 
        } else {
			$Enroll = D('Enroll');
			$map['status'] = $statusid;
			$map['counselor'] = $tusername;
            if($current > $current_year.'-10-31'){
                $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
            } else{
                $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
            }
			$count = $Enroll -> where($map) -> count();
			if ($count > 0) {
				$enrollRecord=D('EnrollRecordView');
                $dao=D('enrollrecord');
                $query='select max(id) as mid from u_enrollrecord group by enrollid';
                $ids=$dao->query($query);            
                $id_array=array();
                foreach($ids as $v){
                    $id_array[]= $v['mid'];
                }
                
                $map['rid'] =array('in',$id_array);
                import("@.ORG.Page");
                $listRows = 10;
                $p = new Page($count, $listRows);
                $my = $enrollRecord -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('rtime desc') -> select();
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
			} 
		} 
		
		$this -> display();
	} 
	public function categoryForFill() {
		$statusid = $_GET['statusid'];
		$fill = $_GET['fill'];
		if (!isset($statusid) || !isset($fill)) {
			$this -> error('参数缺失');
		} 

        //显示地区
        $city_fortag=array('南京'=>'南京',
        '苏州'=>'苏州',
        '无锡'=>'无锡',
        '常州'=>'常州',
        '淮安'=>'淮安',
        '连云港'=>'连云港',
        '南通'=>'南通',
        '宿迁'=>'宿迁',
        '泰州'=>'泰州',
        '徐州'=>'徐州',
        '盐城'=>'盐城',
        '扬州'=>'扬州',
        '镇江'=>'镇江',
        '安徽'=>'安徽',
        '浙江'=>'浙江',
        '其它省或信息不全'=>'其它省或信息不全');
        $this -> assign("city_fortag", $city_fortag);
        
         //显示状态
        $all_status=$this->getStatus();
        $this -> assign('all_status', $all_status);
        
        //显示咨询老师
        $User = D('Enroll');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map_a['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        } 
		$counselor = $User -> Distinct(true) -> Field('counselor,counselorname') -> where($map_a) -> order('counselor asc') -> select();
        $all_teacher=array();
        foreach($counselor as  $value) {
            if($value['counselorname'] != NULL){
                $all_teacher[$value['counselor']] = $value['counselorname'];
            }
        }
        $this -> assign('all_teacher', $all_teacher);
   
		$Enroll = D('Enroll');
		$map_a['fill'] = $fill;
		$map_a['status'] = $statusid;
 
		$count = $Enroll -> where($map_a) -> count();
		if ($count > 0) {
			$enrollRecord=D('EnrollRecordView');
            $dao=D('enrollrecord');
            $query='select max(id) as mid from u_enrollrecord group by enrollid';
            $ids=$dao->query($query);            
            $id_array=array();
            foreach($ids as $v){
                $id_array[]= $v['mid'];
            }
            
            $map_a['rid'] =array('in',$id_array);
            import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $enrollRecord -> where($map_a) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('rtime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 

		$this -> assign('my', $my);
		$this -> display('categoryForTeacher');
	} 
    public function categoryForSchool() {
		$statusid = $_GET['statusid'];
		$region_name = $_GET['region_name'];
		if (!isset($statusid) || !isset($region_name)) {
			$this -> error('参数缺失');
		}
        
        //显示地区
        $this -> assign("city_select", $region_name);
        $city_fortag=array('南京'=>'南京',
        '苏州'=>'苏州',
        '无锡'=>'无锡',
        '常州'=>'常州',
        '淮安'=>'淮安',
        '连云港'=>'连云港',
        '南通'=>'南通',
        '宿迁'=>'宿迁',
        '泰州'=>'泰州',
        '徐州'=>'徐州',
        '盐城'=>'盐城',
        '扬州'=>'扬州',
        '镇江'=>'镇江',
        '安徽'=>'安徽',
        '浙江'=>'浙江',
        '其它省或信息不全'=>'其它省或信息不全');
        $this -> assign("city_fortag", $city_fortag);
        
         //显示状态
        //$all_status=R('EnrollTea/getStatus');
        $all_status=$this->getStatus();
        $this -> assign('all_status', $all_status);
        
        //显示咨询老师
        $User = D('Enroll');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map_a['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        } 
		$counselor = $User -> Distinct(true) -> Field('counselor,counselorname') -> where($map_a) -> order('counselor asc') -> select();
        $all_teacher=array();
        foreach($counselor as  $value) {
            if($value['counselorname'] != NULL){
                $all_teacher[$value['counselor']] = $value['counselorname'];
            }
        }
        $this -> assign('all_teacher', $all_teacher);
        
        $Area = D('Area');
        $map['parent_id']='16';
        $map['region_type']='2';
		$citys = $Area -> Field('region_name') ->where($map) -> select();
        $jiangsu_citys=array();
        foreach($citys as $key => $value){
            $jiangsu_citys[]=$value['region_name'];
        }
		$Enroll = D('Enroll');
        switch ($region_name) {
                case '安徽': 
                    $map_a['schoolprovince']=$region_name;
                    break;
                case '浙江': 
                    $map_a['schoolprovince']=$region_name;
                    break; 
                case '其它省或信息不全': 
                    $map_a['schoolprovince']=array('not in','江苏,安徽,浙江');
                    break;
                default:
                    $map_a['schoolcity']=$region_name;
        }
		$map_a['status'] = $statusid;
		$count = $Enroll -> where($map_a) -> count();
		if ($count > 0) {
            $enrollRecord=D('EnrollRecordView');
            $dao=D('enrollrecord');
            $query='select max(id) as mid from u_enrollrecord group by enrollid';
            $ids=$dao->query($query);            
            $id_array=array();
            foreach($ids as $v){
                $id_array[]= $v['mid'];
            }
            
            $map_a['rid'] =array('in',$id_array);
            import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $enrollRecord -> where($map_a) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('rtime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 

		$this -> assign('my', $my);
		$this -> display('categoryForTeacher');
	}
	public function record() {
		$enrollid = $_GET['enrollid'];
		if (!isset($enrollid)) {
			$this -> error('参数缺失');
		} 
		$enroll = D('Enroll');
		$map['id'] = $enrollid;
		$student_info = $enroll -> where($map) -> find();
		if (!$student_info) {
			$this -> error('未找到该学生');
		} 
		// dump($student_info);
		$map2['enrollid'] = $enrollid;
		$enroll_record = D('Enrollrecord') -> where($map2) -> order('id desc') -> select();
		$this -> assign('my', $student_info);
		$this -> assign('enroll_record', $enroll_record);
		$this -> display();
	} 
    public function pastrecord() {
        $year = $_GET['year'];
        $statusid = $_GET['statusid'];
		$enrollid = $_GET['enrollid'];
		if (!isset($enrollid)) {
			$this -> error('参数缺失');
		} 
		$enroll = D('Enroll');
		$map['id'] = $enrollid;
		$student_info = $enroll -> where($map) -> find();
		if (!$student_info) {
			$this -> error('未找到该学生');
		} 
		// dump($student_info);
		$map2['enrollid'] = $enrollid;
		$enroll_record = D('Enrollrecord') -> where($map2) -> order('id desc') -> select();
		$this -> assign('my', $student_info);
		$this -> assign('enroll_record', $enroll_record);
        $this -> assign('statusid',$statusid);
        $this -> assign('year',$year);
		$this -> display();
	} 
	public function sys() {
		$Sys = D("System");
		$map['category'] = 'enroll';
		$count = $Sys -> where($map) -> count();
		if ($count > 0) {
			$my = $Sys -> where($map) -> order('id desc') -> select();
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
	public function addNotice() {
		$User = D('User');
		$map['role'] = array('like','%EnrollTea%');
		$my = $User -> Field('username,truename') -> where($map) -> order('username asc') -> select();
		$a = array();
		$b = array();
		foreach($my as $key => $value) {
			$temp = $value['username'] . '-' . $value['truename'];
			$a[$temp] = $temp;
			$b[] = $temp;
		} 
		$this -> assign('a', $a);
		$this -> assign('b', $b);
		$this->menunotice();
		$this -> display();
	} 
	public function insertNotice() {
		$title = $_POST['title'];
		$content = $_POST['content'];
		$reader = $_POST['reader'];
		if (empty($title) || empty($content) || empty($reader)) {
			$this -> error('必填项不能为空');
		} 

		$Noticecreate = D('Noticecreate');
		$Notice = D('Notice');
		$data['title'] = $title;
		$data['content'] = $content;
		$data['tusername'] = session('username');
		$data['ttruename'] = D('User') -> where("username='" . session('username') . "'") -> getField('truename');
		$data['ctime'] = date("Y-m-d H:i:s");
		$insertID = $Noticecreate -> add($data);
		if ($insertID) {
			foreach($_POST['reader'] as $key => $value) {
				$data_a[$key]['noticeid'] = $insertID;
				$temp = explode('-', $value);
				$data_a[$key]['readusername'] = $temp[0];
				$data_a[$key]['readtruename'] = $temp[1];
			} 
			$Notice -> addAll($data_a);
			$this -> success('已成功发布');
		} else {
			$this -> error('数据库写入出错');
		} 
	} 
	 public function menunotice() {
    $menu['notice']='所有通知';
    $menu['noticeMy']='我发的通知';
    $menu['addNotice']='新建通知';
    $menu['']='返回';
    $this->assign('menu',$this ->autoMenu($menu));  
    }
	public function notice() {
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['title|content'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		$Noticecreate = D("Noticecreate");
		$Notice = D("Notice");
		$User = D("User");
		$map_b['role'] = array('like', '%EnrollAdm%');
		$enrollAdm = $User -> field('username') -> where($map_b) -> select();
		$a_enrollAdm = array();
		foreach($enrollAdm as $key => $value) {
			$a_enrollAdm[] = $value['username'];
		} 
		$map['tusername'] = array('in', $a_enrollAdm);
		$count = $Noticecreate -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Noticecreate -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			foreach($my as $key => $value) {
				$my[$key]['ok'] = '';
				$r_all = $Notice -> where("noticeid=" . $value['id']) -> count();
				$r_readed = $Notice -> where("readtime is not null and noticeid=" . $value['id']) -> count();
				$my[$key]['ok'] = $r_readed . '/' . $r_all;
			} 
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this->menunotice();
		$this -> display();
	} 
	public function noticeMy() {
		if (isset($_GET['searchkey'])) {
			$searchkey = $_GET['searchkey'];
			$map['title|content'] = array('like', '%' . $searchkey . '%');
			$this -> assign('searchkey', $searchkey);
		} 
		$Noticecreate = D("Noticecreate");
		$Notice = D("Notice");
		$map['tusername'] = session('username');
		$count = $Noticecreate -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 10;
			$p = new Page($count, $listRows);
			$my = $Noticecreate -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			foreach($my as $key => $value) {
				$my[$key]['ok'] = '';
				$r_all = $Notice -> where("noticeid=" . $value['id']) -> count();
				$r_readed = $Notice -> where("readtime is not null and noticeid=" . $value['id']) -> count();
				$my[$key]['ok'] = $r_readed . '/' . $r_all;
			} 
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this->menunotice();
		$this -> display();
	} 
	public function noticeReaded() {
		if (!isset($_GET['noticeid'])) {
			$this -> error('参数缺失');
		} 
		$noticeid = $_GET['noticeid'];
		$Noticecreate = D("Noticecreate");
		$Notice = D("Notice");
		$map['tusername'] = session('username');
		$map['id'] = $noticeid;
		$my = $Noticecreate -> where($map) -> find();
		$r_all = $Notice -> where("noticeid=" . $noticeid) -> select();
		$r_readed = $Notice -> where("readtime is not null and noticeid=" . $noticeid) -> select();
		$a = array();
		$b = array();
		foreach($r_all as $key => $value) {
			$temp = $value['readusername'] . '-' . $value['readtruename'];
			$a[$temp] = $temp;
		} 
		foreach($r_readed as $key => $value) {
			$temp = $value['readusername'] . '-' . $value['readtruename'];
			$b[$temp] = $temp;
		} 
		$this -> assign("my", $my);
		$this -> assign("a", $a);
		$this -> assign("b", $b);
		$this -> display();
	} 
    public function toExcel(){
    	$this->menustatus();
        $this -> display();
    }
    public function pastCommon() {
        $this -> display();
    }
    public function pastCommonLeft() {
        $Enroll = D("Enroll");
        
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        if($current > $current_year.'-10-31'){
            $dtree_year = $Enroll -> field( 'year(ctime) as year') -> group('year')-> order('year desc') -> select();
        } else{
            $dtree_year = $Enroll -> field( 'year(ctime) as year') -> group('year') ->where('year(ctime) != year(now())')-> order('year desc') -> select(); //只查看往年的记录，排除当年的
        }
        $this -> assign('dtree_year',$dtree_year);
        $this -> display();
    }
    public function pastampieData() {
        $year = $_GET['year'];
        $pyear = $year-1;
		$enroll = D('Enroll');
		for($i = 0;$i < 6;$i++) {
			$map['status'] = "$i";
			if ($i == 5) {
				$map['status'] = 'z';
			} 
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
			$a[$i] = $enroll -> Field('id') -> where($map) -> count();
		} 
		$this -> assign('a', $a);
        $this -> assign('year', $year);
		$this -> display();
	} 
    public function paststatus() {
        $year =$_GET['year'];
        $this -> assign('year',$year);
		$this -> display();
	} 
    public function pastcategory() {
        $enroll = D('Enroll');
        $year =$_GET['year'];
        $pyear = $year-1;
        $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
		$statusid = $_GET['statusid'];
		if (!isset($statusid)) {
			$this -> error('参数缺失');
		} 
		$map['status'] = $statusid;
        import("@.ORG.Page");
        $listRows = 10;        
		if ($statusid == '5') {
			$map['status'] = 'z';
            $count = $enroll -> where($map) -> count();
            $p = new Page($count, $listRows);
            $my = $enroll -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
		} else{
            $count = $enroll -> where($map) -> count();
            $enrollRecord=D('EnrollRecordView');
            $dao=D('enrollrecord');
            $query='select max(id) as mid from u_enrollrecord group by enrollid';
            $ids=$dao->query($query);            
            $id_array=array();
            foreach($ids as $v){
                $id_array[]= $v['mid'];
            }                        
            $map['rid'] =array('in',$id_array);
            $p = new Page($count, $listRows);
            $my = $enrollRecord -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('rtime desc') -> select();
        }
        $page = $p -> show();
        $this -> assign("page", $page);
        $this -> assign('my', $my);
		$this -> assign('statusid', $statusid);
        $this -> assign('year',$year);
		$this -> display();
	} 
     public function pastsearch() {
        $year =$_GET['year'];
        $pyear = $year-1;
		$truename = $_GET['truename'];
		$school = $_GET['school'];
		if (isset($truename)) {
			$map['truename|schoolname']=array('like',"%$truename%");
            $this -> assign("truename", $truename);
		} 
        if (isset($school)) {
            $this -> assign("city_select", $school);
            switch ($school) {
                case '安徽': 
                    $map['schoolprovince']=$school;
                    break;
                case '浙江': 
                    $map['schoolprovince']=$school;
                    break; 
                case '其它省或信息不全': 
                    $map['schoolprovince']=array('not in','江苏,安徽,浙江');
                    break;
                default:
                    $map['schoolcity']=$school;
            }
		} 
        $city_fortag=array('南京'=>'南京',
        '苏州'=>'苏州',
        '无锡'=>'无锡',
        '常州'=>'常州',
        '淮安'=>'淮安',
        '连云港'=>'连云港',
        '南通'=>'南通',
        '宿迁'=>'宿迁',
        '泰州'=>'泰州',
        '徐州'=>'徐州',
        '盐城'=>'盐城',
        '扬州'=>'扬州',
        '镇江'=>'镇江',
        '安徽'=>'安徽',
        '浙江'=>'浙江',
        '其它省或信息不全'=>'其它省或信息不全');
        $this -> assign("city_fortag", $city_fortag);
        
            $enroll = D('Enroll');
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
            $count = $enroll -> where($map) -> count();
            if($count>0){
                $enrollRecord=D('EnrollRecordView');
                $dao=D('enrollrecord');
                $query='select max(id) as mid from u_enrollrecord group by enrollid';
                $ids=$dao->query($query);            
                $id_array=array();
                foreach($ids as $v){
                    $id_array[]= $v['mid'];
                }                        
                $map['rid'] =array('in',$id_array);
                import("@.ORG.Page");
                $listRows = 10;
                $p = new Page($count, $listRows);
                $my = $enrollRecord -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('rtime desc') -> select();
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my); 
                $this -> assign('year',$year);
            }
        $this -> display();
	}
     public function pasttoExcel(){
        $year =$_GET['year'];
        $this -> assign('year',$year);
        $this -> display();
    }
    public function pastcategoryForTeacher() {
        $year = $_GET['year'];
        $pyear = $year-1;
		$statusid = $_GET['statusid'];
		$tusername = $_GET['teacher'];
		if (!isset($statusid) || !isset($tusername)) {
			$this -> error('参数缺失');
		} 
		if ($statusid == '5') {
			$EnrollChangeView = D('EnrollChangeView');
			$map_a['fromcounselor'] = $tusername;
            $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
			$count = $EnrollChangeView -> where($map_a) -> count();
			if ($count > 0) {
				import("@.ORG.Page");
				$listRows = 10;
				$p = new Page($count, $listRows);
				$my = $EnrollChangeView -> where($map_a) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
				$page = $p -> show();
				$this -> assign("page", $page);
				$this -> assign('my', $my);
                $this -> assign('year',$year);
                $this -> assign('statusid',$statusid);
			} 
		} else {
			$Enroll = D('Enroll');
			$map['status'] = $statusid;
			$map['counselor'] = $tusername;
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
			$count = $Enroll -> where($map) -> count();
			if ($count > 0) {
				$enrollRecord=D('EnrollRecordView');
                $dao=D('enrollrecord');
                $query='select max(id) as mid from u_enrollrecord group by enrollid';
                $ids=$dao->query($query);            
                $id_array=array();
                foreach($ids as $v){
                    $id_array[]= $v['mid'];
                }
                
                $map['rid'] =array('in',$id_array);
                import("@.ORG.Page");
                $listRows = 10;
                $p = new Page($count, $listRows);
                $my = $enrollRecord -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('rtime desc') -> select();
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
                $this -> assign('year',$year);
                $this -> assign('statusid',$statusid);
			} 
		} 
		
		$this -> display();
	} 
    public function pastteacher() {
        $year = $_GET['year'];
        $pyear = $year-1;
		$User = D('User');
		$map['role'] = array('like','%EnrollTea%');
		$map['ispermit'] = '1';
		$my = $User -> Field('username,truename') -> where($map) -> order('username asc') -> select();
		$a = array();
		$Enroll = D('Enroll');
		$Enrollchange = D('Enrollchange');
		if ($my) {
			foreach($my as $key => $value) {
				$a[$key]['username'] = $value['username'];
				$a[$key]['truename'] = $value['truename'];
				$map_a['counselor'] = $value['username'];
                $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
				for($i = 0;$i < 5;$i++) {
					$map_a['status'] = $i;
					$a[$key]['status' . $i] = $Enroll -> where($map_a) -> count();
				} 
				$map_b['fromcounselor'] = $value['username'];
                $map_b['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
				$a[$key]['status5'] = $Enrollchange -> where($map_b) -> count();
				$a[$key]['status6'] = $a[$key]['status0'] + $a[$key]['status1'] + $a[$key]['status2'] + $a[$key]['status3'] + $a[$key]['status4'] + $a[$key]['status5'];
				$a[$key]['status7'] = '0%';
				if ($a[$key]['status6'] > 0) {
					$a[$key]['status7'] = round($a[$key]['status3'] * 100 / $a[$key]['status6'], 2) . '%';
				} 
			} 
			$this -> assign('a', $a);
            $this -> assign('year',$year);
			$this -> display();
		} 
	} 
	public function pastfill() {
        $year = $_GET['year'];
        $pyear = $year-1;
		$Enroll = D('Enroll');
        $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
		$fills = $Enroll -> where($map_a) -> Field('fill') -> group('fill') -> select();
		$a = array();
		foreach($fills as $key => $value) {
			$a[$key]['fill'] = $value['fill'];
			$map['fill'] = $value['fill'];
			for($i = 0;$i < 6;$i++) {
				$map['status'] = "$i";
                $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
				if ($i == 5) {
					$map['status'] = 'z';
				} 
				$a[$key]['status' . $i] = $Enroll -> where($map) -> count();
			} 
			$a[$key]['status6'] = $a[$key]['status0'] + $a[$key]['status1'] + $a[$key]['status2'] + $a[$key]['status3'] + $a[$key]['status4'] + $a[$key]['status5'];
			$a[$key]['status7'] = '0%';
			if ($a[$key]['status6'] > 0) {
				$a[$key]['status7'] = round($a[$key]['status3'] * 100 / $a[$key]['status6'], 2) . '%';
			} 
		} 
		$this -> assign('a', $a);
        $this -> assign('year',$year);
		$this -> display();
	} 
    public function pastcategoryForFill() {
        $year = $_GET['year'];
        $pyear = $year-1;
		$statusid = $_GET['statusid'];
		$fill = $_GET['fill'];
		if (!isset($statusid) || !isset($fill)) {
			$this -> error('参数缺失');
		} 

		$Enroll = D('Enroll');
		$map_a['fill'] = $fill;
		$map_a['status'] = $statusid;
        $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
		$count = $Enroll -> where($map_a) -> count();
		if ($count > 0) {
			$enrollRecord=D('EnrollRecordView');
            $dao=D('enrollrecord');
            $query='select max(id) as mid from u_enrollrecord group by enrollid';
            $ids=$dao->query($query);            
            $id_array=array();
            foreach($ids as $v){
                $id_array[]= $v['mid'];
            }
                
            $map_a['rid'] =array('in',$id_array);
            import("@.ORG.Page");
            $listRows = 10;
            $p = new Page($count, $listRows);
            $my = $enrollRecord -> where($map_a) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('rtime desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);          
		} 

		$this -> assign('my', $my);
        $this -> assign('year',$year);
        $this -> assign('statusid',$statusid);
		$this -> display();
	} 
    public function pastschool() {
        $year = $_GET['year'];
        $pyear = $year-1;
		$Area = D('Area');
		$Enroll = D('Enroll');
        $map_a['parent_id']='16';
        $map_a['region_type']='2';
		$citys = $Area -> Field('region_name') ->where($map_a) -> select();
        $jiangsu_citys=array();
        foreach($citys as $key => $value){
            $jiangsu_citys[]=$value['region_name'];
        }
        $citys[]=array("region_name" => "安徽");
        $citys[]=array("region_name" => "浙江");
        $citys[]=array("region_name" => "其它省或信息不全");

		$a = array();
		foreach($citys as $key => $value) {
			$a[$key]['region_name'] = $value['region_name'];
            switch ($value['region_name']) {
                case '安徽': 
                    $map['schoolprovince']=$value['region_name'];
                    $map['schoolcity']=array('NEQ','test');
                    break;
                case '浙江': 
                    $map['schoolprovince']=$value['region_name'];
                    $map['schoolcity']=array('NEQ','test');
                    break; 
                case '其它省或信息不全': 
                    $map['schoolprovince']=array('not in','江苏,安徽,浙江');
                    $map['schoolcity']=array('NEQ','test');
                    break;
                default:
                    $map['schoolprovince']=array('NEQ','test');
                    $map['schoolcity']=$value['region_name'];
            }
			for($i = 0;$i < 6;$i++) {
				$map['status'] = "$i";               
				if ($i == 5) {
					$map['status'] = 'z';
				} 
                $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
				$a[$key]['status' . $i] = $Enroll -> where($map) -> count();
			} 
			$a[$key]['status6'] = $a[$key]['status0'] + $a[$key]['status1'] + $a[$key]['status2'] + $a[$key]['status3'] + $a[$key]['status4'] + $a[$key]['status5'];
			$a[$key]['status7'] = '0%';
			if ($a[$key]['status6'] > 0) {
				$a[$key]['status7'] = round($a[$key]['status3'] * 100 / $a[$key]['status6'], 2) . '%';
			} 
		} 
		$this -> assign('a', $a);
        $this -> assign('year', $year);
		$this -> display();
	}
    public function pastcategoryForSchool() {
        $year = $_GET['year'];
        $pyear = $year-1;
		$statusid = $_GET['statusid'];
		$region_name = $_GET['region_name'];
		if (!isset($statusid) || !isset($region_name)) {
			$this -> error('参数缺失');
		} 
        $Area = D('Area');
        $map['parent_id']='16';
        $map['region_type']='2';
		$citys = $Area -> Field('region_name') ->where($map) -> select();
        $jiangsu_citys=array();
        foreach($citys as $key => $value){
            $jiangsu_citys[]=$value['region_name'];
        }
		$Enroll = D('Enroll');
        switch ($region_name) {
                case '安徽': 
                    $map_a['schoolprovince']=$region_name;
                    break;
                case '浙江': 
                    $map_a['schoolprovince']=$region_name;
                    break; 
                case '其它省或信息不全': 
                    $map_a['schoolprovince']=array('not in','江苏,安徽,浙江');
                    break;
                default:
                    $map_a['schoolcity']=$region_name;
        }
		$map_a['status'] = $statusid;
        $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
		$count = $Enroll -> where($map_a) -> count();
		if ($count > 0) {
			$enrollRecord=D('EnrollRecordView');
            $dao=D('enrollrecord');
            $query='select max(id) as mid from u_enrollrecord group by enrollid';
            $ids=$dao->query($query);            
            $id_array=array();
            foreach($ids as $v){
                $id_array[]= $v['mid'];
            }
                
            $map_a['rid'] =array('in',$id_array);
            import("@.ORG.Page");
            $listRows = 10;
            $p = new Page($count, $listRows);
            $my = $enrollRecord -> where($map_a) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('rtime desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
		} 

		$this -> assign('my', $my);
        $this -> assign('year', $year);
        $this -> assign('statusid',$statusid);
		$this -> display();
	}
    public function paststudent(){
        $year = $_GET['year'];
        $statusid = $_GET['statusid'];
        $area = D("Area");
		$province = $area -> where("parent_id = 1") -> Field("region_name") -> select();
		$a = array();
		foreach($province as $key => $value) {
			$a[$value['region_name']] = $value['region_name'];
		} 
		$is_or_not = array('是' => '是', '否' => '否');
		$education = $this -> getSystem("education");
		$entrancefull = $this -> getSystem("entrancefull");
		$englishfull = $this -> getSystem("englishfull");
		$mathfull = $this -> getSystem("mathfull");
		$abroad = $this -> getSystem("abroad");
		$coursewant = $this -> getSystem("coursewant");
		$englishtrain = $this -> getSystem("englishtrain");
		$sourcenewspaper = $this -> getSystem("sourcenewspaper");
		$sourcenet = $this -> getSystem("sourcenet");
		$nationality = $this -> getSystem("nationality");

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
		$id = $_GET['enrollid'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$Enroll = D('Enroll');
		$map['id'] = $id;
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
			$this -> assign('ctime', $my['ctime']);
            $this -> assign('my', $my);
            $this -> assign('year',$year);
            $this -> assign('statusid',$statusid);
			$this -> display();
		} else {
			$this -> error('未找到该学生或对该学生没有操作权限');
		} 
    }
    public function pastPlus() {
		$area = D("Area");
		$user = D("User");
		$province = $area -> where("parent_id = 1") -> Field("region_name") -> select();
		$a = array();
		foreach($province as $key => $value) {
			$a[$value['region_name']] = $value['region_name'];
		} 
		$education = $this -> getSystem("education");
		$entrancefull = $this -> getSystem("entrancefull");
		$englishfull = $this -> getSystem("englishfull");
		$mathfull = $this -> getSystem("mathfull");
		$abroad = $this -> getSystem("abroad");
		$coursewant = $this -> getSystem("coursewant");
		$englishtrain = $this -> getSystem("englishtrain");
		$sourcenewspaper = $this -> getSystem("sourcenewspaper");
		$sourcenet = $this -> getSystem("sourcenet");
		$nationality = $this -> getSystem("nationality");
		$counselor = $user -> Field("username,truename") -> where("role like '%EnrollTea%'") -> select();
		$counselor_tag = array();
		foreach($counselor as $key => $value) {
			$counselor_tag[$value['username']] = $value['truename'];
		} 
		$this -> assign('a', $a);
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
		$this -> assign('counselor_tag', $counselor_tag);
		$this -> display();
	}
    public function attendInsert() {
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
        
        for($i=3;$i<=$count;$i++){
            $data_a[$i-3]['truename'] = $sheetData[$i]['A'];
            $data_a[$i-3]['username'] = $sheetData[$i]['B'];
            $data_a[$i-3]['sex'] = $sheetData[$i]['C'];
            $data_a[$i-3]['nationality'] = $sheetData[$i]['D'];
            $data_a[$i-3]['birthday'] = $sheetData[$i]['E'];
            $data_a[$i-3]['idcard'] = $sheetData[$i]['F'];
            $data_a[$i-3]['address'] = $sheetData[$i]['G'];
            $data_a[$i-3]['nativeprovince'] = $sheetData[$i]['H'];
            $data_a[$i-3]['nativecity'] = $sheetData[$i]['I'];
            $data_a[$i-3]['mobile'] = $sheetData[$i]['J'];
            $data_a[$i-3]['qq'] = $sheetData[$i]['K'];
            $data_a[$i-3]['email'] = $sheetData[$i]['L'];
            $data_a[$i-3]['fname'] = $sheetData[$i]['M'];
            $data_a[$i-3]['fmobile'] = $sheetData[$i]['N'];
            $data_a[$i-3]['fphone'] = $sheetData[$i]['O'];
            $data_a[$i-3]['fprovince'] = $sheetData[$i]['P'];
            $data_a[$i-3]['fcity'] = $sheetData[$i]['Q'];
            $data_a[$i-3]['funit'] = $sheetData[$i]['R'];
            $data_a[$i-3]['fpost'] = $sheetData[$i]['S'];
            $data_a[$i-3]['femail'] = $sheetData[$i]['T'];
            $data_a[$i-3]['mname'] = $sheetData[$i]['U'];
            $data_a[$i-3]['mmobile'] = $sheetData[$i]['V'];
            $data_a[$i-3]['mphone'] = $sheetData[$i]['W'];
            $data_a[$i-3]['mprovince'] = $sheetData[$i]['X'];
            $data_a[$i-3]['mcity'] = $sheetData[$i]['Y'];
            $data_a[$i-3]['munit'] = $sheetData[$i]['Z'];
            $data_a[$i-3]['mpost'] = $sheetData[$i]['AA'];
            $data_a[$i-3]['memail'] = $sheetData[$i]['AB'];
            $data_a[$i-3]['oname'] = $sheetData[$i]['AC'];
            $data_a[$i-3]['olink'] = $sheetData[$i]['AD'];
            $data_a[$i-3]['omobile'] = $sheetData[$i]['AE'];
            $data_a[$i-3]['ophone'] = $sheetData[$i]['AF'];
            $data_a[$i-3]['oprovince'] = $sheetData[$i]['AG'];
            $data_a[$i-3]['ocity'] = $sheetData[$i]['AH'];
            $data_a[$i-3]['ounit'] = $sheetData[$i]['AI'];
            $data_a[$i-3]['opost'] = $sheetData[$i]['AJ'];
            $data_a[$i-3]['oemail'] = $sheetData[$i]['AK'];
            $data_a[$i-3]['education'] = $sheetData[$i]['AL'];
            $data_a[$i-3]['schoolprovince'] = $sheetData[$i]['AM'];
            $data_a[$i-3]['schoolcity'] = $sheetData[$i]['AN'];
            $data_a[$i-3]['schoolname'] = $sheetData[$i]['AO'];
            $data_a[$i-3]['ctime'] = $sheetData[$i]['AP'];
            $data_a[$i-3]['status'] ='3';
            $data_a[$i-3]['statustext'] ='已录取';
        }
        $dao=D('Enroll');
        load("@.check");
    for($i=3;$i<=$count;$i++){
        if (empty($data_a[$i-3]['truename'])) {
			$this -> error('第'.$i.'行 姓名不能为空');
		} 
        if (empty($data_a[$i-3]['username'])) {
			$this -> error('第'.$i.'行 学号不能为空');
		} elseif (!isusername($data_a[$i-3]['username'])) {
			$this -> error('第'.$i.'行 学号校验失败');
		} else{
            $map['username'] = $data_a[$i-3]['username'];
            $isHava = $dao -> where($map) -> select();
            if ($isHava) {
                $this -> error("第".$i."行 "."库中已有相同学号的学生，无法录入! 姓名:" . $isHava[0]['truename'] . " 学号:" . $isHava[0]['username']);
            } 
        }
		if (empty($data_a[$i-3]['sex'])) {
			$this -> error('第'.$i.'行 性别不能为空');
		} elseif (!issex($data_a[$i-3]['sex'])) {
            $this -> error('第'.$i.'行 性别校验失败');
        }   
        if (empty($data_a[$i-3]['birthday'])) {
			$this -> error('第'.$i.'行 出生日期不能为空');
		} else {
            $data_a[$i-3]['birthday'] =$data_a[$i-3]['birthday'] .' 00:00:00';
        }
		if (empty($data_a[$i-3]['mobile'])) {
			$this -> error('第'.$i.'行 手机号不能为空');
		} elseif (!ismobile($data_a[$i-3]['mobile'])) {
			$this -> error('第'.$i.'行 手机号校验失败');
		} 
		if (!empty($data_a[$i-3]['qq'])) {
			if (!is_numeric($data_a[$i-3]['qq'])) {
				$this -> error('第'.$i.'行 QQ号码校验失败');
            }
        }
		if (!empty($data_a[$i-3]['email'])) {
			if (!isemail($data_a[$i-3]['email'])){
				$this -> error('第'.$i.'行 Email校验失败');
            }
        }
        if (empty($data_a[$i-3]['schoolprovince'])) {
			$this -> error('第'.$i.'行 毕业学校所在省不能为空');
		} 
        if (empty($data_a[$i-3]['schoolcity'])) {
			$this -> error('第'.$i.'行 毕业学校所在市不能为空');
		} 
        if (empty($data_a[$i-3]['schoolname'])) {
			$this -> error('第'.$i.'行 毕业学校名称不能为空');
		}
        if (empty($data_a[$i-3]['ctime'])) {
			$this -> error('第'.$i.'行 入学年份不能为空');
		} elseif (!isctime($data_a[$i-3]['ctime'])) {
            $this -> error('第'.$i.'行 入学年份校验失败');
        } else {
            $data_a[$i-3]['ctime'] = $data_a[$i-3]['ctime'].'-09-10 00:00:00';
        }        
    }
       
        $dao -> addAll($data_a);
        $this -> success("已成功保存");
    }
     public function addEnroll() {
		$area = D("Area");
		$user = D("User");
		$province = $area -> where("parent_id = 1") -> Field("region_name") -> select();
		$a = array();
		foreach($province as $key => $value) {
			$a[$value['region_name']] = $value['region_name'];
		} 
		$education = $this -> getSystem("education");
		$entrancefull = $this -> getSystem("entrancefull");
		$englishfull = $this -> getSystem("englishfull");
		$mathfull = $this -> getSystem("mathfull");
		$abroad = $this -> getSystem("abroad");
		$coursewant = $this -> getSystem("coursewant");
		$englishtrain = $this -> getSystem("englishtrain");
		$sourcenewspaper = $this -> getSystem("sourcenewspaper");
		$sourcenet = $this -> getSystem("sourcenet");
		$nationality = $this -> getSystem("nationality");
		$counselor = $user -> Field("username,truename") -> where("role like '%EnrollTea%'") -> select();
		$counselor_tag = array();
		foreach($counselor as $key => $value) {
			$counselor_tag[$value['username']] = $value['truename'];
		} 
		$this -> assign('a', $a);
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
		$this -> assign('counselor_tag', $counselor_tag);
		$this -> display();
	} 
    public function pinsert() {
		$dao = D("Enroll");
        $fill=$_POST['fill'];
		if ($dao -> create()) {
			$dao -> ctime = $_POST['ctime'].'-09-10 00:00:00' ;
			$dao -> abroad = implode(',', $_POST['abroad']);
			$dao -> infosource = implode(',', $_POST['infosource']);
			$dao -> sourcenewspaper = implode(',', $_POST['sourcenewspaper']);
			$dao -> sourcenet = implode(',', $_POST['sourcenet']);
            $dao -> status = '3';
            $dao -> statustext ='已录取';
			if ($insertID = $dao -> add()) {
                    $this -> success('已成功录入！');

			} else{
                $this -> error('新建招生信息：数据写入错误！');
            }
		} else {
			$this -> error($dao -> getError());
		} 
	} 
    public function checkpEnroll() {
		load("@.idcard");
		load("@.check");
        $dao = D("Enroll");
		if (empty($_POST['truename'])) {
			$this -> error('Part1 学生姓名不能为空');
		}
        if (empty($_POST['username'])) {
			$this -> error('Part1 学号不能为空');
		} elseif (!isusername($_POST['username'])) {
			$this -> error('Part1 学号校验失败');
		} else{
            $map['username'] = $_POST['username'];
            $isHava = $dao -> where($map) -> select();
            if ($isHava) {
                $this -> error("库中已有相同学号的学生，无法录入! 姓名:" . $isHava[0]['truename'] . " 学号:" . $isHava[0]['username']);
            } 
        }
        if (empty($_POST['ctime'])) {
			$this -> error('Part1 入学年份不能为空');
		} elseif (!isctime($_POST['ctime'])) {
            $this -> error('Part1 入学年份校验失败');
        }           
		if (empty($_POST['sex'])) {
			$this -> error('Part1 性别不能为空');
		} elseif (!issex($_POST['sex'])) {
            $this -> error('第'.$i.'行 性别校验失败');
        }   
        if (empty($_POST['birthday'])) {
			$this -> error('Part1 出生日期不能为空');
		} 
		if (empty($_POST['mobile'])) {
			$this -> error('Part1 学生手机号不能为空');
		} elseif ((ismobile($_POST['mobile'])||($_POST['mobile']=='00000000000'))==false) {
			$this -> error('Part1 学生手机号校验失败');
		} else{
            $map['mobile'] = $_POST['mobile'];
            $isHava = $dao -> where($map) -> select();
            if ($isHava) {
                $this -> error("库中已有相同手机号，无法录入! 姓名:" . $isHava[0]['truename'] . " 手机号:" . $isHava[0]['mobile'] . " 时间：" . $isHava[0]['ctime'] . " 填表人：" . $isHava[0]['fill']);
            } 
        }
        if (!empty($_POST['qq'])) {
            if (!is_numeric($_POST['qq'])){
				$this -> error('Part1 学生QQ号码校验失败');
            }
		} 
		if (!empty($_POST['email'])) {
            if (!isemail($_POST['email'])){
				$this -> error('Part1 学生Email校验失败');
            }
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
        if (empty($_POST['schoolprovince'])||empty($_POST['schoolcity'])||empty($_POST['schoolname'])) {
			$this -> error('Part3 毕业学校必填');
		}
        
		$this -> success('数据校验成功');
	}
    public function pdel() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$Enroll = D('Enroll');
		$map['id'] = array('in', $id);
		$reslut = $Enroll -> where($map) -> delete();
		if ($reslut > 0) {
			$this -> success('已成功删除');
		} else {
			$this -> error('该记录不存在或已删除');
		} 
	} 
    public function excel(){
		$enroll = D('Enroll');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }
        $my = $enroll -> field('idcard,truename,sex,mobile,statustext,counselorname,fill,ctime') -> where($map) -> order('status asc,id desc') -> select();
        $count = $enroll -> where($map) -> count();
        $count++;
        import("@.ORG.PHPExcel");
		$objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:H'.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        // Add data
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '性别');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '身份证号');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '手机');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '状态');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', '咨询者');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '录入者');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', '录入时间');
		$k = 2;
		foreach($my as $value){
			//纯文本
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.$k, $value['truename']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('B'.$k, $value['sex']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('C'.$k, " ".$value['idcard']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('D'.$k, " ".$value['mobile']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('E'.$k, $value['statustext']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('F'.$k, $value['counselorname']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('G'.$k, $value['fill']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('H'.$k, $value['ctime']);
            ++$k;
		}
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('全部');
		$objPHPExcel->setActiveSheetIndex(0);
		$outFileName = 'data '.date("Y-m-d H_i_s");
		$outFileName = iconv('UTF-8','GBK',$outFileName);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$outFileName.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}            
    public function excel2(){
		$enroll = D('Enroll');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }
        $map['status']='3';
        $my = $enroll -> where($map) -> order('CONVERT(schoolprovince USING gbk), CONVERT(schoolcity USING gbk), CONVERT(schoolname USING gbk)') -> select();
        $count = $enroll -> where($map) -> count();
        $count++;
        import("@.ORG.PHPExcel");
		$objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:J'.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        // Add data
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '性别');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '身份证号');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '高考成绩');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '英语成绩');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', '毕业高中所在省');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '毕业高中名称');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', '报名费收据号');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', '学费发票号');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', '咨询教师');
		$k = 2;
		foreach($my as $value){
			//纯文本
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.$k, $value['truename']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('B'.$k, $value['sex']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('C'.$k, " ".$value['idcard']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('D'.$k, $value['entrancescore']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('E'.$k, $value['englishscore']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('F'.$k, $value['schoolprovince']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('G'.$k, $value['schoolcity'].$value['schoolname']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('H'.$k, " ".$value['entryfees']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('I'.$k, " ".$value['tuitionfeef']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('J'.$k, $value['counselorname']);
            ++$k;
		}
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('已录取');
		$objPHPExcel->setActiveSheetIndex(0);
		$outFileName = 'data '.date("Y-m-d H_i_s");
		$outFileName = iconv('UTF-8','GBK',$outFileName);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$outFileName.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
    public function excel3(){
		$enroll = D('Enroll');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }
        $map['status']='2';
        $my = $enroll -> where($map) -> order('CONVERT(schoolprovince USING gbk), CONVERT(schoolcity USING gbk), CONVERT(schoolname USING gbk)') -> select();
        $count = $enroll -> where($map) -> count();
        $count++;
        import("@.ORG.PHPExcel");
		$objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:K'.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
        // Add data
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '性别');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '身份证号');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '高考成绩');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '英语成绩');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '毕业高中所在省');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '毕业高中名称');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', '报名费收据号');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', '学费发票号');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', '咨询教师');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', '填表人');

		$k = 2;
		foreach($my as $value){
			//纯文本
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.$k, $value['truename']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('B'.$k, $value['sex']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('C'.$k, " ".$value['idcard']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('D'.$k, $value['entrancescore']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('E'.$k, $value['englishscore']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('F'.$k, $value['schoolprovince']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('G'.$k, $value['schoolcity'].$value['schoolname']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('H'.$k, " ".$value['entryfees']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('I'.$k, " ".$value['tuitionfeef']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('J'.$k, $value['counselorname']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('K'.$k, $value['fill']);
            ++$k;
		}
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('已交报名费');
		$objPHPExcel->setActiveSheetIndex(0);
		$outFileName = 'data '.date("Y-m-d H_i_s");
		$outFileName = iconv('UTF-8','GBK',$outFileName);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$outFileName.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
    public function pastexcel(){
		$enroll = D('Enroll');
        $year = $_GET['year'];
        $pyear = $year-1;
        $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
        $my = $enroll ->field('truename,sex,mobile,statustext,counselorname,fill,ctime') -> where($map) -> order('status asc,id desc') -> select();
        $count = $enroll -> where($map) -> count();
        $count++;
        import("@.ORG.PHPExcel");
		$objPHPExcel = new PHPExcel();
        //Add setting
        $objPHPExcel->getActiveSheet()->getStyle('A1:G'.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        // Add data
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '性别');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '手机');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '状态');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '咨询者');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', '录入者');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '录入时间');
		$k = 2;
		foreach($my as $value){
			//纯文本
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.$k, $value['truename']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('B'.$k, $value['sex']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('C'.$k, " ".$value['mobile']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('D'.$k, $value['statustext']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('E'.$k, $value['counselorname']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('F'.$k, $value['fill']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('G'.$k, $value['ctime']);		
            ++$k;
		}
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('全部');
		$objPHPExcel->setActiveSheetIndex(0);
		$outFileName = 'data '.date("Y-m-d H_i_s");
		$outFileName = iconv('UTF-8','GBK',$outFileName);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$outFileName.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
    public function pastexcel2(){
		$enroll = D('Enroll');
        $year = $_GET['year'];
        $pyear = $year-1;
        $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
        $map['status']='3';
        $my = $enroll ->where($map) -> order('CONVERT(schoolprovince USING gbk), CONVERT(schoolcity USING gbk), CONVERT(schoolname USING gbk)') -> select();
        $count = $enroll -> where($map) -> count();
        $count++;
        import("@.ORG.PHPExcel");
		$objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:J'.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        // Add some data
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '性别');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '身份证号');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '高考成绩');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '英语成绩');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '毕业高中所在省');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '毕业高中名称');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', '报名费收据号');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', '学费发票号');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', '咨询教师');
		$k = 2;
		foreach($my as $value){
			//纯文本
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.$k, $value['truename']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('B'.$k, $value['sex']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('C'.$k, " ".$value['idcard']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('D'.$k, $value['entracescore']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('E'.$k, $value['englishscore']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('F'.$k, $value['schoolprovince']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('G'.$k, $value['schoolcity'].$value['schoolname']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('H'.$k, " ".$value['entryfees']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('I'.$k, " ".$value['tuitionfeef']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('J'.$k, $value['counselorname']);
            ++$k;
		}
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('已录取');
		$objPHPExcel->setActiveSheetIndex(0);
		$outFileName = 'data '.date("Y-m-d H_i_s");
		$outFileName = iconv('UTF-8','GBK',$outFileName);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$outFileName.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
    public function excel4(){
        $enroll = D('Enroll');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }
        $map['status']='3';
        $my = $enroll ->where($map) -> order('id asc') -> select();
        $count = $enroll -> where($map) -> count();
        $count++;
        import("@.ORG.PHPExcel");
		$objPHPExcel = new PHPExcel();
        
        for($i='A';$i!='AAA'; $i++){
            $a[]=$i;
        }
        $b=array('录入时间','姓名','性别','民族','出生日期','身份证号','家庭住址','籍贯','手机号','QQ号','email','备注','父亲姓名','父亲手机号','父亲固话','父亲工作单位','父亲职务','父亲email','母亲姓名','母亲手机号','母亲固话','母亲工作单位','母亲职务','母亲email','其他联系人姓名','与学生关系','其他联系人手机号','其他联系人固话','其他联系人工作单位','其他联系人职务','其他联系人email','受教育程度','毕业学校','雅思成绩','托福成绩','PTE成绩','高考总分','英语成绩','数学成绩','留学国家','课程选择','测试成绩','试读协议','英语培训情况','暑期培训成绩','信息来源','报名费发票号','报名费收据号','学费发票号','学费收据号','咨询老师','填表人');
        $c=array('ctime','truename','sex','nationality','birthday','idcard','address','native','mobile','qq','email','plus','fname','fmobile','fphone','funit','fpost','femail','mname','mmobile','mphone','munit','mpost','memail','oname','olink','omobile','ophone','ounit','opost','oemail','education','schoolname','languagescore1','languagescore2','languagescore3','entrancescore','englishscore','mathscore','abroad','coursewant','testscore','try','englishtrain','summertrainscore','infosource','entryfeef','entryfees','tuitionfeef','tuitionfees','counselor','fill');
        
        //
       // $value['infosource'].$value['sourcenewspaper'].$value['sourcenet'].$value['sourcefriend']
        $objPHPExcel->getActiveSheet()->getStyle('A1:AZ'.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        for($i='A';$i!='AAA'; $i++){
            $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setWidth(40);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        foreach($b as $key => $val){
            $objPHPExcel->getActiveSheet()->setCellValue($a[$key].'1', $val);
        }
		$k = 2;
		foreach($my as $k1 => $value){                            
            foreach($c as $k2 => $v){
                $objPHPExcel->getActiveSheet(0)->setCellValue($a[$k2].$k, $value[$v]);
                if($v == 'idcard' || $v == 'entryfeef' || $v == 'entryfees' || $v == 'tuitionfeef' || $v == 'tuitionfees'){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($a[$k2].$k, " ".$value[$v]);
                }
                if($v == 'native'){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($a[$k2].$k, $value['nativeprovince'].$value['nativecity']);
                }
                if($v == 'funit'){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($a[$k2].$k, $value['fprovince'].$value['fcity'].$value['funit']);
                }
                if($v == 'munit'){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($a[$k2].$k, $value['mprovince'].$value['mcity'].$value['munit']);
                }
                if($v == 'ounit'){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($a[$k2].$k, $value['oprovince'].$value['ocity'].$value['ounit']);
                }
                if($v == 'schoolname'){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($a[$k2].$k, $value['schoolprovince'].$value['schoolcity'].$value['schoolname']);
                }
                if($v == 'infosource'){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($a[$k2].$k, $value['infosource'].$value['sourcenewspaper'].$value['sourcenet'].$value['sourcefriend']);
                }
                if($v == 'counselor'){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($a[$k2].$k, $value['counselor'].$value['counselorname']);
                }
            }
            ++$k;
		}
        
     
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('已录取全部信息');
		$objPHPExcel->setActiveSheetIndex(0);
		$outFileName = 'data '.date("Y-m-d H_i_s");
		$outFileName = iconv('UTF-8','GBK',$outFileName);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$outFileName.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
    }
    public function menuagent() {
    $menu['agent']='所有代理人';
    $menu['addAgent']='新增代理人';
    $menu['agentStudent']='按代理人查询';
    $menu['enrollStuList']='报名学员查询';
    $menu['']='返回';
    $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function agent(){
        if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
            $map['username|truename'] = array('like','%'.$searchkey.'%');
            $this->assign('searchkey',$searchkey);
        }

        $Agent = D("Agent");
        $count = $Agent -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=D('System')->where("category='office' and name='listrow'")->getField("content");
            $p = new Page($count, $listRows);
            $ag = $Agent -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
            foreach($ag as $key=>$value){
                $ag[$key]['statusname']=$this->getAgentStatusName($value['status']);
            }
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('ag', $ag);
        }
        $this->menuagent();
        $this->display();
    }

    public function setAgentStatus() {
        $username=$_GET['username'];
        $status=$_GET['status'];
        if(!isset($username)){
            $this->error('参数缺失');
        }
        $Agent=D('Agent');
        $result=$Agent->where("username = '".$username."'")->setField('status',$status);
        if($result!==false){
            $this->success('设置成功');
        }else {
            $this->error('设置失败');
        }
    }

    public function resetAgentPwd() {
        $idcard=$_GET['idcard'];
        if(!isset($idcard)){
            $this->error('参数缺失');
        }
        $Agent=D('Agent');
        $password = md5(substr($idcard,-6));
        $result=$Agent->where("idcard = '".$idcard."'")->setField('pwd',$password);
        if($result!==false){
            $this->success('已成功重置密码，新密码为身份证后六位');
        }else {
            $this->error('重置密码失败');
        }
    }

    public function getAgentStatusName($status){
        if ($status == 0){
            return "暂停";
        }

        return "启用";
    }

    public function delAgent() {
        $username=$_GET['username'];
        if(!isset($username)){
            $this->error('参数缺失');
        }
        $Agent=D('Agent');
        $result=$Agent->where("username = '".$username."'")->delete();
        if($result!==false){
            $this->success('删除成功');
        }else {
            $this->error('删除失败');
        }
    }

    public function addAgent(){
    	$this->menuagent();
        $this->display();
    }

    public function insertAgent(){
        if(empty($_POST['username'])||empty($_POST['truename'])||empty($_POST['idcard'])){
            $this->error('必填项不能为空');
        }
        $username=$_POST['username'];
        $truename=$_POST['truename'];
        $idcard=$_POST['idcard'];
        $applymax=$_POST['applymax'];

        $Agent=D('Agent');
        $map['username']=$username;
        $count=$Agent->where(array("username"=>$username,"idcard"=>$idcard))->count();
        if($count>0){
            $this->error('该帐号或身份证号码已存在于数据库中，请使用其它帐号');
        }

        $data_a['username']=$username;
        $data_a['truename']=$truename;
        $data_a['pwd']=md5(substr($idcard,-6));//身份证后6位
        $data_a['idcard']=$idcard;
        $data_a['applymax']=$applymax;
        $data_a['ctime']=date("Y-m-d H:i:s");
        $insertID=$Agent->add($data_a);
        if($insertID){
            $this->success('已成功保存');
        }else{
            $this->error('数据库影响行数为0');
        }
    }

    public function updateAgent(){
        $map['idcard'] =  $_GET['idcard'];
        $Agent = D("Agent");
        $ag = $Agent -> where($map) -> find();
        $this -> assign('ag', $ag);

        $this->display();
    }

    public function modAgent(){
        if(empty($_POST['username'])||empty($_POST['truename'])||empty($_POST['identitycard'])){
            $this->error('必填项不能为空');
        }
        $username=$_POST['username'];
        $truename=$_POST['truename'];
		$idcard=$_POST['idcardno'];
        $idcard_new=$_POST['identitycard'];
        $applymax=$_POST['applymax'];

        $Agent=D('Agent');
        //$ag = $Agent->where("idcard = '".$idcard."'")->count();
        //$this->error($ag);
        $data['truename'] = $truename;
        $data['applymax'] = $applymax;
		$data['idcard'] = $idcard_new;
		$data['username'] = $username;
        //$result=$Agent->where("idcard = '".$idcard."'")->setField(array('truename','applymax'),array($truename,$applymax));
        $result=$Agent->where("idcard = '".$idcard."'")->save($data);
        if($result!==false){
            $this->success('已成功保存');
        }else {
            $this->error('数据库影响行数为0');
        }
    }

    public function agentStudent(){
        if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
            $map['username|truename'] = array('like','%'.$searchkey.'%');
            $this->assign('searchkey',$searchkey);
        }

        $Agent = D("Agent");
        $count = $Agent -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=D('System')->where("category='office' and name='listrow'")->getField("content");
            $p = new Page($count, $listRows);
            $ag = $Agent -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
            foreach($ag as $key=>$value){
                $ag[$key]['enrollcount']=$this->getAgentEnrollCount($value['username']);
            }
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('ag', $ag);
        }
        $this->menuagent();
        $this->display();
    }

    public function getAgentEnrollCount($agent){
        $Enroll = D("Enroll");
        $map['agent']=$agent;
        $count = $Enroll -> where($map) -> count();

        return $count;
    }

    public function agentStuList(){
        if(isset($_GET['agent'])) {
            $map['agent'] = $_GET['agent'];
        }

        if(isset($_GET['searchkey']) && !empty($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
            $map['username|truename|mobile|idcard'] = array('like','%'.$searchkey.'%');
            $this->assign('searchkey',$searchkey);
        }

        if(isset($_GET['ctimestart'])) {
            $ctimestart = $_GET['ctimestart'];
            $map['ctime'] = array('egt', $ctimestart);
            $this->assign('ctime_start',$ctimestart);
        }

        if(isset($_GET['ctimeend'])) {
            $ctimeend = $_GET['ctimeend'];
            $map['ctime'] = array('elt', $ctimeend);
            $this->assign('ctime_start',$ctimeend);
        }

        $Enroll = D("Enroll");
        $count = $Enroll -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=D('System')->where("category='office' and name='listrow'")->getField("content");
            $p = new Page($count, $listRows);
            $er = $Enroll -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();

            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('er', $er);
        }
        $this->display();
    }

    public function enrollStuList(){
        if(isset($_GET['searchkey']) && !empty($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
            $map['agent|agentname|username|truename|mobile|idcard'] = array('like','%'.$searchkey.'%');
            $this->assign('searchkey',$searchkey);
        }

        if(isset($_GET['ctimestart'])) {
            $ctimestart = $_GET['ctimestart'];
            $map['ctime'] = array('egt', $ctimestart);
            $this->assign('ctime_start',$ctimestart);
        }

        if(isset($_GET['ctimeend'])) {
            $ctimeend = $_GET['ctimeend'];
            $map['ctime'] = array('elt', $ctimeend);
            $this->assign('ctime_start',$ctimeend);
        }

        $Enroll = D("Enroll");
        $count = $Enroll -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=D('System')->where("category='office' and name='listrow'")->getField("content");
            $p = new Page($count, $listRows);
            $er = $Enroll -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();

            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('er', $er);
        }
        $this->menuagent();
        $this->display();
    }

    public function setEnrollStatus() {
        $idcard=$_GET['idcard'];
        $status=$_GET['status'];
        if(!isset($idcard)){
            $this->error('参数缺失');
        }
        $Enroll=D('Enroll');
        $data["enrollstatus"] = $status;
        $data["enrolltime"] = @date("Y-m-d",time());
        $result=$Enroll->where("idcard = '".$idcard."'")->save($data);
        if($result!==false){
            $this->success('设置成功');
        }else {
            $this->error('设置失败');
        }
    }

    public function setPayStatus() {
        $idcard=$_GET['idcard'];
        $status=$_GET['status'];
        if(!isset($idcard)){
            $this->error('参数缺失');
        }
        $Enroll=D('Enroll');
        $result=$Enroll->where("idcard = '".$idcard."'")->setField('paystatus',$status);
        if($result!==false){
            $this->success('设置成功');
        }else {
            $this->error('设置失败');
        }
    }

    public function delEnroll() {
        $idcard=$_GET['idcard'];
        if(!isset($idcard)){
            $this->error('参数缺失');
        }
        $Enroll=D('Enroll');
        $result=$Enroll->where("idcard = '".$idcard."'")->delete();
        if($result!==false){
            $this->success('删除成功');
        }else {
            $this->error('删除失败');
        }
    }
}

?>