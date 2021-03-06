            <script>
                $(document).ready(function(){
//                    $('.dataTables_filter').css("float","right");
                    $('#example2').DataTable();
                    $("#buffalo_fat_snf").on("click", function(e){
                       e.preventDefault();
                       location.href = "<?php echo base_url(); ?>index.php/rate/import_bfat_snf";
                    });
                    $("#buffalo_fat_snf_csv").on("click", function(e){
                       e.preventDefault();
                       location.href = "<?php echo base_url(); ?>index.php/rate/export_bsnf";
                    });
                });
            </script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Buffalo Fat SNF
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Buffalo Fat SNF</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    
                    <!-- Small boxes (Stat box) -->
                    <div class="row">
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
                                    <?php if($this->session->userdata("group") == "dairy"){ ?>
                                    <span class="pull-right"><button class="btn btn-primary" id="buffalo_fat_snf">Import Buffalo SNF</button></span>
                                    <?php } ?>
                                    <span class="pull-right"><button class="btn btn-primary" id="buffalo_fat_snf_csv">Download SNF</button></span>
                                </div><!-- /.box-header -->
                                <?php if(!empty($fat)){ ?>
                                <div class="box-body table-responsive">
                                    <table id="fixed_hdr1">
                                        <thead>
                                            <tr>
                                                <?php 
                                                    if(!empty($fat)){
                                                        foreach($fat as $r_fat){
                                                ?>
                                                <th><?php echo $r_fat; ?></th>
                                                <?php
                                                        }
                                                    }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($vals)){
                                                    foreach($vals as $row){
                                            ?>
                                            <tr>
                                            <?php
                                                        foreach($row as $in){
                                            ?>
                                                <td><?php echo $in; ?></td>
                                            <?php
                                                        }
                                                    }
                                            ?>
                                            </tr>
                                            <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div><!-- /.box-body -->
                                <?php }else{ ?>
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <th>SNFTAB</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <?php } ?>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->