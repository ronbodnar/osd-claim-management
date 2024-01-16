var fileLocation = $("script[src*=script]")
  .attr("src")
  .replace(/script\.js.*$/, "");

function resubmitForm() {
  $("#failure").hide();
  $("#default").removeAttr("hidden");
}

// Uploaded image array from OS&D form
var pictures = {};

/*
 * Insert image source below the camera/file input for users to see what they have uploaded.
 * Add image source to array of pictures
 */
function displayImages(files) {
  if (files && files[0]) {
    for (let i = 0; i < files.length; i++) {
      let reader = new FileReader();

      reader.onload = function (e) {
        var fileName = files.item(i).name;
        pictures[fileName] = e.target.result;
        $("#selectedFiles").append(
          '<div class="col-md-4 p-2 container-img" id="' +
            fileName +
            '"><img src="' +
            e.target.result +
            '" width="100" height="100"><i class="bi bi-trash-fill removePhoto" id="' +
            fileName +
            '"></i></div>'
        );
      };

      reader.readAsDataURL(files[i]);
    }
  }
}

/*
 * Validates all of the form fields for the OS&D form and styles them accordingly
 */
function validateOSDForm() {
  var valid = true;
  const fields = ["tripNumber", "fbNumber", "cases", "trailerNumber", "productNumber"];
  const types = ["overage", "shortage", "damage"];

  for (var i = 0; i < fields.length; i++) {
    if (!isValid(fields[i])) {
      valid = false;
    }
  }

  for (var i = 0; i < types.length; i++) {
    if (!$("#" + types[0]).prop("checked") && !$("#" + types[1]).prop("checked") && !$("#" + types[2]).prop("checked")) {
      $("#overage").addClass("is-invalid");
      $("#shortage").addClass("is-invalid");
      $("#damage").addClass("is-invalid");
      valid = false;
    }
  }

  if (!$("#yCheck").prop("checked") && !$("#nCheck").prop("checked")) {
    $("#yesLabel").addClass("invalid");
    $("#yesCheck").addClass("invalid");
    $("#noLabel").addClass("invalid");
    $("#noCheck").addClass("invalid");
    $("#noLabel").addClass("is-invalid");
    valid = false;
  }

  if ($("#cameraInput").get(0).files.length === 0) {
    $("#cameraInput").addClass("is-invalid");
    valid = false;
  }

  return valid;
}

// Used for claim details image tab - avoid loading images when they were already loaded
var imagesLoaded = false;

/*
 * Document is ready and custom hooks can be performed
 */
$(document).ready(function () {
  $("#back").on("click", function() {
    console.log("hey");
    window.location.replace('/projects/osd-claims');
  });

  $("#claimStatusDropdown .dropdown-item").click(function (e) {
    var selected = $(this).html();
    var dropdownButton = $("#claimStatusDropdownButton");
    dropdownButton.html(selected);
    dropdownButton.removeClass(function (index, className) {
      return (className.match(/(^|\s)btn-outline-\S+/g) || []).join(" ");
    });
    let outlines = { Processing: "info", Pending: "warning", Complete: "success" };
    dropdownButton.addClass("btn-outline-" + outlines[selected]);

    // Update claim status in the database
    var parameters = window.location.search.substring(1);
    var split = parameters.split("=");
    var paramValue = split[1];
    var status = (selected == "Processing" ? 2 : selected === "Pending" ? 3 : 4);
    $.ajax({
      type: "POST",
      url: "process.php",
      data: { id: paramValue, status: status }
    }).done(function (data) {
      console.log(data);
    });
    //TODO:
    // - Update outline of button
    // - Update claim status
  });
  $("#images-tab").click(function () {
    if (!imagesLoaded) {
      var parameters = window.location.search.substring(1);
      var split = parameters.split("=");
      var paramName = split[0];
      var paramValue = split[1];
    }
  });
  $("#clearForm").click(function () {
    window.scrollTo(0, 0);

    // Clear all fields
    var fields = ["tripNumber", "fbNumber", "cases", "productNumber", "trailerNumber"];
    fields.forEach(function (field) {
      var f = $("#" + field);
      f.val("");
      f.removeClass("is-valid");
      f.removeClass("is-invalid");
    });

    // Clear all checkboxes
    var checkboxes = ["oCheck", "sCheck", "dCheck", "yCheck", "nCheck"];
    checkboxes.forEach(function (checkbox) {
      var c = $("#" + checkbox);
      c.prop("checked", false);
    });

    // Clear pictures
    pictures = {};
    $("#selectedFiles").html("");
  });

  // Toggling yes/no options to allow for only one selection
  $("input.checkbox").change(function () {
    var originalCheckbox = $(this);
    var relativeGrandparent = $(this).parent().parent();
    $("input.checkbox").each(function () {
      var grandparent = $(this).parent().parent();
      if (grandparent[0].id == relativeGrandparent[0].id) {
        $(this).prop("checked", false);
      }
    });
    originalCheckbox.prop("checked", true);
  });

  // Removes pictures from the OS&D and Accident Report form image input when delete icon is clicked
  $(document).on("click", ".removePhoto", function () {
    $(this).parent().remove();

    pictures[$(this).attr("id")] = undefined;
  });

  //Removes validation errors from OS&D Form elements
  $("#overage, #shortage, #damage").click(function () {
    if ($(this).hasClass("is-invalid")) {
      $("#overage").removeClass("is-invalid");
      $("#shortage").removeClass("is-invalid");
      $("#damage").removeClass("is-invalid");
    }
  });
  $("#yesCheck, #noCheck").click(function () {
    if ($(this).hasClass("invalid")) {
      $("#yesCheck").removeClass("invalid");
      $("#yesLabel").removeClass("invalid");
      $("#noCheck").removeClass("invalid");
      $("#noLabel").removeClass("invalid");
      $("#noLabel").removeClass("is-invalid");
    }
  });

  // Removes the invalid styling from the camera input
  $("#cameraInput").change(function () {
    $("#cameraInput").removeClass("is-invalid");
  });

  // Removes the invalid styling from the specified inputs
  $("#tripNumber, #fbNumber, #cases, #productNumber, #trailerNumber").change(function () {
    if ($(this).val().length > 0) {
      $(this).removeClass("is-invalid");
      $(this).addClass("is-valid");
    }
  });

  /*
   * Removing invalid styling on clicking input
   */
  $("input").click(function () {
    if ($(this).hasClass("is-invalid")) {
      $(this).removeClass("is-invalid");
    }
    if ($(this).hasClass("invalid")) {
      $(this).removeClass("invalid");
    }
  });

  // Submission of the OS&D Form
  $("#osdForm").submit(function (e) {
    e.preventDefault();
    var form = $(this);

    var valid = validateOSDForm();

    if (!valid) {
      e.stopPropagation();
      return;
    }

    var formData = form.serializeArray();
    formData.push({ name: "date", value: new Date().toISOString() });
    formData.push({ name: "pictures", value: JSON.stringify(pictures) });

    $.ajax({
      type: "POST",
      url: form.attr("action"),
      data: formData,
      success: function (data) {
        $("#default").hide();
        $("#success").removeAttr("hidden");
      },
      error: function (textStatus, errorThrown) {
        $("#default").hide();
        $("#failure").removeAttr("hidden");
      },
    }).done(function (data) {
      console.log(data);
    });

    // Login form submission
    $("#signOut").click(function (e) {
      e.preventDefault();
      e.stopPropagation();

      $.ajax({
        type: "POST",
        url: $(this).attr("href"),
      }).done(function (data) {
        sessionStorage.setItem("userId", null);
        window.location.href = "/projects/logistics-management/";
      });
    });

    $("#forgotPassword").click(function (e) {
      e.preventDefault();
      e.stopPropagation();

      $("#loginForm").toggle("hidden");
      $("#forgotPasswordForm").toggle("hidden");
    });

    $("#backToLogin").click(function (e) {
      e.preventDefault();
      e.stopPropagation();

      $("#loginForm").toggle("hidden");
      $("#forgotPasswordForm").toggle("hidden");
    });

    // Forgot password toggle
    $("#forgotPasswordForm").submit(function (e) {
      e.preventDefault();
      e.stopPropagation();

      $("#loginForm").toggle("hidden");
    });

    // Login form submission
    $("#loginForm").submit(function (e) {
      var form = $(this);

      e.preventDefault();
      e.stopPropagation();

      console.log("loginForm");

      $.ajax({
        type: "POST",
        url: $(this).attr("action"),
        data: form.serialize(),
        dataType: "json",
      }).done(function (data) {
        if (data.result === "success") {
          sessionStorage.setItem("userId", data.userId);
          window.location.href = "";
        } else {
          $("#message").addClass("pb-3").html("Invalid username or password");
        }
      });
    });

    /*
     * Animated dropdown menus
     */
    $(".dropdown-menu").addClass("invisible");

    $(".dropdown").on("show.bs.dropdown", function (e) {
      $(".dropdown-menu").removeClass("invisible");
      $(this).find(".dropdown-menu").first().stop(true, true).slideDown();
    });

    $(".dropdown").on("hide.bs.dropdown", function (e) {
      $(this).find(".dropdown-menu").first().stop(true, true).hide();
    });
  });

  /*
   * Compressing and processing pictures from OS&D tool
   */
  const compressImage = async (file, { quality = 1, type = file.type }) => {
    const imageBitmap = await createImageBitmap(file);

    const canvas = document.createElement("canvas");
    canvas.width = imageBitmap.width;
    canvas.height = imageBitmap.height;
    canvas.getContext("2d").drawImage(imageBitmap, 0, 0);

    const blob = await new Promise((resolve) => canvas.toBlob(resolve, type, quality));

    return new File([blob], file.name, {
      type: blob.type,
    });
  };

  // Get the selected file from the file input
  const input = document.querySelector("#cameraInput");
  if (input) {
    input.addEventListener("change", async (e) => {
      const { files } = e.target;

      if (!files.length) return;

      const dataTransfer = new DataTransfer();

      // For every file in the files list, skipping non images
      for (const file of files) {
        if (!file.type.startsWith("image")) {
          dataTransfer.items.add(file);
          continue;
        }

        // Compress the file by 50%
        const compressedFile = await compressImage(file, {
          quality: 0.5,
          type: "image/jpeg",
        });

        dataTransfer.items.add(compressedFile);
      }

      // Set value of the file input to our new files list
      e.target.files = dataTransfer.files;

      // Display the images and compile array to send with the form data
      displayImages(e.target.files);
    });
  }

  if ($("#claimsTable")) {
    var claimsTable = $("#claimsTable").DataTable({
      responsive: true,
      pageLength: 25,
      ajax: fileLocation + "../../request.php?action=list",
      language: {
        paginate: {
          next: "<i class='bi bi-arrow-right'></i>",
          previous: "<i class='bi bi-arrow-left'></i>",
        },
      },
      columns: [
        {
          className: "dt-edit",
          orderable: false,
          data: null,
          defaultContent: "",
          createdCell: function (td, cellData, rowData, row, col) {
            $(td).attr("data-bs-toggle", "tooltip");
            $(td).attr("data-bs-placement", "top");
            $(td).attr("data-bs-html", "true");
            $(td).attr("title", "View Claim Details");
          },
        },
        { data: "id" },
        { data: "date" },
        { data: "name" },
        { data: "trailer_number" },
        { data: "trip_number" },
        { data: "cases" },
        { data: "type" },
        { data: "received" },
        { data: "driver_id" },
        { data: "status" },
      ],
      order: [[1, "asc"]],
    });

    $("#claimsTable tbody").on("click", "td.dt-edit", function () {
      var tr = $(this).closest("tr");
      var row = claimsTable.row(tr).data();

      console.log(row);

      window.location.replace("details?id=" + row.id);
    });
  }

  if ($("#driverClaimsTable")) {
    var driverClaimsTable = $("#driverClaimsTable").DataTable({
      responsive: true,
      pageLength: 10,
      paging: true,
      searching: false,
      info: false,
      ordering: true,
      language: {
        paginate: {
          next: "<i class='bi bi-arrow-right'></i>",
          previous: "<i class='bi bi-arrow-left'></i>",
        },
      },
      columnDefs: [
        {
          target: 0,
          className: "dt-edit",
          orderable: false,
          data: null,
          defaultContent: "",
          createdCell: function (td, cellData, rowData, row, col) {
            $(td).attr("data-bs-toggle", "tooltip");
            $(td).attr("data-bs-placement", "top");
            $(td).attr("data-bs-html", "true");
            $(td).attr("title", "View Claim Details");
          },
        }
      ],
      order: [[1, "asc"]],
    });

    $("#driverClaimsTable tbody").on("click", "td.dt-edit", function () {
      var tr = $(this).closest("tr");
      var row = driverClaimsTable.row(tr).data();

      window.location.replace("details?id=" + row[1]);
    });
  }

  if ($("facilityClaimsTable")) {
    var facilityClaimsTable = $("#facilityClaimsTable").DataTable({
      responsive: true,
      pageLength: 10,
      paging: true,
      searching: false,
      info: false,
      ordering: true,
      language: {
        paginate: {
          next: "<i class='bi bi-arrow-right'></i>",
          previous: "<i class='bi bi-arrow-left'></i>",
        },
      },
      columnDefs: [
        {
          target: 0,
          className: "dt-edit",
          orderable: false,
          data: null,
          defaultContent: "",
          createdCell: function (td, cellData, rowData, row, col) {
            $(td).attr("data-bs-toggle", "tooltip");
            $(td).attr("data-bs-placement", "top");
            $(td).attr("data-bs-html", "true");
            $(td).attr("title", "View Claim Details");
          },
        }
      ],
      order: [[1, "asc"]],
    });

    $("#facilityClaimsTable tbody").on("click", "td.dt-edit", function () {
      var tr = $(this).closest("tr");
      var row = facilityClaimsTable.row(tr).data();

      console.log(row);

      window.location.replace("details?id=" + row[1]);
    });
  }
});

/*
 * Dark / Light mode functionality
 */
function toggleDarkMode() {
  let theme = localStorage.getItem("theme");
  if (theme === "dark") {
    document.documentElement.setAttribute("data-theme", "light");
    localStorage.setItem("theme", "light");
    if (document.getElementById("darkModeSwitch")) document.getElementById("darkModeSwitch").checked = false;
  } else if (theme === "light") {
    document.documentElement.setAttribute("data-theme", "dark");
    localStorage.setItem("theme", "dark");
    if (document.getElementById("darkModeSwitch")) document.getElementById("darkModeSwitch").checked = true;
  }
  updateChartColors();
}

let theme = localStorage.getItem("theme");
if (!theme || theme === "light") {
  document.documentElement.setAttribute("data-theme", "light");
  if (document.getElementById("darkModeSwitch")) document.getElementById("darkModeSwitch").checked = false;
  localStorage.setItem("theme", "light");
  updateChartColors();
} else if (theme === "dark") {
  document.documentElement.setAttribute("data-theme", "dark");
  if (document.getElementById("darkModeSwitch")) document.getElementById("darkModeSwitch").checked = true;
  localStorage.setItem("theme", "dark");
  updateChartColors();
}

//TODO: Rewrite this function
document.addEventListener("DOMContentLoaded", function (event) {
  const showNavbar = (toggleId, mobileToggleId, navId, bodyId, headerId, footerId) => {
    const toggle = document.getElementById(toggleId),
      mobileToggle = document.getElementById(mobileToggleId),
      nav = document.getElementById(navId),
      bodypd = document.getElementById(bodyId),
      headerpd = document.getElementById(headerId),
      footerpd = document.getElementById(footerId);

    if (toggle && mobileToggle && nav && bodypd && headerpd) {
      if ($(window).width() >= 768 && (!localStorage.getItem("showSidebar") || localStorage.getItem("showSidebar") === "true")) {
        nav.classList.toggle("show");
        //toggle.classList.toggle("bx-x");
        bodypd.classList.toggle("body-pd");
        headerpd.classList.toggle("body-pd");
        footerpd.classList.toggle("body-pd");
        localStorage.setItem("showSidebar", "true");
      }
      if ($(window).width() < 768) {
        mobileToggle.classList.toggle("show");
      }

      mobileToggle.addEventListener("click", () => {
        //mobileToggle.classList.toggle("show");
        nav.classList.toggle("show");
        bodypd.classList.toggle("body-pd");
        // add padding to header
        headerpd.classList.toggle("body-pd");
        footerpd.classList.toggle("body-pd");
      });

      toggle.addEventListener("click", () => {
        // show navbar
        nav.classList.toggle("show");
        mobileToggle.classList.toggle("show");
        // change icon
        //toggle.classList.toggle("bx-x");
        // add padding to body
        bodypd.classList.toggle("body-pd");
        // add padding to header
        headerpd.classList.toggle("body-pd");
        if (footerpd) footerpd.classList.toggle("body-pd");
        var showSidebar = localStorage.getItem("showSidebar");
        localStorage.setItem("showSidebar", showSidebar === "true" ? "false" : "true");
      });
    }
  };

  showNavbar("header-toggle", "header-toggle-mobile", "nav-bar", "body-pd", "header", "footer");

  // Style the active link
  const linkColor = document.querySelectorAll(".nav_link");

  function colorLink() {
    if (linkColor && !this.classList.contains("submenu")) {
      linkColor.forEach((l) => l.classList.remove("active"));
      this.classList.add("active");
    }
  }
  linkColor.forEach((l) => l.addEventListener("click", colorLink));
});

/*
 * Used to validate fields in forms, checking if they are not empty and contain at least @length characters.
 */
function isValid(id, length = 1, maxLength = 0) {
  var input = $("#" + id);
  var valid = true;

  if (!input) return false;

  if (!input.val() || input.val() === "" || input.val().length < length || (maxLength > 0 && input.val().length > maxLength)) {
    document.querySelector("#" + id).classList.add("is-invalid");
    valid = false;
  } else {
    document.querySelector("#" + id).classList.remove("is-invalid");
  }
  return valid;
}
