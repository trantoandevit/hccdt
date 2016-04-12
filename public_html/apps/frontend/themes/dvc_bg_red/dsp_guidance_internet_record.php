<?php
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css']               = array('component');
$VIEW_DATA['arr_script']        = array();

$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

$dom_guidance = simplexml_load_string($arr_guidance['C_XML_DATA'],'SimpleXMLElement',LIBXML_NOCDATA);
$result       =  $dom_guidance->xpath('//item[@id="txt_guidance"]/value');
$guidance     = (string) $result[0];
?>
<div class="clear" style="height: 10px"></div>
<div class="col-md-12 content">
    <div class="col-md-12 block">
        <div class="col-md-7"> 
            <div class="cp-border">
                <div class="div-synthesis">
                    <div class="div_title_bg-title-top"></div>
                    <div class="div_title">
                        <div class="title-border-left"></div>
                        <div class="title-content">
                            <label>
                                <?php echo __('guidance for internet record')?>
                            </label>
                        </div>
                    </div>
                    <div style="overflow: hidden;margin-top: -6px;"></div>
                    <div class="cp-content" style="width: 100%; padding-right: 10px">
                        <div style="text-align: justify">
                            <?php echo html_entity_decode($guidance)?>
                        </div>
                    </div>
                </div><!--END div-synthesis-->
            </div>
        </div>
        <div class="col-md-5">
            <div class="member">
                <div class="cp-border" >
                    <div class="div-synthesis">
                        <div class="div_title_bg-title-top"></div>
                        <div class="div_title">
                            <div class="title-border-left"></div>
                            <div class="title-content">
                                <label>
                                    <?php echo __('receiving unit records')?>
                                </label>
                            </div>
                        </div>
                        <div style="overflow: hidden;margin-top: -6px;"></div>
                        <div class="cp-content" >
                            <ul>
                                <?php foreach($arr_all_member as $arr_memeber):
                                        $v_meber_id   = $arr_memeber['PK_MEMBER'];
                                        $v_meber_name = $arr_memeber['C_NAME'];
                                        $v_url        = build_url_send_internet_record($v_meber_id);
                                ?>
                                <li>
                                    <img src="<?php echo CONST_SITE_THEME_ROOT . 'images/icon_member.png' ?>" />
                                    <a href="<?php echo $v_url?>"><?php echo $v_meber_name?></a>
                                </li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end homepage header-->
    <div class="clear" style="height: 10px;"></div>
</div>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>
