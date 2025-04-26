$(".input_capital").on('keydown', function(evt) {
    $(this).val(function (_, val) {
      return val + String.fromCharCode(evt.which).toUpperCase();
    });
    
    return false;
});