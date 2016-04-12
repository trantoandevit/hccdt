<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = 'Chọn NSD';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
?>
<?php for($i=0;$i<count($arr_theme);$i++): 
    $v_theme_code = $arr_theme[$i]['C_CODE'];
    $v_theme_name = $arr_theme[$i]['C_NAME'];
    $v_xml_data = $arr_theme[$i]['C_XML_DATA'];
    
    $dom = simplexml_load_string($v_xml_data);
    $x_path = "//item[@id='txtGioiThieu'][last()]/value";
    $r = $dom->xpath($x_path);
    $v_theme_desc = isset($r[0]) ? $r[0] : '';
    ?>
    <div class="row-img">
        <div class="right-Col-img">
            <img class="img_theme" src="<?php echo SITE_ROOT . "apps/frontend/themes/$v_theme_code/screenshot.png";?>" 
                 data-theme_code = "<?php echo $v_theme_code;?>" 
                 data-theme_name = "<?php echo $v_theme_name;?>"
                 onclick="get_selected_theme(this)"/>
        </div>
        <div class="left-Col-img">
            <div style="margin-left: 20px;">
            <div style="font-weight: bold; font-size: 15px" ><?php echo $v_theme_name;?><br></div>
            <?php echo $v_theme_desc;?>
            </div>
        </div>
    <br class="clear">
    </div>
<?php endfor; ?>
<script>
function get_selected_theme(img)
    {
        var jsonObj = []; //declare array
                v_theme_code = $(img).attr('data-theme_code');
                v_theme_name = $(img).attr('data-theme_name');
                //alert(v_theme_code);
                jsonObj.push({'theme_code': v_theme_code,'theme_name':v_theme_name});
        returnVal = jsonObj;
        window.parent.hidePopWin(true);
    }
</script>    
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');

