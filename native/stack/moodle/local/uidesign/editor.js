/* local_uidesign - Design Studio overlay (Phase 1a + 1b).
 * Vanilla JS, one IIFE, no build step. Loaded from the head hook for admins only.
 * Theme:  live --itm-* token editing, global text scale, corner radius, heading font.
 * Edit:   point-and-click an element -> a floating toolbar (text / size / bold /
 *         colour / padding / align / hide / reset), scoped to this element or all
 *         like it.  Click-to-edit text on headings & labels.
 * Rules:  every override, on/off + delete + reset all.
 * Header: undo / redo for the session.  Every change auto-saves to save.php. */
(function () {
  "use strict";

  var CFG = window.UIDESIGN;
  if (!CFG || !CFG.saveurl) { return; }

  var ROOT = document.documentElement;
  var previewStyle = null;
  var studio = null;
  var pickMode = null;        // 'hide' | 'style' | null
  var outline = null;
  var pickframe = null;       // dim frame shown while picking
  var toolbar = null;
  var picked = null;          // currently selected element (style mode)
  var undoStack = [];
  var redoStack = [];

  // ---- token catalogue (Theme tab) ---------------------------------
  var TOKENS = [
    { g: "Brand", items: [
      { n: "--itm-maroon",     label: "Maroon — primary" },
      { n: "--itm-maroon-700", label: "Maroon — deep" },
      { n: "--itm-maroon-900", label: "Maroon — darkest" },
      { n: "--itm-gold",       label: "Gold — accent" },
      { n: "--itm-gold-hi",    label: "Gold — bright" }
    ] },
    { g: "Text & lines", items: [
      { n: "--itm-ink",   label: "Body text" },
      { n: "--itm-slate", label: "Muted text" },
      { n: "--itm-line",  label: "Hairlines / borders" },
      { n: "--itm-cream", label: "Warm panel" },
      { n: "--itm-tint",  label: "Section tint" }
    ] },
    { g: "Accent colours", items: [
      { n: "--itm-teal",   label: "Teal" },
      { n: "--itm-indigo", label: "Indigo" },
      { n: "--itm-amber",  label: "Amber" },
      { n: "--itm-steel",  label: "Steel" },
      { n: "--itm-forest", label: "Forest" }
    ] }
  ];
  var FONTS = [
    ['"Playfair Display", "Source Serif 4", Georgia, serif', "Playfair Display"],
    ['"Source Serif 4", Georgia, serif', "Source Serif 4"],
    ['Georgia, "Times New Roman", serif', "Georgia"],
    ['"Inter", system-ui, sans-serif', "Inter (sans)"],
    ['system-ui, -apple-system, "Segoe UI", sans-serif', "System sans"]
  ];

  // ---- helpers ----------------------------------------------------
  function el(tag, cls, text) {
    var n = document.createElement(tag);
    if (cls) { n.className = cls; }
    if (text != null) { n.textContent = text; }
    return n;
  }

  // Feather-style line icons (24x24 viewBox, stroked with currentColor).
  var ICONS = {
    undo:     '<path d="M9 14 4 9l5-5"/><path d="M4 9h11a6 6 0 0 1 0 12H9"/>',
    redo:     '<path d="m15 14 5-5-5-5"/><path d="M20 9H9a6 6 0 0 0 0 12h6"/>',
    close:    '<path d="M18 6 6 18M6 6l12 12"/>',
    text:     '<path d="M5 5h14M12 5v14M9 19h6"/>',
    minus:    '<path d="M5 12h14"/>',
    plus:     '<path d="M12 5v14M5 12h14"/>',
    bold:     '<path d="M6 4h8a4 4 0 0 1 0 8H6zM6 12h9a4 4 0 0 1 0 8H6z"/>',
    palette:  '<path d="M12 3a9 9 0 1 0 0 18c1 0 1.6-.8 1.6-1.6 0-.5-.2-.9-.5-1.2-.3-.3-.5-.8-.5-1.2a1.6 1.6 0 0 1 1.6-1.6H16a5 5 0 0 0 5-5c0-3.9-4-6.8-9-6.8Z"/><circle cx="7.5" cy="10.5" r="1"/><circle cx="12" cy="7.5" r="1"/><circle cx="16.5" cy="10.5" r="1"/>',
    fill:     '<path d="m11 3 8 8-8 8a2 2 0 0 1-3 0l-5-5a2 2 0 0 1 0-3z"/><path d="m5 13 8 0"/><path d="M20 15s2 2.3 2 3.6A2 2 0 0 1 20 21a2 2 0 0 1-2-2.4C18 17.3 20 15 20 15Z"/>',
    image:    '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="m21 16-5-5L5 20"/>',
    square:   '<rect x="4" y="4" width="16" height="16" rx="1"/>',
    align:    '<path d="M4 6h16M4 12h10M4 18h16"/>',
    eyeoff:   '<path d="M10.7 5.1A9.6 9.6 0 0 1 12 5c6 0 10 7 10 7a17.6 17.6 0 0 1-2.4 3.2M6.6 6.6A17.3 17.3 0 0 0 2 12s4 7 10 7a9.5 9.5 0 0 0 5.4-1.6"/><path d="m2 2 20 20"/><path d="M9.5 9.5a3 3 0 0 0 4.2 4.2"/>',
    reset:    '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/>',
    search:   '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
    external: '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/>',
    trash:    '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-13M9 7V4h6v3"/>',
    check:    '<path d="M20 6 9 17l-5-5"/>',
    wand:     '<path d="M15 4V2M15 10V8M9 15H7M23 15h-2M17.5 6.5 19 5"/><path d="m14 7 3 3L8 19l-3-3z"/>'
  };
  function svg(name, size) {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" ' +
      'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"' +
      (size ? ' width="' + size + '" height="' + size + '"' : '') + '>' + (ICONS[name] || '') + '</svg>';
  }
  function getVar(name) { return getComputedStyle(ROOT).getPropertyValue(name).trim(); }
  function toHex(c) {
    c = (c || "").trim();
    if (/^#[0-9a-f]{6}$/i.test(c)) { return c.toLowerCase(); }
    if (/^#[0-9a-f]{3}$/i.test(c)) { return "#" + c[1] + c[1] + c[2] + c[2] + c[3] + c[3]; }
    var m = c.match(/rgba?\(([^)]+)\)/i);
    if (m) {
      var p = m[1].split(",").map(function (x) { return parseInt(x, 10); });
      return "#" + [p[0], p[1], p[2]].map(function (v) {
        v = Math.max(0, Math.min(255, v || 0)).toString(16);
        return v.length === 1 ? "0" + v : v;
      }).join("");
    }
    return "#888888";
  }
  function post(params) {
    params.sesskey = CFG.sesskey;
    return fetch(CFG.saveurl, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      credentials: "same-origin",
      body: new URLSearchParams(params)
    }).then(function (r) { return r.json(); }).catch(function () { return { ok: false, error: "network" }; });
  }
  function toast(msg, isErr) {
    var t = studio.querySelector("#uid-toast");
    t.textContent = msg;
    t.className = isErr ? "err show" : "show";
    clearTimeout(toast._t);
    toast._t = setTimeout(function () { t.className = t.className.replace(" show", ""); }, 1700);
  }

  // ---- in-panel dialog (replaces window.prompt / window.confirm) ----
  function uiDialog(opts) {
    return new Promise(function (resolve) {
      var m = studio.querySelector("#uid-modal");
      m.textContent = "";
      m.hidden = false;
      var card = el("div", "uid-modal-card");
      card.setAttribute("role", "dialog");
      card.setAttribute("aria-modal", "true");
      card.appendChild(el("h3", null, opts.title || "Confirm"));
      if (opts.message) { card.appendChild(el("p", null, opts.message)); }
      var input = null;
      if (opts.prompt) {
        input = el("input");
        input.type = "text";
        input.value = opts.value || "";
        if (opts.placeholder) { input.placeholder = opts.placeholder; }
        card.appendChild(input);
      }
      var acts = el("div", "uid-modal-actions");
      var cancel = el("button", "uid-btn ghost", opts.cancelLabel || "Cancel");
      var ok = el("button", "uid-btn" + (opts.danger ? " danger" : ""), opts.okLabel || "OK");
      cancel.type = "button";
      ok.type = "button";
      acts.appendChild(cancel);
      acts.appendChild(ok);
      card.appendChild(acts);
      m.appendChild(card);
      void m.offsetWidth;
      m.classList.add("is-open");
      (input || ok).focus();
      if (input) { input.select(); }

      var noVal = opts.prompt ? null : false;
      function done(val) {
        m.removeEventListener("keydown", onKey, true);
        m.removeEventListener("click", onClick);
        m.classList.remove("is-open");
        setTimeout(function () { m.hidden = true; m.textContent = ""; }, 160);
        resolve(val);
      }
      function onKey(e) {
        if (e.key === "Escape") { e.preventDefault(); done(noVal); }
        else if (e.key === "Enter") { e.preventDefault(); done(opts.prompt ? input.value : true); }
        else if (e.key === "Tab") {
          var f = card.querySelectorAll("input,button");
          var first = f[0], last = f[f.length - 1];
          if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
          else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        }
      }
      function onClick(e) {
        if (e.target === m || e.target === cancel) { done(noVal); }
        else if (e.target === ok) { done(opts.prompt ? input.value : true); }
      }
      m.addEventListener("keydown", onKey, true);
      m.addEventListener("click", onClick);
    });
  }
  function uiConfirm(title, message, opts) {
    opts = opts || {};
    return uiDialog({ title: title, message: message, okLabel: opts.okLabel, danger: opts.danger });
  }
  function uiPrompt(title, message, value, opts) {
    opts = opts || {};
    return uiDialog({ title: title, message: message, prompt: true, value: value,
      placeholder: opts.placeholder, okLabel: opts.okLabel });
  }
  function previewSheet() {
    if (!previewStyle) {
      previewStyle = el("style");
      previewStyle.id = "uid-preview";
      document.head.appendChild(previewStyle);
    }
    return previewStyle;
  }
  function addPreviewRule(css) { previewSheet().appendChild(document.createTextNode(css + "\n")); }
  function esc(s) {
    return String(s).replace(/[&<>"]/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c];
    });
  }
  function mkWarn(text) {
    var w = el("span", "uid-warn", " ⚠");
    w.title = text;
    return w;
  }
  function cssEsc(s) {
    return (window.CSS && CSS.escape) ? CSS.escape(s) : s.replace(/[^\w-]/g, "\\$&");
  }

  // Reasonably-stable selector for this exact element.
  function cssPath(node) {
    if (!node || node === document.body) { return "body"; }
    var parts = [], cur = node, depth = 0;
    while (cur && cur.nodeType === 1 && cur !== document.body && depth < 5) {
      if (cur.id && /^[A-Za-z][\w-]*$/.test(cur.id) &&
          document.querySelectorAll("#" + cssEsc(cur.id)).length === 1) {
        parts.unshift("#" + cur.id);
        return parts.join(" ");
      }
      var sel = cur.tagName.toLowerCase();
      var picked2 = usefulClass(cur);
      if (picked2) {
        sel += "." + picked2;
      } else if (cur.parentNode) {
        var sib = Array.prototype.filter.call(cur.parentNode.children, function (c) {
          return c.tagName === cur.tagName;
        });
        if (sib.length > 1) { sel += ":nth-of-type(" + (sib.indexOf(cur) + 1) + ")"; }
      }
      parts.unshift(sel);
      cur = cur.parentNode;
      depth++;
    }
    return parts.join(" ");
  }
  // Class-only path -> targets "all elements like this".
  function classPath(node) {
    var parts = [], cur = node, depth = 0;
    while (cur && cur.nodeType === 1 && cur !== document.body && depth < 4) {
      var c = usefulClass(cur);
      parts.unshift(c ? cur.tagName.toLowerCase() + "." + c : cur.tagName.toLowerCase());
      if (c) { break; }
      cur = cur.parentNode;
      depth++;
    }
    return parts.join(" ");
  }
  function usefulClass(node) {
    var classes = (typeof node.className === "string" ? node.className : "").trim().split(/\s+/)
      .filter(function (c) {
        return /^[A-Za-z][\w-]{1,40}$/.test(c) &&
               !/^(is-|uid-|active|show|collapsed|fade|open|selected|focus|col-|row$|d-|m[trblxy]?-|p[trblxy]?-)/.test(c);
      });
    for (var i = 0; i < classes.length; i++) {
      if (document.querySelectorAll("." + cssEsc(classes[i])).length <= 20) { return classes[i]; }
    }
    return null;
  }

  var BLOCK_CONTAINER = '#uid-root,.uid-reopen,.navbar,#nav-drawer,.drawer,' +
    '[data-region="drawer"],[data-region="fixed-drawer"],.editmode-switch-form,#page-footer';
  var BLOCK_EXACT = 'html,body,#page,#page-content,#region-main,[role="main"],.main-inner,main';
  function pickable(node) {
    if (!node || node.nodeType !== 1) { return false; }
    if (/^(HTML|BODY|SCRIPT|STYLE|LINK|META|NOSCRIPT)$/.test(node.tagName)) { return false; }
    if (node.closest(BLOCK_CONTAINER)) { return false; }
    if (node.matches && node.matches(BLOCK_EXACT)) { return false; }
    return true;
  }

  // ---- studio shell ---------------------------------------------
  function buildStudio() {
    if (studio) { return; }
    studio = el("div");
    studio.id = "uid-root";
    studio.hidden = true;
    studio.innerHTML =
      '<div id="uid-panel" role="dialog" aria-label="Design Studio">' +
        '<header>' +
          '<span class="uid-title">' +
            '<span class="uid-eyebrow">ITM Portal</span>' +
            '<b><span class="uid-dot"></span>Design Studio</b>' +
          '</span>' +
          '<button type="button" class="uid-h" data-uid="undo" title="Undo" aria-label="Undo" disabled>' + svg("undo") + '</button>' +
          '<button type="button" class="uid-h" data-uid="redo" title="Redo" aria-label="Redo" disabled>' + svg("redo") + '</button>' +
          '<button type="button" class="uid-h" data-uid="close" title="Close" aria-label="Close">' + svg("close") + '</button>' +
        '</header>' +
        '<div id="uid-tabs" role="tablist">' +
          '<button type="button" role="tab" data-tab="theme" class="is-active" aria-selected="true">Theme</button>' +
          '<button type="button" role="tab" data-tab="edit" aria-selected="false">Edit</button>' +
          '<button type="button" role="tab" data-tab="rules" aria-selected="false">Rules</button>' +
        '</div>' +
        '<div id="uid-publish" hidden>' +
          '<span></span>' +
          '<button type="button" data-uid="publish" class="uid-btn">' + svg("check", 14) + 'Publish</button>' +
          '<button type="button" data-uid="discard" class="uid-btn ghost">Discard</button>' +
        '</div>' +
        '<div id="uid-body">' +
          '<div class="uid-tab" data-tab="theme" role="tabpanel"></div>' +
          '<div class="uid-tab" data-tab="edit" role="tabpanel" hidden></div>' +
          '<div class="uid-tab" data-tab="rules" role="tabpanel" hidden></div>' +
        '</div>' +
      '</div>' +
      '<div id="uid-modal" hidden></div>' +
      '<div id="uid-toast"></div>';
    document.body.appendChild(studio);

    // Esc closes the Studio (unless a pick / modal / text edit owns it).
    document.addEventListener("keydown", function (e) {
      if (e.key !== "Escape" || !studio || studio.hidden || pickMode) { return; }
      if (!studio.querySelector("#uid-modal[hidden]")) { return; }
      var a = document.activeElement;
      if (a && (a.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(a.tagName))) { return; }
      closeStudio();
    });

    studio.addEventListener("click", function (e) {
      var t = e.target.closest("[data-uid],[data-tab]");
      if (!t) { return; }
      if (t.dataset.uid === "close") { closeStudio(); return; }
      if (t.dataset.uid === "undo") { doUndo(); return; }
      if (t.dataset.uid === "redo") { doRedo(); return; }
      if (t.dataset.uid === "publish") { doPublish(); return; }
      if (t.dataset.uid === "discard") { doDiscard(); return; }
      if (t.dataset.tab && t.parentNode.id === "uid-tabs") { switchTab(t.dataset.tab); }
    });
    syncPublishBar();
    renderTheme();
  }

  function switchTab(name) {
    Array.prototype.forEach.call(studio.querySelectorAll("#uid-tabs button"), function (b) {
      var on = b.dataset.tab === name;
      b.classList.toggle("is-active", on);
      b.setAttribute("aria-selected", on ? "true" : "false");
    });
    Array.prototype.forEach.call(studio.querySelectorAll(".uid-tab"), function (p) {
      p.hidden = p.dataset.tab !== name;
    });
    if (name === "edit") { renderEdit(); }
    if (name === "rules") { renderRules(); }
  }

  function openStudio() {
    buildStudio();
    studio.hidden = false;
    void studio.offsetWidth;
    setTimeout(function () { studio.classList.add("is-open"); }, 10);
    reopenHandle();
  }
  function closeStudio() {
    studio.classList.remove("is-open");
    stopPick();
    hideToolbar();
    setTimeout(function () { studio.hidden = true; reopenHandle(); }, 220);
  }

  var handle = null;
  function showLauncher() {
    if (!handle) {
      handle = el("button", "uid-reopen");
      handle.type = "button";
      handle.title = "Open the Design Studio";
      handle.innerHTML = svg("wand", 16) + "<span>Design</span>";
      handle.addEventListener("click", openStudio);
      document.body.appendChild(handle);
    }
    handle.hidden = false;
  }
  function reopenHandle() {
    showLauncher();
    handle.hidden = !studio.hidden;
  }

  // ---- THEME tab ----------------------------------------------
  function renderTheme() {
    var host = studio.querySelector('.uid-tab[data-tab="theme"]');
    host.textContent = "";
    TOKENS.forEach(function (grp) {
      var sec = el("div", "uid-sec");
      sec.appendChild(el("h4", null, grp.g));
      grp.items.forEach(function (it) {
        var row = el("div", "uid-row");
        row.appendChild(el("label", null, it.label));
        var inp = el("input"); inp.type = "color"; inp.value = toHex(getVar(it.n));
        var hex = el("span", "uid-hex", inp.value);
        var prev = null;
        inp.addEventListener("focus", function () { prev = getVar(it.n) || toHex(getVar(it.n)); });
        inp.addEventListener("input", function () {
          ROOT.style.setProperty(it.n, inp.value);
          hex.textContent = inp.value;
        });
        inp.addEventListener("change", function () {
          ROOT.style.setProperty(it.n, inp.value);
          hex.textContent = inp.value;
          saveToken(it.n, inp.value, it.label + " → " + inp.value, prev);
        });
        row.appendChild(inp);
        row.appendChild(hex);
        var rst = el("button", "uid-mini", "reset");
        rst.addEventListener("click", function () {
          ROOT.style.removeProperty(it.n);
          inp.value = toHex(getVar(it.n));
          hex.textContent = inp.value;
          deleteRuleBy("token", "*", it.n, "");
        });
        row.appendChild(rst);
        sec.appendChild(row);
      });
      host.appendChild(sec);
    });

    var s2 = el("div", "uid-sec");
    s2.appendChild(el("h4", null, "Shape & size"));
    s2.appendChild(sliderRow("Corner rounding", 0, 26, parseInt(getVar("--itm-radius"), 10) || 12, "px",
      function (v) { ROOT.style.setProperty("--itm-radius", v + "px"); },
      function (v) { saveToken("--itm-radius", v + "px", "Corner rounding " + v + "px"); },
      function () { ROOT.style.removeProperty("--itm-radius"); deleteRuleBy("token", "*", "--itm-radius", ""); }));
    var curScale = parseFloat(getVar("--itm-scale")) || 1;
    s2.appendChild(sliderRow("Overall text size", 85, 125, Math.round(curScale * 100), "%",
      function (v) { ROOT.style.setProperty("--itm-scale", (v / 100).toFixed(2)); },
      function (v) { saveToken("--itm-scale", (v / 100).toFixed(2), "Text size " + v + "%"); },
      function () { ROOT.style.removeProperty("--itm-scale"); deleteRuleBy("token", "*", "--itm-scale", ""); }));
    host.appendChild(s2);

    var s3 = el("div", "uid-sec");
    s3.appendChild(el("h4", null, "Heading font"));
    var frow = el("div", "uid-row");
    frow.appendChild(el("label", null, "Display face"));
    var sel = el("select");
    var cur = getVar("--itm-display").replace(/\s+/g, " ").trim();
    FONTS.forEach(function (f) {
      var o = el("option", null, f[1]); o.value = f[0];
      if (f[0].replace(/\s+/g, " ") === cur) { o.selected = true; }
      sel.appendChild(o);
    });
    sel.addEventListener("change", function () {
      ROOT.style.setProperty("--itm-display", sel.value);
      saveToken("--itm-display", sel.value, "Heading font: " + sel.options[sel.selectedIndex].text);
    });
    frow.appendChild(sel);
    var frst = el("button", "uid-mini", "reset");
    frst.addEventListener("click", function () {
      ROOT.style.removeProperty("--itm-display"); deleteRuleBy("token", "*", "--itm-display", "");
    });
    frow.appendChild(frst);
    s3.appendChild(frow);
    host.appendChild(s3);

    var s4 = el("div", "uid-sec");
    s4.appendChild(el("h4", null, "Make it permanent"));
    s4.appendChild(el("div", "uid-hint",
      "Bake the colours, sizes and font above into the theme stylesheet — they stop being " +
      "overrides and become the site's real defaults. Rebuilds the theme; give it a moment."));
    var bake = el("button", "uid-btn", "");
    bake.innerHTML = svg("check", 14) + "Bake into the theme";
    bake.addEventListener("click", function () {
      uiConfirm("Bake into the theme",
        "Fold every saved theme token into the site stylesheet and rebuild the theme? This can take a moment.",
        { okLabel: "Bake" }).then(function (ok) {
        if (!ok) { return; }
        bake.disabled = true;
        post({ do: "bake" }).then(function (res) {
          if (res && res.ok) {
            if (previewStyle) { previewStyle.textContent = ""; }
            toast((res.baked || 0) + " token(s) baked — reloading");
            setTimeout(function () { location.reload(); }, 800);
          } else {
            bake.disabled = false;
            toast((res && res.error) || "Error", true);
          }
        });
      });
    });
    var ba = el("div", "uid-actions");
    ba.appendChild(bake);
    s4.appendChild(ba);
    host.appendChild(s4);
  }
  function sliderRow(label, min, max, val, unit, onInput, onCommit, onReset) {
    var row = el("div", "uid-row");
    row.appendChild(el("label", null, label));
    var r = el("input"); r.type = "range"; r.min = min; r.max = max; r.value = val;
    var out = el("span", "uid-val", val + unit);
    r.addEventListener("input", function () { out.textContent = r.value + unit; onInput(+r.value); });
    r.addEventListener("change", function () { onCommit(+r.value); });
    row.appendChild(r); row.appendChild(out);
    var rst = el("button", "uid-mini", "reset");
    rst.addEventListener("click", function () { onReset(); renderTheme(); });
    row.appendChild(rst);
    return row;
  }

  // ---- EDIT tab ---------------------------------------------
  function renderEdit() {
    var host = studio.querySelector('.uid-tab[data-tab="edit"]');
    host.textContent = "";

    var sec = el("div", "uid-sec");
    sec.appendChild(el("h4", null, "Edit an element"));
    var sc = el("div", "uid-row");
    sc.innerHTML =
      '<label>Changes apply to</label>' +
      '<select data-uid="scope">' +
        '<option value="one">Just this element (this page)</option>' +
        '<option value="all">All elements like it (this page)</option>' +
        '<option value="allsite">All like it, every page</option>' +
      '</select>';
    sec.appendChild(sc);
    var acts = el("div", "uid-actions");
    var edit = el("button", "uid-btn", "Point & edit…");
    edit.addEventListener("click", function () { startPick("style"); });
    var hide = el("button", "uid-btn ghost", "Point & hide…");
    hide.addEventListener("click", function () { startPick("hide"); });
    acts.appendChild(edit); acts.appendChild(hide);
    sec.appendChild(acts);
    sec.appendChild(el("div", "uid-hint", "Tip: click text to retype it. Use the toolbar for size, colour, spacing."));
    host.appendChild(sec);

    // --- rename a fixed Moodle label (string override, site-wide) ---
    var lsec = el("div", "uid-sec");
    lsec.appendChild(el("h4", null, "Rename a fixed label"));
    lsec.appendChild(el("div", "uid-hint",
      "For built-in words like “Dashboard” or “Grades” that Point & edit can't keep. " +
      "Type the label exactly as shown, find it, then set your wording. Applies everywhere."));
    var lrow = el("div", "uid-row");
    var linp = el("input");
    linp.type = "text";
    linp.placeholder = "Current label text";
    linp.className = "uid-txt";
    var lfind = el("button", "uid-mini", "find");
    lrow.appendChild(linp);
    lrow.appendChild(lfind);
    lsec.appendChild(lrow);
    var lout = el("div");
    lsec.appendChild(lout);
    lfind.addEventListener("click", function () {
      var q = linp.value.trim();
      if (!q) { return; }
      lout.textContent = "…";
      post({ do: "findstring", text: q }).then(function (res) {
        lout.textContent = "";
        if (!res || !res.ok || !res.matches || !res.matches.length) {
          lout.appendChild(el("div", "uid-row", "No exact match found."));
          return;
        }
        var ul = el("ul", "uid-list");
        res.matches.forEach(function (m) {
          var li = el("li");
          var lbl = el("span", "uid-lbl", m.current);
          lbl.title = m.component + " / " + m.stringid;
          li.appendChild(lbl);
          var rn = el("button", "uid-mini", "rename");
          rn.addEventListener("click", function () {
            uiPrompt("Rename label", 'Replace "' + m.current + '" everywhere with:', m.current,
              { okLabel: "Rename" }).then(function (nv) {
              if (nv === null) { return; }
              nv = nv.trim();
              if (!nv || nv === m.current) { return; }
              post({ do: "overridestring", component: m.component, stringid: m.stringid,
                     value: nv, label: "Label " + m.component + "/" + m.stringid }).then(function (r) {
                if (r && r.ok) { toast("Renamed — reloading"); setTimeout(function () { location.reload(); }, 800); }
                else { toast((r && r.error) || "Error", true); }
              });
            });
          });
          li.appendChild(rn);
          ul.appendChild(li);
        });
        lout.appendChild(ul);
      });
    });
    host.appendChild(lsec);

    var elrules = (CFG.rules || []).filter(function (r) { return r.kind === "element"; });
    var hidden = (CFG.rules || []).filter(function (r) { return r.kind === "hide"; });
    var text = (CFG.rules || []).filter(function (r) { return r.kind === "text"; });
    var langrules = (CFG.rules || []).filter(function (r) { return r.kind === "lang"; });

    host.appendChild(listSec("Element tweaks", elrules, function (r) {
      return r.property + ": " + r.value;
    }));
    host.appendChild(listSec("Retyped text", text, function (r) {
      return "“" + r.value.slice(0, 30) + "”";
    }));
    host.appendChild(listSec("Renamed labels", langrules, function (r) {
      return (r.selector.split("/")[1] || r.selector) + " → “" + r.value.slice(0, 24) + "”";
    }));
    host.appendChild(listSec("Hidden elements", hidden, function () { return "hidden"; }, "unhide"));
  }
  function listSec(title, rows, describe, verb) {
    var s = el("div", "uid-sec");
    s.appendChild(el("h4", null, title));
    if (!rows.length) { s.appendChild(el("div", "uid-row", "None yet.")); return s; }
    var ul = el("ul", "uid-list");
    rows.forEach(function (r) {
      var li = el("li");
      var lbl = el("span", "uid-lbl");
      lbl.innerHTML = "<code>" + esc(r.selector) + "</code> " + esc(describe(r));
      lbl.title = r.selector + "  ·  " + (r.pagetype === "*" ? "every page" : r.pagetype);
      li.appendChild(lbl);
      var b = el("button", "uid-mini", verb || "remove");
      b.addEventListener("click", function () { removeRule(r.id); });
      li.appendChild(b);
      ul.appendChild(li);
    });
    s.appendChild(ul);
    return s;
  }

  // ---- RULES tab ------------------------------------------
  function renderRules() {
    var host = studio.querySelector('.uid-tab[data-tab="rules"]');
    host.textContent = "";
    var rules = CFG.rules || [];
    var sec = el("div", "uid-sec");
    sec.appendChild(el("h4", null, "All overrides (" + rules.length + ")"));
    if (!rules.length) {
      sec.appendChild(el("div", "uid-row", "No overrides saved."));
    } else {
      var ul = el("ul", "uid-list");
      rules.forEach(function (r) {
        var li = el("li");
        var cb = el("input"); cb.type = "checkbox"; cb.checked = !!(+r.enabled);
        cb.addEventListener("change", function () { toggleRule(r.id, cb.checked); });
        li.appendChild(cb);
        var lbl = el("span", "uid-lbl", (r.label || (r.kind + " " + r.selector)) +
          (!(+r.published) ? "  •draft" : ""));
        lbl.title = r.kind + " · " + (r.pagetype === "*" ? "every page" : r.pagetype) + "\n" +
          r.selector + (r.property ? " { " + r.property + ": " + r.value + " }" : "");
        // stale check: does this selector match anything on the current page?
        if ((r.kind === "element" || r.kind === "hide" || r.kind === "text") &&
            (r.pagetype === "*" || r.pagetype === CFG.pagetype)) {
          var hits = 0;
          try { hits = document.querySelectorAll(r.selector).length; } catch (e) { hits = -1; }
          if (hits === 0) { lbl.appendChild(mkWarn("matches nothing on this page")); }
        }
        li.appendChild(lbl);
        var x = el("button", "uid-mini", "✕");
        x.addEventListener("click", function () { removeRule(r.id); });
        li.appendChild(x);
        ul.appendChild(li);
      });
      sec.appendChild(ul);
    }
    host.appendChild(sec);

    // version history
    post({ do: "versions" }).then(function (v) {
      if (!v || !v.ok || !v.versions.length) { return; }
      var vs = el("div", "uid-sec");
      vs.appendChild(el("h4", null, "Published history"));
      var vul = el("ul", "uid-list");
      v.versions.forEach(function (ver) {
        var li = el("li");
        var d = new Date(ver.timecreated * 1000);
        li.appendChild(el("span", "uid-lbl", (ver.note || "snapshot") + "  ·  " + ver.rulecount + " rules"));
        li.querySelector(".uid-lbl").title = d.toLocaleString();
        var rb = el("button", "uid-mini", "restore");
        rb.addEventListener("click", function () {
          uiConfirm("Restore snapshot",
            "Replace the current rules with this snapshot and publish it?",
            { okLabel: "Restore" }).then(function (ok) {
            if (!ok) { return; }
            post({ do: "rollback", id: ver.id }).then(function (res) {
              if (res.ok) { if (previewStyle) { previewStyle.textContent = ""; } location.reload(); }
              else { toast(res.error || "Error", true); }
            });
          });
        });
        li.appendChild(rb);
        vul.appendChild(li);
      });
      vs.appendChild(vul);
      host.appendChild(vs);
    });

    var acts = el("div", "uid-actions");
    var reset = el("button", "uid-btn danger", "");
    reset.innerHTML = svg("trash", 15) + "Reset everything";
    reset.addEventListener("click", function () {
      uiConfirm("Reset everything",
        "Delete every override and restore the site's default look? This cannot be undone.",
        { okLabel: "Reset everything", danger: true }).then(function (ok) {
        if (!ok) { return; }
        post({ do: "resetall" }).then(function (res) {
          if (res.ok) { if (previewStyle) { previewStyle.textContent = ""; } location.reload(); }
          else { toast(res.error || "Error", true); }
        });
      });
    });
    acts.appendChild(reset);
    var mg = el("a", "uid-btn ghost", "");
    mg.innerHTML = svg("external", 15) + "Rule manager";
    mg.href = CFG.manageurl; mg.target = "_blank";
    acts.appendChild(mg);
    host.appendChild(acts);
  }

  // ---- pick mode ----------------------------------------
  function ensureOutline() {
    if (!outline) {
      outline = el("div", "uid-outline");
      outline.appendChild(el("span", "uid-tag"));
      document.body.appendChild(outline);
    }
    return outline;
  }
  function tagLabel(node) {
    var s = node.tagName.toLowerCase();
    if (node.id && /^[A-Za-z][\w-]*$/.test(node.id)) { return s + "#" + node.id; }
    var c = (typeof node.className === "string" ? node.className.trim().split(/\s+/)[0] : "");
    return c ? s + "." + c : s;
  }
  function startPick(mode) {
    hideToolbar();
    pickMode = mode;
    document.body.classList.add("uid-picking");
    ensureOutline();
    outline.style.display = "block";
    if (!pickframe) { pickframe = el("div"); pickframe.id = "uid-pickframe"; document.body.appendChild(pickframe); }
    pickframe.hidden = false;
    var hint = el("div"); hint.id = "uid-pickhint";
    hint.innerHTML = svg("search", 15) +
      '<span>' + (mode === "hide" ? "Click any element to hide it" : "Click any element to restyle it") + '</span>' +
      '<kbd>Esc</kbd>' +
      '<button type="button">Cancel</button>';
    studio.appendChild(hint);
    hint.querySelector("button").addEventListener("click", stopPick);
    document.addEventListener("mousemove", onPickMove, true);
    document.addEventListener("click", onPickClick, true);
    document.addEventListener("keydown", onPickKey, true);
  }
  function stopPick() {
    pickMode = null;
    document.body.classList.remove("uid-picking");
    if (outline) { outline.style.display = "none"; }
    if (pickframe) { pickframe.hidden = true; }
    var h = studio && studio.querySelector("#uid-pickhint");
    if (h) { h.remove(); }
    document.removeEventListener("mousemove", onPickMove, true);
    document.removeEventListener("click", onPickClick, true);
    document.removeEventListener("keydown", onPickKey, true);
  }
  function onPickKey(e) { if (e.key === "Escape") { stopPick(); } }
  function onPickMove(e) {
    var t = e.target;
    if (!pickable(t)) { outline.style.display = "none"; return; }
    var r = t.getBoundingClientRect();
    outline.style.display = "block";
    outline.firstChild.textContent = tagLabel(t);
    outline.style.left = (r.left + window.scrollX) + "px";
    outline.style.top = (r.top + window.scrollY) + "px";
    outline.style.width = r.width + "px";
    outline.style.height = r.height + "px";
  }
  function onPickClick(e) {
    var t = e.target;
    if (!pickable(t)) { return; }
    e.preventDefault(); e.stopPropagation();
    var mode = pickMode;
    stopPick();
    if (mode === "hide") {
      var sel = scopedSel(t).sel, pt = scopedSel(t).pt;
      addPreviewRule((pt === "*" ? "" : "body#" + CFG.bodyclass + " ") + sel + "{display:none !important}");
      t.style.display = "none";
      save({ do: "upsert", kind: "hide", pagetype: pt, selector: sel, label: "Hide " + sel },
        "Hidden", { kind: "hide", pagetype: pt, selector: sel });
    } else {
      selectElement(t);
    }
  }

  function scopeVal() {
    var s = studio.querySelector('[data-uid="scope"]');
    return (s && s.value) || "one";
  }
  function scopedSel(node) {
    var mode = scopeVal();                       // one | all | allsite
    var useClass = (mode === "all" || mode === "allsite");
    var sel = useClass ? (classPath(node) || cssPath(node)) : cssPath(node);
    return { sel: sel, pt: mode === "allsite" ? "*" : CFG.pagetype };
  }

  // ---- element toolbar --------------------------------
  function selectElement(node) {
    picked = node;
    buildToolbar();
    positionToolbar();
    toolbar.hidden = false;
    outlineElement(node);
  }
  function buildToolbar() {
    if (toolbar) { return; }
    toolbar = el("div"); toolbar.id = "uid-toolbar"; toolbar.hidden = true;
    toolbar.innerHTML =
      '<button data-a="text" title="Retype text" aria-label="Retype text">' + svg("text") + '</button>' +
      '<span class="uid-div"></span>' +
      '<button data-a="smaller" title="Smaller text" aria-label="Smaller text">' + svg("minus") + '</button>' +
      '<button data-a="bigger" title="Bigger text" aria-label="Bigger text">' + svg("plus") + '</button>' +
      '<button data-a="bold" title="Bold on/off" aria-label="Bold on/off">' + svg("bold") + '</button>' +
      '<span class="uid-div"></span>' +
      '<button data-a="colour" title="Text colour" aria-label="Text colour">' + svg("palette") + '</button>' +
      '<button data-a="bg" title="Background colour" aria-label="Background colour">' + svg("fill") + '</button>' +
      '<button data-a="bgimg" title="Background image" aria-label="Background image">' + svg("image") + '</button>' +
      '<span class="uid-div"></span>' +
      '<button data-a="border" title="Border on/off" aria-label="Border on/off">' + svg("square") + '</button>' +
      '<button data-a="padless" title="Less padding" aria-label="Less padding">' + svg("minus") + '</button>' +
      '<button data-a="padmore" title="More padding" aria-label="More padding">' + svg("plus") + '</button>' +
      '<button data-a="align" title="Text align" aria-label="Text align">' + svg("align") + '</button>' +
      '<span class="uid-div"></span>' +
      '<button data-a="hide" title="Hide this" aria-label="Hide this">' + svg("eyeoff") + '</button>' +
      '<button data-a="reset" title="Reset this element" aria-label="Reset this element">' + svg("reset") + '</button>' +
      '<input type="color" data-a="colourinput" hidden>' +
      '<input type="color" data-a="bginput" hidden>';
    document.body.appendChild(toolbar);
    toolbar.addEventListener("click", function (e) {
      var b = e.target.closest("button"); if (!b || !picked) { return; }
      onToolbar(b.dataset.a);
    });
    toolbar.querySelector('[data-a="colourinput"]').addEventListener("change", function (e) {
      applyProp("color", e.target.value, "Text colour");
    });
    toolbar.querySelector('[data-a="bginput"]').addEventListener("change", function (e) {
      applyProp("background-color", e.target.value, "Background colour");
    });
    window.addEventListener("scroll", positionToolbar, true);
    window.addEventListener("resize", positionToolbar);
  }
  function positionToolbar() {
    if (!toolbar || toolbar.hidden || !picked) { return; }
    var r = picked.getBoundingClientRect();
    var below = false;
    var top = r.top + window.scrollY - 46;
    if (top < window.scrollY + 4) { top = r.bottom + window.scrollY + 8; below = true; }
    toolbar.classList.toggle("is-below", below);
    toolbar.style.top = top + "px";
    toolbar.style.left = Math.max(6, r.left + window.scrollX) + "px";
  }
  function outlineElement(node) {
    ensureOutline();
    var r = node.getBoundingClientRect();
    outline.style.display = "block";
    outline.firstChild.textContent = tagLabel(node);
    outline.style.left = (r.left + window.scrollX) + "px";
    outline.style.top = (r.top + window.scrollY) + "px";
    outline.style.width = r.width + "px";
    outline.style.height = r.height + "px";
    outline.classList.add("uid-sel");
  }
  function hideToolbar() {
    if (toolbar) { toolbar.hidden = true; }
    if (outline) { outline.style.display = "none"; outline.classList.remove("uid-sel"); }
    picked = null;
  }

  function onToolbar(a) {
    var cs = getComputedStyle(picked);
    if (a === "text") {
      editText(picked);
    } else if (a === "smaller" || a === "bigger") {
      var px = parseFloat(cs.fontSize) || 16;
      px = Math.max(9, Math.round(px + (a === "bigger" ? 1 : -1)));
      applyProp("font-size", px + "px", "Font size " + px + "px");
    } else if (a === "bold") {
      var w = (parseInt(cs.fontWeight, 10) || 400) >= 600 ? "400" : "700";
      applyProp("font-weight", w, w === "700" ? "Bold" : "Normal weight");
    } else if (a === "colour") {
      var ci = toolbar.querySelector('[data-a="colourinput"]');
      ci.value = toHex(cs.color); ci.click();
    } else if (a === "bg") {
      var bi = toolbar.querySelector('[data-a="bginput"]');
      bi.value = toHex(cs.backgroundColor); bi.click();
    } else if (a === "bgimg") {
      var t2 = scopedSel(picked);
      uiPrompt("Background image",
        "Paste an image URL — https://… , /path , or data:image/… . Leave blank to remove.",
        "", { placeholder: "https://…", okLabel: "Apply" }).then(function (url) {
        if (url === null) { return; }
        url = url.trim().replace(/['"\\]/g, "");
        if (url === "") {
          var ex = findRule({ kind: "element", pagetype: t2.pt, selector: t2.sel, property: "background-image" });
          if (ex) { removeRule(ex.id); } else { toast("No background image set"); }
          return;
        }
        applyProp("background-image", "url('" + url + "')", "Background image");
      });
    } else if (a === "border") {
      var has = findRule({ kind: "element", pagetype: scopedSel(picked).pt,
        selector: scopedSel(picked).sel, property: "border" });
      if (has) { removeRule(has.id); toast("Border off"); }
      else { applyProp("border", "1px solid var(--itm-line)", "Border on"); }
    } else if (a === "padless" || a === "padmore") {
      var p = parseFloat(cs.paddingTop) || 0;
      p = Math.max(0, Math.round(p + (a === "padmore" ? 4 : -4)));
      applyProp("padding", p + "px", "Padding " + p + "px");
    } else if (a === "align") {
      var order = ["left", "center", "right"];
      var next = order[(order.indexOf(cs.textAlign) + 1) % 3] || "center";
      applyProp("text-align", next, "Align " + next);
    } else if (a === "hide") {
      var t = scopedSel(picked);
      addPreviewRule((t.pt === "*" ? "" : "body#" + CFG.bodyclass + " ") + t.sel + "{display:none !important}");
      picked.style.display = "none";
      save({ do: "upsert", kind: "hide", pagetype: t.pt, selector: t.sel, label: "Hide " + t.sel },
        "Hidden", { kind: "hide", pagetype: t.pt, selector: t.sel });
      hideToolbar();
    } else if (a === "reset") {
      resetElement(picked);
    }
  }

  function applyProp(property, value, label) {
    var t = scopedSel(picked);
    var full = (t.pt === "*" ? "" : "body#" + CFG.bodyclass + " ") + t.sel;
    addPreviewRule(full + "{" + property + ":" + value + " !important}");
    save({ do: "upsert", kind: "element", pagetype: t.pt, selector: t.sel,
           property: property, value: value, label: label },
      label, { kind: "element", pagetype: t.pt, selector: t.sel, property: property });
    positionToolbar();
  }

  function editText(node) {
    var original = node.textContent;
    node.setAttribute("contenteditable", "true");
    node.classList.add("uid-editing");
    node.focus();
    document.execCommand && document.execCommand("selectAll", false, null);
    function finish() {
      node.removeAttribute("contenteditable");
      node.classList.remove("uid-editing");
      node.removeEventListener("blur", finish);
      node.removeEventListener("keydown", key);
      var val = node.textContent.trim();
      if (val && val !== original.trim()) {
        var t = scopedSel(node);
        save({ do: "upsert", kind: "text", pagetype: t.pt, selector: t.sel, value: val,
               label: "Text: " + val.slice(0, 30) }, "Text changed",
          { kind: "text", pagetype: t.pt, selector: t.sel });
      }
    }
    function key(e) {
      if (e.key === "Enter" && !e.shiftKey) { e.preventDefault(); node.blur(); }
      if (e.key === "Escape") { node.textContent = original; node.blur(); }
    }
    node.addEventListener("blur", finish);
    node.addEventListener("keydown", key);
  }

  function resetElement(node) {
    var sels = [cssPath(node), classPath(node)];
    var toKill = (CFG.rules || []).filter(function (r) {
      return (r.kind === "element" || r.kind === "hide" || r.kind === "text") &&
             sels.indexOf(r.selector) !== -1;
    });
    if (!toKill.length) { toast("Nothing to reset"); return; }
    Promise.all(toKill.map(function (r) { return post({ do: "delete", id: r.id }); }))
      .then(function () { toast("Reset"); location.reload(); });
  }

  // ---- write + undo/redo -----------------------------
  function save(params, okMsg, key) {
    // key = { kind, pagetype, selector, property? } identifying the rule for undo
    var prev = findRule(key);
    var prevSnapshot = prev ? { value: prev.value, enabled: prev.enabled, label: prev.label } : null;
    return post(params).then(function (res) {
      if (!res || !res.ok) { toast((res && res.error) || "Error", true); return res; }
      toast(okMsg || "Saved");
      pushUndo({
        label: okMsg || "change",
        undo: function () {
          if (prevSnapshot) {
            return post({ do: "upsert", kind: key.kind, pagetype: key.pagetype, selector: key.selector,
              property: key.property || "", value: prevSnapshot.value, label: prevSnapshot.label,
              enabled: prevSnapshot.enabled });
          }
          var cur = findRule(key);
          return cur ? post({ do: "delete", id: cur.id }) : Promise.resolve();
        },
        redo: function () { return post(params); }
      });
      return refresh();
    });
  }
  function findRule(key) {
    if (!key) { return null; }
    return (CFG.rules || []).filter(function (r) {
      return r.kind === key.kind && r.pagetype === key.pagetype && r.selector === key.selector &&
             (r.property || "") === (key.property || "");
    })[0] || null;
  }
  function pushUndo(op) {
    undoStack.push(op); redoStack.length = 0;
    if (undoStack.length > 40) { undoStack.shift(); }
    syncUndoButtons();
  }
  function doUndo() {
    var op = undoStack.pop(); if (!op) { return; }
    Promise.resolve(op.undo()).then(function () {
      redoStack.push(op); syncUndoButtons(); toast("Undone"); refresh().then(reapplyPreview);
    });
  }
  function doRedo() {
    var op = redoStack.pop(); if (!op) { return; }
    Promise.resolve(op.redo()).then(function () {
      undoStack.push(op); syncUndoButtons(); toast("Redone"); refresh().then(reapplyPreview);
    });
  }
  function syncUndoButtons() {
    var u = studio.querySelector('[data-uid="undo"]'), r = studio.querySelector('[data-uid="redo"]');
    if (u) { u.disabled = !undoStack.length; }
    if (r) { r.disabled = !redoStack.length; }
  }

  // ---- draft / publish ----------------------------------
  function pendingCount() {
    return (CFG.rules || []).filter(function (r) { return !(+r.published); }).length;
  }
  function syncPublishBar() {
    var bar = studio.querySelector("#uid-publish");
    if (!bar) { return; }
    var n = pendingCount();
    bar.hidden = n === 0;
    bar.querySelector("span").textContent = n === 1 ? "1 unpublished change — only you can see it"
      : n + " unpublished changes — only you can see them";
    var hdr = studio.querySelector("#uid-panel header");
    if (hdr) { hdr.classList.toggle("is-draft", n > 0); }
  }
  function doPublish() {
    post({ do: "publish" }).then(function (res) {
      if (res.ok) { toast("Published — live for everyone"); refresh(); }
      else { toast(res.error || "Error", true); }
    });
  }
  function doDiscard() {
    uiConfirm("Discard changes",
      "Discard your unpublished changes and go back to what's live now?",
      { okLabel: "Discard", danger: true }).then(function (ok) {
      if (!ok) { return; }
      post({ do: "discard" }).then(function (res) {
        if (res.ok) { if (previewStyle) { previewStyle.textContent = ""; } location.reload(); }
        else { toast(res.error || "Error", true); }
      });
    });
  }
  function reapplyPreview() {
    if (!previewStyle) { return; }
    previewStyle.textContent = "";
    (CFG.rules || []).forEach(function (r) {
      if (!(+r.enabled)) { return; }
      var scoped = r.pagetype === "*" ? r.selector : "body#" + CFG.bodyclass + " " + r.selector;
      if (r.kind === "element" && r.property) { addPreviewRule(scoped + "{" + r.property + ":" + r.value + " !important}"); }
      else if (r.kind === "hide") { addPreviewRule(scoped + "{display:none !important}"); }
    });
  }

  function saveToken(name, value, label, prevValue) {
    return save({ do: "upsert", kind: "token", pagetype: "*", selector: name,
      value: String(value), label: label || (name + " = " + value) },
      "Saved", { kind: "token", pagetype: "*", selector: name });
  }
  function toggleRule(id, on) { post({ do: "toggle", id: id, enabled: on ? 1 : 0 }).then(function (r) { after(r, on ? "On" : "Off"); }); }
  function removeRule(id) { post({ do: "delete", id: id }).then(function (r) { if (r.ok) { location.reload(); } else { after(r); } }); }
  function deleteRuleBy(kind, pagetype, selector, property) {
    var m = findRule({ kind: kind, pagetype: pagetype, selector: selector, property: property });
    if (m) { removeRule(m.id); }
  }
  function after(res, msg) {
    if (!res || !res.ok) { toast((res && res.error) || "Error", true); return; }
    toast(msg || "Done"); refresh();
  }
  function refresh() {
    return post({ do: "list" }).then(function (r) {
      if (r && r.ok) {
        CFG.rules = r.rules.map(function (x) {
          return { id: +x.id, kind: x.kind, pagetype: x.pagetype, selector: x.selector,
                   property: x.property, value: x.value, label: x.label,
                   enabled: +x.enabled, published: +x.published };
        });
        var active = studio.querySelector("#uid-tabs button.is-active");
        if (active && active.dataset.tab === "edit") { renderEdit(); }
        if (active && active.dataset.tab === "rules") { renderRules(); }
        syncPublishBar();
        reapplyPreview();
      }
    });
  }

  // ---- boot ---------------------------------------
  function boot() {
    var auto = /[?&]uidstudio=1(\b|&|$)/.test(location.search) || location.hash === "#uidstudio";
    if (auto) {
      openStudio();
      try {
        var u = new URL(location.href);
        u.searchParams.delete("uidstudio");
        history.replaceState(null, "", u.pathname + u.search + (u.hash === "#uidstudio" ? "" : u.hash));
      } catch (e) { /* ignore */ }
      return;
    }
    // Edit mode on -> put a "Design" launcher on the page (bottom-left).
    if (CFG.editing) { showLauncher(); }
  }
  if (document.readyState === "loading") { document.addEventListener("DOMContentLoaded", boot); }
  else { boot(); }
})();
