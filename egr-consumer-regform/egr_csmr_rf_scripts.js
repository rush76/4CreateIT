$(document).ready(function() {

      $("registerform").ready(function(){
        $(".tmpsave").each(function(){

           cc = $.cookie($(this).attr("name"));
           if ( cc != null ) {
                $(this).val(cc);
           }

        });
    });
    $(".tmpsave").change(function(){

     console.log($(this).attr("name")+" - "+$(this).val());
     $.cookie($(this).attr("name"), $(this).val(), { expires: 365, path: '/' });
   });

   $('#clearData').click(function(){
        $(".tmpsave").each(function(){
        $.cookie($(this).attr("name"), '' , {  expires: -1, path: '/' });
        $(this).val('');
      });
  });

});
