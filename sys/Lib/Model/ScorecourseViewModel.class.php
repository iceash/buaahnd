<?php 
class ScorecourseViewModel extends ViewModel {
	public $viewFields = array(
        'course' =>array('name','ename','credit','coursetime'),
        'courseteacher'=>array('_on'=>'course.number=courseteacher.coursenumber'),
        'score' => array('id','subjectid','susername','struename','tusername','ttruename','term','score','plus','bscore','savetime','bsavetime','subtime','ispublic','isb','isvisible','_on'=>'score.subjectid=courseteacher.id')
    );

}
?>