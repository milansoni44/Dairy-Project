            <script>
                $(document).ready(function(){
                    $('#example2').DataTable();
                    $("#form_milk_supplier").validate({
                        rules: {
                            society: "required",
                            machine: "required"
                        },
                        messages: {
                            society: "Please select society",
                            machine: "Please select machine"
                        }
                    });

                    $("#society").on("change", function(e){
                        e.preventDefault();
                        var $this = $(this);
                        var $soc_id = $(this).val();
//                        alert($soc_id);
                        $.ajax({
                            url: "<?php echo base_url(); ?>index.php/customers/allocated_to_society",
                            type: "POST",
                            dataType: "json",
                            cache: false,
                            data: { soc_id: $soc_id },
                            success: function(data)
                            {
                                console.log(typeof data.error);
                                if( data.error === true )
                                {
                                    alert("No machine found.");
                                    $this.val('');
                                    $("#machine").find('option').remove().end().append("<option value=''>-- Select Machine --</option>");
                                    return false;
                                }
                                else
                                {
                                    var select = "";
                                    $.each(data.society_machine, function(key, val){
                                        select += "<option value='"+val.id+"'>"+val.machine_id+"</option>";
                                    });
                                    $("#machine").append(select);
                                }
                            }
                        });
                    });
                });
            </script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Milk Suppliers
                        <small>List Milk Supplier</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Milk Suppliers</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <?php 
                            if($this->session->flashdata("message")){
                                foreach($this->session->flashdata("message") as $error_codes){
                        ?>
                        <div class="alert alert-danger alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $error_codes["Error"]." on line no: ".$error_codes["Line"]; ?>
                        </div>
                        <?php
                                }
                            }
                        ?>
                        <?php
                        if($this->session->flashdata('success')){
                        ?>
                        <div class="alert alert-success alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $this->session->flashdata('success'); ?>
                        </div>
                        <?php
                            }
                        ?>
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title"><!-- Hover Data Table --></h3>
                                    <?php if($this->session->userdata("group") == "society") {?>
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/customers/add" class="btn btn-primary" style="color: #fff;">Add Customers</a></span>
                                    <?php } ?>
                                </div><!-- /.box-header -->
                                <form class="form-horizontal" method="post" id="form_milk_supplier" action="<?php echo base_url(); ?>index.php/customers/society_index">
                                    <div class="form-group">
                                        <label class="control-label col-md-2" for="society">Society <span style="color: red;">*</span></label>
                                        <div class="col-md-3">
                                            <select class="form-control" name="society" id="society">
                                                <option value="">--Select Society--</option>
                                                <?php 
                                                    if(!empty($society)){
                                                        foreach($society as $row_soc){
                                                ?>
                                                <option value="<?php echo $row_soc->id; ?>" <?php if(isset($_POST['society']) && $_POST['society'] == $row_soc->id){ ?>selected  <?php } ?>><?php echo $row_soc->name; ?></option>
                                                <?php 
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control" name="machine" id="machine">
                                                <option value="">--Select machine--</option>
                                                <?php
                                                    if( !empty($machine) )
                                                    {
                                                        foreach($machine as $m)
                                                        {
                                                ?>
                                                            <option value="<?php echo $m->id; ?>" <?php if(isset($_POST['machine']) && $_POST['machine'] == $m->id){ ?>selected  <?php } ?>><?php echo $m->machine_id; ?></option>
                                                <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" name="submit" value="Submit" id="submit" class="btn btn-primary">
                                        </div>
                                    </div>
                                    
                                </form>
                                <div class="box-body table-responsive">
                                    <?php if($this->input->post()){ ?>
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Member Code</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Adhar No</th>
<!--                                                <th>Action</th>-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($customers)){
                                                    foreach($customers as $row){
                                            ?>
                                            <tr>
                                                <td><?php echo $row->mem_code; ?></td>
                                                <td><?php echo $row->customer_name; ?></td>
                                                <td><?php echo $row->mobile; ?></td>
                                                <td><?php echo $row->adhar_no; ?></td>
<!--                                                <td></td>-->
                                            </tr>
                                            <?php
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php } ?>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->