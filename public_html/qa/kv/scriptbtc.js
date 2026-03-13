// In-memory database
let leadDatabase = {};
let countryCode, countryISO;

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

// Constants (API keys and affiliate codes are handled server-side in PHP proxies)
const userId = "67334eefad58e373ce60846f";
const networkName = "kv";
const domainName = "yyyyxyz.com";
const offerName = "wealthmatrix";
const offerWebsite = "https://wealthmatrix.com/";
const apiName = "kv";
const funnel = "wealthmatrix";

// Overlay styling
const style = document.createElement("style");
style.innerHTML = `
  .overlay { display: none; position: fixed; top: 0; left: 0; width: 100%;
  height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; }
  .loader { position: absolute; top: 50%; left: 50%;
  transform: translate(-50%, -50%); }
  .overlay-message { position: absolute; top: 60%; left: 50%;
  transform: translate(-50%, -50%); color: white; font-size: 24px; }
`;
document.head.appendChild(style);

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

function generateUniqueClickId() {
  const length = Math.floor(Math.random() * 7) + 9;
  const chars =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  return (
    "clickid_" +
    Array.from({ length }, () =>
      chars.charAt(Math.floor(Math.random() * chars.length)),
    ).join("")
  );
}

document.addEventListener("DOMContentLoaded", async () => {
  const investForm = document.querySelector('[data-id="form"]');
  const urlParams = new URLSearchParams(window.location.search);
  let clickid = urlParams.get("clickid");

  if (!clickid) {
    clickid = generateUniqueClickId();
    urlParams.set("clickid", clickid);
    window.location.replace(`${window.location.pathname}?${urlParams}`);
    return;
  }

  // Country detection
  try {
    const response = await fetch("https://geinfoinfo.mrtinaixii.workers.dev/");
    countryCode = await response.text(); // e.g. +44
    countryISO = countryPhoneToISO[countryCode] || "US"; // e.g. GB
  } catch (err) {
    console.error("Geo error:", err);
    countryCode = "+1";
    countryISO = "US";
  }

  const phoneInput = document.getElementById("phone");
  if (phoneInput) {
    phoneInput.placeholder = "Phone Number";
  }

  investForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    overlay.style.display = "block";
    message.innerText = "Registering you on the best brand...";

    const firstName = document.getElementById("first_name").value;
    const lastName = document.getElementById("last_name").value;
    const email = document.getElementById("emailokobo").value;
    const phone = document
      .getElementById("phone")
      .value
      .replace(/\D/g, "");
    const password = "085fiyXDV";
    const ip = await fetch("https://api.ipify.org?format=json")
      .then((res) => res.json())
      .then((data) => data.ip);

    // Lead payload (affc, bxc, vtc are injected server-side by reg_leads.php)
    const leadData = {
      profile: { firstName, lastName, email, password, phone },
      ip,
      funnel,
      landingURL: offerWebsite,
      geo: countryISO,
      lang: "EN",
      landingLang: "EN",
      subId: clickid.replace("clickid_", ""),
    };

    // CRM tracking payload
    const crmData = {
      user: userId,
      name: networkName,
      apiName: apiName,
      custom5: clickid.replace("clickid_", ""),
      ip,
      email,
      firstName,
      lastName,
      password,
      phone,
      affiliate_id: "153",
      offer_id: "1",
      aff_sub3: offerName,
      country_code: countryISO,
      offerName,
      offerWebsite,
    };

    // CRM logging
    fetch(
      "https://hidden-falls-93683-18ce9b6ad8bb.herokuapp.com/api/track/leads/submit_lead",
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(crmData),
      },
    );

    try {
      const res = await fetch(
        `https://${domainName}/${networkName}/reg_leads.php`,
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(leadData),
        },
      );

      const result = await res.json();
      console.log("Lead API response:", result);

      if (res.status === 201 && result.auto_login_url) {
        message.innerText = "We have successfully registered you.";
        setTimeout(() => {
          window.location.href = result.auto_login_url;
        }, 3000);

        // /newreg sync
        fetch(
          "https://hidden-falls-93683-18ce9b6ad8bb.herokuapp.com/api/track/leads/newreg",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              custom5: clickid.replace("clickid_", ""),
              ip,
              user: userId,
              name: networkName,
              apiName,
              leadData: result,
            }),
          },
        );
      } else {
        message.innerText = "Sorry, we can't register you now.";
      }
    } catch (err) {
      console.error("Lead submit error:", err);
      message.innerText = "Submission failed.";
    } finally {
      setTimeout(() => {
        overlay.style.display = "none";
      }, 4000);
    }
  });

  // Optional lead & FTD metrics
  try {
    const leadsData = await getLeads("2024-12-10", "2025-01-17");
    console.log("Leads Data:", leadsData);

    const ftdsData = await getFTDs("2024-12-18", "2025-01-17");
    console.log("FTDs Data:", ftdsData);
  } catch (error) {
    console.error("Error fetching leads or FTDs:", error);
  }

  async function getLeads(
    fromDate,
    toDate,
    goalTypeUuid = "7aa0d135-c11f-4046-b32e-b3810a0b34ee",
    page = 1,
    perPage = 100,
  ) {
    const baseUrl = `https://${domainName}/${apiName}/leads_proxy.php`;
    const params = new URLSearchParams({
      fromDate: new Date(fromDate).toISOString(),
      toDate: new Date(toDate).toISOString(),
      goalTypeUuid,
      page,
      perPage,
    });

    const res = await fetch(`${baseUrl}?${params.toString()}`);
    if (res.status !== 200) throw new Error("Lead fetch error");
    return await res.json();
  }

  async function getFTDs(
    fromDate,
    toDate,
    goalTypeUuid = "adf73073-70ab-40b1-84dc-a5c958a36f34",
    page = 1,
    perPage = 100,
  ) {
    const baseUrl = `https://${domainName}/${apiName}/ftd_proxy.php`;
    const params = new URLSearchParams({
      fromDate: new Date(fromDate).toISOString(),
      toDate: new Date(toDate).toISOString(),
      goalTypeUuid,
      page,
      perPage,
    });

    const res = await fetch(`${baseUrl}?${params.toString()}`);
    if (res.status !== 200) throw new Error("FTD fetch error");
    return await res.json();
  }
});
