<script type="text/javascript">
    $(document).ready(function(){
        $("#add_dairy_form").validate({
            rules: {
                society: "required",
                machine: "required",
            },
            messages: {
                society: "Please select society",
                machine: "Please select machine",
            }
        });
    });
</script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Society Machine Mapping
                        <small>Map Machines</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Map Machines</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Map Society and Machines</h3>
                                </div> 
                                <form role="form" class="form-horizontal" id="add_dairy_form" action="<?php echo base_url(); ?>index.php/machines/allocate_to_soc" method="post">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="society">Society <span style="color:red;">*</span></label>
                                            <div class="col-md-4">
                                                <select class="form-control" name="society" id="society">
                                                    <option value="">Select Society</option>
                                                    <?php 
                                                        if(!empty($society)){
                                                            foreach($society as $row){
                                                    ?>
                                                    <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                                                    <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="machine">Machines <span style="color:red;">*</span></label>
                                            <div class="col-md-4">
                                                <select class="form-control" name="machine" id="machine">
                                                    <option value="">Select Machines</option>
                                                    <?php 
                                                        if(!empty($machines)){
                                                            foreach($machines as $row){
                                                    ?>
                                                    <option value="<?php echo $row->mo_id; ?>"><?php echo $row->machine_id; ?></option>
                                                    <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                                        <a class="btn btn-danger" href="<?php echo base_url(); ?>index.php/machines/allocate">Cancel</a>
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->