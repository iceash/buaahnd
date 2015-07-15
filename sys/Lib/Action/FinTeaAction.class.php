<?php
class FinTeaAction extends CommonAction{
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

    public function pay() {
        $paymode=D('System')->where("category='fin' and name='paymode'")->getField("content");
        $modes=explode(',',$paymode);
        if(count($modes)>1){
            $this->assign('paymode',$modes);
        }

        $this -> display();
    }

    public function projectList() {
        $project = D("Finproject");
        $count = $project -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=D('System')->where("category='office' and name='listrow'")->getField("content");
            $p = new Page($count, $listRows);
            $proj = $project -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id') -> select();
            foreach($proj as $key=>$value){
                //$ag[$key]['statusname']=$this->getAgentStatusName($value['status']);
            }
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('proj', $proj);
        }

        $this -> display();
    }

    public function payList() {
        $this -> display();
    }

    public function getStuInfo(){
        $number=$_GET['Number'];

        if(!isset($number)){
            $this->error('参数缺失');
        }
        $dao=D('User');
        $map['username']=$number;
        $my=$dao->where($map)->find();
        //$this->error($number);
        if($my){
            $this->ajaxReturn($my,'',1);
        }else{
            $this->error('未找到记录');
        }
    }

    public function addPayinfo() {
        $dao = D('Payinfo');
        if ($dao -> create()) {
            if(empty($_POST['status'])){
                $this -> error('留学进程不能为空');
            }else{
                $status=implode(',',$_POST['status']);
                $dao->status=$this->fixStatusFull($status);
            }
            $dao->ctime=date('Y-m-d H:i:s');
            $dao->tusername4=session('username');
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
}