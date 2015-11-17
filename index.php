<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Facebook Comment Picker</title>
  <style type="text/css">
  body { font-family: sans-serif; }
  p { font-size: 14px; }
  p.console { font-family: monospace; color: red;}
  .tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
  .tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
  .tftable tr {background-color:#ffffff;}
  .tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
  .tftable tr:hover {background-color:#ffff99;}
  .hidden {display: none;}
  span {font-weight: bold;}
  form input {margin: 10px 1px 5px 0; float: right;}
  </style>
  <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
</head>
<body>
  <?php if ( $_GET['auth'] == '7c9ea9fd2bfff6a4533cd6a9cf932f328be4715c' ) { ?>
  <div id="fb-root"></div>
  <div id="main">
      <p>Total comments: <span id="comments_counter" class="hidden"></span></p>
      <p>Total likes: <span id="likes_counter" class="hidden"></span></p>
      <p>Users who comment and like: <span id="both_counter" class="hidden"></span><span id="percent" class="hidden">()</span></p>
      <p class="console"></p>
    <form action="." method="post"><input id="export_to_excel" type="submit" value="Export to Excel"/></form>
    <table class="tftable" border="1">
      <thead>
        <tr>
          <th>User ID</th>
          <th>User Name</th>
          <th>Message</th>
          <th>Timestamp</th>
          <th>Liked post</th>
        </tr>
      </thead>
      <tbody id="output">
        <tr>
          <td style="text-align: center;"><img src="loader.gif"/></td>
          <td style="text-align: center;"><img src="loader.gif"/></td>
          <td style="text-align: center;"><img src="loader.gif"/></td>
          <td style="text-align: center;"><img src="loader.gif"/></td>
          <td style="text-align: center;"><img src="loader.gif"/></td>
        </tr>
      </tbody>
    </table>
  </div>

  <script>

    String.prototype.removeDiacritics = function() {
        var diacritics = [
            [/[\300-\306]/g, 'A'],
            [/[\340-\346]/g, 'a'],
            [/[\310-\313]/g, 'E'],
            [/[\350-\353]/g, 'e'],
            [/[\314-\317]/g, 'I'],
            [/[\354-\357]/g, 'i'],
            [/[\322-\330]/g, 'O'],
            [/[\362-\370]/g, 'o'],
            [/[\331-\334]/g, 'U'],
            [/[\371-\374]/g, 'u'],
            [/[\321]/g, 'N'],
            [/[\361]/g, 'n'],
            [/[\307]/g, 'C'],
            [/[\347]/g, 'c'],
        ];
        var s = this;
        for (var i = 0; i < diacritics.length; i++) {
            s = s.replace(diacritics[i][0], diacritics[i][1]);
        }
        return s;
    }

    $(document).ready(function() {

      var config = {
        appId : '548396958576106',
        accessToken: '<?php echo ( (  isset( $_GET['access_token'] ) ) ? $_GET['access_token'] : '' ); ?>',
        postId : '<?php echo ( (  isset( $_GET['post_id'] ) ) ? $_GET['post_id'] : '' ); ?>',
        limit : '99999',
        flag : true
      };

      var output = $('#output'),
          console = $('p.console'),
          commentCounter = $('#comments_counter'),
          likesCounter = $('#likes_counter'),
          bothCounter = $('#both_counter'),
          percent = $('#percent'),
          btnExport = $('#export_to_excel');

      var getData = function(likes, call) {

        if (typeof(call) != 'undefined') {

          FB.api(call, function(response) {

            if (typeof response.error != 'object') {
              for (var i = 0; i < response.data.length; i++) {
                likes.push(response.data[i].id);
              }

              if (typeof(response.paging.next) != 'undefined') {
                getData(likes, response.paging.next);
              } else {
                getData(likes);
              }
            } else {
              output.html('');
              console.html(response.error.message);
            }


          });

        } else {

          FB.api(config.postId + '/comments?filter=stream&limit=' + config.limit + '&access_token=' + config.accessToken, function(comments) {

            output.html('');
            commentCounter.text(comments.data.length).removeClass('hidden');
            likesCounter.text(likes.length).removeClass('hidden');

            var both = 0;
            for (var i = 0; i < comments.data.length; i++) {
              var row = comments.data[i];
              var liked = (likes.indexOf(row.from.id) != -1);
              both = (liked) ? both + 1 : both;
              output.append('<tr><td><a target="_blank" href="//facebook.com/' + row.from.id + '">' + row.from.id + '</a></td><td>' + row.from.name.removeDiacritics() + '</td><td>' + row.message.removeDiacritics() + '</td><td>' + row.created_time + '</td><td>' + ((liked) ? 'Yes' : 'No') + '</td></tr>');
            };

            bothCounter.text(both).removeClass('hidden');
            percent.text(' (' + (Math.round(both / comments.data.length * 100)) + '%)').removeClass('hidden');

          });

          return true;
        }
      }

      window.fbAsyncInit = function() {
        // init the FB JS SDK
        FB.init({
          appId      : config.appId,                         // 'Comment Picker' App
          status     : true,                                 // Check Facebook Login status
          xfbml      : true                                  // Look for social plugins on the page
        });

        getData([], config.postId + '/likes?fields=id&limit=' + config.limit + '&access_token=' + config.accessToken);

      };

      // Load the SDK asynchronously
      (function(){
         // If we've already installed the SDK, we're done
         if (document.getElementById('facebook-jssdk')) {return;}

         // Get the first script element, which we'll use to find the parent node
         var firstScriptElement = document.getElementsByTagName('script')[0];

         // Create a new script element and set its id
         var facebookJS = document.createElement('script');
         facebookJS.id = 'facebook-jssdk';

         // Set the new script's source to the source of the Facebook JS SDK
         facebookJS.src = '//connect.facebook.net/en_US/all.js';

         // Insert the Facebook JS SDK into the DOM
         firstScriptElement.parentNode.insertBefore(facebookJS, firstScriptElement);
       }());

      // Export to Excel
      var exportToExcel = function() {
        event.preventDefault();
        var csvContent = "data:text/csv;charset=iso-8859-1,\"User Id\";\"User Name\";\"Message\";\"Timestamp\";\"Liked Post\"\n";
        var rows = output.children('tr');
        if (rows.length > 1) {
          for (var i = 0; i < rows.length; i++) {
            var cells = rows[i].children;
            var dataString = "\"" + cells[0].innerText + "\";\"" + cells[1].innerText + "\";\"" + cells[2].innerText + "\";\"" + cells[3].innerText + "\";\"" + cells[4].innerText + "\"\n";
            csvContent += dataString;
          }
        } else {
          alert('Please, wait while data is being loaded...')
        }

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'comments-' + config.postId + '.csv');

        link.click(); // This will download the data file named "my_data.csv".

        return false;
      }

      btnExport.on('click', exportToExcel);

    });
  </script>
  <?php } else { ?>
    <p>Authentication failed.</p>
  <?php } ?>
</body>
</html>