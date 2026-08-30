/* =====================================================================
 *  local_sebkiosk / exam-ui.js
 *  Loaded on every page via $CFG->additionalhtmlfooter (see config.php).
 *  Does nothing unless the page is a quiz attempt.
 *
 *   1. blocks copy / cut / paste / right-click / drag on the attempt
 *   2. IN REAL SEB ONLY: auto-submits the attempt when the candidate
 *      leaves SEB (pagehide / tab hidden) without submitting - so it
 *      can never be resumed. Internal quiz navigation is exempt.
 *   3. candidate-experience polish: a progress bar, a colour legend
 *      under the question navigation, and low/critical timer states.
 * ===================================================================== */
(function () {
  "use strict";

  var body = document.body;

  /* -----------------------------------------------------------------
   * 0. LOGIN PAGE : build the split-view left panel (campus photo +
   *    college info). CSS in custom.scss styles .itm-loginhero.
   * --------------------------------------------------------------- */
  if (body && / pagelayout-login /.test(" " + body.className + " ")) {
    (function buildLoginChrome() {
      var page = document.getElementById("page");
      if (!page) { return; }

      // LEFT : auto-advancing ITM GOI campus slideshow + college info
      if (!document.querySelector(".itm-loginhero")) {
        var base = ((window.M && M.cfg && M.cfg.wwwroot) || "") + "/local/sebkiosk/";
        var pics = ["campus.jpg", "campus2.jpg"];   // the two itmgoi.in sliders
        var slides = pics.map(function (f, i) {
          return '<div class="itm-hero-slide' + (i === 0 ? " is-active" : "") +
                 '" style="background-image:url(' + base + f + ')"></div>';
        }).join("");

        var hero = document.createElement("aside");
        hero.className = "itm-loginhero";
        hero.innerHTML =
          '<div class="itm-hero-slides">' + slides + '</div>' +
          '<div class="itm-hero-shade"></div>' +
          '<div class="itm-hero-body">' +
            '<div class="itm-hero-kicker">Online Examination Portal</div>' +
            '<h2>ITM Group of Institutions,<br>Gwalior</h2>' +
            '<div class="itm-hero-rule"></div>' +
            '<p>AICTE-approved &middot; RGPV-affiliated<br>' +
            'NH-75, opp. NRI College, Gwalior, Madhya Pradesh</p>' +
            '<div class="itm-hero-assure">Secure &middot; AI-proctored &middot; Examination Cell</div>' +
          '</div>';
        page.insertBefore(hero, page.firstChild);

        // auto crossfade every 6s (respect reduced-motion)
        var reduce = window.matchMedia &&
                     window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        var els = hero.querySelectorAll(".itm-hero-slide");
        if (els.length > 1 && !reduce) {
          var cur = 0;
          setInterval(function () {
            els[cur].classList.remove("is-active");
            cur = (cur + 1) % els.length;
            els[cur].classList.add("is-active");
          }, 6000);
        }
      }

      // RIGHT : fill the space around the card - feature chips + support bar
      var content = document.getElementById("page-content");
      if (content && !document.querySelector(".itm-loginaside")) {
        var aside = document.createElement("div");
        aside.className = "itm-loginaside";
        aside.innerHTML =
          '<div class="itm-chips">' +
            '<span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/></svg>Locked-down browser</span>' +
            '<span><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="14" height="12" rx="2"/><path d="M17 10l4-2v8l-4-2"/></svg>AI proctoring</span>' +
            '<span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z"/></svg>Instant result</span>' +
          '</div>' +
          '<div class="itm-support">' +
            'Examination Cell &nbsp;&middot;&nbsp; examcell@itmgoi.in &nbsp;&middot;&nbsp; +91&nbsp;751&nbsp;244&nbsp;0058' +
            '<span class="itm-copy">&copy; ITM Group of Institutions, Gwalior</span>' +
          '</div>';
        content.appendChild(aside);
      }
    })();
  }

  /* -----------------------------------------------------------------
   * 0b. DASHBOARD : a branded welcome banner above the blocks.
   * --------------------------------------------------------------- */
  if (body && (body.id === "page-my-index" ||
               / pagelayout-mydashboard /.test(" " + body.className + " "))) {
    (function buildDashHero() {
      if (document.querySelector(".itm-dashhero")) { return; }
      var main = document.querySelector('[role="main"]') ||
                 document.getElementById("region-main") ||
                 document.querySelector(".region-main-content");
      if (!main) { return; }

      var name = "";
      var srcs = [
        document.querySelector(".usermenu .usertext"),
        document.querySelector(".usermenu img.userpicture"),
        document.querySelector('.usermenu a[title], .usermenu a[aria-label]'),
        document.querySelector(".logininfo a")
      ];
      for (var i = 0; i < srcs.length && !name; i++) {
        var e = srcs[i];
        if (!e) { continue; }
        name = (e.getAttribute("title") || e.getAttribute("aria-label") ||
                e.getAttribute("alt") || e.textContent || "").trim();
      }
      name = name.replace(/^\s*(picture of|user menu|log ?in as)\s*/i, "")
                 .replace(/[(),]/g, " ").trim().split(/\s+/)[0];
      if (/^(you|are|as)$/i.test(name)) { name = ""; }

      var now = new Date();
      var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
      var months = ["January", "February", "March", "April", "May", "June", "July",
        "August", "September", "October", "November", "December"];
      var dateStr = days[now.getDay()] + ", " + now.getDate() + " " +
        months[now.getMonth()] + " " + now.getFullYear();

      var base = ((window.M && M.cfg && M.cfg.wwwroot) || "") + "/local/sebkiosk/";
      var pics = ["campus.jpg", "campus2.jpg"];
      var slides = pics.map(function (f, i) {
        return '<div class="itm-hero-slide' + (i === 0 ? " is-active" : "") +
               '" style="background-image:url(' + base + f + ')"></div>';
      }).join("");

      var hero = document.createElement("section");
      hero.className = "itm-dashhero";
      hero.innerHTML =
        '<div class="itm-hero-slides">' + slides + '</div>' +
        '<div class="itm-hero-shade"></div>' +
        '<div class="itm-dashhero-in">' +
          '<div class="itm-dashhero-logo"><img src="' + base + 'itm-logo.png" alt="ITM Group of Institutions, Gwalior"></div>' +
          '<div class="itm-dashhero-text">' +
            '<div class="itm-dashhero-kicker">Online Examination Portal</div>' +
            '<h2>Welcome back' + (name ? ", " + escapeHtml(name) : "") + '</h2>' +
            '<p>' + dateStr + ' &nbsp;&middot;&nbsp; ITM Group of Institutions, Gwalior</p>' +
          '</div>' +
          '<div class="itm-dashhero-badge" aria-hidden="true">' +
            '<svg viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/></svg>' +
            '<span>Secure &middot; AI-proctored</span>' +
          '</div>' +
        '</div>';
      main.insertBefore(hero, main.firstChild);

      var reduce = window.matchMedia &&
                   window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      var els = hero.querySelectorAll(".itm-hero-slide");
      if (els.length > 1 && !reduce) {
        var cur = 0;
        setInterval(function () {
          els[cur].classList.remove("is-active");
          cur = (cur + 1) % els.length;
          els[cur].classList.add("is-active");
        }, 6000);
      }
    })();
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }

  if (!body || body.id !== "page-mod-quiz-attempt") {
    return;
  }

  var form = document.getElementById("responseform") || body;
  var IS_SEB = /\bSEB\b/.test(navigator.userAgent) ||
               navigator.userAgent.indexOf("SEB/") !== -1 ||
               navigator.userAgent.indexOf("SafeExamBrowser") !== -1;

  /* -----------------------------------------------------------------
   * 1. clipboard / selection hardening
   * --------------------------------------------------------------- */
  ["copy", "cut", "paste", "contextmenu", "dragstart"].forEach(function (evt) {
    form.addEventListener(evt, function (e) {
      e.preventDefault();
      e.stopPropagation();
    }, true);
  });

  /* -----------------------------------------------------------------
   * 2. leave SEB  ->  auto-submit  (SEB clients only)
   * --------------------------------------------------------------- */
  if (IS_SEB) {
    var cfg = (window.M && window.M.cfg) || {};
    var qs = new URLSearchParams(location.search);
    var attempt = qs.get("attempt") || "";
    var cmid = qs.get("cmid") ||
      ((form.querySelector && form.querySelector("input[name=cmid]") || {}).value) || "";
    var endpoint = (cfg.wwwroot || "") + "/local/sebkiosk/finish.php";

    var internal = false;
    var mark = function () { internal = true; };

    // any quiz form submission = Next / Prev / nav-panel / Finish
    form.addEventListener && form.addEventListener("submit", mark, true);
    // clicks on quiz buttons / links that stay inside the attempt
    document.addEventListener("click", function (e) {
      var t = e.target && e.target.closest &&
              e.target.closest("button, input[type=submit], a[href]");
      if (!t) { return; }
      if (t.tagName === "A") {
        if (/\/mod\/quiz\//.test(t.href)) { mark(); }
      } else {
        mark();
      }
    }, true);
    window.addEventListener("pageshow", function () { internal = false; });

    var fired = false;
    var bail = function () {
      if (internal || fired) { return; }
      fired = true;
      try {
        var payload = "beacon=1&sesskey=" + encodeURIComponent(cfg.sesskey || "") +
          "&attempt=" + encodeURIComponent(attempt) +
          "&cmid=" + encodeURIComponent(cmid);
        navigator.sendBeacon(endpoint,
          new Blob([payload], { type: "application/x-www-form-urlencoded" }));
      } catch (e) { /* best effort */ }
    };
    window.addEventListener("pagehide", bail, false);
    document.addEventListener("visibilitychange", function () {
      if (document.visibilityState === "hidden") { bail(); }
    }, false);
  }

  /* -----------------------------------------------------------------
   * 3. candidate-experience polish  (each step is idempotent; the
   *    whole thing is retried a few times because Moodle renders the
   *    right-hand navigation block via JS after DOMContentLoaded)
   * --------------------------------------------------------------- */
  function el(tag, cls, text) {
    var n = document.createElement(tag);
    if (cls) { n.className = cls; }
    if (text != null) { n.textContent = text; }
    return n;
  }

  var progressUpdate = null;

  function buildProgressBar() {
    if (document.querySelector(".itm-progress")) { return true; }
    var navblock = document.getElementById("mod_quiz_navblock");
    var realForm = document.getElementById("responseform");
    if (!realForm || !navblock || !navblock.querySelectorAll(".qnbutton").length) { return false; }

    var bar = el("div", "itm-progress");
    bar.appendChild(el("span", "itm-progress-count", "0/0"));
    var track = el("span", "itm-progress-track");
    var fill = el("span", "itm-progress-fill");
    track.appendChild(fill);
    bar.appendChild(track);
    bar.appendChild(el("span", null, "answered"));
    realForm.parentNode.insertBefore(bar, realForm);

    var count = bar.querySelector(".itm-progress-count");
    progressUpdate = function () {
      var btns = navblock.querySelectorAll(".qnbutton");
      var tot = btns.length || 1;
      // Moodle 4.5 marks a saved answer with .answersaved
      var done = navblock.querySelectorAll(
        ".qnbutton.answersaved, .qnbutton.answered, .qnbutton.complete").length;
      fill.style.width = (done / tot * 100) + "%";
      count.textContent = done + "/" + btns.length;
    };
    progressUpdate();
    realForm.addEventListener("change", function () { setTimeout(progressUpdate, 60); });
    try {
      new MutationObserver(progressUpdate).observe(navblock, {
        subtree: true, attributes: true, attributeFilter: ["class"]
      });
    } catch (e) { /* ignore */ }
    return true;
  }

  function buildLegend() {
    var navblock = document.getElementById("mod_quiz_navblock");
    if (!navblock || navblock.querySelector(".itm-navlegend")) { return !!navblock; }
    if (!navblock.querySelectorAll(".qnbutton").length) { return false; }

    var host = navblock.querySelector(".card-body") || navblock;
    var legend = el("div", "itm-navlegend");
    [["", "Not answered"], ["is-answered", "Answered"],
     ["is-current", "Current"], ["is-flagged", "Flagged"]].forEach(function (p) {
      var s = document.createElement("span");
      s.appendChild(el("i", p[0] || null));
      s.appendChild(document.createTextNode(p[1]));
      legend.appendChild(s);
    });
    var qb = host.querySelector(".qn_buttons");
    if (qb && qb.parentNode) { qb.parentNode.insertBefore(legend, qb.nextSibling); }
    else { host.appendChild(legend); }
    return true;
  }

  var timerHooked = false;
  function hookTimer() {
    if (timerHooked) { return true; }
    var timer = document.getElementById("quiz-timer");
    var timeLeft = document.getElementById("quiz-time-left");
    if (!timer || !timeLeft) { return false; }
    timerHooked = true;
    var tick = function () {
      var m = /(\d+):(\d\d):(\d\d)|(\d+):(\d\d)/.exec(timeLeft.textContent || "");
      if (!m) { return; }
      var secs = (m[1] !== undefined)
        ? (parseInt(m[1], 10) * 3600 + parseInt(m[2], 10) * 60 + parseInt(m[3], 10))
        : (parseInt(m[4], 10) * 60 + parseInt(m[5], 10));
      timer.classList.toggle("itm-low", secs <= 300 && secs > 60);
      timer.classList.toggle("itm-critical", secs <= 60);
    };
    tick();
    setInterval(tick, 1000);
    return true;
  }

  // Moodle 4.5 does NOT put a .flagged class on .que, so mirror the flag
  // toggle state onto .que as .itm-flagged for the stylesheet to hook.
  var flagsWired = false;
  function wireFlagState() {
    var flags = document.querySelectorAll(".que .questionflag");
    if (!flags.length) { return false; }
    flagsWired = true;
    Array.prototype.forEach.call(flags, function (fq) {
      var que = fq.closest(".que");
      var btn = fq.querySelector("a.aabtn, [role=button], button");
      var sync = function () {
        if (!que) { return; }
        var on = /remove/i.test((btn && btn.textContent) || "") ||
                 (btn && btn.getAttribute("aria-pressed") === "true");
        que.classList.toggle("itm-flagged", !!on);
      };
      sync();
      if (btn) {
        btn.addEventListener("click", function () { setTimeout(sync, 50); });
      }
      try {
        new MutationObserver(sync).observe(fq, {
          subtree: true, childList: true, attributes: true,
          attributeFilter: ["aria-pressed", "class", "value"]
        });
      } catch (e) { /* ignore */ }
    });
    return true;
  }

  // whole answer option (the padded card, not just the label) selects its input
  var cardsWired = false;
  function wireOptionCards() {
    if (cardsWired) { return true; }
    var rows = document.querySelectorAll(".que .answer > div");
    if (!rows.length) { return false; }
    cardsWired = true;
    document.addEventListener("click", function (e) {
      var row = e.target && e.target.closest && e.target.closest(".que .answer > div");
      if (!row) { return; }
      if (/^(INPUT|LABEL|A|BUTTON)$/.test(e.target.tagName) || e.target.closest("label, a")) {
        return;                       // native control already handles it
      }
      var input = row.querySelector('input[type="radio"], input[type="checkbox"]');
      if (input && !input.disabled) {
        input.checked = (input.type === "checkbox") ? !input.checked : true;
        input.dispatchEvent(new Event("change", { bubbles: true }));
      }
    }, true);
    return true;
  }

  var tries = 0;
  function enhance() {
    var a = buildProgressBar();
    var b = buildLegend();
    var c = hookTimer();
    var d = wireOptionCards();
    var e = wireFlagState();
    if ((a && b && c && d && e) || ++tries > 12) { return; }
    setTimeout(enhance, 400);
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", enhance);
  } else {
    enhance();
  }
})();
