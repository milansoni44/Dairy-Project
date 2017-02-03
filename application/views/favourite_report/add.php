<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Favourite Report
            <small>Add Favourite Report</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add Favourite Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Add Favourite Report</h3>
                    </div><!-- /.box-header -->
                    <form role="form" class="form-horizontal" id="add_favourite_report_form"
                          action="<?php echo base_url(); ?>index.php/favourite_report/<?php echo $action == "add" ? 'insert' : 'update'; ?>"
                          method="post">
                        <div class="box-body">
                            <div class="form-group">
                                <label class="control-label col-md-2" for="report_name">Report Name <span
                                        style="color:red;">*</span></label>
                                <div class="col-md-4">
                                    <input type="text" name="report_name" class="form-control"
                                           value="<?php echo $report_name; ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2" for="period">Period <span
                                        style="color:red;">*</span></label>
                                <div class="col-md-4">
                                    <select name="period" class="form-control" required>
                                        <option value="">Select period</option>
                                        <option value="1" <?php echo $period == 1 ? 'selected' : ''; ?>>Last 7 Days</option>
                                        <option value="2" <?php echo $period == 2 ? 'selected' : ''; ?>>Last Month</option>
<!--                                        <option value="3" --><?php //echo $period == 3 ? 'selected' : ''; ?><!-->Yearly</option>-->
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2" for="shift">Shift <span
                                        style="color:red;">*</span></label>
                                <div class="col-md-4">
                                    <select name="shift" class="form-control" required>
                                        <option value="">Select shift</option>
                                        <option value="All" <?php echo $shift == 'All' ? 'selected' : ''; ?>>All
                                        </option>
                                        <option value="M" <?php echo $shift == 'M' ? 'selected' : ''; ?>>Morning
                                        </option>
                                        <option value="E" <?php echo $shift == 'E' ? 'selected' : ''; ?>>Evening
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2" for="machine_type">Machine Type <span
                                        style="color:red;">*</span></label>
                                <div class="col-md-4">
                                    <select name="machine_type" class="form-control" id="machine_type" required>
                                        <option value="">Select type</option>
                                        <option value="GPRS" <?php echo $machine_type == 'GPRS' ? 'selected' : ''; ?>>
                                            GPRS
                                        </option>
                                        <option
                                            value="BLUETOOTH" <?php echo $machine_type == 'BLUETOOTH' ? 'selected' : ''; ?>>
                                            BLUETOOTH
                                        </option>
                                        <option value="USB" <?php echo $machine_type == 'USB' ? 'selected' : ''; ?>>
                                            USB
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <?php if($this->session->userdata("group") == "dairy"){ ?>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="society">Society <span
                                        style="color: red;">*</span></label>
                                <div class="col-md-4">
                                    <select class="form-control" name="society" id="society" required>
                                        <option value="">Select Society</option>
                                        <?php
                                        if (!empty($society_info)) {
                                            foreach ($society_info as $rw) {
                                                ?>
                                                <option value="<?php echo $rw->id; ?>" <?php echo $society ==  $rw->id ? 'selected' : ''; ?>><?php echo $rw->name; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php }else{ ?>
                                    <input type="hidden" name="society" id="society" value="<?php echo $this->session->userdata('id'); ?>" />
                            <?php } ?>


                        </div>
                        <div class="box-footer">
                            <input type="hidden" name="favourite_report_id"
                                   value="<?php echo $favourite_report_id ? $favourite_report_id : '' ?>">
                            <button type="submit" id="add_favourite_report_submit_button" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </form>
                </div><!-- /.box -->
            </div>
        </div><!-- /.row -->
    </section><!-- /.content -->
</aside><!-- /.right-side -->