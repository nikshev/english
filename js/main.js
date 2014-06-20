/**
 * User: nikshev
 * Date: 6/19/14
 * Time: 3:57 PM
 */
$(document).ready(function(){
    var $audio_oth
    function getRandomArbitary(min, max)
    {
      return Math.random() * (max - min) + min;
    }


    setInterval(function(){
     var $rowCount = $('#myTable tr').length;

      var $id=parseInt(getRandomArbitary(0,$rowCount));
      var $rows = $('table#myTable').find('tbody').find('tr');
      var $audio_eng=$($rows[$id]).find('td:eq(0)').find('audio');
      $audio_oth=$($rows[$id]).find('td:eq(1)').find('audio');
      if ($audio_eng[0]!=undefined)
       $audio_eng[0].play();
      setTimeout(function(){
         if ($audio_oth[0]!=undefined)
          $audio_oth[0].play()
       },4000);
      },10000);

     $interval=parseInt($("#interval").val())*60000;
     setTimeout(function(){
          console.log("Click");
          window.location.href = $('a').attr('href');
      },$interval);

});
