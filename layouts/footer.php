<script>
$(document).ready(function () {
        
        
        $('.last_login_2').on("click", function () {
            
            var id = $(this).attr('data-attr');
            var uid = $(this).attr('data-attr-2');
            $.ajax({
                url: "ajaxCall.php",
                type: "POST",
                data: {operation: "last_login", id: id , uid : uid},
                success: function (data) {
                        $('#myModal-2').modal('show');
                        $(".modal-body").html(data);
                }
            });
        });
        
});
 
</script> 
 

</body>
</html>

<?php if(isset($db)) { $db->db_disconnect(); } ?>
