<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Loading dulu ya...</title>

  <style>
    :root{
      --bg:#0b1020;
      --panel:#0f172a;
      --text:#d1d5db;
      --muted:#94a3b8;
      --accent:#22c55e; /* green git */
      --warn:#eab308;
      --danger:#ef4444;
      --border:rgba(255,255,255,.08);
    }

    *{box-sizing:border-box}
    body{
      margin:0;
      min-height:100vh;
      background: radial-gradient(1200px 700px at 20% 10%, rgba(34,197,94,.10), transparent 55%),
                  radial-gradient(900px 600px at 80% 0%, rgba(56,189,248,.10), transparent 50%),
                  var(--bg);
      color:var(--text);
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:24px;
    }

    .card{
      width:min(920px, 100%);
      border:1px solid var(--border);
      background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.01));
      border-radius:18px;
      box-shadow: 0 18px 60px rgba(0,0,0,.45);
      overflow:hidden;
    }

    .topbar{
      display:flex;
      align-items:center;
      gap:10px;
      padding:12px 14px;
      background: rgba(15,23,42,.65);
      border-bottom:1px solid var(--border);
    }
    .dots{display:flex; gap:8px; margin-right:10px}
    .dot{width:10px;height:10px;border-radius:999px;opacity:.9}
    .dot.red{background:#ff5f56}
    .dot.yellow{background:#ffbd2e}
    .dot.green{background:#27c93f}
    .title{
      color:var(--muted);
      font-size:13px;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }
    .body{
      display:grid;
      grid-template-columns: 1fr;
      gap:0;
    }

    .terminal{
      padding:18px 18px 14px;
      background: rgba(2,6,23,.35);
    }

    pre{
      margin:0;
      white-space:pre-wrap;
      word-break:break-word;
      line-height:1.5;
      font-size:13.5px;
      color:var(--text);
    }

    .prompt{ color: var(--accent); }
    .muted{ color: var(--muted); }
    .warn{ color: var(--warn); }
    .danger{ color: var(--danger); }
    .accent{ color: var(--accent); }

    .caret{
      display:inline-block;
      width:8px;
      margin-left:2px;
      background:rgba(209,213,219,.85);
      animation: blink 1s steps(1) infinite;
      transform: translateY(2px);
    }
    @keyframes blink { 50% { opacity:0; } }

    .footer{
      padding:12px 18px;
      border-top:1px solid var(--border);
      background: rgba(15,23,42,.55);
      display:flex;
      flex-wrap:wrap;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      font-size:12.5px;
      color:var(--muted);
    }

    .pill{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:7px 10px;
      border:1px solid var(--border);
      border-radius:999px;
      background: rgba(255,255,255,.03);
    }
    .spinner{
      width:14px;height:14px;border-radius:999px;
      border:2px solid rgba(255,255,255,.18);
      border-top-color: rgba(34,197,94,.9);
      animation: spin .9s linear infinite;
    }
    @keyframes spin { to{ transform:rotate(360deg);} }

    a{ color:#93c5fd; text-decoration:none }
    a:hover{ text-decoration:underline }
  </style>
</head>

<body>
  <div class="card" role="status" aria-live="polite">
    <div class="topbar">
      <div class="dots">
        <span class="dot red"></span>
        <span class="dot yellow"></span>
        <span class="dot green"></span>
      </div>
      <div class="title">system-pkl-pins — booting session</div>
    </div>

    <!-- <div class="body">
        <div class="text-xs text-slate-400 mb-3 text-center">
             Menyiapkan dashboard pembimbing...
        </div> -->

      <div class="terminal">
        <pre id="screen"></pre>
        <span class="caret" id="caret">&nbsp;</span>
      </div>

      <div class="footer">
        <span class="pill"><span class="spinner"></span> Menyiapkan dashboard pembimbing…</span>
        <span class="pill">
          Dev: <span class="accent" id="devName">Ay</span> —
          <a id="devEmail" href="mailto:kamu@email.com">kamu@email.com</a>
        </span>
      </div>
    </div>
  </div>

  <script>
    // === CONFIG ===
    const REDIRECT_TO = "{{ route('pembimbing.dashboard') }}";
    const DEV_NAME = "AYYYYYY";                 // ganti sesuai kamu
    const DEV_EMAIL = "ayamcademic@email.com";    // ganti emailmu

      const TYPE_SPEED = 5;   // ms per karakter
  const FINAL_WAIT = 300;  // jeda sebelum redirect

    // set credit
    document.getElementById('devName').textContent = DEV_NAME;
    const emailEl = document.getElementById('devEmail');
    emailEl.textContent = DEV_EMAIL;
    emailEl.href = `mailto:${DEV_EMAIL}`;

    // === TERMINAL SCRIPT ===
    const lines = [
      { t: `${DEV_NAME.toLowerCase()}@pkl-pins:~$ `, c: "prompt" },
      { t: `git fetch --all`, c: "" },
      { t: `${DEV_NAME.toLowerCase()}@pkl-pins:~$ `, c: "prompt" },
      { t: `php artisan optimize`, c: "" },
      { t: `npm run build`, c: "" },
      { t: `✓ build completed`, c: "accent" },
      { t: `Launching dashboard…`, c: "warn" },
      { t: `Session ready.`, c: "accent" },
    ];

    const screen = document.getElementById('screen');
    const caret = document.getElementById('caret');

    function span(text, cls){
      const s = document.createElement('span');
      if (cls) s.className = cls;
      s.textContent = text;
      return s;
    }

    // typing engine
    async function typeAll(){
      caret.style.display = "inline-block";

      for (const line of lines){
        // create line container
        const row = document.createElement('div');

        // type line char-by-char
        const text = line.t;
        for (let i=0; i<text.length; i++){
          row.appendChild(span(text[i], line.c));
          screen.appendChild(row);
          // keep scrolling
          window.scrollTo(0, document.body.scrollHeight);
          await new Promise(r => setTimeout(r, TYPE_SPEED));
        }

        // newline
        screen.appendChild(document.createTextNode("\n"));
        await new Promise(r => setTimeout(r, 10));
      }

      caret.style.display = "none";
      await new Promise(r => setTimeout(r, FINAL_WAIT));
      window.location.href = REDIRECT_TO;
    }

    // start
    typeAll();
  </script>
</body>
</html>
