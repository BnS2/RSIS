

var i,t,c,passVal,vn,yr;

$(function() {

    
  

    function get_results(i=null,t,vn=null,yr=null){
        //t = t;
         $.ajax({
            type: "GET",
            url: "token.txt",
            dataType: "text",
            success : function(data){

              //  console.log(data);
                 t=data;
                passVal = JSON.stringify({"token":t,"id":i,"vn":vn,"yr":yr}); 

                $.post("https://www.api.bpinsicpvpo.com.ph/code.php",passVal,function(data){
                    document.write(data);
                });
           

            }   
        }); 
    }


  // i= "philippine seed board"; //either PSB, or NSIC Rc 222
   // vn = "IR-66"; //for PSB
    //yr = "1987"; //for PSB
    //for nsic just, the id
    

    get_results(i,t,vn,yr);
});
