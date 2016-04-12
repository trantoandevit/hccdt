<?php
$this->template->display('dsp_header_pop_win.php');

$v_question_id       = get_request_var('question_id',0);
$v_question_name     = htmlspecialchars_decode(urldecode(get_request_var('question_name','')));
$v_question_type     = get_request_var('question_type',0);
$arr_answer          = isset($_REQUEST['question_answer']) ? htmlspecialchars_decode(urldecode($_REQUEST['question_answer'])) : '';
$v_status            = get_request_var('status','new');

?>
<form name="frmMain" id="frmMain">
        <h2 class="module_title"><?php echo __('survey detail');?></h2>
    <div id="box-answer">
        <?php
        echo $this->hidden('controller', $this->get_controller_url());
        echo $this->hidden('hdn_question_type', $v_question_type);
        echo $this->hidden('hdn_status', $v_status);
        echo $this->hidden('hdn_delete_answer_id', '');
        echo $this->hidden('hdn_item_id', $v_question_id);
        ?>
        <div class="Row cat-question">
            <div class="left-Col">Loại câu hỏi</div>
            <div class="right-Col">
                <select name="sel_question_type" id="sel_question_type" onchange="sel_cat_question_onchange(this)">
                    <option value="0" <?php echo ($v_question_type == 0) ? 'selected' : '' ?> >Câu hỏi dạng checkbox( Nhiều lựa chọn)</option>
                    <option value="1" <?php echo ($v_question_type == 1) ? 'selected' : '' ?> >Câu hỏi dạng raido(một lựa chọn)</option>
                    <option value="2" <?php echo ($v_question_type == 2) ? 'selected' : '' ?> >Câu hỏi dạng single text(Hiển thị dạng text box khi trả lời)</option>
                    <option value="3" <?php echo ($v_question_type == 3) ? 'selected' : '' ?> >Câu hỏi dạng text(Hiển thị dạng textArea khi trả lời)</option>
                </select>
            </div>
        </div>
        <div class="Row question">
            <div class="left-Col">Câu hỏi</div>
            <div class="right-Col">
                <input type="text"  name="txt_question_name" id="txt_question_name" 
                        value="<?php echo $v_question_name; ?>"
                        data-allownull="no" data-validate="text" 
                        data-name="<?php echo __('Câu hỏi')?>" 
                        data-xml="no" data-doc="no" 
                        autofocus="autofocus" 
                >
            </div>
        </div>
      
            <div id="box-results">
                    <?php if($v_question_type <2):?>
                     <div id="results">
                            <?php 
                            if(trim($arr_answer) != '')
                            {
                                $arr_answer = json_decode($arr_answer,true);
                            }  
                            if(sizeof($arr_answer) <=0)
                            {
                                echo '<div class="Row" data-id="">
                                <div class="left-Col">Câu trả lời</div>
                                <div class="right-Col answer">
                                    <input type="text"  name="txt_question_answer_name" id="txt_question_answer_name_" class="txt_question_answer_name" 
                                           value=""
                                            data-allownull="no" data-validate="text" 
                                            data-name="Câu trả lời" 
                                            data-xml="no" data-doc="no" 
                                            ofocus="autofocus"
                                           >
                                    <input type="hidden"  name="txt_question_answer_id"  value="" class="txt_question_answer_id" >
                                </div>
                            </div>';
                            }
                    ?>
                        <?php for($i = 0;$i <sizeof($arr_answer);$i ++): ?>
                        <?php 
                            $v_answer_name = isset($arr_answer[$i]['answer_name']) ? html_entity_decode($arr_answer[$i]['answer_name']) : '';
                            $v_answer_id   = isset($arr_answer[$i]['answer_id']) ? $arr_answer[$i]['answer_id'] : '';
                        ?>
                        <?php if($i ==0):?>
                            <div class="Row" data-id="">
                                <div class="left-Col">Câu trả lời</div>
                                <div class="right-Col answer">
                                    <input type="text"  name="txt_question_answer_name" id="txt_question_answer_name_" class="txt_question_answer_name" 
                                           value="<?php echo $v_answer_name; ?>"
                                            data-allownull="no" data-validate="text" 
                                            data-name="Câu trả lời" 
                                            data-xml="no" data-doc="no" 
                                            ofocus="autofocus"
                                           >
                                    <input type="hidden"  name="txt_question_answer_id"  value="<?php echo $v_answer_id;?>" class="txt_question_answer_id" >
                                    <button type="button" class="ButtonDelete" onclick="btn_delete_answer_onclick(this,'<?php echo $v_answer_id?>');">Xóa</button>
                                </div>
                            </div>
                            <?php else:?>
                                <div class="Row" data-id="">
                                    <div class="left-Col">&nbsp;</div>
                                    <div class="right-Col answer">
                                        <input type="text"  name="txt_question_answer_name" id="txt_question_answer_name_" class="txt_question_answer_name" 
                                               value="<?php echo $v_answer_name; ?>"
                                                data-allownull="no" data-validate="text" 
                                                data-name="Câu trả lời" 
                                                data-xml="no" data-doc="no" 
                                                ofocus="autofocus"
                                               >
                                        <input type="hidden"   name="txt_question_answer_id"  value="<?php echo $v_answer_id;?>" class="txt_question_answer_id" >
                                        <button type="button" class="ButtonDelete" onclick="btn_delete_answer_onclick(this,'<?php echo $v_answer_id?>');">Xóa</button>
                                    </div>
                            </div>
                            <?php endif;?>
                        <?php endfor; ?>
                     </div>
                        <!--End results-->
                       <div  class="Row btn-add">
                           <div class="left-Col">&nbsp;</div>
                           <div class="right-Col">
                               <button type="button" class="ButtonAdd" id="btn_add_question" name="btn_add_question" onclick="btn_onlcik_add_question();" >
                                   Thêm câu trả lời 
                               </button>
                           </div>
                       </div>
                    <?php endif;?>
            </div>
    </div>
        <div class="Row">
            <div class="left-Col">&nbsp;</div>
            <div class="right-Col">
                <div class="box-answer-btn">
                    <button type="button" class="ButtonAccept" onclick="btn_hidepopwin_onlick();">Cập nhật</button>
                    <button class="ButtonBack" type="button" onclick=" window.parent.hidePopWin(false);">Quay lại</button>
                </div>
            </div>
        </div>
        <br />
        <br />
        <br />
    <!--End #template-answer-->
</form>

<div id="box-template" style="display: none">
    <div id="template-box-results">
        <div id="template-results">
             <div class="Row">
                <div class="left-Col">Câu trả lời</div>
                <div class="right-Col answer">
                    <input type="text"  name="txt_question_answer_name"  data-count ="0" id="txt_question_answer_name_" 
                           class="txt_question_answer_name" 
                           data-allownull="no" data-validate="text" 
                            data-name="Câu trả lời" 
                            data-xml="no" data-doc="no" 
                            ofocus="autofocus"
                                    />
                    <input type="hidden"  name="txt_question_answer_id" class="txt_question_answer_id" >
                </div>
            </div>
         </div>
        <div id="btn_add">
            <div  class="Row btn-add">
                 <div class="left-Col">&nbsp;</div>
                 <div class="right-Col">
                     <button type="button" class="ButtonAdd" id="btn_add_question" name="btn_add_question" onclick="btn_onlcik_add_question();">
                         + Thêm câu trả lời 
                     </button>
                 </div>
            </div>
        </div>
    </div>
    <div id="two">
        <div class="Row">
            <div class="left-Col">&nbsp;</div>
            <div class="right-Col answer">
                <input type="text"  name="txt_question_answer_name" class="txt_question_answer_name" 
                                value=""
                                class="txt_question_answer_name" 
                                data-allownull="no" data-validate="text" 
                                data-name="Câu trả lời" 
                                data-xml="no" data-doc="no" 
                                ofocus="autofocus"
                        >
                <input type="hidden"   name="txt_question_answer_id" class="txt_question_answer_id" >
                <button type="button" class="ButtonDelete" onclick="btn_delete_answer_onclick(this);">Xóa</button>
            </div>
        </div>
    </div>
    
</div>
<script>
    <!--
    function btn_delete_answer_onclick(anchor,answer_id)
    {
       if($('#results .Row').length <= 1)
       {
            alert('Mỗi câu hỏi phải có ít nhất một câu trả lời. \nBạn không thể xóa câu trả lời này');
            return false;  
       }
        var delete_answer_id = $('#hdn_delete_answer_id').val() || '';
        var id_delete        = $(anchor).parent('div.right-Col.answer').find('.txt_question_answer_id').val();
        if(parseInt($('#hdn_status').val()) > 0)
        {
            if(delete_answer_id.trim() == '')
            {
                $('#hdn_delete_answer_id').val(id_delete);
            }
            else
            {
                $('#hdn_delete_answer_id').val(','+id_delete);
            }
        }
        if(typeof(answer_id) != 'undefined' && parseInt(answer_id) >0)
        {
            var list_anser_delete_id = $('#hdn_delete_answer_id').val() || '';
            if(list_anser_delete_id .length  == 0)
            {
                $('#hdn_delete_answer_id').val(answer_id);
            }
            else
            {
                 $('#hdn_delete_answer_id').val(list_anser_delete_id + ',' +answer_id);
            }
        }
        $(anchor).parents('div.Row').remove();
    }
 
    function btn_hidepopwin_onlick()
    {
       var f = document.frmMain;
       var xObj = new DynamicFormHelper('','',f);
       if (!xObj.ValidateForm(f))
       {
           return ;
       }
        var json = [];
        var question_results = [];
        $('#box-results .txt_question_answer_name').each(function(index){
            question_results.push({
                answer_name: $(this).attr('value')
                ,answer_id :$(this).parent('div.right-Col.answer').find('.txt_question_answer_id').val()
            });
        });
   
        json.push({
             question_id : $('#hdn_item_id').val() 
            ,question_type: $('#sel_question_type').val() 
            ,question_name: $('#txt_question_name').val() 
            ,question_status: $('#hdn_status').val() 
            ,answer_delete_id:$('#hdn_delete_answer_id').val()
            ,question_results: question_results
        });
        returnVal = json;
        window.parent.hidePopWin(true);
    }

    /**
     * Comment
     */
    function sel_cat_question_onchange(selector) 
    {
        var sel_cat_question_id = $(selector).val();
        $('#question_cat_id').val(sel_cat_question_id);
    
         var html         = $('#box-template #template-results').html();
         var html_btn_add = $('#box-template #btn_add').html();
         
        if((sel_cat_question_id  < 2)  && $('#box-results .Row').length <= 0)
        {
            $('#results').append(html);  
            $('#box-results').append(html_btn_add);
        }

        if(sel_cat_question_id  >=2   && $('#box-results .Row').length > 0 )
        {
            $('#box-answer #results .Row').remove();
            $('#box-results .Row.btn-add').remove();
            
        }
    }

               
    function btn_onlcik_add_question() 
    {
        var count_result = $('#results .Row').last().find('.txt_question_answer_name').attr('data-count') || 0;
        count_result = parseInt(count_result) +1;
        if($('#results').find('.Row').length >0)
        {
            var html = $('#box-template #two').html();
            $('#box-results #results').append($(html));
        }
        else
        {
             var html = $('#box-template #template-results').html();
            $('#box-results #results').append($(html));
        }
        $('#results .Row')
            .last()
            .find('.txt_question_answer_name')
            .attr('data-count',count_result)
            .attr('id','txt_question_answer_name_'+count_result);  
    }

    -->
</script>
<?php
$this->template->display('dsp_footer_pop_win.php');
//q