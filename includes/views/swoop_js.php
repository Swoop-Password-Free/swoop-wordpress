<script>
    const configureSwoop = () => {      
      window.swoop.configure({        
        onSuccess: swoopOnSuccess
      });
    }

    const swoopOnSuccess = (user) => {
      let url = "<?php echo site_url() . "/wp-json/" . SWOOP_PLUGIN_NAMESPACE . "/" . SWOOP_PLUGIN_CALLBACK ?>";   

      // Get the user's id_token
      let id_token = user.id_token;

      // Set up the request
      let xhr = new XMLHttpRequest();
      xhr.open('POST', `${url}?token=${user.id_token}`);
      xhr.setRequestHeader('Content-Type', 'application/json');
      // Success callback for the request. Redirect the user to their account
      xhr.onload = function() {
        console.log("Server response:");
        try {
          let response = JSON.parse(xhr.responseText);
          console.log(response);          
          if(response.redirect_to) {
            location.href = response.redirect_to;
          } else if(response.error) {
            alert(response.error);
            console.log(response.error);
          } else {
            alert("Something went wrong. Please try again.");
            console.log("Something went wrong. Please try again.");                      
          }
        } catch(e) {
          console.log(e);          
        }                
        // location.href = '/account';
      };
      // Send the request with the post body of idToken=id_token
      xhr.send('idToken=' + id_token);
    }

    const swoopOnLoading = () => {
    //   document.getElementById("app").innerHTML = `Logging in...`;
    }

    window.addEventListener('load', function () {
        configureSwoop();
    });
</script>