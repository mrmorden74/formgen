    function rescue_checkbox_state(id) {
      var checkbox = document.getElementById(id);
      if (checkbox.checked != true) {
        checkbox.checked = true;
      } else {
        checkbox.checked = false;
      }
    }