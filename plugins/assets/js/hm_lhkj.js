// 遮罩

function mask(text){
    var content = '<div class="layer">'+ text +'</div>'
    $('body').append(content);
    var w = ($('.layer').width() + 50)/2;
    $('.layer').css('marginLeft', -w + 'px');
    console.log($('.layer').width());
    $('.layer').fadeIn();
    setTimeout(function(){
        $('.layer').fadeOut()
    }, 2000);
}



//购物车操作商品数量
function countOper(type,obj){       
    switch(type){
        case '-':
            var num = parseInt($(obj).next().html());
            if(num > 1){
                num--;
            }
            $(obj).next().html(num);
            break;
        case '+':
            var num = parseInt($(obj).prev().html());
            num++;
            $(obj).prev().html(num);
            break;
        default:
            break;
    }
}



//底部弹出选择效果
function pickerShow(arr, valEl){
    var picker = new Picker({
        data: arr,
        selectedIndex: [0],
        title: '选择银行卡'
    });

    picker.on('picker.select', function (selectedVal, selectedIndex) {
        //valEl.innerHTML = arr[0][selectedIndex[0]].text + arr[1][selectedIndex[1]].text;
        valEl.innerHTML = '';

        for(var i=0; i<arr.length; i++){
            valEl.value += arr[i][selectedIndex[i]].text;
        }
    });

    valEl.addEventListener('click', function () {
        picker.show();
    });
}





