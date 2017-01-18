            <script>
                $(document).ready(function(){
                    $('#example2').DataTable();
                });
            </script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Milk Producers
                        <small>List Milk Producer</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Milk Producer</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <?php 
                            if($this->session->flashdata("message")){
                                foreach($this->session->flashdata("message") as $error_codes){
                        ?>
                        <div class="alert alert-danger alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $error_codes["Error"]." on line no: ".$error_codes["Line"]; ?>
                        </div>
                        <?php
                                }
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
                        <?php
                        if($this->session->flashdata('message1')){
                        ?>
                        <div class="alert alert-danger alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $this->session->flashdata('message1'); ?>
                        </div>
                        <?php
                            }
                        ?>
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Hover Data Table</h3>
                                    <?php if($this->session->userdata("group") == "society") {?>
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/customers/add" class="btn btn-primary" style="color: #fff;">Add Milk Producer</a></span>
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/customers/import" class="btn btn-primary" style="color: #fff;">Import Milk Producer</a></span>
                                    <?php } ?>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">Member Code</th>
                                                <th style="width: 10%;">Name</th>
                                                <th style="width: 10%;">Mobile</th>
                                                <th style="width: 10%;">Adhar No</th>
                                                <th style="width: 10%;">Type</th>
                                                <th style="width: 30%;">Bank Name - IFSC</th>
                                                <th style="width: 25%;">A/c</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($customers)){
                                                    foreach($customers as $row){
                                            ?>
                                            <tr>
                                                <td><?php echo $row->mem_code; ?></td>
                                                <td><?php echo $row->customer_name; ?></td>
                                                <td><?php echo $row->mobile; ?></td>
                                                <td><?php echo $row->adhar_no; ?></td>
                                                <td><?php echo $row->type; ?></td>
                                                <td><?php echo $row->bank_name." - ". $row->ifsc; ?></td>
                                                <td><?php echo $row->ac_no; ?></td>
                                                <td>
                                                    <a href="<?php echo base_url(); ?>index.php/customers/edit/<?php echo $row->id; ?>">Edit</a>
                                                    <a href="<?php echo base_url(); ?>index.php/customers/delete/<?php echo $row->id; ?>">Delete</a>
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