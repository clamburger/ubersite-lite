var currentPage = window.location.pathname;

var index = currentPage.lastIndexOf("/") + 1;
var filename = currentPage.substr(index);

function questionnaire_toggle(obj, type) {
  var par = obj.parentNode;
  var expand = (par.style.height != "auto");

  if(expand) {
    obj.innerHTML = "Minimise this box:";
    par.style.height = "auto";
  } else {
    obj.innerHTML = "Did this elective, click to expand:";
    par.style.height = "15px";
  }
}

function helperText(obj, focus) {
  if (focus && obj.className.indexOf('helper_text') !== -1) {
    obj.value = "";
    obj.className = obj.className.replace(/ ?helper_text/, "");
    return;
  }
  if (!focus && obj.value === "") {
    obj.value = obj.helperText;
    if (obj.className.indexOf('helper_text') === -1) {
      obj.className += ' helper_text';
    }
  }
}

function pair(x, f) {
  return function() {
    f(x);
  };
}

$("button.close").click(function () {
    $(this).parent().slideUp(400, function() {
        if ($(this).siblings().length == 0) {
            $(this).closest("div").slideUp(200);
        }
        $(this).remove();
    });
});