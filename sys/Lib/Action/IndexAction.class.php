<?php
class IndexAction extends Action {
	public function index() {
		$this -> redirect('Index/login');
	} 
	public function login() {
		if (session('?role')) {
            $roles=explode(',',session('role'));
			$this -> redirect($roles[0] . '/index');
		} else {
			$this -> display('login');
		} 
	} 
	public function logout() {
		if (session('?role')) {
			session('role', null);
			session('username', null);
			session('truename', null);
			session('[destroy]');
		} 
		$this -> redirect('Index/login');
	}

	public function checkLogin() { 
		if (empty($_POST['user']) || empty($_POST['password'])) {
			$this -> error('必填字段不能留空');
		} 
		$check = false;
		$md = $_POST['md'];
		$rnd = $_POST['rnd'];
		$Admin = D('User');
		$count = 0;
		$map['username'] = $_POST['user'];
		$map['pwd'] = md5($_POST['password']);
		//$map['ukeysn'] = $_POST['sn'];

		$count = $Admin -> where($map) -> count();
        
		if ($count == 1) {
            
			$my = $Admin -> where($map) -> find();
            if($my['ispermit']==0){
                $this -> error('您的帐号已被冻结');
            }
			
			$check = true;
			$Admin -> where('id=' . $my['id']) -> setInc('logincount', 1);
			session('role', $my['role']);
			session('username', $my['username']);
			session('truename', $my['truename']);
           
		} 
        // $check = true;
        // session('role', "EnrollTea,EnrollAdm,FinTea,FinAdm,EduTea,TeaAdm,EduDir,EduAdm,TrainTea,TrainAdm,AbroadTea,AbroadAdm,ExamTea,SourceTea,Office,SysAdm");
        // session('username', "admin");
        // session('truename', "系统管理员");
		if ($check) {
			$this -> success() ;
		} else {
			$this -> error('您输入的密码不匹配');
		} 
	} 
	public function ampieSetting() {
		$this -> assign('date', date("Y-m-d"));
		$this -> display();
	} 
	public function setting() {
        if (!session('?role')){
            $this -> error('未登录');
        }
		$this -> display();
	} 
    public function settingImage() {
        if (!session('?role')){
            $this -> error('未登录');
        }
        $User=D('User');
        $map['username']=session('username');
        $my=$User->where($map)->field('id,photo')->find();
        $this->assign('my',$my);
		$this -> display();
	} 
    public function changePwd() {
        $pwd1=$_POST['pwd1'];
        $pwd2=$_POST['pwd2'];
        $pwd3=$_POST['pwd3'];
        if(empty($pwd1)||empty($pwd2)||empty($pwd3)){
            $this->error('必填项不能为空');
        }
        if(strlen($pwd2)<6){
            $this->error('密码长度至少6位');
        }
        if($pwd2!==$pwd3){
            $this->error('两次输入的新密码不一样');
        }
        $User=D('User');
        $map['pwd']=md5($pwd1);
        $map['username']=session('username');
        $my=$User->where($map)->find();
        if($my){
            $data['id']=$my['id'];
            $data['pwd']=md5($pwd2);
            $result=$User->save($data);
            if($result>0){
                $this->success('密码修改成功');
            }else{
                $this->error('新旧密码一样');
            }
        }else{
            $this->error('旧密码错误');
        }
	} 
    public function uploadImage() {
        if (!empty($_FILES)) {
            $this->_upload();
        }
    }
     protected function _upload() {
        import("@.ORG.UploadFile");
        //导入上传类
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize = 800000;
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath = '../sys/upload_photo/';
        //设置需要生成缩略图，仅对图像文件有效
        $upload->thumb = true;
        // 设置引用图片类库包路径
        $upload->imageClassPath = '@.ORG.Image';
        //设置需要生成缩略图的文件后缀
        $upload->thumbPrefix = 's_';  //生产1张缩略图
        //设置缩略图最大宽度
        $upload->thumbMaxWidth = '100';
        //设置缩略图最大高度
        $upload->thumbMaxHeight = '100';
        //设置上传文件规则
        $upload->saveRule = uniqid;
        //删除原图
        $upload->thumbRemoveOrigin = false;
        if (!$upload->upload()) {
            //捕获上传异常
            $this->error($upload->getErrorMsg());
        } else {
            //取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();
            $_POST['image'] = $uploadList[0]['savename'];
        }
        $User = D('User');
        //保存当前数据对象
        $data['photo'] = $_POST['image'];
        $data['id'] = $_POST['id'];
        $result = $User->save($data);
        if ($result >0) {
            $this -> redirect('settingImage');
        } else {
            $this->error('上传图片失败!');
        }
    }
	public function DigestComp($ClientDigest, $RandomData, $Password) {
		$iPad = "";
		for($i = 0; $i < 64; $i++)
		$iPad .= "6";

		$oPad = "";
		for($i = 0; $i < 64; $i++)
		$oPad .= "\\";

		$KLen = strlen($Password);
		$iResult = "";
		for($i = 0; $i < 64; $i++) {
			if ($i < $KLen)
				$iResult .= $iPad[$i] ^ $Password[$i];
			else
				$iResult .= $iPad[$i];
		} 

		$iResult .= $RandomData;

		$iResult = md5($iResult);
		$Test = $this->hexstr2array($iResult);
		$iResult = "";
		$Num = count($Test);
		for($i = 0; $i < $Num; $i++)
		$iResult .= chr($Test[$i]);

		$oResult = "";
		for($i = 0; $i < 64; $i++) {
			if ($i < $KLen)
				$oResult .= $oPad[$i] ^ $Password[$i];
			else
				$oResult .= $oPad[$i];
		} 

		$oResult .= $iResult;

		$Result = md5($oResult);
		$Result = strtoupper($Result);

		if ($Result == $ClientDigest)
			return true;
		else
			return false;
	} 

	public function hexstr2array($HexStr) {
		$HEX = "0123456789ABCDEF";
		$Str = strtoupper($HexStr);
		$Len = strlen($Str);

		for($i = 0; $i < $Len / 2; $i++) {
			$NumHigh = strpos($HEX, $Str[$i * 2]);
			$NumLow = strpos($HEX, $Str[$i * 2 + 1]);
			$Ret[] = $NumHigh * 16 + $NumLow;
		} 

		return $Ret;
	} 
    public function getRole(){
        $a=array();
        $a['Zero']='零权限';
        $a['EnrollTea']='招生教师';
        $a['EnrollAdm']='招生管理员';
        $a['FinAdm']='财务管理员';
        $a['FinTea']='财务人员';
        $a['EduTea']='任课教师';
        $a['TeaAdm']='教师管理员';
        $a['EduDir']='班主任';
        $a['EduAdm']='教务管理员';
        $a['TrainTea']='培训教师';
        $a['TrainAdm']='培训管理员';
        $a['AbroadTea']='留学教师';
        $a['AbroadAdm']='留学管理员';
        $a['ExamTea']='题库管理员';
        $a['SourceTea']='资源管理员';
        $a['Office']='办公室人员';
        $a['SysAdm']='系统管理员';
        return $a;
    }
   
	public function test2() {
		$a=$this->DigestComp('60619D697F1590D7B4C7A50B5B406DA0','a0449dcca9ea8340a47f63b2255096c2','345');
        dump($a);
	}
} 

?>