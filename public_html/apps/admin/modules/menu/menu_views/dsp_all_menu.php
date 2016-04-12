<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}?>
<?php
//header
@session::init();
$v_website_id = session::get('session_website_id');
$this->template->title = __('menu manager');
$this->template->display('dsp_header.php');
if(isset($arr_all_position[0]['PK_MENU_POSITION']))
{
    $v_position_id  = isset($_POST['hdn_position_id'])?$_POST['hdn_position_id']:$arr_all_position[0]['PK_MENU_POSITION'];
}
else
{
    $v_position_id = '';
}
// Lay gia chi C_TYPE cua menu load hien tai
$v_check_sitemap = 0;
for($i = 0;$i< count($arr_all_position);$i ++)
{
    if($arr_all_position[$i]['PK_MENU_POSITION'] == $v_position_id)
    {
        $v_check_sitemap = $arr_all_position[$i]['C_TYPE'];
        break;
    }
}
$v_chk_sitemap = ($v_check_sitemap == NULL OR $v_check_sitemap == 0)?0:$v_check_sitemap;

$v_theme_xml    = isset($arr_theme_position['C_XML_DATA'])?$arr_theme_position['C_XML_DATA']:'';

if($v_theme_xml!='')
{
    $dom    = simplexml_load_string($v_theme_xml);
    $x_path = '//data/item[@id="txtvitrimenu"]/value';
    $r      = $dom->xpath($x_path);
    if(isset($r[0]))
    {
       $arr_theme_position_menu =  explode(',', $r[0]);
    }
    else
    {
        $arr_theme_position_menu = array();
    }
}

if(isset($website_menu))
{
    $dom    = simplexml_load_string($website_menu);
    $x_path = '//theme_position[@id_website='.$v_website_id.']';
    $r      =$dom->xpath($x_path);
   
}
?>
<h2 class="module_title"><?php echo __('menu manager');?></h2>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id',0);
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_menu');
    echo $this->hidden('hdn_dsp_all_method','dsp_all_menu');
    echo $this->hidden('hdn_update_method','update_menu');
    echo $this->hidden('hdn_delete_method','delete_menu');
    echo $this->hidden('hdn_position_id',$v_position_id);
    echo $this->hidden('hdn_item_id_swap',0);
    
    //su dung de check site map
    echo $this->hidden('hdn_chk_sitemap',$v_chk_sitemap);
    ?>
<div class="Row">
    <?php if($arr_theme_position_menu != array()):?>
    <div class="left-Col" id="theme_position">
        <div class="theme_position">
            <div class="header_theme_position">
                <h3><span><?php echo __('theme position');?></span></h3>
            </div>
            <div>
                <?php foreach($arr_theme_position_menu as $position_name):?>
                <div class="theme_position_item">
                        <span><?php echo $position_name;?></span>
                        <select name="theme_position_select" style="width: 100%" data-position_name="<?php echo $position_name;?>">
                            <option value="0">---- <?php echo __('select position');?> ----</option>
                            <?php foreach ($arr_all_position as $row):?>
                            <option value="<?php echo $row['PK_MENU_POSITION'];?>"
                                    <?php
                                        if(isset($website_menu))
                                        {
                                            $dom    = simplexml_load_string($website_menu);
                                            $x_path = '//theme_position[@id_website='.$v_website_id.']/
                                                        item[@position_menu_id="'.$row['PK_MENU_POSITION'].'"]
                                                       [@position_code="'.$position_name.'"]/@position_menu_id';
                                            $r      =$dom->xpath($x_path);
                                            echo isset($r[0])?'selected':'';
                                        }
                                    ?>
                            >
                                <?php echo $row['C_NAME'];?>
                            </option>
                            <?php endforeach;?>
                        </select>
                </div>
                <?php endforeach;?>
                    <div class="button-area">
                       <input type="button" name="btn_accept_theme" id="btn_accept_theme" onclick="btn_accept_theme_onclick()" class="ButtonAccept" value="<?php echo __('update');?>">
                   </div>
            </div>
        </div>
        <div style="margin: 10px;">
            <input type="button" name="save_cache" value="<?php echo __('save cache')?>" class="button ButtonWriteHtmlCache" onclick="save_cache_onclick()">
        </div>
    </div>
    <?php endif;?>
    <div class="right-Col">
            <div id="tabs_menu">
                <ul>
                    <?php
                    $v_has_check_sitemap = 0;
                        for($i = 0; $i < count($arr_all_position);$i ++ )
                        {
                            if($arr_all_position[$i]['C_TYPE'] == 1)
                            {
                                $v_has_check_sitemap = 1;
                            }
                        }
                    ?>
                    <?php foreach($arr_all_position as $row):?>
                            
                        <li>
                            <a href="#position_detail_<?php echo $row['PK_MENU_POSITION'];?>" 
                               id="tab_<?php echo $row['PK_MENU_POSITION'];?>" 
                               value="<?php echo $row['PK_MENU_POSITION'];?>" 
                               data-name="<?php echo $row['C_NAME'];?>"
                               data-sitemap="<?php echo $row['C_TYPE'];?>"
                               onclick="single_position_onclick(this)"> 
                            <?php echo $row['C_NAME'];?>
                            </a>
                        </li>
                    <?php endforeach;?>
                    <?php if(session::check_permission('THEM_MOI_VI_TRI_MENU')>0):?>
                        <li><a href="#add_new_position" onclick="new_position_onclick()"><?php echo __('+')?></a></li>
                    <?php endif;?>
                </ul>
                <!--div thong tin menu position-->
                <div id="info_position">
                    <!--sua vi tri menu-->
                    <div>
                        <label><?php echo __('ministry menu name');?></label>
                        <input type="textbox" value="" name="txt_position_name" id="txt_position_name">
                        <?php if(session::check_permission('SUA_VI_TRI_MENU')>0):?>
                        <input type="button" value="<?php echo __('update');?>"  class="ButtonAccept" onclick="btn_update_position_onclick()">
                        <br/>
                        <label style="margin-left:96px;">
                            <input type="checkbox" name="chk_sitemap" id="chk_sitemap">
                            <?php echo __('has sitemap');?>
                        </label>
                        <?php endif;?>
                    </div>
                    <!--xoa vi tri menu-->
                    <div>
                        <?php if(session::check_permission('XOA_VI_TRI_MENU')>0):?>
                        <input type="button" class="ButtonDelete" value="<?php echo __('delete ministry menu');?>" onclick="btn_delete_position_onclick()">
                        <?php endif;?>
                    </div>
                    <label class="required" id="check_err_position"></label>
                </div>
                <!--tao div position detail theo id-->
                <?php foreach($arr_all_position as $row):
                        $v_position = $row['PK_MENU_POSITION'];
                ?>
                <div id="position_detail_<?php echo $v_position;?>" style="min-height: 368px;">
                </div>
                <?php  endforeach;?>
                <!--add new position-->
                <?php if(session::check_permission('THEM_MOI_VI_TRI_MENU')>0):?>
                <div id="add_new_position" style="min-height: 368px;">
                    <label><?php echo __('position name ');?></label>
                    <input type="textbox" value="" name="txt_new_position_name" id="txt_new_position_name">
                    <input type="button" value="<?php echo __('update');?>" class="ButtonAccept" onclick="btn_update_position_onclick()">
                    <br/>
                    <label style="margin-left:69px;">
                        <input type="checkbox" name="chk_sitemap" id="chk_sitemap">
                        <?php echo __('has sitemap');?>
                    </label>
                </div>
                <?php endif;?>
            </div>
        </div>
</div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
           $('#tabs_menu').tabs();
           <?php if($v_position_id !=''):?>
           var tab = "#position_detail_<?php echo $v_position_id;?>";
           $('#tabs_menu').tabs('select',tab);
           
            $.ajax({
                type: 'post',
                url: '<?php echo $this->get_controller_url() . 'dsp_single_position/'.$v_position_id; ?>',
                  beforeSend: function() {
                     img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                     $(tab).html(img);
                 },
                success: function(result)
                {
                    $(tab).html(result);
                }
            });
           var position_name     =  $('#tab_<?php echo $v_position_id;?>').attr('data-name');
           $('#txt_position_name').attr('value',position_name);
          
           <?php else:?>
           $('#info_position').css({'display':'none'});
           <?php endif;?>
            
           //check site map
           chk_sitemap();
    });
    function single_position_onclick(position)
    {
       $('#check_err_position').html('');
       $('#txt_new_position_name').val('')
       $('#info_position').css({'display':'block'});
       
       var position_name     = $(position).attr('data-name');
       var check_sitemap     = $(position).attr('data-sitemap');
       //thay doi bien hdn_chk_sitemap
       $('#hdn_chk_sitemap').val(check_sitemap);
       
       
       var value             = $(position).attr('value'); 
       var tab               = "#position_detail_" + value;
       $('#txt_position_name').attr('value',position_name);
       $('#hdn_position_id').attr('value',value);
       
       
       $.ajax({
           type: 'post',
           url: '<?php echo $this->get_controller_url() . 'dsp_single_position/';?>'+value,
           beforeSend: function() {
                     img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                     $(tab).html(img);
                 },
           success: function(result)
           {
               $(tab).html(result);
           }
       });
       
       //check site map
       chk_sitemap();
    }
    function swap_order_menu(id,id_swap)
    {
        $('#hdn_item_id').attr('value',id);
        $('#hdn_item_id_swap').attr('value',id_swap);
        var str = "<?php echo $this->get_controller_url()."swap_order"?>";
        $('#frmMain').attr('action',str);
        $('#frmMain').submit();
    }
    function new_position_onclick()
    {
        $('#info_position').css({'display':'none'});
    }
    function btn_update_position_onclick()
    {
        str="<?php echo $this->get_controller_url()."update_position";?>";
        $('#frmMain').attr('action',str);
        $('#frmMain').submit();
    }
    function btn_delete_position_onclick()
    {
        var tab     = "#position_detail_"+$('#hdn_position_id').val()+' input[name="chk"]';
        var count   = 0;
        $(tab).each(function(){
            count = count +1;
        });
        //alert(count);
        if(count < 1)
        {
            str="<?php echo $this->get_controller_url()."delete_position";?>";
            $('#frmMain').attr('action',str);
            $('#frmMain').submit();
        }
        else
        {
            $('#check_err_position').html('Vị trí này vẫn còn ảnh quảng cáo !!!');
        }
    }
    function btn_accept_theme_onclick()
    {
        var array = new Array();
        $('[name="theme_position_select"]').each(function(index){
            position_name  = $(this).attr('data-position_name');
            position_value = $(this).val();
            temp = position_name +':'+position_value;
            array.push(temp);
        });
        $('#hdn_item_id_list').val(array.join());
        var url='<?php echo $this->get_controller_url().'update_theme_position'?>';
        $('#frmMain').attr('action',url);
        $('#frmMain').submit();
    }
    function save_cache_onclick()
    {
        url = '<?php echo $this->get_controller_url()?>create_cache';
        $('#frmMain').attr('action',url);
        $('#frmMain').submit();
    }
    
    //check site map
    function chk_sitemap()
    {
        checked = parseInt($('#hdn_chk_sitemap').val());
        $('#chk_sitemap').attr('checked',checked);
    }
</script>

<?php $this->template->display('dsp_footer.php');?>