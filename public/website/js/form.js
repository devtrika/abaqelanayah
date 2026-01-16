$(document).ready(function () {
  /***** Form *****/
  let input = $("input[type=tel][intlTelInput]");
  if (input.length > 0) {
    for (let i = 0; i < input.length; i++) {
      intlTelInput(input[i], {
        utilsScript: "/website/js/utils.js",
        autoPlaceholder: "aggressive",
        separateDialCode: true,
        initialCountry: "sa",
        preferredCountries: ["sa", "kw", "ae", "bh", "om", "qa"],
      });
    }
  }
  $(".password-toggle").on("click", function (e) {
    e.preventDefault();
    const input = $(this)
      .closest(".password-content")
      .find("input.form-control");
    const isPassword = input.attr("type") === "password";
    input.attr("type", isPassword ? "text" : "password");
    $(this).toggleClass("active", isPassword);
  });

  if (typeof $.fn.select2 !== "undefined") {
    if ($(window).width() >= 992) {
      $("select[select2]").select2({
        customClass: "error",
        minimumResultsForSearch: Infinity,
      });
    }
  }
  /***** OTP *****/
  const inputs = $("#otp-input input");

  inputs.on("input", function () {
    const index = inputs.index(this);

    if (this.value.length == 1 && index + 1 < inputs.length) {
      $(inputs[index + 1]).removeAttr("disabled");
      inputs[index + 1].focus();
    } else {
      inputs.blur();
      $("form .submit-btn").removeAttr("disabled");
    }

    if (this.value.length > 1) {
      if (isNaN(this.value)) {
        this.value = "";
        updateInput();
        return;
      }

      const chars = this.value.split("");

      $.each(chars, function (pos) {
        if (pos + index >= inputs.length) return false;

        let targetInput = inputs[pos + index];
        targetInput.value = chars[pos];
      });

      let focusIndex = Math.min(inputs.length - 1, index + chars.length);
      inputs[focusIndex].focus();
    }
    updateInput();
  });

  inputs.on("keydown", function (e) {
    const index = inputs.index(this);

    if (e.keyCode == 8 && this.value == "" && index != 0) {
      for (let pos = index; pos < inputs.length - 1; pos++) {
        inputs[pos].value = inputs[pos + 1].value;
      }

      inputs[index - 1].value = "";
      inputs[index - 1].focus();
      updateInput();
      return;
    }

    if (e.keyCode == 46 && index != inputs.length - 1) {
      for (let pos = index; pos < inputs.length - 1; pos++) {
        inputs[pos].value = inputs[pos + 1].value;
      }

      inputs[inputs.length - 1].value = "";
      this.select();
      e.preventDefault();
      updateInput();
      return;
    }

    if (e.keyCode == 37) {
      if (index > 0) {
        e.preventDefault();
        inputs[index - 1].focus();
        inputs[index - 1].select();
      }
      return;
    }

    if (e.keyCode == 39) {
      if (index + 1 < inputs.length) {
        e.preventDefault();
        inputs[index + 1].focus();
        inputs[index + 1].select();
      }
      return;
    }
  });

  function updateInput() {
    let inputValue = inputs.toArray().reduce(function (otp, input) {
      otp += input.value.length ? input.value : " ";
      return otp;
    }, "");
    $("input[name=otp]").val(inputValue);
  }

  /***** OTP Timer and Resend Code *****/
  const timerCountdown = $("#timer-countdown");
  const resendBtn = $("#resend-code-btn");
  const timerLabel = $("#timer-label");

  if (timerCountdown.length > 0 && resendBtn.length > 0) {
    let timeLeft = 60; // 1 minute in seconds
    let timerInterval;

    function startTimer() {
      // Clear any existing interval
      if (timerInterval) {
        clearInterval(timerInterval);
      }

      timeLeft = 60;
      resendBtn.prop("disabled", true);
      timerLabel.show();
      timerCountdown.show();

      // Update display immediately
      timerCountdown.text("01:00");

      timerInterval = setInterval(function () {
        if (timeLeft > 0) {
          timeLeft--;

          const minutes = Math.floor(timeLeft / 60);
          const seconds = timeLeft % 60;
          const formattedTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

          timerCountdown.text(formattedTime);
        }

        if (timeLeft <= 0) {
          clearInterval(timerInterval);
          resendBtn.prop("disabled", false);
          timerLabel.hide();
          timerCountdown.hide();
        }
      }, 1000);
    }

    // Start timer on page load
    startTimer();

    // Resend code button click handler
    resendBtn.on("click", function () {
      const btn = $(this);
      btn.prop("disabled", true);

      // Determine the resend URL based on the current page
      let resendUrl = "/password-otp/resend"; // Default to password reset
      if (window.location.pathname.includes("register-otp")) {
        resendUrl = "/register-otp/resend";
      }

      $.ajax({
        url: resendUrl,
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
          if (response.success) {
            // Show success message using Bootstrap toast
            showToast(response.message || "Code resent successfully", "success");
            // Restart timer
            startTimer();
          } else {
            // Show error message
            showToast(response.message || "Failed to resend code", "danger");
            btn.prop("disabled", false);
          }
        },
        error: function (xhr) {
          // Show error message
          const errorMessage = xhr.responseJSON?.message || "Failed to resend code";
          showToast(errorMessage, "danger");
          btn.prop("disabled", false);
        },
      });
    });
  }

  $(".date-content input").change(function () {
    if ($(this).val() != "") {
      $(this).addClass("filled");
    } else {
      $(this).removeClass("filled");
    }
  });

  $(".file-content input[type=file]").change(function () {
    let file_val;
    if ($(this).val() == "") {
      file_val = "";
    } else {
      file_val = splitFileName($(this).prop("files")[0].name);
    }
    $(this).parent(".file-content").find(".file-name").html(file_val[0]);
    $(this)
      .parent(".file-content")
      .find(".file-type")
      .html("." + file_val[1]);
  });

  $(
    "input[type=radio][name='reason'], input[type=radio][name='cancel_reason']"
  ).on("change", function () {
    const form = $(this).closest("form");
    const other = form.find("input[type=radio][data-id=other]");
    const otherReason = form.find(".other-reason");

    if (other.is(":checked")) {
      otherReason.removeClass("d-none");
    } else {
      otherReason.addClass("d-none");
    }
  });
});

/**
 * Show Bootstrap toast notification
 * @param {string} message - The message to display
 * @param {string} type - The type of toast (success, danger, info, warning)
 */
function showToast(message, type = "success") {
  const bgClass = `bg-${type}`;
  const iconClass = type === "success" ? "fa-check-circle" : "fa-exclamation-circle";

  const toastHtml = `
    <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
      <div class="d-flex">
        <div class="toast-body">
          <i class="fas ${iconClass} me-2"></i>
          ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  `;

  // Find or create toast container
  let toastContainer = $(".toast-container");
  if (toastContainer.length === 0) {
    toastContainer = $('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
    $("body").append(toastContainer);
  }

  // Add toast to container
  const toastElement = $(toastHtml);
  toastContainer.append(toastElement);

  // Initialize and show toast
  const toast = new bootstrap.Toast(toastElement[0]);
  toast.show();

  // Remove toast element after it's hidden
  toastElement.on("hidden.bs.toast", function () {
    $(this).remove();
  });
}
