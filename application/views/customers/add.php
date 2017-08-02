<script type="text/javascript">
    $(document).ready(function(){
        jQuery.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-z\s]+$/i.test(value);
        }, "Only alphabetical characters");
        $("#add_dairy_form").validate({
            rules: {
                machine: "required",
                member_name: "required",
                mobile: {
                        required: true,
                        number: true,
                        maxlength: 12
                },
                adhar_no: "required",
                member_code: {
                    required: true,
                    number: true
                },
                type: "required",
                bank_name:{
                    lettersonly: true,
                },
            },
            messages: {
                machine: "Please select machine",
                member_name: "Please enter name",
                mobile: {
                    required: "Please Enter mobile number",
                    number: "Please enter valid number",
                    maxlength: "Number only 12 digits"
                },
                member_code: {
                    required: "Member code is required",
                    number: "Please enter valid number"
                },
                type: "Please select type",
            }
        });
    });
</script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Milk Supplier
                        <small>Add Milk Supplier</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Add Milk Supplier</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Add Milk Supplier</h3>
                                </div><!-- /.box-header -->
                                <form role="form" class="form-horizontal" id="add_dairy_form" action="<?php echo base_url(); ?>index.php/customers/add" method="post">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="machine">Machine <span style="color:red;">*</span></label>
                                            <div class="col-md-4">
                                                <select class="form-control" id="machine" name="machine">
                                                    <option value="">Select Machine</option>
                                                    <?php 
                                                        if(!empty($machine)){
                                                            foreach($machine as $row_machine){
                                                    ?>
                                                    <option value="<?php echo $row_machine->id ?>"><?php echo $row_machine->machine_id; ?></option>
                                                    <?php 
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="member_name"> Member Name <span style="color:red;">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="member_name" id="member_name" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="mobile"> Mobile <span style="color: red;">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="mobile" id="mobile" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="adhar_no"> Adhar No <span style="color:red;">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="adhar_no" id="adhar_no" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="member_code"> Member Code <span style="color: red;">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="member_code" id="member_code" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="type"> Type</label>
                                            <div class="col-md-4">
                                                <select name="type" id="type" class="form-control">
                                                    <option value="cow">Cow</option>
                                                    <option value="bufallo">Bufalo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="ac_no"> A/c No</label>
                                            <div class="col-md-4">
                                                <input type="text" name="ac_no" id="ac_no" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="bank_name"> Bank Name</label>
                                            <div class="col-md-4">
                                                <input type="text" name="bank_name" id="bank_name" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="ifsc"> IFSC</label>
                                            <div class="col-md-4">
                                                <input type="text" name="ifsc" id="ifsc" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="ac_type"> A/c Type</label>
                                            <div class="col-md-4">
                                                <select class="form-control" name="ac_type" id="ac_type">
                                                    <option value="saving">Saving</option>
                                                    <option value="current">Current</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <input type="submit" name="submit" id="submit" value="Submit" class="btn btn-primary" />
                                        <a class="btn btn-danger" href="<?php echo base_url(); ?>index.php/customers">Cancel</a>
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->