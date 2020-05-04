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
      });
    }
  }
})
(jQuery, window, document);
