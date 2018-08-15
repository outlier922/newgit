/**
 * @brief 获取控件元素值的数组形式
 * @param string nameVal 控件元素的name值
 * @param string sort    控件元素的类型值:checkbox,radio,text,textarea,select
 * @return array
 */
function getArray(nameVal,sort)
{
    //要ajax的json数据
    var jsonData = new Array;
    switch(sort)
    {
        case "checkbox":
            $('input:checkbox[name="'+nameVal+'"]:checked').each(
                function(i)
                {
                    jsonData[i] = $(this).val();
                }
            );
            break;
    }
    return jsonData;
}

//打开iframe层
function hm_layer_iframe_show(title,url,w,h) {
    if (title == null || title == '') {
        title=false;
    }
    if (url == null || url == '') {
        url = "404.html";
    }
    if (w == null || w == '') {
        w=800;
    }
    if (h == null || h == '') {
        h=($(window).height() - 50);
    }
    return layer.open({
        type: 2,
        area: [w+'px', h +'px'],
        fix: false, //不固定
        maxmin: true,
        shade:0.4,
        title: title,
        content: url
    });
}


function http_build_query (data) {
    var str = "";
    for(var i in data){
        str += "&" + i + "=" + data[i];
    }
    return str;
}