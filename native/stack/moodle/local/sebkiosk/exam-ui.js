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
   * Moodle's core/moremenu measures nav-item widths on load; when a
   * webfont finishes loading afterwards that measurement is stale and
   * items wrongly collapse into a "More" dropdown. Nudge a re-measure
   * once fonts settle. (Site-wide, cheap, no-op if already correct.)
   * --------------------------------------------------------------- */
  if (document.fonts && document.fonts.ready && typeof document.fonts.ready.then === "function") {
    document.fonts.ready.then(function () {
      window.dispatchEvent(new Event("resize"));
    });
  }

  /* -----------------------------------------------------------------
   * Thin maroon utility strip above the navbar (echoes itmgoi.in's
   * two-tier header). Skipped on the login page (its own full-bleed
   * branding) and on a live exam attempt (kept distraction-free).
   * CSS: custom.scss #itm-topstrip / .itm-has-topstrip.
   * --------------------------------------------------------------- */
  if (body &&
      !/ pagelayout-login /.test(" " + body.className + " ") &&
      body.id !== "page-mod-quiz-attempt" &&
      !document.getElementById("itm-topstrip")) {
    var itmStrip = document.createElement("div");
    itmStrip.id = "itm-topstrip";
    itmStrip.innerHTML =
      '<span class="itm-topstrip-l">Online Examination Portal</span>' +
      '<span class="itm-topstrip-r">Secure&nbsp;&middot;&nbsp;AI&#8209;proctored&nbsp;&middot;&nbsp;Instant results</span>';
    body.insertBefore(itmStrip, body.firstChild);
    body.classList.add("itm-has-topstrip");
  }

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
        var base = "/local/sebkiosk/";   // root-relative: works on localhost or a tunnel host
        var pics = ["campus3.jpg", "campus.jpg", "campus2.jpg"];   // Sandipani block + the two itmgoi.in sliders
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
            '<div class="itm-tagline">' +
              '<span class="itm-tl-rule"></span>' +
              '<span class="itm-tl-text">Think Big &middot; Think Beyond</span>' +
              '<span class="itm-tl-rule itm-tl-rule-r"></span>' +
            '</div>' +
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

      var base = "/local/sebkiosk/";   // root-relative: works on localhost or a tunnel host
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

  /* -----------------------------------------------------------------
   * 0c. FRONT PAGE : "Available courses" list -> clickable accent cards.
   *     CSS (custom.scss #frontpage-available-course-list) turns the
   *     plain .coursebox list into a grid; this adds the per-card accent,
   *     an initial-letter chip, and a whole-card overlay link.
   * --------------------------------------------------------------- */
  if (body && (body.id === "page-site-index" ||
               / pagelayout-frontpage /.test(" " + body.className + " ") ||
               / path-course-index /.test(" " + body.className + " "))) {

    /* site home only: a branded full-bleed hero + a 3-point assurance
       strip above the "Available courses" grid. */
    if (body.id === "page-site-index") {
      (function buildHomeHero() {
        if (document.querySelector(".itm-homehero")) { return; }
        var main = document.querySelector('[role="main"]') ||
                   document.getElementById("region-main") ||
                   document.querySelector(".region-main-content");
        if (!main) { return; }

        var base = "/local/sebkiosk/";   // root-relative: works on localhost or a tunnel host
        // Sandipani Prakhand academic block leads the home-page slideshow.
        var pics = ["campus3.jpg", "campus.jpg", "campus2.jpg"];
        var slides = pics.map(function (f, i) {
          return '<div class="itm-hero-slide' + (i === 0 ? " is-active" : "") +
                 '" style="background-image:url(' + base + f + ')"></div>';
        }).join("");

        function stat(v, l) {
          return '<div class="itm-hstat"><b>' + v + '</b><span>' + l + '</span></div>';
        }

        var hero = document.createElement("section");
        hero.className = "itm-dashhero itm-homehero";
        hero.innerHTML =
          '<div class="itm-hero-slides">' + slides + '</div>' +
          '<div class="itm-hero-shade"></div>' +
          '<div class="itm-dashhero-in">' +
            '<div class="itm-dashhero-logo"><img src="' + base + 'itm-logo.png" alt="ITM Group of Institutions, Gwalior"></div>' +
            '<div class="itm-dashhero-text">' +
              '<div class="itm-dashhero-kicker">Online Examination Portal</div>' +
              '<h2>ITM Group of Institutions, <em>Gwalior</em></h2>' +
              '<div class="itm-tagline">' +
                '<span class="itm-tl-rule"></span>' +
                '<span class="itm-tl-text">Think Big &middot; Think Beyond</span>' +
                '<span class="itm-tl-rule itm-tl-rule-r"></span>' +
              '</div>' +
              '<p>Sit your exams in a locked-down, AI&#8209;proctored environment &mdash; ' +
              'fair for everyone, graded the moment you submit.</p>' +
              '<div class="itm-hero-cta">' +
                '<a class="btn itm-cta-primary" href="/my/">' +
                  '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>' +
                  'Go to my exams</a>' +
                '<a class="btn itm-cta-ghost" href="/login/index.php">' +
                  '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>' +
                  'Candidate sign in</a>' +
              '</div>' +
            '</div>' +
            '<div class="itm-dashhero-badge" aria-hidden="true">' +
              '<svg viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/></svg>' +
              '<span>Secure &middot; AI-proctored</span>' +
            '</div>' +
          '</div>' +
          '<div class="itm-hero-stats" aria-hidden="true">' +
            stat("100%", "Sessions proctored") +
            stat("AI", "Webcam monitoring") +
            stat("SEB", "Browser lockdown") +
            stat("Instant", "Auto-graded results") +
          '</div>';
        main.insertBefore(hero, main.firstChild);

        // section header, then the numbered assurance cards
        var sec = document.createElement("div");
        sec.className = "itm-sec-head";
        sec.innerHTML =
          '<div class="itm-sec-kicker">Exam Integrity</div>' +
          '<h2>Built to keep every exam <em>fair</em>.</h2>' +
          '<p>Every attempt runs under the same three safeguards &mdash; ' +
          'no exceptions, no advantage.</p>';
        hero.parentNode.insertBefore(sec, hero.nextSibling);

        function chip(n, svg, t, s) {
          return '<div class="itm-assur">' +
                 '<span class="itm-assur-n" aria-hidden="true">' + n + '</span>' +
                 '<svg viewBox="0 0 24 24" aria-hidden="true">' + svg + '</svg>' +
                 '<div><strong>' + t + '</strong><span>' + s + '</span></div></div>';
        }
        var assur = document.createElement("div");
        assur.className = "itm-home-assurance";
        assur.innerHTML =
          chip("01", '<rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
               "Locked-down browser", "Safe Exam Browser kiosk mode blocks every other app, tab and shortcut.") +
          chip("02", '<circle cx="12" cy="12" r="3.2"/><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>',
               "AI proctoring", "The webcam feed is monitored continuously for the whole attempt.") +
          chip("03", '<path d="M4 12l5 5L20 6"/>',
               "Instant results", "Answers are auto-graded the moment you submit &mdash; no waiting.");
        sec.parentNode.insertBefore(assur, sec.nextSibling);

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

      /* home page: give "Available courses" the section-header treatment */
      (function decorateCoursesHeading() {
        var list = document.getElementById("frontpage-available-course-list");
        if (!list || list.querySelector(".itm-courses-head")) { return; }
        var h = list.querySelector(":scope > h2") || list.querySelector("h2");
        var label = (h && (h.textContent || "").trim()) || "Available courses";
        if (h) { h.style.display = "none"; }
        var head = document.createElement("div");
        head.className = "itm-sec-head itm-courses-head";
        head.innerHTML =
          '<div class="itm-sec-kicker">Your assessments</div>' +
          '<h2>' + escapeHtml(label) + '</h2>';
        list.insertBefore(head, list.firstChild);
      })();

      /* home page: a premium brand footer at the foot of the content card */
      (function buildSiteFooter() {
        if (document.querySelector(".itm-sitefoot")) { return; }
        var main = document.querySelector('[role="main"]') ||
                   document.getElementById("region-main");
        if (!main) { return; }
        var base = "/local/sebkiosk/";
        var year = new Date().getFullYear();
        var f = document.createElement("footer");
        f.className = "itm-sitefoot";
        f.innerHTML =
          '<div class="itm-sitefoot-in">' +
            '<div class="itm-foot-brand">' +
              '<img src="' + base + 'itm-logo.png" alt="">' +
              '<div><strong>ITM Group of Institutions, Gwalior</strong>' +
              '<span>Online Examination Portal</span></div>' +
            '</div>' +
            '<ul class="itm-foot-accred">' +
              '<li>NAAC &lsquo;A&rsquo;</li><li>NBA</li><li>AICTE</li><li>RGPV</li>' +
            '</ul>' +
            '<div class="itm-foot-meta">' +
              'AICTE&#8209;approved &middot; RGPV&#8209;affiliated' +
              ' &nbsp;&middot;&nbsp; Gwalior, Madhya Pradesh' +
              ' &nbsp;&middot;&nbsp; Think Big &middot; Think Beyond' +
            '</div>' +
          '</div>' +
          '<div class="itm-foot-copy">&copy; ' + year +
          ' ITM Group of Institutions, Gwalior. All rights reserved.</div>';
        main.appendChild(f);
      })();

      /* home page: "How it works" 3-step timeline on a tinted band */
      (function buildHowItWorks() {
        var assur = document.querySelector(".itm-home-assurance");
        if (!assur || document.querySelector(".itm-howitworks")) { return; }
        function step(t, d) {
          return '<li class="itm-step"><h3>' + t + '</h3><p>' + d + '</p></li>';
        }
        var band = document.createElement("section");
        band.className = "itm-band itm-howitworks";
        band.innerHTML =
          '<div class="itm-sec-head">' +
            '<div class="itm-sec-kicker">How it works</div>' +
            '<h2>From sign&#8209;in to score in <em>three steps</em>.</h2>' +
          '</div>' +
          '<ol class="itm-steps">' +
            step("Sign in",
                 "Log in with the candidate ID and password issued by your department.") +
            step("Launch securely",
                 "Open the exam in Safe Exam Browser &mdash; the screen locks to the test for the whole attempt.") +
            step("Submit &amp; see your score",
                 "Objective answers are graded the instant you submit; written answers are released by your teacher.") +
          '</ol>';
        assur.parentNode.insertBefore(band, assur.nextSibling);
      })();

      /* home page: FAQ accordion on a tinted band, after the course grid */
      (function buildFaq() {
        var list = document.getElementById("frontpage-available-course-list");
        var main = document.querySelector('[role="main"]') ||
                   document.getElementById("region-main");
        if ((!list && !main) || document.querySelector(".itm-faq")) { return; }
        function qa(q, a) {
          return '<details class="itm-faq-item"><summary>' + q + '</summary>' +
                 '<div class="itm-faq-a">' + a + '</div></details>';
        }
        var band = document.createElement("section");
        band.className = "itm-band itm-faq";
        band.innerHTML =
          '<div class="itm-sec-head">' +
            '<div class="itm-sec-kicker">Good to know</div>' +
            '<h2>Before you <em>start</em>.</h2>' +
          '</div>' +
          '<div class="itm-faq-list">' +
            qa("What do I need to sit an exam?",
               "A laptop or desktop with a working webcam, a stable internet connection, and Safe Exam Browser installed &mdash; a download link is shown on the exam page.") +
            qa("Why does the browser lock down?",
               "Safe Exam Browser blocks other apps, tabs, screenshots and keyboard shortcuts so every candidate writes under identical conditions.") +
            qa("Is the webcam recording me?",
               "The webcam is used for exam-integrity monitoring only, for the length of the attempt. It is not shared outside the examination team.") +
            qa("When do I get my result?",
               "Multiple-choice and other objective questions are scored instantly on submission. Manually-marked questions are released by your department.") +
            qa("I lost connection mid&#8209;exam &mdash; what now?",
               "Reconnect and reopen the exam; your answers are saved as you go. If you are locked out, contact your invigilator straight away.") +
          '</div>';
        if (list && list.parentNode) { list.parentNode.insertBefore(band, list.nextSibling); }
        else { main.appendChild(band); }
      })();

      /* home page: gentle scroll-reveal for each section (motion-safe) */
      (function wireReveal() {
        var reduce = window.matchMedia &&
                     window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        var nodes = [].slice.call(document.querySelectorAll(
          ".itm-sec-head, .itm-home-assurance, .itm-steps, " +
          "#frontpage-available-course-list .courses, .itm-faq-list, .itm-sitefoot-in"));
        if (!nodes.length || reduce || !("IntersectionObserver" in window)) { return; }
        nodes.forEach(function (n) { n.classList.add("itm-reveal"); });
        var io = new IntersectionObserver(function (ents) {
          ents.forEach(function (e) {
            if (e.isIntersecting) {
              e.target.classList.add("is-in");
              io.unobserve(e.target);
            }
          });
        }, { rootMargin: "0px 0px -8% 0px", threshold: 0.04 });
        nodes.forEach(function (n) { io.observe(n); });
        // failsafe: never leave a section stuck invisible
        setTimeout(function () {
          nodes.forEach(function (n) { n.classList.add("is-in"); });
        }, 2200);
      })();
    }

    (function decorateCourseBoxes() {
      var boxes = document.querySelectorAll(
        "#frontpage-available-course-list .coursebox, .course_category_tree .coursebox");
      Array.prototype.forEach.call(boxes, function (box, i) {
        if (box.hasAttribute("data-itm-cbox")) { return; }
        var link = box.querySelector("h3.coursename a, .coursename a, a.aalink");
        if (!link) { return; }
        box.setAttribute("data-itm-cbox", "1");
        box.setAttribute("data-accent", String((i % 8) + 1));

        var name = (link.textContent || "").trim();
        var info = box.querySelector(".info");
        if (info && name && !info.querySelector(".itm-cbox-chip")) {
          var chip = document.createElement("span");
          chip.className = "itm-cbox-chip";
          chip.setAttribute("aria-hidden", "true");
          chip.textContent = name.charAt(0).toUpperCase();
          info.insertBefore(chip, info.firstChild);
        }

        if (!box.querySelector(".itm-cbox-link")) {
          var a = document.createElement("a");
          a.className = "itm-cbox-link";
          a.href = link.href;
          a.setAttribute("aria-label", name);
          a.tabIndex = -1;            // the visible course-name link stays the focus target
          box.appendChild(a);
        }
      });
    })();
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }

  /* -----------------------------------------------------------------
   * 0c. SITE ADMINISTRATION : a usability layer on the settings form
   *      - a sticky save bar with an unsaved-changes counter + a
   *        beforeunload guard
   *      - a section jump-nav built from the h3.main headings
   *      - a "filter these settings" box
   *      - collapse very long setting descriptions
   *     Pure enhancement. Every step feature-detects and bails cleanly;
   *     students never reach these pages (moodle/site:config required).
   *     CSS for the injected bits is in custom.scss (SITE ADMINISTRATION).
   * --------------------------------------------------------------- */
  if (body && / pagelayout-admin /.test(" " + body.className + " ")) {
    (function adminUI() {
      var form = document.getElementById("adminsettings");
      var wrap = form && form.querySelector(".settingsform");
      if (!form || !wrap) { return; }          // not a settings *form* page

      function reduceMotion() {
        return window.matchMedia &&
               window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      }
      function rows() { return wrap.querySelectorAll(".form-item.row"); }

      /* ---- change tracking + sticky save bar ------------------- */
      var saveBtn = form.querySelector('button[type="submit"], input[type="submit"]');
      var bar = null, guarding = false;

      function onBeforeUnload(e) { e.preventDefault(); e.returnValue = ""; }
      function guard(on) {
        if (on && !guarding) {
          window.addEventListener("beforeunload", onBeforeUnload);
          guarding = true;
        } else if (!on && guarding) {
          window.removeEventListener("beforeunload", onBeforeUnload);
          guarding = false;
        }
      }
      form.addEventListener("submit", function () { guard(false); }, true);

      function buildBar() {
        if (bar || !saveBtn) { return; }
        bar = document.createElement("div");
        bar.className = "itm-admin-savebar";
        bar.hidden = true;
        bar.innerHTML =
          '<span class="itm-admin-savecount"></span>' +
          '<span class="itm-admin-savebtns">' +
            '<button type="button" class="btn btn-outline-secondary btn-sm itm-admin-discard">Discard</button>' +
            '<button type="button" class="btn btn-primary btn-sm itm-admin-save">Save changes</button>' +
          '</span>';
        document.body.appendChild(bar);
        bar.querySelector(".itm-admin-save").addEventListener("click", function () {
          guard(false);
          if (saveBtn.form && saveBtn.form.requestSubmit) {
            saveBtn.form.requestSubmit(saveBtn.tagName === "BUTTON" ? saveBtn : undefined);
          } else {
            saveBtn.click();
          }
        });
        bar.querySelector(".itm-admin-discard").addEventListener("click", function () {
          guard(false);
          location.reload();
        });
      }
      function refreshBar() {
        buildBar();
        if (!bar) { return; }
        var n = wrap.querySelectorAll(".form-item.row.itm-changed").length;
        bar.hidden = n === 0;
        bar.querySelector(".itm-admin-savecount").textContent =
          n + (n === 1 ? " unsaved change" : " unsaved changes");
        guard(n > 0);
      }
      function markChanged(node) {
        var row = node && node.closest && node.closest(".form-item.row");
        if (!row || row.classList.contains("itm-changed")) { return; }
        row.classList.add("itm-changed");
        refreshBar();
      }
      form.addEventListener("change", function (e) { markChanged(e.target); }, true);
      form.addEventListener("input", function (e) { markChanged(e.target); }, true);

      /* ---- section jump-nav (wide screens) -------------------- */
      (function jumpNav() {
        var heads = wrap.querySelectorAll("h3.main");
        if (heads.length < 2) { return; }
        var nav = document.createElement("nav");
        nav.className = "itm-admin-jump";
        nav.setAttribute("aria-label", "Sections on this page");
        var ul = document.createElement("ul");
        Array.prototype.forEach.call(heads, function (h, i) {
          if (!h.id) { h.id = "itm-sec-" + i; }
          var a = document.createElement("a");
          a.href = "#" + h.id;
          a.textContent = (h.textContent || "").trim();
          a.addEventListener("click", function (ev) {
            ev.preventDefault();
            h.scrollIntoView({ behavior: reduceMotion() ? "auto" : "smooth", block: "start" });
            try { history.replaceState(null, "", "#" + h.id); } catch (e) {}
          });
          var li = document.createElement("li");
          li.appendChild(a);
          ul.appendChild(li);
          h._itmLink = a;
        });
        nav.appendChild(ul);
        form.insertBefore(nav, wrap);
        try {
          var io = new IntersectionObserver(function (ents) {
            ents.forEach(function (en) {
              if (en.isIntersecting && en.target._itmLink) {
                Array.prototype.forEach.call(ul.querySelectorAll("a"), function (x) {
                  x.classList.remove("is-active");
                });
                en.target._itmLink.classList.add("is-active");
              }
            });
          }, { rootMargin: "-90px 0px -70% 0px" });
          Array.prototype.forEach.call(heads, function (h) { io.observe(h); });
        } catch (e) { /* ignore */ }
      })();

      /* ---- filter these settings ------------------------------ */
      (function filterBox() {
        if (rows().length < 8) { return; }
        var box = document.createElement("div");
        box.className = "itm-admin-filter";
        box.innerHTML =
          '<input type="search" autocomplete="off" ' +
          'placeholder="Filter these settings…" aria-label="Filter these settings">' +
          '<span class="itm-admin-filter-n" aria-live="polite"></span>';
        wrap.insertBefore(box, wrap.firstChild);
        var input = box.querySelector("input");
        var nOut = box.querySelector(".itm-admin-filter-n");
        var t;
        input.addEventListener("input", function () {
          clearTimeout(t);
          t = setTimeout(apply, 120);
        });
        function apply() {
          var q = input.value.trim().toLowerCase();
          var shown = 0, total = 0;
          Array.prototype.forEach.call(rows(), function (row) {
            total++;
            var hit = !q || (row.textContent || "").toLowerCase().indexOf(q) !== -1;
            row.classList.toggle("itm-filter-hide", !hit);
            if (hit) { shown++; }
          });
          Array.prototype.forEach.call(wrap.querySelectorAll("h3.main"), function (h) {
            var any = false, n = h.nextElementSibling;
            while (n && !(n.tagName === "H3" && n.classList.contains("main"))) {
              if (n.classList && n.classList.contains("form-item") &&
                  !n.classList.contains("itm-filter-hide")) { any = true; break; }
              n = n.nextElementSibling;
            }
            var hide = !!q && !any;
            h.classList.toggle("itm-filter-hide", hide);
            var sib = h.nextElementSibling;
            if (sib && sib.classList && sib.classList.contains("formsettingheading")) {
              sib.classList.toggle("itm-filter-hide", hide);
            }
          });
          nOut.textContent = q ? (shown + " / " + total) : "";
        }
      })();

      /* ---- collapse long descriptions ------------------------- */
      (function collapseDesc() {
        try { if (localStorage.getItem("itm_admin_desc_expanded") === "1") { return; } }
        catch (e) {}
        Array.prototype.forEach.call(
          wrap.querySelectorAll(".form-item .form-description"), function (d) {
            var txt = (d.textContent || "").trim();
            if (txt.length < 170 || d.querySelector("ul, ol, table, pre, iframe")) { return; }
            d.classList.add("itm-desc-collapsed");
            var more = document.createElement("button");
            more.type = "button";
            more.className = "itm-desc-more";
            more.textContent = "Show more";
            more.addEventListener("click", function () {
              var collapsed = d.classList.toggle("itm-desc-collapsed");
              more.textContent = collapsed ? "Show more" : "Show less";
            });
            d.parentNode.insertBefore(more, d.nextSibling);
          });
      })();

      refreshBar();
    })();
  }

  /* -----------------------------------------------------------------
   * 0d. SITE ADMINISTRATION LANDING (/admin/search.php) : turn the
   *     wasted col-sm-3 gutter + flat link lists into a responsive
   *     card grid (one card per settings group).
   *     DOM: #region-main .tab-content > .tab-pane#link<cat>
   *            > .container-fluid > (.row + <hr>)*
   *          .row > .col-sm-3 [<h4>[<a>]] + .col(-sm-9) > ul.list-unstyled > li > a
   * --------------------------------------------------------------- */
  if (body && body.id === "page-admin-search") {
    (function adminLanding() {
      var containers = document.querySelectorAll("#region-main .tab-pane .container-fluid");
      if (!containers.length) { return; }

      function monogram(txt) {
        var m = (txt || "").trim().replace(/[^A-Za-z0-9]/g, "");
        return m ? m.charAt(0).toUpperCase() : "⚙";
      }
      function makeCard(title, href, listEl, accent) {
        var card = document.createElement("div");
        card.className = "itm-admin-card";
        card.setAttribute("data-accent", ((accent % 8) + 8) % 8 + 1);
        var t = (title || "").trim() || "General";
        var head = document.createElement(href ? "a" : "div");
        head.className = "itm-admin-card-head";
        if (href) { head.href = href; }
        var chip = document.createElement("span");
        chip.className = "itm-admin-card-chip";
        chip.textContent = monogram(t);
        var label = document.createElement("span");
        label.className = "itm-admin-card-title";
        label.textContent = t;
        head.appendChild(chip);
        head.appendChild(label);
        var b = document.createElement("div");
        b.className = "itm-admin-card-body";
        b.appendChild(listEl);
        card.appendChild(head);
        card.appendChild(b);
        return card;
      }

      Array.prototype.forEach.call(containers, function (container) {
        if (container.dataset.itmGrid) { return; }
        var rows = container.querySelectorAll(":scope > .row");
        if (!rows.length) { return; }

        var grid = document.createElement("div");
        grid.className = "itm-admin-cardgrid";
        var n = 0;                       // running index -> even accent spread

        Array.prototype.forEach.call(rows, function (row) {
          var h4 = row.querySelector("h4");
          var link = h4 && h4.querySelector("a");
          var ul = row.querySelector("ul");
          var hasItems = ul && ul.querySelector("li");
          if (hasItems) {
            grid.appendChild(makeCard(
              h4 ? h4.textContent : "",
              link ? link.getAttribute("href") : null, ul, n++));
          } else if (link) {
            // a category header whose own children are all sub-branches:
            // keep a header-only card that links to its category page
            grid.appendChild(makeCard(
              h4.textContent, link.getAttribute("href"),
              document.createElement("ul"), n++));
          }
        });

        if (!grid.children.length) { return; }
        container.dataset.itmGrid = "1";
        Array.prototype.forEach.call(
          container.querySelectorAll(":scope > hr, :scope > .row"),
          function (n) { n.remove(); });
        container.appendChild(grid);
      });
    })();
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
    var endpoint = "/local/sebkiosk/finish.php";   // root-relative

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
