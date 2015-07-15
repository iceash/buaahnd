function selectOne(obj,name){

var objCheckBox =document.getElementsByName(name); 
         for(var i=0;i<objCheckBox.length;i++){ 
             //判断复选框集合中的i元素是否为obj，若为否则便是未被选中 
             if (objCheckBox[i]!=obj) { 
                 objCheckBox[i].checked = false; 
             } else{ 
                 //若是，原先为被勾选的变成勾选，反之则变成未勾选 
                 objCheckBox[i].checked = obj.checked; 
                  
                 //或者使用下句，亦可达到同样效果 
                 //objCheckBox[i].checked = true; 
             } 
         } 
    }