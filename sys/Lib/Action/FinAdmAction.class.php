<?php
class FinAdmAction extends CommonAction{
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

    public function sys() {
        $Sys = D("System");
        $map['category'] = 'fin';
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

    public function project() {
        $paytype=D('System')->where("category='fin' and name='paytype'")->getField("content");
        $types=explode(',',$paytype);
        if(count($types)>1){
            $this->assign('paytype',$types);
        }
        $this -> display();
    }
}