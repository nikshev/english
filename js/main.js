/**
 * User: nikshev
 * Date: 6/19/14
 * Time: 3:57 PM
 */
$(document).ready(function(){
    function getRandomArbitary(min, max)
    {
      return Math.random() * (max - min) + min;
    }

    setInterval(function(){
     var $rowCount = $('#myTable tr').length;

     var $id=parseInt(getRandomArbitary(1,$rowCount));
     /* $("#table tr").eq($id).find('td').find('audio').each(function () {
         $(this)[0].play();
         console.log("Play");
       });*/
     /*var $audio=$("#table tr").eq($id).find('td').find('audio');
     $audio.play();*/
   /*     $('audio').each(function () {
          //  $(this)[0].play();

            console.log($(this).attr('id'));
        });*/
        var MyRows = $('table#htmlTable').find('tbody').find('tr');
        for (var i = 0; i < MyRows.length; i++) {
            var MyIndexValue = $(MyRows[i]).find('td:eq(0)').html();
        }
     console.log("Timer for id="+$id);
    },10000);

    console.log("Document ready");
    //play();
});
