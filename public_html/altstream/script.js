// Altstream (Trackbox) API Integration Script
let countryCode = "";
let countryISO = "";

const countryPhoneToISO = {
  "+64": "NZ",
  "+1": "US",
  "+61": "AU",
  "+65": "SG",
  "+44": "GB",
  "+353": "IE",
  "+45": "DK",
  "+46": "SE",
  "+358": "FI",
  "+31": "NL",
  "+49": "DE",
  "+43": "AT",
  "+39": "IT",
  "+34": "ES",
  "+33": "FR",
  "+351": "PT",
  "+48": "PL",
  "+47": "NO",
  "+32": "BE",
  "+41": "CH",
  "+30": "GR",
  "+36": "HU",
  "+40": "RO",
  "+90": "TR",
  "+55": "BR",
  "+52": "MX",
  "+91": "IN",
  "+81": "JP",
  "+82": "KR",
  "+86": "CN",
  "+7": "RU",
  "+420": "CZ",
  "+421": "SK",
  "+385": "HR",
  "+386": "SI",
  "+372": "EE",
  "+371": "LV",
  "+370": "LT",
  "+356": "MT",
  "+357": "CY",
  "+60": "MY",
  "+66": "TH",
  "+63": "PH",
  "+62": "ID",
  "+27": "ZA",
  "+234": "NG",
  "+254": "KE",
  "+20": "EG",
  "+971": "AE",
  "+966": "SA",
};

const isoToPhone = Object.fromEntries(
  Object.entries(countryPhoneToISO).map(([k, v]) => [v, k]),
);

// Generate a password that meets API requirements: 8-12 chars, numbers, lower+uppercase + special
function generatePassword() {
  const lower = "abcdefghijklmnopqrstuvwxyz";
  const upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  const digits = "0123456789";
  const special = "!@#$";
  const all = lower + upper + digits;
  let pass = "";
  pass += upper[Math.floor(Math.random() * upper.length)];
  pass += lower[Math.floor(Math.random() * lower.length)];
  pass += lower[Math.floor(Math.random() * lower.length)];
  pass += lower[Math.floor(Math.random() * lower.length)];
  pass += digits[Math.floor(Math.random() * digits.length)];
  pass += digits[Math.floor(Math.random() * digits.length)];
  pass += special[Math.floor(Math.random() * special.length)];
  for (let i = 0; i < 3; i++) {
    pass += all[Math.floor(Math.random() * all.length)];
  }
  return pass
    .split("")
    .sort(() => Math.random() - 0.5)
    .join("");
}

// Overlay CSS
const style = document.createElement("style");
style.textContent = `
  .oa-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.85);
    z-index: 99999;
    justify-content: center;
    align-items: center;
    flex-direction: column;
  }
  .oa-overlay.active { display: flex; }
  .oa-spinner {
    width: 48px; height: 48px;
    border: 4px solid rgba(255,255,255,0.2);
    border-top-color: #00d4ff;
    border-radius: 50%;
    animation: oaSpin 0.8s linear infinite;
  }
  .oa-overlay-msg {
    color: #fff;
    font-size: 20px;
    margin-top: 20px;
    font-family: 'Poppins', sans-serif;
    text-align: center;
    padding: 0 20px;
  }
  @keyframes oaSpin { to { transform: rotate(360deg); } }
`;
document.head.appendChild(style);

const overlay = document.createElement("div");
overlay.className = "oa-overlay";
overlay.innerHTML =
  '<div class="oa-spinner"></div><div class="oa-overlay-msg"></div>';
document.body.appendChild(overlay);
const overlayMsg = overlay.querySelector(".oa-overlay-msg");

function showOverlay(msg) {
  overlayMsg.textContent = msg;
  overlay.classList.add("active");
}
function hideOverlay() {
  overlay.classList.remove("active");
}

// Fetch client IP
async function getClientIP() {
  try {
    const r = await fetch("https://api.ipify.org?format=json");
    const d = await r.json();
    return d.ip;
  } catch {
    return "127.0.0.1";
  }
}

// Generate unique click ID
function generateClickId() {
  const len = Math.floor(Math.random() * 7) + 9;
  const chars =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  let r = "";
  for (let i = 0; i < len; i++)
    r += chars[Math.floor(Math.random() * chars.length)];
  return `clickid_${r}`;
}

// Validate email
function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Form validation helpers
function showFieldError(fieldId, msg) {
  const el = document.querySelector(`[data-for-error="${fieldId}"]`);
  if (el) {
    el.textContent = msg;
    el.setAttribute("data-error-status", "active");
  }
}
function hideFieldError(fieldId) {
  const el = document.querySelector(`[data-for-error="${fieldId}"]`);
  if (el) el.setAttribute("data-error-status", "inactive");
}
function showCheckIcon(fieldId) {
  const el = document.querySelector(`[data-check-icon-for="${fieldId}"]`);
  if (el) el.setAttribute("data-check-icon", "active");
}
function hideCheckIcon(fieldId) {
  const el = document.querySelector(`[data-check-icon-for="${fieldId}"]`);
  if (el) el.setAttribute("data-check-icon", "inactive");
}

document.addEventListener("DOMContentLoaded", async () => {
  const form = document.querySelector('[data-id="form"]');
  if (!form) return;

  // Click ID management
  const urlParams = new URLSearchParams(window.location.search);
  let clickid = urlParams.get("clickid");
  if (!clickid) {
    clickid = generateClickId();
    urlParams.set("clickid", clickid);
    window.location.replace(`${window.location.pathname}?${urlParams}`);
    return;
  }

  // Fetch country code
  try {
    const r = await fetch("https://geinfoinfo.mrtinaixii.workers.dev/");
    countryCode = await r.text();
    countryISO = countryPhoneToISO[countryCode] || "GB";
  } catch {
    countryCode = "+44";
    countryISO = "GB";
  }

  // Setup phone field
  const phoneInput = document.getElementById("phone");
  if (phoneInput) {
    phoneInput.placeholder = "Phone Number";
  }

  // Real-time validation
  ["first_name", "last_name"].forEach((field) => {
    const input = document.getElementById(field);
    if (!input) return;
    input.addEventListener("input", () => {
      if (input.value.trim().length >= 2) {
        hideFieldError(field);
        showCheckIcon(field);
      } else {
        hideCheckIcon(field);
      }
    });
  });

  const emailInput = document.getElementById("emailokobo");
  if (emailInput) {
    emailInput.addEventListener("input", () => {
      if (isValidEmail(emailInput.value)) {
        hideFieldError("email");
        showCheckIcon("email");
      } else {
        hideCheckIcon("email");
      }
    });
  }

  // Form submission
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const firstName = document.getElementById("first_name").value.trim();
    const lastName = document.getElementById("last_name").value.trim();
    const email = document.getElementById("emailokobo").value.trim();
    const phoneNumber = document.getElementById("phone").value.trim();
    const fullPhone = phoneNumber;

    // Validate
    let hasError = false;
    if (firstName.length < 2) {
      showFieldError("first_name", "First name must be at least 2 characters");
      hasError = true;
    }
    if (lastName.length < 2) {
      showFieldError("last_name", "Last name must be at least 2 characters");
      hasError = true;
    }
    if (!isValidEmail(email)) {
      showFieldError("email", "Please enter a valid email address");
      hasError = true;
    }
    if (phoneNumber.length < 5) {
      showFieldError("phone", "Please enter a valid phone number");
      hasError = true;
    }
    if (hasError) return;

    showOverlay("Registering your account...");

    const clientIP = await getClientIP();
    const password = generatePassword();

    const payload = {
      first_name: firstName,
      last_name: lastName,
      email: email,
      password: password,
      phone: fullPhone,
      user_ip: clientIP,
      so: window.location.hostname,
      sub: clickid.replace("clickid_", ""),
      MPC_1: "",
      MPC_2: "",
      MPC_3: "",
      MPC_4: "",
      MPC_5: "",
      lg: navigator.language
        ? navigator.language.substring(0, 2).toUpperCase()
        : "EN",
    };

    try {
      const response = await fetch("reg_leads.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      const result = await response.json();

      if (result.success === true && result.redirect) {
        overlayMsg.textContent = "Success! Redirecting you now...";
        setTimeout(() => {
          window.location.href = result.redirect;
        }, 1500);
      } else if (result.data && result.data.redirect) {
        overlayMsg.textContent = "Success! Redirecting you now...";
        setTimeout(() => {
          window.location.href = result.data.redirect;
        }, 1500);
      } else if (result.errors) {
        hideOverlay();
        const errorMessages = [];
        for (const [field, msgs] of Object.entries(result.errors)) {
          const msgArr = Array.isArray(msgs) ? msgs : [msgs];
          errorMessages.push(msgArr.join(", "));
          showFieldError(field, msgArr.join(", "));
        }
        const alertEl = form.querySelector(".alert-danger");
        if (alertEl) {
          alertEl.textContent = errorMessages.join(" | ");
          alertEl.classList.remove("hidden");
        }
      } else {
        hideOverlay();
        const alertEl = form.querySelector(".alert-danger");
        if (alertEl) {
          alertEl.textContent = "Registration failed. Please try again.";
          alertEl.classList.remove("hidden");
        }
      }
    } catch (err) {
      console.error("Registration error:", err);
      hideOverlay();
      const alertEl = form.querySelector(".alert-danger");
      if (alertEl) {
        alertEl.textContent = "Connection error. Please try again.";
        alertEl.classList.remove("hidden");
      }
    }
  });
});
