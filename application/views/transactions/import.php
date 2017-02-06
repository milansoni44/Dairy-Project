<script type="text/javascript">
    $(document).ready(function(){
        $("#add_dairy_form").validate({
            rules: {
//                machine: "required",
                transaction:{
                    required: true,
                    extension: "docx|rtf|doc|pdf"
                }
            },
            messages: {
//                machine: "Please select machine",
                transaction:{
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
                        Transactions
                        <small>Import Transactions</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Import Transactions</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-xs-12">
                            <?php
                            if($this->session->flashdata('message')){
                            ?>
                            <div class="alert alert-danger alert-dismissable">
                                <i class="fa fa-check"></i>
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <?php echo $this->session->flashdata('message'); ?>
                            </div>
                            <?php
                                }
                            ?>
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Import Transaction</h3>
                                </div><!-- /.box-header -->
                                <form role="form" class="form-horizontal" id="add_dairy_form" action="<?php echo base_url(); ?>index.php/transactions/import_txn" method="post" enctype="multipart/form-data">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="txn">Upload Csv <span style="color: red;">*</span></label>
                                            <div class="col-md-4">
                                                <input type="file" name="transaction" id="txn" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <input type="submit" name="submit" id="submit" value="Submit" class="btn btn-primary" />
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->