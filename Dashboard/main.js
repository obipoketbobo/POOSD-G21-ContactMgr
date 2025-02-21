$(document).ready(function () {
  // Light/Dark mode toggling
  const darkModeBtn = $("#darkModeToggle");

  function toggleDarkMode() {
    $("body").toggleClass("dark-mode");
    let isDarkMode = $("body").hasClass("dark-mode");
    darkModeBtn.text(isDarkMode ? "🌞" : "🌛");
    localStorage.setItem("darkMode", isDarkMode);
  }

  if (localStorage.getItem("darkMode") === "true") {
    $("body").addClass("dark-mode");
    darkModeBtn.text("🌞");
  }

  darkModeBtn.click(toggleDarkMode);

  function showAlert(message, type) {
    var alertHTML =
      '<div class="alert alert-' +
      type +
      ' alert-dismissible fade show" role="alert">' +
      message +
      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button></div>';
    $("#alertBox").html(alertHTML);
    setTimeout(function () {
      $(".alert").alert("close");
    }, 3000);
  }

  function fetchContacts(searchQuery = "") {
    let ownerId = getUserIdFromUrl();
    
    if (!ownerId) {
      window.location.href = "../index.html";
      return;
    }

    let firstName = "%";  // Default wildcard (fetch all)
    let lastName = "%";

    if (searchQuery.trim() !== "") {
      let nameParts = searchQuery.trim().split(" ");
      firstName = nameParts[0] || "%";
      lastName = nameParts[1] || "%";  // If no last name, wildcard
    }

    $.ajax({
      url: "http://contactmanager.group21contactmanager.site/SearchContactTest.php",
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify({ ownerId: ownerId, first: firstName, last: lastName }), 
      success: function (data) {
        $("#contactsTableBody").empty();
        if (data.results && data.results.length > 0) {
          data.results.forEach(contact => {
            var fullName = contact.firstName + " " + contact.lastName;
            var newRow = `
              <tr>
                <td>${fullName}</td>
                <td>${contact.email}</td>
                <td class="text-center">
                  <button class="btn btn-sm btn-primary editContactBtn" 
                      data-id="${contact.id}" data-firstname="${contact.firstName}" 
                      data-lastname="${contact.lastName}" data-email="${contact.email}">
                      Edit
                  </button>
                  <button class="btn btn-sm btn-danger deleteContactBtn" data-id="${contact.id}">Delete</button>
                </td>
              </tr>`;
            $("#contactsTableBody").append(newRow);
          });
        } else {
          $("#contactsTableBody").html("<tr><td colspan='3' class='text-center'>No contacts found.</td></tr>");
        }
      },
      error: function () {
        showAlert("Error loading contacts!", "danger");
      }
    });
  }

  function getUserIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get("userId");
  }

  // Search when hitting "Enter" in the textbox
  $("#searchContacts").on("keypress", function (e) {
    if (e.which === 13) { 
      e.preventDefault();
      fetchContacts($(this).val().trim());
    }
  });

  // Refresh contacts (fetch all)
  $("#refreshButton").on("click", function () {
    fetchContacts(""); 
  });

  fetchContacts();

  $("#contactForm").on("submit", function (e) {
    e.preventDefault();
    var contactId = $("#contactId").val();
    var firstName = $("#contactFirstName").val().trim();
    var lastName = $("#contactLastName").val().trim();
    var contactEmail = $("#contactEmail").val().trim();
    var fullName = firstName + " " + lastName;

    if (!firstName || !lastName || !contactEmail) {
      showAlert("Please enter first name, last name, and email!", "warning");
      return;
    }

    var requestData = {
      ownerId: getUserIdFromUrl(),
      firstName: firstName,
      lastName: lastName,
      email: contactEmail
    };

    if (contactId === "") {
      $.ajax({
        url: "http://contactmanager.group21contactmanager.site/AddContact.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(requestData),
        success: function () {
          showAlert(`Contact '${fullName}' added successfully!`, "success");
          $("#contactModal").modal("hide");
          fetchContacts();
        },
        error: function () {
          showAlert("Error adding contact!", "danger");
        }
      });
    } else {
      requestData.contactId = contactId;
      $.ajax({
        url: "http://contactmanager.group21contactmanager.site/update.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(requestData),
        success: function () {
          showAlert(`Contact '${fullName}' updated successfully!`, "success");
          $("#contactModal").modal("hide");
          fetchContacts();
        },
        error: function () {
          showAlert("Error updating contact!", "danger");
        }
      });
    }

    $("#contactForm")[0].reset();
    $("#contactId").val("");
  });

  $(document).on("click", ".editContactBtn", function () {
    var firstName = $(this).attr("data-firstname");
    var lastName = $(this).attr("data-lastname");
    var contactEmail = $(this).attr("data-email");

    $("#contactId").val($(this).attr("data-id"));
    $("#contactFirstName").val(firstName);
    $("#contactLastName").val(lastName);
    $("#contactEmail").val(contactEmail);

    $("#contactModalLabel").text("Edit Contact");
    $("#contactModal").modal("show");
  });

  $(document).on("click", ".deleteContactBtn", function () {
    if (confirm("Are you sure you want to delete this contact?")) {
      let contactId = $(this).attr("data-id");

      $.ajax({
        url: "http://contactmanager.group21contactmanager.site/DeleteContact.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ contactId: contactId }),
        success: function () {
          showAlert("Contact deleted successfully!", "danger");
          fetchContacts();
        },
        error: function () {
          showAlert("Error deleting contact!", "danger");
        }
      });
    }
  });

  $("#contactModal").on("hidden.bs.modal", function () {
    $("#contactForm")[0].reset();
    $("#contactId").val("");
    $("#contactModalLabel").text("Add Contact");
  });

  $("#logoutButton").on("click", function () {
    document.cookie = "userId=;expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    window.location.href = "../index.html";
  });

});Id=;expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    window.location.href = "../index.html";
  });
});
