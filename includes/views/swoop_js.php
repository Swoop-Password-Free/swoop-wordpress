<script>
    const configureSwoop = () => {      
      window.swoop.configure({
        endpoint: "http://staging.auth.swoop.email",
        apiEndpoint: "https://api.swoop.email",
        onSuccess: swoopOnSuccess
      });
    }

    const swoopOnSuccess = (user) => {
      let url = "<?php echo wp_login_url(); ?>";      
      if(user) {
        location.href = `${url}?token=${user.id_token}`;
      }
    }

    const swoopOnLoading = () => {
    //   document.getElementById("app").innerHTML = `Logging in...`;
    }

    window.addEventListener('load', function () {
        configureSwoop();
    });
</script>