            <script>
                $(document).ready(function(){
//                    $('.dataTables_filter').css("float","right");
                    $('#example2').DataTable();
                    $("#cow_fat").on("click", function(e){
                       e.preventDefault();
                       location.href = "<?php echo base_url(); ?>index.php/rate/import_cfat";
                    });
                    $("#cow_fat_csv").on("click", function(e){
                       e.preventDefault();
                       location.href = "<?php echo base_url(); ?>index.php/rate/export_cfat";
                    });
                });
            </script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Cow Rate
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Cow Rate</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
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
                                    <?php if($this->session->userdata("group") == "dairy"){ ?>
                                    <span class="pull-right"><button class="btn btn-primary" id="cow_fat">Import Cow Fat</button></span>
                                    <?php } ?>
                                    <span class="pull-right"><button class="btn btn-primary" id="cow_fat_csv">Download</button></span>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fat</th>
                                                <th>Rate</th>
                                                <!--<th>Action</th>-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($c_rate)){
                                                    foreach($c_rate as $row){
                                            ?>
                                            <tr>
                                                <td><?php echo $row->Fat; ?></td>
                                                <td><?php echo $row->Rate; ?></td>
<!--                                                <td>
                                                    <a href="<?php echo base_url(); ?>index.php/customers/edit/<?php echo $row->Fat; ?>">Edit</a>
                                                    <a href="<?php echo base_url(); ?>index.php/customers/delete/<?php echo $row->Fat; ?>">Delete</a>
                                                </td>-->
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