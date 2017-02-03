<script>
    $(document).ready(function(){
//                    $('.dataTables_filter').css("float","right");
        $('#example2').DataTable({

        });
        $('#example3').DataTable({

        });
    });

    function confirm_delete()
    {
        return confirm("Are your sure to delete this favourite report?");
    }
</script>
<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Run Favourite Report
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Run Favourite Report</li>
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
                <?php
                if($this->session->flashdata('message')){
                    ?>
                    <div class="alert alert-danger alert-dismissable">
                        <i class="fa fa-check"></i>
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                    <?php
                }
                ?>
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><!-- Hover Data Table --></h3>
                        <span class="pull-right"><a href="<?php echo base_url(); ?>index.php/favourite_report/download_cow/<?php echo $id; ?>/csv" class="btn btn-primary" style="color: #fff;">Download</a></span>
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <span style="font-weight: bold; font-size: x-large;">Cow</span>
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <?php if($this->session->userdata("group") == "society"){ ?>
                                    <th>Customer</th>
                                    <?php }else{ ?>
                                    <th>Society</th>
                                    <?php } ?>
                                    <th>FAT%</th>
                                    <th>CLR%</th>
                                    <th>SNF%</th>
                                    <th>Litre</th>
                                    <th>Rate/Ltr</th>
                                    <th>Net Amt</th>
                                    <?php if($this->session->userdata("group") == "society"){ ?>
                                    <th>Date</th>
                                    <?php } ?>
                                    <th>Shift</th>
                                    <!--                                                <th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(!empty($transactions_cow))
                            {
                                foreach($transactions_cow as $rw)
                                {
                                    ?>
                                    <tr>
                                        <?php if($this->session->userdata("group") == "society"){ ?>
                                        <td><?php echo $rw['customer']; ?></td>
                                        <?php }else{ ?>
                                        <td><?php echo $rw['society_name']; ?></td>
                                        <?php } ?>
                                        <td><?php echo $rw['fat']; ?></td>
                                        <td><?php echo $rw['clr']; ?></td>
                                        <td><?php echo $rw['snf']; ?></td>
                                        <td><?php echo $rw['weight']; ?></td>
                                        <td><?php echo $rw['rate']; ?></td>
                                        <td><?php echo $rw['netamt']; ?></td>
                                        <?php if($this->session->userdata("group") == "society"){ ?>
                                        <td><?php echo $rw['date']; ?></td>
                                        <?php } ?>
                                        <td><?php echo $rw['shift']; ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                        <span style="font-weight: bold; font-size: x-large;">Buffalo</span><span class="pull-right"><a href="<?php echo base_url(); ?>index.php/favourite_report/download_buff/<?php echo $id; ?>/csv" class="btn btn-primary" style="color: #fff;">Download</a></span>
                        <table id="example3" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                            <tr>
                                <?php if($this->session->userdata("group") == "society"){ ?>
                                <th>Customer</th>
                                <?php }else{ ?>
                                <th>Society</th>
                                <?php } ?>
                                <th>FAT%</th>
                                <th>CLR%</th>
                                <th>SNF%</th>
                                <th>Litre</th>
                                <th>Rate/Ltr</th>
                                <th>Net Amt</th>
                                <?php if($this->session->userdata("group") == "society"){ ?>
                                    <th>Date</th>
                                <?php } ?>
                                <th>Shift</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(!empty($transactions_buff))
                            {
                                foreach($transactions_buff as $rw)
                                {
                                    ?>
                                    <tr>
                                        <?php if($this->session->userdata("group") == "society"){ ?>
                                        <td><?php echo $rw['customer']; ?></td>
                                        <?php }else{ ?>
                                        <td><?php echo $rw['society_name']; ?></td>
                                        <?php } ?>
                                        <td><?php echo $rw['fat']; ?></td>
                                        <td><?php echo $rw['clr']; ?></td>
                                        <td><?php echo $rw['snf']; ?></td>
                                        <td><?php echo $rw['weight']; ?></td>
                                        <td><?php echo $rw['rate']; ?></td>
                                        <td><?php echo $rw['netamt']; ?></td>
                                        <?php if($this->session->userdata("group") == "society"){ ?>
                                        <td><?php echo $rw['date']; ?></td>
                                        <?php } ?>
                                        <td><?php echo $rw['shift']; ?></td>
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