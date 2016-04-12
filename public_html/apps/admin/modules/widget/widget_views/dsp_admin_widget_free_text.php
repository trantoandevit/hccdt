<?php
defined('DS') or die('no direct access');
$disabled = $content_only ? 'disabled' : '';
?>
<label>
    <?php echo __('content only') ?>
    <input 
        type="checkbox" name="chk_content_only" <?php echo $content_only ?> 
        value="1" onclick="chk_widget_freetext_onclick(this)"
        />
</label>

<?php if ($arr_all_widget_class = Session::get('arr_all_widget_class')): ?>
    <label>
        <?php echo __('color style') ?></br>
        <select name="sel_widget_style" style="width:100%;" <?php echo $disabled ?>>
            <?php echo View::generate_select_option($arr_all_widget_class, $widget_style) ?>
        </select>
    </label>
<?php endif; ?>

<label>
    <?php echo __('title') ?></br>
    <input type="text" name="txt_free_text_title" style="width: 100%;" value="<?php echo $title; ?>" <?php echo $disabled ?>/>
</label>
<label onClick="free_text_show_svc_onclick(this);">
    <?php echo __('content') ?>&nbsp;
    <img 
        class="free_text_img" height="16" width="16" 
        src="<?php echo SITE_ROOT ?>public/images/AddButton.gif"
        />
</label>
<textarea 
    style="width:100%" name="txt_free_text_content" 
    class="txt_free_text_content" rows="10"
    ><?php echo $content; ?></textarea>
<script src="<?php echo SITE_ROOT; ?>public/tinymce/script/tiny_mce.js"></script>
<script>
    
    SITE_ROOT = "<?php echo SITE_ROOT ?>";
    function free_text_show_svc_onclick(label_obj)
    {
        if($('#free_text_dialog').length == 0)
        {
            tinyMCE_init();
            $div = '  <div id="free_text_dialog" title="Widget" style="display: none;">'
                + '<?php echo __('content') ?></br>'
                + '<textarea name="free_text_dialog_content" id="free_text_dialog_content" style="width:100%;height: 80%"></textarea>'
                + '</div>';
            $('body').append($div);
            
        }
        $txt_free_text_content = $(label_obj).parents('form:first').find('.txt_free_text_content');
        $current_val = $($txt_free_text_content).val();
        $('#free_text_dialog_content').val($current_val);
        console.log($('#free_text_dialog_content'));
        tinyMCE.execCommand('mceAddControl', false, 'free_text_dialog_content');

        $('#free_text_dialog').dialog({
            modal: true,
            height: 600,
            width: 700,
            buttons: {
                "<?php echo __('update') ?>": function() {
                    tinyMCE.triggerSave();
                    $($txt_free_text_content).val($('#free_text_dialog_content').val());
                    $( this ).dialog( "close" );
                },
                "<?php echo __('cancel') ?>": function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function(){
                tinyMCE.execCommand('mceRemoveControl', false, 'free_text_dialog_content');
            }
        });
    }
    if(!window.tinyMCE_init)
    {
        window.tinyMCE_init = function(){
            tinyMCE.init({
                //custom
                document_base_url : SITE_ROOT,
                relative_urls : false, 
                extended_valid_elements : "a[class|name|href|target|title|onclick|rel],script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],$elements",
                entity_encoding : "raw",
                entities: "",
                force_br_newlines : true,              //btw, I still get <p> tags if this is false
                remove_trailing_nbsp : false,    
                
                // General options
                mode : "none",
                width:'668',
                theme_advanced_resizing_max_width : 668,
                plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks,govideo,youtubeIframe",
                // Theme options
                theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,forecolor,backcolor,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,justifyfull,search,replace,|,outdent,indent,blockquote,code,|,undo,redo,|,link,unlink,image,govideo,youtubeIframe",
                theme_advanced_buttons2 : "",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                theme_advanced_resizing : true,

                // Example content CSS (should be your site CSS)
                content_css : "public/tinymce/css/content.css",

                // Drop lists for link/image/media/template dialogs
                template_external_list_url : "public/tinymce/lists/template_list.js",
                external_link_list_url : "public/tinymce/lists/link_list.js",
                external_image_list_url : "public/tinymce/lists/image_list.js",
                media_external_list_url : "public/tinymce/lists/media_list.js",

                // Skin options
                skin : "o2k7",
                skin_variant : "silver",
        
                // Style formats
                style_formats : [
                    {
                        title : 'Bold text', 
                        inline : 'b'
                    },

                    {
                        title : 'Red text', 
                        inline : 'span', 
                        styles : {
                            color : '#ff0000'
                        }
                    },
                    {
                        title : 'Red header', 
                        block : 'h1', 
                        styles : {
                            color : '#ff0000'
                        }
                    },
                    {
                        title : 'Example 1', 
                        inline : 'span', 
                        classes : 'example1'
                    },
                    {
                        title : 'Example 2', 
                        inline : 'span', 
                        classes : 'example2'
                    },
                    {
                        title : 'Table styles'
                    },
                    {
                        title : 'Table row 1', 
                        selector : 'tr', 
                        classes : 'tablerow1'
                    }
                ],

                // Replace values for the template plugin
                template_replace_values : {
                    username : "Some User",
                    staffid : "991234"
                }
            }); 
        }
    }
    function chk_widget_freetext_onclick(chk_obj){
        var f = $(chk_obj).parents('form:first');
        if($(chk_obj).attr('checked'))
        {
            
            $(f).find('[name=sel_widget_style]').attr('disabled', '1');
            $(f).find('[name=txt_free_text_title]').attr('disabled', '1');
        }
        else{
            $(f).find('[name=sel_widget_style]').removeAttr('disabled');
            $(f).find('[name=txt_free_text_title]').removeAttr('disabled');
        }
     
    }
</script>