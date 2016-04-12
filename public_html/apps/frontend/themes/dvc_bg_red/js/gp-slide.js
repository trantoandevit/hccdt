var time = 8000;
var silde_index = 0;

$(document).ready(function () {
    //hien trang default
    $('.gp-silde ul li:eq(0)').show();
    silde_index ++;
    
    //chay slide
    window.setInterval(function() {
        gp_change_focus();
    }, time);
    
});

//btn onclick
function gp_select_article_onclick(selected)
{
    //lay index theo button
    index_li_show = $(selected).parent().index();
    //tao selector cho li show
    li_selected = '.gp-silde >ul li:eq('+index_li_show+')';
    //an toan bo li
    $('.gp-silde >ul li').each(function () {
        $(this).hide();
    });
    //hien thi li theo index
    $(li_selected).show();
    
    //gan index moi
    silde_index = index_li_show;
}

//thay doi li
function gp_change_focus()
{
    //an tat ca li
    $('.gp-silde >ul li').each(function () {
        $(this).hide();
    });
    
    //hien thi li theo index
    li_show = '.gp-silde >ul li:eq('+silde_index+')';
    $(li_show).show();
    
    //dem so li de hien thi
    count_li = parseInt($('.gp-silde >ul li').length) - 1;
    
    //tang hoac reset index
    if(silde_index == count_li)
    {
        silde_index = 0;
    }
    else
    {
        silde_index++;
    }
    
}