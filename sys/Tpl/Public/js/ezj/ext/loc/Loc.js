/*
 * ezj.ext.Loc
 * v1.0
 */
ezj.ext.Loc = function(element)
{
///<summary>语法：ezj.ext.Loc(element)</summary>
    element = $g(element);
    var me = this;
    
    this.text = { obj : $c("text", { size : 6 }) };
    this.text.obj.keydown(function(){
        if (ezj.event.getKeyCode() == ezj.event.keyCodes.enter)
        {
            find();
        }
    });
    this.button = { obj : $c("button", { value : "查找" }) };
    this.button.obj.click(function(){
        find();
    });
    
    function find()
    {
        // find([reStart]);
        var key = me.text.obj.val();
        var list = element;
        var reStart = Function.overload(0, false); // 是否从头重新开始
        var pos = (reStart ? 0 : list.options.selectedIndex + 1); // 默认从下一个位置开始搜索
        var found = false;
        
        // 从当前位置下一个开始找
        for (var i = pos; i < list.options.length; i++)
        {
            if (list.options[i].text.toLowerCase().indexOf(key.toLowerCase()) >= 0)
            {
                list.options[i].selected = true;
                found = true;
                break;
            }
        }

        if (!found)
        {
            if (!reStart)
            {
                if (confirm("到达列表结束位置。\r\n正在重新从起始位置开始搜索。"))
                {
                    find(true);
                }
            }
            else
            {
                // 重新找了一遍都没找到
                alert("未找到指定的项。");
            }
        }
    }
    
    element.after(this.text.obj);
    this.text.obj.after(this.button.obj);
};