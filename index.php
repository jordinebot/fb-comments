<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Facebook Comment Picker</title>
  <style type="text/css">
  body { font-family: sans-serif; }
  p { font-size: 14px; }
  .tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
  .tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
  .tftable tr {background-color:#ffffff;}
  .tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
  .tftable tr:hover {background-color:#ffff99;}
  </style>
  <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
</head>
<body>
  <?php if ( $_GET['auth'] == '7c9ea9fd2bfff6a4533cd6a9cf932f328be4715c' ) { ?>
  <div id="fb-root"></div>
  <div id="main">
    <p>Total comments: <span id="counter"></span></p>
    <table class="tftable" border="1">
      <thead>
        <tr>
          <th>User ID</th>
          <th>User Name</th>
          <th>Timestamp</th>
          <th>Like count</th>
        </tr>
      </thead>
      <tbody id="output">
        <tr>
          <td style="text-align: center;"><img src="loader.gif"/></td>
          <td style="text-align: center;"><img src="loader.gif"/></td>
          <td style="text-align: center;"><img src="loader.gif"/></td>
          <td style="text-align: center;"><img src="loader.gif"/></td>
        </tr>
      </tbody>
    </table>
  </div>

  <script>

    $(document).ready(function(){

      var config = {
        appId : '548396958576106',
        postId : <?php echo ( (  isset( $_GET['post_id'] ) ) ? $_GET['post_id'] : '614436355280391' ); ?>,
        limit : '100000'
      };

      var output = $('#output');
      var counter = $('#counter');

      window.fbAsyncInit = function() {
        // init the FB JS SDK
        FB.init({
          appId      : config.appId,                         // 'Comment Picker' App
          status     : true,                                 // Check Facebook Login status
          xfbml      : true                                  // Look for social plugins on the page
        });

        // Additional initialization code such as adding Event Listeners goes here
        FB.api(config.postId + '/comments?filter=stream&limit=' + config.limit, function(response) {
          output.html('');
          counter.text(response.data.length);
          for (var i = 0; response.data.length; i++) {
            var row = response.data[i];
            output.append('<tr><td>' + row.from.id + '</td><td>' + row.from.name + '</td><td>' + row.created_time + '</td><td>' + row.like_count + '</td></tr>');
          };

        });

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

    });
  </script>
  <?php } else { ?>
    <p>Authentication failed.</p>
  <?php } ?>
</body>
</html>