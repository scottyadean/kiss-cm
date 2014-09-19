var AuthJoin = {
   
           gtheqor8  : null,
           onenumber : null,
           oneupper  : null,
           onelower  : null,
   
          init:function(){
               
               $("input[name='password']").val("");
               
               this.oneupper  = $("#pass-one-upper");
               this.onenumber = $("#pass-one-number");
               this.onelower  = $("#pass-one-lower");
               this.gtheqor8  = $("#pass-gtheq-8");
               this.special   = $("#pass-one-special");
               
               
                           
           $("form#join input[name='password']").keyup(function(){
                    var val = $(this).val();
                    AuthJoin.check(val.length >= 8, AuthJoin.gtheqor8);
                    AuthJoin.check(/\d/g.test(val), AuthJoin.onenumber);
                    AuthJoin.check(/[A-Z]/g.test(val), AuthJoin.oneupper);
                    AuthJoin.check(/[a-z]/g.test(val), AuthJoin.onelower);
                    AuthJoin.check(/\W/g.test(val), AuthJoin.special);
             });
               
           },
     
          check:function(res, ele) {
               if(res) {
                   if (ele.find("i").size() == 0) {
                       ele.prepend("<i class='glyphicon glyphicon-thumbs-up color-green'> </i>");
                   }
               }else{
                       ele.find("i").remove();
               }

           }  
   };

    
$(document).ready(function() { AuthJoin.init(); });