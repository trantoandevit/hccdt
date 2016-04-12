<div class="widget-online-tv">
    <div class="widget-online-tv-title">
        <?php echo $title;?>
    </div>
    <?php foreach($arr_all_online_tv as $online_tv):
            $v_xml = $online_tv['C_XML_DATA'];
            $dom = simplexml_load_string($v_xml);
            //lay url logo
            $xpath = "//item[@id='txt_online_tv_logo']/value";
            $r = $dom->xpath($xpath);
            $v_url_logo =(string) $r[0];
            
            //lay url online tv
            $xpath = "//item[@id='txt_online_tv_url']/value";
            $r = $dom->xpath($xpath);
            $v_url_online_tv =(string) $r[0];
    ?>
    
    <div class="widget-online-tv-logo">
        <a href="javascript:void(0);" onClick="VPPlay('<?php echo $v_url_online_tv;?>','<?php echo $chk_radio?>');">
        <img src="<?php echo $v_url_logo?>" />
        </a>
    </div>
    <?php endforeach;?>
</div>