<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Dairysuite | Dashboard</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="<?php echo base_url(); ?>assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="<?php echo base_url(); ?>assets/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Morris chart -->
        <link href="<?php echo base_url(); ?>assets/css/morris/morris.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <link href="<?php echo base_url(); ?>assets/css/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- fullCalendar -->
        <link href="<?php echo base_url(); ?>assets/css/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
        <!-- Daterange picker -->
        <link href="<?php echo base_url(); ?>assets/css/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="<?php echo base_url(); ?>assets/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>assets/js/plugins/datepicker/datepicker3.css" rel="stylesheet" />
        <!-- Theme style -->
        <?php if($this->session->userdata("group") == "dairy"){ ?>
        <link href="<?php echo base_url(); ?>assets/css/AdminLTE_dairy.css" rel="stylesheet" type="text/css" />
        <?php }else if($this->session->userdata("group") == "society"){ ?>
        <link href="<?php echo base_url(); ?>assets/css/AdminLTE_society.css" rel="stylesheet" type="text/css" />
        <?php }else{ ?>
        <link href="<?php echo base_url(); ?>assets/css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <?php } ?>
        <link href="<?php echo base_url(); ?>assets/css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>assets/js/plugins/jquery-validation/demo/css/screen.css" rel="stylesheet" type="text/css" />
        <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
        <!-- daterangepicker -->
        <script src="<?php echo base_url(); ?>assets/js/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>assets/js/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-black">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="<?php echo base_url(); ?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                <?php if($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "admin"){ ?>
                <?php echo ucfirst($this->session->userdata("name")); ?>
                <?php }else{ 
                    echo $this->session->userdata("dairy"); 
                }
                ?>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <span style="position: absolute; line-height: 50px; left: 40%; font-size: 20px; font-weight: 500;">
                    <?php if($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "society"){ ?>
                        <?php echo ucfirst($this->session->userdata("name")); ?>
                    <?php
                    }
                    ?>
                </span>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <?php if($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "society") {?>
                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
								
								<span class="label label-success"><?php echo $total_notification; ?></span>
                            </a>
                            <ul class="dropdown-menu">
							
                                <li class="header">You have <?php echo $total_notification; ?> notifications</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <?php 
                                            if(!empty($notifications)){
                                                foreach($notifications as $noty){
                                        ?>
                                        <li><!-- start message -->
                                            <a href="<?php echo base_url(); ?>index.php/rate">
<!--                                                <div class="pull-left">
                                                    <img src="img/avatar3.png" class="img-circle" alt="User Image"/>
                                                </div>-->
                                                <h4>
                <?php echo str_replace("{society_name}", "you" , $noty['message']); ?>
<!--                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>-->
                                                </h4>
<!--                                                <p><?php //echo "Validity From ". $noty->from_date." to ". $noty->to_date; ?></p>-->
                                            </a>
<!--                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="img/avatar3.png" class="img-circle" alt="User Image"/>
                                                </div>
                                                <h4>
                                                    <?php //echo $noty->message; ?>
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <p><?php //echo "Validity From ". $noty->from_date." to ". $noty->to_date; ?></p>
                                            </a>-->
                                        </li><!-- end message -->
                                        <?php
                                                }
                                            }
                                        ?>
                                    </ul>
                                </li>
                                <?php if($this->session->userdata("group") == "dairy"){ ?>
                                <li class="footer"><a href="<?php echo base_url(); ?>index.php/notification">See All Machines</a></li>
                                <?php } ?>
                                <?php if($this->session->userdata("group") == "society"){ ?>
                                <li class="footer"><a href="<?php echo base_url(); ?>index.php/notification">See All Machines</a></li>
                                <?php } ?>
                            </ul>
                                
                        </li>
                        <?php } ?>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><?php echo $this->session->userdata("username"); ?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img src="<?php echo base_url(); ?>assets/uploads/<?php echo $this->session->userdata("photo"); ?>" class="img-circle" alt="User Image" />
                                    <p>
                                        <?php echo ucfirst($this->session->userdata("username")); ?>
<!--                                        <small>Member since Nov. 2017</small>-->
                                    </p>
                                </li>
                                <!-- Menu Body -->
<!--                                <li class="user-body">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Followers</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </li>-->
                                <!-- Menu Footer-->
                                <li class="user-footer">
<!--                                    <div class="pull-left">
                                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                                    </div>-->
                                    <div class="pull-right">
                                        <a href="<?php echo base_url(); ?>index.php/auth/logout" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?php echo base_url(); ?>assets/uploads/<?php echo $this->session->userdata("photo"); ?>" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p>Hello, <?php echo $this->session->userdata("username"); ?></p>

                            <!--<a href="#"><i class="fa fa-circle text-success"></i> Online</a>-->
                        </div>
                    </div>
                    <!-- search form -->
<!--                    <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search..."/>
                            <span class="input-group-btn">
                                <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>-->
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="active">
                            <a href="<?php echo base_url(); ?>">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        <?php 
                            if($this->session->userdata("group") == "admin"){
                        ?>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Dairy</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/dairy/add"><i class="fa fa-angle-double-right"></i> Add Dairy</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/dairy"><i class="fa fa-angle-double-right"></i> Dairy List</a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <?php if($this->session->userdata("group") == "admin" || $this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "society"){ ?>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Machines</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if($this->session->userdata("group") == "admin"){ ?>
                                <li><a href="<?php echo base_url(); ?>index.php/machines"><i class="fa fa-angle-double-right"></i> Machines <span class="label label-warning" style="float: right;"><?php echo $machine_count->total_allocated."/".$machine_count->total_machine; ?></span></a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/machines/add"><i class="fa fa-angle-double-right"></i> Add Machine</a></li>
<!--                                <li><a href="<?php echo base_url(); ?>index.php/machines/add_allocate"><i class="fa fa-angle-double-right"></i> Add Dairy Machine</a></li>-->
                                <?php } ?>
                                <?php if($this->session->userdata("group") == "dairy"){ ?>
                                <li><a href="<?php echo base_url(); ?>index.php/machines/allocate"><i class="fa fa-angle-double-right"></i> Machines <span class="label label-warning" style="float: right;"><?php echo $machine_count->total_allocated."/".$machine_count->total_machine; ?></span></a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/machines/allocate_to_soc"><i class="fa fa-angle-double-right"></i> Add Society Machine</a></li>
                                <?php } ?>
                                <?php if($this->session->userdata("group") == "society"){ ?>
                                <li><a href="<?php echo base_url(); ?>index.php/machines/allocated_to_society"><i class="fa fa-angle-double-right"></i> Allocated</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <?php } ?>
                        <?php 
                            if($this->session->userdata("group") == "admin" || $this->session->userdata("group") == "dairy"){
                        ?>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Society </span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/society"><i class="fa fa-angle-double-right"></i> Society</a></li>
                                <?php if($this->session->userdata("group") == "dairy"){ ?>
                                <li><a href="<?php echo base_url(); ?>index.php/society/add"><i class="fa fa-angle-double-right"></i> Add Society</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <?php 
                            }
                        ?>
                        <?php if($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "society"){ ?>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Milk Suppliers </span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if($this->session->userdata("group") == "dairy"){ ?>
                                <li><a href="<?php echo base_url(); ?>index.php/customers/society_index"> <span>Milk Supplier</span></a></li>
                                <?php } ?>
                                <?php if($this->session->userdata("group") == "society"){ ?>
                                <li><a href="<?php echo base_url(); ?>index.php/customers"> <span>Milk Supplier</span></a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/customers/add"> <span>Add Milk Supplier</span></a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/customers/import">Import Milk Supplier</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <?php } ?>
                        <?php  ?>
                        <?php

                            //$report_menu = $this->CI->dynamic_report_menu();
                            /*print "<pre>";
                            print_r($report_menu);
                            print "</pre>";*/
                        ?>
                        <!-- changes on 03-02-2017 -->
						<?php if($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "society"){ ?>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Favourite Report</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <!--<li>
									<a href="<?php /*echo base_url(); */?>index.php/favourite_report/"> 	<span>List</span>
									</a>
								</li>-->
                                <li>
									<a href="<?php echo base_url(); ?>index.php/favourite_report/add/"> 	<span>Add</span>
									</a>
								</li>
                            </ul>
                        </li>
                        <?php } ?>

                        <?php if($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "society") { ?>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Milk Collections</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php if($this->session->userdata("group") == "society"){ ?>
                                <li><a href="<?php echo base_url(); ?>index.php/transactions/daily">Milk Collection</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/transactions/import_txn">Import Milk Collection</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/transactions/daily_txn">Milk Collection Summary</a></li>
                                <?php }else if($this->session->userdata("group") == "dairy"){ ?>
                                <li><a href="<?php echo base_url(); ?>index.php/transactions/daily_txn">Milk Collection</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <?php } ?>
                        <!--<li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Reports</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php /*echo base_url(); */?>index.php/transactions/daily_report">Daily Txn</a></li>
                            </ul>
                        </li>-->
                        <?php if($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "society"){ ?>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Buffalo Rate Tables</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/rate">Buffalo Fat</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/rate/bfat_snf">Buffalo Fat SNF</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/rate/bfat_clr">Buffalo Fat CLR</a></li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Cow Rate Tables</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/rate/cfat">Cow Fat</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/rate/cfat_snf">Cow Fat SNF</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/rate/cfat_clr">Cow Fat CLR</a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <span>Favourite Reports</span>
                        <?php
                        $report_menu = $this->CI->dynamic_report_menu();
                        if(!empty($report_menu)){
                            foreach($report_menu as $row_data){
                                ?>
                                <li>
                                    <a href="<?php echo base_url(); ?>index.php/favourite_report/run/<?php echo $row_data['id']; ?>"> 	<span><?php echo ucfirst($row_data['report_name']); ?></span>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>