//千一网络 www.cftea.com
//DragBinder v1.1 ezj 版本 MIT
//http://www.cftea.com/products/DragBinder/
//此文件为 UTF-8 格式
//应用示例：
/*
var winDrag = new DragBinder("win", "win");
winDrag.setScope("multiWin", "body");
*/


//参数 target，字符串或对象。被拖拽的控件 Id 名称或控件对象，好比是 Windows 窗体。
//参数 controller，字符串或对象。用于响应拖拽的控件 Id 名称或控件对象，好比是 Windows 窗体中的标题栏。
//target 和 controller 也可以是同一个控件。
function DragBinder(target, controller)
{
    this.target = $g(target);
    this.controller = $g(controller);
    
    this.draging = false; //是否处于拖拽中
    
    this.phaseOriginalX = 0; //当前拖拽开始时控件的 X 坐标，不必关心坐标原点。
    this.phaseOriginalY = 0; //当前拖拽开始时控件的 Y 坐标，不必关心坐标原点。
    this.phaseMouseOriginalX = 0; //当前拖拽开始时鼠标的 X 坐标，即鼠标按下时，鼠标的 clientX。
    this.phaseMouseOriginalY = 0; //当前拖拽开始时鼠标的 Y 坐标，即鼠标按下时，鼠标的 clientY。
    
    this.scopeElement = null; //限制拖拽范围的控件，控件的拖拽范围被限定在 scopeElement 的区域内。
    this.scopeRestrict = "position"; //可选值：position 和 body。position，控件的左上角在 scopeElement 区域内；body，控件的所有区域在 scopeElement 区域内。
    this.stepX = 1; //X 方向步进长度
    this.stepY = 1; //Y 方向步进长度
    
    
    //绑定拖拽功能
    this.bind = function ()
    {
        this.target.style.position = "absolute";
        
        //准备事件数据
        var me = this;
        if (window.attachEvent)
        {
            //IE
            this.controller.attachEvent("onmousedown", function (){ me.startDrag(); });
            document.body.attachEvent("onmousemove", function (){ me.doDrag(); }); //注意是 document.body
            document.body.attachEvent("onmouseup", function (){ me.stopDrag() }); //注意是 document.body
        }
        else
        {
            this.controller.addEventListener("mousedown", function (){ me.startDrag(); }, false);
            document.body.addEventListener("mousemove", function (){ me.doDrag(); }, false); //注意是 document.body
            document.body.addEventListener("mouseup", function (){ me.stopDrag() }, false); //注意是 document.body
        }
    }
    
    
    //开始拖拽
    this.startDrag = function ()
    {
        var theEvent = window.event || arguments.callee.caller.arguments[0];
        var button = (typeof(theEvent.which) == "number") ? theEvent.which : theEvent.button; //which 在先，因为 button 是各浏览器均有。
        if (button != 1)
        {
            return; //不是鼠标左键，退出。
        }
        
        this.phaseOriginalX = this.target.offsetLeft;
        this.phaseOriginalY = this.target.offsetTop;    
        this.phaseMouseOriginalX = theEvent.clientX;
        this.phaseMouseOriginalY = theEvent.clientY;
        
        this.draging = true;
        
        if (typeof(this.onDragStarted) == "function")
        {
            this.onDragStarted();
        }
    }
    
    
    //执行拖拽
    this.doDrag = function()
    {
        if (!this.draging)
        {
            return; //不处于拖拽中，退出。
        }
        
        //为了更好地兼容 IE 8 对 this.target.offsetLeft（Top） 的解释，特设置 left、top。
        var left = isNaN(parseInt(this.target.style.left)) ? this.target.offsetLeft : parseInt(this.target.style.left);
        var top = isNaN(parseInt(this.target.style.top)) ? this.target.offsetTop : parseInt(this.target.style.top);

        var theEvent = window.event || arguments.callee.caller.arguments[0];

        //预计新位置
        var preX = this.phaseOriginalX + theEvent.clientX - this.phaseMouseOriginalX;
        var preY = this.phaseOriginalY + theEvent.clientY - this.phaseMouseOriginalY;

        //按整数个步长移动
        if (this.stepX > 1)
        {
            preX = Math.round(preX / this.stepX) * this.stepX;
        }
        if (this.stepY > 1)
        {
            preY = Math.round(preY / this.stepY) * this.stepY;
        }

        //拖拽范围限制
        preX = this.calculatePreXWithScope(preX);
        preY = this.calculatePreYWithScope(preY);

        //本次移动了多少，如果 step 为 0，则不移动
        var preDeltaX = (this.stepX > 0) ? preX - left : 0;
        var preDeltaY = (this.stepY > 0) ? preY - top : 0;
        var info = { deltaX: preDeltaX, deltaY: preDeltaY };
        if (typeof (this.onMoving) == "function")
        {
            this.onMoving(info);
        }

        //移动
        this.target.style.left = (left + info.deltaX) + "px";
        this.target.style.top = (top + info.deltaY) + "px";
        if (typeof (this.onMoved) == "function")
        {
            this.onMoved(info);
        }
    }
    
    
    //根据 scope，计算 preX
    this.calculatePreXWithScope = function (preX)
    {
        if (this.scopeElement)
        {
            if (this.scopeRestrict == "position")
            {
                if (preX > this.scopeElement.clientWidth)
                {
                    preX = this.scopeElement.clientWidth;
                }
                else if (preX < 0)
                {
                    preX = 0;
                }
            }
            else if (this.scopeRestrict == "body")
            {
                if (preX > this.scopeElement.clientWidth - this.target.offsetWidth)
                {
                    preX = this.scopeElement.clientWidth - this.target.offsetWidth;
                }
                else if (preX < 0)
                {
                    preX = 0;
                }
            }
        }
        
        return preX;
    }
    
    
    //根据 scope，计算 preY
    this.calculatePreYWithScope = function (preY)
    {
        if (this.scopeElement)
        {
            if (this.scopeRestrict == "position")
            {
                if (preY > this.scopeElement.clientHeight)
                {
                    preY = this.scopeElement.clientHeight;
                }
                else if (preY < 0)
                {
                    preY = 0;
                }
            }
            else if (this.scopeRestrict == "body")
            {
                if (preY > this.scopeElement.clientHeight - this.target.offsetHeight)
                {
                    preY = this.scopeElement.clientHeight - this.target.offsetHeight;
                }
                else if (preY < 0)
                {
                    preY = 0;
                }
            }
        }
        
        return preY;
    }
    
    
    //结束拖拽
    this.stopDrag = function ()
    {
        if (!this.draging)
        {
            return; //不处于拖拽中，退出。
        }
        
        var theEvent = window.event || arguments.callee.caller.arguments[0];
        var button = (typeof(theEvent.which) == "number") ? theEvent.which : theEvent.button; //which 在先，因为 button 是各浏览器均有。
        if (button != 1)
        {
            return; //不是鼠标左键，退出。
        }
        
        this.draging = false;
        
        if (typeof(this.onDragStopped) == "function")
        {
            this.onDragStopped();
        }
    }
    
    
    //自动绑定
    this.bind();
}


//事件。
//当拖拽开始后触发。
DragBinder.prototype.onDragStarted = null;


//事件。
//当拖拽时控件正在移动时触发。传递一个参数：info。
//参数 info，对象。该对象有两个参数 deltaX、deltaY，分别表示在 X 和 Y 方向上即将移动的距离。
DragBinder.prototype.onMoving = null;


//事件。
//当拖拽时控件移动后触发。传递一个参数：info。
//参数 info，对象。该对象有两个参数 deltaX、deltaY，分别表示在 X 和 Y 方向上移动的距离。
DragBinder.prototype.onMoved = null;


//事件。
//当拖拽结束后触发。
DragBinder.prototype.onDragStopped = null;


//限制拖拽范围。
//参数 scopeElement，字符串或对象。控件的拖拽范围被限定在 scopeElement 的区域内，该参数为控件 Id 名称或控件对象。
//参数 scopeRestrict，字符串。限制方式。可选值：position 和 body。position，控件的左上角在 scopeElement 区域内；body，控件的所有区域在 scopeElement 区域内。
DragBinder.prototype.setScope = function(scopeElement, scopeRestrict)
{
    this.scopeElement = $g(scopeElement);
    this.scopeRestrict = scopeRestrict;
}


//设置步进长度，在制作滑块条时特别有用。
//参数 stepX，数字。X 方向上的步进长度。
//参数 stepY，数字。Y 方向上的步进长度。
DragBinder.prototype.setStep = function(stepX, stepY)
{
    this.stepX = stepX;
    this.stepY = stepY;
}