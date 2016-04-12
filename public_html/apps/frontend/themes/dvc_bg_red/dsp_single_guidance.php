<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
//du lieu header
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$VIEW_DATA['arr_css']               = array('component','guidance','single-page');
$VIEW_DATA['arr_script']            = array();

$arr_single_guidance = isset($arr_single_guidance) ? $arr_single_guidance : array();
$v_id_linh_vuc       = isset($arr_single_guidance['PK_LIST']) ? $arr_single_guidance['PK_LIST'] : '';   
$v_name_linh_vuc     = isset($arr_single_guidance['C_NAME_THU_TUC']) ? $arr_single_guidance['C_NAME_THU_TUC'] : '';  
$v_name              = isset($arr_single_guidance['C_NAME']) ? $arr_single_guidance['C_NAME'] : '';
$v_xml               = isset($arr_single_guidance['C_XML_DATA']) ? $arr_single_guidance['C_XML_DATA'] : '';
$v_record_type_id    = isset($arr_single_guidance['PK_RECORD_TYPE']) ?  $arr_single_guidance['PK_RECORD_TYPE'] : 0;
$v_record_type_code  = isset($arr_single_guidance['C_CODE']) ? $arr_single_guidance['C_CODE'] : '';

?>
<?php $this->render('dsp_header', $VIEW_DATA, $this->theme_code); ?>
<!--Start #main-->
<div class="clear"></div>
<div  class="group-option col-md-12" id="page-single-guidance"> 
    <div class="clear" style="height: 10px"></div>
      <div class="col-md-12">
          <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                    <label class="home">
                        <a href="<?php echo SITE_ROOT; ?>">
                            <img src="<?php echo CONST_SITE_THEME_ROOT ?>/images/home-page.png">
                        </a>
                    </label>    
                    <label >
                        <a href="<?php echo build_url_guidance(true); ?>"><?php echo __('list record type'); ?></a>
                    </label>    
                    <label class="active"><?php echo __('detailed guidance on procedures') ?></label>    
                </div>
                <div class="title-border-right"></div>
            </div>
      
    <!--Start .row-fulid-->
    <div class="row-fluid">
        <div class="single-item">
            <div class="row-title title">
                <h2 class=" list-type-name"><?php echo $v_name_linh_vuc; ?></h2>
                    <h3 class=" record-type-name" >
                        <span style="font-size: 1em;color: brown"><?php echo  __('procedures');?>:&nbsp;</span><?php echo $v_name;?>
                    </h3>
            </div>
            <?php
            $r = array();
            if( $v_xml != '')
            {
                
                $dom        = simplexml_load_string($v_xml,'SimpleXMLElement',LIBXML_NOCDATA);
                $i = 0;
                $html ='';
                // Trình tự thực hiện
                $x_path                       = "//item[@id='txta_trinh_tu_thuc_hien']/value";
                $r_txta_trinh_tu_thuc_hien        = $dom->xpath($x_path);
                $v_txta_trinh_tu_thuc_hien        = (string)(isset($r_txta_trinh_tu_thuc_hien[0]) ? $r_txta_trinh_tu_thuc_hien[0] : '');
                if( $v_txta_trinh_tu_thuc_hien != '' && $v_txta_trinh_tu_thuc_hien != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span class="stt">'.($i += 1).',&nbsp;</span>'.__('the order excuted').':</h3>';
                    $html .= '<div class="content">'.  html_entity_decode((string)$r_txta_trinh_tu_thuc_hien[0]).'</div>';
                }
                
                //Cánh thực hiện
                $x_path                       = "//item[@id='cach_thuc_thuc_hien']/value";
                $r_cach_thuc_thuc_hien        = $dom->xpath($x_path);
                $v_cach_thuc_thuc_hien        = (string)(isset($r_cach_thuc_thuc_hien[0]) ? $r_cach_thuc_thuc_hien[0] :'');
                if( $v_cach_thuc_thuc_hien != '' && $v_cach_thuc_thuc_hien != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span class="stt">'.($i += 1).',&nbsp;</span>'.__('how to perform').':</h3>';
                    $html .= '<div class="content">'.html_entity_decode((string)$r_cach_thuc_thuc_hien[0]).'</div>';
                }
                
                
                // Hồ sơ
                $x_path                       = "//item[@id='txta_ho_so']/value";
                $r_txta_ho_so           = $dom->xpath($x_path);
                $v_dtxta_ho_so          = (string)(isset($r_txta_ho_so[0])? $r_txta_ho_so[0] : '');
                if( $v_dtxta_ho_so      != '' && $v_dtxta_ho_so != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span class="stt">'.($i += 1).',&nbsp;</span>'. __('composition, the number of records').':</h3>';
                    $html .= '<div class="content">'.html_entity_decode((string)$r_txta_ho_so[0]).'</div>';
                }
                
                // Thời hạn giải quyết
                $x_path                       = "//item[@id='thoi_han_giai_quyet']/value";
                $r_thoi_han_giai_quyet        = $dom->xpath($x_path);
                $v_thoi_han_giai_quyet        = (string)(isset($r_thoi_han_giai_quyet[0])? $r_thoi_han_giai_quyet[0]: '');
                if( $v_thoi_han_giai_quyet != '' && $v_thoi_han_giai_quyet != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span class="stt">'.($i += 1).',&nbsp;</span>'.__('time limit for settlement').':</h3>';
                    $html .= '<div class="content">'.html_entity_decode((string)$r_thoi_han_giai_quyet[0]).'</div>';
                }
                
                // đối tượng thực hiện
                $x_path                       = "//item[@id='doi_tuong_thuc_hien']/value";
                $r_doi_tuong_thuc_hien        = $dom->xpath($x_path);
                $v_doi_tuong_thuc_hien        = (string)(isset($r_doi_tuong_thuc_hien[0]) ? $r_doi_tuong_thuc_hien[0] : '');
                if( $v_doi_tuong_thuc_hien != '' && $v_doi_tuong_thuc_hien != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span class="stt">'.($i += 1).',&nbsp;</span>'.__('subjects execute of administrative procedures').':</h3>';
                    $html .= '<div class="content">'.html_entity_decode((string)$r_doi_tuong_thuc_hien[0]).'</div>';
                }
                
                // Cơ quan thực hiện
                $x_path                       = "//item[@id='txta_co_quan_thuc_hien']/value";
                $r_txta_co_quan_thuc_hien       = $dom->xpath($x_path);
                $v_txta_co_quan_thuc_hien        = (string)(isset($r_txta_co_quan_thuc_hien[0]) ? $r_txta_co_quan_thuc_hien[0] : '');
                if( $v_txta_co_quan_thuc_hien != '' && $v_txta_co_quan_thuc_hien != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span class="stt">'.($i += 1).',&nbsp;</span>Cơ quan thực hiện TTHC:</h3>';
                    $html .= '<div class="content">'.html_entity_decode((string)$r_txta_co_quan_thuc_hien[0]).'</div>';
                }
                
                // Kết quả
                $x_path                       = "//item[@id='txta_ket_qua']/value";
                $r_txta_ket_qua        = $dom->xpath($x_path);
                $v_txta_ket_qua        = (string)(isset($r_txta_ket_qua[0]) ? $r_txta_ket_qua[0] :'');
                if( $v_txta_ket_qua != '' && $v_txta_ket_qua != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span class="stt">'.($i += 1).',&nbsp;</span>Kết quả thực hiện thủ tục hành chính:</h3>';
                    $html .= '<div class="content">'.html_entity_decode((string)$r_txta_ket_qua[0]).'</div>';
                }
                
                // Lệ phí
                $x_path                       = "//item[@id='le_phi']/value";
                $r_le_phi                     = $dom->xpath($x_path);
                $v_le_phi                     = (string)(isset($r_le_phi[0]) ? $r_le_phi[0] :'');
                if( $v_le_phi != '' && $v_le_phi != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span class="stt">'.($i += 1).',&nbsp;</span>Phí, lệ phí nếu có:</h3>';
                    $html .= '<div class="content">'.html_entity_decode((string)$r_le_phi[0]).'</div>';
                }
                
                // Căn cứ pháp lý
                $x_path                       = "//item[@id='txta_can_cu_phap_ly']/value";
                $r_txta_can_cu_phap_ly        = $dom->xpath($x_path);
                $v_txta_can_cu_phap_ly        = (string)(isset($r_txta_can_cu_phap_ly[0]) ? $r_txta_can_cu_phap_ly[0]  :'');
                if( $v_txta_can_cu_phap_ly != '' && $v_txta_can_cu_phap_ly != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span class="stt">'.($i += 1).',&nbsp;</span>'.__('legal basis').'</h3>';
                    $html .= '<div class="content">'.html_entity_decode((string)$r_txta_can_cu_phap_ly[0]).'</div>';
                }
                if($i == 0)
                {
                    $html .= '<p style="font-size:1.6em; text-align:center">'.__('no upload content').'</p>'; 
                }
            }
              echo $html;
            ?>
            <?php if(trim($v_xml) != ''):; ?>
            <?php
                // Căn cứ pháp lý
                $x_path          = "//media/file/text()";
                $arr_file        = $dom->xpath($x_path);
            ?>
            <?php if($arr_file):?>
            <div class="col-md-12 file">
                <div class="title-file">
                    <?php echo __('attachments');?>
                </div>
                <div class="list-file">
                         <?php  foreach ($arr_file as $item): ?>
                            <?php
                                $item = (string)$item ;

                                if(trim($item) != '' && $item != NULL)
                                {   
                                    $v_path_file = CONST_TYPE_FILE_UPLOAD . 'template_files_types' .DS . $item;

                                    if(is_file($v_path_file))
                                    {
                                        $arr_string     = explode('_', $item,2);
                                        $v_name         =  isset($arr_string[1]) ? $arr_string[1] : '';
                                        $key_file       = isset($arr_string[0]) ? $arr_string[0] : '';
                                        $v_file_name    =  $item;
                                        $v_file_path    =  $v_path_file;
                                        $v_file_type    = filetype($v_path_file);
                                        
                                        $arr_all_icon_file  = json_decode(CONTS_ICON_FILE_GUIDANCE, TRUE);
                                        $v_url_icon = CONST_SITE_THEME_ROOT . 'images/icon-attach-default.png';
//                                        if(key_exists($v_file_type, $arr_all_icon_file))
//                                        {
//                                            $v_url_icon =  CONST_SITE_THEME_ROOT.'images/'.$arr_all_icon_file[$v_file_type];
//                                        }
                                        $v_url = $this->get_controller_url().'download?file_name='. md5($v_file_name) .'&record_code=' . $v_record_type_code . '&name='.$v_name;
                                        echo "<a target='_blank'  class='icon-file-dowload' style='padding:5px; padding-top: 3px;padding-bottom: 3px;display: block;float: left' title=' $v_name' href='$v_url'><img src='$v_url_icon' width='15px' height='auto' ></a>"; 
                                    }
                                }    
                            ?>
                            
                        <?php endforeach; ?>
            </div>
            </div>  
            <?php endif; ?>
            <?php endif; ?>
         
        <!--End .single-item-->
        <div class="controls-row" id="btn-register">
            <?php 
                $v_show_btn = get_request_var('show','');
                if($v_show_btn != 'false'):
            ?>
            <div class="controls">
                <button type="button" class="btn btn-info" onclick="window.history.back()" ><?php echo __('back')?></button>
            </div>
            <?php endif; ?>
        </div>
    </div>
       
    </div>
    </div>
</div>
<!--End #page-single-guidance -->

<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);