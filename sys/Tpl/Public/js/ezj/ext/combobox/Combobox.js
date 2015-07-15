/*
 * ezj.ext.Combobox
 * v1.0
 */
 
ezj.ext.Combobox = function(selectElement)
{
///<summary>Combobox 类。</summary>
///<param name="selectElement" type="string/object">要替换的 select 元素 id，或元素对象。</param>
///<param name="mode" type="string">可选。模式。可选值为：dropdown、dropdownlist，默认值为 dropdownlist。</param>
    var mode = Function.overload(1, "dropdownlist");
    
    // 创建
    var select = $g(selectElement);
    var text = $c("text");
    var arrow = $c("img");
    var drop = $c("div");
    $g(select.parentNode).insert(text, select);
    $g(select.parentNode).insert(arrow, select);
    $g(select.parentNode).insert(drop, select);
    
    // 设置样式
    var selectSpace = select.space();
    text.addClass("ezj_ext_combobox_text");
    var compatWidth = (ezj.ie.is() && ezj.ie.engine <= 6 ? 0 : 2); // text 边框占据问题
    text.style.width = (selectSpace.width - compatWidth - 18) + "px"; // 18 是箭头宽度
    text.style.height = (selectSpace.height - compatWidth) + "px";
    text.style.lineHeight = (selectSpace.height - compatWidth) + "px";
    text.value = (select.sel()[0]).text; // 显示初始值
    arrow.addClass("ezj_ext_combobox_arrow");
    arrow.src = ezj.about.path + "ext/combobox/collapsed.gif";
    arrow.style.width = "18px";
    arrow.style.height = selectSpace.height + "px";
    drop.addClass("ezj_ext_combobox_drop");
    drop.style.display = "none";
    drop.style.width = (selectSpace.width - 2) + "px"; // 2 为 drop 边框
    
    // 填充下拉
    select.val().each(function(v, index){
        var item = $c("div", null, drop);
        $v(item, v.text);
        item.mouseover(function(e){
            e.addClass("ezj_ext_combobox_drop_item_hover");
        });
        item.mouseout(function(e){
            e.removeClass("ezj_ext_combobox_drop_item_hover");
        });
        item.click(function(e){
            text.value = v.text;
            select.sel([v.value]);
            e.removeClass("ezj_ext_combobox_drop_item_hover");
        });
    });
    
    // 设置文本部分可编辑否
    if (mode == "dropdown")
    {
        // combobox 为 list 增加一项来存储
        select.options[select.options.length] = new Option("", "");
        text.leave(function(e){
            select.options[select.options.length - 1].text = e.value;
            select.options[select.options.length - 1].value = e.value;
            select.options[select.options.length - 1].selected = true;
        });
    }
    else
    {
        // 不可手动输入
        text.readOnly = true;
        text.enable(false);
        text.style.cursor = "default";
    }
    
    // 隐藏 select
    select.style.display = "none";
    
    // 下拉动作
    var arrowCmd = false; // 是否是由 arrow 发出的命令
    var expanded = false;
    
    function expand()
    {
        if (expanded)
        {
            return;
        }
        
        arrow.src = ezj.about.path + "ext/combobox/expanding.gif";
        drop.pos(text);
        drop.show("t", "max", "fast", 10); // 使用效果
        expanded = true;
    }
    
    function collapse()
    {
        if (!expanded)
        {
            return;
        }
        
        arrow.src = ezj.about.path + "ext/combobox/collapsed.gif";
        drop.show("cur", "t", "fast", 10); // 使用效果
        expanded = false;
    }
    
    arrow.click(function(){
        arrowCmd = true;
        if (!expanded)
        {
            expand();
        }
        else
        {
            collapse();
        }
    });
    
    $g(document.body).click(function(){
        if (!arrowCmd)
        {
            // 在箭头以外的地方点击鼠标
            collapse();
        }
        arrowCmd = false; // expired
    });
};