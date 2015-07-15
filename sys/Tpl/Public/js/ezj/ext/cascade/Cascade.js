/*
 * ezj.ext.Cascade
 * v1.0
 */
 
ezj.ext.Cascade = function (valueElement, xml) {
    this.valueElement = $g(valueElement);
    this.xml = $x(xml);
    this.lists = new Array();
    var me = this;

    this.clearDescendant = function (parentIndex) {
        for (var i = parentIndex + 1; i < this.lists.length; i++) {
            this.lists[i].empty();
            this.lists[i].display(false);
        }
    }

    this.createChildren = function (parentIndex) {
        this.clearDescendant(curIndex);

        var parentId = "0";
        if (parentIndex >= 0) {
            parentId = this.lists[parentIndex].options[this.lists[parentIndex].selectedIndex].value;
        }
        var texts = Array.from(this.xml.val("//item[@value=" + parentId + "]/item/@text"));
        var values = Array.from(this.xml.val("//item[@value=" + parentId + "]/item/@value"));
        if (texts.length <= 0) {
            return; // 无下级
        }

        var curIndex = parentIndex + 1;
        if (curIndex >= this.lists.length) {
            // 创建新的
            this.lists.push($c("select"));
            // 设置位置
            if (parentIndex >= 0) {
                this.lists[parentIndex].after(this.lists[curIndex]);
            }
            else {
                this.valueElement.after(this.lists[curIndex]); // 第一个
            }
            // 事件
            this.lists[curIndex].addListener("change", function () {
                var value = me.lists[curIndex].options[me.lists[curIndex].selectedIndex].value;
                me.valueElement.val(value);
                me.clearDescendant(curIndex);

                if (value == parentId) {
                    return; // 选择的是空白项
                }
                me.createChildren(curIndex);
            });
        }
        else {
            this.lists[curIndex].empty();
            this.lists[curIndex].display(true);
        }

        // 填充第一项（空白项）
        this.lists[curIndex].val([{ text: "", value: parentId}]);
        // 填充其他项
        for (var i = 0; i < texts.length; i++) {
            this.lists[curIndex].val([{ text: texts[i], value: values[i]}], 1);
        }
    }

    // 用程序执行选择
    this.select = function (index, value) {
        if (this.lists.length <= 0 || index < 0 || index >= this.lists.length) {
            return;
        }

        this.lists[index].sel([value], true);
        this.createChildren(index);
    }
}