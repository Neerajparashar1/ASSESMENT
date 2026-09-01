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
      '<div id="uid-panel">' +
        '<header>' +
          '<b>Design Studio</b>' +
          '<button type="button" class="uid-h" data-uid="undo" title="Undo" disabled>&#8630;</button>' +
          '<button type="button" class="uid-h" data-uid="redo" title="Redo" disabled>&#8631;</button>' +
          '<button type="button" class="uid-h" data-uid="close" title="Close">&times;</button>' +
        '</header>' +
        '<div id="uid-tabs">' +
          '<button type="button" data-tab="theme" class="is-active">Theme</button>' +
          '<button type="button" data-tab="edit">Edit</button>' +
          '<button type="button" data-tab="rules">Rules</button>' +
        '</div>' +
        '<div id="uid-body">' +
          '<div class="uid-tab" data-tab="theme"></div>' +
          '<div class="uid-tab" data-tab="edit" hidden></div>' +
          '<div class="uid-tab" data-tab="rules" hidden></div>' +
        '</div>' +
      '</div>' +
      '<div id="uid-toast"></div>';
    document.body.appendChild(studio);

    studio.addEventListener("click", function (e) {
      var t = e.target.closest("[data-uid],[data-tab]");
      if (!t) { return; }
      if (t.dataset.uid === "close") { closeStudio(); return; }
      if (t.dataset.uid === "undo") { doUndo(); return; }
      if (t.dataset.uid === "redo") { doRedo(); return; }
      if (t.dataset.tab && t.parentNode.id === "uid-tabs") { switchTab(t.dataset.tab); }
    });
    renderTheme();
  }

  function switchTab(name) {
    Array.prototype.forEach.call(studio.querySelectorAll("#uid-tabs button"), function (b) {
      b.classList.toggle("is-active", b.dataset.tab === name);
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
  function reopenHandle() {
    if (!handle) {
      handle = el("button", "uid-reopen", "Design");
      handle.type = "button";
      handle.title = "Reopen the Design Studio";
      handle.addEventListener("click", openStudio);
      document.body.appendChild(handle);
    }
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
        var prev = null;
        inp.addEventListener("focus", function () { prev = getVar(it.n) || toHex(getVar(it.n)); });
        inp.addEventListener("input", function () { ROOT.style.setProperty(it.n, inp.value); });
        inp.addEventListener("change", function () {
          ROOT.style.setProperty(it.n, inp.value);
          saveToken(it.n, inp.value, it.label + " → " + inp.value, prev);
        });
        row.appendChild(inp);
        var rst = el("button", "uid-mini", "reset");
        rst.addEventListener("click", function () {
          ROOT.style.removeProperty(it.n);
          inp.value = toHex(getVar(it.n));
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
        '<option value="page">This page only</option>' +
        '<option value="*">Every page</option>' +
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

    var elrules = (CFG.rules || []).filter(function (r) { return r.kind === "element"; });
    var hidden = (CFG.rules || []).filter(function (r) { return r.kind === "hide"; });
    var text = (CFG.rules || []).filter(function (r) { return r.kind === "text"; });

    host.appendChild(listSec("Element tweaks", elrules, function (r) {
      return r.property + ": " + r.value;
    }));
    host.appendChild(listSec("Retyped text", text, function (r) {
      return "“" + r.value.slice(0, 30) + "”";
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
        var lbl = el("span", "uid-lbl", r.label || (r.kind + " " + r.selector));
        lbl.title = r.kind + " · " + (r.pagetype === "*" ? "every page" : r.pagetype) + "\n" +
          r.selector + (r.property ? " { " + r.property + ": " + r.value + " }" : "");
        li.appendChild(lbl);
        var x = el("button", "uid-mini", "✕");
        x.addEventListener("click", function () { removeRule(r.id); });
        li.appendChild(x);
        ul.appendChild(li);
      });
      sec.appendChild(ul);
    }
    host.appendChild(sec);

    var acts = el("div", "uid-actions");
    var reset = el("button", "uid-btn danger", "Reset everything");
    reset.addEventListener("click", function () {
      if (!window.confirm("Delete every override and restore the default look?")) { return; }
      post({ do: "resetall" }).then(function (res) {
        if (res.ok) { if (previewStyle) { previewStyle.textContent = ""; } location.reload(); }
        else { toast(res.error || "Error", true); }
      });
    });
    acts.appendChild(reset);
    var mg = el("a", "uid-btn ghost", "Rule manager");
    mg.href = CFG.manageurl; mg.target = "_blank";
    acts.appendChild(mg);
    host.appendChild(acts);
  }

  // ---- pick mode ----------------------------------------
  function startPick(mode) {
    hideToolbar();
    pickMode = mode;
    document.body.classList.add("uid-picking");
    if (!outline) { outline = el("div", "uid-outline"); document.body.appendChild(outline); }
    outline.style.display = "block";
    var hint = el("div"); hint.id = "uid-pickhint";
    hint.innerHTML = (mode === "hide" ? "Click an element to hide it" : "Click an element to edit it") +
      ' <button type="button">cancel</button>';
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
    return (s && s.value === "*") ? "*" : "page";
  }
  function scopedSel(node, useClassPath) {
    var mode = scopeVal();
    var sel = (mode === "*" || useClassPath) ? classPath(node) : cssPath(node);
    if (!sel) { sel = cssPath(node); }
    return { sel: sel, pt: mode === "*" ? "*" : CFG.pagetype };
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
      '<button data-a="text"  title="Retype text">&#9998;</button>' +
      '<button data-a="smaller" title="Smaller">A&minus;</button>' +
      '<button data-a="bigger"  title="Bigger">A+</button>' +
      '<button data-a="bold"  title="Bold">B</button>' +
      '<button data-a="colour" title="Text colour">&#127912;</button>' +
      '<button data-a="padless" title="Less padding">&#8722;&#9647;</button>' +
      '<button data-a="padmore" title="More padding">+&#9647;</button>' +
      '<button data-a="align" title="Align">&#8801;</button>' +
      '<button data-a="hide"  title="Hide">&#128065;</button>' +
      '<button data-a="reset" title="Reset this element">&#8634;</button>' +
      '<input type="color" data-a="colourinput" hidden>';
    document.body.appendChild(toolbar);
    toolbar.addEventListener("click", function (e) {
      var b = e.target.closest("button"); if (!b || !picked) { return; }
      onToolbar(b.dataset.a);
    });
    toolbar.querySelector('[data-a="colourinput"]').addEventListener("change", function (e) {
      applyProp("color", e.target.value, "Text colour");
    });
    window.addEventListener("scroll", positionToolbar, true);
    window.addEventListener("resize", positionToolbar);
  }
  function positionToolbar() {
    if (!toolbar || toolbar.hidden || !picked) { return; }
    var r = picked.getBoundingClientRect();
    var top = r.top + window.scrollY - 44;
    if (top < window.scrollY + 4) { top = r.bottom + window.scrollY + 6; }
    toolbar.style.top = top + "px";
    toolbar.style.left = Math.max(6, r.left + window.scrollX) + "px";
  }
  function outlineElement(node) {
    if (!outline) { outline = el("div", "uid-outline"); document.body.appendChild(outline); }
    var r = node.getBoundingClientRect();
    outline.style.display = "block";
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
                   property: x.property, value: x.value, label: x.label, enabled: +x.enabled };
        });
        var active = studio.querySelector("#uid-tabs button.is-active");
        if (active && active.dataset.tab === "edit") { renderEdit(); }
        if (active && active.dataset.tab === "rules") { renderRules(); }
        reapplyPreview();
      }
    });
  }

  // ---- boot ---------------------------------------
  function boot() {
    var auto = /[?&]uidstudio=1(\b|&|$)/.test(location.search) || location.hash === "#uidstudio";
    if (!auto) { return; }
    openStudio();
    try {
      var u = new URL(location.href);
      u.searchParams.delete("uidstudio");
      history.replaceState(null, "", u.pathname + u.search + (u.hash === "#uidstudio" ? "" : u.hash));
    } catch (e) { /* ignore */ }
  }
  if (document.readyState === "loading") { document.addEventListener("DOMContentLoaded", boot); }
  else { boot(); }
})();
