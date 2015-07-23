<?php
class EaterAction extends CommonAction {
	public function index() {
        $User = D('User');
        $dao = D('mail');
        $map1['a1'] = session('username');
        $map1['isdela'] = 0;
        $map1['isread'] = 0;
        $mail_num=$dao->where($map1)->count();
        $this->assign('mail_num',$mail_num);
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



}
?>