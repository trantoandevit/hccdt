<?php
Session::init();
?>
<?php
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
?>
<?php
$VIEW_DATA['arr_css'] = array('main','single-page', 'synthesis', 'component', 'breadcrumb','cadre_evaluation');
$VIEW_DATA['arr_script'] = array();
$this->render('dsp_header', $VIEW_DATA, $this->theme_code);
$arr_evaluation_results         = isset($arr_evaluation_results) ? $arr_evaluation_results : array();
$arr_evaluation_results_child   = isset($arr_evaluation_results_child) ? $arr_evaluation_results_child : array();
$arr_all_criteria               = isset($arr_all_criteria) ? $arr_all_criteria :array();
?>

<?php
    $file_path_menu_top_two = __DIR__ . DS . 'menu_top_two_evaluation.php';
    if (is_file($file_path_menu_top_two)) {
        require $file_path_menu_top_two;
    }
?>
<div class="col-md-12 content" id="single-page">
    <div class="col-md-12 " >
        <div class="col-md-12 " >
            <div class="div_title_bg-title-top"></div>
            <div class="div_title">
                    <div class="title-border-left"></div>
                    <div class="title-content">
                        <label>
                            Kết quả đánh giá công chức tiếp nhận và giải quyết hồ sơ
                        </label>
                    </div>
                    <div class="title-border-right"></div>
                </div>
            <div class="col-md-12 block " id="scopes" style="margin-top: -11px;">
                <div id="box-scopes">
                    <!--End .title-->
                    <table style="width: 100%" class="table table-bordered table_synthesis">
                        <colgroup>
                            <col style="width: 5%" />
                            <col style="width: 45%" />
                             <?php 
                                    foreach ($arr_all_criteria as $key => $val)
                                    {
                                       $v_list_name      = $val['C_NAME'];
                                       $v_list_code      = $val['C_CODE'];
                                       echo "<col style=\"width: ".  ceil(40/count($arr_all_criteria))."%\" />";
                                    }
                                ?>

                            <col style="width: 10%" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th><?php echo __('row');?></th>
                                <th><?php echo __('ou name');?></th>
                                 <?php 
                                    foreach ($arr_all_criteria as $key => $val)
                                    {
                                       $v_list_name      = $val['C_NAME'];
                                       $v_list_code      = $val['C_CODE'];
                                       echo "<th class='center' >$v_list_name</th>";
                                    }
                                   ?>
                                <th><?php echo __('total'); ?></th>
                            </tr>
                        </thead>

                    <?php 
                        if(sizeof($arr_evaluation_results)>0) :
                        $stt = 0;
                    ?>
                    <?php for($i= 0;$i<sizeof($arr_evaluation_results);$i ++):?>
                    <?php
                        $v_member_name          = $arr_evaluation_results[$i]['C_NAME'];
                        $v_member_id            = $arr_evaluation_results[$i]['PK_MEMBER'];
                        $stt ++;
                    ?>
                    <tr>
                        <td><?php echo $stt; ?></td>
                        <td><?php echo $v_member_name ; ?></td>
                         <?php 
                             foreach ($arr_all_criteria as $key => $val)
                             {
                                 $v_list_id = $val['PK_LIST'];

                                $v_vote     = $arr_evaluation_results[$i]['C_VOTE_'.$v_list_id];
                                $v_vote     = ($v_vote >0) ? $v_vote : 0;
                                echo "<td style='text-align:center' >$v_vote </td>";
                             }
                            $v_vote_total       = isset($arr_evaluation_results[$i]['C_TOTAL_VOTE']) ? $arr_evaluation_results[$i]['C_TOTAL_VOTE'] : 0;                        
                            echo "<td style='text-align:center' >$v_vote_total </td>";
                        ?>

                    </tr>
                        <?php for($j= 0;$j<sizeof($arr_evaluation_results_child);$j ++):?>
                        <?php
                            //check member child
                            $v_member_child_id    = $arr_evaluation_results_child[$j]['FK_MEMBER'];
                            if($v_member_id       == $v_member_child_id):
                            $v_member_name_child  = $arr_evaluation_results_child[$j]['C_NAME'];
                            $stt ++;
                        ?>
                        <tr>
                            <td><?php echo $stt; ?></td>
                            <td> -- <?php echo $v_member_name_child ; ?></td>
                             <?php 
                                 foreach ($arr_all_criteria as $key => $val)
                                 {
                                     $v_list_id = $val['PK_LIST'];

                                    $v_vote     = $arr_evaluation_results_child[$j]['C_VOTE_'.$v_list_id];
                                    $v_vote     = ($v_vote >0) ? $v_vote : 0;
                                    echo "<td style='text-align:center' >$v_vote </td>";
                                 }
                                $v_vote_total    = isset($arr_evaluation_results_child[$j]['C_TOTAL_VOTE']) ? $arr_evaluation_results_child[$j]['C_TOTAL_VOTE'] : 0;                        
                                echo "<td style='text-align:center' >$v_vote_total </td>";
                            ?>
                        </tr>
                        <?php
                            unset($arr_evaluation_results_child[$j]);
                            endif;
                        ?>
                        <?php endfor;?>
                    <?php endfor; ?>

                    <?php else:;?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>  
                    <?php endif;?>
                     <tr>
                         <td></td>
                         <td><b><?php echo __('rate'); ?></b></td>
                         <?php
                            $arr_evaluation_results_total = ($arr_evaluation_results[0]) ? $arr_evaluation_results[0] : array();
                                foreach ($arr_all_criteria as $key => $val)
                                {
                                    $v_list_id = $val['PK_LIST'];

                                   $v_vote     = $arr_evaluation_results_total['C_VOTE_TOTAL_'.$v_list_id];
                                   $v_vote     = ($v_vote >0) ? $v_vote : 0;

                                   $v_vote_total_all  = $arr_evaluation_results_total['C_VOTE_ALL'];
                                   $v_vote_total_all  = ($v_vote_total_all >0) ? $v_vote_total_all :0;
                                   $v_vote  = round((($v_vote/$v_vote_total_all) *100),2);

                                   echo "<td style='text-align:center' ><b>$v_vote &nbsp;%</b> </td>";
                                }

                                $v_vote_total_all  = $arr_evaluation_results_total['C_VOTE_ALL'];
                                $v_vote_total_all  = ($v_vote_total_all >0) ? $v_vote_total_all :0;
                                echo "<td style='text-align:center' ><b>$v_vote_total_all</b> </td>";
                         ?>
                    </tr>
                    </table>
                </div>
                <div class="col-md-12 block box-button">
                    <button class="btn btn-info" onclick="window.history.go(-1);"><?php echo __('back')?></button>
                </div>
            </div>
            <!--End #scopes-->
        </div><!--End #main-page-->
    <!--End #box-check-record-code-->
    </div>
        <div class="clear"></div>
        <!--End #Main-content-->
        
</div>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);