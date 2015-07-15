/*
 * ezj.ext.Zoom
 * v1.0
 */
 
ezj.ext.Zoom = function(e)
{
    e = $g(e);
    e.style.cursor = "crosshair";
    function getImgSrc(e)
    {
        var src = e.getAttribute("ezjExtZoom");
        if (!String.isNullOrEmpty(src))
        {
            return src;
        }
        
        var ltn = e.tagName.toLowerCase();
        if (ltn == "a")
        {
            src = e.href;
        }
        else if (ltn == "img")
        {
            src = e.src;
        }
        else
        {
            var a = $g("<a", e);
            var img = $g("<img", e);
            if (a.length > 0)
            {
                src = getImgSrc(a[0]);
            }
            else if (img.length > 0)
            {
                src = getImgSrc(img[0]);
            }
        }
        
        return src;
    }
    
    e.mouseover(function(){
        var space = e.space();
        ezj.ext.Zoom.previewBox.style.width = (space.width - 2) + "px"; // 减边框
        ezj.ext.Zoom.previewBox.style.height = (space.height - 2) + "px"; // 减边框
        ezj.ext.Zoom.previewBox.style.overflow = "hidden";
        ezj.ext.Zoom.previewBox.style.borderWidth = "1px";
        ezj.ext.Zoom.previewBox.style.borderStyle = "solid";
        ezj.ext.Zoom.previewBox.style.borderColor = "#ccc";
        ezj.ext.Zoom.previewBox.style.backgroundColor = "#fff";
        ezj.ext.Zoom.previewImg.src = getImgSrc(e);
        ezj.ext.Zoom.previewBox.pos(e, "roti");
        ezj.ext.Zoom.previewBox.display(true);
    });
    e.addListener("mousemove", function(){
        // 要求 e 和 previewBox 大小相等，才适用。
        var space = e.space();
        var imgSpace = ezj.ext.Zoom.previewImg.space();
        var event = ezj.event.getEvent();
        var left = parseInt((event.clientX - space.x) * (imgSpace.width / space.width) - (space.width / 2));
        var top = parseInt((event.clientY - space.y) * (imgSpace.height / space.height) - (space.height / 2));
        ezj.ext.Zoom.previewBox.scrollLeft = left;
        ezj.ext.Zoom.previewBox.scrollTop = top;
    });
    e.mouseout(function(){
        ezj.ext.Zoom.previewBox.display(false);
    });
};
ezj.ext.Zoom.previewBox = $c("div", null, document.body);
ezj.ext.Zoom.previewImg = $c("img", null, ezj.ext.Zoom.previewBox);
ezj.ext.Zoom.previewBox.style.display = "none";