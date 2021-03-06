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
                        Dairy
                        <small>List Dairies</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Dairy</li>
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
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/dairy/add" class="btn btn-primary" style="color: #fff;">Add Dairy</a></span>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Username</th>
                                                <th>Address</th>
                                                <th>Area</th>
                                                <th>Contact Person</th>
                                                <th>Mobile</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($dairies)){
                                                    foreach($dairies as $row){
                                            ?>
                                            <tr>
                                                <td><?php echo $row->name; ?></td>
                                                <td><?php echo $row->username; ?></td>
                                                <td><?php echo $row->address; ?></td>
                                                <td><?php echo $row->area; ?></td>
                                                <td><?php echo $row->contact_person; ?></td>
                                                <td><?php echo $row->mobile; ?></td>
                                                <td>
                                                    <a href="<?php echo base_url(); ?>index.php/dairy/edit/<?php echo $row->id; ?>">Edit</a>
                                                    <a href="<?php echo base_url(); ?>index.php/dairy/change_status/<?php echo $row->id; ?>" <?php if($row->status == '0'){ ?> onclick="return confirm('Are you sure you want to activate?');" <?php }else{ ?> onclick="return confirm('Are you sure you want to deactivate?');" <?php } ?>><?php if($row->status == '0'){ ?> Activate<?php }else{ ?>Deactivate <?php } ?></a>
                                                </td>
                                            </tr>
                                            <?php
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Name</th>
                                                <th>Username</th>
                                                <th>Address</th>
                                                <th>Area</th>
                                                <th>Contact Person</th>
                                                <th>Mobile</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->