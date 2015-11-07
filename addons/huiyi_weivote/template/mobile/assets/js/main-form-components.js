/*
# =============================================================================
#   Sparkline Linechart JS
# =============================================================================
*/


(function() {

  $(document).ready(function() {

    /*
    # =============================================================================
    #   Select2
    # =============================================================================
    */

    $('.select2able').select2();

    /*
    # =============================================================================
    #   Typeahead
    # =============================================================================
    */

    if ($('.typeahead').length) {
      $(".states.typeahead").typeahead({
        name: "省份",
        local: ["安徽", "北京", "重庆", "福建", "甘肃", "广东", "广西", "贵州", "海南", "河北", "黑龙江", "河南", "湖北", "湖南", "内蒙古", "江苏", "江西", "吉林", "辽宁", "宁夏", "青海", "山西", "山东", "上海", "四川", "天津", "西藏", "新疆", "云南", "浙江", "陕西", "台湾", "香港", "澳门"]
      });
      $(".countries.typeahead").typeahead({
        name: "国家",
        local: ["Andorra", "United Arab Emirates", "Afghanistan", "Antigua and Barbuda", "Anguilla", "Albania", "Armenia", "Angola", "Antarctica", "Argentina", "American Samoa", "Austria", "Australia", "Aruba", "Ã…land", "Azerbaijan", "Bosnia and Herzegovina", "Barbados", "Bangladesh", "Belgium", "Burkina Faso", "Bulgaria", "Bahrain", "Burundi", "Benin", "Saint BarthÃ©lemy", "Bermuda", "Brunei", "Bolivia", "Bonaire", "Brazil", "Bahamas", "Bhutan", "Bouvet Island", "Botswana", "Belarus", "Belize", "Canada", "Cocos [Keeling] Islands", "Congo", "Central African Republic", "Republic of the Congo", "Switzerland", "Ivory Coast", "Cook Islands", "Chile", "Cameroon", "China", "Colombia", "Costa Rica", "Cuba", "Cape Verde", "Curacao", "Christmas Island", "Cyprus", "Czechia", "Germany", "Djibouti", "Denmark", "Dominica", "Dominican Republic", "Algeria", "Ecuador", "Estonia", "Egypt", "Western Sahara", "Eritrea", "Spain", "Ethiopia", "Finland", "Fiji", "Falkland Islands", "Micronesia", "Faroe Islands", "France", "Gabon", "United Kingdom", "Grenada", "Georgia", "French Guiana", "Guernsey", "Ghana", "Gibraltar", "Greenland", "Gambia", "Guinea", "Guadeloupe", "Equatorial Guinea", "Greece", "South Georgia and the South Sandwich Islands", "Guatemala", "Guam", "Guinea-Bissau", "Guyana", "Hong Kong", "Heard Island and McDonald Islands", "Honduras", "Croatia", "Haiti", "Hungary", "Indonesia", "Ireland", "Israel", "Isle of Man", "India", "British Indian Ocean Territory", "Iraq", "Iran", "Iceland", "Italy", "Jersey", "Jamaica", "Jordan", "Japan", "Kenya", "Kyrgyzstan", "Cambodia", "Kiribati", "Comoros", "Saint Kitts and Nevis", "North Korea", "South Korea", "Kuwait", "Cayman Islands", "Kazakhstan", "Laos", "Lebanon", "Saint Lucia", "Liechtenstein", "Sri Lanka", "Liberia", "Lesotho", "Lithuania", "Luxembourg", "Latvia", "Libya", "Morocco", "Monaco", "Moldova", "Montenegro", "Saint Martin", "Madagascar", "Marshall Islands", "Macedonia", "Mali", "Myanmar [Burma]", "Mongolia", "Macao", "Northern Mariana Islands", "Martinique", "Mauritania", "Montserrat", "Malta", "Mauritius", "Maldives", "Malawi", "Mexico", "Malaysia", "Mozambique", "Namibia", "New Caledonia", "Niger", "Norfolk Island", "Nigeria", "Nicaragua", "Netherlands", "Norway", "Nepal", "Nauru", "Niue", "New Zealand", "Oman", "Panama", "Peru", "French Polynesia", "Papua New Guinea", "Philippines", "Pakistan", "Poland", "Saint Pierre and Miquelon", "Pitcairn Islands", "Puerto Rico", "Palestine", "Portugal", "Palau", "Paraguay", "Qatar", "RÃ©union", "Romania", "Serbia", "Russia", "Rwanda", "Saudi Arabia", "Solomon Islands", "Seychelles", "Sudan", "Sweden", "Singapore", "Saint Helena", "Slovenia", "Svalbard and Jan Mayen", "Slovakia", "Sierra Leone", "San Marino", "Senegal", "Somalia", "Suriname", "South Sudan", "SÃ£o TomÃ© and PrÃ­ncipe", "El Salvador", "Sint Maarten", "Syria", "Swaziland", "Turks and Caicos Islands", "Chad", "French Southern Territories", "Togo", "Thailand", "Tajikistan", "Tokelau", "East Timor", "Turkmenistan", "Tunisia", "Tonga", "Turkey", "Trinidad and Tobago", "Tuvalu", "Taiwan", "Tanzania", "Ukraine", "Uganda", "U.S. Minor Outlying Islands", "United States", "Uruguay", "Uzbekistan", "Vatican City", "Saint Vincent and the Grenadines", "Venezuela", "British Virgin Islands", "U.S. Virgin Islands", "Vietnam", "Vanuatu", "Wallis and Futuna", "Samoa", "Kosovo", "Yemen", "Mayotte", "South Africa", "Zambia", "Zimbabwe"]
      });
    }
    /*
    # =============================================================================
    #   Form Input Masks
    # =============================================================================
    */

    $(":input").inputmask();
    /*
    # =============================================================================
    #   Validation
    # =============================================================================
    */

    $("#validate-form").validate({
      rules: {
        firstname: "required",
        lastname: "required",
        username: {
          required: true,
          minlength: 2
        },
        password: {
          required: true,
          minlength: 5
        },
        confirm_password: {
          required: true,
          minlength: 5,
          equalTo: "#password"
        },
        email: {
          required: true,
          email: true
        }
      },
      messages: {
        firstname: "请输入您的姓名",
        lastname: "请输入您的职位",
        username: {
          required: "请输入用户名",
          minlength: "您的用户名必须由至少2个以上的字符组成"
        },
        password: {
          required: "请输入密码",
          minlength: "您的密码不能少于5个字符"
        },
        confirm_password: {
          required: "请输入密码",
          minlength: "您的密码不能少于5个字符",
          equalTo: "请输入相同的密码"
        },
        email: "请输入一个有效的邮箱地址"
      }
    });
    /*
    # =============================================================================
    #   Drag and drop files
    # =============================================================================
    */

    $(".single-file-drop").each(function() {
      var $dropbox;
      $dropbox = $(this);
      if (typeof window.FileReader === "undefined") {
        $("small", this).html("File API 或 FileReader API不支持").addClass("text-danger");
        return;
      }
      this.ondragover = function() {
        $dropbox.addClass("hover");
        return false;
      };
      this.ondragend = function() {
        $dropbox.removeClass("hover");
        return false;
      };
      return this.ondrop = function(e) {
        var file, reader;
        e.preventDefault();
        $dropbox.removeClass("hover").html("");
        file = e.dataTransfer.files[0];
        reader = new FileReader();
        reader.onload = function(event) {
          return $dropbox.append($("<img>").attr("src", event.target.result));
        };
        reader.readAsDataURL(file);
        return false;
      };
    });
    /*
    # =============================================================================
    #   File upload buttons
    # =============================================================================
    */

    $('.fileupload').fileupload();
    /*
    # =============================================================================
    #   Datepicker
    # =============================================================================
    */

    $('.datepicker').datepicker();
    nowTemp = new Date();
    now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    checkin = $("#dpd1").datepicker({
      onRender: function(date) {
        if (date.valueOf() < now.valueOf()) {
          return "disabled";
        } else {
          return "";
        }
      }
    }).on("changeDate", function(ev) {
      var newDate;
      if (ev.date.valueOf() > checkout.date.valueOf()) {
        newDate = new Date(ev.date);
        newDate.setDate(newDate.getDate() + 1);
        checkout.setValue(newDate);
      }
      checkin.hide();
      return $("#dpd2")[0].focus();
    }).data("datepicker");
    checkout = $("#dpd2").datepicker({
      onRender: function(date) {
        if (date.valueOf() <= checkin.date.valueOf()) {
          return "disabled";
        } else {
          return "";
        }
      }
    }).on("changeDate", function(ev) {
      return checkout.hide();
    }).data("datepicker");
    /*
    # =============================================================================
    #   Daterange Picker
    # =============================================================================
    */

    $(".date-range").daterangepicker({
      format: "MM/dd/yyyy",
      separator: " to ",
      startDate: Date.today().add({
        days: -29
      }),
      endDate: Date.today(),
      minDate: "01/01/2012",
      maxDate: "12/31/2014"
    });
    /*
    # =============================================================================
    #   Timepicker
    # =============================================================================
    */

    $("#timepicker-default").timepicker();
    $("#timepicker-24h").timepicker({
      minuteStep: 1,
      showSeconds: true,
      showMeridian: false
    });
    $("#timepicker-noTemplate").timepicker({
      template: false,
      showInputs: false,
      minuteStep: 5
    });
    $("#timepicker-modal").timepicker({
      minuteStep: 1,
      secondStep: 5,
      showInputs: false,
      modalBackdrop: true,
      showSeconds: true,
      showMeridian: false
    });
    $("#cp1").colorpicker({
      format: "hex"
    });
    $("#cp2").colorpicker();
    $("#cp3").colorpicker();

  });

}).call(this);
