<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Dashboard
                        <small>Control panel</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Dashboard</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
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
                    if($this->session->flashdata("message")){
                    ?>
                    <div class="alert alert-danger alert-dismissable">
                        <i class="fa fa-check"></i>
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                    <?php 
                    }
                    ?>
                </section>
                <div class="row">
                    <!-- Left col -->
                    <section class="col-lg-12 connectedSortable">
                        <!-- Box (with bar chart) -->
                        <div class="box box-danger" id="loading-example">
                            <div class="box-header">
                                <h3 class="box-title">Upcomming Renewal</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body no-padding">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="box-body table-responsive">
                                            <table id="example2" class="table table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th style="width: 5%;">Machine ID</th>
                                                    <th style="width: 10%;">Type</th>
                                                    <th style="width: 10%;">Name</th>
                                                    <th style="width: 10%;">Dairy</th>
                                                    <th style="width: 10%;">Society</th>
                                                    <th style="width: 10%;">From Date</th>
                                                    <th style="width: 30%;">To Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                if(!empty($renewal)){
                                                    foreach($renewal as $row){
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $row->machine_id; ?></td>
                                                            <td><?php echo $row->machine_type; ?></td>
                                                            <td><?php echo $row->machine_name; ?></td>
                                                            <td><?php echo $row->dairy_name; ?></td>
                                                            <td><?php echo $row->society_name; ?></td>
                                                            <td><?php echo $row->from_date; ?></td>
                                                            <td><?php echo $row->to_date; ?></td>
                                                            <td>
                                                                <a href="<?php echo base_url(); ?>index.php/machines/edit/<?php echo $row->id; ?>/renew">Edit</a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div><!-- /.box-body -->
                                    </div>
                                </div><!-- /.row - inside box -->
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->

                    </section><!-- /.Left col -->
                </div>
            </aside><!-- /.right-side -->