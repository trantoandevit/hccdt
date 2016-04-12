
<?php 
    $v_website_id = $this->website_id;
?>
<div class="widget-gallery widget widget_blue">
    <div class="widget-header"><h6>Thư viện ảnh</h6></div>
    <div class="widget-gallery-content">
        <?php foreach ($arr_all_photo_gallery as $row_gallery):
                $v_id = $row_gallery[0];
                $v_title = $row_gallery[1];
                $v_slug = $row_gallery[2];
                $v_file_name = $row_gallery[3];
                $v_summary = $row_gallery[4];
        ?>
        <div class="widget-gallery-row">
            <a href="<?php echo build_url_photo_gallery($v_website_id, $v_slug, $v_id)?>" >
                <img src="<?php echo SITE_ROOT . 'upload/' . $v_file_name?>"/>
                <div class="widget-gallery-title">
                    <?php echo $v_title?>
                </div>
            </a>
        </div>
        <?php endforeach;?>
    </div>
</div>