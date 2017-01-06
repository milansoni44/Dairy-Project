            <script>
                $(document).ready(function() {
                    var oTable = $('#example2').dataTable( {
                        "processing": true,
                        "serverSide": true,
                        "sAjaxSource": '<?php echo base_url(); ?>index.php/transactions/datatable',
                        "bJQueryUI": true,
//                        "sPaginationType": "full_numbers",
                        "iDisplayStart ":20,
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
                        }
                    });
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
                                    <h3 class="box-title">Hover Data Table</h3>
<!--                                    <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/dairy/add" class="btn btn-primary" style="color: #fff;">Add Transaction</a></span>-->
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>FAT%</th>
                                                <th>CLR%</th>
                                                <th>SNF%</th>
<!--                                                <th>Action</th>-->
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->