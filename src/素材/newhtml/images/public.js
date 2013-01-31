//s当前ID，num总共个数，p1选中是的样式，p2正常时的样式，obj1选项卡栏ID前缀，obj2要显示内容的ID前缀
function changemenu(s, num, p1, p2, obj1, obj2){
    for(var i=0;i<num;i++){
        if(s==i){
            $("#"+obj1+i).removeClass().addClass(p1);
            $("#"+obj2+i).show();
        }else{
            $("#"+obj1+i).removeClass().addClass(p2);
            $("#"+obj2+i).hide();
        }
    }
}