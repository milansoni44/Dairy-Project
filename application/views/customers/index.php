            <script>
                $(document).ready(function(){
                    $("#customer_machine").validate({
                        rules: {
                            society_machine: "required",
                        },
                        messages: {
                            society_machine: "Please select machine",
                        }
                    });
                    $('#example2').DataTable();
                    $("#download_customer").on("click", function(e){
                        var machine;
                        var $url;
                        if($("#society_machine").val() == ""){
                            alert("Please select machine");
                            return false;
                        }else{
                            machine = $("#society_machine").val();
                            $url = "<?php echo base_url(); ?>index.php/customers/export_customer/"+machine;
                        }
                        window.location = $url;
                        return false;
                    });

                    $(document).on("click", '#getUser', function(e){
                        e.preventDefault();

                        var uid = $(this).data('id'); // get id of clicked row
                        $("#dynamic-content").html(''); // leave this div blank
                        $("#modal-loader").show();

                        $.ajax({
                            url: "<?php echo base_url(); ?>index.php/customers/view",
                            type: 'POST',
                            data: { id: uid },
                            dataType: 'html',
                            success: function(data){
                                console.log(data);
                                $('#dynamic-content').html(''); // blank before load.
                                $('#dynamic-content').html(data); // load here
                                $('#modal-loader').hide(); // hide loader
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
                        <li class="active">Milk Supplier</li>
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
                        <?php
                        if($this->session->flashdata('message1')){
                        ?>
                        <div class="alert alert-danger alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $this->session->flashdata('message1'); ?>
                        </div>
                        <?php
                            }
                        ?>
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title"><!-- Hover Data Table --></h3>
                                    <?php if($this->session->userdata("group") == "society") {?>
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/customers/add" class="btn btn-primary" style="color: #fff;">Add Milk Supplier</a></span>
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/customers/import" class="btn btn-primary" style="color: #fff;">Import </a></span>
                                    <span class="pull-right"><a href="#" id="download_customer" class="btn btn-primary" style="color: #fff;">Export </a></span>
                                    <?php } ?>
                                </div><!-- /.box-header -->
                                <form class="form-horizontal" action="<?php echo base_url(); ?>index.php/customers" method="post" id="customer_machine">
                                    <div class="form-group">
                                        <label class="control-label col-md-2" for="society_machine">Machines <span style="color: red;">*</span></label>
                                        <div class="col-md-4">
                                            <select class="form-control" name="society_machine" id="society_machine">
                                                <option value="">--Select Machine--</option>
                                                <?php 
                                                    if(!empty($society_machine)){
                                                        foreach($society_machine as $rw){
                                                ?>
                                                <option value="<?php echo $rw->id; ?>" <?php if(isset($_POST['society_machine']) && $_POST['society_machine'] == $rw->id){ ?>selected <?php } ?>><?php echo $rw->machine_id; ?></option>
                                                <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" name="submit_machine" id="submit_machine" class="btn btn-primary" />
                                        </div>
                                    </div>
                                </form>
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">Member Code</th>
                                                <th style="width: 10%;">Name</th>
                                                <th style="width: 10%;">Mobile</th>
                                                <th style="width: 10%;">Adhar No</th>
                                                <th style="width: 10%;">Type</th>
                                                <th style="width: 30%;">Bank Name - IFSC</th>
                                                <th style="width: 25%;">A/c</th>
                                                <th>Actions</th>
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
                                                <td><?php echo $row->type; ?></td>
                                                <td><?php echo $row->bank_name." - ". $row->ifsc; ?></td>
                                                <td><?php echo $row->ac_no; ?></td>
                                                <td>
                                                    <a href="<?php echo base_url(); ?>index.php/customers/edit/<?php echo $row->id; ?>">Edit</a>
                                                    <!--<button data-toggle="modal" data-target="#view-modal" data-id="<?php /*echo $row->id; */?>" id="getUser" class="btn btn-sm btn-info"><i class="glyphicon glyphicon-eye-open"></i> View</button>-->
                                                    <a data-toggle="modal" data-target="#view-modal" data-id="<?php echo $row->id; ?>" id="getUser" href="#">View</a>
                                                </td>
                                            </tr>
                                            <?php
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
            <div id="view-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title">
                                <i class="glyphicon glyphicon-user"></i> Milk Supplier Profile
                            </h4>
                        </div>

                        <div class="modal-body">
                            <div id="modal-loader" style="display: none; text-align: center;">
                                <!-- ajax loader -->
                                <img src="ajax-loader.gif">
                            </div>

                            <!-- mysql data will be load here -->
                            <div id="dynamic-content"></div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>

                    </div>
                </div>
            </div>