<script type="text/javascript">
    $(document).ready(function(){
        $("#add_dairy_form").validate({
            rules: {
                name: "required",
                username: { 
                    required: true,
                    minlength: 5
                },
//                password: "required",
                email: {
                    email: true,
                },
                mobile: {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10,
                },
                pincode: {
                    required: true,
                    number: true,
                    minlength: 6,
                    maxlength: 6,
                }
            },
            messages: {
                name: "Please enter name",
                username: {
                    required: "Please enter a username",
                    minlength: "Your username must consist of at least 5 characters"
                },
//                password: "Please enter password",
                email: {
                    email: "Please enter valid email",
                },
                mobile: {
                    required: "Please enter mobile",
                    number: "Only numeric value is allowed",
                    minlength: "Minimum 10 number allowed",
                    maxlength: "Maximum 10 number allowed",
                },
                pincode: {
                    number: "Only numeric value is allowed",
                    minlength: "Minimum 6 number allowed",
                    maxlength: "Maximum 6 number allowed",
                }
            }
        });
        
        // reset password active
        $("#reset_pass").on("click", function(e){
            e.preventDefault();
            $("#password").show();
            $(this).hide();
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
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $("#blah").show()
                $('#blah')
                    .attr('src', e.target.result)
                    .width(100)
                    .height(100);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Society
                        <small>Edit Society</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Edit Society</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Edit Society</h3>
                                </div><!-- /.box-header -->
                                <form role="form" class="form-horizontal" id="add_dairy_form" action="<?php echo base_url(); ?>index.php/society/edit/<?php echo $id; ?>" method="post" enctype="multipart/form-data">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="name">Name <span style="color:red;">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="name" class="form-control" id="name" value="<?php echo $society->name; ?>"/>
                                                <?php if(isset($errors['name'])){
                                                    echo "<label class='error' for='name'>".$errors['name']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="username">Username <span style="color:red;">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="username" class="form-control" id="username" value="<?php echo $society->username; ?>"/>
                                                <?php if(isset($errors['username'])){
                                                    echo "<label class='error'>".$errors['username']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="username">Email</label>
                                            <div class="col-md-4">
                                                <input type="text" name="email" class="form-control" id="email" value="<?php echo $society->email; ?>"/>
                                                <?php if(isset($errors['email'])){
                                                    echo "<label class='error'>".$errors['email']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="password">Password</label>
                                            <div class="col-md-4">
                                                <input type="password" name="password" class="form-control" id="password" style="display: none;"/>
                                                <?php if(isset($errors['password'])){
                                                    echo "<label class='error'>".$errors['password']."</label>";
                                                } ?>
                                                <span style="position: relative; top: 10px;"><a style="cursor: pointer;" id="reset_pass">Reset Password</a></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="mobile">Mobile <span style="color:red;">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="mobile" class="form-control" id="mobile" value="<?php echo $society->mobile; ?>"/>
                                                <?php if(isset($errors['mobile'])){
                                                    echo "<label class='error'>".$errors['mobile']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="address">Address</label>
                                            <div class="col-md-4">
                                                <textarea class="form-control" cols="50" rows="3" id="address" name="address"><?php echo $society->address; ?></textarea>
                                                <?php if(isset($errors['address'])){
                                                    echo "<label class='error'>".$errors['address']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="area">Area</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="area" id="area" value="<?php echo $society->area; ?>"/>
                                                <?php if(isset($errors['area'])){
                                                    echo "<label class='error'>".$errors['area']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="street">Street</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="street" id="street" value="<?php echo $society->street; ?>"/>
                                                <?php if(isset($errors['street'])){
                                                    echo "<label class='error'>".$errors['street']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="contact_person">Contact Person</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="contact_person" id="contact_person" value="<?php echo $society->contact_person; ?>"/>
                                                <?php if(isset($errors['contact_person'])){
                                                    echo "<label class='error'>".$errors['contact_person']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="pincode">Pincode</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="pincode" id="pincode" value="<?php echo $society->pincode; ?>"/>
                                                <?php if(isset($errors['pincode'])){
                                                    echo "<label class='error'>".$errors['pincode']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="state">State</label>
                                            <div class="col-md-4">
                                                <select name="state" class="form-control" id="states">
                                                    <option value="">Select State</option>
                                                    <?php 
                                                        if(!empty($states)){
                                                            foreach($states as $s){
                                                    ?>
                                                    <option value="<?php echo $s->id; ?>" <?php if($society->state == $s->id){ ?>selected <?php } ?>><?php echo $s->name; ?></option>
                                                    <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="city_content"></div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="ac_no"> A/c No</label>
                                            <div class="col-md-4">
                                                <input type="number" name="ac_no" id="ac_no" class="form-control" value="<?php echo $society->acc_no; ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="bank_name"> Bank Name</label>
                                            <div class="col-md-4">
                                                <input type="text" name="bank_name" id="bank_name" class="form-control" value="<?php echo $society->bank_name; ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="acc_type"> A/c Type</label>
                                            <div class="col-md-4">
                                                <select class="form-control" name="acc_type" id="acc_type">
                                                    <option value="current" <?php if($society->acc_type == "current"){?>selected <?php } ?>>Current</option>
                                                    <option value="saving" <?php if($society->acc_type == "saving"){?>selected <?php } ?>>Saving</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="ifsc">IFSC</label>
                                            <div class="col-md-4">
                                                <input type="text" name="ifsc" id="ifsc" class="form-control" value="<?php echo $society->ifsc; ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="logo">Logo</label>
                                            <div class="col-md-4">
                                                <input type="file" name="logo" id="logo" class="form-control" onchange="readURL(this);"/>
                                                <?php if(isset($errors['logo'])){
                                                    echo "<label class='error'>".$errors['logo']."</label>";
                                                } ?>
                                                <img id="blah" src="<?php echo base_url(); ?>assets/uploads/<?php echo $society->photo; ?>" alt="Dairy Logo" height="100" width="100" />
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="username_edit" id="username_edit" value="<?php echo $society->username; ?>" />
                                    <input type="hidden" name="email_edit" id="email_edit" value="<?php echo $society->email; ?>" />
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a class="btn btn-danger" href="<?php echo base_url(); ?>index.php/society">Cancel</a>
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->