<script>
    $(document).ready(function () {
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
        var oTable = $('#example2').dataTable({
            "processing": true,
            "serverSide": true,
            <?php if($this->input->post()){ ?>
            "sAjaxSource": '<?php echo base_url(); ?>index.php/transactions/dairy_txn_datatable/<?php echo $this->input->post("society"); ?>',
            <?php }else{ ?>
            "sAjaxSource": '<?php echo base_url(); ?>index.php/transactions/dairy_txn_datatable',
            <?php } ?>
            "bJQueryUI": true,
//                        "sPaginationType": "full_numbers",
            "iDisplayStart ": 20,
            "aLengthMenu": [[10, 15, 25, 35, 50, 100, -1], [10, 15, 25, 35, 50, 100, "All"]],
//                        "oLanguage": {
//                            "sProcessing": "<img src='<?php echo base_url(); ?>assets/images/ajax-loader_dark.gif'>"
//                        },  
            "fnInitComplete": function () {
                //oTable.fnAdjustColumnSizing();
            },
            'fnServerData': function (sSource, aoData, fnCallback)
            {
                $.ajax
                        ({
                            'dataType': 'json',
                            'type': 'POST',
                            'url': sSource,
                            'data': aoData,
                            'success': fnCallback
                        });
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
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
                    iNet += aaData[aiDisplay[i]][5] * 1;
                }
                // Modifying the footer row
//                            var nCells = nRow.getElementsByTagName('th');
//                            nCells[1].innerHTML = parseFloat(Math.round(iLitre * 100) / 100).toFixed(2);
//                            nCells[3].innerHTML = parseFloat(Math.round(iNet * 100) / 100).toFixed(2);
                document.getElementById("total_net").innerHTML = "Total Amount: " + parseFloat(Math.round(iNet * 100) / 100).toFixed(2);
                document.getElementById("total_litre").innerHTML = "Total Litre: " + parseFloat(Math.round(iLitre * 100) / 100).toFixed(2);
            }
        });
    });
</script>
<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Daily Milk Collection
            <small>List Daily Milk Collection</small>
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
            if (!empty($errors)) {
                foreach ($errors as $err) {
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
                        <span class="pull-right" id="total_net" style="font-weight: bold;"></span><br>
                        <span class="pull-right" id="total_litre" style="font-weight: bold;"></span>
                    </div><!-- /.box-header -->
                    <?php if($this->session->userdata("group") == "dairy"){ ?>
                    <form action="<?php echo base_url(); ?>index.php/transactions/daily_txn" method="post" class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="society">Society</label>
                                <div class="col-md-4">
                                    <select class="form-control" name="society" id="society">
                                        <option value="">--Select Society--</option>
                                        <?php
                                        if (!empty($society)) {
                                            foreach ($society as $rw) {
                                                ?>
                                                <option value="<?php echo $rw->id; ?>" <?php if(isset($_POST['society']) && $_POST['society'] == $rw->id){ ?>selected <?php } ?>><?php echo $rw->name; ?></option>
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
                    <?php } ?>
                    <div class="box-body table-responsive">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Society Name</th>
                                    <th>AVG FAT%</th>
                                    <th>AVG CLR%</th>
                                    <th>AVG SNF%</th>
                                    <th>Total Litre</th>
                                    <!--<th>Rate/Ltr</th>-->
                                    <th>Total Amount</th>
                                    <!--<th>Date</th>-->
<!--                                                <th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody></tbody>
<!--                                        <tfoot>
                                <tr>
                                    <th colspan="3" style="text-align:right">Total Litre:</th>
                                    <th></th>
                                    <th>Total Net Amt:</th>
                                    <th></th>
                                </tr>
                            </tfoot>-->
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div><!-- /.row -->
    </section><!-- /.content -->
</aside><!-- /.right-side -->