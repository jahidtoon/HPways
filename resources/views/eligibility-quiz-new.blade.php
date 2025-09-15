@extends('layouts.app')

@section('content')
<div class="quiz-wrapper">
  <div class="wrap" role="application" aria-label="Immigration Eligibility Quiz">
    <aside class="sidebar" aria-live="polite">
      <h3>Path summary</h3>
      <div id="crumbs" class="crumbs" aria-label="Chosen answers"></div>
    </aside>

    <main class="card">
      <div class="head">
        <div class="brand" aria-label="Brand and step">
          <span class="badge">Horizon Pathways</span>
          <b id="stepTitle">Eligibility Quiz</b>
        </div>
        <div style="min-width:220px; flex:1">
          <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--muted)">
            <span id="stepCount">Step 1</span>
            <span id="pct">0%</span>
          </div>
          <div class="progress" aria-hidden="true"><div id="bar" class="bar"></div></div>
        </div>
      </div>

      <section id="qa" aria-live="polite"></section>

      <div class="actions">
        <button class="btn ghost" id="backBtn" aria-label="Go to previous step">← Back</button>
        <button class="btn primary" id="nextBtn" aria-label="Go to next step">Next →</button>
        <button class="btn" id="restartBtn" aria-label="Restart quiz">Restart</button>
      </div>
      <div id="infoArea"></div>
    </main>
  </div>
</div>

<style>
  :root{
    --bg:#0f1115;            /* dark neutral background */
    --card:#151925;          /* card surface */
    --muted:#8b93a7;         /* secondary text */
    --text:#ffffff;          /* primary text - changed to white */
    --accent:#6ea5ff;        /* single accent color */
    --danger:#ff6b6b;
    --success:#52d273;
            // Persist to server session so pricing works even if user bookmarks or strips params
            fetch('/quiz/tag-terminal', {method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json'}, body:JSON.stringify({terminal: terminalCode})}).catch(()=>{});
    --ring: rgba(110,165,255,.35);
    --shadow: 0 10px 30px rgba(0,0,0,.35);
    --radius: 16px;
  }
  
  .quiz-wrapper {
    background:
      linear-gradient(180deg,rgba(53, 59, 70, 0.92) 0%, rgba(35, 51, 83, 0.98) 100%),
      url('/images/flag-background.jpg') center center/cover no-repeat;
    color:var(--text);
    min-height: 100vh;
    padding: 24px 0;
  }
  
  .wrap{
    max-width:1100px;
    margin:24px auto;
    padding:0 16px;
    display:grid;
    grid-template-columns: 1fr;
    gap:16px;
  }
  @media (min-width: 980px){
    .wrap{ grid-template-columns: 320px 1fr; }
  }

    .sidebar {
      position: sticky; top: 16px;
      background: rgba(39, 39, 45, 1); /* lighter, semi-opaque white */
      border: 1px solid #e5e7eb;
      border-radius: var(--radius);
      padding: 16px;
      color: #222b45;
  }
  .sidebar h3{
    margin:0 0 8px 0; font-size:14px; color:var(--muted); letter-spacing:.2px; text-transform:uppercase;
  }
  .crumbs{
    display:flex; flex-wrap:wrap; gap:8px;
  }
  .crumb{
    background:rgba(110,165,255,.12);
    color:#ffffff;
    border:1px solid rgba(110,165,255,.25);
    padding:6px 10px; border-radius:999px; font-size:13px;
  }

  .card{
    background:linear-gradient(180deg,#121622 0%, #1d263dff 100%);
    border:1px solid rgba(113, 77, 77, 0.07);
    border-radius:var(--radius);
    box-shadow:var(--shadow);
    padding:22px;
  }
  .head{
    display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px;
  }
  .brand{
    display:flex; align-items:center; gap:10px;
  }
  .brand b{font-weight:700; font-size:12px; color:#ffffff}
  .badge{
    font-size:12px; color:#ffffff; background:rgba(110,165,255,.12);
    border:1px solid rgba(110,165,255,.25);
    padding:4px 8px; border-radius:999px;
  }

  .progress{
    height:10px; width:100%; background:rgba(255,255,255,.06);
    border-radius:999px; overflow:hidden; margin:10px 0 0;
  }
  .bar{height:100%; width:0; background:linear-gradient(90deg, var(--accent), #9ad0ff); transition:width .3s ease}

  .q-title{font-size:22px; margin:10px 0 6px 0; color:#ffffff;}
  .q-sub{color:#ffffff; margin:0 0 18px 0}

  .options{display:grid; gap:10px}
  .opt{
    display:flex; gap:10px; align-items:flex-start;
    background:rgba(255,255,255,.03);
    border:1px solid rgba(255,255,255,.08);
    border-radius:12px; padding:12px;
    cursor:pointer;
    color:#ffffff;
  }
  .opt:hover{ border-color: rgba(110,165,255,.35); box-shadow:0 0 0 4px var(--ring) }
  .opt input{ accent-color: var(--accent); margin-top:3px; }
  .opt .label{font-weight:300; color:#ffffff}
  .hint{font-size:12px; color:var(--muted)}

  .actions{
    display:flex; flex-wrap:wrap; gap:10px; margin-top:18px;
  }
  .btn{
    appearance:none; border:1px solid rgba(255,255,255,.12);
    background:#121522; color:var(--text); border-radius:10px;
    padding:10px 14px; font-weight:600; cursor:pointer;
  }
  .btn.primary{ background:var(--accent); border-color:transparent; color:#0b1020 }
  .btn.ghost{ background:transparent }
  .btn:focus{ outline:none; box-shadow:0 0 0 4px var(--ring) }

  .note{
    margin-top:12px; font-size:13px; color:#ffffff;
    background:rgba(110,165,255,.08); border:1px dashed rgba(110,165,255,.35);
    padding:8px 10px; border-radius:10px;
  }
  .result{
    border-left:4px solid var(--success);
    background:linear-gradient(180deg,#0f1714 0,#0d1911 100%);
    padding:12px; border-radius:12px; margin:12px 0;
    color: #fff;
  }
  .result h3{margin:0 0 4px 0; color:#b6f5cc}
  .error{
    border-left:4px solid var(--danger);
    background:linear-gradient(180deg,#1b1111 0,#1a1212 100%);
    padding:12px; border-radius:12px; margin:12px 0; color:#ffd7d7
  }
  .split{display:flex; gap:10px; flex-wrap:wrap}
  a.cta{display:inline-block; text-decoration:none}
</style>

<script>
/** ================== DATA (Exact business logic) ================== */
const STRINGS = {
  success: "Congratulations: You might be eligible for this category. Please review our available packages and pricing, and proceed with signing up to get started.",
  pricingCta: "View Packages",
  loginCta: "Create Account / Login",
  required: "Please select an answer to continue.",
  redirected: "Redirected due to your previous answer.",
  saved: "Your progress is saved locally. You can leave and return later.",
  brand: "Horizon Pathways"
};

const quiz_spec = @json($quiz_spec);

/** ================== RUNTIME ================== */
document.body.classList.add('quiz-bg');

const nodes = new Map(quiz_spec.nodes.map(n => [n.id, n]));
const LS_KEY = "eligibilityQuiz.v1";

const state = {
  current: quiz_spec.meta.root,
  history: [],            // array of nodeIds visited
  answers: {},            // nodeId -> value
  infoNote: null
};

function save(){
  localStorage.setItem(LS_KEY, JSON.stringify(state));
}
function load(){
  const raw = localStorage.getItem(LS_KEY);
  if(!raw) return;
  try{
    const s = JSON.parse(raw);
    if(s && s.current && s.history && s.answers){
      Object.assign(state, s);
    }
  }catch(e){}
}

function reset(){
  state.current = quiz_spec.meta.root;
  state.history = [];
  state.answers = {};
  state.infoNote = null;
  save();
  render();
}
function goto(id){
  state.current = id;
  save();
  render();
}
function getState(){ return JSON.parse(JSON.stringify(state)); }

function render(){
  const node = nodes.get(state.current);
  const qa = document.getElementById("qa");
  const stepTitle = document.getElementById("stepTitle");
  const stepCount = document.getElementById("stepCount");
  const pct = document.getElementById("pct");
  const bar = document.getElementById("bar");
  const crumbs = document.getElementById("crumbs");
  const infoArea = document.getElementById("infoArea");
  const actions = document.querySelector('.actions');

  // breadcrumbs
  crumbs.innerHTML = "";
  state.history.forEach(id => {
    const n = nodes.get(id);
    const chosen = state.answers[id];
    const opt = n?.options?.find(o => o.value === chosen);
    const chip = document.createElement("span");
    chip.className = "crumb";
    chip.textContent = `${n?.title ?? id}: ${opt?.label ?? ""}`;
    crumbs.appendChild(chip);
  });

  // header
  stepTitle.textContent = nodes.get(state.current)?.title ?? "Eligibility Quiz";
  const stepsDone = state.history.length;
  stepCount.textContent = `Step ${Math.max(1, stepsDone+1)}`;
  const percent = Math.min(100, Math.round((stepsDone/(stepsDone+2))*100)); // soft estimate
  pct.textContent = `${percent}%`;
  bar.style.width = `${percent}%`;

  // info
  infoArea.innerHTML = "";
  if(state.infoNote){
    const div = document.createElement("div");
    div.className = "note";
    div.textContent = state.infoNote;
    infoArea.appendChild(div);
  }
  // main body
  qa.innerHTML = "";

  // Terminal: success
  if(node && node._terminal === "eligible"){
    qa.innerHTML = `
      <div class="result" role="status" aria-live="polite">
        <h3>✅ Success</h3>
        <p>${STRINGS.success}</p>
      </div>
      <div class="split" style="margin-top:12px">
        <a class="btn primary cta" id="pricingCta" href="${quiz_spec.meta.routes.pricingUrl}">${STRINGS.pricingCta}</a>
        <a class="btn cta" href="${quiz_spec.meta.routes.loginUrl}">${STRINGS.loginCta}</a>
        <button class="btn" onclick="reset()">Restart</button>
      </div>
    `;
    if(actions) actions.style.display = 'none';
    toggleNav(false);
    // Enhanced mapping that considers sub-flows for Option B
    const rootAns = state.answers['Q1'];
    let pair = null;
    
    if (rootAns === 'A') pair = ['I90','FORM_I90'];
    else if (rootAns === 'B') {
      // Check if they chose fiancé or relative
      const bringWho = state.answers['N3_Q1'];
      if (bringWho === 'fiance') {
        pair = ['K1','K1_ELIGIBLE'];
      } else {
        pair = ['I130','RELATIVE_I130'];
      }
    }
    else if (rootAns === 'C') pair = ['I485','I485_ONLY'];
    else if (rootAns === 'D') pair = ['I751','I751'];
    else if (rootAns === 'E') pair = ['DACA','DACA_FORMS'];
    else if (rootAns === 'F') pair = ['N400','N400'];
    const pricingLink = document.getElementById('pricingCta');
    if (pricingLink && pair){
      const [visaType, terminalCode] = pair;
      try {
        const url = new URL(pricingLink.getAttribute('href'), window.location.origin);
        url.searchParams.set('vt', visaType);
        url.searchParams.set('t', terminalCode);
        pricingLink.setAttribute('href', url.pathname + url.search);
        fetch('/quiz/tag-terminal', {method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json'}, body:JSON.stringify({terminal: terminalCode})}).catch(()=>{});
      } catch(e) {}
    }
    return;
  }
  // Terminal: ineligible
  if(node && node._terminal === "ineligible"){
    qa.innerHTML = `
      <div class="error" role="alert" aria-live="polite">
        <strong>Not Eligible:</strong> ${node._message}
      </div>
      <div class="split" style="margin-top:12px">
        <button class="btn" onclick="reset()">Restart</button>
        <button class="btn ghost" onclick="historyBack()">Go Back</button>
      </div>
    `;
    if(actions) actions.style.display = 'none';
    toggleNav(false);
    return;
  }

  // Question node
  if(!node){ qa.textContent = "Invalid node."; return; }
  qa.insertAdjacentHTML("beforeend", `
    <h2 class="q-title">${node.title}</h2>
    <p class="q-sub">${node.question}</p>
  `);

  const opts = document.createElement("div");
  opts.className = "options";
  node.options?.forEach(o => {
    const id = `${node.id}_${o.value}`;
    const checked = state.answers[node.id] === o.value ? "checked" : "";
    const note = o.note ? `<div class="hint">${o.note}</div>` : "";
    opts.insertAdjacentHTML("beforeend", `
      <label class="opt" for="${id}">
        <input type="radio" id="${id}" name="opt" value="${o.value}" ${checked} />
        <div>
          <div class="label">${o.label}</div>
          ${note}
        </div>
      </label>
    `);
  });
  qa.appendChild(opts);

  // Require selection message placeholder
  qa.insertAdjacentHTML("beforeend", `<div id="req" class="hint" style="display:none; color:#ffd7d7">${STRINGS.required}</div>`);

  if(actions) actions.style.display = '';
  toggleNav(true);
}

function historyBack(){
  if(!state.history.length) return;
  // Go back exactly one step: show the last visited question
  const prev = state.history.pop();
  if(prev){ state.current = prev; } else { state.current = quiz_spec.meta.root; }
  // remove the answer for that question so user can change it
  delete state.answers[prev];
  save(); render();
}

function next(){
  const node = nodes.get(state.current);
  const chosen = document.querySelector('input[name="opt"]:checked');
  const req = document.getElementById("req");
  if(!chosen){
    req.style.display = "block";
    return;
  }
  req.style.display = "none";
  const val = chosen.value;
  state.answers[node.id] = val;
  state.history.push(node.id);

  const option = node.options.find(o => o.value === val);
  state.infoNote = null;

  // redirects
  if(option.redirect){
    state.current = option.redirect;
    state.infoNote = STRINGS.redirected;
    save(); render(); return;
  }
  // eligible terminal
  if(option.eligible){
    const tid = `_OK_${node.id}`;
    nodes.set(tid, {_terminal:"eligible"});
    state.current = tid; save(); render(); return;
  }
  // ineligible terminal
  if(option.ineligible){
    const tid = `_NO_${node.id}`;
    nodes.set(tid, {_terminal:"ineligible", _message: option.ineligible});
    state.current = tid; save(); render(); return;
  }
  // regular next
  if(option.next){
    state.current = option.next; save(); render(); return;
  }

  // fallback
  const tid = `_NO_${node.id}`;
  nodes.set(tid, {_terminal:"ineligible", _message:"Path incomplete."});
  state.current = tid; save(); render();
}

function toggleNav(show){
  document.getElementById("backBtn").style.display = show ? "inline-flex" : "none";
  document.getElementById("nextBtn").style.display = show ? "inline-flex" : "none";
  document.getElementById("restartBtn").style.display = "inline-flex";
}

// Setup Event Listeners
document.addEventListener('DOMContentLoaded', function() {
  // Buttons
  document.getElementById("nextBtn").addEventListener('click', next);
  document.getElementById("backBtn").addEventListener('click', historyBack);
  document.getElementById("restartBtn").addEventListener('click', reset);

  // Detect browser page refresh and restart quiz
  let isReload = false;
  try {
    const nav = (performance && performance.getEntriesByType) ? performance.getEntriesByType('navigation')[0] : null;
    isReload = nav ? (nav.type === 'reload') : (performance && performance.navigation && performance.navigation.type === 1);
  } catch(e) { /* ignore */ }

  if (isReload) {
    // Clear local progress and ask server to reset session state
    try { localStorage.removeItem(LS_KEY); localStorage.removeItem(LS_KEY + ".hint"); } catch(e) {}
    try { fetch('/quiz/reset', { method:'POST', headers: { 'Accept':'application/json' } }).catch(()=>{}); } catch(e) {}
    // Reset runtime state and render fresh
    state.current = quiz_spec.meta.root;
    state.history = [];
    state.answers = {};
    state.infoNote = null;
    save();
  } else {
    // Load saved progress if not a hard reload
    load();
  }
  render();
  
  // One-time info
  setTimeout(() => {
    const once = localStorage.getItem(LS_KEY + ".hint");
    if(!once){
      const info = document.getElementById("infoArea");
      const div = document.createElement("div");
      div.className = "note";
      div.textContent = STRINGS.saved;
      info.appendChild(div);
      localStorage.setItem(LS_KEY + ".hint", "1");
    }
  }, 50);
});

// Keyboard: Enter to Next
document.addEventListener('click', function(e) {
  if (e.target.matches('input[name="opt"]')) {
    e.target.addEventListener('keydown', (e) => {
      if(e.key === "Enter"){ 
        e.preventDefault();
        next();
      }
    });
  }
});
</script>
@endsection
