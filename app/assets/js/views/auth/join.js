var AuthJoin = {
   
           gtheqor8     : null,
           onenumber    : null,
           oneupper     : null,
           onelower     : null,
           
           checkedName  : "",

          init:function(){
            
            /*
            * Set up the password output dom elements
            */
            $("input[name='password']").val("");
            this.oneupper  = $("#pass-one-upper");
            this.onenumber = $("#pass-one-number");
            this.onelower  = $("#pass-one-lower");
            this.gtheqor8  = $("#pass-gtheq-8");
            this.special   = $("#pass-one-special");
            
            
           /*
           * Check password
           */
            $("#join-password").append("<div id='join-password-message'></div>");
            $("form#join input[name='password']").keyup(function(){
                var val = $(this).val().trim();
                AuthJoin.password(val);
            });
               
            /*
           * Check if username already exists
           */
           $("#join-username").append("<div id='join-username-message'></div>");
           $("#join-username label").append("<a class='js-check-username' title='Check username is available'><i class='glyphicon glyphicon-ok'></i><a/>");
           
           $("form#join input[name='username']").blur(function(){
                var val = $(this).val().trim();
                AuthJoin.checkExists(val, {check:val, input:'username'}, AuthJoin.username);     
           });
           $("#js-check-username").click(function(){
                var val = $("form#join input[name='username']").val().trim();
                AuthJoin.checkExists(val, {check:val, input:'username'}, AuthJoin.username);     
           });

           /*
           * Check if user email already exists
           */
           $("#join-email").append("<div id='join-email-message'></div>");
           $("#join-email label").append("<a class='js-check-email' title='Check email is available'><i class='glyphicon glyphicon-ok'></i><a/>");
           
           $("form#join input[name='email']").blur(function(){
                var val = $(this).val().trim();
                
                if (AuthJoin.isValidEmail(val)) {
                    AuthJoin.checkExists(val, {check:val, input:'email'}, AuthJoin.emailexists); 
                }else{
                    
                     $("#join-email-message").html("");
                }
                    
           });
           
           $("#js-check-email").click(function(){
                var val = $("form#join input[name='email']").val().trim();
                if (AuthJoin.isValidEmail(val)) {
                    AuthJoin.checkExists(val, {check:val, input:'email'}, AuthJoin.emailexists);
                }else{
                    
                     $("#join-email-message").html("");
                }
           });

        },
    
        checkExists:function(val, params, callback){
            
             if (val.length > 3) {
                    
                    if (AuthJoin.checkedName != val) {
                        AuthJoin.checkedName = val
                        $.ajax({type: "POST",
                                url: '/auth/check-exist',
                                data: params,
                                success: callback,
                                dataType: 'json'
                        });
                    }
                  }
        },
     
        username:function(data) {
          if (data.success) {
              var msg = !data.found ? AuthJoin.messages('yay', 'Username is available.') : AuthJoin.messages('boo', 'Username is already taken.');  
              $("#join-username #join-username-message").html(msg);
          }   
        },
     
        emailexists:function(data) {

          if (data.success) {
              var msg = !data.found ? AuthJoin.messages('yay', 'Email is available.') : AuthJoin.messages('boo', 'Email is already taken.');  
              $("#join-email-message").html(msg);
          }   
        },
     
        password:function(val){
            
            var res = [];
            
            res.push( AuthJoin.checkPass(val.length >= 8, AuthJoin.gtheqor8) );
            res.push( AuthJoin.checkPass(/\d/g.test(val), AuthJoin.onenumber) );
            res.push( AuthJoin.checkPass(/[A-Z]/g.test(val), AuthJoin.oneupper) );
            res.push( AuthJoin.checkPass(/[a-z]/g.test(val), AuthJoin.onelower) );
            res.push( AuthJoin.checkPass(/\W/g.test(val), AuthJoin.special) );
            
            
            if (val.length < 3) {
                $("#join-password-message").html();
                return;
            }
            
            var password_success = true;
            for(var i =0; i<res.length; i++) {
                if (!res[i]) {
                    password_success = false;            
                    break;
                }
            }
            
            var msg = (password_success) ? AuthJoin.messages('yay', 'Password is secure.')
                                         : AuthJoin.messages('boo', 'Password is not secure.');  
            $("#join-password-message").html(msg);
                
            
        },
     
        checkPass:function(res, ele) {
             if(res) {
                 if (ele.find("i").size() == 0) {
                     ele.prepend("<i class='glyphicon glyphicon-thumbs-up color-green'> </i>");
                 }
                 return true;
             }else{
                     ele.find("i").remove();
                     
                     return false;
             }
             
         },
         
         messages:function(msg, field) {
            
             return msg == 'yay' ? "<span class='label-success rounded-corners-all pad-avg'>Yay! "+field+"</span>" :
                                   "<span class='label-warning rounded-corners-all pad-avg'>Boo... "+field+"</span>";
            
         },
         
         
         isValidEmail:function(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);        
         }
         
   };

    
$(document).ready(function() { AuthJoin.init(); });