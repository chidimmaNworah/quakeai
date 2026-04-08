(function () {
  // Detect active section from URL path
  var path = window.location.pathname;
  var active = "hub";
  if (path.indexOf("/openaff") === 0) active = "openaff";
  else if (path.indexOf("/ch") === 0) active = "ch";
  else if (path.indexOf("/altstream") === 0) active = "altstream";
  else if (path.indexOf("/13partners") === 0) active = "13partners";

  // Inject navbar CSS
  var css = document.createElement("style");
  css.textContent =
    ".gnav{position:fixed;top:0;left:0;right:0;height:48px;background:#0d1130;border-bottom:1px solid rgba(255,255,255,.1);display:flex;align-items:center;padding:0 24px;font-family:Inter,-apple-system,sans-serif;z-index:10000}" +
    ".gnav *{box-sizing:border-box;margin:0;padding:0}" +
    ".gnav-brand{font-size:15px;font-weight:700;background:linear-gradient(135deg,#00d4ff,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;text-decoration:none;margin-right:32px;white-space:nowrap}" +
    ".gnav-items{display:flex;gap:2px;list-style:none;height:100%;align-items:stretch}" +
    ".gnav-item{position:relative;display:flex;align-items:stretch}" +
    ".gnav-link{display:flex;align-items:center;gap:5px;padding:0 16px;color:#a0a3bd;font-size:13px;font-weight:500;text-decoration:none;transition:color .2s;border-bottom:2px solid transparent;white-space:nowrap}" +
    ".gnav-link:hover{color:#fff}" +
    ".gnav-link.active{color:#00d4ff;border-bottom-color:#00d4ff}" +
    ".gnav-arrow{font-size:9px;transition:transform .2s}" +
    ".gnav-dropdown{display:none;position:absolute;top:100%;left:0;min-width:180px;background:#161b42;border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:6px 0;box-shadow:0 8px 24px rgba(0,0,0,.5)}" +
    ".gnav-item:hover .gnav-dropdown{display:block}" +
    ".gnav-item:hover .gnav-arrow{transform:rotate(180deg)}" +
    ".gnav-dropdown a{display:block;padding:10px 16px;color:#a0a3bd;font-size:13px;text-decoration:none;transition:background .15s,color .15s}" +
    ".gnav-dropdown a:hover{background:rgba(0,212,255,.08);color:#fff}" +
    ".gnav-spacer{height:48px;flex-shrink:0}" +
    "@media(max-width:640px){.gnav{padding:0 12px}.gnav-brand{font-size:13px;margin-right:12px}.gnav-link{padding:0 10px;font-size:12px}}";
  document.head.appendChild(css);

  // Campaign config
  var campaigns = [
    {
      id: "openaff",
      label: "OpenAFF",
      links: [
        { label: "Landing Page", href: "/openaff/" },
        { label: "Dashboard", href: "/openaff/dashboard.php" },
      ],
    },
    {
      id: "ch",
      label: "CH",
      links: [{ label: "Landing Page", href: "/ch/btcprft.html" }],
    },
    {
      id: "altstream",
      label: "Altstream",
      links: [
        { label: "Landing Page", href: "/altstream/" },
        { label: "Dashboard", href: "/altstream/dashboard.php" },
      ],
    },
    {
      id: "13partners",
      label: "13Partners",
      links: [
        { label: "Landing Page", href: "/13partners/" },
        { label: "Dashboard", href: "/13partners/dashboard.php" },
      ],
    },
  ];

  // Build navbar HTML
  var nav = document.createElement("nav");
  nav.className = "gnav";
  var html = '<a href="/" class="gnav-brand">Campaign Hub</a>';
  html += '<ul class="gnav-items">';

  for (var i = 0; i < campaigns.length; i++) {
    var c = campaigns[i];
    var isActive = active === c.id;
    if (c.links.length === 1) {
      html +=
        '<li class="gnav-item"><a class="gnav-link' +
        (isActive ? " active" : "") +
        '" href="' +
        c.links[0].href +
        '">' +
        c.label +
        "</a></li>";
    } else {
      html +=
        '<li class="gnav-item"><a class="gnav-link' +
        (isActive ? " active" : "") +
        '" href="' +
        c.links[0].href +
        '">' +
        c.label +
        ' <span class="gnav-arrow">&#9660;</span></a>';
      html += '<div class="gnav-dropdown">';
      for (var j = 0; j < c.links.length; j++) {
        html +=
          '<a href="' + c.links[j].href + '">' + c.links[j].label + "</a>";
      }
      html += "</div></li>";
    }
  }
  html += "</ul>";
  nav.innerHTML = html;

  // Insert navbar + spacer
  var spacer = document.createElement("div");
  spacer.className = "gnav-spacer";
  document.body.insertBefore(spacer, document.body.firstChild);
  document.body.insertBefore(nav, spacer);

  // Adjust existing fixed-position headers so they sit below the navbar
  setTimeout(function () {
    var headers = document.querySelectorAll("header, .header");
    for (var k = 0; k < headers.length; k++) {
      var el = headers[k];
      if (el === nav) continue;
      var pos = window.getComputedStyle(el).position;
      if (pos === "fixed") {
        el.style.top = "48px";
      }
    }
    // Adjust hero sections that were padded for a fixed header
    var heroes = document.querySelectorAll(".hero");
    for (var m = 0; m < heroes.length; m++) {
      var pt = parseInt(window.getComputedStyle(heroes[m]).paddingTop, 10) || 0;
      if (pt >= 100) {
        heroes[m].style.paddingTop = pt + 48 + "px";
      }
    }
  }, 0);
})();
