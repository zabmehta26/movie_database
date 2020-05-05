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
            url: Drupal.url('moviedb/costar/' + movie_nid + '/' + nid),
            type:"POST",
            contentType:"application/json; charset=utf-8",
            dataType:"json",
          });
        }
      });
    }
  }
})
(jQuery, window, document);
