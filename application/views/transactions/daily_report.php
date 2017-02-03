<script>
    $(document).ready(function() {
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });
</script>
<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Daily Transactions
            <small>List Daily Transaction</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Daily</li>
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
                        <h3 class="box-title"><!-- Hover Data Table --></h3>
                    </div><!-- /.box-header -->
                    <form action="<?php echo base_url(); ?>index.php/transactions/daily_report" method="post" class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="date">From Date</label>
                                <div class="col-md-4">
                                    <input type="text" name="date" placeholder="yyyy-mm-dd" id="date" class="form-control datepicker" value="<?php echo set_value("date", date("Y-m-d")); ?>" autocomplete="off" />
                                </div>
                                <label class="control-label col-sm-2" for="to_date">To Date</label>
                                <div class="col-md-4">
                                    <input type="text" name="to_date" placeholder="yyyy-mm-dd" id="to_date" class="form-control datepicker" value="<?php echo set_value("to_date", date("Y-m-d")); ?>" autocomplete="off" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="shift">Shift</label>
                                <div class="col-md-4">
                                    <select class="form-control" name="shift" id="shift">
                                        <option value="All">All</option>
                                        <option value="M" <?php if($this->input->post("shift") == "M"){ ?>selected <?php } ?>>Morning</option>
                                        <option value="E" <?php if($this->input->post("shift") == "E"){ ?>selected <?php } ?>>Evening</option>
                                    </select>
                                </div>
                                <label class="control-label col-sm-2" for="customer">Customer</label>
                                <div class="col-md-4">
                                    <select class="form-control" name="customer" id="customer">
                                        <option value="">All Customers</option>
                                        <?php
                                        if(!empty($customers)){
                                            foreach($customers as $row_cust){
                                                ?>
                                                <option value="<?php echo $row_cust->id; ?>" <?php if($this->input->post("customer") == $row_cust->id){ ?>selected <?php } ?>><?php echo $row_cust->customer_name; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2" for="favourite">Favourite Report</label>
                                <div class="col-md-4">
                                    <select class="form-control" name="favourite" id="favourite">
                                        <option value="">Select Report</option>
                                        <?php
                                        if(!empty($favourite_report)){
                                            foreach($favourite_report as $rw_fav){
                                                ?>
                                                <option value="<?php echo $rw_fav['id']; ?>"><?php echo $rw_fav['report_name']; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <input type="submit" name="submit" value="Submit" class="btn btn-primary" />
                            </div>
                        </div>
                    </form>
                    <div class="box-body table-responsive">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Customer</th>
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
                            <tbody></tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div><!-- /.box-body -->
					<?php echo $pagination;?>
					<div class="box-footer clearfix">
                                    <ul class="pagination pagination-sm no-margin pull-right">
                                        <li><a href="#">«</a></li>
                                        <li><a href="#">1</a></li>
                                        <li><a href="#">2</a></li>
                                        <li><a href="#">3</a></li>
                                        <li><a href="#">»</a></li>
                                    </ul>
                                </div>
                </div><!-- /.box -->
            </div>
        </div><!-- /.row -->
    </section><!-- /.content -->
</aside><!-- /.right-side -->