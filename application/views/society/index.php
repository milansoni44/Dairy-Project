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
                        Society
                        <small>List Societies</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Society</li>
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
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/society/add" class="btn btn-primary" style="color: #fff;">Add Society</a></span>
                                    <?php } ?>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <?php if($this->session->userdata("group") == "admin"){ ?>
                                                <th>Dairy Name</th>
                                                <?php } ?>
                                                <th>Society Name</th>
                                                <th>Username</th>
                                                <th>Address</th>
                                                <th>Area</th>
                                                <th>Contact Person</th>
                                                <th>Mobile</th>
                                                <?php if($this->session->userdata("group") == "dairy"){ ?>
                                                <th>Action</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($society)){
                                                    foreach($society as $row){
                                            ?>
                                            <tr>
                                                <?php if($this->session->userdata("group") == "admin"){ ?>
                                                <td><?php echo $row->dairy_name; ?></td>
                                                <?php } ?>
                                                <td><?php echo $row->name; ?></td>
                                                <td><?php echo $row->username; ?></td>
                                                <td><?php echo $row->address; ?></td>
                                                <td><?php echo $row->area; ?></td>
                                                <td><?php echo $row->contact_person; ?></td>
                                                <td><?php echo $row->mobile; ?></td>
                                                <?php if($this->session->userdata("group") == "dairy"){ ?>
                                                <td>
                                                    <a href="<?php echo base_url(); ?>index.php/society/edit/<?php echo $row->id; ?>">Edit</a>
                                                    <a href="<?php echo base_url(); ?>index.php/society/change_status/<?php echo $row->id; ?>" <?php if($row->society_status == '0'){ ?> onclick="return confirm('Are you sure you want to activate?');" <?php }else{ ?> onclick="return confirm('Are you sure you want to deactivate?');" <?php } ?>><?php if($row->society_status == '0'){ ?> Activate<?php }else{ ?>Deactivate <?php } ?></a>
                                                </td>
                                                <?php } ?>
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