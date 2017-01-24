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
                                    <?php if($this->session->userdata("group") == "dairy"){ ?>
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/machines/allocate_to_soc" class="btn btn-primary" style="color: #fff;">Allocate Machine</a></span>
                                    <?php } ?>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Machine ID</th>
                                                <th>Machine Name</th>
                                                <th>Machine Type</th>
                                                <th>Validity</th>
                                                <?php if($this->session->userdata("group") == "dairy"){ ?>
                                                <th>Actions</th
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($allocated_machines)){
                                                    foreach($allocated_machines as $row){
                                            ?>
                                            <tr>
                                                <td><?php echo $row->machine_id; ?></td>
                                                <td><?php echo $row->machine_name; ?></td>
                                                <td><?php echo $row->machine_type; ?></td>
                                                <td><?php echo $row->validity; ?></td>
                                                <?php if($this->session->userdata("group") == "dairy"){ ?>
                                                <td>
                                                    <a href="<?php echo base_url(); ?>index.php/machines/edit_society_machine/<?php echo $row->id; ?>">Edit</a>
                                                    <a href="<?php echo base_url(); ?>index.php/machines/delete/<?php echo $row->id; ?>">Delete</a>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Machine ID</th>
                                                <th>Machine Name</th>
                                                <th>Machine Type</th>
                                                <th>Validity</th>
                                                <?php if($this->session->userdata("group") == "dairy"){ ?>
                                                <th>Actions</th>
                                                <?php } ?>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->