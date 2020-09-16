function swoop_render(where, title, componentFunction) {
  jQuery('#swoop_title').html(title);
  jQuery(where).html(componentFunction);
}

function connect() {

  var site = window.state.site;

  swoop = new SwoopConnect("https://connect.swoop.email/auth/swoop");
  // swoop = new SwoopConnect("https://brandon_swoop.ngrok.io/auth/swoop");
  window.swoop = swoop;
  swoop.iFrame(document.getElementById("swoop"), {
      name: site.title,
      url: site.url,
      redirect_uri: site.redirect_uri
    })
    .then(obj => {
      var property = obj.property || obj;
      var organization = obj.organization;

      swoop_connected_ajax(site.siteRoot,
        property.sid,
        property.secret,
        property._id,
        property.name,
        organization._id,
        organization.name);
    });

  return false;
}

function swoop_connected_ajax(siteRoot, client_id, client_secret, property_id, property_name, organization_id, organization_name) {
  jQuery.ajax({
    method: "POST",
    data: {
      action: 'swoop_connected',
      client_id: client_id,
      client_secret: client_secret,
      property_id: property_id,
      property_name: property_name,
      organization_id: organization_id,
      organization_name: organization_name
    },
    url: siteRoot + "/wp-admin/admin-ajax.php",
    success: function(result) {
      var r = JSON.parse(result)
      window.state.swoop = {
        ...window.state.swoop,
        organizationId: organization_id,
        organizationName: organization_name,
        propertyId: property_id,
        propertyName: property_name,
        adminEmail: r.swoop_wp_admin_email,
        adminName: r.swoop_wp_admin,
        connectedDate: r.swoop_wp_connected_date
      };

      swoop_render('#swoop',
      'Swoop there it is. Passwords are officially over!',
      swoop_connected());

    },
    error: function(error) {
      console.log("error")
      console.log(error);
    }
  });
}

function swoop_disconnect_ajax(siteRoot) {
  jQuery.ajax({
    method: "POST",
    data: {
      action: 'swoop_disconnect'
    },
    url: siteRoot + "/wp-admin/admin-ajax.php",
    success: function(result) {
      swoop_render(
        '#swoop',
        'Welcome to a future free of passwords.',
        swoop_buttons()
      );
    },
    error: function(error) {
      console.log("error")
      console.log(error);
    }
  });
}

// Componenents

function swoop_buttons() {
  return '\
  <div class="row col-lg-12 welcome-screen">\
    <div class="col col-md-12 col-lg-5">\
      <span class="users">New Users</span>\
      <a href="#" onclick="return connect();"><button class="create-connect">Create And Connect</button>\
    </div></a>\
    <div class="col col-md-12 col-lg-7">\
      <span class="users">Existing Users</span>\
      <a href="#" onclick="return connect();"><button class="connect">Connect</button></a>\
    </div>\
  </div>\
  <div class="user-copy">\
    <p>New users: When you click “Create and Connect,” you’ll get a Swoop account that you can continue to use for any of your web applications.</p>\
\
    <p>Already have a Swoop account? Just click “Connect” and choose or create a property to add Swoop Password-Free Authentication to.</p>\
  </div>\
  ';
}

function swoop_connected() {

  var swoop = window.state.swoop;

  return '\
  <div class="col-md-12">\
        <div class="card">\
            <div class="card-block">\
                <figure class="profile">\
                  <img class="profile-avatar" src=\'' + swoop.pluginRoot + 'includes/assets/images/security.svg\'>\
                </figure>\
                <h4 class="card-title mt-3">' + swoop.propertyName + '</h4>\
                <div class="card-text">\
                    Connected to Swoop as ' + swoop.organizationName + '/' + swoop.propertyName + ' <span style="display:'+(swoop.adminEmail ? '' : 'none') +'">by <a href="mailto:' + swoop.adminEmail + '"">' + swoop.adminName + '</a></span>\
                </div>\
            </div>\
            <div class="card-footer" style="display:'+(swoop.connectedDate ? '' : 'none') +'">\
                <small>Connected on ' + swoop.connectedDate + '</small>\
                <button onclick="swoop_disconnect_ajax(\''+swoop.siteRoot+'\')" class="float-right btn-sm disconnect">Disconnect</button>\
            </div>\
        </div>\
    </div>\
    <div class="user-copy">\
      <p>By the way, you can connect Swoop password-free authentication to any of your other web properties inside your <a href="https://dashboard.swoop.email" target="_blank">Swoop Dashboard</a>.</p>\
    </div>\
    ';
}
