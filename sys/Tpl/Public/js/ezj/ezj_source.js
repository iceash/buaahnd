/*
 * ezj v2.9.1
 * 版权所有 (C) 2008 - 2011 www.getezj.com
 * http://www.getezj.com/
 * 授权：MIT（不包含 ext 部分）
 * 反馈：yuyelin@163.com
 */


// Object


Object.is = function(v)
{
///<summary>判断 v 是否是 Object 类型。在这里 undefined、null、Date、Array 均不是 Object 类型。语法：Object.is(v)</summary>
///<param name="v" type="any">要判断的变量或表达式。</param>
///<returns type="boolean">v 是否是 Object 类型。</returns>
    // new Object()、new function()、{} 返回 true
    // function()、undefined、null、new Date()、[] 返回 false
    // 某些对象，比如 HTML 元素对象，IE 的判断和 Firefox、Chrome 的不同，IE 认为是 Object。
    
    var result = Object.prototype.toString.apply(v) === "[object Object]"; // 此处结果：Object、undefined、null 均为 true
    
    if (result)
    {
        if (v === undefined || v === null)
        {
            // 排除 undefined 和 null
            result = false;
        }
    }
    
    return result;
};


Object.prop = function(obj, propName)
{
///<summary>获取对象的属性值。语法：Object.prop(obj, propName[, defaultValue])</summary>
///<param name="obj" type="object">要获取属性值的对象。</param>
///<param name="propName" type="string">属性名称。</param>
///<param name="defaultValue" type="any">可选。当属性名称对应属性不存在时，返回 defaultValue。默认值为 null。</param>
///<returns type="any">属性的值。</returns>
    var defaultValue = Function.overload(2, null);
    
    if (obj === undefined)
    {
        return defaultValue;
    }
    else if (obj === null)
    {
        return defaultValue;
    }
    else if (eval("obj." + propName) === undefined)
    {
        return defaultValue;
    }
    return eval("obj." + propName);
};


//================================================================================


// Boolean


Boolean.is = function(v)
{
///<summary>判断 v 是否是 Boolean 类型。语法：Boolean.is(v)</summary>
///<param name="v" type="any">要判断的变量或表达式。</param>
///<returns type="boolean">v 是否是 Boolean 类型。</returns>
    return Object.prototype.toString.apply(v) === "[object Boolean]";
};


Boolean.from = function(v)
{
///<summary>将其他类型的值转换成 Boolean 类型。语法：Boolean.from(v)</summary>
///<param name="v" type="any">要转换的值。</param>
///<returns type="boolean">转换后的值。</returns>
    var result = false;
    
    // 其实除了字符串 true，其他都可用 v == true 来判断。
    if (String.is(v))
    {
        if (v.equalsIgnoreCase("true") || v == "1")
        {
            result = true;
        }
    }
    else if (Number.is(v))
    {
        // 数字 1 被认定为 true，其他数字被认定为 false
        if (v == 1)
        {
            return true;
        }
    }
    else if (v === true)
    {
        // 不能用 if (v) 这种形势来判断，因为字符串也会返回 true，所以这里使用了 v === true
        // 尽管前面已经截住了字符串，但这样做好些。
        result = true;
    }
    
    return result;
};


//================================================================================


// Number


Number.is = function(v)
{
///<summary>判断 v 是否是 Number 类型。在这里 NaN 不是 Number。语法：Number.is(v)</summary>
///<param name="v" type="any">要判断的变量或表达式。</param>
///<returns type="boolean">v 是否是 Number 类型。</returns>
    var result = Object.prototype.toString.apply(v) === "[object Number]";
    
    if (result && isNaN(v))
    {
        result = false;
    }
    
    return result;
};


Number.as = function(v)
{
///<summary>判断 v 是否是字符串类型的数字，零长度字符串不是数字。语法：Number.as(v)</summary>
///<param name="v" type="string">要判断的变量或表达式。</param>
///<returns type="boolean">v 是否是字符串类型的数字。</returns>
    var result = false;
    
    if (String.is(v))
    {
        // 首先要确保是字符串类型；
        // 其次要符合以下规则：
        // 一、字符串最前面可以出现一个负号，也可以出现一个正号，也可以都不出现，但不能都出现。
        // 二、然后是任意多个 0-9 的数字。
        // 三、然后是零个或一个小数点。
        // 四、然后是任意多个 0-9 的数字。
        // 五、第二和第四中至少需要出现一个 0-9 的数字。
        // 六、除此外不再出现其他字符。
        var reg = new RegExp("^[-+]{0,1}([0-9]{0,})[.]{0,1}([0-9]{0,})$", "gi");
        if (reg.test(v) && 
            (RegExp.$1 != "" || RegExp.$2 !=""))
        {
            result = true;
        }
    }
    
    return result;
};


Number.asInt = function(v)
{
///<summary>判断 v 是否是字符串类型的整数，零长度字符串不是整数。语法：Number.asInt(v)</summary>
///<param name="v" type="string">要判断的变量或表达式。</param>
///<returns type="boolean">v 是否是字符串类型的整数。</returns>
    var result = false;
    if (String.is(v))
    {
        // 首先要确保是字符串类型；
        // 其次要符合以下规则：
        // 一、字符串最前面可以出现一个负号，也可以出现一个正号，也可以都不出现，但不能都出现。
        // 二、然后是至少一个 0-9 的数字。
        // 三、除此外不再出现其他字符。
        var reg = new RegExp("^[-+]{0,1}[0-9]{1,}$", "gi");
        if (reg.test(v))
        {
            result = true;
        }
    }
    
    return result;
};


Number.asPureNumber = function(v)
{
///<summary>判断 v 是否是字符串类型的纯阿拉伯数字，零长度字符串不是纯阿拉伯数字。语法：Number.asPureNumber(v)</summary>
///<param name="v" type="string">要判断的变量或表达式。</param>
///<returns type="boolean">v 是否是字符串类型的纯阿拉伯数字。</returns>
    var result = false;
    
    if (String.is(v))
    {
        // 首先要确保是字符串类型；
        // 其次要符合以下规则：
        // 一、至少出现一个 0-9 的数字。
        // 二、除此外不再出现其他字符。
        var reg = new RegExp("^[0-9]{1,}$", "gi");
        if (reg.test(v))
        {
            result = true;
        }
    }
    
    return result;
};


Number.from = function(v)
{
///<summary>将字符串类型的数字转换成数字并返回。语法：Number.from(v[, defaultValue])</summary>
///<param name="v" type="string/number">要转换的变量或表达式。</param>
///<param name="defaultValue" type="number">可选。当无法转换时返回的默认值。默认值为 null。</param>
///<returns type="number">转换后的值。</returns>
    // 先经过 asInt、as 判断，避免像 11px 这样的字符串被 parseInt 转换成 11。
    if (Number.is(v))
    {
        return v;
    }
    
    if (Number.asInt(v))
    {
        return new Number(v);
    }
    else if (Number.as(v))
    {
        return new Number(v);
    }
    
    return Function.overload(1, null);
};


Number.from2 = function(numString)
{
///<summary>将字符串类型的二进制数字转换成十进制数字。语法：Number.from2(numString[, defaultValue])</summary>
///<param name="numString" type="string">要转换的字符串类型的二进制数字。</param>
///<param name="defaultValue" type="number">可选。当无法转换时返回的默认值。默认值为 null。</param>
///<returns type="integer">转换后的值。</returns>
    var v = parseInt(numString, 2);
    if (Number.is(v))
    {
        return v;
    }
    return Function.overload(1, null);
};


Number.from8 = function(numString)
{
///<summary>将字符串类型的八进制数字转换成十进制数字。语法：Number.from8(numString[, defaultValue])</summary>
///<param name="numString" type="string">要转换的字符串类型的八进制数字。</param>
///<param name="defaultValue" type="number">可选。当无法转换时返回的默认值。默认值为 null。</param>
///<returns type="integer">转换后的值。</returns>
    var v = parseInt(numString, 8);
    if (Number.is(v))
    {
        return v;
    }
    return Function.overload(1, null);
};


Number.from16 = function(numString)
{
///<summary>将字符串类型的十六进制数字转换成十进制数字。语法：Number.from16(numString[, defaultValue])</summary>
///<param name="numString" type="string">要转换的字符串类型的十六进制数字。</param>
///<param name="defaultValue" type="number">可选。当无法转换时返回的默认值。默认值为 null。</param>
///<returns type="integer">转换后的值。</returns>
    var v = parseInt(numString, 16);
    if (Number.is(v))
    {
        return v;
    }
    return Function.overload(1, null);
};


Number.prototype.to2 = function()
{
///<summary>将十进制数字转换成字符串类型的二进制数字。语法：number1.to2()</summary>
///<returns type="integer">字符串类型的二进制数字。</returns>
    return this.toString(2);
};


Number.prototype.to8 = function()
{
///<summary>将十进制数字转换成字符串类型的八进制数字。语法：number1.to8()</summary>
///<returns type="integer">字符串类型的八进制数字。</returns>
    return this.toString(8);
};


Number.prototype.to16 = function()
{
///<summary>将十进制数字转换成字符串类型的十六进制数字。语法：number1.to16()</summary>
///<returns type="integer">字符串类型的十六进制数字。</returns>
    return this.toString(16);
};


Number.prototype.format = function()
{
///<summary>对数字进行三位分节格式化并返回。语法：number1.format()</summary>
///<returns type="string">经三位分节格式化的字符串。</returns>
    var str = this.toString();
    
    var mark = ""; // 负号
    var intPart = ""; // 整数部分
    var intArr = [];
    var decPart = ""; // 小数部分，不进行三位分节
    
    // 取负号。数字.toString() 后，前面不会有正号。
    if (str.left(1) == "-")
    {
        mark = "-";
        str = str.substr(1);
    }
    
    // 取整数部分
    var pos = str.indexOf(".");
    if (pos >= 0)
    {
        intPart = str.substr(0, pos);
        decPart = str.substr(pos + 1);
    }
    else
    {
        intPart = str;
    }
    
    // 整数分节
    var i = intPart.length; // 上一个处理的位置
    while (i > 0)
    {
        intArr.unshift(intPart.substring(i-3, i)); // 在数组的顶端插入，此处 i-3 若 < 0，则相当于 0
        i -= 3;
    }
    
    // 合并返回
    if (decPart != "")
    {
        return mark + intArr.join(",") + "." + decPart;
    }
    return mark + intArr.join(",");
};


//================================================================================


// String


String.is = function(v)
{
///<summary>判断 v 是否是 String 类型。语法：String.is(v)</summary>
///<param name="v" type="any">要判断的变量或表达式。</param>
///<returns type="boolean">v 是否是 String 类型。</returns>
    return Object.prototype.toString.apply(v) === "[object String]";
};


String.isNullOrEmpty = function(v)
{
///<summary>判断 v 是否是 undefined 或 null 或零长度字符串。语法：String.isNullOrEmpty(v)</summary>
///<param name="v" type="any">要判断的变量或表达式。</param>
///<returns type="boolean">v 是否是 undefined 或 null 或零长度字符串。</returns>
    return (v === undefined || v === null || v === "");
};


String.prototype.equalsIgnoreCase = function(str)
{
///<summary>忽略大小写比较两个字符串是否相等并返回。语法：string1.equalsIgnoreCase(str)</summary>
///<param name="str" type="string">要参与比较的另一字符串。</param>
///<returns type="boolean">字符串是否忽略大小写相等。</returns>
    return (this.toUpperCase() == str.toUpperCase());
};


String.prototype.startsWith = function(value)
{
///<summary>判断字符串是否以 value 开头。语法：string1.startsWith(value[, ignoreCase])</summary>
///<param name="value" type="string">用来比较的字符串。</param>
///<param name="ignoreCase" type="boolean">可选。是否忽略大小写。默认值为 false。</param>
///<returns type="boolean">是否以 value 开头。</returns>
    var ignoreCase = Function.overload(1, false);
    var str = (!ignoreCase ? this : this.toLowerCase());
    value = (!ignoreCase ? value : value.toLowerCase());
    return str.left(value.length) == value;
};


String.prototype.endsWith = function(value)
{
///<summary>判断字符串是否以 value 结尾。语法：string1.endsWith(value[, ignoreCase])</summary>
///<param name="value" type="string">用来比较的字符串。</param>
///<param name="ignoreCase" type="boolean">可选。是否忽略大小写。默认值为 false。</param>
///<returns type="boolean">是否以 value 结尾。</returns>
    var ignoreCase = Function.overload(1, false);
    var str = (!ignoreCase ? this : this.toLowerCase());
    value = (!ignoreCase ? value : value.toLowerCase());
    return str.right(value.length) == value;
};


String.prototype.wChrs = function()
{
///<summary>获取字符串中 Unicode 值大于 255 的字符个数。语法：string1.wChrs()</summary>
///<returns type="integer">字符串中 Unicode 值大于 255 的字符个数。</returns>
    var result = 0;
    
    for (var i = 0; i < this.length; i++)
    {
        if (this.charCodeAt(i) > 255)
        {
            result++;
        }
    }
    
    return result;
};


String.prototype.left = function(length)
{
///<summary>获取字符串左边 length 长度的子字符串。语法：string1.left(length)</summary>
///<param name="length" type="integer">要获取的子字符串长度。</param>
///<returns type="string">字符串左边 length 长度的子字符串。</returns>
    return this.substr(0, length); // length 超过字符串长度则取整个字符串
};


String.prototype.right = function(length)
{
///<summary>获取字符串右边 length 长度的子字符串。语法：string1.right(length)</summary>
///<param name="length" type="integer">要获取的子字符串长度。</param>
///<returns type="string">字符串右边 length 长度的子字符串。</returns>
    var start = this.length - length;
    // Firefox、Chrome 中，start < 0，则 substr 表示从右边多少个字符（第二参数无效）。
    // IE 中则效果同 0
    if (start < 0)
    {
        start = 0;
    }
    
    return this.substr(start, length); // length 超过字符串长度则取整个字符串
};


String.prototype.mid = function(start)
{
///<summary>从指定位置开始获取特定长度的子字符串，或获取指定位置到特定字符串位置的子字符串。语法：string1.mid(start[, lengthOrSubStr])</summary>
///<param name="start" type="integer">开始位置。</param>
///<param name="lengthOrSubStr" type="integer/string">可选。要获取的特定长度，或用来标明位置的字符串。省略表示开始位置之后的全部字符串。</param>
///<returns type="string">子字符串。</returns>
    if (start < 0)
    {
        start = 0;
    }
    
    if (arguments.length < 2)
    {
        return this.substr(start);
    }
    
    var lengthOrSubStr = arguments[1];
    var length = lengthOrSubStr;
    if (!Number.is(lengthOrSubStr))
    {
        length = this.indexOf(lengthOrSubStr, start) - start;
    }
    
    return this.substr(start, length);
};


String.prototype.trimLeft = function()
{
///<summary>获取移除了字符串左端的半角和全角空格之后的字符串。语法：string1.trimLeft([trimRN])</summary>
///<param name="trimRN" type="boolean">可选。是否移除回车或换行符。默认值为 false。</param>
///<returns type="string">移除了字符串左端的半角和全角空格之后的字符串。</returns>
    var trimRN = Function.overload(0, false);
    if (trimRN)
    {
        // \s 即 [ \f\n\r\t\v]，实际上 Firefox、Chrome 中，\s 等效于 [ 　\f\n\r\t\v]，多了个全角空格。
        return this.replace(/^[　\s]+/gi, ""); // 没有指定 m，故整个字符串当作一行
    }
    return this.replace(/^[ 　]+/gi, ""); // 没有指定 m，故整个字符串当作一行
};


String.prototype.trimRight = function()
{
///<summary>获取移除了字符串右端的半角和全角空格之后的字符串。语法：string1.trimRight([trimRN])</summary>
///<param name="trimRN" type="boolean">可选。是否移除回车或换行符。默认值为 false。</param>
///<returns type="string">移除了字符串右端的半角和全角空格之后的字符串。</returns>
    var trimRN = Function.overload(0, false);
    if (trimRN)
    {
        // \s 即 [ \f\n\r\t\v]，实际上 Firefox、Chrome 中，\s 等效于 [ 　\f\n\r\t\v]，多了个全角空格。
        return this.replace(/[　\s]+$/gi, ""); // 没有指定 m，故整个字符串当作一行
    }
    return this.replace(/[ 　]+$/gi, ""); // 没有指定 m，故整个字符串当作一行
};


String.prototype.trim = function()
{
///<summary>获取移除了字符串两端的半角和全角空格之后的字符串。语法：string1.trim([trimRN])</summary>
///<param name="trimRN" type="boolean">可选。是否移除回车或换行符。默认值为 false。</param>
///<returns type="string">移除了字符串两端的半角和全角空格之后的字符串。</returns>
    var trimRN = Function.overload(0, false);
    return this.trimLeft(trimRN).trimRight(trimRN);
};


String.prototype.padLeft = function(totalWidth, chr)
{
///<summary>向字符串左端追加一定数量的字符并返回。语法：string1.padLeft(totalWidth, chr)</summary>
///<param name="totalWidth" type="integer">追加字符后要达到的总长度。</param>
///<param name="chr" type="character">要追加的字符，在这里也就是一个长度的字符串。</param>
///<returns type="string">追加字符后的字符串。</returns>
    var str = "";
    for (var i = 0; i < totalWidth - this.length; i++)
    {
        str += chr;
    }

    return str + this;
};


String.prototype.padRight = function(totalWidth, chr)
{
///<summary>向字符串右端追加一定数量的字符并返回。语法：string1.padRight(totalWidth, chr)</summary>
///<param name="totalWidth" type="integer">追加字符后要达到的总长度。</param>
///<param name="chr" type="character">要追加的字符，在这里也就是一个长度的字符串。</param>
///<returns type="string">追加字符后的字符串。</returns>
    var str = "";
    for (var i = 0; i < totalWidth - this.length; i++)
    {
        str += chr;
    }

    return this + str;
};


String.prototype.removeHtml = function()
{
///<summary>移除字符串中的 HTML 标签并返回。语法：string1.removeHtml()</summary>
///<returns type="string">移除了 HTML 标签的字符串。</returns>
    return this.replace(/<(.|\n)+?>/gi, "");
};


String.prototype.replaceAll = function(find, replacement)
{
///<summary>替换字符串中的所有已找到内容并返回。正则表达式中的匹配符将生效。语法：string1.replaceAll(find, replacement[, ignoreCase])</summary>
///<param name="find" type="string">要查找的子字符串。</param>
///<param name="replacement" type="string">要替换的子字符串。</param>
///<param name="ignoreCase" type="boolean">可选。是否忽略大小写，默认值为 false。</param>
///<returns type="string">替换后的字符串。</returns>
/*
var str = "a123b";
alert(str.replaceAll("\\d", "")); // 这里结果为 ab
*/
    var ignoreCase = Function.overload(2, false);
    var reg = new RegExp(find, "gm" + (ignoreCase ? "i" : ""));
    return this.replace(reg, replacement);
};


String.prototype.splitBefore = function(separator, count)
{
///<summary>只对字符串前面一定数量的分隔符进行字符串分隔并返回。语法：string1.splitBefore(separator, count)</summary>
///<param name="separator" type="string">分割符。</param>
///<param name="count" type="integer">指定字符串前面多少个分隔符有效。</param>
///<returns type="array">分割后的字符串数组。</returns>
    var result = [];
    
    var arr = this.split(separator);
    for (var i = 0; i <= count && i < arr.length; i++)
    {
        result.push(arr[i]);
    }
    // 把分隔出来的多余项全部合并到最后一项中
    for (var i = count + 1; i < arr.length; i++)
    {
        result[result.length - 1] += separator + arr[i];
    }
    
    return result;
};


String.prototype.splitEx = function(separator, trimItem)
{
///<summary>增强功能的 split。语法：string1.splitEx(separator, trimItem[, removeEmptyItem])</summary>
///<param name="separator" type="string">分割符。</param>
///<param name="trimItem" type="boolean">是否对分隔后的每一项进行 trim 操作。</param>
///<param name="removeEmptyItem" type="boolean">可选。是否删除分隔后的空项，某项可能不是空项但 trim 后可能变为空项。默认值为 false。</param>
///<returns type="array">分割后的字符串数组。</returns>
    var removeEmptyItem = Function.overload(2, false);
    
    var arr = this.split(separator);
    var i = 0;
    while (i < arr.length)
    {
        if (trimItem)
        {
            arr[i] = arr[i].trim();
        }
        if (removeEmptyItem && arr[i] == "")
        {
            arr.splice(i, 1);
        }
        else
        {
            i++;
        }
    }
    
    return arr;
};


//================================================================================


// Array


Array.is = function(v)
{
///<summary>判断 v 是否是 Array 类型。语法：Array.is(v)</summary>
///<param name="v" type="any">要判断的变量或表达式。</param>
///<returns type="boolean">v 是否是 Array 类型。</returns>
    return Object.prototype.toString.apply(v) === "[object Array]";
};


Array.from = function(v)
{
///<summary>将 v 转换或包装成数组，若 v 本身就是数组，则直接返回 v。语法：Array.from(v)</summary>
///<param name="v" type="any">要转换或包装的变量或表达式。</param>
///<returns type="array">转换或包装后的值。</returns>
    if (v === undefined || v === null)
    {
        return [];
    }
    else if (v.tagName && v.tagName.equalsIgnoreCase("FORM"))
    {
        // form 也有 length 属性，故要除开。
        return [v];
    }
    else if (!Array.is(v))
    {
        if(typeof v.length != "undefined" && !String.is(v) && typeof v.options == "undefined")
        {
            // List 一类的东西
            // Firefox 可用 typeof 判断这类东西，但 IE 却总返回 Object 类型，所以只好不用 typeof 或类似的方法判断
            var arr = [];
            for (var i = 0; i < v.length; i++)
            {
                arr.push(v[i]);
            }
            return arr;
        }
        else
        {
            return [v];
        }
    }
    return v;
};


Array.map = function(arr1, arr2, arr1ItemValue, defaultValue)
{
///<summary>根据 arr1ItemValue 查找其在 arr1 中的索引值，再根据索引值返回 arr2 中对应的值。若不存在，则返回 defaultValue。语法：Array.map(arr1, arr2, arr1ItemValue, defaultValue)</summary>
///<param name="arr1" type="array">对应数组 1。</param>
///<param name="arr2" type="array">对应数组 2。</param>
///<param name="arr1ItemValue" type="any">要在数组 1 中查找索引值的值。</param>
///<param name="defaultValue" type="any">找不到对应值时返回的默认值。</param>
///<returns type="any">arr2 的对应值。</returns>
    var result = defaultValue;
    
    var arr1Index = -1;
    for (var i = 0; i < arr1.length; i++)
    {
        if (arr1[i] == arr1ItemValue)
        {
            arr1Index = i;
            break;
        }
    }
    
    if (arr1Index >= 0)
    {
        result = arr2[arr1Index];
    }
    
    return result;
};


Array.prototype.copy = function()
{
///<summary>将当前数组对象复制为一个新的对象并返回。语法：array1.copy()</summary>
///<returns type="array">新的数组对象。</returns>
    return this.mid(0);
};


Array.prototype.exists = function(v)
{
///<summary>查找 v 是否在数组中存在并返回。语法：array1.exists(v[, ignoreCase])</summary>
///<param name="v" type="any">要查找的变量或表达式。可以是数组类型。</param>
///<param name="ignoreCase" type="boolean">可选。是否忽略大小写，默认值为 false。</param>
///<returns type="boolean">v 是否在数组中存在，若 v 是数组，则数组中的元素必须全部在当前数组中存在才为 true。</returns>
    var result = false;
    
    var ignoreCase = Function.overload(1, false);
    
    if (Array.is(v))
    {
        // v 是数组的情况
        result = true;
        for (var i = 0; i < v.length; i++)
        {
            if (!this.exists(v[i], ignoreCase))
            {
                result = false;
                break;
            }
        }
        return result; // 注意这里返回了
    }
    
    for (var i = 0; i < this.length; i++)
    {
        if (!ignoreCase)
        {
            if (this[i] == v)
            {
                result = true;
                break;
            }
        }
        else
        {
            // 如果是数字，则调用 toUpperCase 会出错。
            var item = this[i].toString();
            if (item.toUpperCase() == v.toUpperCase())
            {
                result = true;
                break;
            }
        }
    }
    
    return result;
};


Array.prototype.checkRepeat = function()
{
///<summary>检查数组中是否存在重复值。语法：array1.checkRepeat()</summary>
///<returns type="boolean">是否存在重复值。</returns>
    for (var i = 0; i < this.length - 1; i++)
    {
        for (var j = i + 1; j < this.length; j++)
        {
            if (this[i] == this[j])
            {
                return true;
            }
        }
    }

    return false;
};


Array.prototype.min = function()
{
///<summary>获取数组中的最小值。语法：array1.min()</summary>
///<returns type="number">数组中的最小值。</returns>
    var min = this[0];
    for (var i = 1; i < this.length; i++)
    {
        if (this[i] < min)
        {
            min = this[i];
        }
    }

    return min;
};


Array.prototype.max = function()
{
///<summary>获取数组中的最大值。语法：array1.max()</summary>
///<returns type="number">数组中的最大值。</returns>
    var max = this[0];
    for (var i = 1; i < this.length; i++)
    {
        if (this[i] > max)
        {
            max = this[i];
        }
    }

    return max;
};


Array.prototype.sum = function()
{
///<summary>获取数组中各项的总和。语法：array1.sum()</summary>
///<returns type="number">数组中各项的总和。</returns>
    var t = 0;
    for (var i = 0; i < this.length; i++)
    {
        t += this[i];
    }
    
    return t;
};


Array.prototype.avg = function()
{
///<summary>获取数组中各项的平均值。语法：array1.avg()</summary>
///<returns type="number">数组中各项的平均值。</returns>
    return this.sum() / this.length;
};


Array.prototype.mid = function(start)
{
///<summary>从指定位置获取特定长度的数组。语法：array1.mid(start[, length])</summary>
///<param name="start" type="integer">开始的数组索引。</param>
///<param name="length" type="integer">可选。要获取的数组长度。省略则表示开始位置之后的所有项目。</param>
///<returns type="array">获取的子数组。</returns>
    if (arguments.length >= 2)
    {
        return this.slice(start, start + arguments[1]);
    }
    return this.slice(start)
};


Array.prototype.del = function(start)
{
///<summary>删除数组的指定部分。语法：array1.del(start[, length])</summary>
///<param name="start" type="integer">开始的数组索引。</param>
///<param name="length" type="integer">可选。要删除的数组长度。默认值为 1。</param>
    var length = Function.overload(1, 1);
    this.splice(start, length);
};


Array.prototype.clear = function()
{
///<summary>清空数组的所有项。语法：array1.clear()</summary>
    this.splice(0, this.length);
};


Array.prototype.each = function(func)
{
///<summary>对数组的所有项执行 func 函数，若 func 有返回值，则将返回值组成数组后返回。语法：array1.each(func[, expand])</summary>
///<param name="func" type="function">要执行的函数，数组当前项的值和索引通过参数传递给 func，同时传入一个 info，用来在 func 中中断 each。</param>
///<param name="expand" type="boolean">可选。若 func 执行结果是数组，则该值决定是否展开数组再返回给 each 结果。默认值为 false。</param>
///<returns type="array">各项执行 func 后，func 结果组成的数组。</returns>
    var expand = Function.overload(1, false);
    
    var result = [];
    
    var info = {};
    info.breakEach = false; // 是否中断 each 中的 for，info.breakEach 被 func 中赋值为 true，即可中断
    for (var i = 0; i < this.length; i++)
    {
        var e = func(this[i], i, info);
        if (Array.is(e) && expand)
        {
            // func 结果是数组，并且要求把数组展开加到结果数组中。
            result = result.concat(e);
        }
        else
        {
            result.push(e);
        }
        if (info.breakEach)
        {
            break;
        }
    }

    return result;
};


Array.prototype.fetch = function(propName)
{
///<summary>针对项目为对象的数组，取对象中指定的属性值组成新的数组并返回。语法：array1.fetch(propName)</summary>
///<param name="propName" type="string">要取值的属性名称。</param>
///<returns type="array">指定属性的值组成的数组。</returns>
    var result = [];
    
    for (var i = 0; i < this.length; i++)
    {
        result.push(eval("this[" + i + "]." + propName));
    }
    
    return result;
};


Array.prototype.z = function(size, index)
{
///<summary>获取数组中“斑马”项，再组成数组并返回。语法：array1.z(size, index)</summary>
///<param name="size" type="integer">“斑马”值。</param>
///<param name="index" type="integer">要返回的项的索引值。</param>
///<returns type="array">子数组。</returns>
    var result = [];
    
    for (var i = 0; i < this.length; i++)
    {
        if (i % size == index)
        {
            // 0 % 0 为 NaN
            result.push(this[i]);
        }
    }
    
    return result;
};


Array.prototype.and = function(arr)
{
///<summary>获取两个数组中均存在的项，组成新的数组并返回。语法：array1.and(arr)</summary>
///<param name="arr" type="array">参与运算的数组。</param>
///<returns type="array">均存在的项组成数组。</returns>
    var result = [];
    
    for (var i = 0; i < this.length; i++)
    {
        if (arr.exists(this[i]))
        {
            result.push(this[i]);
        }
    }
    
    return result;
};
//================================================================================


// Date


Date.is = function(v)
{
///<summary>判断 v 是否是 Date 类型。语法：Date.is(v)</summary>
///<param name="v" type="any">要判断的变量或表达式。</param>
///<returns type="boolean">v 是否是 Date 类型。</returns>
    return Object.prototype.toString.apply(v) === "[object Date]";
};


Date.asDate = function(str)
{
///<summary>判断 str 是否是符合“年-月-日”日期格式的字符串，或判断指定的年月日是否是日期。语法：Date.asDate(str) 或 Date.asDate(year, month, day)</summary>
///<param name="str" type="string">要判断的变量或表达式。</param>
///<returns type="boolean">str 是否是符合日期格式的字符串。</returns>
    // 指定年月日判断
    if (arguments.length >= 3)
    {
        if (arguments[0] >= 0 && arguments[0] <= 9999 &&
            arguments[1] >= 1 && arguments[1] <= 12 &&
            arguments[2] >= 1 && arguments[2] <= Date.daysInMonth(arguments[0], arguments[1]))
        {
            return true;
        }
        return false;
    }
    
    
    // 指定字符串判断
    var result = false;
    
    var arr = str.split("-");
    if (arr.length == 3 &&
        Number.asPureNumber(arr[0]) &&
        Number.asPureNumber(arr[1]) &&
        Number.asPureNumber(arr[2]))
    {
        result = Date.asDate(Number.from(arr[0]),
                             Number.from(arr[1]),
                             Number.from(arr[2]));
    }

    return result;
};


Date.asTime = function(str)
{
///<summary>判断 str 是否是符合“时:分:秒”或“时:分:秒.毫”时间格式的字符串，或判断指定的时分秒是否是时间，或判断指定的时分秒毫是否是时间。语法：Date.asTime(str) 或 Date.asTime(hour, minute, second[, millisecond])</summary>
///<param name="str" type="string">要判断的变量或表达式。</param>
///<returns type="boolean">str 是否是符合时间格式的字符串。</returns>
    // 指定时分秒或时分秒毫判断
    if (arguments.length >= 3)
    {
        if (arguments[0] >= 0 && arguments[0] <= 23 &&
            arguments[1] >= 0 && arguments[1] <= 59 &&
            arguments[2] >= 0 && arguments[2] <= 59)
        {
            if (arguments.length >= 4)
            {
                // 指定了毫秒
                return (arguments[3] >= 0 && arguments[3] <= 999);
            }
            else
            {
                return true;
            }
        }
        return false;
    }
    
    
    // 指定字符串判断
    var result = false;
    
    var arr = str.split(":");
    if (arr.length == 3 &&
        Number.asPureNumber(arr[0]) &&
        Number.asPureNumber(arr[1]))
    {
        // 先验证时、分
        arr[0] = Number.from(arr[0]);
        arr[1] = Number.from(arr[1]);
        if (arr[0] >= 0 && arr[0] <= 23 &&
            arr[1] >= 0 && arr[1] <= 59)
        {
            if (Number.asPureNumber(arr[2]))
            {
                // 无毫秒
                arr[2] = Number.from(arr[2]);
                result = (arr[2] >= 0 && arr[2] <= 59);
            }
            else
            {
                // 可能有毫秒
                var arr2 = arr[2].split(".");
                if (arr2.length == 2 &&
                    Number.asPureNumber(arr2[0]) &&
                    Number.asPureNumber(arr2[1]))
                {
                    arr2[0] = Number.from(arr2[0]);
                    arr2[1] = Number.from(arr2[1]);
                    result = (arr2[0] >= 0 && arr2[0] <= 59 && arr2[1] >= 0 && arr2[1] <= 999);
                }
            }
        }
    }
    
    return result;
};


Date.asDateTime = function(str)
{
///<summary>判断 str 是否是符合日期时间格式的字符串，或判断指定的年月日时分秒或年月日时分秒毫是否是日期时间。语法：Date.asDateTime(str) 或 Date.asDateTime(year, month, day, hour, minute, second[, millisecond])</summary>
///<param name="str" type="string">要判断的变量或表达式。</param>
///<returns type="boolean">str 是否是符合日期时间格式的字符串。</returns>
    // 指定年月日时分秒或年月日时分秒毫判断
    if (arguments.length == 6)
    {
        if (Date.asDate(arguments[0], arguments[1], arguments[2]) &&
            Date.asTime(arguments[3], arguments[4], arguments[5]))
        {
            return true;
        }
        return false;
    }
    else if (arguments.length == 7)
    {
        if (Date.asDate(arguments[0], arguments[1], arguments[2]) &&
            Date.asTime(arguments[3], arguments[4], arguments[5], arguments[6]))
        {
            return true;
        }
        return false;
    }
    
    
    // 指定字符串判断
    var result = false;
    
    var arr = str.split(" ");
    if (arr.length == 2 &&
        Date.asDate(arr[0]) &&
        Date.asTime(arr[1]))
    {
        result = true;
    }
    
    return result;
};


Date.from = function(v)
{
///<summary>将字符串类型的日期或日期时间转换成日期时间并返回，或根据指定的年月日、年月日时分秒、年月日时分秒毫创建日期时间。语法：Date.from(v) 或 Date.from(year, month, day[, hour, minute, second[, millisecond]]) </summary>
///<param name="v" type="string/datetime">要转换的变量或表达式。</param>
///<returns type="datetime">转换后的值。</returns>
    if (Date.is(v))
    {
        return v;
    }
    
    if (arguments.length >= 7)
    {
        // 年月日时分秒毫
        if (Date.asDateTime(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4], arguments[5], arguments[6]))
        {
            return new Date(arguments[0], arguments[1] - 1, arguments[2], arguments[3], arguments[4], arguments[5], arguments[6]);
        }
        return null;
    }
    else if (arguments.length >= 6)
    {
        // 年月日时分秒
        if (Date.asDateTime(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]))
        {
            return new Date(arguments[0], arguments[1] - 1, arguments[2], arguments[3], arguments[4], arguments[5]);
        }
        return null;
    }
    else if (arguments.length >= 3)
    {
        // 仅年月日
        if (Date.asDate(arguments[0], arguments[1], arguments[2]))
        {
            return new Date(arguments[0], arguments[1] - 1, arguments[2]); // new Date 的第二个参数月是 0 开始
        }
        return null;
    }
    
    
    var result = null;
    
    var dField = null;
    var tField = null;
    if (Date.asDateTime(v))
    {
        var arr = v.split(" ");
        dField = arr[0];
        tField = arr[1];
    }
    else if (Date.asDate(v))
    {
        dField = v;
    }
    else if (Date.asTime(v))
    {
        tField = v;
    }
    
    var y = null; // 年
    var m = null; // 月
    var d = null; // 日
    var h = null; // 时
    var n = null; // 分
    var s = null; // 秒
    var l = null; // 毫
    
    if (dField != null)
    {
        // 因前面 dField 已经通过了 Date.asDate 判断，所以各段格式肯定是正确的。
        var dArr = dField.split("-");
        y = Number.from(dArr[0]);
        m = Number.from(dArr[1]);
        d = Number.from(dArr[2]);
    }
    
    if (tField != null)
    {
        // 因前面 tField 已经通过了 Date.asTime 判断，所以各段格式肯定是正确的。
        var tArr = tField.split(":");
        h = Number.from(tArr[0]);
        n = Number.from(tArr[1]);
        if (Number.asPureNumber(tArr[2]))
        {
            // 无毫秒
            s = Number.from(tArr[2]);
        }
        else
        {
            // 有毫秒，
            var arr2 = tArr[2].split(".");
            s = Number.from(arr2[0]);
            l = Number.from(arr2[1]);
        }
    }
    
    if (y != null && m != null && d != null &&
        h != null && n != null && s != null)
    {
        // 日期时间
        if (l == null)
        {
            // 无毫秒
            result = new Date(y, m - 1, d, h, n, s);
        }
        else
        {
            // 有毫秒
            result = new Date(y, m - 1, d, h, n, s, l);
        }
    }
    else if (y != null && m != null && d != null)
    {
        // 仅日期
        result = new Date(y, m - 1, d);
    }
    
    return result;
};


Date.isLeapYear = function(year)
{
///<summary>判断指定的年份是否是闰年。语法：Date.isLeapYear(year)</summary>
///<param name="year" type="integer">要进行判断的年份。</param>
///<returns type="boolean">当前日期所在年是否是闰年。</returns>
    return ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0);
};


Date.daysInMonth  = function(year, month)
{
///<summary>获取指定年的指定月有多少天。语法：Date.daysInMonth(year, month)</summary>
///<param name="year" type="integer">指定的年</param>
///<param name="month" type="integer">指定的月。</param>
///<returns type="integer">指定年的指定月的天数。</returns>
    var daysCount = 0;
    if (month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12)
    {
        daysCount = 31;
    }
    else if (month == 4 || month == 6 || month == 9 || month == 11)
    {
        daysCount = 30;
    }
    else if (month == 2)
    {
        if (!Date.isLeapYear(year))
        {
            daysCount = 28;
        }
        else
        {
            daysCount = 29;
        }
    }

    return daysCount;
};


Date.compare = function(dt1, dt2)
{
///<summary>比较 dt1、dt2 的早晚并返回比较值。语法：Date.compare(dt1, dt2)</summary>
///<param name="dt1" type="string/datetime">参与比较的第一个日期。可以是日期格式的字符串。</param>
///<param name="dt2" type="string/datetime">参与比较的第二个日期。可以是日期格式的字符串。</param>
///<returns type="integer">若 dt1 早于 dt2，则为 -1；若 dt1 晚于 dt2，则为 1；否则为 0。</returns>
    dt1 = Date.from(dt1);
    dt2 = Date.from(dt2);
    
    if (dt1.y() > dt2.y())
    {
        return 1;
    }
    else if (dt1.y() < dt2.y())
    {
        return -1;
    }
    
    if (dt1.m() > dt2.m())
    {
        return 1;
    }
    else if (dt1.m() < dt2.m())
    {
        return -1;
    }
    
    if (dt1.d() > dt2.d())
    {
        return 1;
    }
    else if (dt1.d() < dt2.d())
    {
        return -1;
    }
    
    if (dt1.h() > dt2.h())
    {
        return 1;
    }
    else if (dt1.h() < dt2.h())
    {
        return -1;
    }
    
    if (dt1.n() > dt2.n())
    {
        return 1;
    }
    else if (dt1.n() < dt2.n())
    {
        return -1;
    }
    
    if (dt1.s() > dt2.s())
    {
        return 1;
    }
    else if (dt1.s() < dt2.s())
    {
        return -1;
    }
    
    if (dt1.l() > dt2.l())
    {
        return 1;
    }
    else if (dt1.l() < dt2.l())
    {
        return -1;
    }
    
    return 0;
};


Date.differ = function(dt1, dt2)
{
///<summary>dt1 减去 dt2，并返回一个 timespan。语法：Date.differ(dt1, dt2)</summary>
///<param name="dt1" type="string/datetime">参与相减的第一个日期。可以是日期格式的字符串。</param>
///<param name="dt2" type="string/datetime">参与相减的第二个日期。可以是日期格式的字符串。</param>
///<returns type="timespan">相减后的 timespan。</returns>
    if (String.is(dt1))
    {
        dt1 = Date.from(dt1);
    }
    if (String.is(dt2))
    {
        dt2 = Date.from(dt2);
    }
    
    var total1 = dt1.total(); // 折算成毫秒
    var total2 = dt2.total(); // 折算成毫秒
    var m = total1 - total2; // 毫秒相差
    
    var result = {};
    
    result.totalMilliseconds = m;
    
    result.totalDays = m / 86400000; // 86400000 = 24 * 60 * 60 * 1000
    result.totalHours = m / 3600000; // 3600000 = 60 * 60 * 1000
    result.totalMinutes = m / 60000; // 60000 = 60 * 1000
    result.totalSeconds = m / 1000;
    
    var r = m;
    result.days = parseInt(r / 86400000);
    r -= result.days * 86400000;
    result.hours = parseInt(r / 3600000);
    r -= result.hours * 3600000;
    result.minutes = parseInt(r / 60000);
    r -= result.minutes * 60000;
    result.seconds = parseInt(r / 1000);
    
    return result;
};


Date.prototype.copy = function()
{
///<summary>将当前日期时间对象复制为一个新的对象并返回。语法：datetime1.copy()</summary>
///<returns type="datetime">新的日期时间对象。</returns>
    return Date.from(this.y(), this.m(), this.d(), this.h(), this.n(), this.s(), this.l());
};


Date.prototype.format = function()
{
///<summary>格式化当前对象并返回。语法：datetime1.format([formatStr])</summary>
///<param name="formatStr" type="string">可选。格式化字符串，默认值为 yyyy-mm-dd hh:nn:ss。</param>
///<returns type="string">格式化后的字符串。</returns>
    var formatStr = Function.overload(0, "yyyy-mm-dd hh:nn:ss");
    
    var yyyy = this.y().toString();
    var yy = yyyy.right(2);
    var m = this.m().toString();
    var mm = m.padLeft(2, "0");
    var d = this.d().toString();
    var dd = d.padLeft(2, "0");
    var h = this.h().toString();
    var hh = h.padLeft(2, "0");
    var n = this.n().toString();
    var nn = n.padLeft(2, "0");
    var s = this.s().toString();
    var ss = s.padLeft(2, "0");
    var l = this.l().toString();
    var ll = l.padLeft(3, "0");
    var lll = ll;
    
    var result = formatStr;
    result = result.replaceAll("yyyy", yyyy);
    result = result.replaceAll("yy", yy);
    result = result.replaceAll("mm", mm);
    result = result.replaceAll("m", m);
    result = result.replaceAll("dd", dd);
    result = result.replaceAll("d", d);
    result = result.replaceAll("hh", hh);
    result = result.replaceAll("h", h);
    result = result.replaceAll("nn", nn);
    result = result.replaceAll("n", n);
    result = result.replaceAll("ss", ss);
    result = result.replaceAll("s", s);
    result = result.replaceAll("lll", lll);
    result = result.replaceAll("ll", ll);
    result = result.replaceAll("l", l);
    return result;
};


Date.prototype.isLeapYear = function()
{
///<summary>判断日期对象所在年是否是闰年。语法：datetime1.isLeapYear()</summary>
///<returns type="boolean">当前日期所在年是否是闰年。</returns>
    return Date.isLeapYear(this.y());
};


Date.prototype.daysInMonth  = function()
{
///<summary>获取日期对象所在月有多少天。语法：datetime1.daysInMonth()</summary>
///<returns type="integer">当前日期所在月的天数。</returns>
    return Date.daysInMonth(this.y(), this.m());
};


Date.prototype.y = function()
{
///<summary>获取年。等效于 getFullYear()。语法：datetime1.y()</summary>
///<returns type="integer">四位数年。</returns>
    return this.getFullYear();
};


Date.prototype.m = function()
{
///<summary>获取月。等效于 getMonth() + 1。语法：datetime1.m()</summary>
///<returns type="integer">月。一月为 1。</returns>
    return this.getMonth() + 1;
};


Date.prototype.d = function()
{
///<summary>获取日。等效于 getDate()。语法：datetime1.d()</summary>
///<returns type="integer">日。</returns>
    return this.getDate();
};


Date.prototype.h = function()
{
///<summary>获取时。等效于 getHours()。语法：datetime1.h()</summary>
///<returns type="integer">时。</returns>
    return this.getHours();
};


Date.prototype.n = function()
{
///<summary>获取分。等效于 getMinutes()。语法：datetime1.n()</summary>
///<returns type="integer">分。</returns>
    return this.getMinutes();
};


Date.prototype.s = function()
{
///<summary>获取秒。等效于 getSeconds()。语法：datetime1.s()</summary>
///<returns type="integer">秒。</returns>
    return this.getSeconds();
};


Date.prototype.l = function()
{
///<summary>获取毫秒。等效于 getMilliseconds()。语法：datetime1.l()</summary>
///<returns type="integer">毫秒。</returns>
    return this.getMilliseconds();
};


Date.prototype.w = function()
{
///<summary>获取星期。语法：datetime1.w()</summary>
///<returns type="integer">星期。星期一为 1，星期日为 7。</returns>
    var result = this.getDay();
    if (result == 0)
    {
        result = 7;
    }
    
    return result;
};


Date.prototype.total = function()
{
///<summary>将日期时间折算成毫秒并返回。语法：datetime1.total()</summary>
///<returns type="integer">折算后的毫秒数</returns>
    var result = 0;
    var y = this.y();
    var m = this.m();
    
    for (var i = 1; i < y; i++)
    {
        if (!Date.isLeapYear(i))
        {
            result += 31536000000; // 31536000000 = 365 * 24 * 3600 * 1000
        }
        else
        {
            result += 31622400000; // 31622400000 = 366 * 24 * 3600 * 1000
        }
    }
    for (var i = 1; i < m; i++)
    {
        result += Date.daysInMonth(y, i) * 86400000; // 86400000 = 24 * 3600 * 1000
    }
    result += (this.d() - 1) * 86400000; // 86400000 = 24 * 3600 * 1000
    result += this.h() * 3600000 + this.n() * 60000 + this.s() * 1000 + this.l(); // 时与分均是从 0 开始计数，故这里不减 1。
    
    return result;
};


Date.prototype.addYears = function(years)
{
///<summary>添加年数并返回新的日期时间对象，该方法并不影响当前对象。语法：datetime1.addYears(years)</summary>
///<param name="years" type="integer">要添加的年数。</param>
///<returns type="datetime">添加了年数的新的日期时间对象。</returns>
    var y = this.y() + years;
    var m = this.m();
    var d = this.d();
    var maxD = Date.daysInMonth(y, m);
    if (d > maxD)
    {
        d = maxD;
    }
    
    return Date.from(y, m, d, this.h(), this.n(), this.s(), this.l());
};


Date.prototype.addMonths = function(months)
{
///<summary>添加月数并返回新的日期时间对象，该方法并不影响当前对象。语法：datetime1.addMonths(months)</summary>
///<param name="months" type="integer">要添加的月数。</param>
///<returns type="datetime">添加了月数的新的日期时间对象。</returns>
    var y = this.y();
    var m = this.m();
    var d = this.d();
    
    // 由于 months 为负数的情况下，可能需要向年份借位，故采用这种计算方法。
    var ms = y * 12 + m + months;
    y =  parseInt(ms / 12);
    m = ms % 12;
    
    var maxD = Date.daysInMonth(y, m);
    if (d > maxD)
    {
        d = maxD;
    }
    
    return Date.from(y, m, d, this.h(), this.n(), this.s(), this.l());
};


Date.prototype.addDays = function(days)
{
///<summary>添加日数并返回新的日期时间对象，该方法并不影响当前对象。语法：datetime1.addDays(days)</summary>
///<param name="days" type="integer">要添加的日数。</param>
///<returns type="datetime">添加了日数的新的日期时间对象。</returns>
    var result = this.copy();
    result.setDate(this.d() + days); // 不能在此处返回，因为 setDate 方法返回的并不是日期对象。
    return result;
};


Date.prototype.addHours = function(hours)
{
///<summary>添加时数并返回新的日期时间对象，该方法并不影响当前对象。语法：datetime1.addHours(hours)</summary>
///<param name="hours" type="integer">要添加的时数。</param>
///<returns type="datetime">添加了时数的新的日期时间对象。</returns>
    var result = this.copy();
    result.setHours(this.h() + hours); // 不能在此处返回，因为 setHours 方法返回的并不是日期对象。
    return result;
};


Date.prototype.addMinutes = function(minutes)
{
///<summary>添加分数并返回新的日期时间对象，该方法并不影响当前对象。语法：datetime1.addMinutes(minutes)</summary>
///<param name="minutes" type="integer">要添加的分数。</param>
///<returns type="datetime">添加了分数的新的日期时间对象。</returns>
    var result = this.copy();
    result.setMinutes(this.n() + minutes); // 不能在此处返回，因为 setMinutes 方法返回的并不是日期对象。
    return result;
};


Date.prototype.addSeconds = function(seconds)
{
///<summary>添加秒数并返回新的日期时间对象，该方法并不影响当前对象。语法：datetime1.addSeconds(seconds)</summary>
///<param name="seconds" type="integer">要添加的秒数。</param>
///<returns type="datetime">添加了秒数的新的日期时间对象。</returns>
    var result = this.copy();
    result.setSeconds(this.s() + seconds); // 不能在此处返回，因为 setSeconds 方法返回的并不是日期对象。
    return result;
};


Date.prototype.addMilliseconds = function(milliseconds)
{
///<summary>添加毫秒数并返回新的日期时间对象，该方法并不影响当前对象。语法：datetime1.addMilliseconds(milliseconds)</summary>
///<param name="milliseconds" type="integer">要添加的毫秒数。</param>
///<returns type="datetime">添加了毫秒数的新的日期时间对象。</returns>
    var result = this.copy();
    result.setMilliseconds(this.l() + milliseconds); // 不能在此处返回，因为 setMilliseconds 方法返回的并不是日期对象。
    return result;
};


Date.prototype.compareTo = function(dt)
{
///<summary>比较当前对象与 dt 的早晚并返回比较值。语法：datetime1.compareTo(dt)</summary>
///<param name="dt" type="string/datetime">参与比较的日期。可以是日期格式的字符串。</param>
///<returns type="integer">若当前对象早于 dt，则为 -1；若当前对象晚于 dt，则为 1；否则为 0。</returns>
    return Date.compare(this, dt);
};


Date.prototype.differTo = function(dt)
{
///<summary>当前对象减去 dt，并返回一个 timespan。语法：datetime1.differTo(dt)</summary>
///<param name="dt" type="string/datetime">参与相减的日期。可以是日期格式的字符串。</param>
///<returns type="timespan">相减后的 timespan。</returns>
    return Date.differ(this, dt);
};


//================================================================================


//Math


Math.nextRandom = function(minValue, maxValue)
{
///<summary>产生一个随机整数并返回。语法：Math.nextRandom(minValue, maxValue)</summary>
///<param name="minValue" type="integer">随机整数的下界（随机整数可取该下界值）。</param>
///<param name="maxValue" type="integer">随机整数的上界（随机整数不能取该上界值）。</param>
///<returns type="integer">产生的随机整数。</returns>
    var min = parseInt(minValue);
    var max = parseInt(maxValue) - 1;
    return Math.floor(((max - min) + 1) * Math.random() + min);
};


//================================================================================


//Function


Function.is = function(v)
{
///<summary>判断 v 是否是 Function 类型。语法：Function.is(v)</summary>
///<param name="v" type="any">要判断的变量或表达式。</param>
///<returns type="boolean">v 是否是 Function 类型。</returns>
    return Object.prototype.toString.apply(v) === "[object Function]";
};


Function.run = function(v)
{
///<summary>运行函数 v，若 v 不是函数，则自动跳过。语法：Function.run(v[, param1[, param2[, ...[, paramN]]]])</summary>
///<param name="v" type="function">要运行的函数。</param>
///<param name="paramN" type="any">可选。v 的参数。</param>
///<returns type="any">函数 v 的返回值。</returns>
    if (Function.is(v))
    {
        var arr = [];
        for (var i = 1; i < arguments.length; i++)
        {
            arr.push(arguments[i]);
        }
        return v.apply(window, arr);
    }
};


Function.overload = function(paramIndex, defaultValue)
{
///<summary>获取函数的参数值。语法：Function.overload(paramIndex, defaultValue[, skipNull])</summary>
///<param name="paramIndex" type="integer">要获取的参数的索引值，第一个参数值为 0。</param>
///<param name="defaultValue" type="any">参数的默认值。</param>
///<param name="skipNull" type="boolean">可选。当传入的参数是 null 时，是否使用 defaultValue。默认值为 false。</param>
///<returns type="any">若指定了索引为 paramIndex 的参数则为该参数的值；否则为 defaultValue。</returns>
    var result = defaultValue;
    
    if (arguments.callee.caller.arguments.length > paramIndex)
    {
        var skipNull = false;
        if (arguments.length >= 3)
        {
            skipNull = arguments[2];
        }
        
        var v = arguments.callee.caller.arguments[paramIndex];
        if (!skipNull || v !== null)
        {
            result = v;
        }
    }
    
    return result;
};


Function.like = function()
{
///<summary>测试函数是否符合某一重载，调用参数个数可以多于要求的参数个数。语法：Function.like([type1[, type2[, ...[, typeN]]]])</summary>
///<param name="typeN" type="string">可选。第 N 个参数的类型。可用 any 表示任意类型。</param>
///<returns type="boolean">函数是否符合某一重载。</returns>
    var result = false;
    
    if (arguments.callee.caller.arguments.length >= arguments.length)
    {
        result = true;
        for (var i = 0; i < arguments.length; i++)
        {
            var trueType = Object.prototype.toString.apply(arguments.callee.caller.arguments[i]); // 形如：[object Function]
            trueType = trueType.mid(8, trueType.length - 9);
            trueType = trueType.toLowerCase();
            var demand = arguments[i];
            if (String.is(demand))
            {
                if (demand != "any" && trueType != demand)
                {
                    result = false;
                    break;
                }
            }
            else if (Array.is(demand))
            {
                if (!demand.exists(trueType))
                {
                    result = false;
                    break;
                }
            }
        }
    }
    
    return result;
};


Function.match = function()
{
///<summary>测试函数是否符合某一重载，调用参数个数必须等于要求的参数个数。语法：Function.match([type1[, type2[, ...[, typeN]]]])</summary>
///<param name="typeN" type="string">可选。第 N 个参数的类型。可用 any 表示任意类型。</param>
///<returns type="boolean">函数是否符合某一重载。</returns>
    var result = false;
    
    if (arguments.callee.caller.arguments.length == arguments.length)
    {
        result = true;
        for (var i = 0; i < arguments.length; i++)
        {
            var trueType = Object.prototype.toString.apply(arguments.callee.caller.arguments[i]); // 形如：[object Function]
            trueType = trueType.mid(8, trueType.length - 9);
            trueType = trueType.toLowerCase();
            var demand = arguments[i];
            if (String.is(demand))
            {
                if (demand != "any" && trueType != demand)
                {
                    result = false;
                    break;
                }
            }
            else if (Array.is(demand))
            {
                if (!demand.exists(trueType))
                {
                    result = false;
                    break;
                }
            }
        }
    }
    
    return result;
};


//================================================================================


var nativeAlert = window.alert;
window.alert = function()
{
///<summary>增强了的 alert，支持多个参数，显示时将各参数用换行符隔开。语法：alert([v1[, v2[, ...[, vN]]]])</summary>
///<param name="vN" type="any">可选。要显示的内容。</param>
    var str = "";
    for (var i = 0; i < arguments.length; i++)
    {
        if (i > 0)
        {
            str += "\n" + arguments[i];
        }
        else
        {
            str = arguments[i];
        }
    }
    nativeAlert(str);
};


//================================================================================
//================================================================================
//================================================================================


var ezj = function()
{
///<summary>ezj 命名空间。语法：ezj</summary>
};


//================================================================================


ezj.about = function()
{
///<summary>介绍关于 ezj 内容的对象。语法：ezj.about</summary>
};


///<summary>ezj 版本。语法：ezj.about.version</summary>
ezj.about.version = "v2.9.1";


///<summary>ezj 描述。语法：ezj.about.description</summary>
ezj.about.description = "ezj - 驱动 JavaScript!";


///<summary>ezj 制造商。语法：ezj.about.company</summary>
ezj.about.company = "www.getezj.com";


///<summary>ezj 引用目录。语法：ezj.about.path</summary>
ezj.about.path = (function() {
    var result = "";
    
    var es = document.getElementsByTagName("script");
    for (var i = 0; i < es.length; i++)
    {
        if (String.is(es[i].src) &&
            (es[i].src.right(6).equalsIgnoreCase("ezj.js") || es[i].src.right(13).equalsIgnoreCase("ezj_source.js")))
        {
            result = es[i].src.left(es[i].src.lastIndexOf("/") + 1);
            break;
        }
    }
    
    return result;
})();


//================================================================================


ezj.ready = function(handler)
{
///<summary>在 $g(window).ready 的基础上，增加自动按 id 创建变量功能，将 HTML 元素的 id 经 $g 包装后在 handler 中直接当变量使用。语法：ezj.ready(handler)</summary>
///<param name="handler" type="function">事件处理程序。</param>
    function ready(handler)
    {
        var arr = $g("<*");
        for (var i = 0; i < arr.length; i++)
        {
            if (!String.isNullOrEmpty(arr[i].id))
            {
                var code = "var " + arr[i].id + " = $g(\"" + arr[i].id + "\");";
                if (window.execScript)
                {
                    window.execScript(code); // IE 中用 window.execScript 在全局作用域执行代码
                }
                else
                {
                    window.eval(code); // Firefox、Chrome 中用 window.eval 在全局作用域执行代码
                }
            }
        }
        handler();
    }
    
    $g(window).ready(function(){
        ready(handler);
    });
};


//================================================================================


ezj.Dom = function(selector)
{
///<summary>处理 DOM 的类。语法：new ezj.Dom(selector[, parentElement])</summary>
    var parentElement = Function.overload(1, null);
    
    var obj = query(selector, parentElement); // 可以是数组
    
    var objs = Array.from(obj); // 转换成数组
    if (Array.is(obj))
    {
        // 让 obj 的每一项，也经过包装
        for (var i = 0; i < objs.length; i++)
        {
            objs[i] = $g(objs[i], parentElement);
        }
    }
    
    if (!obj)
    {
        return obj;
    }
    else if (Function.is(obj.val))
    {
        // 具有 ezj 的方法了，不再继续包装
        return obj;
    }
    
    function query(selector)
    {
    // query(selector[, parentElement])
        if (!String.is(selector))
        {
            return selector;
        }
        
        // 分解元素查询字符串
        // 先处理第一段元素查询字符串，再根据递归处理后面的段
        var seg1 = "";
        var seg2 = "";
        var spacePos = selector.indexOf(" ");
        if (spacePos > 0)
        {
            seg1 = selector.substr(0, spacePos).trim();
            seg2 = selector.substr(spacePos + 1).trim();
        }
        else
        {
            seg1 = selector.trim();
        }
        
        // 以下处理均是处理第一段元素查询字符串
        
        // 获取选择器。
        // id 没有前缀、name 前缀为 :、class 前缀为 .、tag 前缀为 <（<* 表示任意元素）
        // 根据 DOM 中 id 的用法，id 只能出现在第一个选择器中
        var selectors = [];
        for (var i = 0; i < seg1.length; i++)
        {
            var c = seg1.substr(i, 1);
            if (c == ":")
            {
                selectors.push({ type: "name", value: "" });
            }
            else if (c == ".")
            {
                selectors.push({ type: "class", value: "" });
            }
            else if (c == "<")
            {
                selectors.push({ type: "tag", value: "" });
            }
            else
            {
                if (selectors.length > 0)
                {
                    selectors[selectors.length - 1].value += c;
                }
                else
                {
                    selectors.push({ type: "id", value: c });
                }
            }
        }
        
        var result = [];
        
        // 根据第一个选择符先获取初步的结果
        // 注意 id 总是在全局范围内取，故无 parentElement 限制。
        var parentElement = Function.overload(1, null);
        if (selectors.length > 0)
        {
            if (selectors[0].type == "id")
            {
                result.push(getById(selectors[0].value));
            }
            else if (selectors[0].type == "name")
            {
                // getByName 返回的是数组
                // 使用 concat 连接数组并返回给 result
                result = result.concat(getByName(selectors[0].value, parentElement));
            }
            else if (selectors[0].type == "class")
            {
                // getByClass 返回的是数组
                // 使用 concat 连接数组并返回给 result
                result = result.concat(getByClass(selectors[0].value, parentElement));
            }
            else if (selectors[0].type == "tag")
            {
                // getByTag 返回的是数组
                // 使用 concat 连接数组并返回给 result
                result = result.concat(getByTag(selectors[0].value, parentElement));
            }
        }
        
        // 去除不符合条件的元素：从第二个选择符开始判断当前元素是否符合
        var i = result.length - 1;
        while (i >= 0)
        {
            if (!result[i])
            {
                result.splice(i, 1);
                i--;
                continue;
            }
            
            // j 从 1 开始
            for (var j = 1; j < selectors.length; j++)
            {
                // W3C 规定：id 大小写敏感、name 大小写不敏感、class 大小写敏感、tag 大小写不敏感
                if (selectors[j].type == "id" && selectors[j].value != result[i].id)
                {
                    // 由于 id 无前缀，所以 id 肯定是位于第一个选择符
                    // 所以这里肯定不会执行到，这里写出来只是考虑到程序的可扩展性
                    result.splice(i, 1); // 当前元素要去除
                    break;
                }
                else if (selectors[j].type == "name" && !selectors[j].value.equalsIgnoreCase(result[i].name))
                {
                    result.splice(i, 1); // 当前元素要去除
                    break;
                }
                else if (selectors[j].type == "class")
                {
                    var arr = result[i].className.splitEx(" ", true, true);
                    if (!arr.exists(selectors[j].value))
                    {
                        result.splice(i, 1); // 当前元素要去除
                        break;
                    }
                }
                else if (selectors[j].type == "tag" &&
                    selectors[j].value != "*" && !selectors[j].value.equalsIgnoreCase(result[i].tagName))
                {
                    result.splice(i, 1); // 当前元素要去除
                    break;
                }
            }
            
            i--;
        }
        
        if (seg2 == "")
        {
            if (seg1.indexOf(":") < 0 && seg1.indexOf(".") < 0 && seg1.indexOf("<") < 0)
            {
                // 传入的仅是 id，不以数组形式返回。
                return result[0];
            }
            return result;
        }
        else
        {
            return result.each(function(e, index){
                return query(seg2, e);
            }, true);
        }
        
        return document.getElementById(element); // 若 element 不存在，则返回 null；element 为 null，也返回 null
    }
    
    
    function getById(id)
    {
        var result = document.getElementById(id); // 不存在 parentElement.getElementById(id);
        
        if (result == null)
        {
            return result;
        }
        
        // IE 有 BUG，把 name 和 id 混淆，故这里要判断
        if (result.id != id)
        {
            for (var i = 0; i < document.all[id].length; i++)
            {
                if (document.all[id][i].id == id)
                {
                    result = document.all[id][i];
                    break;
                }
            }
        }
        
        return result;
    }


    function getByName(name)
    {
        var parentElement = query(Function.overload(1, null));
        // 由于在 IE 中，getElementsByName 不支持某些标签，所以这里没有直接用该方法。
        var elements = (parentElement || document).getElementsByTagName("*");
        
        var result = [];
        for (var i = 0; i < elements.length; i++)
        {
            if (elements[i].getAttribute("name") == name)
            {
                result.push(elements[i]);
            }
        }
        
        return result;
    }
    
    
    function getByClass(className)
    {
        var parentElement = query(Function.overload(1, null));
        var elements = (parentElement || document).getElementsByTagName("*");
        
        var result = [];
        for (var i = 0; i < elements.length; i++)
        {
            classes = elements[i].className.splitEx(" ", true, true);
            // class 名称大小写敏感
            if (classes.exists(className))
            {
                result.push(elements[i]);
            }
        }
        
        return result;
    }


    function getByTag(tagName)
    {
        // 支持自定义标签 checkbox、radio、text
        var loweredTagName = tagName.toLowerCase();
        var inputTagType = "";
        if (loweredTagName == "checkbox")
        {
            tagName = "input";
            inputTagType = "checkbox";
        }
        else if (loweredTagName == "radio")
        {
            tagName = "input";
            inputTagType = "radio";
        }
        else if (loweredTagName == "text")
        {
            tagName = "input";
            inputTagType = "text";
        }
        else if (loweredTagName == "password")
        {
            tagName = "input";
            inputTagType = "password";
        }
        else if (loweredTagName == "hidden")
        {
            tagName = "input";
            inputTagType = "hidden";
        }
        else if (loweredTagName == "file")
        {
            tagName = "input";
            inputTagType = "file";
        }
        else if (loweredTagName == "image")
        {
            tagName = "input";
            inputTagType = "image";
        }
        
        var parentElement = query(Function.overload(1, null));
        var elements = (parentElement || document).getElementsByTagName(tagName); // 不管后面过不过滤，都不能直接返回
        
        var result = [];
        for (var i = 0; i < elements.length; i++)
        {
            if (inputTagType != "" && elements[i].type.toLowerCase() != inputTagType)
            {
                // 要求 input 标签的 type
                continue;
            }
            result.push(elements[i]);
        }
        
        return result;
    }
    
    
    obj.val = function()
    {
    ///<summary>获取、设置元素的值。将元素的 value、src、innerHTML 属性值作为值，对于 SELECT 元素则返回具有 text、value 属性的对象数组。语法：$1.val([value[, mode]])</summary>
    ///<param name="value" type="string/array">可选。元素的新值。对于 SELECT 则为具有 text、value 属性的对象数组。</param>
    ///<param name="mode" type="integer">可选。赋值模式：-1 插入，0 重写，1 追加。默认值为 0。</param>
    ///<returns type="string/array">元素的值或新值。对于 SELECT 则为具有 text、value 属性的对象数组。</returns>
        var value = Function.overload(0, null);
        var mode = Function.overload(1, 0); // -1 插入，0 重写，1 追加。
        
        var result = objs.each(function(e, index){
            var result = null;
            
            if (e.tagName.equalsIgnoreCase("SELECT"))
            {
                if (value !== null)
                {
                    if (mode == -1)
                    {
                        // 插入
                        for (var i = e.options.length - 1; i >= 0; i--)
                        {
                            e.options[i + value.length] = new Option(e.options[i].text, e.options[i].value); // 后移
                        }
                        for (var i = 0; i < value.length; i++)
                        {
                            e.options[i] = new Option(value[i].text, value[i].value);
                        }
                    }
                    else if (mode == 1)
                    {
                        // 追加
                        for (var i = 0; i < value.length; i++)
                        {
                            e.options[e.options.length] = new Option(value[i].text, value[i].value);
                        }
                    }
                    else
                    {
                        // 先删除，后填充
                        while (e.options.length > 0)
                        {
                            e.options[0] = null;
                        }
                        for (var i = 0; i < value.length; i++)
                        {
                            e.options[e.options.length] = new Option(value[i].text, value[i].value);
                        }
                    }
                }
                result = [];
                for (var i = 0; i < e.options.length; i++)
                {
                    result.push({ text : e.options[i].text, value : e.options[i].value });
                }
            }
            else if (typeof e.value != "undefined")
            {
                if (value !== null)
                {
                    if (e.ezjExtWatermarkInputed === false)
                    {
                        e.value = ""; // 排除 ezj.ext.watermark 水印情况
                    }
                    
                    if (mode == -1)
                    {
                        e.value = value + e.value;
                    }
                    else if (mode == 1)
                    {
                        e.value += value;
                    }
                    else
                    {
                        e.value = value;
                    }
                    
                    e.ezjExtWatermarkInputed = true; // 指定了内容
                }
                
                // 排除 ezj.ext.watermark 水印情况
                if (e.ezjExtWatermarkInputed === false)
                {
                    result = "";
                }
                else
                {
                    result = e.value;
                }
            }
            else if (typeof e.src != "undefined")
            {
                if (value !== null)
                {
                    if (mode == -1)
                    {
                        e.src = value + e.src;
                    }
                    else if (mode == 1)
                    {
                        e.src += value;
                    }
                    else
                    {
                        e.src = value;
                    }
                }
                result = e.src;
            }
            else if (typeof e.innerHTML != "undefined")
            {
                if (value !== null)
                {
                    if (mode == -1)
                    {
                        e.innerHTML = value + e.innerHTML;
                    }
                    else if (mode == 1)
                    {
                        e.innerHTML += value;
                    }
                    else
                    {
                        e.innerHTML = value;
                    }
                }
                result = e.innerHTML;
            }
            
            return result;
        });
        
        return ((!Array.is(obj)) ? result[0] : result);
    };
    
    
    obj.empty = function()
    {
    ///<summary>清空元素的值，对于 SELECT 则清空项。语法：$1.empty()</summary>
        objs.each(function(e, index){
            if (e.tagName.equalsIgnoreCase("SELECT"))
            {
                e.val([]);
            }
            else
            {
                e.val("");
            }
        });
    };
    
    
    obj.sel = function()
    {
    ///<summary>获取、设置 SELECT、单选框、多选框元素的选择项，这里 SELECT 必须按 id 取。语法：$1.sel([selItems[, empty]])</summary>
    ///<param name="selItems" type="array/string">可选。数字或字符串数组，分别用以按索引、值来选择项，按值选择时大小写敏感；也可为字符串：all、inverse、none。默认值为 null。</param>
    ///<param name="empty" type="boolean">可选。选择前是否清空原有选择。默认值为 false。</param>
    ///<returns type="array">具有 index、value 属性的对象数组。</returns>
        var selItems = Function.overload(0, null);
        var empty = Function.overload(1, false);
        
        var result = [];
        
        // 对于单选框、多选框可能通过以下几种形式传入（以单选框为例）：
        // form.单选框（一个时）    IE 中为 [object Object]；Firefox 中为 [object HTMLInputElement]。均无 length 属性
        // form.单选框（多个时）    IE 中为 [object Object]；Firefox 中为 [object NodeList]。均有 length 属性
        // 一个单选框对象           IE 中为 [object Object]；Firefox 中为 [object HTMLInputElement]。均无 length 属性
        // 多个单选框对象组成的数组 均为 [object Array]。均有 length 属性
        
        // 最好不要用 if (obj.length) 判断，因为如果 obj.length 值为 0 呢？
        // 要注意 string、SELECT 也有 length 属性，要把他们排除。
        // obj.length not objs.length
        if (typeof obj.length != "undefined" &&
            !String.is(obj) &&
            typeof obj.options == "undefined")
        {
            // form.单选框（多个时）
            // 或
            // 多个单选框对象组成的数组
            // 多选框类似
            for (var i = 0; i < obj.length; i++)
            {
                // 设置
                if (Array.is(selItems))
                {
                    if (empty)
                    {
                        obj[i].checked = false;
                    }
                    for (var j = 0; j < selItems.length; j++)
                    {
                        if (Number.is(selItems[j]) && i == selItems[j] ||
                            String.is(selItems[j]) && obj[i].value == selItems[j])
                        {
                            // 按索引或 value 匹配
                            obj[i].checked = true;
                            break;
                        }
                    }
                }
                else if (selItems == "all")
                {
                    obj[i].checked = true;
                }
                else if (selItems == "inverse")
                {
                    // 不要对单选框使用 inverse，即使只有两个单选框。
                    // 因为从原理上来说，他们应该不支持反选。
                    // 对单选框使用 inverse，总是会选中最后一个。
                    obj[i].checked = !obj[i].checked;
                }
                else if (selItems == "none")
                {
                    obj[i].checked = false;
                }
                // 获取
                if (obj[i].checked)
                {
                    result.push({ index : i, value : obj[i].value });
                }
            }
        }
        else
        {
            // obj not objs
            // SELECT
            for (var i = 0; i < obj.options.length; i++)
            {
                // 设置
                if (Array.is(selItems))
                {
                    if (empty)
                    {
                        obj.options[i].selected = false;
                    }
                    for (var j = 0; j < selItems.length; j++)
                    {
                        if (Number.is(selItems[j]) && i == selItems[j] ||
                            String.is(selItems[j]) && obj.options[i].value == selItems[j])
                        {
                            obj.options[i].selected = true;
                            break;
                        }
                    }
                }
                else if (selItems == "all")
                {
                    obj.options[i].selected = true;
                }
                else if (selItems == "inverse")
                {
                    obj.options[i].selected = !obj.options[i].selected;
                }
                else if (selItems == "none")
                {
                    obj.options[i].selected = false;
                }
                // 获取
                if (obj.options[i].selected)
                {
                    result.push({ index : i, value : obj.options[i].value, text : obj.options[i].text });
                }
            }
        }
        
        return result;
    };
    
    
    obj.data = function()
    {
    ///<summary>获取用户录入或选择的值。对于单选或用 id 直接获取的 CHECKBOX，返回选择项的值；对于多选，以数组形式返回选择项的值。语法：$1.data()</summary>
    ///<returns type="string/array">用户录入或选择的值。</returns>
        var result = [];
        
        var isSelectArray = false;
        if (Array.is(obj) && obj.length > 0 && objs[0].tagName == "SELECT")
        {
            isSelectArray = true;
        }
        
        if (typeof obj.length != "undefined" &&
            !String.is(obj) &&
            typeof obj.options == "undefined" &&
            !isSelectArray)
        {
            // form.单选框（多个时）
            // 或
            // 多个单选框对象组成的数组
            // 多选框类似
            result = obj.sel().fetch("value");
            // radio 不是以数组的形式返回
            // checkbox 以数组的形式返回
            // 是 radio 还是 checkbox 由第一个元素的 type 决定
            if (result.length <= 0 &&
                objs.length > 0 &&
                objs[0].type == "radio")
            {
                result = null;
            }
            else if (result.length == 1 &&
                objs.length > 0 &&
                objs[0].type == "radio")
            {
                result = result[0];
            }
        }
        else if (!Array.is(obj) &&
            (obj.type == "radio" || obj.type == "checkbox"))
        {
            // 用 id 获取一个 radio 或 checkbox
            if (obj.checked)
            {
                result = obj.value;
            }
            else
            {
                result = null;
            }
        }
        else
        {
            result = objs.each(function(e, index){
                var result = null;
                if (e.tagName.equalsIgnoreCase("SELECT") &&
                    e.multiple)
                {
                    result = e.sel().fetch("value"); // 多选
                }
                else if (e.tagName.equalsIgnoreCase("SELECT") &&
                    !e.multiple)
                {
                    result = e.sel().fetch("value"); // 单选
                    result = (result.length > 0) ? result[0] : null;
                }
                else
                {
                    result = e.val();
                }
                
                return result;
            });
            
            result = ((!Array.is(obj)) ? result[0] : result);
        }
        
        return result;
    };
    
    
    obj.display = function()
    {
    ///<summary>获取、设置元素是否显示。语法：$1.display([isDisplay])</summary>
    ///<param name="isDisplay" type="boolean">可选。设置是否显示，若省略则切换显示；若为 null 则不设置只获取。</param>
    ///<returns type="boolean/array">是否是显示状态。</returns>
        var isDisplay = Function.overload(0, "exchange");
        
        var inlineElements = ["A", "ACRONYM", "B", "BDO", "BIG", "BR", "CITE", "CODE", "DFN", "EM", "FONT", "I", "IMG", "INPUT", "KBD", "LABEL", "Q", "S", "SAMP", "SELECT", "SMALL", "SPAN", "STRIKE", "STRONG", "SUB", "SUP", "TEXTAREA", "TT", "U", "VAR"];
        var result = objs.each(function(e, index){
            var displayString = inlineElements.exists(e.tagName.toUpperCase()) ? "inline" : "block";
            if (isDisplay == "exchange")
            {
                e.style.display = ((e.style.display == "none") ? displayString : "none"); // 切换
            }
            else if (isDisplay !== null)
            {
                e.style.display = ((isDisplay) ? displayString : "none");
            }
            
            return ((e.style.display == "none") ? false : true);
        });
        
        return ((!Array.is(obj)) ? result[0] : result);
    };
    
    
    obj.enable = function()
    {
    ///<summary>获取、设置元素是否可用。语法：$1.enable([isEnable])</summary>
    ///<param name="isEnable" type="boolean">可选。设置是否可用，若省略则切换可用；若为 null 则不设置只获取。</param>
    ///<returns type="boolean/array">是否是可用状态。</returns>
        var isEnable = Function.overload(0, "exchange");
        
        var result = objs.each(function(e, index){
            if (isEnable == "exchange")
            {
                e.disabled = !(e.disabled ? true : false) // 切换。注意 isEnable 和 disabled
            }
            else if (isEnable !== null)
            {
                e.disabled = !isEnable;
            }
            
            return (e.disabled ? false : true);
        });
        
        return ((!Array.is(obj)) ? result[0] : result);
    };
    
    
    // tr 等具有 bgcolor 属性，对应的脚本为 bgColor，为避免冲突这里小写。
    obj.bgcolor = function(r, g, b)
    {
    ///<summary>设置元素的背景色。语法：$1.bgcolor(r, g, b) 或 $1.bgcolor(colorString)</summary>
    ///<param name="r" type="integer">颜色的红色值。范围为 0-255。</param>
    ///<param name="g" type="integer">颜色的绿色值。范围为 0-255。</param>
    ///<param name="b" type="integer">颜色的蓝色值。范围为 0-255。</param>
        if (arguments.length == 3)
        {
            objs.each(function(e, index){
                var c = "#" +
                    r.toString(16).padLeft(2, "0") +
                    g.toString(16).padLeft(2, "0") +
                    b.toString(16).padLeft(2, "0");
                e.style.backgroundColor = c;
            });
        }
        else if (arguments.length == 1)
        {
            var colorString = arguments[0];
            objs.each(function(e, index){
                e.style.backgroundColor = colorString;
            });
        }
    };
    
    
    obj.color = function(r, g, b)
    {
    ///<summary>设置元素的前景色。语法：$1.color(r, g, b) 或 $1.color(colorString)</summary>
    ///<param name="r" type="integer">颜色的红色值。范围为 0-255。</param>
    ///<param name="g" type="integer">颜色的绿色值。范围为 0-255。</param>
    ///<param name="b" type="integer">颜色的蓝色值。范围为 0-255。</param>
        if (arguments.length == 3)
        {
            objs.each(function(e, index){
                var c = "#" +
                    r.toString(16).padLeft(2, "0") +
                    g.toString(16).padLeft(2, "0") +
                    b.toString(16).padLeft(2, "0");
                e.style.color = c;
            });
        }
        else if (arguments.length == 1)
        {
            var colorString = arguments[0];
            objs.each(function(e, index){
                e.style.color = colorString;
            });
        }
    };
    
    
    obj.space = function()
    {
    ///<summary>获取元素的宽、高、在页面中的 x、y 座标等空间信息。语法：$1.space()</summary>
    ///<returns type="object/array">具有 x、y、width、height、ancestors 等属性的对象。</returns>
        var result = objs.each(function(e, index){
            var result = {};
            
            var x = e.offsetLeft;
            var y = e.offsetTop;
            var pe = e.offsetParent;
            while (pe)
            {
                x += pe.offsetLeft;
                y += pe.offsetTop;
                pe = pe.offsetParent;
            }
            
            result.x = x;
            result.y = y;
            result.width = e.offsetWidth;
            result.height = e.offsetHeight;
            
            result.ancestors = [];
            var pn = e.parentNode;
            while(pn)
            {
                result.ancestors.push(pn);
                pn = pn.parentNode;
            }
            
            if (e == document.body || e == document.documentElement)
            {
                result.scrollLeft = (document.documentElement.scrollLeft || document.body.scrollLeft);
                result.scrollTop = (document.documentElement.scrollTop || document.body.scrollTop);
                result.clientWidth = (document.documentElement.clientWidth || document.body.clientWidth);
                result.clientHeight = (document.documentElement.clientHeight || document.body.clientHeight);
            }
            
            return result;
        });
        
        return ((!Array.is(obj)) ? result[0] : result);
    };
    
    
    obj.maxWidth = function(width)
    {
    ///<summary>设置元素的最大宽度，某些浏览器不支持 CSS 的 max-width 的解决办法。语法：$1.maxWidth(width)</summary>
    ///<param name="width" type="integer">最大宽度数值。</param>
        objs.each(function(e, index){
            e.style.maxWidth = width + "px"; // 适用于 Firefox、Chrome
            if (e.offsetWidth > width)
            {
                e.style.width = width;
            }
        });
    };
    
    
    obj.maxHeight = function(height)
    {
    ///<summary>设置元素的最大高度，某些浏览器不支持 CSS 的 max-height 的解决办法。语法：$1.maxHeight(height)</summary>
    ///<param name="height" type="integer">最大高度数值。</param>
        objs.each(function(e, index){
            e.style.maxHeight = height + "px"; // 适用于 Firefox、Chrome
            if (e.offsetHeight > height)
            {
                e.style.height = height;
            }
        });
    };
    
    
    obj.pos = function(baseElement)
    {
    ///<summary>以 baseElement 元素为基准元素设置元素的位置。语法：$1.pos(baseElement[, base])</summary>
    ///<param name="baseElement" type="string/object">基准元素 id，或元素对象。</param>
    ///<param name="base" type="string">可选。位置配置字符串，l、r、t、b、c、m、i、o 分别表示：左、右、上、下、中、中、内、外，可组合使用。默认值为 libo。</param>
        var base = Function.overload(1, "");
        
        objs.each(function(e, index){
            var baseSpace = $g(baseElement).space();
            var space = e.space();
            e.style.position = "absolute";
            
            if (base.indexOf("c") >= 0)
            {
                e.style.left = (baseSpace.x + parseInt(baseSpace.width / 2) - parseInt(space.width / 2)) + "px";
            }
            else if (base.indexOf("ri") >= 0)
            {
                e.style.left = (baseSpace.x + baseSpace.width - space.width) + "px";
            }
            else if (base.indexOf("ro") >= 0)
            {
                e.style.left = (baseSpace.x + baseSpace.width) + "px";
            }
            else if (base.indexOf("lo") >= 0)
            {
                e.style.left = (baseSpace.x - space.width) + "px";
            }
            else
            {
                e.style.left = baseSpace.x + "px";
            }
            
            if (base.indexOf("m") >= 0)
            {
                e.style.top = (baseSpace.y + parseInt(baseSpace.height / 2) - parseInt(space.height / 2)) + "px";
            }
            else if (base.indexOf("ti") >= 0)
            {
                e.style.top = baseSpace.y + "px";
            }
            else if (base.indexOf("to") >= 0)
            {
                e.style.top = (baseSpace.y - space.height) + "px";
            }
            else if (base.indexOf("bi") >= 0)
            {
                e.style.top = (baseSpace.y + baseSpace.height - space.height) + "px";
            }
            else
            {
                e.style.top = (baseSpace.y + baseSpace.height) + "px";
            }
        });
    };
    
    
    obj.centerWin = function()
    {
    ///<summary>将元素置于视口（viewport）中央。语法：$1.centerWin()</summary>
        objs.each(function(e, index){
            var space = e.space();
            
            function reset()
            {
                // 标准模式
                e.style.position = "fixed";
                e.style.top = parseInt((document.documentElement.clientHeight || document.body.clientHeight) / 2) - parseInt(space.height / 2) + "px";
                e.style.left = parseInt((document.documentElement.clientWidth || document.body.clientWidth) / 2) - parseInt(space.width / 2) + "px";
                
                if (ezj.ie.is() && ezj.ie.engine <= 6)
                {
                    // 这里很头痛，因为 IE 6 的 position 认 fixed 这几个字母，只是不实现其效果，
                    // 所以无法通过 position 来判断它是不是支持 fixed，只好通过 IE 引擎来判断。
                    // 兼容模式
                    e.style.position = "absolute";
                    e.style.top = (document.documentElement.scrollTop || document.body.scrollTop) +
                                  parseInt((document.documentElement.clientHeight || document.body.clientHeight) / 2) - parseInt(space.height / 2) + "px";
                    e.style.left = (document.documentElement.scrollLeft || document.body.scrollLeft) +
                                  parseInt((document.documentElement.clientWidth || document.body.clientWidth) / 2) - parseInt(space.width / 2) + "px";
                }
            }
            
            reset();
            
            if (!e.centerWinEventOk)
            {
                $g(window).scroll(reset);
                $g(window).resize(reset);
                e.centerWinEventOk = true;
            }
        });
    };
    
    
    obj.childElements = function()
    {
    ///<summary>获取子元素，与 childNodes 相比，它只取 nodeType 为 1 的节点。语法：$1.childElements()</summary>
    ///<returns type="array">子元素数组。</returns>
        var result = objs.each(function(e, index){
            var result = [];
            for (var i = 0; i < e.childNodes.length; i++)
            {
                if (e.childNodes[i].nodeType == 1)
                {
                    result.push(e.childNodes[i]);
                }
            }
            return result;
        });
        
        return ((!Array.is(obj)) ? result[0] : result);
    };
    
    
    obj.insert = function(element)
    {
    ///<summary>插入子元素。语法：$1.insert(element[, pos])</summary>
    ///<param name="element" type="string/object/array">要插入的元素 id，或元素对象，或元素对象数组。</param>
    ///<param name="pos" type="number/string/object">可选。在 pos 对应的子元素前面插入元素。number 时表示子元素索引；string 时表示子元素 id，object 时表示子元素对象。省略表示在最前面插入。</param>
        var pos = Function.overload(1, 0); // 注意默认值
        
        var ces = Array.from(element);
        ces.each(function(e, index){
            ces[index] = $g(e);
        });
        
        // 实际上 objs 元素多个时，只对一个起作用。
        var e = objs[objs.length - 1];
        if (Number.is(pos))
        {
            var childElements = e.childElements(); // childElements 后面不变了
            if (pos >= 0 && pos < childElements.length)
            {
                ces.each(function(ce){
                    e.insertBefore(ce, childElements[pos]);
                });
            }
            else if (pos < 0 && childElements.length > 0)
            {
                ces.each(function(ce){
                    e.insertBefore(ce, childElements[0]);
                });
            }
            else
            {
                ces.each(function(ce){
                    e.appendChild(ce);
                });
            }
        }
        else if (pos)
        {
            pos = $g(pos);
            ces.each(function(ce){
                e.insertBefore(ce, pos);
            });
        }
    };
    
    
    obj.append = function(element)
    {
    ///<summary>追加子元素。语法：$1.append(element[, pos])</summary>
    ///<param name="element" type="string/object/array">要追加的元素 id，或元素对象，或元素对象数组。</param>
    ///<param name="pos" type="number/string/object">可选。在 pos 对应的子元素后面追加元素。number 时表示子元素索引；string 时表示子元素 id，object 时表示子元素对象。省略表示在最后面追加。</param>
        var pos = Function.overload(1, objs[objs.length - 1].childElements().length - 1); // 注意默认值
        
        var ces = Array.from(element);
        ces.each(function(e, index){
            ces[index] = $g(e);
        });
        
        // 实际上 objs 元素多个时，只对一个起作用。
        var e = objs[objs.length - 1];
        if (Number.is(pos))
        {
            var childElements = e.childElements(); // childElements 后面不变了
            if (pos >= 0 && pos < (childElements.length - 1))
            {
                ces.each(function(ce){
                    e.insertBefore(ce, childElements[pos + 1]);
                });
            }
            else if (pos < 0 && childElements.length > 0)
            {
                ces.each(function(ce){
                    e.insertBefore(ce, childElements[0]);
                });
            }
            else
            {
                ces.each(function(ce){
                    e.appendChild(ce);
                });
            }
        }
        else if (pos)
        {
            pos = $g(pos);
            if (pos.nextSibling)
            {
                pos = pos.nextSibling;
                ces.each(function(ce){
                    e.insertBefore(ce, pos);
                });
            }
            else
            {
                ces.each(function(ce){
                    e.appendChild(ce);
                });
            }
        }
    };
    
    
    obj.before = function()
    {
    ///<summary>获取、设置前面的兄弟元素。语法：$1.before([element])</summary>
    ///<param name="element" type="string/object/array">可选。要插入的兄弟元素 id，或元素对象，或元素对象数组。</param>
    ///<returns type="object/array">前面的兄弟元素。</returns>
        // 某些浏览器中有 previousElementSibling 属性，有类似功能，但它是属性，故不能插入。
        
        // 插入
        var element = Function.overload(0, null);
        if (element)
        {
            // 实际上 objs 元素多个时，只对一个起作用，所以直接处理最后一个。
            var e = objs[objs.length - 1];
            $g(e.parentNode).insert(element, e);
        }
        
        var result = objs.each(function(e, index){
            var beforeElement = null;
            
            var brothers = $g(e.parentNode).childElements();
            for (var i = 0; i < brothers.length; i++)
            {
                if (brothers[i] == e)
                {
                    if (i > 0)
                    {
                        beforeElement = brothers[i - 1];
                    }
                    break;
                }
            }
            
            return beforeElement;
        });
        
        return ((!Array.is(obj)) ? result[0] : result);
    };
    
    
    obj.after = function()
    {
    ///<summary>获取、设置后面的兄弟元素。语法：$1.after([element])</summary>
    ///<param name="element" type="string/object/array">可选。要追加的兄弟元素 id，或元素对象，或元素对象数组。</param>
    ///<returns type="object/array">后面的兄弟元素。</returns>
        // 某些浏览器中有 nextElementSibling 属性，有类似功能，但它是属性，故不能追加。
        
        // 追加
        var element = Function.overload(0, null);
        if (element)
        {
            // 实际上 objs 元素多个时，只对一个起作用，所以直接处理最后一个。
            var e = objs[objs.length - 1];
            $g(e.parentNode).append(element, e);
        }
        
        var result = objs.each(function(e, index){
            var afterElement = null;
            
            var brothers = $g(e.parentNode).childElements();
            for (var i = 0; i < brothers.length; i++)
            {
                if (brothers[i] == e)
                {
                    if (i < brothers.length - 1)
                    {
                        afterElement = brothers[i + 1];
                    }
                    break;
                }
            }
            
            return afterElement;
        });
        
        return ((!Array.is(obj)) ? result[0] : result);
    };
    
    
    obj.detach = function()
    {
    ///<summary>从 HTML 结构中分离当前元素。语法：$1.detach()</summary>
        objs.each(function(e, index){
            e.parentNode.removeChild(e);
        });
    };
    
    
    obj.collapse = function()
    {
    ///<summary>折叠元素。语法：$1.collapse([speed[, step]])</summary>
    ///<param name="speed" type="integer/string">可选。折叠元素速度。可选值为：slow、normal、fast、immediate，默认值为 normal。也可用数字，数字越大越慢。默认值为：normal。</param>
    ///<param name="step" type="number">可选。表示折叠步长的数字。默认值为 20。</param>
        var speed = Function.overload(0, "normal", true);
        speed = Array.map(["slow", "normal", "fast", "immediate"], [100, 50, 10, 0], speed, (!Number.is(speed) ? 50 : speed));
        var step = Function.overload(1, 20, true);
        
        objs.each(function(e){
            e.style.overflow = "hidden";
            var ezjCollapseKey = "ezj_collapse_key_" + Math.nextRandom(0, 999999999);
            e.ezjCollapseKey = ezjCollapseKey;
            
            function collapse(ezjCollapseKey)
            {
                // 失效判断
                if (ezjCollapseKey != e.ezjCollapseKey)
                {
                    return;
                }
                
                if (e.scrollHeight <= 0)
                {
                    return;
                }
                
                // speed 为 0，表示立即设置到完全折叠的状态。
                if (speed == 0)
                {
                    e.style.height = "0px";
                    return;
                }
                
                if (!String.isNullOrEmpty(e.style.height))
                {
                    e.style.height = Math.max((parseInt(e.style.height) - step), 0) + "px";
                }
                else
                {
                    e.style.height = Math.max((e.scrollHeight - step), 0) + "px";
                }
                
                setTimeout(function(){
                    collapse(ezjCollapseKey);
                }, speed);
            }
            
            collapse(ezjCollapseKey);
        });
    };
    
    
    obj.expand = function()
    {
    ///<summary>展开元素。语法：$1.expand([speed[, step]])</summary>
    ///<param name="speed" type="integer/string">可选。展开元素速度。可选值为：slow、normal、fast、immediate，默认值为 normal。也可用数字，数字越大越慢。默认值为：normal。</param>
    ///<param name="step" type="number">可选。表示展开步长的数字。默认值为 20。</param>
        var speed = Function.overload(0, "normal", true);
        speed = Array.map(["slow", "normal", "fast", "immediate"], [100, 50, 10, 0], speed, (!Number.is(speed) ? 50 : speed));
        var step = Function.overload(1, 20, true);
        
        objs.each(function(e){
            e.style.overflow = "hidden";
            // 与 collapse 使用同一 key
            var ezjCollapseKey = "ezj_collapse_key_" + Math.nextRandom(0, 999999999);
            e.ezjCollapseKey = ezjCollapseKey;
            
            function expand(ezjCollapseKey)
            {
                // 失效判断
                if (ezjCollapseKey != e.ezjCollapseKey)
                {
                    return;
                }
                
                if (!String.isNullOrEmpty(e.style.height))
                {
                    if (parseInt(e.style.height) >= e.scrollHeight)
                    {
                        e.style.height = e.scrollHeight + "px";
                        return; // 已经完全展开
                    }
                }
                else
                {
                    return; // 没有指定 style.height，也表示已经完全展开
                }
                
                // speed 为 0，表示立即设置到完全展开的状态。
                if (speed == 0)
                {
                    e.style.height = e.scrollHeight + "px";
                    return;
                }
                
                // 前面已经判断了，有 style.height。
                e.style.height = Math.min((parseInt(e.style.height) + step), e.scrollHeight) + "px";
                
                setTimeout(function(){
                    expand(ezjCollapseKey);
                }, speed);
            }
            
            expand(ezjCollapseKey);
        });
    };
    
    
    obj.opacity = function()
    {
    ///<summary>获取、设置元素的透明度值。语法：$1.opacity([value])</summary>
    ///<param name="value" type="number">可选。要设置的透明度值，范围为 0-1。</param>
    ///<returns type="number/array">元素的透明度值或新值。</returns>
        var value = Function.overload(0, null);
        
        var result = objs.each(function(e, index){
            var result = null;
            
            if (String.is(e.style.opacity))
            {
                // 支持 opacity
                if (value != null)
                {
                    e.style.opacity = value;
                }
                result = parseFloat(e.style.opacity);
                if (!Number.is(result))
                {
                    result = 1;
                }
            }
            else if (String.is(e.style.filter))
            {
                // 支持 filter
                if (value != null)
                {
                    e.style.filter = "alpha(opacity=" + value * 100 + ")";
                }
                var pos = e.style.filter.toLowerCase().indexOf("opacity=");
                if (pos > 0)
                {
                    result = e.style.filter.substr(pos + 8);
                    if (result.left(1) == "'" || result.left(1) == "\"")
                    {
                        result = result.substr(1);
                    }
                    result = parseInt(result) / 100;
                }
                else
                {
                    result = 1;
                }
            }
            
            return result;
        });
        
        return ((!Array.is(obj)) ? result[0] : result);
    };
    
    
    obj.fadeIn = function()
    {
    ///<summary>渐显元素。语法：$1.fadeIn([speed[, onComplete]])</summary>
    ///<param name="speed" type="integer/string">可选。渐显速度。可选值为：slow、normal、fast，默认值为 normal。也可用数字，数字越大越慢。</param>
    ///<param name="onComplete" type="function">可选。渐显完成后要执行的函数。</param>
        if (arguments.length >= 2)
        {
            obj.fadeTo(1, arguments[0], arguments[1]);
        }
        else if (arguments.length >= 1)
        {
            obj.fadeTo(1, arguments[0]);
        }
        else
        {
            obj.fadeTo(1);
        }
    };
    
    
    obj.fadeOut = function()
    {
    ///<summary>渐隐元素。语法：$1.fadeOut([speed[, onComplete]])</summary>
    ///<param name="speed" type="integer/string">可选。渐隐速度。可选值为：slow、normal、fast，默认值为 normal。也可用数字，数字越大越慢。</param>
    ///<param name="onComplete" type="function">可选。渐隐完成后要执行的函数。</param>
        if (arguments.length >= 2)
        {
            obj.fadeTo(0, arguments[0], arguments[1]);
        }
        else if (arguments.length >= 1)
        {
            obj.fadeTo(0, arguments[0]);
        }
        else
        {
            obj.fadeTo(0);
        }
    };
    
    
    obj.fadeTo = function(opacity)
    {
    ///<summary>渐显或渐隐元素。语法：$1.fadeTo(opacity[, speed[, onComplete]])</summary>
    ///<param name="opacity" type="number">要渐显或渐隐达到的透明度值。范围为 0-1。</param>
    ///<param name="speed" type="integer/string">可选。渐显或渐隐速度。可选值为：slow、normal、fast，默认值为 normal。也可用数字，数字越大越慢。</param>
    ///<param name="onComplete" type="function">可选。渐显或渐隐完成后要执行的函数。</param>
        var speed = Function.overload(1, "normal");
        speed = Array.map(["slow", "normal", "fast"], [100, 50, 10], speed, (!Number.is(speed) ? 50 : speed));
        var onComplete = Function.overload(2, null);
        
        objs.each(function(e, index){
            var ezjFadeKey = "ezj_fade_key_" + Math.nextRandom(0, 999999999);
            e.ezjFadeKey = ezjFadeKey;
            
            function animate(ezjFadeKey)
            {
                // 失效判断
                if (ezjFadeKey != e.ezjFadeKey)
                {
                    return;
                }
                var opacityStep = 0.05;
                var curOpacity = e.opacity();
                if (curOpacity > (opacity + opacityStep))
                {
                    e.opacity(curOpacity - opacityStep);
                    setTimeout(function(){ animate(ezjFadeKey); }, speed);
                }
                else if (curOpacity > opacity)
                {
                    e.opacity(opacity);
                    Function.run(onComplete, e);
                }
                else if (curOpacity < (opacity - opacityStep))
                {
                    e.opacity(curOpacity + opacityStep);
                    setTimeout(function(){ animate(ezjFadeKey); }, speed);
                }
                else if (curOpacity < opacity)
                {
                    e.opacity(opacity);
                    Function.run(onComplete, e);
                }
                else
                {
                    Function.run(onComplete, e);
                }
            }
            
            animate(ezjFadeKey);
        });
    };
    
    
    obj.show = function()
    {
    ///<summary>以剪切的方式显示或隐藏元素。语法：$1.show([start[, end[, speed[, step[, onComplete]]]]])</summary>
    ///<param name="start" type="string/array/object">可选。表示剪切开始位置的字符串，或具有四个元素的数组，或对象。默认值为：tl。</param>
    ///<param name="end" type="string/array/object">可选。表示剪切结束位置的字符串，或具有四个元素的数组，或对象。默认值为：max。</param>
    ///<param name="speed" type="integer/string">可选。显示或隐藏速度。可选值为：slow、normal、fast，默认值为 normal。也可用数字，数字越大越慢。默认值为：normal。</param>
    ///<param name="step" type="number/array/object">可选。表示剪切步长的数字，或具有两个元素的数组，或具有四个元素的数组，或对象。默认值为 20。</param>
    ///<param name="onComplete" type="function">可选。显示或隐藏完成后要执行的函数。</param>
        var start = Function.overload(0, "tl", true);
        var end = Function.overload(1, "max", true);
        var speed = Function.overload(2, "normal", true);
        var step = Function.overload(3, 20, true);
        var onComplete = Function.overload(4, null);
        speed = Array.map(["slow", "normal", "fast"], [100, 50, 10], speed, (!Number.is(speed) ? 50 : speed));
        if (Number.is(step))
        {
            step = { t:step, r:step, b:step, l:step };
        }
        else if (Array.is(step))
        {
            if (step.length == 2)
            {
                step = { t:step[1], r:step[0], b:step[1], l:step[0] };
            }
            else
            {
                step = { t:step[2], r:step[1], b:step[3], l:step[0] };
            }
        }
        else if (Object.is(step))
        {
            // do nothing
        }
        
        
        function getClip(clipCmd, space, clipStr, defaultClip)
        {
            var clip = defaultClip;
            
            if (String.is(clipCmd))
            {
                // left, right
                if (clipCmd.indexOf("l") >= 0)
                {
                    clip.l = 0;
                    clip.r = 0;
                }
                else if (clipCmd.indexOf("r") >= 0)
                {
                    clip.l = space.width;
                    clip.r = space.width;
                }
                else if (clipCmd.indexOf("c") >= 0)
                {
                    clip.l = parseInt(space.width / 2);
                    clip.r = parseInt(space.width / 2);
                }
                else
                {
                    clip.l = 0;
                    clip.r = space.width;
                }
                
                // top, bottom
                if (clipCmd.indexOf("t") >= 0)
                {
                    clip.t = 0;
                    clip.b = 0;
                }
                else if (clipCmd.indexOf("b") >= 0)
                {
                    clip.t = space.height;
                    clip.b = space.height;
                }
                else if (clipCmd.indexOf("m") >= 0 && clipCmd.indexOf("max") < 0)
                {
                    clip.t = parseInt(space.height / 2);
                    clip.b = parseInt(space.height / 2);
                }
                else
                {
                    clip.t = 0;
                    clip.b = space.height;
                }
                
                // left, right, top, bottom
                if (clipCmd.indexOf("max") >= 0)
                {
                    clip.l = 0;
                    clip.r = space.width;
                    clip.t = 0;
                    clip.b = space.height;
                }
                
                // left, right, top, bottom
                if (clipCmd.indexOf("cur") >= 0)
                {
                    if (clipStr != "")
                    {
                        clipStr = clipStr.replace("rect(", "");
                        var clipArr = (clipStr.indexOf(" ") >= 0 ? clipStr.split(" ") : clipStr.split(","));
                        clip.l = parseInt(clipArr[3]);
                        clip.r = parseInt(clipArr[1]);
                        clip.t = parseInt(clipArr[0]);
                        clip.b = parseInt(clipArr[2]);
                    }
                    else
                    {
                        clip.l = 0;
                        clip.r = space.width;
                        clip.t = 0;
                        clip.b = space.height;
                    }
                }
            }
            else if (Array.is(clipCmd))
            {
                clip.t = clipCmd[2];
                clip.r = clipCmd[1];
                clip.b = clipCmd[3];
                clip.l = clipCmd[0];
            }
            else if (Object.is(clipCmd))
            {
                clip.t = clipCmd.t;
                clip.r = clipCmd.r;
                clip.b = clipCmd.b;
                clip.l = clipCmd.l;
            }
            
            return clip;
        }
        
        
        objs.each(function(e, index){
            e.style.position = "absolute";
            e.style.overflow = "hidden";
            e.display(true);
            var ezjShowKey = "ezj_show_key_" + Math.nextRandom(0, 999999999);
            e.ezjShowKey = ezjShowKey;
            
            var space = e.space();
            var startClip = getClip(start, space, e.style.clip, { t:0, r:0, b:0, l:0 });
            var endClip = getClip(end, space, e.style.clip, { t:0, r:space.width, b:space.height, l:0 });
            var pos = { t:startClip.t, r:startClip.r, b:startClip.b, l:startClip.l};
            
            function animate(ezjShowKey)
            {
                // 失效判断
                if (ezjShowKey != e.ezjShowKey)
                {
                    return;
                }
                
                // 设置
                e.style.clip = "rect(" + pos.t + "px," +
                                   pos.r + "px," +
                                   pos.b + "px," +
                                   pos.l + "px)";
                
                // 完成判断
                if (pos.t == endClip.t &&
                    pos.r == endClip.r &&
                    pos.b == endClip.b &&
                    pos.l == endClip.l)
                {
                    Function.run(onComplete, e);
                    return;
                }
                
                // 计算下一位置
                if (endClip.t > startClip.t)
                {
                    pos.t = ((pos.t + step.t < endClip.t) ? pos.t + step.t : endClip.t);
                }
                else if (endClip.t < startClip.t)
                {
                    pos.t = ((pos.t - step.t > endClip.t) ? pos.t - step.t : endClip.t);
                }
                
                if (endClip.r > startClip.r)
                {
                    pos.r = ((pos.r + step.r < endClip.r) ? pos.r + step.r : endClip.r);
                }
                else if (endClip.r < startClip.r)
                {
                    pos.r = ((pos.r - step.r > endClip.r) ? pos.r - step.r : endClip.r);
                }
                
                if (endClip.b > startClip.b)
                {
                    pos.b = ((pos.b + step.b < endClip.b) ? pos.b + step.b : endClip.b);
                }
                else if (endClip.b < startClip.b)
                {
                    pos.b = ((pos.b - step.b > endClip.b) ? pos.b - step.b : endClip.b);
                }
                
                if (endClip.l > startClip.l)
                {
                    pos.l = ((pos.l + step.l < endClip.l) ? pos.l + step.l : endClip.l);
                }
                else if (endClip.l < startClip.l)
                {
                    pos.l = ((pos.l - step.l > endClip.l) ? pos.l - step.l : endClip.l);
                }
                
                setTimeout(function(){
                    animate(ezjShowKey);
                }, speed);
            }
            
            animate(ezjShowKey);
        });
    };
    
    
    obj.up = function()
    {
    ///<summary>将 SELECT 选中的项目上移。语法：$1.up()</summary>
        objs.each(function(e){
            if (e && e.tagName.equalsIgnoreCase("SELECT"))
            {
                for (var i = 0; i < (e.options.length - 1); i++)
                {
                    if (!e.options[i].selected && e.options[i + 1].selected)
                    {
                        var value = e.options[i].value;
                        var text = e.options[i].text;
                        e.options[i] = new Option(e.options[i + 1].text, e.options[i + 1].value);
                        e.options[i].selected = true;
                        e.options[i + 1] = new Option(text, value);
                    }
                }
            }
        });
    };
    
    
    obj.down = function()
    {
    ///<summary>将 SELECT 选中的项目下移。语法：$1.down()</summary>
        objs.each(function(e){
            if (e && e.tagName.equalsIgnoreCase("SELECT"))
            {
                for (var i = e.options.length - 1; i > 0; i--)
                {
                    if (!e.options[i].selected && e.options[i-1].selected)
                    {
                        var value = e.options[i].value;
                        var text = e.options[i].text;
                        e.options[i] = new Option(e.options[i-1].text, e.options[i-1].value);
                        e.options[i].selected = true;
                        e.options[i-1] = new Option(text, value);
                    }
                }
            }
        });
    };
    
    
    obj.labelFor = function()
    {
    ///<summary>为 LABEL 自动创建针对前面多选框、单选框或后面文本框的 for 属性。语法：$1.labelFor()</summary>
        objs.each(function(e){
            if (e && e.tagName.equalsIgnoreCase("LABEL"))
            {
                var forElement = e.before();
                if (forElement &&
                    forElement.tagName.equalsIgnoreCase("INPUT") &&
                    (forElement.type.equalsIgnoreCase("checkbox") || forElement.type.equalsIgnoreCase("radio")))
                {
                    var id = forElement.id;
                    if (String.isNullOrEmpty(id))
                    {
                        id = "ezj_dom_labelfor_" + Math.nextRandom(0, 999999999);
                        forElement.id = id;
                    }
                    
                    e.htmlFor = id;
                }
                else
                {
                    forElement = e.after();
                    if (forElement &&
                        forElement.tagName.equalsIgnoreCase("INPUT") &&
                        (forElement.type.equalsIgnoreCase("text") || forElement.type.equalsIgnoreCase("password")))
                    {
                        var id = forElement.id;
                        if (String.isNullOrEmpty(id))
                        {
                            id = "ezj_dom_labelfor_" + Math.nextRandom(0, 999999999);
                            forElement.id = id;
                        }
                        e.htmlFor = id;
                    }
                }
            }
        });
    };
    
    
    if (obj == document.body)
    {
        // 不会是数组了
        obj.mask = function()
        {
        ///<summary>遮罩文档。语法：$g(document.body).mask([opacity[, color]])</summary>
        ///<param name="opacity" type="number">可选。遮罩的透明度，范围为 0-1。默认值为 0.7。</param>
        ///<param name="color" type="string">可选。遮罩的颜色。默认值为：#ccc。</param>
            var opacity = Function.overload(0, 0.7, true);
            var color = Function.overload(1, "#ccc", true);
            
            if (!obj.maskObj)
            {
                obj.maskObj = $c("div", null);
                
                // 标准模式
                obj.maskObj.style.position = "fixed";
                obj.maskObj.style.top = "0px";
                obj.maskObj.style.left = "0px";
                
                if (ezj.ie.is() && ezj.ie.engine <= 6)
                {
                    // 这里很头痛，因为 IE 6 的 position 认 fixed 这几个字母，只是不实现其效果，
                    // 所以无法通过 position 来判断它是不是支持 fixed，只好通过 IE 引擎来判断。
                    // 兼容模式
                    obj.maskObj.style.position = "absolute";
                    obj.maskObj.style.top = (document.documentElement.scrollTop || document.body.scrollTop) + "px";
                    obj.maskObj.style.left = (document.documentElement.scrollLeft || document.body.scrollLeft) + "px";
                }
                
                obj.maskObj.style.width = (document.documentElement.clientWidth || document.body.clientWidth) + "px";
                obj.maskObj.style.height = (document.documentElement.clientHeight || document.body.clientHeight) + "px";
            }
            
            obj.maskObj.opacity(opacity);
            obj.maskObj.bgcolor(color);
            $g(document.body).insert(obj.maskObj);
            
            function reset()
            {
                if (ezj.ie.is() && ezj.ie.engine <= 6)
                {
                    // 这里很头痛，因为 IE 6 的 position 认 fixed 这几个字母，只是不实现其效果，
                    // 所以无法通过 position 来判断它是不是支持 fixed，只好通过 IE 引擎来判断。
                    // 兼容模式
                    obj.maskObj.style.top = (document.documentElement.scrollTop || document.body.scrollTop) + "px";
                    obj.maskObj.style.left = (document.documentElement.scrollLeft || document.body.scrollLeft) + "px";
                }
             
                obj.maskObj.style.width = (document.documentElement.clientWidth || document.body.clientWidth) + "px";
                obj.maskObj.style.height = (document.documentElement.clientHeight || document.body.clientHeight) + "px";
            }
            
            if (!obj.maskEventOk)
            {
                $g(window).scroll(reset);
                $g(window).resize(reset);
                obj.maskEventOk = true;
            }
        };
        
        
        obj.unmask = function()
        {
        ///<summary>取消文档遮罩。语法：$g(document.body).unmask()</summary>
            if (obj.maskObj)
            {
                obj.maskObj.detach();
            }
        };
    }
    
    
    obj.addClass = function(className)
    {
    ///<summary>为元素添加 class。语法：$1.addClass(className)</summary>
    ///<param name="className" type="string">要添加的 class 名称，大小写敏感。</param>
        objs.each(function(e, index){
            var arr = e.className.splitEx(" ", true, true);
            if (!arr.exists(className))
            {
                if (String.isNullOrEmpty(e.className))
                {
                    e.className = className;
                }
                else
                {
                    e.className += " " + className;
                }
            }
        });
    };
    
    
    obj.removeClass = function(className)
    {
    ///<summary>为元素移除 class。语法：$1.removeClass(className)</summary>
    ///<param name="className" type="string">要移除的 class 名称，大小写敏感。</param>
        objs.each(function(e, index){
            var arr = e.className.splitEx(" ", true, true);
            var newClassName = "";
            arr.each(function(en, index){
                if (en != className)
                {
                    if (String.isNullOrEmpty(newClassName))
                    {
                        newClassName = en;
                    }
                    else
                    {
                        newClassName += " " + en;
                    }
                }
            });
            e.className = newClassName;
        });
    };
    
    
    obj.toggleClass = function(className)
    {
    ///<summary>切换元素的 class，若存在 className 则移除之，若不存在则添加之。语法：$1.toggleClass(className)</summary>
    ///<param name="className" type="string">要切换的 class 名称，大小写敏感。</param>
        objs.each(function(e, index){
            var arr = e.className.splitEx(" ", true, true);
            if (!arr.exists(className))
            {
                // 不存在，添加之
                e.addClass(className);
            }
            else
            {
                // 存在，移除之
                e.removeClass(className);
            }
        });
    };
    
    
    obj.addListener = function(eventName, handler)
    {
    ///<summary>添加事件处理程序。语法：$1.addListener(eventName, handler)</summary>
    ///<param name="eventName" type="string">事件名称，比如：click、mouseover、mouseout，不能以 on 开头。</param>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            if (e.attachEvent)
            {
                e.attachEvent("on" + eventName, function() { handler(e); });
            }
            else if (e.addEventListener)
            {
                e.addEventListener(eventName, function() { handler(e); }, false);
            }
        });
    };
    
    
    obj.click = function(handler)
    {
    ///<summary>添加 click 事件处理程序。语法：$1.click(handler)</summary>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            e.addListener("click", handler);
        });
    };
    
    
    obj.mousedown = function(handler)
    {
    ///<summary>添加 mousedown 事件处理程序。语法：$1.mousedown(handler)</summary>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            e.addListener("mousedown", handler);
        });
    };
    
    
    obj.mouseup = function(handler)
    {
    ///<summary>添加 mouseup 事件处理程序。语法：$1.mouseup(handler)</summary>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            e.addListener("mouseup", handler);
        });
    };
    
    
    obj.mouseover = function(handler)
    {
    ///<summary>添加 mouseover 事件处理程序。语法：$1.mouseover(handler)</summary>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            e.addListener("mouseover", handler);
        });
    };
    
    
    obj.mouseout = function(handler)
    {
    ///<summary>添加 mouseout 事件处理程序。语法：$1.mouseout(handler)</summary>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            e.addListener("mouseout", handler);
        });
    };
    
    
    obj.mousemove = function(handler)
    {
    ///<summary>添加 mousemove 事件处理程序。语法：$1.mousemove(handler)</summary>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            e.addListener("mousemove", handler);
        });
    };
    
    
    obj.keydown = function(handler)
    {
    ///<summary>添加 keydown 事件处理程序。语法：$1.keydown(handler)</summary>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            e.addListener("keydown", handler);
        });
    };
    
    
    obj.keyup = function(handler)
    {
    ///<summary>添加 keyup 事件处理程序。语法：$1.keyup(handler)</summary>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            e.addListener("keyup", handler);
        });
    };
    
    
    obj.enter = function(handler)
    {
    ///<summary>添加 focus 事件处理程序。语法：$1.enter(handler)</summary>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            e.addListener("focus", handler);
        });
    };
    
    
    obj.leave = function(handler)
    {
    ///<summary>添加 blur 事件处理程序。语法：$1.leave(handler)</summary>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            e.addListener("blur", handler);
        });
    };
    
    
    obj.change = function(handler)
    {
    ///<summary>添加 change 事件处理程序。语法：$1.change(handler)</summary>
    ///<param name="handler" type="function">事件处理程序。</param>
        objs.each(function(e, index){
            e.addListener("change", handler);
        });
    };
    
    
    if (obj == window || obj == document.body)
    {
        // 不会是数组了
        obj.ready = function(handler)
        {
        ///<summary>为 window 对象添加 load 事件处理程序。语法：$g(window).ready(handler)</summary>
        ///<param name="handler" type="function">事件处理程序。</param>
            var oldLoadHandler = window.onload;
            window.onload =  function()
            {
                Function.run(oldLoadHandler, window);
                handler(window);
            };
        };
    }
    
    
    if (obj == window)
    {
        // 不会是数组了
        obj.resize = function(handler)
        {
        ///<summary>为 window 对象添加 resize 事件处理程序。语法：$g(window).resize(handler)</summary>
        ///<param name="handler" type="function">事件处理程序。</param>
            // 该事件不是 DOM 2 中的内容，不支持 attachEvent 和 addEventListener
            var oldResizeHandler = window.onresize;
            window.onresize =  function()
            {
                Function.run(oldResizeHandler, window);
                handler(window);
            };
        };
        
        
        // 不会是数组了
        obj.scroll = function(handler)
        {
        ///<summary>为 window 对象添加 scroll 事件处理程序。语法：$g(window).scroll(handler)</summary>
        ///<param name="handler" type="function">事件处理程序。</param>
            var oldScrollHandler = window.onscroll;
            window.onscroll =  function()
            {
                Function.run(oldScrollHandler, window);
                handler(window);
            };
        };
    }
    
    
    return obj;
};


var $g = function(selector)
{
///<summary>等效于 new ezj.Dom(selector[, parentElement])。语法：$g(selector[, parentElement])</summary>
///<param name="selector" type="string/object/array">查询字符串，或元素对象，或元素对象数组。</param>
///<param name="parentElement" type="object">可选。在这个对象下查询子对象。</param>
///<returns type="object/array">元素对象，或元素对象数组。</returns>
    var parentElement = Function.overload(1, null);
    return new ezj.Dom(selector, parentElement);
};


if (typeof $ == "undefined")
{
    var $ = $g;
}


var $c = ezj.Dom.create = function(element)
{
///<summary>创建、设置、追加元素并返回。语法：$c(element[, attributes[, parentElement]])</summary>
///<param name="element" type="string/object">要创建、设置、追加的元素。既可以是 HTML 标签名称，也可以是字符串 literal（此时 attributes 必须含有 text 属性）、text、password、radio、checkbox、button、submit、reset 中的一个，也可以是元素对象。</param>
///<param name="attributes" type="object">可选。对象中的属性名称表示元素的属性名称，对象中的属性值表示元素的属性值；比如：{ param1 : value1, param2 : value2 }。</param>
///<param name="parentElement" type="string/object">可选。创建的元素存放的位置。</param>
///<returns type="object">经过 $g 函数包装的，新创建或新设置的元素。</returns>
    var e = null;
    
    if (String.is(element))
    {
        switch (element)
        {
            case "literal":
                e = document.createTextNode(Function.overload(1, null).text);
                break;
            case "text":
                e = document.createElement("input");
                e.type = "text";
                break;
            case "password":
                e = document.createElement("input");
                e.type = "password";
                break;
            case "textarea":
                e = document.createElement("textarea"); // 实际上这里没有简化什么
                break;
            case "radio":
                e = document.createElement("input");
                e.type = "radio";
                break;
            case "checkbox":
                e = document.createElement("input");
                e.type = "checkbox";
                break;
            case "button":
                e = document.createElement("input");
                e.type = "button";
                break;
            case "submit":
                e = document.createElement("input");
                e.type = "submit";
                break;
            case "reset":
                e = document.createElement("input");
                e.type = "reset";
                break;
            default:
                e = document.createElement(element);
                break;
        }
    }
    else
    {
        e = element;
    }
    
    
    if (element != "literal")
    {
        // 设置元素属性
        var attributes = Function.overload(1, null);
        if (Object.is(attributes))
        {
            for (var param in attributes)
            {
                if (param == "className")
                {
                    e.className = eval("attributes." + param);
                }
                else if (param == "innerHTML")
                {
                    e.innerHTML = eval("attributes." + param);
                }
                else if (param == "val")
                {
                    $g(e).val(eval("attributes." + param));
                }
                else
                {
                    e.setAttribute(param, eval("attributes." + param));
                }
            }
        }
    }
    
    
    //追加元素
    var parentElement = Function.overload(2, null);
    if (parentElement != null)
    {
        $g(parentElement).appendChild(e);
    }
    
    if (element == "literal")
    {
        return e;
    }
    
    return $g(e);
};


var $v = function(selector)
{
///<summary>获取、设置元素的值。将元素的 value、src、innerHTML 属性值作为值，对于 SELECT 元素则返回具有 text、value 属性的对象数组。语法：$v(selector, [value[, mode]])</summary>
///<param name="selector" type="string/object/array">要获取、设置值的元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="value" type="string/array">可选。元素的新值。对于 SELECT 则为具有 text、value 属性的对象数组。</param>
///<param name="mode" type="integer">可选。赋值模式：-1 插入，0 重写，1 追加。默认值为 0。</param>
///<returns type="string/array">元素的值或新值。对于 SELECT 则为具有 text、value 属性的对象数组。</returns>
    var value = Function.overload(1, null);
    var mode = Function.overload(2, 0);
    return $g(selector).val(value, mode);
};


var $exists = function (id)
{
///<summary>获取 id 对应的元素是否存在。语法：$exists(id)</summary>
///<param name="id" type="string">要判断的元素 id。</param>
///<returns type="boolean">id 对应的元素是否存在。</returns>
    if (document.getElementById(id))
    {
        return true;
    }
    return false;
};


var $getData = function (id)
{
///<summary>获取 id 对应的元素的值。若元素不存在，则返回 null。语法：$getData(id)</summary>
///<param name="id" type="string">要获取的元素 id。</param>
///<returns type="string/array">id 对应的元素的值。若对应的元素不存在，则为 null。</returns>
    if ($exists(id))
    {
        return $g(id).data();
    }
    return null;
};

//================================================================================


ezj.Ajax = function()
{
///<summary>处理 Ajax 的类。语法：new ezj.Ajax()</summary>
    this.xmlhttp = (function()
    {
        var xmlhttp = null;
        
        try
        {
            xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); // 若成功，Object.is 判断为 true
        }
        catch (e)
        {
            try
            {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e)
            {
                xmlhttp = null;
            }
        }
        
        if (!xmlhttp)
        {
            try
            {
                xmlhttp = new XMLHttpRequest(); // 即使成功，Object.is 判断也为 false
            }
            catch (e)
            {
                xmlhttp = null;
            }
        }
        
        if (!xmlhttp)
        {
            try
            {
                xmlhttp = window.createRequest();
            }
            catch (e)
            {
                xmlhttp = null;
            }
        }
        
        return xmlhttp;
    })(); // this.xmlhttp
    
    this.requestData = new function()
    {
        this.items = []; // 用于存储要发送的数据
        
        // 清空数据
        this.clear = function()
        {
            this.items.splice(0, this.items.count);
        };
        
        // 增加一个数据
        this.add = function(name, value)
        {
            this.items.push({ name : name, value : value });
        };
        
        // 以字符串形式返回要发送的数据，格式中：name1=value1&name2=value2...
        // 无数据则返回 ""
        this.getRequestString = function()
        {
            var str = "";
            for (var i = 0; i < this.items.length; i++)
            {
                if (str != "")
                {
                    str += "&" + this.items[i].name + "=" + encodeURIComponent(this.items[i].value);
                }
                else
                {
                    str = this.items[i].name + "=" + encodeURIComponent(this.items[i].value);
                }
            }
            return str;
        };
    }; // this.requestData
    
    this.onOk = null; // 当返回状态码是 200 时。
    this.onError = null; // 当返回状态码不是 200 时，即使是在 201 到 299 之间，即使是 304，也当作 Error 对待。
    this.onComplete = null; // 当处理完成时，不论成功和失败均要执行。
    
    
    // Web Service
    // 这里使用 Web Service 由于是基于 JavaScript 的 Ajax 的，所以因权限问题，也只能调用本服务器的 Web Service。
    this.service = function(url, namespace, wsMethod)
    {
    // this.service(url, namespace, wsMethod[, key])
        var key = Function.overload(3, null); // 默认每次调用都是一个单独序列
        var keyOrder = -1;
        if (key !== null)
        {
            keyOrder = ezj.Ajax.sequenceManager.add(key);
        }
        
        var data = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>" +
                   "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"" +
                                 " xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"" +
                                 " xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">" +
                   "<soap:Body>" +
                   "<" + wsMethod + " xmlns=\"" + namespace + "\">";
        for(var i = 0; i < this.requestData.items.length; i++)
        {
        　　data += "<" + this.requestData.items[i].name + ">" +
                    this.requestData.items[i].value +
                    "</" + this.requestData.items[i].name + ">";
        　　
        }
        data += "</" + wsMethod + ">" +
                "</soap:Body>" +
                "</soap:Envelope>";
        
        var xmlhttp = this.xmlhttp;
        var onOk = this.onOk;
        var onError = this.onError;
        var onComplete = this.onComplete;
        this.xmlhttp.onreadystatechange = function()
        {
            if (key !== null && ezj.Ajax.sequenceManager.max(key) != keyOrder)
            {
                return;
            }
            
            if (xmlhttp.readyState == 4)
            {
                if (xmlhttp.status == 200)
                {
                    // IE 中可使用类似如下形式：
                    // var onOkData = $x(xmlhttp.responseXML).nodes("/soap:Envelope/soap:Body/" + wsMethod + "Response/" + wsMethod + "Result")[0].cloneNode(true);
                     
                    // Firefox、Chrome 中可使用类似如下形式：
                    // var onOkData = $x(xmlhttp.responseXML).nodes("/*[local-name()='Envelope']/*[local-name()='Body']/" + wsMethod + "Response/" + wsMethod + "Result")[0].cloneNode(true);
                    
                    // IE、Firefox、Chrome 均有效
                    // 下面两种代码均有效
                    // var onOkData = $x(xmlhttp.responseText).xmlDom.getElementsByTagName(wsMethod + "Result")[0].firstChild.nodeValue;
                    // 输出的是字符串，所以如果 Web Service 的 Result 是 XML 字符串，则要注意，在处理该 XML 时外层要套一层根节点
                    var response = xmlhttp.responseXML.documentElement.childNodes[0].childNodes[0];
                    var result = null;
                    var onOkData = null;
                    if (response.childNodes.length > 0)
                    {
                        // response 下面有节点，说明 Web Service 有返回值，且返回值不为 null
                        result = response.childNodes[0];
                        if (result.childNodes.length > 0)
                        {
                            if (result.firstChild.nodeType != 1)
                            {
                                // Web Service 返回值是 boolean、int、string 等简单类型时
                                // 这里只是从 SOAP 中取值，故取出来全部是字符串，这里不会进行类型转换
                                // 需要由使用者自行来转换
                                onOkData = result.firstChild.nodeValue;
                            }
                            else
                            {
                                // Web Service 返回值是数组等较复杂的类型时
                                // 这里也不会进行类型转换，返回 xml 节点
                                // 需要由使用者自行来拆解 xml 为数组等实际的类型
                                onOkData = result.childNodes;
                            }
                        }
                    }
                    
                    Function.run(onOk, onOkData, xmlhttp.status);
                }
                else
                {
                    Function.run(onError, xmlhttp.responseText, xmlhttp.status);
                }
                Function.run(onComplete, xmlhttp.responseText, xmlhttp.status);
            }
        };
        this.xmlhttp.open("post",url, true);
        this.xmlhttp.setRequestHeader("Content-Type","text/xml; charset=utf-8");
        this.xmlhttp.setRequestHeader("SOAPAction", namespace + wsMethod);
        this.xmlhttp.send(data);
    };
    
    
    // request
    this.request = function(method, url)
    {
    // this.request(method, url[, key])
        var key = Function.overload(2, null); // 默认每次调用都是一个单独序列
        var keyOrder = -1;
        if (key !== null)
        {
            keyOrder = ezj.Ajax.sequenceManager.add(key);
        }
        
        if (method.equalsIgnoreCase("get"))
        {
            var qs = this.requestData.getRequestString();
            var pos = url.indexOf("?");
            var requestUrl = url;
            if (qs.length > 0 && pos < 0)
            {
                requestUrl += "?" + qs;
            }
            else if (qs.length > 0 && pos < url.length - 1)
            {
                requestUrl += "&" + qs;
            }
            else if (qs.length > 0)
            {
                requestUrl += qs;
            }
            this.xmlhttp.open(method, requestUrl, true);
        }
        else
        {
            this.xmlhttp.open(method, url, true);
            this.xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        }
        
        // 事件相关
        var xmlhttp = this.xmlhttp;
        var onOk = this.onOk;
        var onError = this.onError;
        var onComplete = this.onComplete;
        this.xmlhttp.onreadystatechange = function()
        {
            if (key !== null && ezj.Ajax.sequenceManager.max(key) != keyOrder)
            {
                return;
            }
            
            if (xmlhttp.readyState == 4)
            {
                if (xmlhttp.status == 200)
                {
                    // 不一定每个服务器都能正确输出 content-type
                    // 没有 content-type 时，getResponseHeader 可能返回 null
                    if (xmlhttp.getResponseHeader("content-type") == "text/xml" || xmlhttp.responseText.left(5) == "<?xml")
                    {
                        Function.run(onOk, xmlhttp.responseXML, xmlhttp.status);
                    }
                    else
                    {
                        Function.run(onOk, xmlhttp.responseText, xmlhttp.status);
                    }
                }
                else
                {
                    Function.run(onError, xmlhttp.responseText, xmlhttp.status);
                }
                Function.run(onComplete, xmlhttp.responseText, xmlhttp.status);
            }
        };

        // 发送
        if (method.equalsIgnoreCase("get"))
        {
            this.xmlhttp.send(null);
        }
        else
        {
            this.xmlhttp.send(this.requestData.getRequestString());
        }
    };
};


// 一个序列指同一件事，被反复执行。由于 Ajax 的异步性，所以为了保证数据有效，同一序列中仅最后一个执行有效。
// 比如级联菜单中，第一列表改变会影响第二列表的改变，
// 但第一列表改变时，第二列表还没来得及改变，第一列表又已经变了，前次就应该失效。
ezj.Ajax.sequences = [];
ezj.Ajax.sequenceManager = function()
{
    
};
ezj.Ajax.sequenceManager.add = function(key)
{
    if (Array.is(ezj.Ajax.sequences[key]))
    {
        ezj.Ajax.sequences[key].push(key);
    }
    else
    {
        ezj.Ajax.sequences[key] = [key];
    }
    return ezj.Ajax.sequences[key].length;
};
ezj.Ajax.sequenceManager.max = function(key)
{
    return ezj.Ajax.sequences[key].length;
};


//================================================================================


ezj.ajax = function()
{
///<summary>处理 Ajax 的对象。语法：ezj.ajax</summary>
};


ezj.ajax.bind = function(url, element)
{
///<summary>利用 Ajax 的方式，访问一个网页，并将网页内容绑定到 element。语法：ezj.ajax.bind(url, element[, errorText])</summary>
///<param name="url" type="string">要访问的网页的 URL。URL 中传送中文 QueryString 可能会有问题。</param>
///<param name="element" type="string/object">要绑定的元素 id 或元素对象。</param>
///<param name="errorText" type="string">可选。Ajax 读取失败时要显示的内容。</param>
    var errorHandler = null;
    if (arguments.length >= 3)
    {
        var errorText = arguments[2];
        errorHandler = function(data, status)
        {
            $g(element).val(errorText);
        };
    }
    
    ezj.ajax.get(url, null, function(data, status)
    {
        $g(element).val(data);
    }, errorHandler);
};


ezj.ajax.get = function(url)
{
///<summary>利用 Ajax 的方式，用 get 方法访问一个网页。语法：ezj.ajax.get(url[, requestData[, onOk[, onError[, onComplete[, key]]]]])</summary>
///<param name="url" type="string">要访问的网页的 URL。URL 中传送中文 QueryString 可能会有问题。</param>
///<param name="requestData" type="object/array">可选。要发送的数据，比如：{ param1 : value1, param2 : value2 }，也可以是对象数组便于发送同名参数。</param>
///<param name="onOk" type="function">可选。访问成功后要执行的函数。</param>
///<param name="onError" type="function">可选。访问失败后要执行的函数。</param>
///<param name="onComplete" type="function">可选。访问完成（不论成功与否）后要执行的函数。</param>
///<param name="key" type="string">可选。本次调用的 key，用来在 onOk、onError、onComplete 对应的回调函数中确认是否是本次调用引发的。</param>
    var requestData = Function.overload(1, null);
    var onOk = Function.overload(2, null);
    var onError = Function.overload(3, null);
    var onComplete = Function.overload(4, null);
    var key = Function.overload(5, null);
    ezj.ajax.request("get", url, requestData, onOk, onError, onComplete, key);
};


ezj.ajax.post = function(url)
{
///<summary>利用 Ajax 的方式，用 post 方法访问一个网页。语法：ezj.ajax.post(url[, requestData[, onOk[, onError[, onComplete[, key]]]]])</summary>
///<param name="url" type="string">要访问的网页的 URL。URL 中传送中文 QueryString 可能会有问题。</param>
///<param name="requestData" type="object/array">可选。要发送的数据，比如：{ param1 : value1, param2 : value2 }，也可以是对象数组便于发送同名参数。</param>
///<param name="onOk" type="function">可选。访问成功后要执行的函数。</param>
///<param name="onError" type="function">可选。访问失败后要执行的函数。</param>
///<param name="onComplete" type="function">可选。访问完成（不论成功与否）后要执行的函数。</param>
///<param name="key" type="string">可选。本次调用的 key，用来在 onOk、onError、onComplete 对应的回调函数中确认是否是本次调用引发的。</param>
    var requestData = Function.overload(1, null);
    var onOk = Function.overload(2, null);
    var onError = Function.overload(3, null);
    var onComplete = Function.overload(4, null);
    var key = Function.overload(5, null);
    ezj.ajax.request("post", url, requestData, onOk, onError, onComplete, key);
};


ezj.ajax.submit = function(form)
{
///<summary>利用 Ajax 的方式提交一个表单。语法：ezj.ajax.submit(form[, onOk[, onError[, onComplete[, key]]]])</summary>
///<param name="form" type="string/object">要提交的表单元素 id 或表单元素对象。</param>
///<param name="onOk" type="function">可选。访问成功后要执行的函数。</param>
///<param name="onError" type="function">可选。访问失败后要执行的函数。</param>
///<param name="onComplete" type="function">可选。访问完成（不论成功与否）后要执行的函数。</param>
///<param name="key" type="string">可选。本次调用的 key，用来在 onOk、onError、onComplete 对应的回调函数中确认是否是本次调用引发的。</param>
    var f = $g(form);
    var ajax = new ezj.Ajax();
    
    // 方法和目标
    var method = ((f.getAttribute("method") != "") ? f.getAttribute("method") : "get");
    var url = ((f.getAttribute("action") != "") ? f.getAttribute("action") : location.href);
    
    // 表单内容
    ajax.requestData.clear();
    for (var i = 0; i < f.length; i++)
    {
        var e = f[i];
        if (e.name == "")
        {
            continue; // 无名称，跳过。
        }
        if (e.tagName.equalsIgnoreCase("INPUT") &&
               (e.type.equalsIgnoreCase("radio") || e.type.equalsIgnoreCase("checkbox")))
        {
            // 单选框和多选按钮
            if (e.checked)
            {
                ajax.requestData.add(e.name, e.value);
            }
        }
        else if (e.tagName.equalsIgnoreCase("SELECT"))
        {
            // 下拉列表框
            for (var j = 0; j < e.options.length; j++)
            {
                if (e.options[j].selected)
                {
                    ajax.requestData.add(e.name, e.options[j].value);
                }
            }
        }
        else
        {
            // 其他元素
            ajax.requestData.add(e.name, e.value);
        }
    }
    
    // 事件处理程序
    ajax.onOk = Function.overload(1, null);
    ajax.onError = Function.overload(2, null);
    ajax.onComplete = Function.overload(3, null);
    
    // 发送
    ajax.request(method, url, Function.overload(4, null));
};


ezj.ajax.request = function(method, url)
{
///<summary>利用 Ajax 的方式访问一个网页。语法：ezj.ajax.request(method, url[, requestData[, onOk[, onError[, onComplete[, key]]]]])</summary>
///<param name="method" type="string">Ajax 访问方法，可选值为 get、post。</param>
///<param name="url" type="string">要访问的网页的 URL。URL 中传送中文 QueryString 可能会有问题。</param>
///<param name="requestData" type="object/array">可选。要发送的数据，比如：{ param1 : value1, param2 : value2 }，也可以是对象数组便于发送同名参数。</param>
///<param name="onOk" type="function">可选。访问成功后要执行的函数。</param>
///<param name="onError" type="function">可选。访问失败后要执行的函数。</param>
///<param name="onComplete" type="function">可选。访问完成（不论成功与否）后要执行的函数。</param>
///<param name="key" type="string">可选。本次调用的 key，用来在 onOk、onError、onComplete 对应的回调函数中确认是否是本次调用引发的。</param>
    var ajax = new ezj.Ajax();
    
    var requestData = Function.overload(2, null);
    ajax.requestData.clear();
    requestData = Array.from(requestData);
    for (var i = 0; i < requestData.length; i++)
    {
        var data = requestData[i];
        if (Object.is(data))
        {
            for (var param in data)
            {
                ajax.requestData.add(param, eval("data." + param));
            }
        }
    }
    ajax.onOk = Function.overload(3, null);
    ajax.onError = Function.overload(4, null);
    ajax.onComplete = Function.overload(5, null);
    
    // 发送
    ajax.request(method, url, Function.overload(6, null));
};


ezj.ajax.service = function(url, namespace, wsMethod)
{
///<summary>利用 Ajax 的方式访问一个 Web Service。语法：ezj.ajax.service(url, namespace, wsMethod[, requestData[, onOk[, onError[, onComplete[, key]]]]])</summary>
///<param name="url" type="string">要访问的 Web Service 的 URL。URL 中传送中文 QueryString 可能会有问题。</param>
///<param name="namespace" type="function">Web Service 的命名空间，在 Web Service 的 WSDL 中有描述。</param>
///<param name="wsMethod" type="string">Web Service 方法名称。</param>
///<param name="requestData" type="object/array">可选。要发送的数据，也就是 Web Service 参数，比如：{ param1 : value1, param2 : value2 }，也可以是对象数组便于发送同名参数。</param>
///<param name="onOk" type="function">可选。访问成功后要执行的函数。</param>
///<param name="onError" type="function">可选。访问失败后要执行的函数。</param>
///<param name="onComplete" type="function">可选。访问完成（不论成功与否）后要执行的函数。</param>
///<param name="key" type="string">可选。本次调用的 key，用来在 onOk、onError、onComplete 对应的回调函数中确认是否是本次调用引发的。</param>
    var ajax = new ezj.Ajax();
    
    var requestData = Function.overload(3, null);
    ajax.requestData.clear();
    requestData = Array.from(requestData);
    for (var i = 0; i < requestData.length; i++)
    {
        var data = requestData[i];
        if (Object.is(data))
        {
            for (var param in data)
            {
                ajax.requestData.add(param, eval("data." + param));
            }
        }
    }
    ajax.onOk = Function.overload(4, null);
    ajax.onError = Function.overload(5, null);
    ajax.onComplete = Function.overload(6, null);
    
    // 发送
    ajax.service(url, namespace, wsMethod, Function.overload(7, null));
};


//================================================================================


ezj.checker = function()
{
///<summary>检查值规则的对象。语法：ezj.checker</summary>
};


ezj.checker.bind = function(element)
{
///<summary>绑定元素，自动检查元素输入的值是否符合规则。元素 checking 为检查表达式，检查不通过为元素 class 加上 error。语法：ezj.checker.bind(element)</summary>
///<param name="element" type="string/object/array">要绑定的元素查询字符串，或元素对象，或元素对象数组。</param>
    Array.from($g(element)).each(function (e)
    {
        if (e.tagName === null ||
            (e.tagName.toLowerCase() != "input" && e.tagName.toLowerCase() != "textarea"))
        {
            return;
        }

        function check(e)
        {
            var checking = e.getAttribute("checking");
            if (!String.isNullOrEmpty(checking))
            {
                if (!ezj.checker.check(e.val(), checking))
                {
                    e.addClass("error");
                }
                else
                {
                    e.removeClass("error");
                }
            }
        }
        e.enter(function (e)
        {
            check(e);
        });
        e.leave(function (e)
        {
            check(e);
        });
        e.keydown(function (e)
        {
            check(e);
        });
        e.keyup(function (e)
        {
            check(e);
        });
        e.mousedown(function (e)
        {
            check(e);
        });
        e.mouseup(function (e)
        {
            check(e);
        });
    });
};


ezj.checker.check = function(str, required, type, min, max)
{
///<summary>检查值规则并返回结果。语法：ezj.checker.check(str, required, type, min, max) 或 ezj.checker.check(str, expression)</summary>
///<param name="str" type="string">要进行规则检查的值，其类型为字符串，但正常情况下可转换成参数 type 对应的类型。</param>
///<param name="required" type="boolean">值是否是必须的。</param>
///<param name="type" type="string">按该类型对值进行检查，可选值为：int、decimal、date、time、datetime、varchar、nvarchar</param>
///<param name="min" type="any">值允许的最小值，若值是 varchar、nvarchar，则表示允许的最小字节、长度。</param>
///<param name="max" type="any">值允许的最大值，若值是 varchar、nvarchar，则表示允许的最大字节、长度。</param>
///<returns type="boolean">是否符合规则。</returns>
    if (!String.is(str))
    {
        // 类型不符合，不能进入检查。
        return false;
    }
    
    if (arguments.length == 2)
    {
        var r1 = "(^[<|\[]{1})"; // required
        var r2 = "(.+?)"; // type，加 ? 组成非谈贪婪匹配，将第一个 : 让给 r3
        var r3 = "([:]{1})";
        var r4 = "(.+)"; // min
        var r5 = "([_]{1})";
        var r6 = "(.+)"; // max
        var r7 = "(\]|>{1}$)";
        var reg = new RegExp(r1 + r2 + r3 + r4 + r5 + r6 + r7, "gi");
        reg.exec(arguments[1]);
        return ezj.checker.check(str, (RegExp.$1 == "<" ? true : false), RegExp.$2, RegExp.$4, RegExp.$6);
    }
    
    
    if (str == "")
    {
        if (required)
        {
            // 值是必须的，但这里没有提供值。
            return false;
        }
        else
        {
            return true;
        }
    }
    
    
    var result = false;
    
    if (type == "int")
    {
        // min、max 为数字类型或“min”、“max”
        if (Number.asInt(str))
        {
            var m = Number.from(str);
            if (min == "min" && max == "max")
            {
                result = true;
            }
            else if (min == "min")
            {
                result = (m <= max);
            }
            else if (max == "max")
            {
                result = (m >= min);
            }
            else
            {
                result = (m >= min && m <= max);
            }
        }
    }
    else if(type == "decimal")
    {
        // min、max 为数字类型或“min”、“max”
        if (Number.as(str))
        {
            var m = Number.from(str);
            if (min == "min" && max == "max")
            {
                result = true;
            }
            else if (min == "min")
            {
                result = (m <= max);
            }
            else if (max == "max")
            {
                result = (m >= min);
            }
            else
            {
                result = (m >= min && m <= max);
            }
        }
    }
    else if (type == "date")
    {
        // min、max 为字符串类型
        if (Date.asDate(str))
        {
            var dt = Date.from(str);
            min = Date.from(min);
            max = Date.from(max);
            result = (dt.compareTo(min) >= 0 && dt.compareTo(max) <= 0);
        }
    }
    else if (type == "time")
    {
        // min、max 为字符串类型
        if (Date.asTime(str))
        {
            var dt = Date.from("1900-1-1 " + str);
            min = Date.from("1900-1-1 " + min);
            max = Date.from("1900-1-1 " + max);
            result = (dt.compareTo(min) >= 0 && dt.compareTo(max) <= 0);
        }
    }
    else if (type == "datetime")
    {
        // min、max 为字符串类型或日期时间类型
        if (Date.asDateTime(str))
        {
            var dt = Date.from(str);
            if (!Date.is(min))
            {
                min = Date.from(min);
            }
            if (!Date.is(max))
            {
                max = Date.from(max);
            }
            result = (dt.compareTo(min) >= 0 && dt.compareTo(max) <= 0);
        }
    }
    else if (type == "varchar")
    {
        var bytes = str.length + str.wChrs();
        if (Number.as(max))
        {
            if (bytes >= min && bytes <= max)
            {
                result = true;
            }
        }
        else
        {
            if (bytes >= min && max.equalsIgnoreCase("max"))
            {
                result = true;
            }
        }
    }
    else if (type == "nvarchar")
    {
        if (Number.as(max))
        {
            if (str.length >= min && str.length <= max)
            {
                result = true;
            }
        }
        else
        {
            if (str.length >= min && max.equalsIgnoreCase("max"))
            {
                result = true;
            }
        }
    }
    
    return result;
};


ezj.checker.validate = function(element)
{
///<summary>检查元素输入的值是否符合规则并返回。元素 checking 为检查表达式，检查不通过为元素 class 加上 error。语法：ezj.checker.validate(element)</summary>
///<param name="element" type="string/object/array">要检查的元素查询字符串，或元素对象，或元素对象数组。</param>
///<returns type="boolean">是否所有元素都通过了检查。</returns>
    var checkOk = true;

    Array.from($g(element)).each(function (e)
    {
        var checking = e.getAttribute("checking");
        if (!String.isNullOrEmpty(checking))
        {
            if (!ezj.checker.check(e.val(), checking))
            {
                var errorMsg = e.getAttribute("errorMsg");
                if (!String.isNullOrEmpty(errorMsg) && checkOk)
                {
                    alert(errorMsg); // 加上 checkOk，避免多个错误时弹出多个提示框。
                }
                e.addClass("error"); // 所有的错误都给出这种提示。
                checkOk = false;
            }
        }
    });

    return checkOk;
};


//================================================================================


ezj.cookie = function()
{
///<summary>处理 Cookie 的对象。语法：ezj.cookie</summary>
};


ezj.cookie.read = function(name)
{
///<summary>获取 Cookie 的值。语法：ezj.cookie.read(name[, defaultValue])</summary>
///<param name="name" type="string">Cookie 名称。</param>
///<param name="defaultValue" type="string">可选。当对应的 Cookie 不存在时，返回 defaultValue。默认值为 null。</param>
///<returns type="string">对应 Cookie 的值。</returns>
    var defaultValue = Function.overload(1, null);
    
    var value = defaultValue;

    var cookieStr = document.cookie; // 取 Cookie 字符串，由于 expires 不可读，所以 expires 将不会出现在 cookieStr 中。
    var cookies = cookieStr.split("; "); // 将各个 Cookie 分隔开，并存为数组，多个 Cookie 之间用分号加空隔隔开。
    for (var i = 0; i < cookies.length; i++)
    {
        if (cookies[i].left(name.length + 1) == (name + "="))
        {
            value = unescape(cookies[i].right(cookies[i].length - name.length - 1)); // -1 为等号长度
            break;
        }
    }

    return value;
};


ezj.cookie.write = function(name, value) {
///<summary>写入 Cookie。某些浏览器（比如 Chrome）不支持本地文件（file:///）写 Cookie。语法：ezj.cookie.write(name, value[, expires[, path]])</summary>
///<param name="name" type="string">Cookie 名称。</param>
///<param name="value" type="string">Cookie 值。</param>
///<param name="expires" type="integer/string">可选。过期时间值，距离当前时间的秒数。或使用整数带单位（s、n、h、d 分别表示秒、分、时、天）的字符串，比如：180s 表示 180 秒，365d 表示 365 天。若省略，则表示关闭浏览器后 Cookie 即失效。</param>
///<param name="path" type="string">可选。Cookie 路径。</param>
    var expires = Function.overload(2, null);
    var path = Function.overload(3, null);
    
    var cookieStr = name + "=" + escape(value) + ";";
    if (String.is(expires))
    {
        var unit = expires.right(1);
        if (unit == "s")
        {
            expires = parseInt(expires);
        }
        else if (unit == "n")
        {
            expires = parseInt(expires) * 60;
        }
        else if (unit == "h")
        {
            expires = parseInt(expires) * 3600;
        }
        else if (unit == "d")
        {
            expires = parseInt(expires) * 86400; // 3600 * 24
        }
    }
    if (Number.is(expires))
    {
        // 注意 expires = 0，则立即过期，立马再取值也取不到。
        var expireDate = new Date();
        expireDate = expireDate.addSeconds(expires);
        cookieStr += " expires=" + expireDate.toUTCString() + ";";
    }
    if (String.is(path))
    {
        cookieStr += " path=" + path + ";";
    }
    
    document.cookie = cookieStr;
};


//================================================================================


ezj.css = function()
{
///<summary>处理 CSS 的对象。语法：ezj.css</summary>
};


ezj.css.load = function(cssUrls)
{
///<summary>添加 CSS 链接。语法：ezj.css.load(cssUrls[, media])</summary>
///<param name="cssUrls" type="string/array">要添加的 CSS 文件的 URL，多个时使用数组。</param>
///<param name="media" type="string">可选。CSS 文件应用的 media。</param>
    cssUrls = Array.from(cssUrls);
    var media = Function.overload(1, null);
    
    cssUrls.each(function(cssUrl){
        if (String.isNullOrEmpty(media))
        {
            $c("link", { rel : "stylesheet", type : "text/css", href : cssUrl }, document.getElementsByTagName("head").item(0));
        }
        else
        {
            $c("link", { rel : "stylesheet", type : "text/css", href : cssUrl, media : media }, document.getElementsByTagName("head").item(0));
        }
    });
};


ezj.css.addText = function(cssText)
{
///<summary>添加 CSS 文字。语法：ezj.css.addText(cssText)</summary>
///<param name="cssText" type="string">要添加的 CSS 文字，比如：body { font-size: 13px; }。</param>
    try
    {
        // IE
        var style = document.createStyleSheet();
        style.cssText = cssText;
    }
    catch (ex)
    {
        var style = $c("style", {type : "text/css"}, document.getElementsByTagName("head").item(0));
        style.textContent = cssText;
    }
};


//================================================================================


ezj.Editor = function(frame)
{
///<summary>HTML 编辑器的类，包含 HTML 编辑器基本操作。可用以开发 HTML 编辑器，也可应用到现有 HTML 编辑器增强其功能。语法：new ezj.Editor(frame)</summary>
///<param name="frame" type="string/object">HTML 编辑器框（一个 iframe）的查询字符串，或元素对象。</param>
    this.frame = $g(frame);
    this.window = this.frame.contentWindow;
    this.document = this.window.document;
};


ezj.Editor.prototype.ini = function(content)
{
///<summary>初始化 HTML 编辑器。语法：editor1.ini(content)</summary>
///<param name="content" type="string">HTML 编辑器内容。</param>
    this.document.designMode = "on";
    this.document.open();
    this.document.write(content);
    this.document.close();
};


ezj.Editor.prototype.getSelection = function()
{
///<summary>获取 HTML 编辑器选择对象。语法：editor1.getSelection()</summary>
///<returns type="object">HTML 编辑器选择对象。</returns>
    var range = null;
    
    if (this.window.getSelection)
    {
        range = this.window.getSelection().getRangeAt(0);
    }
    else
    {
        range = this.document.selection.createRange();
    }
    
    return range;
};


ezj.Editor.prototype.getSelectionValue = function()
{
///<summary>获取 HTML 编辑器选择的内容。语法：editor1.getSelectionValue()</summary>
///<returns type="string">HTML 编辑器选择的内容。</returns>
    var value = "";
    
    var range = this.getSelection();
    if (range.cloneContents)
    {
        // 问题：刚好选中一个标签中的内容时，无标签，光内容。
        value = getNodesString(range.cloneContents().childNodes);
    }
    else
    {
        value = range.htmlText; // 若取文本：range.text（chrome 为 range.toString()）
    }
    
    function getNodesString(nodes)
    {
        var str = "";
        for (var i = 0; i < nodes.length; i++)
        {
            if (nodes[i].outerHTML != undefined)
            {
                str += nodes[i].outerHTML;
            }
            else if (nodes[i].innerHTML)
            {
                str += nodes[i].innerHTML; // Firefox 中需要
            }
            else
            {
                str += nodes[i].nodeValue;
            }
        }
        return str;
    }
    
    return value;
};


ezj.Editor.prototype.setSelectionValue = function(value)
{
///<summary>设置 HTML 编辑器选择的内容。语法：editor1.setSelectionValue(value)</summary>
///<param name="value" type="string">HTML 编辑器选择的内容。</param>
    this.window.focus(); // 若省略此句，在 IE 中，若只是光标，没有选择内容，pasteHTML 就会出错。
    var range = this.getSelection();
    
    if (range.pasteHTML)
    {
        range.pasteHTML(value);
    }
    else
    {
        range.deleteContents();
        var fragment = range.createContextualFragment(value);
        var lastChild = fragment.lastChild; // 需要赋给一个变量，不能直接在后面使用 fragment.lastChild
        range.insertNode(fragment);
        range.setEndAfter(lastChild); // 设置末尾位置
        range.collapse(false); // 合并范围至末尾
    }
};


//================================================================================


ezj.Event = function()
{
///<summary>处理事件的类。语法：new ezj.Event()</summary>
    this.handlers = []; // 对象数组，handler、handlerKey
};


// 不能使用 prototype 指定 handlers
// 请参见：http://www.cftea.com/c/2009/06/M4NFQTP1UY7A3FCE.asp
// 以下做法是错误的：
// ezj.Event.prototype.handlers = []; // 对象数组，handler、handlerKey


ezj.Event.prototype.addHandler = function(handler)
{
///<summary>添加事件处理程序。语法：event1.addHandler(handler[, handlerKey[, skipWhenExists]])</summary>
///<param name="handler" type="function">要添加的事件处理程序。</param>
///<param name="handlerKey" type="string">可选。本 handler 的唯一标识，可以利用该标识来删除 handler，大小写敏感。null 表示不指定标识。</param>
///<param name="skipWhenExists" type="boolean">可选。相同的 handlerKey 或 handler 存在时，是否跳过添加本 handler。默认值为 false。</param>
    var handlerKey = Function.overload(1, "");
    var skipWhenExists = Function.overload(2, false);
    
    if (skipWhenExists)
    {
        for (var i = 0; i < this.handlers.length; i++)
        {
            if ((this.handlers[i].handlerKey == handlerKey && handlerKey !== null) ||
                this.handlers[i].handler == handler)
            {
                return;
            }
        }
    }
    
    this.handlers.push({ handler : handler, handlerKey : handlerKey });
};


ezj.Event.prototype.removeHandler = function(handlerOrHandlerKey)
{
///<summary>移除事件处理程序。同一个 handler 添加多次，则一次只移除相同 handler 中的一个。后添加的先移除。语法：event1.removeHandler(handlerOrHandlerKey)</summary>
///<param name="handlerOrHandlerKey" type="function/string">要移除的事件处理程序或其标识。</param>
    var i = this.handlers.length - 1;
    
    if (Function.is(handlerOrHandlerKey))
    {
        while (i >= 0)
        {
            if (this.handlers[i].handler == handlerOrHandlerKey)
            {
                this.handlers.splice(i, 1);
                break;
            }
            i--;
        }
    }
    else
    {
        while (i >= 0)
        {
            if (this.handlers[i].handlerKey == handlerOrHandlerKey)
            {
                this.handlers.splice(i, 1);
                break;
            }
            i--;
        }
    }
};


ezj.Event.prototype.clear = function()
{
///<summary>移除所有的事件处理程序。语法：event1.clear()</summary>
    this.handlers.splice(0, this.handlers.length);
};


ezj.Event.prototype.trigger = function(sender, eventArgs)
{
///<summary>触发事件。语法：event1.trigger(sender, eventArgs)</summary>
///<param name="sender" type="any">触发事件的源。</param>
///<param name="eventArgs" type="any">事件参数。</returns>
    for (var i = 0; i < this.handlers.length; i++)
    {
        this.handlers[i].handler(sender, eventArgs);
    }
};


//================================================================================


ezj.event = function()
{
///<summary>处理事件的对象。语法：ezj.event</summary>
};


ezj.event.getEvent = function()
{
///<summary>用在事件处理程序中，获取引发事件的事件对象。语法：ezj.event.getEvent()</summary>
///<returns type="object">引发事件的事件对象。</returns>
    var event = null;
    if (window.event)
    {
        event = window.event;
    }
    else
    {
        var curCaller = arguments.callee.caller;
        while (curCaller.arguments.callee.caller)
        {
            curCaller = curCaller.arguments.callee.caller;
        }
        event = curCaller.arguments[0];
    }
    
    return event;
};


ezj.event.getMouseButton = function()
{
///<summary>获取按下的鼠标按键。不同的浏览器，对不同的鼠标按键值有不值的定义，该方法返回统一的定义值。语法：ezj.event.getMouseButton()</summary>
///<returns type="string/integer">l-按下鼠标左键；r-按下鼠标右键；m-按下鼠标中键（不是所有的浏览器都会触发鼠标中键）；其他值-按下鼠标其他键。</returns>
    var event = ezj.event.getEvent();
    
    if (window.navigator.appName.indexOf("Microsoft") >= 0)
    {
        // IE5.0、IE6.0 的 appName 均为：Microsoft Internet Explorer
        // 腾讯TT、傲游等使用 IE 内核的浏览器，其 appName 也为：Microsoft Internet Explorer
        // 这里不能使用 document.all 来判断，因为 Opera 也具有 document.all 属性，但其鼠标按钮值却和 FireFox 是一类的。
        if (event.button == 1)
        {
            return "l";
        }
        else if (event.button == 2)
        {
            return "r";
        }
        else if (event.button == 4)
        {
            return "m";
        }
    }
    else
    {
        if (event.button == 0)
        {
            return "l";
        }
        else if (event.button == 2)
        {
            return "r";
        }
        else if (event.button == 1)
        {
            return "m";
        }
    }

    return event.button;
};


ezj.event.getKeyCode = function()
{
///<summary>获取键盘按下的键值。语法：ezj.event.getKeyCode()</summary>
///<returns type="integer">键盘按下的键值。</returns>
    return ezj.event.getEvent().keyCode;
};


ezj.event.keyCodes = function()
{
///<summary>键盘按键键值集合。语法：ezj.event.keyCodes</summary>
};


ezj.event.keyCodes.n0 = 48; // 数字 0
ezj.event.keyCodes.n1 = 49;
ezj.event.keyCodes.n2 = 50;
ezj.event.keyCodes.n3 = 51;
ezj.event.keyCodes.n4 = 52;
ezj.event.keyCodes.n5 = 53;
ezj.event.keyCodes.n6 = 54;
ezj.event.keyCodes.n7 = 55;
ezj.event.keyCodes.n8 = 56;
ezj.event.keyCodes.n9 = 57;
ezj.event.keyCodes.nn0 = 96; // 小键盘上的数字 0
ezj.event.keyCodes.nn1 = 97;
ezj.event.keyCodes.nn2 = 98;
ezj.event.keyCodes.nn3 = 99;
ezj.event.keyCodes.nn4 = 100;
ezj.event.keyCodes.nn5 = 101;
ezj.event.keyCodes.nn6 = 102;
ezj.event.keyCodes.nn7 = 103;
ezj.event.keyCodes.nn8 = 104;
ezj.event.keyCodes.nn9 = 105;
ezj.event.keyCodes.a = 65; // 按下大写小写的 a 均是该值
ezj.event.keyCodes.b = 66;
ezj.event.keyCodes.c = 67;
ezj.event.keyCodes.d = 68;
ezj.event.keyCodes.e = 69;
ezj.event.keyCodes.f = 70;
ezj.event.keyCodes.g = 71;
ezj.event.keyCodes.h = 72;
ezj.event.keyCodes.i = 73;
ezj.event.keyCodes.j = 74;
ezj.event.keyCodes.k = 75;
ezj.event.keyCodes.l = 76;
ezj.event.keyCodes.m = 77;
ezj.event.keyCodes.n = 78;
ezj.event.keyCodes.o = 79;
ezj.event.keyCodes.p = 80;
ezj.event.keyCodes.q = 81;
ezj.event.keyCodes.r = 82;
ezj.event.keyCodes.s = 83;
ezj.event.keyCodes.t = 84;
ezj.event.keyCodes.u = 85;
ezj.event.keyCodes.v = 86;
ezj.event.keyCodes.w = 87;
ezj.event.keyCodes.x = 88;
ezj.event.keyCodes.y = 89;
ezj.event.keyCodes.z = 90;
ezj.event.keyCodes.f1 = 112; // 功能键
ezj.event.keyCodes.f2 = 113;
ezj.event.keyCodes.f3 = 114;
ezj.event.keyCodes.f4 = 115;
ezj.event.keyCodes.f5 = 116;
ezj.event.keyCodes.f6 = 117;
ezj.event.keyCodes.f7 = 118;
ezj.event.keyCodes.f8 = 119;
ezj.event.keyCodes.f9 = 120;
ezj.event.keyCodes.f10 = 121;
ezj.event.keyCodes.f11 = 122;
ezj.event.keyCodes.f12 = 123;
ezj.event.keyCodes.tab = 9;
ezj.event.keyCodes.shift = 16;
ezj.event.keyCodes.ctrl = 17;
ezj.event.keyCodes.alt = 18;
ezj.event.keyCodes.enter = 13;
ezj.event.keyCodes.backspace = 8;
ezj.event.keyCodes.up = 38;
ezj.event.keyCodes.down = 40;
ezj.event.keyCodes.left = 37;
ezj.event.keyCodes.right = 39;
ezj.event.keyCodes.insert = 45;
ezj.event.keyCodes.del = 46; // 注意：这里为了避免与关键词 delete 冲突，使用的名称是 del。
ezj.event.keyCodes.home = 36;
ezj.event.keyCodes.end = 35;
ezj.event.keyCodes.pageUp = 33;
ezj.event.keyCodes.pageDown = 34;
ezj.event.keyCodes.add = 107; // 小键盘上的加号
ezj.event.keyCodes.subtract = 109; // 小键盘上的减号
ezj.event.keyCodes.multiply = 106; // 小键盘上的乘号
ezj.event.keyCodes.divide = 111; // 小键盘上的除号


//================================================================================


ezj.ie = (function(){
///<summary>IE 浏览器对象。ezj.ie.is() 判断当前浏览器是否是 IE；ezj.ie.engine 表示 IE 的引擎（非版本），值有：5、7、8。语法：ezj.ie</summary>
    var result = { engine:0 };
    result.is = function(){return false;}; // 把 is 作方法
    
    if (window.navigator.appName == "Microsoft Internet Explorer")
    {
        result.is = function(){return true;};
        result.engine = 5;
        if (document.doctype)
        {
            // IE 8 及以前的浏览器 doctype 总是 null
            // IE 9 处于非 IE 9 模式时，也是 null
            result.engine = 9;
        }
        else if (document.documentMode)
        {
            // document.documentMode 是个数字（无 6）
            // 虽然它的值可能是 5、7、8，但不表示 IE 5、IE 6 中也有这个属性。
            result.engine = document.documentMode;
        }
        else if (document.compatMode && document.compatMode == "CSS1Compat")
        {
            // compatMode 还有一个可能的值是 BackCompat。
            // IE 8 中不建议使用这个属性，而是使用 documentMode。
            // IE 6 中也有这个属性，它的值为 CSS1Compat，所以问题就来了。
            if (window.XMLHttpRequest)
            {
                result.engine = 7;
            }
            else
            {
                result.engine = 5;
            }
        }
    }
    
    return result;
})();


//================================================================================


ezj.javascript = function()
{
///<summary>处理 JavaScript 的对象。语法：ezj.javascript</summary>
};


ezj.javascript.load = function(jsUrls)
{
///<summary>按顺序加载一个或多个 JavaScript 文件。语法：ezj.javascript.load(jsUrls[, onComplete[, charset]])</summary>
///<param name="jsUrls" type="string/array">要加载的一个或多个 JavaScript 文件的 URL，多个时使用数组。</param>
///<param name="onComplete" type="function">可选。所有 js 文件加载完成（不判断成功与否）后要执行的函数。默认值为 null。</param>
///<param name="charset" type="string">可选。脚本的字符集，零长度字符串表示自动。默认值为零长度字符串。</param>
    var urls = String.is(jsUrls) ? [jsUrls] : jsUrls; // 统一为数组
    if (urls.length <= 0)
    {
        return;
    }
    var onComplete = Function.overload(1, null);
    var charset = Function.overload(2, "");

    // 由于多个 JavaScript 文件之间可能存在引用，为了保障引用的可用性，采用依次加载的方式。
    // 每轮只加载一个 JavaScript 文件，当该文件加载完成后，利用类似递归的方式继续加载下一个文件。

    var script = $c("script", { type : "text/javascript" });
    
    if (typeof script.onreadystatechange == "undefined")
    {
        // Firefox 一类响应 onload，无 readyState。
        script.onload = function()
        {
            if (urls.length == 1)
            {
                // 无可继续加载的文件
                Function.run(onComplete);
            }
            else
            {
                ezj.javascript.load(urls.slice(1), onComplete, charset);
            }
        }
    }
    else
    {
        //IE 一类浏览器响应 onreadystatechange。
        script.onreadystatechange = function()
        {
            if (script.readyState == 4 || script.readyState == "loaded" || script.readyState == "complete")
            {
                if (urls.length == 1)
                {
                    // 无可继续加载的文件
                    Function.run(onComplete);
                }
                else
                {
                    ezj.javascript.load(urls.slice(1), onComplete, charset);
                }
            }
        }
    }
    
    
    if (!String.isNullOrEmpty(charset))
    {
        script.setAttribute("charset", charset);
    }
    script.setAttribute("src", urls[0]); // 一轮只加载一个
    $c(script, null, document.body); // 将 script 加到 document.body
};


ezj.javascript.path = function(jsFile)
{
///<summary>获取 js 文件的引用目录。语法：ezj.javascript.path(jsFile)</summary>
///<param name="jsFile" type="string">要获取引用目录的 js 文件名。忽略大小写。</param>
///<returns type="string">除开 jsFile 后的 js 文件的引用目录。</returns>
    var result = "";
    
    var es = document.getElementsByTagName("script");
    for (var i = 0; i < es.length; i++)
    {
        if (String.is(es[i].src) && es[i].src.endsWith(jsFile, true))
        {
            result = es[i].src.left(es[i].src.length - jsFile.length);
            break;
        }
    }
    
    return result;
};


//================================================================================


ezj.qs = function()
{
///<summary>处理 QueryString 的对象。语法：ezj.qs</summary>
};


ezj.qs.get = function(paramName)
{
///<summary>获取 URL 查询字符串中参数值，只适用于 UTF-8 编码的 URL。语法：ezj.qs.get(paramName)</summary>
///<param name="paramName" type="string">参数名称。</param>
///<returns type="string">参数值。若找不到对应的参数名称，则为 null。</returns>
    var result = null;
    
    var search = location.search;
    if (search.left(1) == "?")
    {
        search = search.substr(1);
    }
    var params = search.split("&");
    for (var i = 0; i < params.length; i++)
    {
        var pos = params[i].indexOf("=");
        if (pos > 0 && params[i].left(pos).equalsIgnoreCase(paramName))
        {
            // 自动解码
            // Firefox 是按 GBK 来编码，所以解不出来
            // 故 Firefox 应用此方法时无法对付中文
            try
            {
                result = decodeURIComponent(params[i].substr(pos + 1));
            }
            catch (ex)
            {
                result = params[i].substr(pos + 1);
            }
            break;
        }
    }
    
    return result;
};


//================================================================================


ezj.Xml = function(xml)
{
///<summary>处理 XML 的类。语法：new ezj.Xml(xml)</summary>
    this.xmlDom = create(xml);
    
    function create(xml)
    {
    // 获取 XML Dom 对象。
    // xml string/objectXML。字符串，或 XML URL 地址，或 XML DOM 对象。
    // 返回 XML DOM 对象
        if (!String.is(xml))
        {
            return xml;
        }
        
        var xmlDom;
        try
        {
            xmlDom = new ActiveXObject("Microsoft.XMLDOM"); // IE
        }
        catch (ex)
        {
            xmlDom = document.implementation.createDocument("", "", null); // Firefox、Chrome
        }
        
        if (xml.substr(0, 1) == "<")
        {
            // XML 字符串
            // 支持带 <?xml 的，也支持不带 <?xml 的，但都只能只有一个根节点。
            // 出现多个根节点时 IE、Firefox 不认可；Chrome 认可第一个根节点，抛弃后面的。
            try
            {
                xmlDom.loadXML(xml); // IE
            }
            catch (ex)
            {
                xmlDom = (new DOMParser()).parseFromString(xml,"text/xml"); // Firefox、Chrome
            }
        }
        else
        {
            // XML URL
            // URL 可以是绝对的，也可以是相对的。
            // 同 XML 字符串：URL 的 XML 文件，可有 <?xml，也可无 <?xml
            // xmlDom.load 很特殊，在 IE 中 if (xmlDom.load) 会出错，但 if (typeof xmlDom.load != "undefined") 是正确的
            try
            {
                // IE、Firefox
                xmlDom.async = false; // 注意不能省略此句。
                xmlDom.load(xml);
            }
            catch (ex)
            {
                // Chrome
                // 测试时请注意：在本地电脑中双击 HTML 文件预览时，Chrome 在这里是会出错的。
                var request = new XMLHttpRequest();
                request.open("GET", xml, false); // 不使用异步
                request.send(null);
                xmlDom = request.responseXML;
            }
        }
        
        return xmlDom;
    }
};


ezj.Xml.prototype.val = function(path)
{
///<summary>获取 xml 对应节点或属性的值。语法：$x1.val(path)</summary>
///<param name="path" type="string">查询节点或属性的 XPath 字符串。</param>
///<returns type="string/array">若找不到，则为 null；若只有一个值，则为字符串；若有多个值，则为数组。</returns>
    var result = [];
    
    if (typeof this.xmlDom.selectNodes != "undefined")
    {
        var nodes = this.xmlDom.selectNodes(path); // IE
        for (var i = 0; i < nodes.length; i++)
        {
            result.push((nodes[i].firstChild) ? nodes[i].firstChild.nodeValue : ""); // 属性也是这样
        }
    }
    else
    {
        // evaluate(xpathText, contextNode, namespaceURLMapper, resultType, result)
        // 结果 XPathResult 对象
        var nodes = this.xmlDom.evaluate(path, this.xmlDom, null, XPathResult.ANY_TYPE, null); // Firefox、IE
        var node = nodes.iterateNext();
        while (node)
        {
            result.push((node.firstChild) ? node.firstChild.nodeValue : ""); // 属性也是这样
            node = nodes.iterateNext();
        }
    }
    
    if (result.length <= 0)
    {
        return null;
    }
    else if (result.length == 1)
    {
        return result[0];
    }
    return result;
};


ezj.Xml.prototype.nodes = function(path)
{
///<summary>获取 xml 对应节点或属性并以数组的形式返回。语法：$x1.nodes(path)</summary>
///<param name="path" type="string">查询节点或属性的 XPath 字符串。</param>
///<returns type="array">节点或属性的数组。</returns>
    var result = [];
    
    if (typeof this.xmlDom.selectNodes != "undefined")
    {
        result = Array.from(this.xmlDom.selectNodes(path)); // IE
    }
    else
    {
        // evaluate(xpathText, contextNode, namespaceURLMapper, resultType, result)
        // 结果 XPathResult 对象
        var nodes = this.xmlDom.evaluate(path, this.xmlDom, null, XPathResult.ANY_TYPE, null); // Firefox、IE
        var node = nodes.iterateNext();
        while (node)
        {
            result.push(node); // 属性也是这样
            node = nodes.iterateNext();
        }
    }
    
    return result;
};


ezj.Xml.prototype.pathNodes = function(pathOrNode)
{
///<summary>获取根节点（不含 Document 节点）到 pathOrNode 对应节点的节点数组。语法：$x1.path(pathOrNode)</summary>
///<param name="pathOrNode" type="string/object">查询节点的 XPath 字符串，或目标节点。若为 XPath 字符串，则 XPath 查出的第一个节点作为目标节点。</param>
///<returns type="array">节点数组。</returns>
    var result = [];
    
    var node = pathOrNode;
    if (String.is(pathOrNode))
    {
        node = (this.nodes(pathOrNode))[0];
    }
    
    while (node)
    {
        if (node.nodeType == 1)
        {
            // 只选择 Element，避免将 Document（值 9）也返回。
            result.push(node.cloneNode(false)); // 是否克隆子节点下的子树
        }
        node = node.parentNode;
    }
    
    result.reverse();
    return result;
};


var $x = function(xml)
{
///<summary>等效于 new ezj.Xml(xml)。语法：$x(xml)</summary>
    return new ezj.Xml(xml);
};


//================================================================================
//================================================================================
//================================================================================


ezj.ext = function()
{
///<summary>ezj 扩展内容的命名空间。语法：ezj.ext</summary>
};


//================================================================================


ezj.ext.resources = [];
ezj.ext.load = function(name, jsArr)
{
///<summary>自动读取 ezj.ext 所需资源，读取成功后自动执行调用的语句。语法：ezj.ext.load(name, jsArr[, cssArr])</summary>
///<param name="name" type="string">ezj.ext 资源的唯一名称。</param>
///<param name="jsArr" type="array">ezj.ext 所需的 js 文件。</param>
///<param name="cssArr" type="array">可选。ezj.ext 所需的 css 文件。</param>
    var caller = arguments.callee.caller;
    var callerArguments = arguments.callee.caller.arguments;
    var cssArr = Function.overload(2, []);
    
    if (ezj.ext.resources[name])
    {
        if (ezj.ext.resources[name].state == "complete")
        {
            return true; // 资源已经准备好了，直接以 true 退出去执行就是了。
        }
        else if (ezj.ext.resources[name].state == "loading")
        {
            ezj.ext.resources[name].onload.addHandler(function(){
                caller.apply(window, callerArguments);
            });
            return false; // 前面有“人”在读取了，这里加 handler，跳出去等待就是了。
        }
        else
        {
            return false;
        }
    }
    else
    {
        // 第一次读。
        ezj.ext.resources[name] = { state : "loading", onload : new ezj.Event() };
        ezj.ext.resources[name].onload.addHandler(function(){
            caller.apply(window, callerArguments);
        });
    }
    
    var jsArrWithPath = jsArr.each(function(value, index){
        return ezj.about.path + value;
    });
    ezj.javascript.load(jsArrWithPath, function(){
        cssArr.each(function(value, index){
            ezj.css.load(ezj.about.path + value);
        });
        ezj.ext.resources[name].state = "complete";
        ezj.ext.resources[name].onload.trigger();
    });
    
    return false;
};


//================================================================================


ezj.ext.calendar = function(inputField)
{
///<summary>创建基于 The DHTML Calendar 的日历控件。语法：ezj.ext.calendar(inputField[, button[, showsTime[, format]]]) 或 ezj.ext.calendar(inputField[, config])</summary>
///<param name="inputField" type="string/object/array">存储、显示日历值的元素查询字符串，或元素对象，或元素对象数组。inputField 需是文本框。</param>
///<param name="button" type="string/object/array">可选。触发日历控件显示的元素查询字符串，或元素对象，或元素对象数组。默认值为 inputField 对应的元素。</param>
///<param name="showsTime" type="boolean">可选。是否在日历控件中显示时间部分。默认值为 false。</param>
///<param name="format" type="string">可选。日期时间格式。默认值为 %Y-%m-%d（不显示时间部分时）和 %Y-%m-%d %H:%M:%S（显示时间部分时）。</param>
    if (!ezj.ext.load("calendar", ["ext/calendar/calendar_stripped.js", "ext/calendar/lang/calendar-zh-utf8.js", "ext/calendar/calendar-setup_stripped.js"], ["ext/calendar/calendar-" + ezj.ext.calendar.skin + ".css"]))
    {
        return;
    }
    
    inputField = $g(inputField);
    var button = inputField; 
    var showsTime = false;
    var format = null;
    if (Function.like("any", "object") && !String.is(arguments[1].tagName))
    {
        // 在 IE 中，HTML 元素也是 object 类型，这里用 tagName 排除之。
        var config = arguments[1];
        button = $g(Object.prop(config, "button", button));
        showsTime = Object.prop(config, "showsTime", showsTime);
        format = Object.prop(config, "format", format);
    }
    else
    {
        button = $g(Function.overload(1, button));
        showsTime = Function.overload(2, showsTime);
        format = Function.overload(3, format);
    }
    
    var inputFields = Array.from(inputField);
    var buttons = Array.from(button);
    inputFields.each(function(e, index){
        if (format == null)
        {
            Calendar.setup({
                inputField : inputFields[index],
                button : buttons[index],
                showsTime : showsTime,
                ifFormat : (!showsTime ? "%Y-%m-%d" : "%Y-%m-%d %H:%M:%S")
            });
        }
        else
        {
            Calendar.setup({
                inputField : inputFields[index],
                button : buttons[index],
                showsTime : showsTime,
                ifFormat : format
            });
        }
    });
};


///<summary>日历皮肤配置，该配置影响当前页面的所有日历控件。语法：ezj.ext.calendar.skin</summary>
ezj.ext.calendar.skin = "blue2";


ezj.ext.calendar.skins = function()
{
///<summary>日历控件的皮肤集。语法：ezj.ext.calendar.skins</summary>
};
ezj.ext.calendar.skins.blue = "blue";
ezj.ext.calendar.skins.blue2 = "blue2";
ezj.ext.calendar.skins.brown = "brown";
ezj.ext.calendar.skins.green = "green";
ezj.ext.calendar.skins.system = "system";
ezj.ext.calendar.skins.tas = "tas";
ezj.ext.calendar.skins.win2k = "win2k-1";
ezj.ext.calendar.skins.win2k2 = "win2k-2";
ezj.ext.calendar.skins.win2k3 = "win2k-cold-1";
ezj.ext.calendar.skins.win2k4 = "win2k-cold-2";


//================================================================================


ezj.ext.cancelable = function(element)
{
///<summary>创建基于 ezj.ext.Cancelable 的可取消单选框或列表框。语法：ezj.ext.cancelable(element)</summary>
///<param name="element" type="string/object/array">要执行可取消的元素查询字符串，或元素对象，或元素对象数组。</param>
    if (!ezj.ext.load("cancelable", ["ext/cancelable/Cancelable.js"]))
    {
        return;
    }
    var es = Array.from($g(element));
    es.each(function(e, index){
        var cancelable = new ezj.ext.Cancelable(e);
    });
};


//================================================================================


ezj.ext.cascade = function(valueElement, xml)
{
///<summary>创建基于 ezj.ext.Cascade 的级联控件。语法：ezj.ext.cascade(valueElement, xml)</summary>
///<param name="valueElement" type="string/object/array">要关联为级联控件的元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="xml" type="string">控件 XML 数据结构。</param>
    if (!ezj.ext.load("cascade", ["ext/cascade/Cascade.js"]))
    {
        return;
    }
    
    if (Function.like("any", "object"))
    {
        var config = Function.overload(1);
        xml = Object.prop(config, "xml", "");
    }
    
    var es = Array.from($g(valueElement));
    es.each(function(e, index){
        var cascade = new ezj.ext.Cascade(e, xml);
        cascade.createChildren(-1); // 创建第一项
        
        // 选择初始
        var selectedValue = $v(e);
        if (!String.isNullOrEmpty(selectedValue))
        {
            var offset = 0;
            (($x(xml)).pathNodes("//item[@value='" + selectedValue + "']")).each(function(node, index){
                var value = node.getAttribute("value");
                if (value == "0")
                {
                    offset = 1;
                }
                cascade.select(index - offset, value); // 对节点是否从 0 开始，有个兼容性，即不管是不是从 0 开始，结果都一样。
            });
        }
    });
};


//================================================================================


ezj.ext.chart = function(element, dataUrl, width, height)
{
///<summary>创建基于 Open Flash Chart 的图表。语法：ezj.ext.chart(element, dataUrl, width, height) 或 ezj.ext.chart(element, config)</summary>
///<param name="element" type="string/object/array">创建图表的元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="dataUrl" type="string">图表数据地址。其 QueryString 需要进行 URLEncode 编码。</param>
///<param name="width" type="integer">图表宽度。</param>
///<param name="height" type="integer">图表高度。</param>
    if (Function.like("any", "object"))
    {
        var config = Function.overload(1);
        dataUrl = Object.prop(config, "dataUrl", "");
        width = Object.prop(config, "width", "");
        height = Object.prop(config, "height", "");
    }
    var es = Array.from(element);
    es.each(function(e){
        ezj.ext.swf(e, ezj.about.path + "ext/chart/open-flash-chart.swf", width, height, "9", { "data-file" : dataUrl });
    });
};


//================================================================================


ezj.ext.codelighter = function(element)
{
///<summary>创建基于 SyCODE Syntax Highlighter 的语法高亮控件，元素的最后一个 class 表示高亮的语言，如果是 HTML，会自动识别其中的 CSS 和 JavaScript。语法：ezj.ext.codelighter(element)</summary>
///<param name="element" type="string/object/array">要执行语法高亮的元素查询字符串，或元素对象，或元素对象数组。</param>
    if (!ezj.ext.load("codelighter", ["ext/codelighter/highlighter.js"]))
    {
        return;
    }
    
    var es = $g(element);
    if (Array.is(es))
    {
        for (var i = 0; i < es.length; i++)
        {
            ezj.ext.codelighter(es[i]);
        }
        return;
    }
    else if (!es)
    {
        return;
    }
    
    // codeContainer 是装代码的元素对象。
    var container = es;
    var containerClassName = container.className;
    var code = $g(container).val(); // 代码
    var language = (function() {
        var classes = containerClassName.splitEx(" ", true, true);
        return classes[classes.length - 1];
    })(); // 代码的语言（利用最后一个 class 标识）
    if (String.isNullOrEmpty(language))
    {
        return; // 没有指明语言，退出。
    }
    
    // 处理值
    /* Highlighter.Execute 测试
     * 只是测试 Highlighter.Execute 这个方法，并不涉及 DOM，实际中因浏览器差异，涉及到 DOM 时可能要考虑兼容性。
     * 通过这个测试可以理解后面的替换问题。
     *
     * Highlighter.Execute("<b>", "xhtml") 的结果：
     * <span style="color:blue">&lt;b</span><span style="color:blue">&gt;</span>
     *
     * Highlighter.Execute("&lt;&nbsp;&gt;&quot;&amp;", "xhtml") 的结果：
     * <span style="color:blue">&amp;lt;</span>&amp;nbsp;<span style="color:blue">&amp;gt;</span>&amp;quot;&amp;amp;
     *
     * Highlighter.Execute("< >&\"", "xhtml") 的结果：
     * <span style="color:blue">&lt;</span> <span style="color:blue">&gt;</span>&amp;"
     *
     * 测试结果：似乎进行了 HTMLEncode 转换。
     * 进一步查看 Highlighter 代码，原来作了如下替换：
       str = str.replace(/&/g, '&amp;');
       str = str.replace(/</g, '&lt;');
       str = str.replace(/>/g, '&gt;');
       str = str.replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
       str = str.replace(/[ ]{2}/g, '&nbsp;&nbsp;');
       return str.replace(/\n/g, '<br/>');
     */
     
     /* 根据上面的测试及 Highlighter 代码分析，在取值后，根据不同的情况需要先作替换再进入 Highlighter
      * 原则：保持高亮之前的代码显示的布局（除非本来显示就不兼容），只是改变颜色。
      * 若原代码存在 textarea、pre 中，则都替换为 div，因为：textarea 无法高亮，pre “1<br>2”这种代码在 IE 中复制到文本是一行“12”。
      *
      * &、<、> 中以 & 为例
      * 如果本来是 &，显示为 &；进入 Highlighter 后变成 &amp; 出来后仍然是 &amp;，显示为 &。
      * 如果本来是 &amp;，显示为 &；进入 Highlighter 后变成 &amp;amp;；出来后仍然是 &amp;amp;，显示为 &amp;。
      * 所以为了统一，在进入 Highlighter 之前要统一为 &。
      *
      * \t 如何处理得视标签了：
      * \t 在 textarea、pre 中表现为四个空格长度，所以保留 \n 进入 Highlighter，由 Highlighter 替换
      * \t 在 div 中表现为一个空格长度，所以替换为一个空格
      *
      * [ ]{2} 如何处理得视标签了：
      * [ ]{2} 在 textarea、pre 中表现为两个空格长度，所以保留 [ ]{2} 进入 Highlighter，由 Highlighter 替换
      * [ ]{2} 在 div 中表现为一个空格长度，所以替换为一个空格
      *
      * \n 如何处理得视标签了：
      * \n 在 textarea、pre 中表现为换行，所以保留 \n 进入 Highlighter，由 Highlighter 替换
      * \n 在 div 中无表现，所以得去除 \n，以免 Highlighter 将其替换成 <br/>
      */
    if (container.tagName.equalsIgnoreCase("textarea"))
    {
        // textarea 源代码中的 &lt;、&gt;、&nbsp;、&amp;、&quot; 提取值时被 HTMLDecode，各浏览器中均是如此。
        // 所以不需要转换什么。
        // code = code.replaceAll("&lt;", "<");
        // code = code.replaceAll("&gt;", ">");
        // code = code.replaceAll("&amp;", "&");
        
        // code = code.trim(true);
    }
    else if (container.tagName.equalsIgnoreCase("div"))
    {
        // 关于 \n、\t、[ ]{2} 这些虽然表现上相同，但各浏览器在提取值时还是不兼容的。
        code = code.replace(/\t/g, ' ');
        code = code.replace(/[ ]{2}/g, ' ');
        code = code.replace(/\n/gi, ""); // 换行符不能是 \r\n
        
        // 取 div 的值，与源代码中的内容（这里的内容均指非标签，比如指单独的 <，不是指 <b>）中有差别的：
        //
        // 情况一
        // 源代码：&lt;、&gt;、&nbsp;、&amp;、&quot;
        // 取的值：&lt;、&gt;、&nbsp;、&amp;、"
        // 可以看到 &quot; 变成了 "
        //
        // 情况二
        // 源代码：<、>、 、&、"
        // 取的值：&lt;、&gt;、 、&amp;、"
        // 可以看到 <、>、& 都变了
        //
        // 也就是说取的值必定存在：&lt;、&gt;、&amp;、"
        //             必定不存在：<、>、&、&quot;
        //               可能存在：&nbsp;、(空格)
        // 所以取的值可能是：&lt;、&gt;、&nbsp;、 、&amp;、"
        // 带 & 的都需处理，因为进入 Highlighter 会将其替换为 &amp;
        // 引号进入不会出什么乱子；关于空格，如果是一个就不会出什么乱子，如果是多个，就要合并成多个了，
        // 但要注意替换 &nbsp; 要在替换 [ ]{2} 之后，否则一个空格加一个 &nbsp; 会被替换成两个空格，再被替换成一个空格
        code = code.replaceAll("&lt;", "<");
        code = code.replaceAll("&gt;", ">");
        code = code.replaceAll("&nbsp;", " ");
        code = code.replaceAll("&amp;", "&");
        
        // 如果代码中本身就有 HTML 标签，比如 <b> 怎么办呢？
        // 希望高亮后，他仍然是粗体，就不考虑它了，因为这个程序还做不到，并且这不太合乎现实，你见过代码区域中出现一个按钮吗？
        // 但有一个是必须考虑的，就是 <br>。
        code = code.replace(/<br[ ]?[/]?>/gi, "\n");
    }
    else if (container.tagName.equalsIgnoreCase("pre"))
    {
        // pre 的取值情况与 div 相同
        code = code.replaceAll("&lt;", "<");
        code = code.replaceAll("&gt;", ">");
        code = code.replaceAll("&nbsp;", " ");
        code = code.replaceAll("&amp;", "&");
        
        // 如果代码中本身就有 HTML 标签，比如 <b> 怎么办呢？
        // 希望高亮后，他仍然是粗体，就不考虑它了，因为这个程序还做不到，并且这不太合乎现实，你见过代码区域中出现一个按钮吗？
        // 但有一个是必须考虑的，就是 <br>。
        code = code.replace(/<br[ ]?[/]?>/gi, "\n");
    }
    
    // 高亮
    language = language.toLowerCase();
    if (language == "html")
    {
        language = "xhtml";
    }
    // 如果语言是 XHTML，还将其拆开，高亮其中的 CSS、JavaScript
    if (language == "xhtml")
    {
        var loweredCode = code.toLowerCase();
        var codes = [];
        var pin = 0;
        var scriptStartTagStartPos = loweredCode.indexOf("<script", pin); // 认小写
        var styleStartTagStartPos = loweredCode.indexOf("<style", pin);
        var startTagStartPos = -1;
        var tagName = "";
        var segLanguage = "";
        if (scriptStartTagStartPos >= 0 && styleStartTagStartPos >= 0)
        {
            if (scriptStartTagStartPos > styleStartTagStartPos)
            {
                startTagStartPos = styleStartTagStartPos;
                tagName = "style";
                segLanguage = "css";
            }
            else
            {
                startTagStartPos = scriptStartTagStartPos;
                tagName = "script";
                segLanguage = "jscript";
            }
        }
        else if (scriptStartTagStartPos >= 0)
        {
            startTagStartPos = scriptStartTagStartPos;
            tagName = "script";
            segLanguage = "jscript";
        }
        else if (styleStartTagStartPos >= 0)
        {
            startTagStartPos = styleStartTagStartPos;
            tagName = "style";
            segLanguage = "css";
        }
        while (startTagStartPos >= 0)
        {
            var startTagEndPos = loweredCode.indexOf(">", startTagStartPos);
            var endTagStartPos = loweredCode.indexOf("</" + tagName + ">", startTagEndPos);
            codes.push({ language : "xhtml", code : code.substring(pin, startTagEndPos + 1) });
            codes.push({ language : segLanguage, code : code.substring(startTagEndPos + 1, endTagStartPos) });
            pin = endTagStartPos;
            scriptStartTagStartPos = loweredCode.indexOf("<script", pin);
            styleStartTagStartPos = loweredCode.indexOf("<style", pin);
            if (scriptStartTagStartPos >= 0 && styleStartTagStartPos >= 0)
            {
                if (scriptStartTagStartPos > styleStartTagStartPos)
                {
                    startTagStartPos = styleStartTagStartPos;
                    tagName = "style";
                    segLanguage = "css";
                }
                else
                {
                    startTagStartPos = scriptStartTagStartPos;
                    tagName = "script";
                    segLanguage = "jscript";
                }
            }
            else if (scriptStartTagStartPos >= 0)
            {
                startTagStartPos = scriptStartTagStartPos;
                tagName = "script";
                segLanguage = "jscript";
            }
            else if (styleStartTagStartPos >= 0)
            {
                startTagStartPos = styleStartTagStartPos;
                tagName = "style";
                segLanguage = "css";
            }
            else
            {
                break;
            }
        }
        if (pin >= 0)
        {
            codes.push({ language : "xhtml", code : code.substring(pin)});
        }
        else
        {
            codes.push({ language : "xhtml", code : code}); // 没有任何 CSS、JavaScript 内容
        }
        
        code = "";
        for (var i = 0; i < codes.length; i++)
        {
            code += Highlighter.Execute(codes[i].code, codes[i].language);
        }
    }
    else
    {
        code = Highlighter.Execute(code, language);
    }
    
    // 替换代码
    if (container.tagName.equalsIgnoreCase("textarea") ||
        container.tagName.equalsIgnoreCase("pre"))
    {
        // 也要替换容器
        var codeDiv = $c("div");
        codeDiv.className = containerClassName;
        codeDiv.val(code);
        container.before(codeDiv);
        container.detach();
    }
    else
    {
        container.val(code);
    }
};


//================================================================================


ezj.ext.combobox = function(selectElement)
{
///<summary>创建基于 ezj.ext.Combobox 的列表框。语法：ezj.ext.combobox(selectElement[, mode]) 或 ezj.ext.combobox(selectElement[, config])</summary>
///<param name="selectElement" type="string/object/array">要替换的 select 元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="mode" type="string">可选。模式。可选值为：dropdown、dropdownlist，默认值为 dropdownlist。</param>
    if (!ezj.ext.load("combobox", ["ext/combobox/Combobox.js"], ["ext/combobox/combobox.css"]))
    {
        return;
    }
    
    var mode = "dropdownlist";
    if (Function.like("any", "object"))
    {
        var config = arguments[1];
        mode = Object.prop(config, "mode", mode);
    }
    else
    {
        mode = Function.overload(1, mode);
    }
    
    Array.from($g(selectElement)).each(function(e){
        var combobox = new ezj.ext.Combobox(e, mode);
    });
};


//================================================================================


ezj.ext.drag = function(element)
{
///<summary>创建基于 DragBinder 的拖拽。语法：ezj.ext.drag(element[, controller[, scopeElement[, scopeRestrict[, onOk]]]]) 或 ezj.ext.drag(element[, config[, onOk]])</summary>
///<param name="element" type="string/object">要执行拖拽的元素查询字符串，或元素对象。</param>
///<param name="controller" type="string/object">可选。鼠标可在此拖拽的区域，为元素查询字符串，或元素对象。</param>
///<param name="scopeElement" type="string/object">可选。限制拖拽的区域，为元素查询字符串，或元素对象。</param>
///<param name="scopeRestrict" type="string">可选。拖拽限制的方式，可选值为：position、body，默认值为：body。</param>
///<param name="onOk" type="function">可选。创建完成后要执行的函数。</param>
    if (!ezj.ext.load("drag", ["ext/drag/DragBinder.js"]))
    {
        return;
    }
    
    element = $g(element);
    var controller = element;
    var scopeElement = null;
    var scopeRestrict = "body";
    var onOk = null;
    if (Function.like("any", "object") && !String.is(arguments[1].tagName))
    {
        // 在 IE 中，HTML 元素也是 object 类型，这里用 tagName 排除之。
        var config = arguments[1];
        controller = $g(Object.prop(config, "controller", controller));
        scopeElement = Object.prop(config, "scopeElement", scopeElement);
        if (scopeElement !== null)
        {
            scopeElement = $g(scopeElement);
        }
        scopeRestrict = Object.prop(config, "scopeRestrict", scopeRestrict);
        onOk = Function.overload(2, onOk);
    }
    else
    {
        controller = $g(Function.overload(1, controller));
        scopeElement = Function.overload(2, scopeElement);
        if (scopeElement !== null)
        {
            scopeElement = $g(scopeElement);
        }
        scopeRestrict = Function.overload(3, scopeRestrict);
        onOk = Function.overload(4, onOk);
    }
    
    var drag = new DragBinder(element, controller);
    if (scopeElement)
    {
        if (!scopeElement.style.position)
        {
            scopeElement.style.position = "relative";
        }
        drag.setScope(scopeElement, scopeRestrict);
    }
    
    Function.run(onOk, drag);
};


//================================================================================


ezj.ext.drop = function(button, box)
{
///<summary>创建联动效果的菜单效果。语法：ezj.ext.drop(button, box[, base]) 或 ezj.ext.drop(button, box[, config])</summary>
///<param name="button" type="string/object/array">鼠标移上去激发菜单显示的元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="box" type="string/object/array">菜单元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="base" type="string">可选。位置配置字符串，l、r、t、b、c、m、i、o 分别表示：左、右、上、下、中、中、内、外，可组合使用。默认值为 libo。</param>
    var buttons = Array.from($g(button));
    var boxs = Array.from($g(box));
    var base = "libo";
    
    if (Function.like("any", "any", "object"))
    {
        var config = arguments[2];
        base = Object.prop(config, "base", base);
    }
    else
    {
        base = Function.overload(2, base);
    }

    buttons.each(function (e, index) {
        var button = buttons[index];
        var box = boxs[index];

        button.addListener("mouseover", function () {
            box.pos(button, base);
            box.display(true);
        });

        var canHidden = true;
        button.addListener("mousemove", function () { canHidden = false; });
        box.addListener("mousemove", function () { canHidden = false; });
        $g(document.body).addListener("mousemove", function () {
            if (canHidden) {
                box.display(false);
            }
            canHidden = true;
        });
    });
};


//================================================================================


ezj.ext.editor = function(element)
{
///<summary>创建基于 Kind Editor 的带语法高亮的 HTML 编辑器控件。语法：ezj.ext.editor(element[, config][, onOk])</summary>
///<param name="element" type="string/object">文本区域元素查询字符串，或元素对象。</param>
///<param name="onOk" type="function">可选。创建完成后要执行的函数。</param>
    if (!ezj.ext.load("editor", ["ext/editor/kindeditor-min.js"]))
    {
        return;
    }
    
    if (!ezj.ext.load("codelighter", ["ext/codelighter/highlighter.js"]))
    {
        return;
    }
    
    element = $g(element);
    var textareaId = element.id;
    if (textareaId == "")
    {
        textareaId = "ezj_ext_editor_" + Math.nextRandom(0, 999999999);
        element.id = textareaId;
    }
    
    var setupType = "create"; // create、show
    var onOk = null;
    if (Function.like("any", "object"))
    {
        var config = Function.overload(1, null);
        setupType = Object.prop(config, "setupType", setupType);
        onOk = Function.overload(2, null);
    }
    else
    {
        onOk = Function.overload(1, null);
    }
    
    // 按钮
    var items = ["source", "fullscreen", "undo", "redo", "print", "cut", "copy", "paste",
                 "plainpaste", "wordpaste", "justifyleft", "justifycenter", "justifyright",
                 "justifyfull", "insertorderedlist", "insertunorderedlist", "indent", "outdent", "subscript",
                 "superscript", "selectall", "-",
                 "title", "fontname", "fontsize", "textcolor", "bgcolor", "bold",
                 "italic", "underline", "strikethrough", "removeformat", "image",
                 "flash", "media", "advtable", "hr", "emoticons", "link", "unlink", "|"];

    // 插入设置热词按钮
    ezj.css.addText(".ke-icon-hotkey {background-image:url(" + KE.scriptPath + "skins/default/default.gif);background-position:0px -960px;width:16px;height:16px;}");
    KE.lang["hotkey"] = "设置热词";
    KE.plugin["hotkey"] = {
        click: function (id) {
            KE.util.selection(id);
            var key = KE.selectedHtml(id);
            if (key == "") {
                return;
            }
            KE.util.insertHtml(id, "<a href=\"#\">" + key + "</a>");
            KE.util.focus(id);
        }
    };
    items.push("hotkey"); // 增加按钮

    // 插入分页符按钮
    ezj.css.addText(".ke-icon-pagebreak {background-image:url(" + KE.scriptPath + "skins/default/default.gif);background-position:0px -976px;width:16px;height:16px;}");
    KE.lang["pagebreak"] = "插入分页符";
    KE.plugin["pagebreak"] = {
        click: function (id) {
            KE.util.selection(id);
            KE.util.insertHtml(id, "<hr class=\"pagebreak\" />");
            KE.util.focus(id);
        }
    };
    items.push("pagebreak"); // 增加按钮
    
    // 是否增加高亮代码按钮
    if (typeof Highlighter != "undefined")
    {
        // 高亮代码（SyCODE Syntax Highlighter）有效
        ezj.css.addText(".ke-icon-code {background-image:url(" + KE.scriptPath + "skins/default/default.gif);background-position:0px -992px;width:16px;height:16px;}");
        KE.lang["code"] = "插入代码";
        KE.plugin["code"] = {
            click : function(id)
            {
                KE.util.selection(id);
                // 同时将上次代码语言通过 URL 传到对话框中。
                var dialog = new KE.dialog({
                    id : id,
                    cmd : "code",
                    file : "code.html?id=" + id + "&ver=" + KE.version +
                           "&language=" + ezj.cookie.read("ezj_ext_editor_language") +
                           "&highlightDirectly=" + ezj.cookie.read("ezj_ext_editor_highlightDirectly"),
                    width : 600,
                    height : 400,
                    loadingMode : true,
                    title : KE.lang["code"],
                    yesButton : KE.lang["yes"],
                    noButton : KE.lang["no"]
                });
                dialog.show();
           },
            exec : function(id)
            {
                KE.util.select(id);
                var iframeDoc = KE.g[id].iframeDoc;
                var dialogDoc = KE.util.getIframeDoc(KE.g[id].dialog);
                var language = KE.$g("language", dialogDoc).value;
                var highlightDirectly = KE.$g("highlightDirectly", dialogDoc).checked;
                var content = KE.$g("content", dialogDoc).value;
                if (highlightDirectly)
                {
                    content = Highlighter.Execute(content, language);
                    content = "<div class=\"" + ezj.ext.editor.codeClassName + "\">" + content + "</div>";
                }
                else
                {
                    content = "<textarea class=\"" + ezj.ext.editor.codeClassName + " " + language + "\">" + content.replaceAll("</textarea>", "&lt;/textarea&gt;") + "</textarea>";
                }
                KE.util.insertHtml(id, content);
                KE.layout.hide(id);
                KE.util.focus(id);
                
                ezj.cookie.write("ezj_ext_editor_language", language, 2592000); // 记录本次代码语言，有效期 30 天
                ezj.cookie.write("ezj_ext_editor_highlightDirectly", (highlightDirectly ? "1" : "0"), 2592000); // 有效期 30 天
            }
        };
        items.push("code"); // 增加按钮
    }
    
    items.push("|");
    items.push("about"); // 增加关于按钮
    
    // 两种加载方式，有时候某种加载方式显示不出来图标，可选用另一种加载方式。
    if (setupType == "show")
    {
        KE.show({
            id : textareaId,
            items : items
        });
    }
    else
    {
        KE.init({
            id : textareaId,
            items : items
            
        });
        KE.create(textareaId);
    }
    
    Function.run(onOk);
};


ezj.ext.editor.codeClassName = "code";


//================================================================================


ezj.ext.flier = function(imageUrl, imageAlt, link)
{
///<summary>创建基于 FlyAds 的飞行广告控件。语法：ezj.ext.flier(imageUrl, imageAlt, link) 或 ezj.ext.flier(config)</summary>
///<param name="imageUrl" type="string">飞行广告的图片地址。</param>
///<param name="imageAlt" type="string">飞行广告的图片替换文字。</param>
///<param name="link" type="url">飞行广告链接地址。</param>
    if (!ezj.ext.load("flier", ["ext/flier/FlyAds.js"]))
    {
        return;
    }
    
    if (Function.like("object"))
    {
        var config = arguments[0];
        imageUrl = Object.prop(config, "imageUrl", "");
        imageAlt = Object.prop(config, "imageAlt", "");
        link = Object.prop(config, "link", "");
    }
    
    var id = "ezj_ext_flier_" + Math.nextRandom(0, 999999999);
    var div = $c("div", { id : id }, document.body);
    var a = $c("a", { href : link, target : "_blank" }, div);
    $c("img", { src : imageUrl, alt : imageAlt, border : "0" }, a);
    
    var flierObj = new FlyAds(id, Math.nextRandom(0, 500), Math.nextRandom(0, 300));
    var flierElement = $g(id);
    flierElement.addListener("mouseover", function() { flierObj.Pause(); }); // 鼠标移上去时暂停
    flierElement.addListener("mouseout", function() { flierObj.Pause(); }); // 鼠标移出去时继续
    flierElement.addListener("click", function() { flierObj.Clear(); }); // 单击后消失
    flierObj.Fly();
};


//================================================================================


ezj.ext.floater = function(element, offsetX, offsetY)
{
///<summary>创建基于 FloatAds 的浮动控件。语法：ezj.ext.floater(element, offsetX, offsetY) 或 ezj.ext.floater(element[, config])</summary>
///<param name="element" type="string/object">要进行浮动的元素查询字符串，或元素对象。</param>
///<param name="offsetX" type="number">若为正值，则为浮动元素左边距窗口左边的距离；若为负值，则为浮动元素右边距窗口右边的距离。可使用 -0.1 表示 -0 效果。</param>
///<param name="offsetY" type="number">若为正值，则为浮动元素上边距窗口上边的距离；若为负值，则为浮动元素下边距窗口下边的距离。可使用 -0.1 表示 -0 效果。</param>
    if (!ezj.ext.load("floater", ["ext/floater/FloatAds.js"]))
    {
        return;
    }
    
    if (Function.like("any", "object"))
    {
        var config = arguments[1];
        offsetX = Object.prop(config, "offsetX", 0);
        offsetY = Object.prop(config, "offsetY", 0);
    }
    
    $g(element).style.position = "absolute"; // 这里有必要
    var elementSpace = $g(element).space();
    var winSpace = $g(document.body).space();
    var x = Math.round((offsetX >= 0) ? offsetX : winSpace.clientWidth + offsetX - elementSpace.width);
    var y = Math.round((offsetY >= 0) ? offsetY : winSpace.clientHeight + offsetY - elementSpace.height);
    var floater = new FloatAds($g(element), "absolute", x, y);
    
    $g(window).resize(function(e){
        var elementSpace = $g(element).space();
        var winSpace = $g(document.body).space();
        floater.fixedX = Math.round((offsetX >= 0) ? offsetX : winSpace.clientWidth + offsetX - elementSpace.width);
        floater.fixedY = Math.round((offsetY >= 0) ? offsetY : winSpace.clientHeight + offsetY - elementSpace.height);
    });

    floater.StartFloat();
};


//================================================================================


ezj.ext.focus = function(element, width, height)
{
///<summary>创建基于 SWFObject 的图片轮显控件。语法：ezj.ext.focus(element, width, height) 或 ezj.ext.focus(element[, config])</summary>
///<param name="element" type="string/object">要创建图片轮显的元素 id，或元素对象。这也是图片轮显数据来源之处。</param>
///<param name="width" type="integer">图片轮显宽度。</param>
///<param name="height" type="integer">图片轮显高度。</param>
    element = $g(element);
    if (Function.like("any", "object"))
    {
        var config = arguments[1];
        width = Object.prop(config, "width", 320);
        height = Object.prop(config, "height", 240);
    }
    
    // pics、links、texts 数据来源于 element
    // 超链接下面的图片的 src 作为 pics
    // 超链接的 href 作为 links
    // 超链接的 title 作为 texts
    var pics = "";
    var links = "";
    var texts = "";
    $g("<a", element).each(function(e, index){
        if (pics != "")
        {
            pics += "|";
            links += "|";
            texts += "|";
        }
        pics += $g("<img", e)[0].src.replaceAll("[|]", "/");
        links += e.href.replaceAll("[|]", "/");
        texts += e.title.replaceAll("[|]", "/");
    });
    var flashVars = {
        pics : pics,
        links : links,
        texts : texts,
        borderwidth : width,
        borderheight : height - 20, /*图片区高度*/
        textheight : 20 /*文字区高度*/
    };
    var params = {
        menu : false,
        allowfullscreen : false,
        bgcolor : "#FFFFFF",
        quality : "high",
        wmode : "opaque"
    };
    
    var container = $c("div");
    element.before(container);
    element.detach();
    ezj.ext.swf(container, ezj.about.path + "ext/focus/focus.swf", width, height, "6", flashVars, params);
};


//================================================================================


ezj.ext.focus2 = function(element, width, height)
{
///<summary>创建基于 ezj.ext.Focus2 的图片轮显控件。语法：ezj.ext.focus2(element, width, height[, speed]) 或 ezj.ext.focus2(element[, config])</summary>
///<param name="element" type="string/object">要创建图片轮显的元素 id，或元素对象。这也是图片轮显数据来源之处。</param>
///<param name="width" type="integer">图片轮显宽度。</param>
///<param name="height" type="integer">图片轮显高度。</param>
///<param name="speed" type="string/integer">可选。可选值为：slow、normal、fast，默认值为 normal。也可用数字，数字越大越慢。</param>
    if (!ezj.ext.load("focus2", ["ext/focus2/Focus2.js"], ["ext/focus2/focus2.css"]))
    {
        return;
    }
    
    var speed = "normal";
    if (Function.like("any", "object"))
    {
        var config = arguments[1];
        width = Object.prop(config, "width", 400);
        height = Object.prop(config, "height", 300);
        speed = Object.prop(config, "speed", speed);
    }
    else
    {
        speed = Function.overload(3, speed);
    }
    // speed 也可是字符串，其转换为数字的工作不在这里进行，而在 ezj.ext.Focus2 中。
    
    var focus2 = new ezj.ext.Focus2(element, width, height, speed);
};


//================================================================================


ezj.ext.keylighter = function(element, keys, bgColors, colors)
{
///<summary>创建基于 highlightKey 的关键词高亮控件。语法：ezj.ext.keylighter(element, keys, bgColors, colors) 或 ezj.ext.keylighter(element[, config])</summary>
///<param name="element" type="string/object">要查找关键词并高亮的元素查询字符串，或元素对象。</param>
///<param name="keys" type="string/array">关键词。若有多个关键词，则使用数组。</param>
///<param name="bgColors" type="string/array">关键词背景颜色，与关键词一一对应。若有多个背景颜色，则使用数组。</param>
///<param name="colors" type="string/array">关键词前景颜色，与关键词一一对应。若有多个前景颜色，则使用数组。</param>
    if (!ezj.ext.load("keylighter", ["ext/keylighter/HighlightKey.js"]))
    {
        return;
    }
    
    if (Function.like("any", "object"))
    {
        var config = arguments[1];
        keys = Object.prop(config, "keys", keys);
        bgColors = Object.prop(config, "bgColors", bgColors);
        colors = Object.prop(config, "colors", colors);
    }
    
    highlightKey($g(element), Array.from(keys), Array.from(bgColors), Array.from(colors));
};


//================================================================================


ezj.ext.loc = function(element)
{
///<summary>创建基于 ezj.ext.Loc 的下拉列表项查找控件。语法：ezj.ext.loc(element)</summary>
///<param name="element" type="string/object">要进行下拉列表项查找的元素查询字符串，或元素对象。</param>
    if (!ezj.ext.load("keylighter", ["ext/loc/Loc.js"]))
    {
        return;
    }
    
    Array.from($g(element)).each(function(e){
        var loc = new ezj.ext.Loc(e);
    });
};


//================================================================================


ezj.ext.palette = function(inputField)
{
///<summary>创建基于 ColorDialog 的颜色对话框控件。语法：ezj.ext.palette(inputField[, button[, response[, onSelecting[, onSelected]]]]) 或 ezj.ext.palette(inputField[, config])</summary>
///<param name="inputField" type="string/object">存储、显示颜色值的元素查询字符串，或元素对象。</param>
///<param name="button" type="string/object">可选。触发颜色对话框显示的元素查询字符串，或元素对象。默认值为 inputField 对应的元素。</param>
///<param name="response" type="string/array">可选。inputField 如何响应颜色对话框的选择。value 表示显示选择的值；bgColor 表示将背景色设置为选择的值；color 表示将前景色设置为选择的值。要设置多个值，可使用数组。默认值为 value。</param>
///<param name="onSelecting" type="function">可选。正在选择颜色时要执行的函数。正在选择的颜色值将传入该函数。默认值为 null。</param>
///<param name="onSelected" type="function">可选。选择了颜色后要执行的函数。选择的颜色值将传入该函数。默认值为 null。</param>
    if (!ezj.ext.load("palette", ["ext/palette/ColorDialog.js"]))
    {
        return;
    }
    
    inputField = $g(inputField);
    var button = inputField;
    var response = ["value"];
    var onSelecting = null;
    var onSelected = null;
    if (Function.like("any", "object") && !String.is(arguments[1].tagName))
    {
        // 在 IE 中，HTML 元素也是 object 类型，这里用 tagName 排除之。
        var config = arguments[1];
        button = $g(Object.prop(config, "button", button));
        response = Array.from(Object.prop(config, "response", response));
        onSelecting = Object.prop(config, "onSelecting", onSelecting);
        onSelected = Object.prop(config, "onSelected", onSelected);
    }
    else
    {
        button = $g(Function.overload(1, button));
        response = Array.from(Function.overload(2, response));
        onSelecting = Function.overload(3, onSelecting);
        onSelected = Function.overload(4, onSelected);
    }
    
    var colorDialog = new ColorDialog(button);
    if (response.exists("value"))
    {
        colorDialog.selectedColor = $g(inputField).val(); // 设置颜色对话框控件初始值
    }
    colorDialog.onColorSelecting = function(){
        if (response.exists("value"))
        {
            $g(inputField).val(colorDialog.selectingColor);
        }
        if (response.exists("bgColor"))
        {
            $g(inputField).style.backgroundColor = colorDialog.selectingColor;
        }
        if (response.exists("color"))
        {
            $g(inputField).style.color = colorDialog.selectingColor;
        }
        Function.run(onSelecting, colorDialog.selectingColor);
    };
    colorDialog.onColorSelected = function(){
        if (response.exists("value"))
        {
            $g(inputField).val(colorDialog.selectedColor);
        }
        if (response.exists("bgColor"))
        {
            $g(inputField).style.backgroundColor = colorDialog.selectedColor;
        }
        if (response.exists("color"))
        {
            $g(inputField).style.color = colorDialog.selectedColor;
        }
        Function.run(onSelected, colorDialog.selectedColor);
    };
    colorDialog.onColorCancelled = function(){
        // 取消了颜色选择
        var color = colorDialog.selectedColor;
        if (response.exists("value"))
        {
            $g(inputField).val(color);
        }
        if (response.exists("bgColor"))
        {
            $g(inputField).style.backgroundColor = color;
        }
        if (response.exists("color"))
        {
            $g(inputField).style.color = color;
        }
    };
    if (response.exists("value"))
    {
        $g(inputField).val(colorDialog.selectedColor);
    }
    if (response.exists("bgColor"))
    {
        $g(inputField).style.backgroundColor = colorDialog.selectedColor;
    }
    if (response.exists("color"))
    {
        $g(inputField).style.color = colorDialog.selectedColor;
    }
    colorDialog.create();
};


//================================================================================


ezj.ext.pin = function(element)
{
///<summary>为 SELECT 的各项创建便于定位的拼音首字母。语法：ezj.ext.pin(element[, onOk])</summary>
///<param name="element" type="string/object/array">要创建拼音首字母的 SELECT 元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="onOk" type="function">可选。创建完成后要执行的函数。</param>
    if (!ezj.ext.load("pinyin", ["ext/pinyin/Pinyin.js"]))
    {
        return;
    }
    
    var onOk = Function.overload(1, null);
    var es = Array.from($g(element));
    es.each(function(e){
        for (var i = 0; i < e.options.length; i++)
        {
            if (e.options[i].text.length <= 0)
            {
                continue;
            }
            
            // 解决常见多音字
            if (e.options[i].text.substr(0, 2) == "重庆")
            {
                e.options[i].text = "C - " + e.options[i].text;
                continue;
            }
            
            var pin = ezj.ext.Pinyin.getSpell(e.options[i].text.substr(0, 1));
            if (!String.isNullOrEmpty(pin))
            {
                e.options[i].text = pin.substr(0, 1).toUpperCase() + " - " + e.options[i].text;
            }
        }
        
        Function.run(onOk, e);
    });
};


//================================================================================


// http://www.longtailvideo.com/players/jw-flv-player/
ezj.ext.player = function(container, flvUrl, width, height)
{
///<summary>创建基于 SWFObject 和 JW FLV Player 的 .flv 文件播放器控件。语法：ezj.ext.player(container, flvUrl, width, height[, previewImageUrl][, autoStart]) 或 ezj.ext.player(container[, config])</summary>
///<param name="container" type="string/object">要创建 .flv 文件播放器的元素查询字符串，或元素对象。</param>
///<param name="flvUrl" type="string">.flv 文件 URL。</param>
///<param name="width" type="integer">播放器宽度。</param>
///<param name="height" type="integer">播放器高度。</param>
///<param name="previewImageUrl" type="string">可选。视频预览图片 URL，当视频还未播放时显示该图片。</param>
///<param name="autoStart" type="boolean">可选。是否自动播放。默认值为 false。</param>
    var previewImageUrl = ezj.about.path + "ext/player/preview.jpg";
    var autoStart = false;
    
    if (Function.like("any", "object"))
    {
        var config = arguments[1];
        flvUrl = Object.prop(config, "flvUrl", flvUrl);
        width = Object.prop(config, "width", width);
        height = Object.prop(config, "height", height);
        previewImageUrl = Object.prop(config, "previewImageUrl", previewImageUrl);
        autoStart = Object.prop(config, "autoStart", autoStart);
    }
    else if (Function.like("any", "string", "number", "number", "string"))
    {
        previewImageUrl = Function.overload(4, previewImageUrl);
        autoStart = Function.overload(5, autoStart);
    }
    else
    {
        autoStart = Function.overload(4, autoStart);
    }
    
    var flashVars = {
        file : flvUrl,
        width : width,
        height : height,
        image : previewImageUrl
    };
    var params = {
        allowfullscreen : true
    };
    
    ezj.ext.swf(container, ezj.about.path + "ext/player/flvplayer.swf" + (autoStart ? "?autostart=true" : ""), width, height, "9", flashVars, params);
};


//================================================================================


ezj.ext.rating = function(container, config, onOk)
{
///<summary>创建基于 ezj.ext.Rating 的评级控件。语法：ezj.ext.rating(container, config, onOk)</summary>
///<param name="container" type="string/object">要创建评级控件的元素查询字符串，或元素对象。</param>
///<param name="config" type="object">评级控件的配置对象。</param>
///<param name="onOk" type="function">创建完成后要执行的函数。</param>
    if (!ezj.ext.load("rating", ["ext/rating/Rating.js"]))
    {
        return;
    }
    
    var rating = new ezj.ext.Rating(container, config);
    Function.run(onOk, rating);
};


//================================================================================


ezj.ext.round = function(element, wallColor, bgColor)
{
///<summary>创建基于 nifty 的圆角背景和边框控件。语法：ezj.ext.round(element, wallColor, bgColor[, borderColor[, corners[, radius]]]) 或 ezj.ext.round(element[, config])</summary>
///<param name="element" type="string/object/array">要创建圆角背景和边框的元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="wallColor" type="string">墙面颜色，即圆角外围的颜色。形如：#f00 或 #ff0000。</param>
///<param name="bgColor" type="string">圆角背景颜色，即圆角内围的颜色。形如：#f00 或 #ff0000。</param>
///<param name="borderColor" type="string">可选。圆角边框颜色。形如：#f00 或 #ff0000。若为零长度字符串，表示不使用圆角边框。默认值为零长度字符串。</param>
///<param name="corners" type="string">可选。圆角位置。all 四个角；top 上方；bottom 下方；tl 左上角；tr 右上角；bl 左下角；br 右下角。多个这间用空格隔开。默认值为 all。</param>
///<param name="radius" type="string">可选。圆角半径。可选值为：big、small，默认值为 small。</param>
    if (!ezj.ext.load("round", ["ext/round/nifty.js"], ["ext/round/niftyCorners.css"]))
    {
        return;
    }
    
    if(!NiftyCheck())
    {
        return;
    }
    
    var options = "smooth";
    var borderColor = "";
    var corners = "all";
    var radius = "small";
    if (Function.like("any", "object"))
    {
        var config = arguments[1];
        wallColor = Object.prop(config, "wallColor", wallColor);
        bgColor = Object.prop(config, "bgColor", bgColor);
        borderColor = Object.prop(config, "borderColor", borderColor);
        corners = Object.prop(config, "corners", corners);
        radius = Object.prop(config, "radius", radius);
    }
    else
    {
        borderColor = Function.overload(3, borderColor);
        corners = Function.overload(4, corners);
        radius = Function.overload(5, radius);
    }
    
    if (borderColor != "")
    {
        options += " border " + borderColor;
    }
    if (radius == "small")
    {
        options += " small";
    }
    var es = Array.from($g(element));
    es.each(function(value, index){
        value.style.backgroundColor = bgColor; // 自动添加背景色
    });
    Rounded(es, corners, wallColor, bgColor, options);
};


//================================================================================


ezj.ext.scroll = function(element)
{
///<summary>创建基于 ScrollBinder 的滚动控件。语法：ezj.ext.scroll(element[, direction[, screenDelay[, speed[, step[, onOk]]]]]) 或 ezj.ext.scroll(element[, config[, onOk]])</summary>
///<param name="element" type="string/object">要创建滚动的元素查询字符串，或元素对象。</param>
///<param name="direction" type="string">可选。滚动方向。可选值为：up、right、down、left。默认值为：up</param>
///<param name="screenDelay" type="integer">可选。屏停时间，单位为毫秒。默认值为 0，即不使用屏停效果。</param>
///<param name="speed" type="string/integer">可选。可选值为：slow、normal、fast，默认值为 normal。也可用数字，数字越大越慢。</param>
///<param name="step" type="integer">可选。滚动时的步进长度，若需要较快的滚动，而改变 speed 也无能为力时，可增大该值。默认值为 1。</param>
///<param name="onOk" type="function">可选。创建完成后要执行的函数。</param>
    if (!ezj.ext.load("scroll", ["ext/scroll/ScrollBinder.js"]))
    {
        return;
    }
    
    var direction = "up";
    var screenDelay = 0;
    var speed = 50;
    var step = 1;
    var onOk = null;
    if (Function.like("any", "object"))
    {
        var config = arguments[1];
        direction = Object.prop(config, "direction", direction);
        screenDelay = Object.prop(config, "screenDelay", screenDelay);
        speed = Object.prop(config, "speed", speed);
        step = Object.prop(config, "step", step);
        onOk = Function.overload(2, onOk);
    }
    else
    {
        direction = Function.overload(1, direction);
        screenDelay = Function.overload(2, screenDelay);
        speed = Function.overload(3, speed);
        step = Function.overload(4, step);
        onOk = Function.overload(5, onOk);
    }
    speed = Array.map(["slow", "normal", "fast"], [100, 50, 10], speed, (!Number.is(speed) ? 50 : speed));
    
    var scroll = new ScrollBinder($g(element), direction, step, speed, screenDelay);
    if (screenDelay <= 0)
    {
        scroll.setAutoPause();
        scroll.start();
    }
    else
    {
        scroll.start("screen");
    }
    
    Function.run(onOk, scroll);
};


//================================================================================


// http://code.google.com/p/swfobject/
ezj.ext.swf = function(container, swfUrl, width, height)
{
///<summary>创建基于 SWFObject 的 .swf 文件播放器控件。语法：ezj.ext.swf(container, swfUrl, width, height[, version[, flashVars[, params]]]) 或 ezj.ext.swf(container[, config])</summary>
///<param name="container" type="string/object">要创建 .swf 文件播放器的元素查询字符串，或元素对象。</param>
///<param name="swfUrl" type="string">.swf 文件 URL。</param>
///<param name="width" type="integer">播放器宽度。</param>
///<param name="height" type="integer">播放器高度。</param>
///<param name="version" type="string">可选。.swf 文件发布的 Flash Player 版本。默认值为 9.0.0。</param>
///<param name="flashVars" type="object">可选。FlashVars。默认值为 {}。</param>
///<param name="params" type="object">可选。Flash object 的 params。默认值为 {}。</param>
    if (!ezj.ext.load("swf", ["ext/swf/swfobject.js"]))
    {
        return;
    }
    
    container = $g(container);
    var element = $c("div", {id:"ezj_ext_swf_" + Math.nextRandom(0, 999999999)}, container); // 替换 element 为 swf
    
    var version = "9.0.0";
    var flashVars = {};
    var params = {};
    if (Function.like("any", "object"))
    {
        var config = arguments[1];
        swfUrl = Object.prop(config, "swfUrl", "");
        width = Object.prop(config, "width", 320);
        height = Object.prop(config, "height", 240);
        version = Object.prop(config, "version", version);
        flashVars = Object.prop(config, "flashVars", flashVars);
        params = Object.prop(config, "params", params);
    }
    else
    {
        version = Function.overload(4, version);
        flashVars = Function.overload(5, flashVars);
        params = Function.overload(6, params);
    }
    
    // 先显示一个 flash 图标，这样如果客户端未安装 Flash 插件时，将显示这个图标。
    $g(element).val("<div><a href=\"http://www.adobe.com/go/getflashplayer/\" target=\"_blank\"><img src=\"http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif\" alt=\"Get Adobe Flash player\" /></a></div>");
    
    // 注意：传入 swfobject.embedSWF 的第一个参数应该是 id（字符串）。
    swfobject.embedSWF(swfUrl, element.id, width, height, version,
        null, flashVars, params); // 替换 element 为 swf
};


//================================================================================


ezj.ext.tab = function(titleContainers, contentContainers)
{
///<summary>创建基于 TabControl 的标签控件。语法：ezj.ext.tab(titleContainers, contentContainers[, normalTitleClass, hoverTitleClass, activatedTitleClass][, eventName]) 或 ezj.ext.tab(titleContainers, contentContainers[, config])</summary>
///<param name="titleContainers" type="string/object/array">标签头容器元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="contentContainers" type="string/object/array">标签内容容器元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="normalTitleClass" type="string">可选。普通状态标签头的样式名称。若不指定，则由 ezj 创建标签头样式。</param>
///<param name="hoverTitleClass" type="string">可选。鼠标移上去时标签头的样式名称。若不指定，则由 ezj 创建标签头样式。</param>
///<param name="activatedTitleClass" type="string">可选。激活状态标签头的样式名称。若不指定，则由 ezj 创建标签头样式。</param>
///<param name="eventName" type="string">可选。触发标签切换的事件名称，可选值为：mouseover、click，默认值为 mouseover。</param>
    if (!ezj.ext.load("tab", ["ext/tab/TabControl.js"], ["ext/tab/tab.css"]))
    {
        return;
    }
    
    titleContainers = Array.from($g(titleContainers));
    contentContainers = Array.from($g(contentContainers));
    var normalTitleClass = "ezj_ext_tab_normalTitle";
    var hoverTitleClass = "ezj_ext_tab_hoverTitle";
    var activatedTitleClass = "ezj_ext_tab_activatedTitle";
    var eventName = "mouseover";
    if (Function.match("any", "any", "string"))
    {
        eventName = Function.overload(2, eventName);
    }
    else if (Function.like("any", "any", "object"))
    {
        var config = arguments[2];
        normalTitleClass = Object.prop(config, "normalTitleClass", normalTitleClass);
        hoverTitleClass = Object.prop(config, "hoverTitleClass", hoverTitleClass);
        activatedTitleClass = Object.prop(config, "activatedTitleClass", activatedTitleClass);
        eventName = Object.prop(config, "eventName", eventName);
    }
    else
    {
        normalTitleClass = Function.overload(2, normalTitleClass);
        hoverTitleClass = Function.overload(3, hoverTitleClass);
        activatedTitleClass = Function.overload(4, activatedTitleClass);
        eventName = Function.overload(5, eventName);
    }
    
    if (arguments.length <= 3)
    {
        // 自动样式，设置每一个 titleContainer、contentContainer 的样式
        titleContainers.each(function(value, index){
            $g(titleContainers[index]).addClass("ezj_ext_tab_titleContainer");
            $g(contentContainers[index]).addClass("ezj_ext_tab_contentContainer");
            var titlesCount = 0;
            for (var i = 0; i < titleContainers[index].childNodes.length; i++)
            {
                if (titleContainers[index].childNodes[i].nodeType == 1)
                {
                    // 避免空白节点“闹事”
                    titlesCount++;
                }
            }
            contentContainers[index].style.width = ((titlesCount * 73) - 2) + "px"; // 73 为一个 tabTitle 宽度，这是默认样式的。-2 是减边框。
        });
    }
    
    titleContainers.each(function(value, index){
        var tabControl = new TabControl(normalTitleClass, hoverTitleClass, activatedTitleClass, eventName);
        tabControl.bindTabPages(titleContainers[index].childNodes, contentContainers[index].childNodes);
        tabControl.activateTabPage(0);
    });
};


//================================================================================


ezj.ext.tab2 = function (titleContainers, contentContainers)
{
///<summary>创建标签控件。每个标签的标题和内容 className 应为 item，当前标签 className 应为 cur，当触发标签切换的事件名称不为 mouseover 时，支持用 hover 作为 className 指示鼠标移上去的标签。语法：ezj.ext.tab2(titleContainers, contentContainers[, eventName[, onChanging[, onChanged]]]) 或 ezj.ext.tab2(titleContainers, contentContainers[, config])</summary>
///<param name="titleContainers" type="string/object/array">标签头容器元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="contentContainers" type="string/object/array">标签内容容器元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="eventName" type="string">可选。触发标签切换的事件名称，可选值为：mouseover、click，默认值为 mouseover。</param>
///<param name="onChanging" type="function">可选。正在切换标签时的事件处理程序，当前项的索引值作为参数传入。若该事件处理程序返回 false，则停止当前切换。</param>
///<param name="onChanged" type="function">可选。切换标签后的事件处理程序，当前项的索引值作为参数传入。</param>
    titleContainers = Array.from($g(titleContainers));
    contentContainers = Array.from($g(contentContainers));
    var eventName = "mouseover";
    var onChanging = null;
    var onChanged = null;
    if (Function.like("any", "any", "string"))
    {
        eventName = Function.overload(2, eventName);
        onChanging = Function.overload(3, onChanging);
        onChanged = Function.overload(4, onChanged);
    }
    else if (Function.like("any", "any", "object"))
    {
        var config = arguments[2];
        eventName = Object.prop(config, "eventName", eventName);
        onChanging = Object.prop(config, "onChanging", onChanging);
        onChanged = Object.prop(config, "onChanged", onChanged);
    }
    
    titleContainers.each(function (titleContainer, index)
    {
        var titleItems = $g(".item", titleContainers[index]);
        var contentItems = $g(".item", contentContainers[index]);
        titleItems.each(function (titleItem, index)
        {
            if (eventName != "mouseover")
            {
                titleItems[index].addListener("mouseover", function(e){
                    e.addClass("hover");
                });
                titleItems[index].addListener("mouseout", function(e){
                    e.removeClass("hover");
                });
            }
            
            titleItems[index].addListener(eventName, function ()
            {
                if (Function.run(onChanging, index) === false)
                {
                    return;
                }
                
                for (var i = 0; i < titleItems.length; i++)
                {
                    titleItems[i].removeClass("cur");
                    contentItems[i].removeClass("cur");
                }
                titleItems[index].addClass("cur");
                contentItems[index].addClass("cur");
                
                Function.run(onChanged, index);
            });
        });
    });
};


//================================================================================


ezj.ext.typer = function(element)
{
///<summary>创建基于 ezj.ext.Typer 的打字控件。语法：ezj.ext.typer(element[, screenDelay[, speed[, onOk]]]) 或 ezj.ext.typer(element[, config[, onOk]])</summary>
///<param name="element" type="string/object/array">要创建打字控件的元素查询字符串，或元素对象，或元素对象数组。这也是打字控件数据来源之处。</param>
///<param name="screenDelay" type="integer">可选。屏停时间，单位为毫秒。默认值为 1000。</param>
///<param name="speed" type="integer/string">可选。打字速度。可选值为：slow、normal、fast，默认值为 normal。也可用数字，数字越大越慢。</param>
///<param name="onOk" type="function">可选。创建完成后要执行的函数。</param>
    if (!ezj.ext.load("typer", ["ext/typer/Typer.js"]))
    {
        return;
    }
    
    element = $g(element);
    var screenDelay = 1000;
    var speed = "normal";
    var onOk = null;
    if (Function.like("any", "object"))
    {
        var config = arguments[1];
        screenDelay = Object.prop(config, "screenDelay", screenDelay);
        speed = Object.prop(config, "speed", speed);
        onOk = Function.overload(2, null);
    }
    else
    {
        screenDelay = Function.overload(1, screenDelay);
        speed = Function.overload(2, speed);
        onOk = Function.overload(3, null);
    }
    // speed 也可是字符串，其转换为数字的工作不在这里进行，而在 ezj.ext.Typer 中。
    
    (Array.from(element)).each(function(element){
        var items = [];
        $g("<a", element).each(function(e, index){
            items.push({href:e.href, title:e.title, target:e.target, text:$v(e)});
        });
        
        var container = $c("span");
        element.before(container);
        element.detach();
        var t = new ezj.ext.Typer(container, items, screenDelay, speed);
        t.type();
        
        Function.run(onOk, t);
    });
};


//================================================================================


ezj.ext.watermark = function(element)
{
///<summary>创建基于 ezj.ext.Watermark 的文本框水印控件，将其 title 属性值作为水印文字。语法：ezj.ext.watermark(element[, text]) 或 ezj.ext.watermark(element[, config])</summary>
///<param name="element" type="string/object/array">要创建水印控件的元素查询字符串，或元素对象，或元素对象数组。</param>
///<param name="text" type="string/array">可选。水印文字，多个元素时请使用数组。若省略则使用 element 的 title 属性值作为属性文字。</param>
    if (!ezj.ext.load("watermark", ["ext/watermark/Watermark.js"], ["ext/watermark/watermark.css"]))
    {
        return;
    }
    
    var text = null;
    if (Function.like("any", "object"))
    {
        var config = arguments[1];
        text = Object.prop(config, "text", text);
    }
    else
    {
        text = Function.overload(1, null);
    }
    (Array.from($g(element))).each(function(e, index){
        if (String.is(text))
        {
            var wm = new ezj.ext.Watermark(e, text);
        }
        else if (Array.is(text) && index < text.length)
        {
            var wm = new ezj.ext.Watermark(e, text[index]);
        }
        else
        {
            var wm = new ezj.ext.Watermark(e);
        }
    });
};


//================================================================================


ezj.ext.zoom = function(element)
{
///<summary>创建基于 ezj.ext.Zoom 的图片放大（商城商品图片放大效果）控件。语法：ezj.ext.zoom(element)</summary>
///<param name="element" type="string/object/array">要创建图片放大（商城商品图片放大效果）控件的元素查询字符串，或元素对象，或元素对象数组。</param>
    if (!ezj.ext.load("zoom", ["ext/zoom/Zoom.js"]))
    {
        return;
    }
    
    var es = Array.from($g(element));
    es.each(function(e){
        new ezj.ext.Zoom(e);
    });
};