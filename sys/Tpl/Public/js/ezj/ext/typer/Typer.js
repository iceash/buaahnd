/*
 * ezj.ext.Typer
 * v1.0.1
 */
 
ezj.ext.Typer = function(container, items)
{
///<summary>语法：ezj.ext.Typer(container, items[[, screenDelay], speed])</summary>
    this.container = $g(container);
    this.items = items;
    this.itemIndex = 0;
    this.characterIndex = 0;
    this.screenDelay = Function.overload(2, 1000);
    this.speed = Function.overload(3, "normal");
    this.speed = Array.map(["slow", "normal", "fast"], [200, 100, 50], this.speed, (!Number.is(this.speed) ? 50 : this.speed));
};


ezj.ext.Typer.prototype.type = function()
{
    if (this.items.length <= 0)
    {
        return;
    }
    
    if (this.characterIndex >= this.items[this.itemIndex].text.length)
    {
        this.characterIndex = 0;
        this.itemIndex++;
        if (this.itemIndex >= this.items.length)
        {
            this.itemIndex = 0;
            
        }
    }
    
    var href = this.items[this.itemIndex].href;
    var title = this.items[this.itemIndex].title;
    var target = this.items[this.itemIndex].target;
    var text = this.items[this.itemIndex].text;
    var curText = text.substr(0, this.characterIndex + 1);
    if (curText.endsWith("&"))
    {
        // 可能是实体，不能把一个实体拆开来显示
        // 实体应为小写
        var entities = ["&nbsp;", "&lt;", "&gt;", "&amp;", "&quot;", "&apos;"];
        var entity = text.mid(curText.length - 1, ";") + ";";
        if (entities.exists(entity))
        {
            // 是实体
            curText += entity.mid(1);
            this.characterIndex = curText.length - 1;
        }
    }
    
    curText += "_";
    if (String.isNullOrEmpty(href))
    {
        $v(this.container, curText);
    }
    else
    {
        if (String.isNullOrEmpty(title))
        {
            title = text;
        }
        if (String.isNullOrEmpty(target))
        {
            $v(this.container, "<a href=\"" + href + "\" title=\"" + title + "\">" + curText + "</a>");
        }
        else
        {
            $v(this.container, "<a href=\"" + href + "\" title=\"" + title + "\" target=\"" + target + "\">" + curText + "</a>");
        }
    }
    
    
    var me = this;
    if (this.characterIndex < this.items[this.itemIndex].text.length - 1)
    {
        this.characterIndex++;
        setTimeout(function (){ me.type(); }, this.speed);
    }
    else
    {
        this.characterIndex++;
        setTimeout(function (){ me.type(); }, this.screenDelay);
    }
};