<script type="text/javascript">
    $(document).ready(function(){
        $("#add_dairy_form").validate({
            rules: {
                dairy: "required",
                machine: "required",
            },
            messages: {
                dairy: "Please select dairy",
                machine: "Please select machine",
            }
        });
    });
</script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Dairy Machine Mapping
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
                                    <h3 class="box-title">Map Dairy and Machines</h3>
                                </div> 
                                <form role="form" class="form-horizontal" id="add_dairy_form" action="<?php echo base_url(); ?>index.php/machines/add_allocate" method="post">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="dairy">Dairy</label>
                                            <div class="col-md-4">
                                                <select class="form-control" name="dairy" id="dairy">
                                                    <option value="">Select Dairy</option>
                                                    <?php 
                                                        if(!empty($dairy)){
                                                            foreach($dairy as $row){
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
                                            <label class="control-label col-md-2" for="machine">Machines</label>
                                            <div class="col-md-4">
                                                <select class="form-control" name="machine" id="machine">
                                                    <option value="">Select Machines</option>
                                                    <?php 
                                                        if(!empty($machines)){
                                                            foreach($machines as $row){
                                                    ?>
                                                    <option value="<?php echo $row->id; ?>"><?php echo $row->machine_id; ?></option>
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
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->