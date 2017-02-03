            <script>
                $(document).ready(function(){
//                    $('.dataTables_filter').css("float","right");
                    $('#example2').DataTable();
                });
				
				function confirm_delete()
				{
					return confirm("Are your sure to delete this favourite report?");
				}
            </script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Favourite Report
                        <small>List Favourite Report</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Favourite Report</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
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
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/favourite_report/add" class="btn btn-primary" style="color: #fff;">Add Favourite Report</a></span>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Report Name</th>
                                                <th>Period</th>
                                                <th>Shift</th>
                                                <th>Machine Type</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($all_favourite_report))
												{
                                                    foreach($all_favourite_report as $report)
													{
                                            ?>
                                            <tr>
                                                <td><?php echo $report['report_name']; ?></td>
                                                <td><?php echo $report['period_word']; ?></td>
                                                <td><?php echo $report['shift_word']; ?></td>
                                                <td><?php echo $report['machine_type']; ?></td>
                                                <td>
													<a href="<?php echo base_url(); ?>index.php/favourite_report/update/<?php echo $report['id']?>/">Edit</a>
													<a href="<?php echo base_url(); ?>index.php/favourite_report/delete/<?php echo $report['id']?>/" onclick="return confirm_delete()">Delete</a>
                                                    <a href="<?php echo base_url(); ?>index.php/favourite_report/run/<?php echo $report['id']?>/" >Run</a>
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