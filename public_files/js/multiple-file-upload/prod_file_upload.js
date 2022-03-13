$(document).ready(function() {
    var remove_products_ids =[];
    
    if (window.File && window.FileList && window.FileReader) {

        $("#prod_model_file").on("change", function(e) {
           
            var files = e.target.files,
            filesLength = files.length;
            $("#append_after_list").show();
            for (var i = 0; i < filesLength; i++) {
            
            var f = files[i];
            var fileReader = new FileReader();
            fileReader.fileName = f.name;
            fileReader.size = f.size;
            fileReader.type = f.type;
            fileReader.onload = (function(e) {
                
            var file = e.target;
            console.log(e);
            
            $make_element = "<div  class=\"prod_upload_files row mb_10 \">" +
            "<div class='col-md-10 p_10'><p class=\"imageThumb m_0 \"  title=\"" + e.target.fileName + "\">"+e.target.fileName +"</p>"+
            "<input type='text' style='display:none' name='uploaded_prodfile_path[]' value='"+e.target.result+"'>"+
            "<input type='text' style='display:none' name='uploaded_prodfile_name[]' value='"+e.target.fileName+"'>"+
            "<input type='text' style='display:none' name='uploaded_prodfile_size[]' value='"+e.target.size+"'>"+
            "<input type='text' style='display:none' name='uploaded_prodfile_type[]' value='"+e.target.type+"'>"+
            "</div>"+
            "<div class='col-md-2 center p_10'><button type='button' class=\"btn btn-danger btn-block btn-xs remove\">Delete</button></div>" +
            "</div>";
            
            
            $($make_element).insertAfter("#append_after_list");
            $("#append_after_list").hide();
            $(".remove").click(function(){
                var id = $(this).attr('id');
                remove_products_ids.push(id);
                $(this).parent().parent('.prod_upload_files').remove();
                
            });
            });
            fileReader.readAsDataURL(f);

        }
        // console.log('uploaded_files',JSON.stringify(uploaded_files))
        console.log(files);
        });
    } else {
        alert("Your browser doesn't support to File API")
    }

    
});

function dataURLtoFile(dataurl, filename) {
 
    var arr = dataurl.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), 
        n = bstr.length, 
        u8arr = new Uint8Array(n);
        
    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }
    
    return new File([u8arr], filename, {type:mime});
}