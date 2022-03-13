<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Files</h1>
	</div>
	<div class="content-header-right">
		<a href="file-add.php" class="btn btn-primary btn-sm">Add New</a>
	</div>
</section>


<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">        
                <div class="box-body table-responsive">            
                    <table id="example1" class="table table-bordered table-striped">
    			        <thead>
                            <tr>
                                <th>SL</th>
                                <th>File Title</th>
                                <th>File</th>
                                <th>Download File</th>
                                <th>Copy Link</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php
                        	$i=0;
                        	$statement = $pdo->prepare("SELECT 
                        	                           
            											file_id,
            											file_title,
            											file_name

                        	                           	FROM tbl_file
                        	                           	");
                        	$statement->execute();
                        	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
                        	foreach ($result as $row) {
                    		$i++;
            	            	?>
            	                <tr>
            	                    <td><?php echo $i; ?></td>
            	                    <td><?php echo $row['file_title']; ?></td>
            	                    <td>
            	                    	<?php
            	                    		$t = explode('.',$row['file_name']);
                                            if( $t[1] == 'jpg' || $t[1] == 'JPG' || $t[1] == 'jpeg' || $t[1] == 'JPEG' || $t[1] == 'png' || $t[1] == 'PNG' || $t[1] == 'gif' || $t[1] == 'GIF' )
                                            {
                                                ?>
                                                <img src="../public_files/uploads/<?php echo $row['file_name']; ?>" alt="" style="width:200px;">
                                                <?php
                                            }

                                            elseif( $t[1] == 'doc' || $t[1] == 'DOC' || $t[1] == 'docx' || $t[1] == 'DOCX' )
                                            {
                                                ?>
                                                <img src="../public_files/img/doc.png" alt="" style="width:80px;">
                                                <?php   
                                            }

                                            elseif( $t[1] == 'ppt' || $t[1] == 'PPT' || $t[1] == 'pptx' || $t[1] == 'PPTX' )
                                            {
                                                ?>
                                                <img src="../public_files/img/ppt.png" alt="" style="width:80px;">
                                                <?php   
                                            }

                                            elseif( $t[1] == 'xls' || $t[1] == 'XLS' || $t[1] == 'xlsx' || $t[1] == 'XLSX' )
                                            {
                                                ?>
                                                <img src="../public_files/img/xls.png" alt="" style="width:80px;">
                                                <?php   
                                            }

                                            elseif( $t[1] == 'pdf' || $t[1] == 'PDF' )
                                            {
                                                ?>
                                                <img src="../public_files/img/pdf.png" alt="" style="width:80px;">
                                                <?php   
                                            }
            	                    	?>
            	                    </td>
            	                    <td>
            	                    	<a class="btn btn-warning btn-xs" download="<?php echo $row['file_name']; ?>" href="../public_files/uploads/<?php echo $row['file_name']; ?>" target="_blank">Click Here to Download</a>
            	                    </td>
                                    <td>
                                        <input class="clipboard" type="text" value="../public_files/uploads/<?php echo $row['file_name']; ?>" id="myInput<?php echo $i; ?>">
                                        <button class="btn btn-info btn-xs" onclick="myFunction<?php echo $i; ?>()">Copy link to clipboard</button>
                                        <script>
                                            function myFunction<?php echo $i; ?>() {
                                                var copyText = document.getElementById("myInput<?php echo $i; ?>");
                                                copyText.select();
                                                copyText.setSelectionRange(0, 99999); /*For mobile devices*/
                                                document.execCommand("copy");
                                                alert("Link is Copied to Clipboard");
                                            }
                                        </script>
                                    </td>
            	                    <td>
            	                        <a href="file-edit.php?id=<?php echo $row['file_id']; ?>" class="btn btn-primary btn-xs">Edit</a>
            	                        <a href="#" class="btn btn-danger btn-xs" data-href="file-delete.php?id=<?php echo $row['file_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
            	                    </td>
            	                </tr>
            	                <?php
                        	}
                        	?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                Are you sure want to delete this item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>


<?php require_once('footer.php'); ?>