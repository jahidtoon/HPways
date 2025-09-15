@extends('layouts.app')

@section('content')
<!-- Quiz Content -->
<div class="quiz-wrapper" style="margin: 3rem 0; padding: 2rem 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 1rem;">
            <a href="{{ route('eligibility.quiz') }}" class="btn btn-primary">Try our new Eligibility Quiz!</a>
        </div>
        <div class="quiz-container">
            <div class="quiz-content">
                <div class="quiz-left">
                    <div>
                        <h1 id="quiz-heading" class="quiz-title">Find Your Immigration Path</h1>
                        <p class="quiz-description">To ensure you find the right application, let's confirm your eligibility. Choose the type of application you're interested in.</p>
                        <button id="quiz-back-btn" type="button" class="btn btn-light" style="position:absolute;left:0.1rem;bottom:1.5rem;z-index:2;">
                            <i class="bi bi-arrow-left" style="margin-right:6px;"></i> Back
                        </button>
                    </div>
                </div>
                <div class="quiz-right">
                    <div class="quiz-right-inner">
                        <div style="padding-top: 1.2rem;"></div>
                        <div class="quiz-instruction" style="color:#fff;font-size:1.08rem;font-weight:500;margin-bottom:.7rem;">Select an option to continue.</div>
                        <div id="quiz" class="quiz-options-list quiz-scroll" role="list" aria-labelledby="quiz-heading">
                            <!-- Quiz options will be loaded here by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
body.quiz-bg { 
    background: #fff; 
}
.quiz-wrapper {
    margin: 3rem 0;
    padding: 2rem 0;
}
.container {
    max-width: 1249px;
    margin: 0 auto;
}
/* Add transition prevention */
#quiz {
    min-height: 200px;
    position: relative;
}
.quiz-container {
    max-width: 1191px;
    width: 100%;
    margin: 0 auto;
    padding: 0;
    position: relative;
    z-index: 1;
    border-radius: 22px;
    box-shadow: 0 3px 14px rgba(0, 0, 0, 0.13);
    overflow: hidden;
    height: 520px; /* Reduced height for more compact display area */
    display: flex; /* Use flexbox for consistent sizing */
}

.quiz-content {
    display: flex;
    flex-direction: row;
    height: 520px;
    min-height: 520px;
    max-height: 520px;
    align-items: stretch;
    width: 100%;
    flex: 1;
}
.quiz-left {
    color: white;
    padding: 2.24rem 2.77rem;
    width: 50%;
    flex: 0 0 50%;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    height: 520px;
    min-height: 520px;
    max-height: 520px;
    border-radius: 8px 0 0 8px;
    text-align: left;
    background: url('/images/flag.jpg') center center/cover no-repeat;
    overflow: hidden; /* Prevent content from expanding container */
    position: relative;
}
.quiz-left > div {
    height: 100%;
    max-height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    overflow: hidden;
}
/* Blue overlay for left section background */
.quiz-left::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(5, 12, 31, 0.72); /* semi-transparent blue */
    z-index: 0;
    border-radius: 8px 0 0 8px;
}
.quiz-left > div {
    position: relative;
    z-index: 1;
}
.quiz-title {
    font-weight: 700;
    font-size: 1.55rem;
    color: white;
    margin-bottom: 0.8rem;
    line-height: 1.15;
    text-shadow: 0 2px 8px rgba(0,0,0,0.16);
    height: 3.5rem;
    min-height: 3.5rem;
    max-height: 3.5rem;
    overflow: hidden;
}
.quiz-description {
    font-size: 1.15rem;
    margin-bottom: 0.5rem;
    color: rgba(255, 255, 255, 0.96);
    line-height: 1.5;
    max-width: 92%;
    text-shadow: 0 1px 6px rgba(0,0,0,0.13);
    height: 7rem;
    min-height: 4.5rem;
    max-height: 7rem;
    overflow-y: auto; /* Allow scrolling if content exceeds */
    overflow-x: hidden;
}
.quiz-right {
    padding: 2.24rem 2.77rem;
    flex: 1 1 0%;
    background: #192544ff;
    border-radius: 0 8px 8px 0;
    height: 520px;
}
.quiz-left, .quiz-right { height: 520px; }
.quiz-right-inner {
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: center;
}
.quiz-scroll { 
    flex: none;
    overflow-y: visible;
    padding-right: .4rem;
    min-height: unset;
    max-height: unset;
    overflow-x: visible;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    transition: opacity 0.35s cubic-bezier(.4,0,.2,1);
    opacity: 1;
}
.quiz-scroll.transitioning {
    opacity: 0.25;
}
.quiz-scroll::-webkit-scrollbar { width:8px; }
.quiz-scroll::-webkit-scrollbar-track { background:rgba(255,255,255,0.07); border-radius:4px; }
.quiz-scroll::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.25); border-radius:4px; }
.quiz-scroll::-webkit-scrollbar-thumb:hover { background:rgba(255,255,255,0.4); }
.quiz-options-list { display:flex; flex-direction:column; gap:.9rem; }
.quiz-option { position:relative; display:block; width:100%; text-align:left; border:1px solid rgba(255,255,255,0.15); background:rgba(255,255,255,0.05); color:#fff; padding:.85rem 1rem .85rem 2.25rem; font-size:.9rem; font-weight:500; border-radius:6px; cursor:pointer; transition:.18s background,.18s border-color,.18s color; }
.quiz-option:before { content:"\2192"; position:absolute; left:.9rem; top:50%; transform:translateY(-50%); opacity:.8; }
.quiz-option:hover { background:rgba(255,255,255,0.12); border-color:rgba(255,255,255,0.35); }
.quiz-option:focus { outline:2px solid #4f8cff; outline-offset:2px; }
.quiz-option[disabled] { opacity:.45; cursor:not-allowed; }
.quiz-option-arrow {
    position: absolute;
    left: -20px;
    font-weight: bold;
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.quiz-option svg {
    display: none;
}

/* Slide-in transitions for left and right section text */
.quiz-left .quiz-title, .quiz-left .quiz-description {
    transition: transform 0.4s cubic-bezier(.4,0,.2,1), opacity 0.4s cubic-bezier(.4,0,.2,1);
    opacity: 1;
    transform: translateX(0);
}
.quiz-left .slide-out {
    opacity: 0;
    transform: translateX(-40px);
}
.quiz-left .slide-in {
    opacity: 1;
    transform: translateX(0);
}
.quiz-right-inner, .quiz-scroll {
    transition: transform 0.4s cubic-bezier(.4,0,.2,1), opacity 0.4s cubic-bezier(.4,0,.2,1);
    opacity: 1;
    transform: translateX(0);
}
.quiz-right-inner.slide-out, .quiz-scroll.slide-out {
    opacity: 0;
    transform: translateX(40px);
}
.quiz-right-inner.slide-in, .quiz-scroll.slide-in {
    opacity: 1;
    transform: translateX(0);
}

@media (max-width: 768px) {
    .quiz-content {
        flex-direction: column;
        height: auto;
        max-height: none;
    }
    .quiz-container {
        margin: 0.8rem;
        max-width: 98%;
        height: auto;
        min-height: 449px;
    }
    .quiz-left, .quiz-right {
        padding: 1.2rem;
        width: 100%;
        height: auto;
    }
    .quiz-title {
        font-size: 1.5rem;
    }
    .quiz-description {
        max-width: 100%;
        font-size: 0.8rem;
    }
    .quiz-scroll {
        max-height: 300px;
    }
}
</style>
<script>document.body.classList.add('quiz-bg');</script>
<div style="height: 130px;"></div>
<script>
(function(){
  const quizEl = document.getElementById('quiz');
  const titleEl = document.getElementById('quiz-heading');
  const descEl = document.querySelector('.quiz-description');
  let busy = false;

  async function fetchState(){
    const r = await fetch('/quiz/state', {headers:{'Accept':'application/json'}});
    if(!r.ok) return; render(await r.json());
  }

  function slideTransitionLeft() {
    titleEl.classList.add('slide-out');
    descEl.classList.add('slide-out');
    setTimeout(() => {
      titleEl.classList.remove('slide-out');
      titleEl.classList.add('slide-in');
      descEl.classList.remove('slide-out');
      descEl.classList.add('slide-in');
      setTimeout(() => {
        titleEl.classList.remove('slide-in');
        descEl.classList.remove('slide-in');
      }, 400);
    }, 200);
  }
  function slideTransitionRight() {
    quizEl.classList.add('slide-out');
    setTimeout(() => {
      quizEl.classList.remove('slide-out');
      quizEl.classList.add('slide-in');
      setTimeout(() => {
        quizEl.classList.remove('slide-in');
      }, 400);
    }, 200);
  }

  function render(data){
    slideTransitionLeft();
    slideTransitionRight();
    setTimeout(() => {
      if(data.done){
        titleEl.textContent = data.terminal.title || 'Result';
        descEl.textContent = data.terminal.message || '';
        let linkHtml = '';
              if(data.terminal.forms && Array.isArray(data.terminal.forms)){
                  if(data.terminal.forms.length > 1){
                      linkHtml += `<div style=\"margin-bottom:.4rem;color:#fff;font-weight:600;\">Recommended Forms:</div>`;
                  }
                  linkHtml += data.terminal.forms.map(f => `<a class=\"quiz-option\" href=\"${f.pdf}\" target=\"_blank\" rel=\"noopener\" download>${f.name}</a>`).join('');
                  if(data.terminal.forms.length > 1){
                      const allUrls = data.terminal.forms.map(f=>f.pdf).join('|');
                      linkHtml += `<button type=\"button\" class=\"quiz-option\" data-download-all=\"1\" data-form-urls=\"${allUrls}\">Open All Forms</button>`;
                  }
              } else if(data.terminal.link) {
                  linkHtml += `<a class=\"quiz-option\" href=\"${data.terminal.link}\" target=\"_blank\" rel=\"noopener\">Open Recommended Form</a>`;
              }
              linkHtml += `<a class=\"quiz-option\" href=\"/pricing\" style=\"margin-top:.7rem;background:#4f8cff;color:#fff;font-weight:600;\">View Pricing & Packages</a>`;
              quizEl.innerHTML = linkHtml + '<button type="button" class="quiz-option" data-restart="1">Start Over</button>';
      } else {
        const node = data.node;
        titleEl.textContent = node.text || '';
        descEl.textContent = '';
        const optionsHtml = node.options.map(o => `<button type="button" class="quiz-option" data-choice="${o.code}">${o.label}</button>`).join('');
        quizEl.innerHTML = optionsHtml;
      }
    }, 200);
  }

  quizEl.addEventListener('click', async e => {
    const restart = e.target.getAttribute('data-restart');
        if(e.target.getAttribute('data-download-all') === '1'){
            const urls = e.target.getAttribute('data-form-urls')?.split('|').filter(Boolean) || [];
            urls.forEach(u => window.open(u, '_blank', 'noopener'));
            return;
        }
    if(restart){
      if(busy) return; busy = true;
      try {
        const r = await fetch('/quiz/reset', {method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content || ''}});
        if(r.ok) render(await r.json());
      } finally { busy = false; }
      return;
    }
    const btn = e.target.closest('.quiz-option');
    if(!btn || busy) return;
    const choice = btn.dataset.choice; if(!choice) return;
    busy = true; btn.disabled = true;
    try {
      const r = await fetch('/quiz/advance', {method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content || ''}, body:JSON.stringify({choice})});
      if(r.ok) render(await r.json());
    } finally { busy = false; }
  });

  // Back button event listener
  const backBtn = document.getElementById('quiz-back-btn');
  if(backBtn) {
    backBtn.addEventListener('click', async () => {
      if(busy) return;
      busy = true;
      backBtn.disabled = true;
      try {
        const r = await fetch('/quiz/reset', {method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content || ''}});
        if(r.ok) render(await r.json());
      } finally { 
        busy = false; 
        backBtn.disabled = false;
      }
    });
  }

  fetchState();
})();
</script>
@endsection
