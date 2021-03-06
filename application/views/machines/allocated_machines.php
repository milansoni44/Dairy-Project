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
                                    <h3 class="box-title"><!-- Hover Data Table --></h3>
                                    <?php if($this->session->userdata("group") == "admin"){ ?>
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/machines/add_allocate" class="btn btn-primary" style="color: #fff;">Allocate Machine</a></span>
                                    <?php } ?>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Machine ID</th>
                                                <th>Machine Name</th>
                                                <th>Machine Type</th>
                                                <th>Validity</th>
                                                <th>Allocation Date</th>
                                                <th>Expiry Date</th>
                                                <th>Society</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($allocated_machines)){
                                                    foreach($allocated_machines as $row){
                                            ?>
                                            <tr <?php echo ($row->status != '1')? 'style="background: red"': '' ?>>
                                                <td><?php echo $row->machine_id; ?></td>
                                                <td><?php echo $row->machine_name; ?></td>
                                                <td><?php echo $row->machine_type; ?></td>
                                                <td><?php echo $row->validity; ?></td>
                                                <td><?php echo $row->from_date; ?></td>
                                                <td><?php echo $row->to_date; ?></td>
                                                <td><?php echo $row->name; ?></td>
                                                <td><?php if($row->status == '1'){ ?> Activate<?php }else{ ?>Deactivate <?php } ?></td>
                                                <td>
                                                    <a href="<?php echo base_url(); ?>index.php/machines/edit_allocate/<?php echo $row->id; ?>">Edit</a>
                                                    <a href="<?php echo base_url(); ?>index.php/machines/change_status/<?php echo $row->id; ?>" <?php if($row->status == '0'){ ?> onclick="return confirm('Are you sure you want to activate?');" <?php }else{ ?> onclick="return confirm('Are you sure you want to deactivate?');" <?php } ?>><?php if($row->status == '0'){ ?> Activate<?php }else{ ?>Deactivate <?php } ?></a>
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