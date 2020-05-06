(function($, window, document, undefined) {
    Drupal.behaviors.general = {
    attach: function (context) {
      $("a.costar-link").off("click").on("click", function(e) {
        e.preventDefault();
        var nid = $(this).attr('data-nid');
        var movie_nid = $(this).attr('movie-nid');
        console.log(nid);
        console.log(movie_nid);
        console.log(Drupal.url('movie_database/costar' + movie_nid + '/' + nid));
        if (nid) {
          $.ajax({
            // Hitting the url of costar page.
            url: Drupal.url('moviedb/costar/' + movie_nid + '/' + nid),
            type:"POST",
            // data: JSON.stringify(data),
            contentType:"application/json; charset=utf-8",
            dataType:"json",
            // If function return JsonResponse suucessfully pop up a dialouge box.
            success: function(response) {
              console.log(response);
              $(".modal-title").html(response.name);
              $(".modal-body img ").attr("src",response.image);
              $(".modal-body p").html(response.role);
              // When clicked on costar name costar pop up will open.
              $('.costar_popup').show();
              // when click on close button hide the pop-up
              $('.popupCloseButton').click(function(){
                $('.costar_popup').hide();
              });
            }
          });
        }
      });
    }
  }
})
(jQuery, window, document);
