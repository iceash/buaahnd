/*
 * ezj.ext.Watermark
 * v1.1
 */
 
ezj.ext.Watermark = function(element)
{
///<summary>语法：ezj.ext.Watermark(element[, text])</summary>
    var e = $g(element);
    var text = Function.overload(1, e.title);
    
    if (String.isNullOrEmpty(text))
    {
        return;
    }
    
    if (typeof e.ezjExtWatermarkInputed == "undefined")
    {
        e.ezjExtWatermarkInputed = false;
    }
    
    if (String.isNullOrEmpty(e.value))
    {
        e.value = text;
        e.addClass("ezj_ext_watermark");
        e.ezjExtWatermarkInputed = false;
    }
    else
    {
        e.ezjExtWatermarkInputed = true;
    }
    
    e.enter(function(){
        if (!e.ezjExtWatermarkInputed)
        {
            e.value = "";
            e.removeClass("ezj_ext_watermark");
        }
    });
    e.leave(function(){
        e.ezjExtWatermarkInputed = e.value != ""; // 鼠标右键的删除，不会触发 keyup、mouseup，所以在 leave 中也判断
        if (!e.ezjExtWatermarkInputed)
        {
            e.value = text;
            e.addClass("ezj_ext_watermark");
        }
    });
    e.keyup(function(){
        e.ezjExtWatermarkInputed = e.value != "";
    });
    e.mouseup(function(){
        e.ezjExtWatermarkInputed = e.value != "";
    });
}