            <script type="text/javascript">
                $(document).ready(function(){
                    $("#import_cfat").validate({
                        rules:{
                            import_cfat:"required",
                        },
                        messages:{
                            import_cfat: "Please select file"
                        }
                    })
                });
            </script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Import Cow Fat
                        <small>Import</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Import Cfat</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Import Cfat</h3>
                                </div><!-- /.box-header -->
                                <form action="<?php echo base_url(); ?>index.php/rate/import_cfat" class="form-horizontal" method="post" id="import_cfat" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="import">Import</label>
                                        <div class="col-md-4">
                                            <input type="file" name="import_cfat" id="import" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <input type="submit" name="submit" class="btn btn-primary" />
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->