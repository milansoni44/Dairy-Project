            <script>
                $(document).ready(function() {
                    $(".datepicker").datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true
                    });
                    var oTable = $('#example2').dataTable( {
                        "processing": true,
                        "serverSide": true,
                        <?php if($this->input->post()){ 
                            $from = $this->input->post("date");
                            $to = $this->input->post("to_date");
                        ?>
                        "sAjaxSource": '<?php echo base_url(); ?>index.php/transactions/dairy_admin_txn_datatable/<?php echo $from; ?>/<?php echo $to; ?>',
                        <?php }else{ ?>
                        "sAjaxSource": '<?php echo base_url(); ?>index.php/transactions/dairy_admin_txn_datatable',
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
                            for (var i = iStart; i < iEnd; i++) {
                              iLitre += aaData[aiDisplay[i]][4] * 1; // because you get string in aaData[aiDisplay[i]][1] so multiplying with 1 gives number 
                              iNet += aaData[aiDisplay[i]][6] * 1;
                            }
                            // Modifying the footer row
                            var nCells = nRow.getElementsByTagName('th');
                            nCells[1].innerHTML = parseFloat(Math.round(iLitre * 100) / 100).toFixed(2);
                            nCells[3].innerHTML = parseFloat(Math.round(iNet * 100) / 100).toFixed(2);
                        }
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
                                    <h3 class="box-title">Hover Data Table</h3>
<!--                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/dairy/add" class="btn btn-primary" style="color: #fff;">Add Transaction</a></span>-->
                                </div><!-- /.box-header -->
                                <form action="<?php echo base_url(); ?>index.php/transactions/daily_admin" method="post" class="form-horizontal">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="date">From Date</label>
                                            <div class="col-md-4">
                                                <input type="text" name="date" placeholder="yyyy-mm-dd" id="date" class="form-control datepicker" value="<?php echo set_value("date", date("Y-m-d")); ?>" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="to_date">To Date</label>
                                            <div class="col-md-4">
                                                <input type="text" name="to_date" placeholder="yyyy-mm-dd" id="to_date" class="form-control datepicker" value="<?php echo set_value("to_date", date("Y-m-d")); ?>" autocomplete="off" />
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
                                                <th>Society</th>
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
                                                <th colspan="4" style="text-align:right">Total Litre:</th>
                                                <th></th>
                                                <th>Total Net:</th>
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