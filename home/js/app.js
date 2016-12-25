
function pager(str) {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var movie = JSON.parse(xmlhttp.responseText);
      // change the title
      var h3 = document.getElementsByClassName("movie-title");
      for (var i in h3) {
        try {
          h3[i].innerHTML = movie[i]['title'];// edit html content
        } catch (e) {
          console.log(e instanceof TypeError); // true
          console.log(e.message);              // "null has no properties"
        }
      }
      // change img
      var img = document.getElementsByClassName("img-responsive");
      for (var a in img) {
        try {
          img[a].src = movie[a]['large_cover_image'];// edit html content
        } catch (e) {
          console.log(e instanceof TypeError); // true
          console.log(e.message);              // "null has no properties"
        }
      }
      //change stream id
      var atag = document.getElementsByClassName("stream-id");
      for (var b in atag) {
        try {
          //regex, gets url, replaces with new id then new movie name, if stream doesnt recognize the movie possibly cause of my regex
          atag[b].href = atag[b].href.replace(/movie=\d+/i, "movie=" + movie[b]['id']);
          atag[b].href = atag[b].href.replace(/name=.+/, "name=" + encodeURI(movie[b]['title']));
        } catch (e) {
          console.log(e instanceof TypeError); // true
          console.log(e.message);              // "null has no properties"
        }
      }
      //check if movie has been watched
      var span = document.getElementsByClassName("movie-watched");
      for (var c in span) {
        try {

        } catch (e) {
          console.log(e instanceof TypeError); // true
          console.log(e.message);              // "null has no properties"
        }
      }
    }
  };
  xmlhttp.open("GET", "{{ path_for('pager' )}}?pager=" + str, true);
  xmlhttp.send();
}
