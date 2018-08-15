// Custom scripts
SYS_PAGE_SIZE=20;
function selectAll()
{
    var checkbox_len=$("[id^='checkbox_']:checkbox").length;
    var checked_len=$("input[type='checkbox'][id^='checkbox_']:checked").length;

    if (checkbox_len==checked_len)
    {
        $("input[type='checkbox'][id^='checkbox_']").removeAttr("checked");
    }
    else
    {
        $("#selectAll").prop("checked", true);
        $("input[type='checkbox'][id^='checkbox_']").prop("checked", true);
    }
}

function datatable_get(url,postdata)
{
    $.post(url,postdata,function(data){
        alert(data);
        var jd=eval(data);

    });
}

function clear_id()
{
    $("#id").val("");
}

function layer_show(title,url,w,h){
    if (title == null || title == '') {
        title=false;
    };
    if (url == null || url == '') {
        url="404.html";
    };
    if (w == null || w == '') {
        w=800;
    };
    if (h == null || h == '') {
        h=($(window).height() - 50);
    };
    layer.open({
        type: 2,
        area: [w+'px', h +'px'],
        fix: false, //不固定
        maxmin: true,
        shade:0.4,
        title: title,
        content: url
    });
}

/*关闭弹出框口*/
function layer_close(url){
    var index = parent.layer.getFrameIndex(window.name);
    if(url!='') //重新载入父窗口页面
        parent.location.reload();
    parent.layer.close(index);
}

$(document).keypress(function(e) {
    // 回车键事件
    if(e.which == 13) {
        getData(1);
    }
});

function closeModal(name)
{
    $("#"+name+"").hide();
}


