<?php
class ItemViewModel extends ViewModel {
	public $viewFields = array(
        'theme' => array('theme'),
        'item' => array('id','item','type','options','beginTime','endTime','_on'=>'item.themeId=theme.id')
    );
} 

?>