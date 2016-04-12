
<div class="col-md-12 content" id="menu-top-two">
    <div class="col-md-12 block" >                    
        <script type="text/javascript">
            $(function() {
                $('#main-menu').smartmenus({
                    subMenusSubOffsetX: 1,
                    subMenusSubOffsetY: -8
                });
            });
        </script>
        <style type="text/css">
            #main-menu {
                position:relative;
                z-index:9999;
                width:auto;
            }
            #main-menu ul {
                width:10em; /* fixed width only please - you can use the "subMenusMinWidth"/"subMenusMaxWidth" script options to override this if you like */
                
            }
        </style>
        <ul id="main-menu" class="sm sm-blue" >
            <li class="<?php echo ($v_url_type == 'member') ? 'active' : ''; ?>">
                <a href="<?php echo build_url_synthesis($this->website_id, 'member', 'tiep_nhan') ?>"  class="has-submenu">
                    <!--<span class="sub-arrow">+</span>-->
                    <?php echo __('summing by unit'); ?>
                </a>
<!--                <ul class="sm-nowrap">
                    <li>
                        <a class="<?php echo ($v_url_method == 'tiep_nhan' && $v_url_type == 'member') ? 'active' : ''; ?>" href="<?php echo build_url_synthesis($this->website_id, 'member', 'tiep_nhan') ?>"><?php echo __('profile reception') ?></a>
                    </li>
                    <li>
                        <a class="<?php echo ($v_url_method == 'thu_ly_va_giai_quyet' && $v_url_type == 'member') ? 'active' : ''; ?>" href="<?php echo build_url_synthesis($this->website_id, 'member', 'thu_ly_va_giai_quyet') ?>"><?php echo __('statistics status to resolve profile') ?></a>
                    </li>
                    <li>
                        <a class="<?php echo ($v_url_method == 'tu_choi' && $v_url_type == 'member') ? 'active' : ''; ?>" href="<?php echo build_url_synthesis($this->website_id, 'member', 'tu_choi') ?>"><?php echo __('statistical records denied') ?></a>
                    </li>
                    <li>
                        <a class="<?php echo ($v_url_method == 'bo_sung' && $v_url_type == 'member') ? 'active' : ''; ?>" href="<?php echo build_url_synthesis($this->website_id, 'member', 'bo_sung') ?>"><?php echo __('statistics records for supplements') ?></a>
                    </li>
                </ul>-->
            </li>
<!--            <li class="<?php echo ($v_url_type == 'spec') ? 'active' : ''; ?>">
                <a  class="has-submenu">
                    <span class="sub-arrow">+</span>
                    <?php echo __('summing records by field') ?>
                </a>
                <ul class="sm-nowrap">
                    <li>
                        <a class="<?php echo ($v_url_method == 'tiep_nhan' && $v_url_type == 'spec') ? 'active' : ''; ?>" href="<?php echo build_url_synthesis($this->website_id, 'spec', 'tiep_nhan') ?>"><?php echo __('profile reception') ?></a>
                    </li>
                    <li>
                        <a class="<?php echo ($v_url_method == 'thu_ly_va_giai_quyet' && $v_url_type == 'spec') ? 'active' : ''; ?>" href="<?php echo build_url_synthesis($this->website_id, 'spec', 'thu_ly_va_giai_quyet') ?>"><?php echo __('statistics status to resolve profile') ?></a>
                    </li>
                    <li>
                        <a class="<?php echo ($v_url_method == 'tu_choi' && $v_url_type == 'spec') ? 'active' : ''; ?>" href="<?php echo build_url_synthesis($this->website_id, 'spec', 'tu_choi') ?>"><?php echo __('statistical records denied') ?></a>
                    </li>
                    <li>
                        <a class="<?php echo ($v_url_method == 'bo_sung' && $v_url_type == 'spec') ? 'active' : ''; ?>" href="<?php echo build_url_synthesis($this->website_id, 'spec', 'bo_sung') ?>"><?php echo __('statistics records for supplements') ?></a>
                    </li>
                </ul>
            </li>-->
             <?php if(session::check_permission('THEO_DOI_TRUC_TUYEN',FALSE) == TRUE):?>
                <li class=" <?php echo ($v_url_type == 'liveboard') ? 'active' : ''; ?>">
                    <a href="<?php echo build_url_synthesis($this->website_id, 'liveboard')?>"><?php echo __('liveboard') ?></a>
                </li>
            <?php endif;?>
            <li class="<?php echo ($v_url_type == 'chart') ? 'active' : ''; ?>">
                <a  class="has-submenu">
                    <span class="sub-arrow">+</span>
                    <?php echo __('statistical graphs'); ?>
                </a>        
                <ul class="sm-nowrap">
                    <li>
                        <a href="<?php echo build_url_synthesis($this->website_id, 'chart', 'tien_do') ?>"><?php echo __('progress chart') ?></a>
                    </li>
                    <li>
                        <a href="<?php echo build_url_synthesis($this->website_id, 'chart', 'so_sanh') ?>"><?php echo __('comparison chart') ?></a>
                    </li>
                    <li>
                        <a target="_blank" href="<?php echo SITE_ROOT.'frontend/frontend/slideshow_data'?>"><?php echo __('Bảng tổng hợp toàn tình') ?></a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!--End menu_menu_top_two-->
