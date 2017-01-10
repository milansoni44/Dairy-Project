            <script>
                $(document).ready(function(){
//                    $('.dataTables_filter').css("float","right");
                    $('#example2').DataTable();
                });
            </script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Machines
                        <small>List Machines</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Machines</li>
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
                                    <h3 class="box-title">Hover Data Table</h3>
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/machines/add" class="btn btn-primary" style="color: #fff;">Add Machines</a></span>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Machine ID</th>
                                                <th>Dairy</th>
                                                <th>Society</th>
                                                <th>Validity</th>
                                                <th>From Date</th>
                                                <th>To Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($machines)){
                                                    foreach($machines as $row){
                                            ?>
                                            <tr>
                                                <td><?php echo $row->machine_id; ?></td>
                                                <td><?php echo $row->dairy_name; ?></td>
                                                <td><?php echo $row->society_name; ?></td>
                                                <td><?php echo $row->validity; ?></td>
                                                <td><?php echo $row->from_date; ?></td>
                                                <td><?php echo $row->to_date; ?></td>
                                                <td><?php if($row->dairy_name OR $row->society_name){ ?>Active<?php }else{ ?>Deactive<?php } ?></td>
                                                <td>
                                                    <a href="<?php echo base_url(); ?>index.php/machines/edit/<?php echo $row->id; ?>">Edit</a>
                                                    <a href="<?php echo base_url(); ?>index.php/machines/delete/<?php echo $row->id; ?>">Delete</a>
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