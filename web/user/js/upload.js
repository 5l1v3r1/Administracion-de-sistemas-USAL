$(document).ready(function(){
    $('#up_man_input').change(function () {
      $('#up_man_p').text(this.files.length + " file(s) selected");
    });
  });