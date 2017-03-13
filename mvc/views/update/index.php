
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-refresh"></i> <?=$this->lang->line('panel_title')?></h3>


        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_update')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-10">
                <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">

                    <?php
                        if(form_error('file'))
                            echo "<div class='form-group has-error' >";
                        else
                            echo "<div class='form-group' >";
                    ?>
                        <label for="file" class="col-sm-2 control-label">
                            <?=$this->lang->line("update_file")?>
                        </label>
                        <div class="col-sm-6">
                            <div class="input-group image-preview">
                                <input type="text" class="form-control image-preview-filename" disabled="disabled">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                        <span class="fa fa-remove"></span>
                                        <?=$this->lang->line('update_clear')?>
                                    </button>
                                    <div class="btn btn-success image-preview-input">
                                        <span class="fa fa-repeat"></span>
                                        <span class="image-preview-input-title">
                                        <?=$this->lang->line('update_file_browse')?></span>
                                        <input id="uploadBtn" type="file" name="file"/>
                                    </div>
                                </span>
                            </div>
                        </div>

                        <span class="col-sm-4 control-label">
                            <span id="error"></span>
                            <?php echo form_error('file'); ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input id="update" type="button" class="btn btn-success" value="<?=$this->lang->line("update_update")?>" >
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

$('#update').click(function() {
    if($('#uploadBtn').val() == '') {
        $('#error').text('Select A File');
    } else {
        $('#error').text('');
        var formData = new FormData();
        formData.append('file', $('#uploadBtn')[0].files[0]);
        $.ajax({
            type: 'POST',
            url: "<?=base_url('update/upload')?>",
            dataType: "html",
            data:  formData,
            contentType: false,
            cache: false,
            processData:false,
            success: function(data) {
                data = JSON.parse(data);
                if (data.database.status=="No") {
                    $.ajax({
                        type: 'POST',
                        url: "<?=base_url('update/index')?>",
                        data: {'data': data},
                        dataType: "html",
                        success: function(result) {
                            window.location.href = result;
                        }
                    });
                }
            }
        });
    }

});


$(function() {
    // Create the close button
    var closebtn = $('<button/>', {
        type:"button",
        text: 'x',
        id: 'close-preview',
        style: 'font-size: initial;',
    });
    closebtn.attr("class","close pull-right");
    // Set the popover default content

    // Create the preview image
    $(".image-preview-input input:file").change(function (){
        var img = $('<img/>', {
            id: 'dynamic',
            width:250,
            height:200,
            overflow:'hidden'
        });
        var file = this.files[0];
        var reader = new FileReader();

        $('.image-preview-clear').click(function(){
            $('.image-preview').attr("data-content","").popover('hide');
            $('.image-preview-filename').val("");
            $('.image-preview-clear').hide();
            $('.image-preview-input input:file').val("");
            $(".image-preview-input-title").text("<?=$this->lang->line('update_file_browse')?>");
        });

        // Set preview image into the popover data-content
        reader.onload = function (e) {
            $(".image-preview-input-title").text("<?=$this->lang->line('update_file_browse')?>");
            $(".image-preview-clear").show();
            $(".image-preview-filename").val(file.name);
            $('.content').css('padding-bottom', '100px');
        }
        reader.readAsDataURL(file);
    });
});

</script>
