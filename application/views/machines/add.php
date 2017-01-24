<style>
.validation_error {
	border : 3px solid #f00;
}
</style>

<!-- sample machine add form * start -->
	<div class="form-group" style="border: 1px solid; padding: 10px 0; border-radius: 5px; display:none;" id="sample_machine_add">
	   <label class="control-label col-md-2 machine_lable">Machine 1</label>
	   <div class="col-md-2">
		  <input name="machine_id[]" class="form-control machine_id" placeholder="Machine ID" type="text"><br>
		  
		  <select name="validity[]" class="form-control validity">
			 <option value="">Select Validity</option>
			 <option value="3m"> 3 months </option>
			 <option value="6m"> 6 months </option>
			 <option value="9m"> 9 months </option>
			 <option value="1y"> 1 Year </option>
		  </select>
	   </div>
	   <div class="col-md-2">
			<input name="machine_name[]" class="form-control machine_name" placeholder="Machine Name" type="text">
		   <select name="type[]" class="form-control type" style="margin-top:19px;">
			  <option value="">Select Type</option>
			  <option value="USB"> USB </option>
			  <option value="BLUETOOTH"> BLUETOOTH </option>
			  <option value="GPRS"> GPRS </option>
		   </select>
	   </div>
	   <div class="col-md-2">
		   <select name="dairy_id[]" class="form-control dairy_id">
			  <option value="">Select Dairy</option>
		   <?php echo "<pre>"; print_r($dairy_info); echo "</pre>"; foreach( $dairy_info as $dairy ) { ?>
			  <option value="<?php echo $dairy->id;?>">
				<?php echo $dairy->name;?>
			  </option>
		   <?php } ?>
		   </select>
	   </div>
	   <div class="col-md-2">
		   <div class="btn btn-danger remove" style=" border-radius: 100%;font-size: 10px;height: 20px;line-height: 7px;margin-top: 7px;padding-left: 6px;width: 20px;">X</div>
	   </div>
	</div>
<!-- sample machine add form * end -->

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
                                
									<div class="form-group">
										<label class="control-label col-md-2" style="text-align:right;">Number</label>
										<div class="col-md-4">
											<input type="number" name="num" class="form-control" id="num"/> 
										</div>
										<button class="btn btn-primary" id="add_row">Add Row</button>
									</div>
								
                                <form role="form" class="form-horizontal" id="add_machine_form" action="<?php echo base_url(); ?>index.php/machines/add" method="post">
                                    <div class="box-body">
									
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary" id="add_machine_submit_button">Submit</button>
                                    </div>
                                </form>
                            </div><!-- /.box -->
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<script>
				var $add_machine_form = $("#add_machine_form");
					$("#add_machine_submit_button").on("click", function()
					{
						var valid = 1;
						$add_machine_form.find(".machine_id").each(function(index)
						{
							var $this = $(this);
							//console.log( $this.val() );
							if( $this.val() == "" )
							{
								$this.addClass("validation_error")
								.attr("title", "Please insert machine id");
								valid = 0;
							}
							else
							{
								$this.removeClass("validation_error").removeAttr("title");
							}
						})
						.end()
						.find(".machine_name").each(function(index)
						{
							var $this = $(this);
							if( $this.val() == "" )
							{
								$this.addClass("validation_error")
								.attr("title", "Please insert machine name");
								valid = 0;
							}
							else
							{
								$this.removeClass("validation_error").removeAttr("title");
							}
						})
						.end()
						.find(".dairy_id").each(function(index)
						{
							var $this = $(this);
							if( $this.val() == "" )
							{
								$this.addClass("validation_error")
								.attr("title", "Please select dairy");
								valid = 0;
							}
							else
							{
								$this.removeClass("validation_error").removeAttr("title");
							}
						})
						.end()
						.find(".validity").each(function(index)
						{
							var $this = $(this);
							if( $this.val() == "" )
							{
								$this.addClass("validation_error")
								.attr("title", "Please select validity");
								valid = 0;
							}
							else
							{
								$this.removeClass("validation_error").removeAttr("title");
							}
						})
						.end()
						.find(".type").each(function(index)
						{
							var $this = $(this);
							if( $this.val() == "" )
							{
								$this.addClass("validation_error")
								.attr("title", "Please select machine type");
								valid = 0;
							}
							else
							{
								$this.removeClass("validation_error").removeAttr("title");
							}
						});
						
						if(valid==1)
						{
							$add_machine_form.submit();
						}
						else
						{
							//alert("hi false");
							return false;
						}
					});
					
				var $box_body = $add_machine_form.find(".box-body").on("click", ".remove", function()
					{
						$(this).parent().parent().remove();
					})
				  , $sample_machine_add = $("#sample_machine_add")
				  ;
				
				// /*
				$("#num").change(function(e)
				{
					e.preventDefault();
					var $this_val = $(this).val();
					var total_child = $box_body.children(".form-group").length;
					
					if( $this_val > total_child )
					{
						var diff = $this_val - total_child;
						var $fragment = $( document.createDocumentFragment() );
						for(var i=0; i < diff; i++)
						{
							$fragment.append(
								$sample_machine_add.clone().show().removeAttr("id")
								.find(".machine_lable").text("Machine "+ ( total_child+i+1 ) ).end()
							);
						}
						$box_body.append( $fragment );
					}
					else
					{
						$box_body.find(".form-group:eq("+ ($this_val - 1) +")").nextAll().remove();
					}
				}); //*/
				
				$("#add_row").click(function()
				{
					$("#num").trigger("change");
				});
			</script>