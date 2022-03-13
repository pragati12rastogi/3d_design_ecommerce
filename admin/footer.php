		</div>

	</div>


	<script>
		
		$(".top-cat").on('change',function(){
			var id=$(this).val();
			var dataString = 'id='+ id;
			$.ajax
			({
				type: "POST",
				url: "get-mid-category.php",
				data: dataString,
				cache: false,
				success: function(html)
				{
					$(".mid-cat").html(html);
				}
			});			
		});
		$(".mid-cat").on('change',function(){
			var id=$(this).val();
			var dataString = 'id='+ id;
			$.ajax
			({
				type: "POST",
				url: "get-end-category.php",
				data: dataString,
				cache: false,
				success: function(html)
				{
					$(".end-cat").html(html);
				}
			});			
		});

	</script>

	<script>
		$(function () {

			//Initialize Select2 Elements
			$(".select2").select2();

			//Datemask dd/mm/yyyy
			$("#datemask").inputmask("dd-mm-yyyy", {"placeholder": "dd-mm-yyyy"});
			//Datemask2 mm/dd/yyyy
			$("#datemask2").inputmask("mm-dd-yyyy", {"placeholder": "mm-dd-yyyy"});
			//Money Euro
			$("[data-mask]").inputmask();

			//Date picker
			$('.datepicker').datepicker({
			autoclose: true,
			format: 'dd-mm-yyyy',
			todayBtn: 'linked',
			
			});

			$(".datetimepicker").datetimepicker({
				format:'YYYY-MM-DD',
				minDate: new Date(),
				
			});

			
			//iCheck for checkbox and radio inputs
			$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
			checkboxClass: 'icheckbox_minimal-blue',
			radioClass: 'iradio_minimal-blue'
			});
			//Red color scheme for iCheck
			$('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
			checkboxClass: 'icheckbox_minimal-red',
			radioClass: 'iradio_minimal-red'
			});
			//Flat red color scheme for iCheck
			$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
			});

			

			$("#example1").DataTable();
			$('#example2').DataTable({
			"paging": true,
			"lengthChange": false,
			"searching": false,
			"ordering": true,
			"info": true,
			"autoWidth": false
			});

			$('#confirm-delete').on('show.bs.modal', function(e) {
			$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
			});
			
			$('#confirm-approve').on('show.bs.modal', function(e) {
			$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
			});

			$(document).ready(function () {
				
				$(document).on("keydown", "form", function(event) { 
					return event.key != "Enter";
				});

				$('.tag_input').tagsinput({
					onTagExists: function(item, $tag) {
						$tag.hide().fadeIn();
					},
					trimValue: true,
					confirmKeys: [44,13]
					
				});
				$('.tag_input').on('itemAdded', function(event) {
					
					var items = event.currentTarget.value;
					
					trigger_after_tag_add(items);
					
				});

				$('.tag_input').on('itemRemoved', function(event) {
					var items = event.currentTarget.value;
					trigger_after_tag_add(items);
				});

				var tag_input_js = $('.tag_input').tagsinput('input');
				if(tag_input_js != undefined){
					tag_input_js.attr('onkeyup',"this.value = this.value.replace(/,/g, '')");
				}
			});
			

			$("#salepage-saletype").change(function(){
				if( $(this).val() == 'special_sale'){
					$("#sale_name_div").show('slow');

				}else{
					$("#sale_name_div").hide('slow');
				}
			})
		});

		function confirmDelete()
	    {
	        return confirm("Are you sure want to delete this data?");
	    }
	    function confirmActive()
	    {
	        return confirm("Are you sure want to Active?");
	    }
	    function confirmInactive()
	    {
	        return confirm("Are you sure want to Inactive?");
	    }

		function trigger_after_tag_add(items){
			console.log(items);
			if(items != '' && items != undefined){
				$.ajax({
					type:'json',
					method:'post',
					url:'ajax-function.php',
					data:{'tagitems':items},
					success:function(response){
						$("#normally-hidden").empty();
						var str = '<b>Recommended Tags: </b>';

						response = JSON.parse(response);
						$.each(response,function(i,val){
							str += '<a href="javascript:void(0)" onclick="add_newtag(this)" class="badge">'+val+'</a> &nbsp;';
						})

						$("#normally-hidden").append(str);
					}
				})
			}
		}

		function add_newtag(newtag){
			$('.tag_input').tagsinput('add', newtag.text);
		}

		function date_range_filter(){
			var min_date = $("#min-date").val();
			
			if(min_date != ''){
				$("#max-date").datetimepicker('destroy');
				var startDate = new Date(min_date);
				$("#max-date").datetimepicker({
					format:'YYYY-MM-DD',
					minDate: startDate,
				})
			}

		}
		
	</script>

	<script type="text/javascript">
		function showDiv(elem){
			if(elem.value == 0) {
		      	document.getElementById('photo_div').style.display = "none";
		      	document.getElementById('icon_div').style.display = "none";
		   	}
		   	if(elem.value == 1) {
		      	document.getElementById('photo_div').style.display = "block";
		      	document.getElementById('photo_div_existing').style.display = "block";
		      	document.getElementById('icon_div').style.display = "none";
		   	}
		   	if(elem.value == 2) {
		      	document.getElementById('photo_div').style.display = "none";
		      	document.getElementById('photo_div_existing').style.display = "none";
		      	document.getElementById('icon_div').style.display = "block";
		   	}
		}
		function showContentInputArea(elem){
		   if(elem.value == 'Full Width Page Layout') {
		      	document.getElementById('showPageContent').style.display = "block";
		   } else {
		   		document.getElementById('showPageContent').style.display = "none";
		   }
		}

	
	</script>

	<script type="text/javascript">

        $(document).ready(function () {

            $("#btnAddNew").click(function () {

		        var rowNumber = $("#ProductTable tbody tr").length;

		        var trNew = "";              

		        var addLink = "<div class=\"upload-btn" + rowNumber + "\"><input type=\"file\" name=\"photo[]\"  style=\"margin-bottom:5px;\"></div>";
		           
		        var deleteRow = "<a href=\"javascript:void()\" class=\"Delete btn btn-danger btn-xs\">X</a>";

		        trNew = trNew + "<tr> ";

		        trNew += "<td>" + addLink + "</td>";
		        trNew += "<td style=\"width:28px;\">" + deleteRow + "</td>";

		        trNew = trNew + " </tr>";

		        $("#ProductTable tbody").append(trNew);

		    });

		    $('#ProductTable').delegate('a.Delete', 'click', function () {
		        $(this).parent().parent().fadeOut('slow').remove();
		        return false;
		    });

        });



        var items = [];
        for( i=1; i<=24; i++ ) {
        	items[i] = document.getElementById("tabField"+i);
        }

		items[1].style.display = 'block';
		items[2].style.display = 'block';
		items[3].style.display = 'block';
		items[4].style.display = 'none';

		items[5].style.display = 'block';
		items[6].style.display = 'block';
		items[7].style.display = 'block';
		items[8].style.display = 'none';

		items[9].style.display = 'block';
		items[10].style.display = 'block';
		items[11].style.display = 'block';
		items[12].style.display = 'none';

		items[13].style.display = 'block';
		items[14].style.display = 'block';
		items[15].style.display = 'block';
		items[16].style.display = 'none';

		items[17].style.display = 'block';
		items[18].style.display = 'block';
		items[19].style.display = 'block';
		items[20].style.display = 'none';

		items[21].style.display = 'block';
		items[22].style.display = 'block';
		items[23].style.display = 'block';
		items[24].style.display = 'none';

		function funcTab1(elem) {
			var txt = elem.value;
			if(txt == 'Image Advertisement') {
				items[1].style.display = 'block';
		       	items[2].style.display = 'block';
		       	items[3].style.display = 'block';
		       	items[4].style.display = 'none';
			} 
			if(txt == 'Adsense Code') {
				items[1].style.display = 'none';
		       	items[2].style.display = 'none';
		       	items[3].style.display = 'none';
		       	items[4].style.display = 'block';
			}
		};

		function funcTab2(elem) {
			var txt = elem.value;
			if(txt == 'Image Advertisement') {
				items[5].style.display = 'block';
		       	items[6].style.display = 'block';
		       	items[7].style.display = 'block';
		       	items[8].style.display = 'none';
			} 
			if(txt == 'Adsense Code') {
				items[5].style.display = 'none';
		       	items[6].style.display = 'none';
		       	items[7].style.display = 'none';
		       	items[8].style.display = 'block';
			}
		};

		function funcTab3(elem) {
			var txt = elem.value;
			if(txt == 'Image Advertisement') {
				items[9].style.display = 'block';
		       	items[10].style.display = 'block';
		       	items[11].style.display = 'block';
		       	items[12].style.display = 'none';
			} 
			if(txt == 'Adsense Code') {
				items[9].style.display = 'none';
		       	items[10].style.display = 'none';
		       	items[11].style.display = 'none';
		       	items[12].style.display = 'block';
			}
		};

		function funcTab4(elem) {
			var txt = elem.value;
			if(txt == 'Image Advertisement') {
				items[13].style.display = 'block';
		       	items[14].style.display = 'block';
		       	items[15].style.display = 'block';
		       	items[16].style.display = 'none';
			} 
			if(txt == 'Adsense Code') {
				items[13].style.display = 'none';
		       	items[14].style.display = 'none';
		       	items[15].style.display = 'none';
		       	items[16].style.display = 'block';
			}
		};

		function funcTab5(elem) {
			var txt = elem.value;
			if(txt == 'Image Advertisement') {
				items[17].style.display = 'block';
		       	items[18].style.display = 'block';
		       	items[19].style.display = 'block';
		       	items[20].style.display = 'none';
			} 
			if(txt == 'Adsense Code') {
				items[17].style.display = 'none';
		       	items[18].style.display = 'none';
		       	items[19].style.display = 'none';
		       	items[20].style.display = 'block';
			}
		};

		function funcTab6(elem) {
			var txt = elem.value;
			if(txt == 'Image Advertisement') {
				items[21].style.display = 'block';
		       	items[22].style.display = 'block';
		       	items[23].style.display = 'block';
		       	items[24].style.display = 'none';
			} 
			if(txt == 'Adsense Code') {
				items[21].style.display = 'none';
		       	items[22].style.display = 'none';
		       	items[23].style.display = 'none';
		       	items[24].style.display = 'block';
			}
		};



        
    </script>

</body>
</html>