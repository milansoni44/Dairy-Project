<script type="text/javascript">
    $(document).ready(function(){
        $("#add_dairy_form").validate({
            rules: {
                machine: "required",
                import_member:{
                    required: true,
                    extension: "docx|rtf|doc|pdf"
                }
            },
            messages: {
                machine: "Please select machine",
                import_member:{
                    required: "Please select file",
                    extension: "Please upload valid file formats"
                }
            }
        });
    });
</script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Milk Supplier
                        <small>Import Milk Supplier</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Import Milk Supplier</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Import Milk Supplier</h3>
                                </div><!-- /.box-header -->
                                <form role="form" class="form-horizontal" id="add_dairy_form" action="<?php echo base_url(); ?>index.php/customers/import_test" method="post" enctype="multipart/form-data">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="machine">Machine</label>
                                            <div class="col-md-4">
                                                <select class="form-control" id="machine" name="machine" <?php if($tmp > 0){ ?>disabled <?php } ?>>
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
                                            <label class="control-label col-sm-2">Upload Csv</label>
                                            <div class="col-md-4">
                                                <input type="file" <?php if($tmp > 0){ ?>disabled <?php } ?> name="import_member" id="import_member" class="form-control"/>
                                            </div>
                                            <?php if($tmp > 0){ ?>
                                            <span style="color:red;"><a href="<?php echo base_url(); ?>index.php/customers/correct">Please correct previous data</a></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <input type="submit" <?php if($tmp > 0){ ?>disabled <?php } ?> name="submit" id="submit" value="Submit" class="btn btn-primary" />
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->