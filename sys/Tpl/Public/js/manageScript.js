/*----三帧结构的折叠 ---*/function UnWrap(k){	if(top.document.getElementsByTagName("FRAMESET")[0]==null||top.document.getElementsByTagName("FRAMESET")[1]==null)		return;	//右帧全屏显示	if(k==0){		UnWrap(1);		UnWrap(3);	}	//显示三帧	else if(k==-1){		UnWrap(2);		UnWrap(4);	}	//展开上帧	else if(k==1)		top.document.getElementsByTagName("FRAMESET")[0].rows="90,*";	//折叠上帧	else if(k==2)		top.document.getElementsByTagName("FRAMESET")[0].rows="0,*";	//展开左帧	else if(k==3)		top.document.getElementsByTagName("FRAMESET")[1].cols="180,*";	//折叠左帧	else if( k == 4 )		top.document.getElementsByTagName("FRAMESET")[1].cols="14,*";}//左右变化function LeftRightUnWrap( obj ){	var attr = obj.attributes.getNamedItem( "class" );	var str = attr.value.toLowerCase();	if( str == "leftbutton" )	{		attr.value = "rightbutton";		obj.title = "恢复默认";		UnWrap( 4 );	}	else	{		obj.title = "向左收缩";		attr.value = "leftbutton";		UnWrap(3);	}	return false;}//按纽所在td的宽度var WrapButtonTdWidth = 12;/*----三帧结构的折叠 ---*/