var targetDate;
var page;

function init() {
  setParam();
  getCalendar();
  getSiteList();
  setEvent();
}
function getCalendar() {
  var url = "calendar.php?date=" + targetDate;
  $.get(url, function(data) {
    $(".calendar").append(data);
  }).done(function() {
    setCalendarStyle();
  });
}

function setCalendarStyle() {
  var today = getDateYMD(new Date());
  $("#calendar_table").find("th").each(function(i) {
    $(this).addClass('calendar_th');
    if (i != 0) {
      $(this).addClass('calendar_th_week');
    }
  });
  $("#calendar_table").find("td").each(function(i) {
    if ($(this).html() != "") {
      $(this).addClass('calendar_td');
    }
    if (i % 2 == 1) {
      $(this).addClass('odd_cell');
    } else {
      $(this).addClass('even_cell');
    }
    if ($(this).html() != "") {
      var thisDate = targetDate.substring(0, 6) + ("0"+$(this).html()).slice(-2);
      if (targetDate == thisDate) {
        $(this).addClass('targetDate');
      }
      if (today >= thisDate) {
        $(this).addClass('calendar_td_ago');
        $(this).on("click", function(){
          var url = "http://3bon.net?date=" + thisDate;
          window.location.href = url;
        });
      }
    }
  })
}

function getSiteList() {
  $.get("category.php", function(data) {
    $(".siteList").append(data);
  }).done(function() {
    setSiteListEvent();
    getRows();
  });
}

function setSiteListEvent() {
//  $(".category_label").on("click", function() {
//    $(this).parent().next().slideToggle();
//  });
  changeCategoryCheck();
  changeSiteCheck();
}

function changeCategoryCheck() {
  $(".category_check").on("change", function() {
    if ($(this).prop("checked")) {
      $(this).parent().next().find("input:checkbox").prop("checked", true);
      $(this).parent().parent().prev().find(".category_check").first().prop("checked", true);
    } else {
      $(this).parent().next().find("input:checkbox").prop("checked", false);
    }
    $(this).parent().next().find(".site_check").trigger("change");
    writeCookie();
  });
}

function writeCookie() {
  var value = "";
  $(".category_check").each(function() {
    if (!$(this).prop("checked")) {
      value = value + $(this).attr("category") + "a";
    }
  });
  var nowtime = new Date().getTime();
  var clear_time = new Date(nowtime + (60 * 60 * 24 * 1000 * 7));
  var expires = clear_time.toGMTString();
  document.cookie = "cg=" +escape(value)+ "; expires=" + expires;
}

function changeSiteCheck() {
  $(".site_check").on("change", function() {
    var siteNoClass = "." + $(this).attr("site");
    if ($(this).prop("checked")) {
      $(siteNoClass).each(function() {
        if($(this).css("display") == "none") {
          $(this).css("display", "");
        }
      });
    } else {
      $(siteNoClass).each(function() {
        if($(this).css("display") != "none") {
          $(this).css("display", "none");
        }
      });
    }
  });
}

function getRows() {
  $.get("get_cst.php", {date: targetDate, url: location.href, p: page}, function(data) {
    $(".rss_row").append(data);
    sort.refresh();
  }).done(function() {
    setLinkEvent();
    readCookie();
  });
}

function setLinkEvent() {
  $(".link").each(function(i) {
    var pageId = $(this).attr('pageId');
    $(this).on("click", function() {
      $.get("countup.php", {pageId: pageId}, function(data) {
        return true;
      });
    });
  });
}

function readCookie() {
  var value;
  if (document.cookie) {
    var cookies = document.cookie.split("; ");
    for (var i = 0; i < cookies.length; i++) {
      var str = cookies[i].split("=");
      var name = str[0];
      if ("cg" == name) {
        value = unescape(str[1]);
      }
    }
    var vs = value.split("a");
    for (var j = 0; j < vs.length; j++) {
      var target = "input[category='" + vs[j] + "']";
      $(target).prop("checked", false);
      $(target).trigger("change");
    }
  }
}

function setParam() {
  var now = new Date();
  targetDate = getDateYMD(now);
  var query = document.location.search.substring(1);
  var parameters = query.split('&');
  var result = new Object();
  for (var i = 0; i < parameters.length; i++) {
    var element = parameters[i].split('=');
    var paramName = decodeURIComponent(element[0]);
    var paramValue = decodeURIComponent(element[1]);
    if ("date" == paramName) {
      if (validDate(paramValue)) {
        targetDate = paramValue;
      }
    }
    page = 0;
    if ("p" == paramName) {
      var p = paramValue;
      if (!isNaN(p)) {
        page = paramValue;
      }
    }
  }
}

function getDateYMD(date) {
  return date.getFullYear()+("0"+(date.getMonth()+1)).slice(-2)+("0"+date.getDate()).slice(-2)
}

function validDate(str) {
  if (str.length != 8) { return false; }
  var y = str.substring(0, 4);
  var m = str.substring(5, 6);
  var d = str.substring(6, 8);
  var date = new Date(y,m-1,d);
  return(date.getFullYear()==y && date.getMonth()==m-1 && date.getDate()==d);
}

function setEvent() {
  $(".prevPage").on("click", function() {
    if (page != 0) { page-- };
    var url = "http://3bon.net?date=" + targetDate + "&p=" + page;
    window.location.href = url;
  });
  $(".nextPage").on("click", function() {
    page++;
    var url = "http://3bon.net?date=" + targetDate + "&p=" + page;
    window.location.href = url;
  });
}
