
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
                width:12em; /* fixed width only please - you can use the "subMenusMinWidth"/"subMenusMaxWidth" script options to override this if you like */
            }
        </style>
        <ul id="main-menu" class="sm sm-blue" >
            <li class="<?php echo ($this->menu_active == 'dsp_all_scope' OR $this->menu_active == 'dsp_all_staff' OR $this->menu_active == 'dsp_single_staff' OR $this->menu_active == 'dsp_single_village' OR $this->menu_active == 'dsp_all_village') ? 'active' : ''; ?>" >
                <a  href="<?php echo build_url_evaluation() ?>" >
                    <?php echo __('Đánh giá cán bộ'); ?>
                </a>
            </li>
            <li  class="<?php echo ($this->menu_active == 'dsp_evaluation_results') ? 'active' : ''; ?>" >
                <a href="<?php echo build_url_evaluation( FALSE, true) ?>">
                    Kết quả đánh giá
                </a>
            </li>
            <li class="  <?php echo ($this->menu_active == 'dsp_assessment_guidelines') ? 'active' : ''; ?>">
                <a href="<?php echo build_url_evaluation(true) ?>" >
                    <?php echo __('guidance') ?>
                </a>
            </li>
    <!--  <li>
                <a href="#" class="has-submenu">
                    <span class="sub-arrow">+</span>Support</a>
                <ul class="sm-nowrap" >
                    <li>
                        <a href="#">Premium support</a>
                        <li>
                            <a href="#" class="has-submenu"><span class="sub-arrow">+</span>2</a>
                            <ul class="sm-nowrap" >
                                <li>
                                    <a href="#">1</a>
                                </li>
                            </ul>
                        </li>
                    </li>
                </ul>
            </li>-->
        </ul>
    </div>
</div>
<!--End menu_menu_top_two-->
