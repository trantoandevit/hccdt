<?php
$v_website_id    = get_request_var('website_id',0);
$arr_single_category = $arr_single_category['arr_single_category'];
$v_category_id   = isset($arr_single_category['PK_CATEGORY'])?$arr_single_category['PK_CATEGORY']:'';
$v_category_slug = isset($arr_single_category['C_SLUG'])?$arr_single_category['C_SLUG']:'';
$v_category_name = isset($arr_single_category['C_NAME'])?$arr_single_category['C_NAME']:'';
$v_xml_article   = isset($arr_single_category['C_XML_ARTICLE'])?$arr_single_category['C_XML_ARTICLE']:'';
?>
<?php if($v_xml_article !=''):?>
    <?php 
        $xml = '';
        $dom = simplexml_load_string($v_xml_article);
        $x_path = "//row";
        $r = $dom->xpath($x_path);
    ?>
    <?php 
    $xml .= '<?xml version="1.0" encoding="utf-8"?>';
    $xml .= '<rss version="2.0">';
    $xml .= '<channel>';
    $xml .= '<title><![CDATA['.$v_category_name.' - 192.168.1.53/go-paper]]></title>';
    $xml .= '<description><![CDATA['.$v_category_name.' - go-paper - Tờ báo điện tử thành phố Lạng Sơn]]></description>';
    $xml .= '<link><![CDATA['.  build_url_category($v_category_slug, $v_website_id, $v_category_id).']]></link>';
    $xml .= '<copyright><![CDATA[go-paper]]></copyright>';
    $xml .= '<generator><![CDATA[go-paper:go-paper/RSS]]></generator>';
    $xml .= '<pubDate>'.date('r').'</pubDate>';
    $xml .= '<lastBuildDate>'.date('r').'</lastBuildDate>';
    
    ?>
    <?php foreach ($r as $row_article):
            $v_file_name    = isset($row_article->attributes()->C_FILE_NAME)?$row_article->attributes()->C_FILE_NAME:'';
            $v_article_id   = $row_article->attributes()->PK_ARTICLE;
            $v_article_slug = $row_article->attributes()->C_SLUG;
            $v_begin_date   = $row_article->attributes()->C_BEGIN_DATE_YYYYMMDD;
            $v_title        = $row_article->attributes()->C_TITLE;
            $v_summary      = $row_article->attributes()->C_SUMMARY;
            $v_content      = $row_article->attributes()->C_CONTENT;
            
           $xml .='<item>';
           $xml .='<title><![CDATA['.$v_title.']]></title>';
	   $xml .='<description><![CDATA[<a href="'.build_url_article($v_category_slug, $v_article_slug, $v_website_id, $v_category_id, $v_article_id).'"><img src="'.SITE_ROOT.'upload/'.$v_file_name.'"></a>'.$v_summary.']]></description>';
           $xml .='<link><![CDATA['. build_url_article($v_category_slug, $v_article_slug, $v_website_id, $v_category_id, $v_article_id).']]></link>';
           $xml .='<pubDate>'.date('r',  strtotime($v_begin_date)).'</pubDate>';
           $xml .='</item>';	
            
    ?>
    <?php endforeach;?>
    <?php 
        $xml .= "</channel>";
        $xml .= "</rss>";
    ?>
<?php 
    ob_end_clean();
    header('Content-type: text/xml');
 echo $xml;
?>
<?php endif;?>
