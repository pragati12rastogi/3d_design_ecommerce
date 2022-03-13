
<script>
    $(document).ready(function(){
        

        $(".top-cat").on('change',function(){
            var id=$(this).val();
            var dataString = 'post_tid='+ id;
            $.ajax
            ({
                type: "POST",
                url: "ajax_function.php",
                data: dataString,
                cache: false,
                success: function(html)
                {
                    $(".mid-cat").html(html);
                }
            });			
        }); 

        $("#btnAddNew_prev").click(function () {

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


        $(document).on("keydown", "form", function(event) { 
            return event.key != "Enter";
        });

        $('.tag_input').tagsinput({
            onTagExists: function(item, $tag) {
                $tag.hide().fadeIn();
            },
            trimValue: true,
            confirmKeys: [44],

            
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
        if(tag_input_js !== undefined){
            tag_input_js.attr('onkeyup',"this.value = this.value.replace(/,/g, '')");
        }
        
        
    })
    
    function trigger_after_tag_add(items){
        console.log(items);
        if(items != '' && items != undefined){
            $.ajax({
                type:'json',
                method:'post',
                url:'ajax_function.php',
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
</script>

<div class="user-sidebar">
    
    <ul>
        <?php if($_SESSION['customer']['customer_type'] == 'both'){?>
            
            <li><a href="dashboard.php"><?php echo DASHBOARD; ?></a></li>
            <li><a href="publishing.php"><?php echo PUBLISHING; ?></a></li>
            <li><a href="my-models.php"><?php echo MYMODELS; ?></a></li>
            <li><a href="my-sales.php"><?php echo MY_SALES; ?></a></li>
            <li><a href="my-purchases.php"><?php echo MY_PURCHASES; ?></a></li>
            <li><a href="account-settings.php"><?php echo ACCOUNT_SETTINGS; ?></a></li>
            
            
        <?php }else if($_SESSION['customer']['customer_type'] == 'selling'){ ?>

            <li><a href="dashboard.php"><?php echo DASHBOARD; ?></a></li>
            <li><a href="publishing.php"><?php echo PUBLISHING; ?></a></li>
            <li><a href="my-models.php"><?php echo MYMODELS; ?></a></li>
            <li><a href="my-sales.php"><?php echo MY_SALES; ?></a></li>
            <li><a href="account-settings.php"><?php echo ACCOUNT_SETTINGS; ?></a></li>

        <?php }else{ ?>

            <li><a href="dashboard.php"><?php echo DASHBOARD; ?></a></li>
            <li><a href="my-purchases.php"><?php echo MY_PURCHASES; ?></a></li>
            <li><a href="account-settings.php"><?php echo ACCOUNT_SETTINGS; ?></a></li>

        <?php } ?>
    </ul>

</div>