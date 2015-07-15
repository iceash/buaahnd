//千一网络 www.cftea.com
//HighlightKey v1.0 ezj 版本 MIT
//http://www.cftea.com/products/HighlightKey/
//此文件为 UTF-8 格式
//应用示例：
/*
highlightKey("content", ["千一", "cftea"], ["#FF0000", "#009999"], ["#00FF00", "#666666"]);
*/


//参数 target，字符串或对象。要查找并高亮其内容的控件 Id 或控件对象。
//参数 keys，数组。要查找的词的数组。
//参数 bgColors，数组。高亮时各个查找词的背景色。
//参数 colors，数组。高亮时各个查找词的前景色。
function highlightKey(target, keys, bgColors, colors)
{
    var e = target;
    
    if (e.nodeType == 3)
    {
        //文本节点
        highlightKey_replace(e, keys, bgColors, colors)
        return;
    }
    
    
    //循环子节点
    for (var i = 0; i < e.childNodes.length; i++)
    {
        var node = e.childNodes[i];
        if (node.nodeType != 3)
        {
            //子节点不是文本节点，可能需要递归继续处理。
            if (node.nodeName.toLowerCase() != "textarea")
            {
                //textarea、button 中的内容不能替换，因为不响应 HTML，也就无法标明关键字。
                //input 控件中也不响应 HTML，但显示的内容是在其属性 value 中，这里并不判断属性的值。
                //同理也不用担心替换到 a 的 title、img 的 alt 等。
                //注意：可以在 <button>按钮</button> 上使用高亮效果。
                highlightKey(node, keys, bgColors, colors);
            }
        }
        else if (node.nodeType == 3)
        {
            highlightKey_replace(node, keys, bgColors, colors);
        }
    }
}


function highlightKey_replace(textNode, keys, bgColors, colors)
{
    var str = textNode.nodeValue;
    for (var i = 0; i < keys.length; i++)
    {
        var re = new RegExp("(" + keys[i] + ")", "gi");
        if (bgColors && colors)
        {
            str = str.replace(re, "<font style=\"background-color:" + bgColors[i] + ";color:" + colors[i] + ";\">$1</font>");
        }
        else if (bgColors)
        {
            str = str.replace(re, "<font style=\"background-color:" + bgColors[i] + ";\">$1</font>");
        }
        else if (colors)
        {
            str = str.replace(re, "<font style=\"color:" + colors[i] + ";\">$1</font>");
        }
    }
    
    
    //替换节点。
    //由于 nodeValue 并不支持 HTML，所以采用了一种技巧（http://www.cftea.com/c/2008/11/F9N72VIWFS2G7459.asp）：
    //首先将替换后的 textNode 的内容装于一个 div；
    //然后再将 div 中各个节点（由于有了 HTML，不再是一个节点）插入到 textNode 之前；
    //再删除 textNode。
    var e = $c("div");
    $g(e).val(str);
    
    //注意：insertBefore 会删除源节点，请参见 http://www.cftea.com/c/2008/11/LL4JWZBYUWM1UD1K.asp。
    while (e.childNodes.length > 0)
    {
        textNode.parentNode.insertBefore(e.childNodes[0], textNode);
    }
    
    textNode.parentNode.removeChild(textNode);
}