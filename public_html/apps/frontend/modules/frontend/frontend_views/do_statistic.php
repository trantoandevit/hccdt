<?php
defined('DS') or die();
?>

<script>
    function update_stats(){
        v_url = '<?php echo $this->get_controller_url() ?>' + 'update_statistic';
        $.ajax({
            type: 'post'
            ,url: v_url
        });
    }
    $(document).ready(function(){
        //sau 30s update lai statistic
        update_stats();
        setInterval(update_stats, 30000);
    });
</script>
