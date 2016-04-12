<?php
defined('DS') or die('no direct access');
$v_keywords          = get_request_var('keywords');
$category_id         = (int) get_request_var('category');
$v_search_begin_date = get_request_var('begin_date');
$v_end_date          = get_request_var('end_date');

$VIEW_DATA['arr_css']               = array('synthesis');
$VIEW_DATA['arr_script']        = array('jquery.min.1.5');

$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

$v_total_record = isset($arr_all_article[0]['TOTAL_RECORD']) ? $arr_all_article[0]['TOTAL_RECORD'] : 0;
?>
<div class="col-md-12 content" style="margin-top: 10px"> 
    <div class="col-md-3 block">
        <?php $n = isset($arr_all_widget_position['widget_left']) ? count($arr_all_widget_position['widget_left']) : 0; ?>
        <?php for ($i = 0; $i < $n; $i++): ?>
            <?php echo $arr_all_widget_position['widget_left'][$i]['C_CONTENT'] ?>
        <?php endfor; ?>
    </div>
    <div class="col-md-9 block">
            <div class="div-synthesis" style="margin-top: 0px">
                <div class="div_title">
                    <div class="title-border-left"></div>
                    <div class="title-content">
                       <label>
                        <?php echo __('search')?>  
                    </label>
                    </div>
                </div>
               
                <form class="form-horizontal" action="" style="margin: 5px;">
                    <div class="form-group">
                        <label for="category" class="col-md-2 control-label"><?php echo __('category') ?></label>
                        <div class="col-md-8">
                            <select name="category" id="category" class="form-control">
                                <option value="0"></option>
                                <?php $n = count($arr_all_category) ?>
                                <?php for ($i = 0; $i < $n; $i++): ?>
                                    <?php
                                    $item = $arr_all_category[$i];
                                    $v_name = $item['C_NAME'];
                                    $v_lvl = strlen($item['C_INTERNAL_ORDER']) / 3 - 1;
                                    $v_indent = '';
                                    $v_id = $item['PK_CATEGORY'];
                                    for ($j = 0; $j < $v_lvl; $j++) {
                                        $v_indent .= ' -- ';
                                    }
                                    ?>
                                    <option value="<?php echo $v_id ?>"><?php echo $v_indent . $v_name ?></option>
                                <?php endfor; ?>
                            </select>
                            <script>$('#category').val(<?php echo $category_id ?>);</script>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo __('begin date') ?></label>
                        <div class="col-md-7">
                            <div class="col-md-8" style="padding-left: 0px;">
                                <input class="form-control"
                                    type="text" name="begin_date" id="begin_date" 
                                    onFocus="DoCal('begin_date')" value="<?php echo $v_search_begin_date ?>"
                                />
                            </div>
                            <img 
                                height="32" width="32" onclick="DoCal('begin_date')"
                                src="<?php echo CONST_SITE_THEME_ROOT . 'images/calendar.gif' ?>"
                                />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo __('end date') ?></label>
                        <div class="col-md-7">
                            <div class="col-md-8" style="padding-left: 0px;">
                                <input class="form-control"
                                    type="text" name="end_date" id="end_date" 
                                    onFocus="DoCal('end_date')"  value="<?php echo $v_end_date ?>"
                                />
                            </div>
                            <img 
                                height="32" width="32" onclick="DoCal('end_date')"
                                src="<?php echo CONST_SITE_THEME_ROOT . 'images/calendar.gif'?>"
                                />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label" for="keywords"><?php echo __('keywords') ?></label>
                        <div class="col-md-8">
                            <input class="form-control" type="text" id="keywords" name="keywords" value="<?php echo $v_keywords ?>" style="width:450px"/>
                        </div>
                    </div>
                    <div class="form-actions" style="text-align: right">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <span class="glyphicon glyphicon-search "></span>
                            <?php echo __('search') ?>
                        </button>
                    </div>
                </form>
            </div><!--div search-->
            <div class="clear" style="height: 10px"></div>
            <div class="col-md-12">  <div class="div_title">
                    <div class="title-border-left"></div>
                    <div class="title-content">
                        <label>
                              <?php echo __('search result') ?>
                                    <?php echo __('found') ?>&nbsp;
                                   ( <?php echo $v_total_record ?>)
                                    &nbsp;<?php echo __('article') ?>
                        </label>
                    </div>
                </div>
            <div class="clear"></div>
            <!--article search-->
            <div class='col-md-12 block' style="min-height: 300px">
                <?php $n = count($arr_all_article) ?>
                <?php for ($i = 0; $i < $n; $i++): ?>
                    <?php
                    $item = $arr_all_article[$i];
                    $v_id = $item['PK_ARTICLE'];
                    $v_cat_id = $item['PK_CATEGORY'];
                    $v_title = $item['C_TITLE'];
                    $v_slug = $item['C_SLUG'];
                    $v_cat_slug = $item['C_CAT_SLUG'];

                    $v_summary = $item['C_SUMMARY'];
                    $v_summary = htmlspecialchars_decode($v_summary);
                    $v_summary = remove_html_tag($v_summary);

                    $v_file_name = $item['C_FILE_NAME'];

                    $v_url = build_url_article($v_cat_slug, $v_slug, $this->website_id, $v_cat_id, $v_id);

                    $v_begin_date = $item['C_BEGIN_DATE'];
                    $v_begin_date = date_create_from_format('Y-m-d H:i:s', $v_begin_date)->format('d/m/Y');

                    $img_new_url = CONST_SITE_THEME_ROOT . 'images/new.png';
                    $ck_new_article = $item['CK_NEW_ARTICLE'];
                    $new_article_mode = get_system_config_value(CFGKEY_NEW_ARTICLE_ICON);
                    ?>
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <?php if ($v_file_name != ''): ?>
                                <a href="<?php echo $v_url ?>">
                                    <img class="img-thumbnail" src="<?php echo SITE_ROOT . "upload/" . $v_file_name ?>">
                                </a>
                            <?php else: ?>
                                <a href="<?php echo $v_url ?>">
                                    <img class="img-thumbnail" src="<?php echo CONST_SITE_THEME_ROOT . "images/default-sticky.png"; ?>" width="50px" height="33px">
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <a href="<?php echo $v_url ?>" class='article-title'>
                                <?php echo $v_title; ?>
                                <?php if ($ck_new_article <= 0 && $new_article_mode == 'true'): ?>
                                    <img height="9" src="<?php echo $img_new_url ?>"/>
                                <?php endif; ?>
                            </a>
                            <div class="article-summary" >
                                <?php echo get_leftmost_words($v_summary, 40); ?> 
                            </div>
                        </div>
                    </div>
                    <div class='clear' style='height: 10px'></div>
                <?php endfor; ?>
            </div>
            <!--button filter paging-->
            <div class="div_pagination" align="right">
                <?php
                $v_url = build_url_search($this->website_id, $v_keywords, $category_id, $v_search_begin_date, $v_end_date);
                $v_total_record = isset($arr_all_article[0]['TOTAL_RECORD']) ? $arr_all_article[0]['TOTAL_RECORD'] : 0;
                $n = ceil($v_total_record / _CONST_DEFAULT_ROWS_PER_PAGE);
                if (!$n) {
                    $n = 1;
                }
                $filter_page_no = get_request_var('page', 1);
                $first_page = 1;
                $previous_page = $filter_page_no <= 1 ? 1 : $filter_page_no - 1;
                $next_page = $filter_page_no == $n ? $n : $filter_page_no + 1;
                $last_page = $n;
                $i = $filter_page_no <= 2 ? 1 : $filter_page_no - 1;
                ?>
                <?php if ($n > 1): ?>
                    <ul class="pagination">
                        <li data-val="1">
                            <a href="<?php echo $v_url . "&page=$first_page"; ?>" title="<?php echo __("first page") ?>">
                                <?php echo __("first page") ?>
                            </a>
                        </li>
                            <?php
                            for ($i, $j = 1; $i <= $n && $j <= 9; $i++, $j++):
                                if ($i == 1 or $i == $n) {
                                    continue;
                                }
                                ?>
                                                    <li data-val="<?php echo $i; ?>">
                                                        <a href="<?php echo $v_url . "&page=$i" ?>">
                                                            <strong><?php echo $i; ?></strong>
                                                        </a>
                                                    </li>
                            <?php endfor; ?>
                        <li data-val="<?php echo $n; ?>">
                            <a href="<?php echo $v_url . "&page=$last_page"; ?>" title="<?php echo __("last page") ?>">
                                <?php echo __("last page") ?>
                            </a>
                        </li>
                    </ul>
                <?php endif; //n > 1 ?>
            </div></div>
    </div>
</div>

<script>
    $page = <?php echo (int) get_request_var('page', 1); ?>;
    if($page == 0)
    {
        $page = 1;
    }
    
    
    $('.pagination li[data-val='+$page+']').addClass('active');
</script>


<?php $this->render('dsp_footer', $VIEW_DATA, $this->theme_code); ?>