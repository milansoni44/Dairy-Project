<script type="text/javascript">
    $(document).ready(function(){
        $("#add_row").on("click", function(e){
            e.preventDefault();
            var n = $("div.box-body div.form-group").length;
            if(n > 1){
                alert("You have already created elements.");
                return false;
            }
            var e= $("div.box-body div.form-group:not(:first)").remove();
            var ele = "";
//            $("#num").attr("disabled", "disabled");
            var num = $("#num").val();
            if(num == 0 || num == ""){
                alert("Please select number.");
                return false;
            }
            for(var i=1; i<=num; i++){
                ele += "<div class='form-group'><label class='control-label col-md-2' for='name'>Machine "+i+"</label><div class='col-md-2'>";
                ele += "<input type='text' name='machine_id[]' id='machine_id_"+i+"' class='form-control' placeholder='Machine ID'/><br>";
                ele += "<select name='validity[]' class='form-control validity'><option value=''>-- Select Validity--</option><option value='3m'> 3 months </option><option value='6m'> 6 months </option><option value='9m'> 9 months </option><option value='1y'> 1 Year </option></select>";
                ele += "</div><input type='text' name='machine_name[]' class='form-control' style='width:15%;' placeholder='Machine Name'/><!--<input type='text' name='date_validity[]' class='form-control reservation' style='width:15%;' placeholder='Validity'/>--><select name='type[]' class='form-control type' style='width:15%; margin-top:19px;'><option value='USB'> USB </option><option value='BLUETOOTH'> BLUETOOTH </option><option value='GPRS'> GPRS </option></select>";
                ele += "<button class='btn btn-danger remove' id='remove-"+i+"' style='margin-top:18px;'>Remove</button></div></div></div>";
            }
            $(ele).insertAfter("div.form-group");
        });
        $('body').on("focus",".reservation", function(){
            $(this).daterangepicker();
        });
        $("div.box-body").on("click", "button.remove", function(){
            $(this).parent().remove();
        });
        
        $("div.box-body").on("change", "select.validity", function(e){
            var $this = $(this);
//            $this.parent().next().val($this.find(":selected").text());
            e.preventDefault();
            var validity = this.value;
            $.ajax({
                url: "<?php echo base_url(); ?>index.php/machines/get_validity",
                type: "POST",
                dataType: 'json',
                data: { validity: validity },
                cache: false,
                success: function(data){
//                    console.log(data.date_range);
                    $this.parent().next().val(data.date_range);
                }
            })
        })
    });
</script>
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Machines
                        <small>Add Machines</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Add Machines</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Add Machines</h3>
                                </div><!-- /.box-header -->
                                <form role="form" class="form-horizontal" id="add_dairy_form" action="<?php echo base_url(); ?>index.php/machines/add" method="post">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="name">Number</label>
                                            <div class="col-md-4">
                                                <input type="number" name="num" class="form-control" id="num"/> 
                                            </div>
                                            <button class="btn btn-primary" id="add_row">Add Row</button>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->