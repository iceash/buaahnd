<?php 
class ScoreclassViewModel extends ViewModel {
	public $viewFields = array(
        'score' => array('id','subjectid','susername','struename','tusername','ttruename','ttruename','coursename','courseename','coursenumber','coursetime','credit','term','score','plus','bscore','savetime','bsavetime','subtime','bsubtime','ispublic','isb','isvisible','ordinaryscore','qimoscore','levelscore','blevelscore'),
        'classstudent' => array('ename'=>'sename', '_on'=>'classstudent.student=score.susername'),
        'class' => array('name'=>'classname','ename'=>'classename', 'year', 'id'=>'classid','_on'=>'class.id=classstudent.classid')
    );

}
?>