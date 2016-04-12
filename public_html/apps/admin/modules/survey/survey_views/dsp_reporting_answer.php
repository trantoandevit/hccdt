<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
$this->template->title = 'Quản lý câu hỏi khảo sát chất lượng';
$this->template->display('dsp_header.php');

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
    }
    .title-general-infor
    {
        margin-bottom: 5px;
        height: auto !important;
    }
</style>
<!-- Toolbar -->
<h2 class="module_title" style="width: auto">Kết quả đánh giá câu hỏi khảo sát</h2>
<h3>&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $v_survey_name;?></h3>
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
            <span class="title-general-infor"><?php echo 'Câu hỏi '. ($i + 1) .':  '.$v_answer_name; ?> </span>
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
                            <table style="width: 100%;">
                                <tr>
                                    <td style="text-align: right; ">20%</td>
                                    <td style="text-align: right; ">40%</td>
                                    <td style="text-align: right ">60%</td>
                                    <td style="text-align: right ">80%</td>
                                    <td style="text-align: right ">100%</td>
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
                        <span class="title-general-infor"><?php echo 'Câu hỏi '. ($i + 1) .':  '.$v_answer_name; ?> </span>
                    </div>
                    <!--End title-->
                    <div classs="box-results">
                        <table width="100%" class="adminlist" cellspacing="0" border="1">
                            <colgroup>
                                <col style="width: 10%"/>
                                <col style="width: 90%;"/>
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
    <div class="btn" style="text-align: center">
        <button type="button" class="ButtonBack" onclick="history.back()">Quay lại</button>
        <button type="button" class="ButtonPrint" onclick="dsp_print_report_answer('<?php echo $v_current_survey_id;?>');">In</button>
    </div>
</div>
<div class="clear" style="height: 20px;"></div>
<script type="text/javascript">
    function dsp_print_report_answer(current_survey_id){
         if(current_survey_id != '' && current_survey_id != null )
            {
               var url = '<?php echo $this->get_controller_url().'dsp_print_report_answer/';?>' + current_survey_id; 
               showPopWin(url, 800, 600);
            }
    }
    
</script>
<?php
$this->template->display('dsp_footer.php');

                    
                    
                            