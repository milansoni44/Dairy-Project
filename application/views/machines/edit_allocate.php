            <script type="text/javascript">
                $(document).ready(function(){
                    $("#add_dairy_form").validate({
                        rules: {
                            machine_name: {
                                required: true,
                            },
                            society: {
                                required: true,
                            },
                        },
                        messages: {
                            machine_name: {
                                required: "Please enter a machine name",
                            },
                            society: {
                                required: "Please select society",
                            },
                        }
                    });
                });
            </script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Edit Dairy Machine Mapping
                        <small>Edit Machine</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Edit Dairy Machine</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Edit Dairy Machine</h3>
                                </div>

                                <form role="form" class="form-horizontal" id="add_dairy_form" action="<?php echo base_url(); ?>index.php/machines/edit_allocate/<?php echo $id; ?>" method="post">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="machine_name"> Machine Name <span style="color: red;">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="machine_name" id="machine_name" class="form-control" value="<?php echo $mapped_machine->machine_name; ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="society">Society <span style="color:red;">*</span></label>
                                            <div class="col-md-4">
                                                <select class="form-control" name="society" id="society">
                                                    <option value="">Select Society</option>
                                                    <?php
                                                    if(!empty($society)){
                                                        foreach($society as $row){
                                                            ?>
                                                            <option value="<?php echo $row->id; ?>" <?php if($mapped_machine->society_id == $row->id){ ?>selected <?php } ?>><?php echo $row->name; ?></option>
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