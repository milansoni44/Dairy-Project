            <script>
                $(document).ready(function() {
                    $("#import").on("click", function(e){
                        e.preventDefault();
                        location.href = "<?php echo base_url(); ?>index.php/transactions/import_txn";
                    })
                    $(".datepicker").datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true
                    });
                    var oTable = $('#example2').dataTable({
                        "processing": true,
                        "serverSide": true,
                        <?php if($this->input->post()){ 
                            $from = $this->input->post("date");
                            $to = $this->input->post("to_date");
                            $shift = $this->input->post("shift");
                            $customer = $this->input->post("customer");
                        ?>
                        "sAjaxSource": '<?php echo base_url(); ?>index.php/transactions/get_daily_transaction_post/<?php echo $from; ?>/<?php echo $to; ?>/<?php echo $shift; ?>/<?php echo $customer; ?>',
                        <?php }else{ ?>
                        "sAjaxSource": '<?php echo base_url(); ?>index.php/transactions/get_daily_transaction',
                        <?php } ?>
                        "bJQueryUI": true,
//                        "sPaginationType": "full_numbers",
                        "iDisplayStart ":20,
                        "aLengthMenu": [[10, 15, 25, 35, 50, 100, -1], [10, 15, 25, 35, 50, 100, "All"]],
//                        "oLanguage": {
//                            "sProcessing": "<img src='<?php echo base_url(); ?>assets/images/ajax-loader_dark.gif'>"
//                        },  
                        "fnInitComplete": function() {
                                //oTable.fnAdjustColumnSizing();
                         },
                        'fnServerData': function(sSource, aoData, fnCallback)
                        {
                            $.ajax
                            ({
                                'dataType': 'json',
                                'type'    : 'POST',
                                'url'     : sSource,
                                'data'    : aoData,
                                'success' : fnCallback
                            });
                        },
                        "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                            //when working with pagination if you want to sum all records present in the current visible page only then use below  if block
                            var iDisplayLength = parseInt(iEnd) - parseInt(iStart);
                            if (iStart != 0) {
                              iStart = iStart - iDisplayLength;
                              iEnd = aaData.length;
                            }
                            //columns start from 0, i took 1st column so the line --> aaData[aiDisplay[i]][1]
                            var iLitre = 0;
                            var iNet = 0;
                            var iFat = 0;
                            var iClr = 0;
                            var iSnf = 0;
                            for (var i = iStart; i < iEnd; i++) {
                              iLitre += aaData[aiDisplay[i]][5] * 1; // because you get string in aaData[aiDisplay[i]][1] so multiplying with 1 gives number
                              iNet += aaData[aiDisplay[i]][7] * 1;
                              iFat += aaData[aiDisplay[i]][2] * 1;
                              iClr += aaData[aiDisplay[i]][3] * 1;
                              iSnf += aaData[aiDisplay[i]][4] * 1;
                            }
                            var avg_fat = (iFat/i).toFixed(2);
                            var avg_clr = (iClr/i).toFixed(2);
                            var avg_snf = (iSnf/i).toFixed(2);
                            // Modifying the footer row
                            var nCells = nRow.getElementsByTagName('th');
                            nCells[2].innerHTML = "AVG Fat: "+avg_fat+"%";
                            nCells[3].innerHTML = "AVG CLR: "+avg_clr+"%";
                            nCells[4].innerHTML = "AVG SNF: "+avg_snf+"%";
                            nCells[5].innerHTML = "Total: "+parseFloat(Math.round(iLitre * 100) / 100).toFixed(2);
                            nCells[7].innerHTML = "Total: "+parseFloat(Math.round(iNet * 100) / 100).toFixed(2);
//                            document.getElementById("total_net").innerHTML = "Total Amount : "+parseFloat(Math.round(iNet * 100) / 100).toFixed(2);
//                            document.getElementById("total_litre").innerHTML = "Total Litre  : "+parseFloat(Math.round(iLitre * 100) / 100).toFixed(2);
//                            document.getElementById("avg_fat").innerHTML = "AVG Fat  : "+avg_fat+"%";
//                            document.getElementById("avg_clr").innerHTML = "AVG CLR  : "+avg_clr+"%";
//                            document.getElementById("avg_snf").innerHTML = "AVG Snf  : "+avg_snf+"%";
                        }
                    });
                    
                    var oTable2 = $('#example_buff').dataTable({
                        "processing": true,
                        "serverSide": true,
                        <?php if($this->input->post()){ 
                            $from = $this->input->post("date");
                            $to = $this->input->post("to_date");
                            $shift = $this->input->post("shift");
                            $customer = $this->input->post("customer");
                        ?>
                        "sAjaxSource": '<?php echo base_url(); ?>index.php/transactions/get_daily_buff_transaction_post/<?php echo $from; ?>/<?php echo $to; ?>/<?php echo $shift; ?>/<?php echo $customer; ?>',
                        <?php }else{ ?>
                        "sAjaxSource": '<?php echo base_url(); ?>index.php/transactions/get_daily_buff_transaction',
                        <?php } ?>
                        "bJQueryUI": true,
//                        "sPaginationType": "full_numbers",
                        "iDisplayStart ":20,
                        "aLengthMenu": [[10, 15, 25, 35, 50, 100, -1], [10, 15, 25, 35, 50, 100, "All"]],
//                        "oLanguage": {
//                            "sProcessing": "<img src='<?php echo base_url(); ?>assets/images/ajax-loader_dark.gif'>"
//                        },  
                        "fnInitComplete": function() {
                                //oTable.fnAdjustColumnSizing();
                         },
                        'fnServerData': function(sSource, aoData, fnCallback)
                        {
                            $.ajax
                            ({
                                'dataType': 'json',
                                'type'    : 'POST',
                                'url'     : sSource,
                                'data'    : aoData,
                                'success' : fnCallback
                            });
                        },
                        "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                            //when working with pagination if you want to sum all records present in the current visible page only then use below  if block
                            var iDisplayLength = parseInt(iEnd) - parseInt(iStart);
                            if (iStart != 0) {
                              iStart = iStart - iDisplayLength;
                              iEnd = aaData.length;
                            }
                            //columns start from 0, i took 1st column so the line --> aaData[aiDisplay[i]][1]
                            var iLitre = 0;
                            var iNet = 0;
                            var iFat = 0;
                            var iClr = 0;
                            var iSnf = 0;
                            for (var i = iStart; i < iEnd; i++) {
                              iLitre += aaData[aiDisplay[i]][5] * 1; // because you get string in aaData[aiDisplay[i]][1] so multiplying with 1 gives number
                              iNet += aaData[aiDisplay[i]][7] * 1;
                              iFat += aaData[aiDisplay[i]][2] * 1;
                              iClr += aaData[aiDisplay[i]][3] * 1;
                              iSnf += aaData[aiDisplay[i]][4] * 1;
                            }
                            var avg_fat = (iFat/i).toFixed(2);
                            var avg_clr = (iClr/i).toFixed(2);
                            var avg_snf = (iSnf/i).toFixed(2);
                            // Modifying the footer row
                            var nCells = nRow.getElementsByTagName('th');
                            nCells[2].innerHTML = "AVG Fat: "+avg_fat+"%";
                            nCells[3].innerHTML = "AVG CLR: "+avg_clr+"%";
                            nCells[4].innerHTML = "AVG SNF: "+avg_snf+"%";
                            nCells[5].innerHTML = "Total: "+parseFloat(Math.round(iLitre * 100) / 100).toFixed(2);
                            nCells[7].innerHTML = "Total: "+parseFloat(Math.round(iNet * 100) / 100).toFixed(2);
//                            document.getElementById("total_net").innerHTML = "Total Amount : "+parseFloat(Math.round(iNet * 100) / 100).toFixed(2);
//                            document.getElementById("total_litre").innerHTML = "Total Litre  : "+parseFloat(Math.round(iLitre * 100) / 100).toFixed(2);
//                            document.getElementById("avg_fat").innerHTML = "AVG Fat  : "+avg_fat+"%";
//                            document.getElementById("avg_clr").innerHTML = "AVG CLR  : "+avg_clr+"%";
//                            document.getElementById("avg_snf").innerHTML = "AVG Snf  : "+avg_snf+"%";
                        }
                    });
                    $(document).on("click", '#getUser', function(e){
                        e.preventDefault();

                        var uid = $(this).data('id'); // get id of clicked row
                        $("#dynamic-content").html(''); // leave this div blank
                        $("#modal-loader").show();

                        $.ajax({
                            url: "<?php echo base_url(); ?>index.php/transactions/view",
                            type: 'POST',
                            data: { id: uid },
                            dataType: 'html',
                            success: function(data){
                                console.log(data);
                                $('#dynamic-content').html(''); // blank before load.
                                $('#dynamic-content').html(data); // load here
                                $('#modal-loader').hide(); // hide loader
                            }
                        });
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
                        <?php 
                            if($this->session->flashdata("success")){
                        ?>
                        <div class="alert alert-success alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $this->session->flashdata("success"); ?>
                        </div>
                        <?php
                            }
                        ?>
                        <?php 
                            if($this->session->flashdata("message")){
                        ?>
                        <div class="alert alert-danger alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $this->session->flashdata("message"); ?>
                        </div>
                        <?php
                            }
                        ?>
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title"><!-- Hover Data Table --></h3>
<!--                                    <span class="pull-right" id="total_net" style="font-weight: bold;"></span><br>
                                    <span class="pull-right" id="total_litre" style="font-weight: bold;"></span><br>
                                    <span class="pull-right" id="avg_fat" style="font-weight: bold;"></span><br>
                                    <span class="pull-right" id="avg_clr" style="font-weight: bold;"></span><br>
                                    <span class="pull-right" id="avg_snf" style="font-weight: bold;"></span><br>-->
                                </div><!-- /.box-header -->
                                <div style="text-align: center; font-weight: bold;">
                                    <?php 
                                        $s = date("d-M-Y H:i"); 
                                        if(date("H") < '13'){
                                            echo $s." Morning";
                                        }else{
                                            echo $s." Evening";
                                        }
                                    ?>
                                </div>
                                <div>
                                    <button class="btn btn-primary" id="import">Import Transaction</button>
                                </div>
                                <form action="<?php echo base_url(); ?>index.php/transactions/daily" method="post" class="form-horizontal">
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
                                        <!--<div class="form-group">-->
<!--                                            <label class="control-label col-sm-2" for="to_date">To Date</label>-->
<!--                                            <div class="col-md-4">
                                                <input type="text" name="to_date" placeholder="yyyy-mm-dd" id="to_date" class="form-control datepicker" value="<?php echo set_value("to_date", date("Y-m-d")); ?>" autocomplete="off" />
                                            </div>-->
                                        <!--</div>-->
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
                                                            <option value="<?php echo $row_cust->id; ?>" <?php if($this->input->post("customer") == $row_cust->id){ ?>selected <?php } ?>><?php echo $row_cust->customer_name." - ". $row_cust->adhar_no; ?></option>
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
                                    <div>
                                        <span style="font-weight: bold; font-size: x-large;">Cow</span>
                                        <span class="pull-right" style="margin-bottom: 12px;"><a class="btn btn-primary" href="<?php echo base_url(); ?>index.php/transactions/export_cow/<?php echo ($this->input->post("date")) ? $this->input->post("date") : date('Y-m-d'); ?>/<?php echo ($this->input->post("to_date")) ? $this->input->post("to_date") : date('Y-m-d'); ?>/<?php echo ($this->input->post('shift')) ? $this->input->post('shift'):'All'; ?>/<?php echo ($this->input->post('customer')) ? $this->input->post('customer'):'All'; ?>">Export Cow</a></span>
                                    </div>
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Machine</th>
                                                <th>FAT%</th>
                                                <th>CLR%</th>
                                                <th>SNF%</th>
                                                <th>Litre</th>
                                                <th>Rate/Ltr</th>
                                                <th>Net Amt</th>
                                                <th>Date</th>
                                                <th>Action</th>
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
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div>
                                        <span style="font-weight: bold; font-size: x-large;">Buffalo</span>
                                        <span class="pull-right" style="margin-bottom: 12px; margin-top: 10px;"><a class="btn btn-primary" href="<?php echo base_url(); ?>index.php/transactions/export_buff/<?php echo ($this->input->post("date")) ? $this->input->post("date") : date('Y-m-d'); ?>/<?php echo ($this->input->post("to_date")) ? $this->input->post("to_date") : date('Y-m-d'); ?>/<?php echo ($this->input->post('shift')) ? $this->input->post('shift'):'All'; ?>/<?php echo ($this->input->post('customer')) ? $this->input->post('customer'):'All'; ?>">Export Buffalo</a></span>
                                    </div>
                                    <table id="example_buff" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Machine</th>
                                                <th>FAT%</th>
                                                <th>CLR%</th>
                                                <th>SNF%</th>
                                                <th>Litre</th>
                                                <th>Rate/Ltr</th>
                                                <th>Net Amt</th>
                                                <th>Date</th>
                                                <th>Action</th>
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
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->

            <!-- Modal Pop up for view Transaction-->
            <div id="view-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title">
                                <i class="glyphicon glyphicon-user"></i> View Transaction
                            </h4>
                        </div>

                        <div class="modal-body">
                            <div id="modal-loader" style="display: none; text-align: center;">
                                <!-- ajax loader -->
                                <img src="ajax-loader.gif">
                            </div>

                            <!-- mysql data will be load here -->
                            <div id="dynamic-content"></div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>

                    </div>
                </div>
            </div>
