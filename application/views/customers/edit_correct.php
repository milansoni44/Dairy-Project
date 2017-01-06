<script type="text/javascript">
    $(document).ready(function(){
        $("#add_dairy_form").validate({
            rules: {
                machine: "required",
                member_name: "required",
                mobile: "required",
                adhar_no: "required",
                member_code: "required",
            },
            messages: {
                machine: "Please select machine",
                member_name: "Please enter name",
                mobile: "Please Enter mobile number",
                mobile: "Please enter Adhar Number",
                member_code: "Please enter member code",
            }
        });
        
        // on change state ajax
        $("#states").on("change", function(){
            var s_id = $(this).val();
            $.ajax({
                url: "<?php echo base_url(); ?>index.php/dairy/get_cities",
                type: "POST",
                dataType: 'json',
                data: { s_id: s_id },
                cache: false,
                success: function(data){
                    var form = "<div class='form-group'>"+
                        "<label class='control-label col-md-2' for='city'>City</label>"+
                        "<div class='col-md-4'>"+
                        "<select name='city' class='form-control' id='city'><option value=''>Select City</option>";
                
                        $.each(data, function(index, value){
                            form += "<option value='"+value.id+"'>"+value.name+"</option>";
                        });
                    form += "</select>"+
                            "</div></div>";
                    $("#city_content").html(form);
                }
            });
        });
    });
</script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Milk Supplier
                        <small>Update Milk Supplier</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Update Milk Supplier</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Update Milk Supplier</h3>
                                </div><!-- /.box-header -->
                                <form role="form" class="form-horizontal" id="add_dairy_form" action="<?php echo base_url(); ?>index.php/customers/edit_correct/<?php echo $id; ?>" method="post">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="machine">Machine</label>
                                            <div class="col-md-4">
                                                <select class="form-control" id="machine" name="machine">
                                                    <option value="">Select Machine</option>
                                                    <?php 
                                                        if(!empty($machine)){
                                                            foreach($machine as $row_machine){
                                                    ?>
                                                    <option value="<?php echo $row_machine->id ?>" <?php if($row_machine->id == $member->machine_id){?>selected <?php } ?>><?php echo $row_machine->machine_id; ?></option>
                                                    <?php 
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="member_name"> Member Name</label>
                                            <div class="col-md-4">
                                                <input type="text" name="member_name" id="member_name" class="form-control" value="<?php echo $member->customer_name; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="mobile"> Mobile</label>
                                            <div class="col-md-4">
                                                <input type="text" name="mobile" id="mobile" class="form-control" value="<?php echo $member->mobile; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="adhar_no"> Adhar No</label>
                                            <div class="col-md-4">
                                                <input type="text" name="adhar_no" id="adhar_no" class="form-control" value="<?php echo $member->adhar_no; ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="member_code"> Member Code</label>
                                            <div class="col-md-4">
                                                <input type="text" name="member_code" id="member_code" class="form-control" value="<?php echo $member->mem_code; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <input type="submit" name="submit" id="submit" value="Submit" class="btn btn-primary" />
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->