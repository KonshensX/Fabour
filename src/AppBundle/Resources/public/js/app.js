(function(){
  var files;

  $('#userinfo-form').on('submit', function(e){
    e.preventDefault();
    $.post($(this).attr('action'), $(this).serialize())
      .done(function(data){
        notif(data.title, data.message, "lightgreen");
      })
      .fail(function(data){
        alert(data);
      });
  });

  $('#avatar-form').on('submit', function(e){
    e.preventDefault();
    var url = $(this).attr('action');
    var form = document.querySelector('#avatar-form');

    //FormData object and append data to it
    var data = new FormData(form);
    console.log(files[0]);
    console.log(data);
    data.append("0", files[0]);
    data.append("fuck", "fuck a nigga for real");
    console.log(data);
    $.ajax({
      url: url,
      type: 'POST',
      data: data,
      processData: false,
      contentType: false,
      success: function(data, textStatus){
        if(typeof data.error === 'undefined'){
          console.log('data submitted successfully');
          console.log(url);
        }
      },
      error: function(err, textStatus){
        console.log(textStatus+ ' ');
        //console.log(err);
        console.log(url);
      }
    });

  });



  $('input[type=file]').on('change', prepareUpload);

  function prepareUpload(e){
    files = e.target.files;
  }
})(jQuery);

function favedOrNah(url) {
  var button = document.querySelector('#fav');
  $.get(url)
    .done(function(data){
      if(data.title === "favorite") {
        button.innerHTML = "Favorite <3";
      } else if(data.title === "unfavorite") {
        button.innerHTML = "UnFavorite </3";
        button.classList.add('red-haze');
      }
    })
    .fail(function(data){
      console.log(data);
    });
}

function otherSide(url) {
  var button = document.querySelector('#fav');
  $.post(url)
    .done(function(data){
      if(data.title === "favorated"){
        button.innerHTML = "UnFavorite <:3";
        notif("Addded to favorites", "This post has been successfully added to your favorites list", "lightgreen");
        button.classList.remove('green-haze');
        button.classList.add('red-haze');
      } else if(data.title === "deleted"){
        notif("Removed from favorites", "This post has been successfully removed from your favorites list", "red");
        button.innerHTML = "Favorite <3";
        button.classList.remove('red-haze');
        button.classList.add('green-haze');
      }
    })
    .fail(function(data){
      console.log(data);
    });
}

function notif(title, text, bgColor) {
  var $box = $('.notification');
  document.querySelector('.notification h4').innerHTML = title;
  document.querySelector('.notification p').innerHTML = text;
  $box.css("background-color", bgColor);
  $box.animate({
    display: "block",
    right: "10px",
    opacity: "0.9"
  }, 300, "linear", function(){
    setTimeout(function(){
      $box.animate({
        right: "-400px",
        display: "none",
        opacity: "0"
      });
    }, 3000);
  });

}

function previewImage() {
    var preview = document.querySelector('.preview');
    var file = document.querySelector('input[type=file]').files[0];
    var reader = new FileReader();

    reader.onloadend = function() {
      preview.src = reader.result;
    }

    if(file) {
      reader.readAsDataURL(file);
    }
}
//Delete the city from database using ajax
function deleteCity(e, id) {
  e.preventDefault();
  //var url =
}

/*
=========================================================================================================
Toggle items in the profile section Activate/Deactivate
=========================================================================================================
*/

function toggleStatus(e, id) {
  e.preventDefault();
  var $button = document.querySelector("#toggleButton"+id);
  $.get('/fabour/web/app_dev.php/post/toggleStatus/'+id)
      .done(function(data) {
        if (data.message === "deactivated") {
          if ($button.classList.contains("btn-success")) {
            $button.classList.remove("btn-success");
            $button.classList.add("btn-danger");
            $button.innerHTML = "Activate";
          }
        } else if (data.message === "activated"){
          if ($button.classList.contains("btn-danger")) {
            $button.classList.remove("btn-danger");
            $button.classList.add("btn-success");
            $button.innerHTML = "Deactivate";
          }
        }
  });
  //var status = $button.classList;
  //console.log(value);

  // Submit the form
  var contactForm = $("#contactForm");

  contactForm.on('submit', function (e) {
    console.log('hello hhh');
    alert('contactForm was triggered');
    e.preventDefault();
    // Prepare the data that will be sent to the server
    // var data = new FormData(contactForm);
    // Send the  message via AJAX
    $.post(contactForm.attr('action'), {data: 'hello world'})
        .done(function (data) {
          console.log('done');
        })
        .catch(function (err) {
          alert('error')
        });
  })
}