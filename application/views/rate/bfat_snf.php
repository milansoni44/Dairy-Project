<script type="text/javascript">
    $(document).ready(function(){
        $("#import_bfat").validate({
            rules:{
                import_bfat:"required",
            },
            messages:{
                import_bfat: "Please select file"
            }
        })
    });
</script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Import Buffalo Fat SNF
                        <small>Import</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Import Bfat SNF</li>
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
                                    <h3 class="box-title">Import Bfat SNF</h3>
                                </div><!-- /.box-header -->
                                <form action="<?php echo base_url(); ?>index.php/rate/import_bfat_snf" class="form-horizontal" method="post" id="import_cfat" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="import">Import</label>
                                        <div class="col-md-4">
                                            <input type="file" name="import_bfat" id="import" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <input type="submit" name="submit" class="btn btn-primary" />
                                        <a class="btn btn-danger" href="<?php echo base_url(); ?>index.php/rate/bfat_snf">Cancel</a>
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->