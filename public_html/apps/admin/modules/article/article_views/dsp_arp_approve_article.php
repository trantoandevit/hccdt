<?php foreach ($arr_all_article as $arr_article):
            $v_article_id    = $arr_article['PK_ARTICLE'];
            $v_title         = $arr_article['C_TITLE'];
            $v_begin_date    = $arr_article['C_BEGIN_DATE'];
            $v_website_name  = $arr_article['C_NAME'];
            $v_category_name = $arr_article['C_DEFAULT_CATEGORY_NAME'];
            $v_file_name     = $arr_article['C_FILE_NAME'];
            $v_author        = ($arr_article['C_AUTHOR'] == NULL || $arr_article['C_AUTHOR'] == '')?'Chưa có tác giả':$arr_article['C_AUTHOR'];
    ?>
    <!--article item-->
        <div class="div-table mobile-article-item ">
            <div class="div-table-cell" style="width:80px">
                <?php if ($v_file_name != ''): ?>
                    <a  href="javascript:void(0)" onClick="row_onclick(<?php echo $v_article_id ?>)">
                        <img src="<?php echo SITE_ROOT . "upload/" . $v_file_name ?>" class="mobile-article-img"/>
                    </a>
                <?php else: ?>
                    <a href="javascript:void(0)" onClick="row_onclick(<?php echo $v_article_id ?>)">
                        <img src="" class="mobile-article-img" />
                    </a>
                <?php endif; ?>
                
            </div>
            <div class="div-table-cell">
                <div class="Row">
                    
                    <a class="mobile-article-title" href="javascript:void(0)" onClick="row_onclick(<?php echo $v_article_id ?>)"><?php echo $v_title;?></a>
                </div>
                <div class="Row">
                    <div class="left-Col">
                        Thuộc chuyên trang: 
                    </div>
                    <div class="right-Col">
                        <?php echo $v_website_name;?>
                        &nbsp;&nbsp;-&nbsp;&nbsp;
                        <b>Chuyên mục: </b>
                        <?php echo $v_category_name;?>
                    </div>
                </div>
                <div class="Row">
                    <div class="left-Col">
                        Tác giả: 
                    </div>
                    <div class="right-Col">
                        <?php echo $v_author;?>
                    </div>
                </div>
                <div class="Row">
                    <div class="left-Col">
                        Ngày nhập:    
                    </div>
                    <div class="right-Col">
                        <?php echo $v_begin_date;?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>