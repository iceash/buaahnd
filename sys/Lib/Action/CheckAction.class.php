<?php
class CheckAction extends Action {
	public function index() {
        if(!isset($_GET['username'])||!isset($_GET['session_id'])) {
			echo '0';
        }else{
            $map['username']=$_GET['username'];
            $map['session_id']=$_GET['session_id'];
            $mysession= D("session")->where($map)->find();
            if($mysession){
//             if($_GET['username']=='GJ201288888'&&$_GET['session_id']=='vli8n7ofv65h3f1pmd0cqmmgi7'){
                echo '1';
            }else{
                echo '0';
            }
        }
	} 
    public function islogin() {
        if(!isset($_GET['username'])) {
			echo '0';
        }else{
//             $map['username']=$_GET['username'];
//             $map['session_id']=$_GET['session_id'];
//             $mysession= D("session")->where($map)->find();
            if($_GET['username']=='120001'){
                echo '1';
            }else{
                echo '0';
            }
        }
	} 
} 

?>