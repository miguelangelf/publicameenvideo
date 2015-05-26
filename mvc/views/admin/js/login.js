$("#errorlogin").hide();

function checklogin()
{
 
   
    var user=$("#emaillogin").val();
    var password=$("#passwordlogin").val();
    
     var userpass={
         email:user,
         password:password
        
    };
    
     $.post("/site/admin/checklog", userpass, function (response) {
         //alert(response);
         
         if(response==0)
         {
         $("#errorlogin").show();
         }
         else
         {
             window.location ="http://publicameenvideo.local/site/admin/dashboard";
             
         }
       //alert(response);

    });
}