//验证字符串是否为空
function val_empty(str){
    if(str == "" || str.length == 0 || str == null){
        return false;
    }else{
        return true;
    }
}

function fortable(obj){
    //定义表格列除第一行外，鼠标经过以及移出时的样式
    if ($("#"+obj).length != 0) {
        $("#"+obj+" tr").mouseover(function() {
            $(this).css({
                "background":"#ecf6fc",
                "color":"#369"
            });
        }).mouseout(function() {
            $(this).css({
                "background":"#fff",
                "color":"#333"
            });
        });
        //表格的最行一行的每列的下边框为none
        $("#"+obj+" tr:last").find("td").css("border-bottom", "none");
    }
}


function val_num(strArg) {
    var result=strArg.match(/^\d+$/);
    if(result==null){
        return false;
    }
    if(parseInt(strArg)>0){
        return true;
    }
    return false;

}

