      function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
      }

      async function refresh() {
        await sleep(500);
        location.reload(true);
      }

      function checkSettings() {
        settings = [];
        $('.settingval').each(function() {
          settings.push( this.id );
        });
        sLen = settings.length;

        for (i = 0; i < sLen; i++) {
          // Get the checkbox
          var settingCheckBox = document.getElementById(settings[i].slice(0, -2));

          if (document.getElementById(settings[i])) {
            settingCheckBox.checked = true;
          }
        }
      }

      window.onload = checkSettings()

      function changeSetting(elementID,csrftoken) {
        // Get the checkbox
        var settingCheckBox = document.getElementById(elementID);

        // If the checkbox is checked do X
        // First we setup the POST data
        var $elementID = elementID
        var $csrftoken = csrftoken
        var data = {};
        if (settingCheckBox.checked == true){
          data[$elementID] = "True";
          data["csrf_token"] = $csrftoken;
          $.ajax
            ({
              url: '/settings',
              data: data,
              type: 'post',
            });
          } else {
          data[$elementID] = "False";
          data["csrf_token"] = $csrftoken;
          $.ajax
            ({
              url: '/settings',
              data: data,
              type: 'post',
            });
        }
        refresh();
      }

      function postreboot(csrftoken) {
        $.ajax
          ({
            url: '/reboot?reboot=rnow',
            data: {"confirm": "True", "csrf_token":csrftoken},
            type: 'post',
          });
      }

      function postshutdown(csrftoken) {
        $.ajax
          ({
            url: '/reboot?reboot=shutdown',
            data: {"confirm": "True", "csrf_token":csrftoken},
            type: 'post',
          });
      }

      function delUser(user,csrftoken) {
        $.ajax
          ({
            url: '/settings?update=accounting',
            data: {"remove_user": user, "csrf_token":csrftoken},
            type: 'post',
          });
        window.location = "/settings?update=accounting";
      }

