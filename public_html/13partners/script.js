// 13Partners API Integration Script (QuakeAI landing)
let countryCode = "";
let countryISO = "";

const countryPhoneToISO = {
  "+93": "AF",
  "+355": "AL",
  "+213": "DZ",
  "+376": "AD",
  "+244": "AO",
  "+1268": "AG",
  "+54": "AR",
  "+374": "AM",
  "+61": "AU",
  "+43": "AT",
  "+994": "AZ",
  "+1242": "BS",
  "+973": "BH",
  "+880": "BD",
  "+1246": "BB",
  "+375": "BY",
  "+32": "BE",
  "+501": "BZ",
  "+229": "BJ",
  "+975": "BT",
  "+591": "BO",
  "+387": "BA",
  "+267": "BW",
  "+55": "BR",
  "+673": "BN",
  "+359": "BG",
  "+226": "BF",
  "+257": "BI",
  "+855": "KH",
  "+237": "CM",
  "+1": "US",
  "+238": "CV",
  "+236": "CF",
  "+235": "TD",
  "+56": "CL",
  "+86": "CN",
  "+57": "CO",
  "+269": "KM",
  "+242": "CG",
  "+243": "CD",
  "+506": "CR",
  "+225": "CI",
  "+385": "HR",
  "+53": "CU",
  "+357": "CY",
  "+420": "CZ",
  "+45": "DK",
  "+253": "DJ",
  "+1767": "DM",
  "+1809": "DO",
  "+593": "EC",
  "+20": "EG",
  "+503": "SV",
  "+240": "GQ",
  "+291": "ER",
  "+372": "EE",
  "+268": "SZ",
  "+251": "ET",
  "+679": "FJ",
  "+358": "FI",
  "+33": "FR",
  "+241": "GA",
  "+220": "GM",
  "+995": "GE",
  "+49": "DE",
  "+233": "GH",
  "+30": "GR",
  "+1473": "GD",
  "+502": "GT",
  "+224": "GN",
  "+245": "GW",
  "+592": "GY",
  "+509": "HT",
  "+504": "HN",
  "+852": "HK",
  "+36": "HU",
  "+354": "IS",
  "+91": "IN",
  "+62": "ID",
  "+98": "IR",
  "+964": "IQ",
  "+353": "IE",
  "+972": "IL",
  "+39": "IT",
  "+1876": "JM",
  "+81": "JP",
  "+962": "JO",
  "+7": "KZ",
  "+254": "KE",
  "+686": "KI",
  "+850": "KP",
  "+82": "KR",
  "+965": "KW",
  "+996": "KG",
  "+856": "LA",
  "+371": "LV",
  "+961": "LB",
  "+266": "LS",
  "+231": "LR",
  "+218": "LY",
  "+423": "LI",
  "+370": "LT",
  "+352": "LU",
  "+853": "MO",
  "+261": "MG",
  "+265": "MW",
  "+60": "MY",
  "+960": "MV",
  "+223": "ML",
  "+356": "MT",
  "+692": "MH",
  "+222": "MR",
  "+230": "MU",
  "+52": "MX",
  "+691": "FM",
  "+373": "MD",
  "+377": "MC",
  "+976": "MN",
  "+382": "ME",
  "+212": "MA",
  "+258": "MZ",
  "+95": "MM",
  "+264": "NA",
  "+674": "NR",
  "+977": "NP",
  "+31": "NL",
  "+64": "NZ",
  "+505": "NI",
  "+227": "NE",
  "+234": "NG",
  "+389": "MK",
  "+47": "NO",
  "+968": "OM",
  "+92": "PK",
  "+680": "PW",
  "+970": "PS",
  "+507": "PA",
  "+675": "PG",
  "+595": "PY",
  "+51": "PE",
  "+63": "PH",
  "+48": "PL",
  "+351": "PT",
  "+974": "QA",
  "+40": "RO",
  "+7": "RU",
  "+250": "RW",
  "+1869": "KN",
  "+1758": "LC",
  "+1784": "VC",
  "+685": "WS",
  "+378": "SM",
  "+239": "ST",
  "+966": "SA",
  "+221": "SN",
  "+381": "RS",
  "+248": "SC",
  "+232": "SL",
  "+65": "SG",
  "+421": "SK",
  "+386": "SI",
  "+677": "SB",
  "+252": "SO",
  "+27": "ZA",
  "+211": "SS",
  "+34": "ES",
  "+94": "LK",
  "+249": "SD",
  "+597": "SR",
  "+46": "SE",
  "+41": "CH",
  "+963": "SY",
  "+886": "TW",
  "+992": "TJ",
  "+255": "TZ",
  "+66": "TH",
  "+670": "TL",
  "+228": "TG",
  "+676": "TO",
  "+1868": "TT",
  "+216": "TN",
  "+90": "TR",
  "+993": "TM",
  "+688": "TV",
  "+256": "UG",
  "+380": "UA",
  "+971": "AE",
  "+44": "GB",
  "+598": "UY",
  "+998": "UZ",
  "+678": "VU",
  "+379": "VA",
  "+58": "VE",
  "+84": "VN",
  "+967": "YE",
  "+260": "ZM",
  "+263": "ZW",
};

const isoToPhone = Object.fromEntries(
  Object.entries(countryPhoneToISO).map(([k, v]) => [v, k]),
);

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

  // Show detected country
  const detectedEl = document.getElementById("detectedCountry");
  if (detectedEl) {
    detectedEl.textContent = "Detected: " + countryISO;
  }

  // Setup phone field — user types full number with country code
  const phoneInput = document.getElementById("phone");
  if (phoneInput) {
    phoneInput.placeholder = "+1 234 567 8900";
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

  // Form submission -> 13Partners API
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const firstName = document.getElementById("first_name").value.trim();
    const lastName = document.getElementById("last_name").value.trim();
    const email = document.getElementById("emailokobo").value.trim();
    const rawPhone = document.getElementById("phone").value.trim();

    // Parse country code from phone (e.g. "+44 7400123456" -> cc="+44", local="7400123456")
    let parsedCC = countryCode; // fallback to auto-detected
    let phoneNumber = rawPhone;
    if (rawPhone.startsWith("+")) {
      const digitsOnly = rawPhone.replace(/[^\d]/g, "");
      // Try matching longest country code first (4 digits down to 1)
      let matched = false;
      for (let len = 4; len >= 1; len--) {
        const prefix = "+" + digitsOnly.substring(0, len);
        if (countryPhoneToISO[prefix]) {
          parsedCC = prefix;
          countryISO = countryPhoneToISO[prefix];
          phoneNumber = digitsOnly.substring(len);
          matched = true;
          break;
        }
      }
      if (!matched) {
        // No match — send full number as-is
        phoneNumber = digitsOnly;
      }
    }

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
    if (rawPhone.replace(/[^\d]/g, "").length < 7) {
      showFieldError(
        "phone",
        "Enter full number with country code (e.g. +44 7400123456)",
      );
      hasError = true;
    }
    if (hasError) return;

    showOverlay("Registering your account...");

    const clientIP = await getClientIP();

    // Collect UTM params from URL
    const utmParams = {};
    [
      "utm_source",
      "utm_medium",
      "utm_campaign",
      "utm_term",
      "utm_content",
    ].forEach((key) => {
      const val = urlParams.get(key);
      if (val) utmParams[key] = val;
    });

    const payload = {
      first_name: firstName,
      last_name: lastName,
      email: email,
      phonecc: parsedCC,
      phone: phoneNumber,
      country: countryISO,
      user_ip: clientIP,
      keitaro_id: clickid.replace("clickid_", ""),
      description: "",
      ...utmParams,
    };

    try {
      const response = await fetch("reg_leads.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      const result = await response.json();
      const responseJson = JSON.stringify(result, null, 2);
      console.log(
        "13Partners API full response:",
        responseJson,
        "HTTP:",
        response.status,
      );
      sessionStorage.setItem("lastApiResponse", responseJson);
      sessionStorage.setItem("lastApiStatus", String(response.status));
      alert("API Response (HTTP " + response.status + "):\n\n" + responseJson);

      // Deep search for redirect URL in any nested structure
      function findRedirectUrl(obj) {
        if (!obj || typeof obj !== "object") return null;
        // Check common redirect field names at current level
        const urlKeys = [
          "redirect",
          "redirect_url",
          "redirectUrl",
          "url",
          "autologin",
          "autologin_url",
          "login_url",
          "broker_url",
          "link",
        ];
        for (const key of urlKeys) {
          if (obj[key]) {
            if (typeof obj[key] === "string" && obj[key].startsWith("http"))
              return obj[key];
            if (
              typeof obj[key] === "object" &&
              obj[key].url &&
              typeof obj[key].url === "string"
            )
              return obj[key].url;
          }
        }
        // Recurse into nested objects
        for (const val of Object.values(obj)) {
          if (val && typeof val === "object") {
            const found = findRedirectUrl(val);
            if (found) return found;
          }
        }
        return null;
      }

      if (result.success === true || (response.ok && !result.errors)) {
        const redirectUrl = findRedirectUrl(result);
        console.log("Detected redirect URL:", redirectUrl);
        overlayMsg.textContent = "Success! Redirecting to trading platform...";
        setTimeout(() => {
          window.location.href = redirectUrl || "thank_you.html";
        }, 1500);
      } else if (result.errors) {
        hideOverlay();
        const errorMessages = [];
        for (const [field, msgs] of Object.entries(result.errors)) {
          const msgList = Array.isArray(msgs) ? msgs : [msgs];
          errorMessages.push(msgList.join(", "));
          showFieldError(field, msgList.join(", "));
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
          alertEl.textContent =
            result.message || "Registration failed. Please try again.";
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
