var reg_user_color;
var reg_user_cursor = "pointer";
var valid_mail = true;
var valid_user = true;
var valid_pass = true;

$(document).ready(function() {
    reg_user_color = $('#reg_user').css("backgroundColor");
    $('#username').keyup(check_user);
    $('#username').change(check_user);
    $('#username').on('paste', check_user);
    $('#email').keyup(check_mail);
    $('#email').on('paste', check_mail);
    $('#password').keyup(function() {
    $('#result').html(checkStrength($('#password').val()))
    })
});
function check_user(){
    $.ajax ({
        url : "functions.php",
        method : "GET",
        contentType: 'application/json',
        data :  {op : 0, username : $('#username').val() },
        dataType: "text",
        success: function(html)
        {
            var response = JSON.parse(html);
            if (response['Result']){
                $('#user_result').html("Ya existe el usuario");
                var user = $('#username').val();
                var content = $('#user_result').html();
                $('#user_result').html(content + " " + user);
                $('#user_result').show();
                valid_user = false;
                dis_but();
            } else {
                $('#user_result').hide();
                $('#user_result').html("Ya existe el usuario");
                valid_user = true;
                en_but();
            }
        }
    });
}
function check_mail(){
    $.ajax ({
        url : "functions.php",
        method : "GET",
        contentType: 'application/json',
        data :  {op : 1, email : $('#email').val() },
        dataType: "text",
        success: function(html)
        {
            var response = JSON.parse(html);
            if (response['Result']){
                $('#email_result').html("ya está en uso");
                var email = $('#email').val();
                var content = $('#email_result').html();
                $('#email_result').html(email + " " + content);
                $('#email_result').show();
                valid_mail = false;
                dis_but();
            } else {
                $('#email_result').html("ya está en uso");
                $('#email_result').hide();
                valid_mail = true;
                en_but();
            }
        }
    });
}

function checkStrength(password) {
    var strength = 0
    if (password.length < 6) {
    $('#result').removeClass()
    $('#result').addClass('short')
    valid_pass = false;
    dis_but();
    return 'Too short'
    }
    if (password.length > 7) strength += 1
    // If password contains both lower and uppercase characters, increase strength value.
    if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1
    // If it has numbers and characters, increase strength value.
    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1
    // If it has one special character, increase strength value.
    if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1
    // If it has two special characters, increase strength value.
    if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1
    // Calculated strength value, we can return messages
    // If value is less than 2
    if (strength < 2) {
    $('#result').removeClass()
    $('#result').addClass('weak')
    valid_pass = false;
    dis_but();
    return 'Weak'
    } else if (strength == 2) {
    $('#result').removeClass()
    $('#result').addClass('good')
    valid_pass = true;
    en_but();
    return 'Good'
    } else {
    $('#result').removeClass()
    $('#result').addClass('strong')
    valid_pass = true;
    en_but();
    return 'Strong'
    }
}

function dis_but(){
    $('#reg_user').attr("disabled", true);
    $('#reg_user').css({"background-color": "#ff6e6e", "cursor": "default"});
}
function en_but(){
    if (valid_mail && valid_user && valid_pass){
        $('#reg_user').attr("disabled", false);
        $('#reg_user').css({"background-color": reg_user_color, "cursor": reg_user_cursor});
    }
}