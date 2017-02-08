<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Dashboard
                        <small>Control panel</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Dashboard</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
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
                    if($this->session->flashdata("message")){
                    ?>
                    <div class="alert alert-danger alert-dismissable">
                        <i class="fa fa-check"></i>
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                    <?php 
                    }
                    ?>
                    <div class="row">
                        <!-- Left col -->
                        <section class="col-lg-6 connectedSortable">
                            <!-- Box (with bar chart) -->
                            <div class="box box-danger" id="loading-example">
                                <div class="box-header">
                                    <h3 class="box-title">Daily Collection Summary (Morning)</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12">
                                            <!-- bar chart -->
                                            <div id="container_morning"></div>
                                        </div>
                                    </div><!-- /.row - inside box -->
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->

                        </section><!-- /.Left col -->
                        <section class="col-lg-6 connectedSortable">
                            <!-- Box (with bar chart) -->
                            <div class="box box-danger" id="loading-example">
                                <div class="box-header">
                                    <h3 class="box-title">Daily Collection Summary (Evening)</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12">
                                            <!-- bar chart -->
                                            <div id="container_evening"></div>
                                        </div>
                                    </div><!-- /.row - inside box -->
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->

                        </section><!-- /.Left col -->
                    </div><!-- /.row (main row) -->
                    <div class="row">
                        <!-- Left col -->
                        <section class="col-lg-12 col-md-12 connectedSortable">
                            <!-- Box (with bar chart) -->
                            <div class="box box-danger" id="loading-example">
                                <div class="box-header">
                                    <h3 class="box-title">Daily Collection Summary (Morning) <?php echo date('F'); ?> Month</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12">
                                            <!-- bar chart -->
                                            <div id="container_month_morning"></div>
                                        </div>
                                    </div><!-- /.row - inside box -->
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->

                        </section><!-- /.Left col -->
                    </div>

                    <div class="row">
                        <!-- Left col -->
                        <section class="col-lg-12 col-md-12 connectedSortable">
                            <!-- Box (with bar chart) -->
                            <div class="box box-danger" id="loading-example">
                                <div class="box-header">
                                    <h3 class="box-title">Daily Collection Summary (Evening) <?php echo date('F'); ?> Month</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12">
                                            <!-- bar chart -->
                                            <div id="container_month_evening"></div>
                                        </div>
                                    </div><!-- /.row - inside box -->
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->

                        </section><!-- /.Left col -->
                    </div>
                </section>
            </aside><!-- /.right-side -->
			
			<script>
                $(document).ready(function () {
                    $(".highcharts-credits").hide();
                });
	Highcharts.chart('container_morning', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Daily Collection Summary (Morning)'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} ltr',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Type',
        colorByPoint: true,
        data: <?php echo $morning; ?>
    }]
});

Highcharts.chart('container_evening', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Daily Collection Summary (Evening)'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} ltr',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Type',
        colorByPoint: true,
        data: <?php echo $evening; ?>
    }]
});

Highcharts.chart('container_month_morning', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'column'
    },
    title: {
        text: 'Daily Morning shift Collection Summary Cow & Buffalo of <?php echo date('F'); ?> Month'
    },
	xAxis: {
		categories: <?php echo json_encode($month_dates); ?>,
	},
    tooltip: {
        pointFormat: '{series.name}: <b>{point.y:.1f} ltr</b>',
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f}',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Cow',
        data: <?php echo json_encode($monthly_cow_summary); ?>
    }, {
		name: 'Buffalo',
		data: <?php echo json_encode($monthly_buff_summary); ?>
	}]
});

                Highcharts.chart('container_month_evening', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'column'
                    },
                    title: {
                        text: 'Daily Evening shift Collection Summary Cow & Buffalo of <?php echo date('F'); ?> Month'
                    },
                    xAxis: {
                        categories: <?php echo json_encode($month_dates); ?>,
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y:.1f} ltr</b>',
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f}',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Cow',
                        data: <?php echo json_encode($monthly_cow_summary_eve); ?>
                    }, {
                        name: 'Buffalo',
                        data: <?php echo json_encode($monthly_buff_summary_eve); ?>
                    }]
                });
</script>