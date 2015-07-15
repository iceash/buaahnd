ezj.ext.Cancelable = function(element)
{
    var e = $g(element);
    
    if (typeof e.checked != "undefined")
    {
        var checked = e.checked;
        e.click(function(){
            if (checked)
            {
                e.checked = false;
            }
            checked = e.checked;
        });
    }
    else if (typeof e.options != "undefined")
    {
        var selectedIndex = -1;
        e.click(function(){
            if (selectedIndex == e.options.selectedIndex && selectedIndex >= 0)
            {
                // e.options[selectedIndex].selected = false; // 在 IE 中取消不了
                e.options.selectedIndex = -1;
            }
            selectedIndex = e.options.selectedIndex;
        });
    }
};