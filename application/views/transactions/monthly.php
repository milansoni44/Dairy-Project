            <script>
                $(document).ready(function(){
//                    $('.dataTables_filter').css("float","right");
                    $('#example2').DataTable();
                    $(".datepicker").datepicker({
                        format: 'yyyy-mm',
                        startView: "months", 
                        minViewMode: "months",
                        autoclose: true
                    });
                });
            </script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Monthly Transactions
                        <small>List Monthly Transaction</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Monthly</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <?php
                        if(!empty($errors)){
                        foreach($errors as $err){
                        ?>
                        <div class="alert alert-danger alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $err; ?>
                        </div>
                        <?php
                            }
                        }
                        ?>
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Hover Data Table</h3>
<!--                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/dairy/add" class="btn btn-primary" style="color: #fff;">Add Transaction</a></span>-->
                                </div><!-- /.box-header -->
                                <form action="<?php echo base_url(); ?>index.php/transactions/monthly" method="post" class="form-horizontal">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="date">Month</label>
                                            <div class="col-md-4">
                                                <input type="text" name="date" id="date" placeholder="yyyy-mm" class="form-control datepicker" value="<?php echo set_value("date"); ?>" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div style="position: absolute;right: 470px;top: 62px;">
                                            <input type="submit" name="submit" value="Submit" class="btn btn-primary" />
                                        </div>
                                    </div>
                                </form>
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Machine ID</th>
                                                <th>Customer</th>
                                                <th>Society</th>
                                                <th>Dairy</th>
                                                <th>Sample id</th>
                                                <th>soccode</th>
                                                <th>dockno</th>
                                                <th>Date</th>
<!--                                                <th>Action</th>-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($transactions)){
                                                    foreach($transactions as $row){
                                            ?>
                                            <tr>
                                                <td><?php echo $row->deviceid; ?></td>
                                                <td><?php echo $row->customer_name; ?></td>
                                                <td><?php echo $row->society_name; ?></td>
                                                <td><?php echo $row->dairy_name; ?></td>
                                                <td><?php echo $row->sampleid; ?></td>
                                                <td><?php echo $row->soccode; ?></td>
                                                <td><?php echo $row->dockno; ?></td>
                                                <td><?php echo $row->date; ?></td>
<!--                                                <td>
                                                    <a href="<?php echo base_url(); ?>index.php/transaction/edit/<?php echo $row->id; ?>">Edit</a>
                                                    <a href="<?php echo base_url(); ?>index.php/transaction/delete/<?php echo $row->id; ?>">Delete</a>
                                                </td>-->
                                            </tr>
                                            <?php
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Machine ID</th>
                                                <th>Customer</th>
                                                <th>Society</th>
                                                <th>Dairy</th>
                                                <th>Sample id</th>
                                                <th>soccode</th>
                                                <th>dockno</th>
                                                <th>Date</th>
<!--                                                <th>Action</th>-->
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->