<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
//$this->template->title = 'Quản lý câu hỏi khảo sát chất lượng212';
//$this->template->display('dsp_header.php');

$v_survey_name = isset($arr_single_answer['C_NAME']) ? $arr_single_answer['C_NAME'] : '';
?>
<style>
    .progress.progress-info
    {
            float: left;
            width: 0;
            height: 100%;
            font-size: 12px;
            color: #ffffff;
            text-align: center;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);                   
            width: 100%
    }
    .progress.progress-info .color
    {
        margin: 5px 0;
        height: 20px;
        background: #0093a8;
        /*background: #000000;*/
    }
    .title-general-infor
    {
        margin-bottom: 5px;
        height: auto !important;
    }
    
    td.percent_td{
        text-align: center;
        border: 1px solid #ffffff !important;
        border-right: 1px solid #000 !important; 
    }
    
    td.percent_td_last{
        text-align: center;
        border: 1px solid #ffffff !important;
    }
@media print {
body {-webkit-print-color-adjust: exact;}
}
</style>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js"></script>
<link rel="stylesheet" href="<?php echo SITE_ROOT?>public/css/reset.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="<?php echo SITE_ROOT?>public/css/text.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="<?php echo SITE_ROOT?>public/css/printer.css" type="text/css" media="all"/>
<div class="print-button">
            <input type="button" value="In trang" onclick="window.print(); return false;">
            <input type="button" value="Đóng cửa sổ" onclick="window.parent.hidePopWin()">
</div>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="header">
    <tbody>
        <tr>
        <td align="center" class="unit_full_name">
    <u> <strong>UBND Thành Phố Bắc Giang</strong></u>
        </td>
        <td align="center">
            <span style="font-size: 12px">
                <strong>CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong>
            </span>
            <br>
            <strong>
                <u style="font-size: 10px">Độc lập - Tự do - Hạnh phúc</u>
            </strong>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="report-title">
            <span class="title-1">Kết quả đánh giá câu hỏi khảo sát</span><br>
            <span class="title-2"><?php echo $v_survey_name;?></span>
        </td>
    </tr>
</tbody></table>
<div class="full-width">
    <?php for($i=0;$i<count($arr_question_answer);$i++):?>
        <?php
            $v_answer_name   = $arr_question_answer[$i]['C_NAME'];
            $v_xml_answer    = $arr_question_answer[$i]['C_XML_ANSWER'];
            $v_xml_answer    = xml_remove_declaration($v_xml_answer); 
            $v_question_type = $arr_question_answer[$i]['C_TYPE'];
        ?>
    <?php
        @$dom = simplexml_load_string($v_xml_answer);
        if($dom && ($v_question_type == 1 or $v_question_type == 0))
        {
    ?>
    <div class="item" style="margin-top: 20px;">
        <div claas="title">
            <span class="title-general-infor"><?php echo '<b>Câu hỏi '. ($i + 1) .':  </b>'.$v_answer_name; ?> </span>
        </div>
        <!--End title-->
        <div classs="box-results">
            <table width="100%" class="adminlist" cellspacing="0" border="1">
                <colgroup>
                    <col style="width: 30%"/>
                    <col style="width: 10%;"/>
                    <col style="width: 10%"/>
                    <col style="width: 50%"/>    
                </colgroup>
                <thead>
                    <tr>
                        <th>Câu trả lời</th>
                        <th>Kết quả</th>
                        <th>Tỷ lệ</th>
                        <th>
                            <table style="width: 100%;border:none;" >
                                <tr>
                                    <td class= "percent_td">20%</td>
                                    <td class= "percent_td" >40%</td>
                                    <td class= "percent_td" >60%</td>
                                    <td class= "percent_td" >80%</td>
                                    <td class= "percent_td_last" >100%</td>
                                </tr>
                            </table>
                        </th>
                    </tr>
                </thead>
                <?php
                     $v_xpath = '//row';
                    $results_answer = $dom->xpath($v_xpath);

                    $v_vote_total  = $results_answer[0]->attributes()->C_TOTAL_VOTE;
                    $v_vote_total  = ((int) $v_vote_total > 0 ) ? (int) $v_vote_total : 0;

                    for($o = 0;$o <count($results_answer);$o ++ )
                    {
                        $v_answer_name = $results_answer[$o]->attributes()->C_NAME;
                        $v_answer_vote = ((int)$results_answer[$o] >0 ) ? (int)$results_answer[$o] : 0;
                        if($v_vote_total > 0) 
                        {
                            $v_width = round($v_answer_vote/$v_vote_total,2) * 100;
                        }
                        else
                        {
                            $v_width = 0;
                        }
                ?>
                <tr>
                    <td><?php echo $v_answer_name; ?></td>
                    <td style="text-align: center" ><?php echo $v_answer_vote ?></td>
                    <td style="text-align: center"><?php echo $v_width ?>%</td>
                    <td style="padding-left: 0;" title="<?php echo $v_width; ?> %">
                        <div class="progress progress-info">
                            <div class="color" style="width:<?php echo$v_width.'%'; ?>" ></div>
                        </div>                       
                    </td>
                </tr>
                <?php
                    }
                ?>
            </table>
        </div>
        <!--End .box-results--> 
    </div>
    <?php
        }
        else
        {
    ?>
                <div class="item" style="margin-top: 20px;">
                    <div claas="title">
                        <span class="title-general-infor"><?php echo '<b>Câu hỏi '. ($i + 1) .': </b> '.$v_answer_name; ?> </span>
                    </div>
                    <!--End title-->
                    <div classs="box-results">
                        <table width="100%" class="adminlist" cellspacing="0" border="1">
                            <colgroup>
                                <col style="width: 15%"/>
                                <col style="width: 85%;"/>
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Ngày trả lời</th>
                                    <th>Trả lời</th>
                                </tr>

                            </thead>
                               <?php
                               if($dom)
                               {
                                   $v_xpath = '//row/root/item';
                                    $results_answer     = $dom->xpath($v_xpath);
                                    for($o = 0;$o <count($results_answer);$o ++)
                                    {
                                         $v_answer_date      = $results_answer[$o]->attributes()->date;
                                         $v_answer_message   = (string)$results_answer[$o];
                                    ?>
                                    <tr>
                                        <td><?php echo $v_answer_date; ?></td>
                                        <td style="text-align: left"><?php echo $v_answer_message ;;
                                        ?></td>
                                    </tr>
                                 <?php 
                                    }
                                }
                                else 
                                {
                                    ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td style="text-align: center">&nbsp;</td>
                                    </tr>
                            <?php
                                }
                            ?>
                        </table>
                    </div>
                    <!--End .box-results--> 
                </div>
                 <?php    
        }
        ?>
    <?php endfor;?>
   
</div>
<div class="clear" style="height: 20px;"></div>


                    
                    
                            