<script type="text/javascript">
    $(document).ready(function(){
        $("#add_dairy_form").validate({
            rules: {
                name: "required",
                username: { 
                    required: true,
                    minlength: 2
                },
                password: "required",
                email: {
                    email: true,
                },
                mobile: {
                    required: true,
                    number: true,
                    minlength: 6,
                    maxlength: 12,
                }
            },
            messages: {
                name: "Please enter name",
                username: {
                    required: "Please enter a username",
                    minlength: "Your username must consist of at least 5 characters"
                },
                password: "Please enter password",
                email: {
                    email: "Please enter valid email",
                },
                mobile: {
                    required: "Please enter mobile",
                    number: "Only numeric value is allowed",
                    minlength: "Minimum 6 character allowed",
                    maxlength: "Maximum 12 character allowed",
                }
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
        $('body').on("focus",".validity", function(){
            $(this).daterangepicker();
        });
    });
</script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Dairy
                        <small>Add Dairy</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Add Dairy</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Add Dairy</h3>
                                </div><!-- /.box-header -->
                                <form role="form" class="form-horizontal" id="add_dairy_form" action="<?php echo base_url(); ?>index.php/dairy/add" method="post">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="name">Name</label>
                                            <div class="col-md-4">
                                                <input type="text" name="name" class="form-control" id="name"/>
                                                <?php if(isset($errors['name'])){
                                                    echo "<label class='error' for='name'>".$errors['name']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="username">Username</label>
                                            <div class="col-md-4">
                                                <input type="text" name="username" class="form-control" id="username"/>
                                                <?php if(isset($errors['username'])){
                                                    echo "<label class='error'>".$errors['username']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="username">Email</label>
                                            <div class="col-md-4">
                                                <input type="text" name="email" class="form-control" id="email"/>
                                                <?php if(isset($errors['email'])){
                                                    echo "<label class='error'>".$errors['email']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="password">Password</label>
                                            <div class="col-md-4">
                                                <input type="password" name="password" class="form-control" id="password"/>
                                                <?php if(isset($errors['password'])){
                                                    echo "<label class='error'>".$errors['password']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="mobile">Mobile</label>
                                            <div class="col-md-4">
                                                <input type="text" name="mobile" class="form-control" id="mobile"/>
                                                <?php if(isset($errors['mobile'])){
                                                    echo "<label class='error'>".$errors['mobile']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="address">Address</label>
                                            <div class="col-md-4">
                                                <textarea class="form-control" cols="50" rows="3" id="address" name="address"></textarea>
                                                <?php if(isset($errors['address'])){
                                                    echo "<label class='error'>".$errors['address']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="area">Area</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="area" id="area" />
                                                <?php if(isset($errors['area'])){
                                                    echo "<label class='error'>".$errors['area']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="street">Street</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="street" id="street" />
                                                <?php if(isset($errors['street'])){
                                                    echo "<label class='error'>".$errors['street']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="contact_person">Contact Person</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="contact_person" id="contact_person" />
                                                <?php if(isset($errors['contact_person'])){
                                                    echo "<label class='error'>".$errors['contact_person']."</label>";
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="pincode">Pincode</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="pincode" id="pincode" />
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
                                                    <option value="<?php echo $s->id; ?>"><?php echo $s->name; ?></option>
                                                    <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="city_content"></div>
<!--                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="city">City</label>
                                            <div class="col-md-4">
                                                
                                            </div>
                                        </div>-->
<!--                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="validity">Validity</label>
                                            <div class="col-md-4">
                                                <input type="text" name="validity" id="validity" class="form-control validity" />
                                            </div>
                                        </div>-->
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->