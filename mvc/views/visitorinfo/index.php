
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-visitorinfo"></i> <?=$this->lang->line('panel_title')?></h3>


        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><a href="<?=base_url("visitorinfo/index")?>"><?=$this->lang->line('menu_visitorinfo')?></a></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="col-sm-7">
                    <form  id="myform" action="<?=base_url('visitorinfo/index')?>" class="form-horizontal" role="form" method="post" enctype="multipart/form-data">

                        <div class='form-group'>
                            <label for="name" class="col-sm-3 control-label">
                                <?=$this->lang->line("name")?>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-sm" id="name" name="name" value="<?=set_value('name')?>" required>
                                <span class="text-danger" id="error_name"></span>
                            </div>
                        </div>

                        <div class='form-group' >
                            <label for="email_id" class="col-sm-3 control-label">
                                <?=$this->lang->line("email_id")?>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-sm" id="email_id" name="email_id" value="<?=set_value('email_id')?>" >
                                <span class="text-danger" id="error_email_id"></span>
                            </div>
                        </div>

                        <div class='form-group' >
                            <label for="phone" class="col-sm-3 control-label">
                                <?=$this->lang->line("phone")?>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-sm" id="phone" name="phone" value="<?=set_value('phone')?>" >
                                <span class="text-danger" id="error_phone"></span>
                            </div>
                        </div>
                        <div class='form-group' >
                            <label for="company_name" class="col-sm-3 control-label">
                                <?=$this->lang->line("company_name")?>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-sm" id="company_name" name="company_name" value="<?=set_value('company_name')?>" >
                                <span class="text-danger" id="error_company_name"></span>
                            </div>
                        </div>
                        <div class='form-group' >
                            <label for="coming_from" class="col-sm-3 control-label">
                                <?=$this->lang->line("coming_from")?>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-sm" id="coming_from" name="coming_from" value="<?=set_value('coming_from')?>" >
                                <span class="text-danger" id="error_coming_from"></span>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for="usertypeID" class="col-sm-3 control-label">
                                <?=$this->lang->line("visitor_usrtype")?>
                            </label>
                            <div class="col-sm-8">
                                <?php
                                    $array[0] = $this->lang->line('visitor_select_usertype');
                                    if(count($usertypes)) {
                                        foreach ($usertypes as $key => $usertype) {
                                            $array[$usertype->usertypeID] = $usertype->usertype;
                                        }
                                    }
                                    echo form_dropdown("usertypeID", $array,
                                        set_value("usertypeID"), "id='usertypeID' class='form-control'"
                                    );
                                ?>
                                <span class="text-danger" id="error_to_meet_personID"></span>
                            </div>
                        </div>

                        <div class='form-group' >
                            <label for="to_meet" class="col-sm-3 control-label">
                                <?=$this->lang->line("to_meet")?>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control select3" name="to_meet" id="to_meet">
                                    <?php if(count($admins)) { foreach ($admins as $admin) {  ?>
                                        <option value="<?php echo $admin->usertypeID.','.$admin->systemadminID.','.$admin->name ?>"><?php echo $admin->name ?></option>
                                    <?php } } ?>
                                </select>
                                <span class="text-danger" id="error_to_meet_personID"></span>
                            </div>
                        </div>
                        <div class='form-group' >
                            <label for="representing" class="col-sm-3 control-label">
                                <?=$this->lang->line("representing")?>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control input-sm" name="representing" id="representing">
                                    <option value="vendor">Vendor</option>
                                    <option value="friend">Friend</option>
                                    <option value="family">Family</option>
                                    <option value="interview">Interview</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <span class="text-danger" id="error_representing"></span>
                        </div>

                        <input id="mydata" type="hidden" name="mydata" value=""/>

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-8">
                                <input class="btn btn-success" type="button" value="<?=$this->lang->line("add_title")?>" onClick="take_snapshot()">
                                <input class="btn btn-danger" type="button" value="<?=$this->lang->line("cancel")?>" >
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-5">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-calendar"></i> <?=$this->lang->line('photo')?></h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="visitor_face" width="240" height="240"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-calendar"></i> <?=$this->lang->line('visitor_checkout')?></h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-12">
                                <form action="" class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="col-sm-10" >
                                            <input type="text" class="form-control input-sm" name="checkout_id" id="checkout_id" placeholder="<?=$this->lang->line("visitorID")?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-8" >
                                            <input type="button" class="btn btn-danger" name="logout_submit" id="logout_submit" value="<?=$this->lang->line("logout")?>" >
                                        </div>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="col-sm-12">
                <div id="hide-table">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th ><?=$this->lang->line('slno')?></th>
                                <th ><?=$this->lang->line('visitorID')?></th>
                                <th><?=$this->lang->line('name')?></th>
                                <th><?=$this->lang->line('to_meet')?></th>
                                <th><?=$this->lang->line('check_in')?></th>
                                <th><?=$this->lang->line('check_out')?></th>
                                <th><?=$this->lang->line('status')?></th>
                                <?php
                                    if(permissionChecker('visitorinfo_view') || permissionChecker('visitorinfo_delete')) {
                                        echo "<th>".$this->lang->line('action')."</th>";
                                    }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(count($passes)) {$i = 1; foreach($passes as $pass) { ?>
                            <tr>
                                <td data-title="<?=$this->lang->line('slno')?>">
                                    <?php echo $i; ?>
                                </td>
                                <td data-title="<?=$this->lang->line('slno')?>">
                                    <?php echo $pass->visitorID; ?>
                                </td>
                                <td data-title="<?=$this->lang->line('name')?>">
                                    <?php echo $pass->name; ?>
                                </td>
                                <td data-title="<?=$this->lang->line('to_meet')?>">
                                    <?php
                                        if (count($allUsers)) {
                                            echo $allUsers[$pass->to_meet_usertypeID][$pass->to_meet_personID][0];
                                        }
                                    ?>
                                </td>
                                <td data-title="<?=$this->lang->line('check_in')?>">
                                    <?php echo $pass->check_in; ?>
                                </td>
                                <td data-title="<?=$this->lang->line('check_out')?>">
                                    <?php echo $pass->check_out; ?>
                                </td>
                                <td data-title="<?=$this->lang->line('status')?>" class="text-center">
                                <?php if ($pass->status==0): ?>
                                    <button class="btn btn-success btn-xs">in</button>
                                <?php else: ?>
                                    <button class="btn btn-danger btn-xs">out</button>
                                <?php endif ?>
                                </td>
                                <?php if(permissionChecker('visitorinfo_view') || permissionChecker('visitorinfo_delete')) { ?>
                                <td data-title="<?=$this->lang->line('action')?>">
                                    <?php
                                        if(permissionChecker('visitorinfo_view')) {
                                    ?>
                                        <button class="btn btn-success btn-xs view_pass" id="<?php echo $pass->visitorID; ?>"><i class="fa fa-check-square-o"></i></button>
                                    <?php
                                        }
                                    ?>
                                    <?php echo btn_delete('visitorinfo/delete/'.$pass->visitorID, $this->lang->line('delete')) ?>
                                </td>
                                <?php } ?>

                            </tr>
                        <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url('assets/webcamjs/webcam.min.js'); ?>"></script>
<script language="JavaScript">
    Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#visitor_face');
</script>

<script language="JavaScript">
    function emailValidate() {
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        var emailDom = document.getElementById('email_id');
        if (emailDom) {
            if (emailDom.value.search(emailPattern) == -1) {
                alert('Invalid Email Id');
                return true;
            };
        };
        return true;
    }
    function name () {
        var name = $("#name").val();
        if (!name) {
            alert('Name field required');
            return true;
        };
        return true;
    }

    function take_snapshot() {
        var image = '';
        var print_image = '';
        var name = $('#name').val();
        var email_id = $('#email_id').val();
        var phone = $('#phone').val();
        var company_name = $('#company_name').val();
        var coming_from = $('#coming_from').val();
        var to_meet_personID = $('#to_meet').val();
        var to_meet_usertypeID = $('#usertypeID').val();
        var representing = $('#representing').val();

        Webcam.snap( function(data_uri) {
            image = data_uri.replace(/^data\:image\/\w+\;base64\,/, '');
            print_image = data_uri;
        } );
        $.ajax({
            type: 'POST',
            url: "<?=base_url('visitorinfo/index')?>",
            data: {image: image, name: name, email_id : email_id, phone : phone, company_name: company_name, coming_from: coming_from, to_meet_personID : to_meet_personID , representing: representing, to_meet_usertypeID:to_meet_usertypeID},
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if(data.validate == false) {

                    if (data.email_id) {
                        $("#error_email_id").html(data.email_id);
                    }
                    if (data.name) {
                        $("#error_name").html(data.name);
                    }
                    if (data.phone) {
                        $("#error_phone").html(data.phone);
                    }
                    if (data.company_name) {
                        $("#error_company_name").html(data.company_name);
                    }
                    if (data.coming_from) {
                        $("#error_coming_from").html(data.coming_from);
                    }
                    if (data.to_meet_personID) {
                        $("#error_to_meet_personID").html(data.to_meet_personID);
                    }
                    if (data.to_meet_usertypeID) {
                        $("#error_to_meet_person_usertypeID").html(data.to_meet_person_usertypeID);
                    }
                }
                if (data.id) {
                    myWindow = window.open('', '', 'width=500,height=500');
                    var divUpd = document.createElement('div');
                    divUpd.id = 'upload_results';
                    if (myWindow.document.body != null) { myWindow.document.body.appendChild(divUpd); }

                    myWindow.document.body.innerHTML =
                                    "<table width='250px' style='border-width: 1px 1px 1px 1px;border-spacing: 0;border-collapse: collapse;" +
                                    "border-style: ridge;border-color: green;font-family: Verdana;font-size: 8pt;clear: both;margin: 0;padding: 0;'>" +
                                    "<tr style='height: 20px;'>" +
                                    "<td align='center' valign='middle' style='font-weight: bold;font-size: 12px;color: black;background-color:" +
                                    "#D7D8D2;width: 250px;height:30px;font-size:15pt;font-family: Verdana;font-weight:bold;background-color: #000000;color:#ffffff;'>" +
                                    "VISITOR PASS</td></tr>" +
                                    "<tr><td align='center'><br /><img width='150' height='120' src='" + print_image + "'/><br /><br />" +
                                    "<table border='0' style='font-size: 8pt;font-family: Verdana;' " +
                                    "width='200px' bgcolor='#ffffff' >" +
                                    "<tr style='height: 20px;'>" +
                                    "<td align='left' style='width: 150px;font-weight:bold;'>ID &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                    "<td align='right' style='width: 150px;font-weight:bold;'>" + data.id + "</td></tr>" +
                                    "<tr style='height: 20px;'>" +
                                    "<td align='left' style='width: 150px;font-weight:bold;'>Name &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                    "<td align='right' style='width: 150px;font-weight:bold;'>" + name + "</td></tr>" +
                                    "<tr style='height: 20px;'>" +
                                    "<td align='left' style='width: 150px;font-weight:bold;'>Email &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                    "<td align='right' style='width: 150px;font-weight:bold;'>" + email_id + "</td></tr>" +
                                    "<tr style='height: 20px;'>" +
                                    "<td align='left' style='width: 150px;font-weight:bold;'>Phone &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                    "<td align='right' style='width: 150px;font-weight:bold;'>" + phone + "</td></tr>" +
                                    "<tr style='height: 20px;'>" +
                                    "<td align='left' style='width: 150px;font-weight:bold;'>Company &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                    "<td align='right' style='width: 150px;font-weight:bold;'>" + company_name + "</td></tr>" +
                                    "<tr style='height: 20px;'>" +
                                    "<td align='left' style='width: 150px;font-weight:bold;'>Coming From &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                    "<td align='right' style='width: 150px;font-weight:bold;'>" + coming_from + "</td></tr>" +
                                    "<tr style='height: 20px;'>" +
                                    "<td align='left' style='width: 150px;font-weight:bold;'>To Meet &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                    "<td align='right' style='width: 150px;font-weight:bold;'>" + data.to_meet +"</td></tr>" +
                                    "<tr style='height: 20px;'>" +
                                    "<td align='left' style='width: 150px;font-weight:bold;'> Designation &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                    "<td align='right' style='width: 150px;font-weight:bold;'>" + data.to_meet_type +"</td></tr>" +
                                    "<tr style='height: 20px;'>" +
                                    "<td align='left' style='width: 150px;font-weight:bold;'>Representing &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                    "<td align='right' style='width: 150px;font-weight:bold;'>" + representing + "</td></tr>" +
                                    "</table>";

                    myWindow.focus();
                    printContent = function() {
                        myWindow.focus();
                        myWindow.print();
                        closeCurWindow = function() {
                            myWindow.close();
                        };
                        window.setTimeout(closeCurWindow, 3000);
                    };
                    window.setTimeout(printContent, 3000);
                    setTimeout(function(){
                        window.location.reload();
                    }, 5000);
                };
            }, error: function(data){
                var errors = $.parseJSON(data.responseText);
                alert(errors);
            }
        });

    }
</script>
<script type="text/javascript">
    $("#logout_submit").click( function() {
        var visitorID = $('#checkout_id').val();
        if (visitorID) {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('visitorinfo/logout')?>",
                data: "visitorID=" + visitorID,
                dataType: "html",
                success: function(data) {
                    window.location.href = data;
                }
            });
        }
    });
    $(".view_pass").click( function() {
        var visitorinfoID = $(this).attr('id');
        var image_path = "<?php echo base_url()?>";
        if (visitorinfoID) {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('visitorinfo/view')?>",
                data: "visitorinfoID=" + visitorinfoID,
                dataType: "json",
                success: function(data) {
                    if (data) {
                        myWindow = window.open('', '', 'width=500,height=500');
                        var divUpd = document.createElement('div');
                        divUpd.id = 'upload_results';
                        if (myWindow.document.body != null) { myWindow.document.body.appendChild(divUpd); }

                        myWindow.document.body.innerHTML =
                                        "<table width='250px' style='border-width: 1px 1px 1px 1px;border-spacing: 0;border-collapse: collapse;" +
                                        "border-style: ridge;border-color: green;font-family: Verdana;font-size: 8pt;clear: both;margin: 0;padding: 0;'>" +
                                        "<tr style='height: 20px;'>" +
                                        "<td align='center' valign='middle' style='font-weight: bold;font-size: 12px;color: black;background-color:" +
                                        "#D7D8D2;width: 250px;height:30px;font-size:15pt;font-family: Verdana;font-weight:bold;background-color: #000000;color:#ffffff;'>" +
                                        "VISITOR PASS</td></tr>" +
                                        "<tr><td align='center'><br /><img width='150' height='120' src='"+image_path+"uploads/visitor/" + data.photo + "'/><br /><br />" +
                                        "<table border='0' style='font-size: 8pt;font-family: Verdana;' " +
                                        "width='200px' bgcolor='#ffffff' >" +
                                        "<tr style='height: 20px;'>" +
                                        "<td align='left' style='width: 150px;font-weight:bold;'>ID &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                        "<td align='right' style='width: 150px;font-weight:bold;'>" + data.id + "</td></tr>" +
                                        "<td align='left' style='width: 150px;font-weight:bold;'>Name &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                        "<td align='right' style='width: 150px;font-weight:bold;'>" + data.name + "</td></tr>" +
                                        "<td align='left' style='width: 150px;font-weight:bold;'>Email &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                        "<td align='right' style='width: 150px;font-weight:bold;'>" + data.email_id + "</td></tr>" +
                                        "<td align='left' style='width: 150px;font-weight:bold;'>Phone &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                        "<td align='right' style='width: 150px;font-weight:bold;'>" + data.phone + "</td></tr>" +
                                        "<td align='left' style='width: 150px;font-weight:bold;'>Company &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                        "<td align='right' style='width: 150px;font-weight:bold;'>" + data.company_name + "</td></tr>" +
                                        "<td align='left' style='width: 150px;font-weight:bold;'>From &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                        "<td align='right' style='width: 150px;font-weight:bold;'>" + data.coming_from + "</td></tr>" +
                                        "<td align='left' style='width: 150px;font-weight:bold;'>To Meet &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                        "<td align='right' style='width: 150px;font-weight:bold;'>" + data.to_meet + "</td></tr>" +
                                        "<td align='left' style='width: 150px;font-weight:bold;'>Designation &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                        "<td align='right' style='width: 150px;font-weight:bold;'>" + data.to_meet_type + "</td></tr>" +
                                        "<td align='left' style='width: 150px;font-weight:bold;'>Representing &nbsp;&nbsp;&nbsp;&nbsp;</td>" +
                                        "<td align='right' style='width: 150px;font-weight:bold;'>" + data.representing + "</td></tr>" +
                                        "</table>";

                        myWindow.focus();
                        printContent = function() {
                            myWindow.focus();
                            myWindow.print();
                            closeCurWindow = function() {
                                myWindow.close();
                            };
                            window.setTimeout(closeCurWindow, 3000);
                        };
                        window.setTimeout(printContent, 3000);
                    };
                }
            });
        }
    });
</script>


<script type="text/javascript">
$('#usertypeID').change(function(event) {
    var usertypeID = $(this).val();
    if(usertypeID === '0') {
        $('#to').val(0);
        $('#to_meet').val(0);
    } else {
        $.ajax({
            type: 'POST',
            url: "<?=base_url('visitorinfo/usercall')?>",
            data: "id=" + usertypeID,
            dataType: "html",
            success: function(data) {
               $('#to_meet').html(data);
            }
        });
    }
});
</script>
