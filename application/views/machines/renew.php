            <script type="text/javascript">
                $(document).ready(function(){
                    $("#date_range").daterangepicker();
                    // put code here
                    $("#add_dairy_form").validate({
                        rules: {
//                            machine_id: "required",
                            machine_name: { 
                                required: true,
                            },
            //                password: "required",
                            type: {
                                required: true,
                            },
                            dairy_id: {
                                required: true,
                            },
                            /*validity: {
                                required: true,
                            }*/
                        },
                        messages: {
//                            machine_id: "Please enter machine id",
                            machine_name: {
                                required: "Please enter a machine name",
                            },
            //                password: "Please enter password",
                            type: {
                                required: "Please select machine type",
                            },
                            dairy_id: {
                                required: "Please select dairy"
                            },
                            /*validity: {
                                required: "Please select validity"
                            }*/
                        }
                    });
                });
            </script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Renew Machine
                        <small>Renew Machine</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Renew Machine</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Renew Machine</h3>
                                </div><!-- /.box-header -->
                                <form role="form" class="form-horizontal" id="add_dairy_form" action="<?php echo base_url(); ?>index.php/machines/renew/<?php echo $id; ?>" method="post">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="machine_id">Machine ID</label>
                                            <div class="col-md-4">
                                                <input type="text" name="machine_id" id="machine_id" class="form-control" value="" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="machine_name">Machine Name <span style="color: red;">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="machine_name" id="machine_name" class="form-control" value=""/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="machine_type">Machine Type <span style="color: red;">*</span></label>
                                            <div class="col-md-4">
                                                <select class="form-control" id="machine_type" name="type">
                                                    <option value="">--Select Type--</option>
                                                    <option value="USB" <?php if($machine->machine_type == "USB"){?>selected <?php } ?>>USB</option>
                                                    <option value="BLUETOOTH" <?php if($machine->machine_type == "BLUETOOTH"){?>selected <?php } ?>>BLUETOOTH</option>
                                                    <option value="GPRS" <?php if($machine->machine_type == "GPRS"){?>selected <?php } ?>>GPRS</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="dairy_id">Dairy <span style="color: red;">*</span></label>
                                            <div class="col-md-4">
                                                <select class="form-control" id="dairy_id" name="dairy_id">
                                                    <option value="">--Select Dairy--</option>
                                                    <?php 
                                                        if(!empty($dairy_info)) {
                                                            foreach($dairy_info as $row){
                                                    ?>
                                                    <option value="<?php echo $row->id; ?>" <?php if($row->id == $machine->dairy_id){ ?>selected <?php } ?>><?php echo $row->name; ?></option>
                                                    <?php 
                                                            }
                                                        }
                                                    ?>          
                                                </select>
                                            </div>
                                        </div>
                                        <!--<div class="form-group">
                                            <label class="control-label col-md-2" for="validity">Validity</label>
                                            <div class="col-md-4">
                                                <select class="form-control" id="validity" name="validity" >
                                                    <option value="">--Select Validity--</option>
                                                    <option value="3m" <?php /*if($machine->validity == "3m"){*/?>selected <?php /*} */?>> 3 Months</option>
                                                    <option value="6m" <?php /*if($machine->validity == "6m"){*/?>selected <?php /*} */?>> 6 Months</option>
                                                    <option value="9m" <?php /*if($machine->validity == "9m"){*/?>selected <?php /*} */?>> 9 Months</option>
                                                    <option value="1y" <?php /*if($machine->validity == "1y"){*/?>selected <?php /*} */?>> 1 Year</option>
                                                </select>
                                            </div>
                                        </div>-->
                                        <?php if(!$machine->from_date){ $date_range = ''; }else{ $date_range = date('m/d/Y', strtotime($machine->from_date))." - ".date('m/d/Y', strtotime($machine->to_date)); } ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="date_range">Validity</label>
                                            <div class="col-md-4">
                                                <input type="text" name="validity" id="date_range" class="form-control" value="<?php echo $date_range; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->