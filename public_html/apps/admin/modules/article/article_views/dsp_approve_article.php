<?php
defined('DS') or die('no direct access');

$this->template->title = __('approve article');
$this->template->display('dsp_header_mobile.php');

$rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
$page          = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 0;

$v_search           = get_post_var('hdn_search');
$arp_approve_method = get_post_var('hdn_arp_approve_artilce','arp_approve_article/2');
?>
<form action="<?php echo SITE_ROOT . "admin/banner"; ?>" name="frmMain" id="frmMain" method="POST">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', '0');
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_approve_article');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_approve_article');
    
    echo $this->hidden('hdn_arp_approve_artilce', $arp_approve_method);
    
    //paging
    echo $this->hidden('sel_rows_per_page', $rows_per_page);
    echo $this->hidden('sel_goto_page', $page);
    echo $this->hidden('hdn_search',$v_search);
    ?>
<div name="div_article" id="div_article" style="height: auto;background-color: #EBEBEB;padding: 5px;">
</div><!--end content-->
<div id="div_load" style="display: none">
    <center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>
</div>
<div id="div_continue" class="Row mobile-continue">
    <a href="javascript:void(0);" onclick="arp_view_next_onclick();" style="display: block;width: 100%;">Xem tiếp</a>
</div>
</form>
<script>
    $(document).ready(function (){
        $('#txt_search').val($('#hdn_search').val());
        //arp_view_next_onclick();
        //select menu
        data_ajax = $('#hdn_arp_approve_artilce').val();
        str_selector = '.mobile-menu [data-ajax="'+ data_ajax +'"]';
        menu_selected = $(str_selector);
        menu_mobile_onclick(menu_selected);
    });
    function arp_view_next_onclick()
    {
        page = parseInt($('#sel_goto_page').val()) + 1;
        $('#sel_goto_page').val(page);
        url_arp = $('#controller').val() + $('#hdn_arp_approve_artilce').val();
        $.ajax({
            type: 'post',
            url: url_arp,
            data: $('#frmMain').serialize(),
            beforeSend: function() {
                    $('#div_load').show();
                    $('#div_continue').hide();
            },
            success: function (html){
                    $('#div_load').hide();
                    $('#div_continue').show();
                    $('#div_article').append(html);
            }
            });
    }
    
    function btn_search_onclick()
    {
        $('#sel_goto_page').val(0);
        $('#hdn_search').val($('#txt_search').val());
        $('#div_article').html('');
        arp_view_next_onclick();
    }
    
    //xu ly menu
    function menu_mobile_onclick(menu_selected)
    {
        $('.mobile-menu a').each(function (){
            $(this).attr('class','');
        });
        
        $(menu_selected).addClass('mobile-menu-selected');
        
        hdn_arp_approve_artilce = $(menu_selected).attr('data-ajax');
        $('#hdn_arp_approve_artilce').val(hdn_arp_approve_artilce);
        
        $('#sel_goto_page').val(0);
        $('#hdn_search').val('');
        $('#div_article').html('');
        arp_view_next_onclick();
    }
    
    
</script>
<?php $this->template->display('dsp_footer_mobile.php'); ?>