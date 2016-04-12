<?php
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css']               = array('component','synthesis','table');
$VIEW_DATA['arr_script']        = array();

$v_url_type   = get_request_var('type','member');
$v_url_method = get_request_var('method','tiep_nhan');

$this->render('dsp_header', $VIEW_DATA, $this->theme_code);
?>
<?php
    $file_path_menu_top_two = __DIR__ . DS . 'menu_top_two_synthesis.php';
    if (is_file($file_path_menu_top_two)) {
        require $file_path_menu_top_two;
    }
?>
<div class="col-md-12 content">
    
    <div class="col-md-12 block" style="padding: 5px;">
        <?php echo $content?>
    </div><!--col md 9-->
</div><!--col md 12-->

<script>
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>
