            <script>
                $(document).ready(function(){
//                    $('.dataTables_filter').css("float","right");
                    $('#example2').DataTable();
                });
            </script>
            <style>
                th{
                    text-align:center;
                }
            </style>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Transactions
                        <small>List Transaction</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Transaction</li>
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
<!--                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/dairy/add" class="btn btn-primary" style="color: #fff;">Add Transaction</a></span>-->
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Type</th>
                                                <th>FAT%</th>
                                                <th>CLR%</th>
                                                <th>SNF%</th>
                                                <th>Litre</th>
                                                <th>Rate/Ltr</th>
                                                <th>Net Amt</th>
                                                <th>Date</th>
<!--                                                <th>Action</th>-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($transaction))
												{
                                                    foreach($transaction as $row)
													{
                                            ?>
                                            <tr>
                                                <td><?php echo $row->customer_name; ?></td>
                                                <td><?php if($row->type == "C"){ echo "Cow"; }else{ echo "Buffalo"; } ?></td>
                                                <td><?php echo $row->fat; ?></td>
                                                <td><?php echo $row->snf; ?></td>
                                                <td><?php echo $row->weight; ?></td>
                                                <td><?php echo $row->rate; ?></td>
                                                <td><?php echo $row->totalamt; ?></td>
                                                <td><?php echo $row->netamt; ?></td>
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
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->