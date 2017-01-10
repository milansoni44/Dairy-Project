            <script>
                $(document).ready(function(){
                    $('#example2').DataTable();
                });
            </script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Milk Suppliers
                        <small>List Milk Supplier</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Milk Suppliers</li>
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
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Hover Data Table</h3>
                                    <?php if($this->session->userdata("group") == "society") {?>
                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/customers/add" class="btn btn-primary" style="color: #fff;">Add Customers</a></span>
                                    <?php } ?>
                                </div><!-- /.box-header -->
                                <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>index.php/customers/society_index">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="society">Society</label>
                                        <div class="col-md-4">
                                            <select class="form-control" name="society" id="society">
                                                <option value="">--Select Society--</option>
                                                <?php 
                                                    if(!empty($society)){
                                                        foreach($society as $row_soc){
                                                ?>
                                                <option value="<?php echo $row_soc->id; ?>" <?php if(isset($_POST['society']) && $_POST['society'] == $row_soc->id){ ?>selected  <?php } ?>><?php echo $row_soc->name; ?></option>
                                                <?php 
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="footer" style="margin-left: 100px;">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary" />
                                    </div>
                                </form>
                                <div class="box-body table-responsive">
                                    <?php if($this->input->post()){ ?>
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Member Code</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Adhar No</th>
                                                <th>Created</th>
                                                <th>Expiry</th>
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
                                                <td><?php echo $row->created_at; ?></td>
                                                <td><?php echo $row->expiry; ?></td>
                                            </tr>
                                            <?php
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php } ?>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->