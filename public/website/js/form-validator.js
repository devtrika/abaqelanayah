/**
 * TARA Bay Form Validator
 * A centralized, reusable form validation system for Laravel Blade templates
 *
 * Features:
 * - Real-time validation (on input/blur events)
 * - Consistent error display with icons
 * - Easy to implement with data attributes
 * - Reusable across the entire project
 * - Works with existing form styling
 */

class TaraFormValidator {
  constructor() {
    this.validators = {
      required: (value) => value.trim() !== "",
      email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
      phone: (value) => {
        const cleanPhone = value.replace(/\D/g, "");
        return cleanPhone.length >= 8 && cleanPhone.length <= 15;
      },
      saudiPhone: (value) => {
        const clean = value.replace(/\D/g, "");
        // ✅ Must start with 5 and have exactly 9 digits (e.g. 501234567)
        return /^5\d{8}$/.test(clean);
      },
      equalTo: (value, target) => value === document.querySelector(`[name="${target}"]`).value,
      minLength: (value, min) => value.length >= parseInt(min),
      maxLength: (value, max) => value.length <= parseInt(max),
      min: (value, min) => parseFloat(value) >= parseFloat(min),
      max: (value, max) => parseFloat(value) <= parseFloat(max),
      numeric: (value) => /^\d+$/.test(value),
      alphanumeric: (value) => /^[a-zA-Z0-9\u0600-\u06FF\s]+$/.test(value),
      password: (value) => value.length >= 6,
      passwordStrong: (value) =>
        /^(?=.*[A-Za-z])(?=.*\d)/.test(value) && value.length >= 8,
      url: (value) => {
        try {
          new URL(value);
          return true;
        } catch {
          return false;
        }
      },
      regex: (value, pattern) => {
        try {
          const regex = new RegExp(pattern);
          return regex.test(value);
        } catch {
          return false;
        }
      },
    };

    this.messages = {
      required: "هذا الحقل مطلوب",
      email: "البريد الإلكتروني غير صالح",
      phone: "رقم الجوال غير صالح (يجب أن يكون بين 8-15 رقم)",
      saudiPhone: "رقم الجوال غير صالح، يجب أن يكون 9 أرقام ويبدأ بالرقم 5",
      minLength: "يجب أن يكون {min} أحرف على الأقل",
      maxLength: "يجب أن يكون أقل من {max} حرف",
      min: "القيمة يجب أن تكون {min} على الأقل",
      max: "القيمة يجب أن تكون {max} على الأكثر",
      numeric: "يجب أن يحتوي على أرقام فقط",
      alphanumeric: "يجب أن يحتوي على أحرف وأرقام فقط",
      password: "كلمة المرور يجب أن تكون 6 أحرف على الأقل",
      passwordStrong:
        "كلمة المرور يجب أن تحتوي على حرف ورقم على الأقل وتكون 8 أحرف على الأقل",
      url: "الرابط غير صالح",
      equalTo: "القيمتان غير متطابقتان",
      regex: "التنسيق غير صحيح",
    };

    this.init();
  }

  init() {
    // Auto-initialize forms with data-validate attribute
    // Check if DOM is already loaded (in case script loads after DOMContentLoaded)
    if (document.readyState === 'loading') {
      document.addEventListener("DOMContentLoaded", () => {
        this.initializeForms();
      });
    } else {
      // DOM is already loaded, initialize immediately
      this.initializeForms();
    }
  }

  initializeForms() {
    const forms = document.querySelectorAll("[data-validate]");
    forms.forEach((form) => this.initializeForm(form));
  }

  initializeForm(form) {
    // Support both data-rules and data-validation attributes
    const inputs = form.querySelectorAll("[data-rules], [data-validation]");

    inputs.forEach((input) => {
      // Add real-time validation
      input.addEventListener("blur", () => {
        this.validateField(input);
        this.updateSubmitButton(form);
      });
      input.addEventListener("input", () => {
        // Debounce input validation
        clearTimeout(input.validationTimeout);
        input.validationTimeout = setTimeout(() => {
          this.validateField(input);
          this.updateSubmitButton(form);
        }, 300);
      });
    });

    // Add form submission validation
    form.addEventListener("submit", (e) => {
      if (!this.validateForm(form)) {
        e.preventDefault();
        e.stopPropagation();
      }
    });

    // Initial submit button state check
    this.updateSubmitButton(form);
  }



  validateField(input) {
    // Support both data-rules and data-validation attributes
    const rulesAttr = input.getAttribute("data-rules") || input.getAttribute("data-validation");
    if (!rulesAttr) {
      return true;
    }

    const rules = rulesAttr.split("|");
    // Support both data-message and data-validation-error-msg attributes
    const customMessage = input.getAttribute("data-message") || input.getAttribute("data-validation-error-msg");

    // Parse custom messages JSON if available
    let customMessages = {};
    const customMessagesAttr = input.getAttribute("data-messages");
    if (customMessagesAttr) {
      try {
        customMessages = JSON.parse(customMessagesAttr);
      } catch (e) {
        console.warn('Failed to parse data-messages attribute:', e);
      }
    }

    const isRequired = rules.includes("required");

    // Get value - handle checkboxes differently
    let value;
    let isEmpty;

    if (input.type === "checkbox" || input.type === "radio") {
      value = input.checked;
      isEmpty = !input.checked;
    } else {
      // intlTelInput updates the input.value, so we can use it directly
      value = input.value || "";
      isEmpty = value.trim() === "";
    }

    // Skip validation for optional empty fields
    if (!isRequired && isEmpty) {
      this.clearError(input);
      return true;
    }

    for (let rule of rules) {
      const [ruleName, ruleValue] = rule.split(":");

      // Skip 'required' check here as we handle it above
      if (ruleName === "required") {
        if (isEmpty) {
          const errorMsg = customMessages[ruleName] || customMessage || this.messages.required;
          this.showError(input, errorMsg);
          return false;
        }
        continue;
      }

      // Handle special cases
      if (ruleName === "match") {
        const targetInput = document.querySelector(`[name="${ruleValue}"]`);
        // Only validate match if current field has value OR target field has value
        if (targetInput) {
          const targetValue = targetInput.value || "";
          const targetIsEmpty = targetValue.trim() === "";
          // Skip match validation if both fields are empty and not required
          if (isEmpty && targetIsEmpty && !isRequired) {
            continue;
          }
          // Validate match if either field has value
          if (value !== targetValue) {
            const errorMsg = customMessages[ruleName] || customMessage || this.messages.match;
            this.showError(input, errorMsg);
            return false;
          }
        }
      } else if (this.validators[ruleName]) {
        if (!this.validators[ruleName](value, ruleValue)) {
          let message = customMessages[ruleName] || customMessage || this.messages[ruleName];
          if (ruleValue) {
            message = message
              .replace("{min}", ruleValue)
              .replace("{max}", ruleValue)
              .replace(
                `{${ruleName.replace(/[A-Z]/g, (letter) =>
                  letter.toLowerCase()
                )}}`,
                ruleValue
              );
          }
          this.showError(input, message);
          return false;
        }
      }
    }

    this.clearError(input);
    return true;
  }

  validateForm(form) {
    // Support both data-rules and data-validation attributes
    const inputs = form.querySelectorAll("[data-rules], [data-validation]");
    let isValid = true;

    inputs.forEach((input) => {
      if (!this.validateField(input)) {
        isValid = false;
      }
    });

    return isValid;
  }

  updateSubmitButton(form) {
    // Find submit button(s) in the form
    const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"], .submit-btn[type="submit"]');

    if (submitButtons.length === 0) return;

    // Check if form has any validation errors
    const hasErrors = form.querySelectorAll('.validation-error').length > 0;

    // Disable button ONLY if there are validation errors
    // Don't check for empty required fields on initial load
    submitButtons.forEach((button) => {
      if (hasErrors) {
        button.disabled = true;
        button.style.opacity = '0.6';
        button.style.cursor = 'not-allowed';
      } else {
        button.disabled = false;
        button.style.opacity = '1';
        button.style.cursor = 'pointer';
      }
    });
  }

  showError(input, message) {
    const formGroup = input.closest(".form-group") || input.closest(".terms-group");
    if (!formGroup) return;

    // Check if input currently has focus - we'll preserve it
    const hadFocus = document.activeElement === input;
    const cursorPosition = hadFocus ? input.selectionStart : null;

    // Remove existing error elements
    this.clearError(input);

    // Create error message (NO ICON)
    const errorMessage = document.createElement("span");
    errorMessage.className = "form-error validation-error-message";
    errorMessage.textContent = message;
    errorMessage.style.cssText = `
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 400;
            margin: 2px 0 0;
            color: #e24a56;
            animation: fadeInError 0.3s ease-in-out;
            text-align: right;
            direction: rtl;
            width: 100%;
            justify-content: flex-start;
        `;

    // Style the input - red border only, no padding adjustment
    input.style.borderColor = "#e24a56";
    input.classList.add("validation-error");

    // Handle different input container types
    const passwordContent = formGroup.querySelector(".password-content");
    const iconedInput = formGroup.querySelector(".iconed-input");
    const intlTelInput = formGroup.querySelector(".intl-tel-input");
    const checkboxContainer = formGroup.querySelector(".checkbox");
    const termsGroup = input.closest(".terms-group");

    if (checkboxContainer || termsGroup) {
      // Checkbox input (like terms checkbox)
      const container = checkboxContainer || termsGroup;

      // Special styling for terms: Center the error message
      if (termsGroup) {
        errorMessage.style.justifyContent = "center";
        errorMessage.style.textAlign = "center";
        errorMessage.style.marginTop = "5px";
        errorMessage.style.fontWeight = "bold";
      }

      // Only insert message if not already present
      if (!errorMessage.parentNode) {
        container.parentNode.insertBefore(
          errorMessage,
          container.nextSibling
        );
      }
    } else if (passwordContent) {
      // Password input with toggle
      passwordContent.style.position = "relative";

      // Only insert message if not already present
      if (!errorMessage.parentNode) {
        passwordContent.parentNode.insertBefore(
          errorMessage,
          passwordContent.nextSibling
        );
      }
    } else if (intlTelInput) {
      // Phone input with international tel input
      intlTelInput.style.position = "relative";

      // Only insert message if not already present
      if (!errorMessage.parentNode) {
        intlTelInput.parentNode.insertBefore(
          errorMessage,
          intlTelInput.nextSibling
        );
      }
    } else if (iconedInput) {
      // Input with icon wrapper
      iconedInput.style.position = "relative";

      // Only insert message if not already present
      if (!errorMessage.parentNode) {
        iconedInput.parentNode.insertBefore(
          errorMessage,
          iconedInput.nextSibling
        );
      }
    } else {
      // Regular input
      const existingWrapper = input.parentNode.classList?.contains('validation-wrapper')
        ? input.parentNode
        : null;

      if (existingWrapper) {
        // Already wrapped, just update message
        if (!errorMessage.parentNode) {
          existingWrapper.parentNode.insertBefore(errorMessage, existingWrapper.nextSibling);
        }
      } else {
        // Only wrap if input doesn't have focus to avoid cursor jumping
        // If it has focus, just insert the error message next to the input
        if (!hadFocus) {
          // Create wrapper when input doesn't have focus
          const wrapper = document.createElement("div");
          wrapper.className = "validation-wrapper";
          wrapper.style.cssText = "position: relative; width: 100%;";

          input.parentNode.insertBefore(wrapper, input);
          wrapper.appendChild(input);
          wrapper.parentNode.insertBefore(errorMessage, wrapper.nextSibling);
        } else {
          // Input has focus - just insert error message without wrapping
          input.parentNode.insertBefore(errorMessage, input.nextSibling);
        }
      }
    }

    // Restore focus and cursor position if input had focus
    if (hadFocus) {
      // Use setTimeout to ensure DOM updates are complete
      setTimeout(() => {
        input.focus();
        if (cursorPosition !== null && input.setSelectionRange) {
          input.setSelectionRange(cursorPosition, cursorPosition);
        }
      }, 0);
    }

    // Add CSS animation if not already added
    if (!document.querySelector("#validation-animations")) {
      const style = document.createElement("style");
      style.id = "validation-animations";
      style.textContent = `
                @keyframes fadeInError {
                    from { opacity: 0; transform: translateY(-5px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .form-group:has(.validation-error) .form-label {
                    color: #e24a56 !important;
                }
                .validation-error-message {
                    text-align: right !important;
                    direction: rtl !important;
                    width: 100% !important;
                    padding-right: 20px;
                }
                .form-group:has([intlTelInput]) .validation-error-message {
                    text-align: right !important;
                    direction: rtl !important;
                }
                /* Disabled submit button styling */
                button[type="submit"]:disabled,
                input[type="submit"]:disabled,
                .submit-btn[type="submit"]:disabled {
                    opacity: 0.6 !important;
                    cursor: not-allowed !important;
                    pointer-events: none !important;
                }
            `;
      document.head.appendChild(style);
    }
  }

  clearError(input) {
    const formGroup = input.closest(".form-group");
    if (!formGroup) return;

    // Check if input currently has focus
    const hadFocus = document.activeElement === input;
    const cursorPosition = hadFocus ? input.selectionStart : null;

    // Remove error elements
    const errorIcon = formGroup.querySelector(".validation-error-icon");
    const errorMessage = formGroup.querySelector(".validation-error-message");
    const wrapper = formGroup.querySelector(".validation-wrapper");

    if (errorIcon) errorIcon.remove();
    if (errorMessage) errorMessage.remove();

    // Unwrap if we created a wrapper (but preserve focus)
    if (wrapper && wrapper.parentNode) {
      wrapper.parentNode.insertBefore(input, wrapper);
      wrapper.remove();
    }

    // Reset input styling
    input.style.borderColor = "";
    input.classList.remove("validation-error");

    // Restore focus if input had it
    if (hadFocus) {
      setTimeout(() => {
        input.focus();
        if (cursorPosition !== null && input.setSelectionRange) {
          input.setSelectionRange(cursorPosition, cursorPosition);
        }
      }, 0);
    }
  }

  // Public method to manually validate a form
  validate(formSelector) {
    const form = document.querySelector(formSelector);
    if (form) {
      return this.validateForm(form);
    }
    return false;
  }

  // Public method to add custom validator
  addValidator(name, validator, message) {
    this.validators[name] = validator;
    this.messages[name] = message;
  }

  // Public method to manually initialize a form
  initForm(formSelector) {
    const form = document.querySelector(formSelector);
    if (form) {
      this.initializeForm(form);
    }
  }
}

// Initialize the global validator
window.TaraValidator = new TaraFormValidator();

// Export for module systems
if (typeof module !== "undefined" && module.exports) {
  module.exports = TaraFormValidator;
}
