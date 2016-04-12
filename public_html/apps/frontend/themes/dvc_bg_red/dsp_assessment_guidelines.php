<?php
//du lieu header
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$arr_assessment_guidancelines = isset($arr_assessment_guidancelines) ? $arr_assessment_guidancelines : '';
?>
<?php
$VIEW_DATA['arr_css']               = array('synthesis','component','single-page');
$VIEW_DATA['arr_script']        = array('');
$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

?>
<?php
    $file_path_menu_top_two = __DIR__ . DS . 'menu_top_two_evaluation.php';
    if (is_file($file_path_menu_top_two)) {
        require $file_path_menu_top_two;
    }
?>
<div class="col-md-12 content" id="single-page">
        <div  class="col-md-12 ">
            <div class="col-md-12 block">
            <div class="div_article">            
                <?php 
                    @$dom = simplexml_load_string($arr_assessment_guidancelines);
                    if($dom)
                    {
                        $assessment_guidance = $dom->xpath('//item/value');
                        $assessment_guidance = isset($assessment_guidance[0]) ? (string)$assessment_guidance[0] : '';
                        echo html_entity_decode($assessment_guidance);
                    }
                ?>
            </div>       

        </div>
        <!--End #Main-content-->
        <div class="clear" style="height: 10px;"></div>
    </div>
</div>
<script>
    function print_onclick()
    {
        str="<?php echo build_url_print($v_category_slug, $article_slug, $website_id, $category_id, $article_id) ?>";
        window.open(str,"",'scrollbars=1,width=700,height=600');
    }
</script>

<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>