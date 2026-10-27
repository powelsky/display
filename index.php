<?php

declare(strict_types=1);

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

if (!isset($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$csrf = $_SESSION['csrf'];
$nonce = base64_encode(random_bytes(18));

header("Content-Security-Policy: default-src 'self'; script-src 'nonce-{$nonce}'; style-src 'nonce-{$nonce}'; img-src 'self' data:; connect-src 'self'; worker-src 'self'; manifest-src 'self'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'");
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: geolocation=(), camera=(), microphone=()');
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Mandiri USD → IDR</title>
<meta name="theme-color" content="#F4F5F4">
<link rel="manifest" href="manifest.json">
<link rel="icon" href="favicon.ico" sizes="any">
<link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Mandiri FX">
<style nonce="<?= htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8') ?>">
@font-face{font-family:"Segoe UI Adjusted";src:local("Segoe UI");ascent-override:95%}
@font-face{font-family:"Segoe UI Adjusted";src:local("Segoe UI Italic");ascent-override:95%;font-style:italic}
@font-face{font-family:"Segoe UI Adjusted";src:local("Segoe UI Semibold");ascent-override:95%;font-weight:600}
@font-face{font-family:"Segoe UI Adjusted";src:local("Segoe UI Semibold Italic");ascent-override:95%;font-style:italic;font-weight:600}
@font-face{font-family:"Segoe UI Adjusted";src:local("Segoe UI Semibold");ascent-override:95%;font-weight:700}
@font-face{font-family:"Segoe UI Adjusted";src:local("Segoe UI Semibold Italic");ascent-override:95%;font-style:italic;font-weight:700}
@font-face{font-family:"Segoe UI Adjusted";src:local("Segoe UI Bold");ascent-override:90%;font-weight:800}
@font-face{font-family:"Segoe UI Adjusted";src:local("Segoe UI Bold Italic");ascent-override:95%;font-style:italic;font-weight:800}
:root{--bg:#F4F5F4;--panel:#fff;--text:#111;--muted:#6b7280;--line:#e5e7eb;--ok:#08783e;--danger:#b42318;--r:.5rem;--ff-sans:"MyriadPro","Helvetica Neue",Helvetica,Arial,sans-serif;--ff-mono:ui-monospace,"SF Mono","Cascadia Mono","Segoe UI Mono","Liberation Mono",Menlo,Monaco,Consolas,monospace;--shadow:0 1px 3px rgba(0,0,0,.05)}
*,::before,::after{box-sizing:border-box}html,body{margin:0;color:var(--text);font-family:var(--ff-sans);font-size:14px}html{background:var(--bg);-webkit-text-size-adjust:100%;text-size-adjust:100%}body{padding:max(env(safe-area-inset-top),clamp(8px,2vw,16px)) max(env(safe-area-inset-right),clamp(8px,2vw,16px)) max(env(safe-area-inset-bottom),clamp(8px,2vw,16px)) max(env(safe-area-inset-left),clamp(8px,2vw,16px));caret-color:transparent;user-select:none;-webkit-user-select:none}input,select,textarea{caret-color:transparent;user-select:text;-webkit-user-select:text}.wrap{width:100%;max-width:1100px;margin:0 auto}.grid{display:grid;grid-template-columns:1.15fr .85fr;gap:14px}.card{background:var(--panel);border:1px solid var(--line);border-radius:var(--r);box-shadow:var(--shadow);padding:clamp(12px,2.2vw,16px)}.title{font-size:clamp(15px,2.2vw,18px);font-weight:700;margin:0 0 4px}.word{position:relative;border-bottom: 1px solid var(--line);z-index:2;min-width:20ch;max-width:92vw;margin:0;padding:0 .5rem;text-align:center;white-space:pre-wrap;overflow-wrap:anywhere;font-size:clamp(10px,.85vw,13px);font-weight:400;line-height:1.6;letter-spacing:.22em;color:#24252A;-webkit-text-stroke:0;text-shadow:0 .07em .14em rgba(36,37,42,.4),0 .02em .05em rgba(36,37,42,.3);user-select:none;-webkit-user-select:none;text-rendering:geometricPrecision;font-variant-ligatures:none;font-kerning:normal;font-feature-settings:"liga" 0,"calt" 0,"kern" 1;transform:translateZ(0);backface-visibility:hidden;will-change:contents}.word.d{font-weight:300;letter-spacing:.3em;text-shadow:0 .06em .12em rgba(36,37,42,.26),0 .015em .04em rgba(36,37,42,.2)}.word.l{letter-spacing:.18em}.word.g{animation:g .12s steps(2,end) 1}.word:after{content:"";display:inline-block;width:.1em;min-width:2px;height:1.1em;margin-left:.28em;vertical-align:-.12em;background:#24252A;transform-origin:bottom;animation:c 1.2s ease-in-out infinite}@keyframes c{0%{opacity:0;transform:scaleY(.6)}20%,60%{opacity:1;transform:scaleY(1)}80%,100%{opacity:0;transform:scaleY(.6)}}@keyframes cf{0%{opacity:0}20%,60%{opacity:1}80%,100%{opacity:0}}@keyframes g{0%{transform:translate(0)}30%{transform:translate(-.4px,.4px)}60%{transform:translate(.4px,-.4px)}100%{transform:translate(0)}}@media(max-width:600px){.word{min-width:16ch;max-width:94vw;font-size:clamp(9px,2.6vw,12px);letter-spacing:.16em;line-height:1.65}.word.l{letter-spacing:.14em}.word.d{letter-spacing:.24em}}@media(prefers-reduced-motion:reduce){.word:after{animation:cf 1.2s ease-in-out infinite}.word.g{animation:none!important}}.muted{color:var(--muted);font-size:12px}.rate{display:flex;flex-wrap:wrap;align-items:end;justify-content:space-between;gap:12px;margin:16px 0}.rate strong{font-family:var(--ff-mono);font-variant-numeric:tabular-nums;font-size:clamp(22px,5.2vw,30px);letter-spacing:-.03em}.status{font-size:12px;color:var(--muted)}.inputs{display:grid;grid-template-columns:1fr 1fr;gap:10px}.field label{display:block;font-size:12px;color:var(--muted);margin-bottom:5px}.field input{width:100%;height:42px;border:1px solid var(--line);border-radius:var(--r);padding:0 12px;font-family:var(--ff-mono);font-variant-numeric:tabular-nums;font-size:16px;background:#fff;outline:none}.field input:focus{border-color:#111}::placeholder{color:rgba(200,200,200,.8)}.result{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-top:12px}.metric{border:1px solid var(--line);border-radius:var(--r);padding:10px}.metric span{display:block;color:var(--muted);font-size:11px;margin-bottom:4px}.metric strong{font-family:var(--ff-mono);font-variant-numeric:tabular-nums;font-size:16px}.parts{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:8px}.actions{display:flex;gap:8px;margin-top:12px}.btn{border:1px solid #111;background:#111;color:#fff;height:36px;border-radius:var(--r);padding:0 14px;cursor:pointer;font-weight:600}.btn.alt{background:#fff;color:#111;border-color:var(--line)}.btn.danger{background:#fff;color:var(--danger);border-color:#fecaca}.btn:disabled{opacity:.55;cursor:not-allowed}.msg{min-height:18px;margin-top:10px;font-size:12px}.msg.ok{color:var(--ok)}.msg.err{color:var(--danger)}table{width:100%;border-collapse:collapse;margin-top:10px;font-family:var(--ff-mono);font-variant-numeric:tabular-nums;font-size:12px}th,td{text-align:right;padding:clamp(6px,1.2vw,8px);border-bottom:1px solid var(--line);white-space:nowrap}th:first-child,td:first-child{text-align:left}.table-wrap{overflow-x:auto;overflow-y:hidden;-webkit-overflow-scrolling:touch;scrollbar-width:thin;scrollbar-color:var(--line) transparent}.weekly{display:grid;gap:8px;margin-top:10px}.week{display:grid;grid-template-columns:1fr auto;gap:4px;border:1px solid var(--line);border-radius:var(--r);padding:9px}.week strong,.week small{font-family:var(--ff-mono);font-variant-numeric:tabular-nums}.week small{color:var(--muted)}.stack{margin-top:14px}.hist-head{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:8px}.week>div+div{text-align:right}@media(max-width:1000px){.grid{grid-template-columns:1fr}}@media(max-width:560px){.parts{grid-template-columns:repeat(2,1fr)}}@media(max-width:420px){.inputs,.result{grid-template-columns:1fr}}@media print{*,:before,:after{background:transparent!important;color:#000!important;box-shadow:none!important;text-shadow:none!important}}.copy{cursor:pointer;border-radius:4px;transition:background-color .12s ease,color .12s ease}.copy:hover{background:rgba(17,17,17,.06)}.copy:active{background:rgba(17,17,17,.12)}.copy:focus-visible{outline:2px solid #111;outline-offset:2px}.copy.copied{background:rgba(8,120,62,.14);color:var(--ok)}.clip-helper{position:fixed;top:0;left:-9999px;width:1px;height:1px;opacity:0;pointer-events:none}
</style>
</head>
<body>
<div class="wrap">
  <div class="grid">
    <section class="card">
      <h1 class="word">Mandiri USD → IDR</h1>
      <div class="rate">
        <div>
          <div class="muted">1 USD</div>
          <strong id="rate" class="copy" tabindex="0" role="button" title="Klik: salin format mata uang · Shift+Klik: angka mentah">—</strong>
        </div>
        <div class="status" id="rateStatus">Memuat kurs…</div>
      </div>

      <div class="inputs">
        <div class="field"><label for="net1">NET 1 (USD)</label><input id="net1" type="number" min="0" step="0.01" value="0" inputmode="decimal"></div>
        <div class="field"><label for="net2">NET 2 (USD)</label><input id="net2" type="number" min="0" step="0.01" value="0" inputmode="decimal"></div>
      </div>

      <div class="result">
        <div class="metric"><span>Total USD</span><strong id="totalUsd" class="copy" tabindex="0" role="button" title="Klik: salin format mata uang · Shift+Klik: angka mentah">$0.00</strong></div>
        <div class="metric"><span>Total IDR</span><strong id="totalIdr" class="copy" tabindex="0" role="button" title="Klik: salin format mata uang · Shift+Klik: angka mentah">IDR 0</strong></div>
      </div>

      <div class="parts">
        <div class="metric"><span>M</span><strong class="part copy" tabindex="0" role="button" title="Klik: salin format mata uang · Shift+Klik: angka mentah">IDR 0</strong></div>
        <div class="metric"><span>B</span><strong class="part copy" tabindex="0" role="button" title="Klik: salin format mata uang · Shift+Klik: angka mentah">IDR 0</strong></div>
        <div class="metric"><span>R</span><strong class="part copy" tabindex="0" role="button" title="Klik: salin format mata uang · Shift+Klik: angka mentah">IDR 0</strong></div>
        <div class="metric"><span>Misc</span><strong class="part copy" tabindex="0" role="button" title="Klik: salin format mata uang · Shift+Klik: angka mentah">IDR 0</strong></div>
      </div>

      <div class="actions">
        <button class="btn" id="saveBtn" type="button">Simpan</button>
        <button class="btn alt" id="refreshBtn" type="button">Refresh kurs</button>
        <button class="btn alt" id="installBtn" type="button" hidden>Pasang aplikasi</button>
      </div>
      <div class="msg" id="msg"></div>
      <div class="muted" id="installHint" hidden>iOS: tombol Bagikan → “Tambahkan ke Layar Utama”.</div>
    </section>

    <aside class="card">
      <div class="title">Ringkasan Mingguan</div>
      <div class="muted">12 minggu terakhir</div>
      <div class="weekly" id="weekly"></div>
    </aside>
  </div>

  <section class="card stack">
    <div class="hist-head">
      <div><div class="title">Histori</div><div class="muted">Maks. 100 transaksi terakhir · auto refresh 5 detik</div></div>
      <button class="btn danger" id="clearBtn" type="button">Hapus histori</button>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Waktu</th><th>NET1</th><th>NET2</th><th>Total USD</th><th>Rate</th><th>Total IDR</th><th>M / Misc</th><th>B / R</th></tr></thead>
        <tbody id="history"><tr><td colspan="8">Memuat…</td></tr></tbody>
      </table>
    </div>
  </section>
</div>

<script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8') ?>">
'use strict';
const CSRF = <?= json_encode($csrf, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
let currentRate = 0;
const $ = id => document.getElementById(id);
const idrFmt = new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',currencyDisplay:'code',maximumFractionDigits:0});
const usdFmt = new Intl.NumberFormat('en-US',{style:'currency',currency:'USD',minimumFractionDigits:2,maximumFractionDigits:2});
const num = v => { const n = Number(v); return Number.isFinite(n) && n >= 0 ? n : 0; };
function copyText(text){
  if(navigator.clipboard&&window.isSecureContext){return navigator.clipboard.writeText(text);}
  return new Promise((resolve,reject)=>{
    const ta=document.createElement('textarea');
    ta.className='clip-helper'; ta.value=text; ta.setAttribute('readonly','');
    document.body.appendChild(ta); ta.select();
    const ok=document.execCommand('copy'); ta.remove();
    ok?resolve():reject(new Error('copy failed'));
  });
}
function copyFrom(el,raw=false){
  const label=el.dataset.copyLabel||'';
  const value=(raw&&el.dataset.copyRaw!==undefined)?el.dataset.copyRaw:(el.dataset.copy!==undefined?el.dataset.copy:el.textContent.trim());
  copyText(value).then(()=>{
    el.classList.add('copied'); setTimeout(()=>el.classList.remove('copied'),700);
    message((label?label+' ':'')+value+' disalin','ok');
  }).catch(()=>message('Gagal menyalin.','err'));
}
document.addEventListener('click',e=>{
  const el=e.target.closest('.copy,[data-copy]');
  if(!el||el.closest('button,input,select,textarea,a'))return;
  copyFrom(el,e.shiftKey);
});
document.addEventListener('keydown',e=>{
  if(e.key!=='Enter'&&e.key!==' ')return;
  const el=e.target.closest('.copy[role="button"]');
  if(!el)return;
  e.preventDefault(); copyFrom(el,e.shiftKey);
});

function recalc(){
  const n1=num($('net1').value), n2=num($('net2').value), total=n1+n2;
  const idr=total*currentRate, p1=n1*currentRate/4, p2=n2*currentRate/2;
  $('totalUsd').textContent=usdFmt.format(total);
  $('totalUsd').dataset.copy=usdFmt.format(total);
  $('totalUsd').dataset.copyRaw=total.toFixed(2);
  $('totalIdr').textContent=idrFmt.format(idr);
  $('totalIdr').dataset.copy=idrFmt.format(idr);
  $('totalIdr').dataset.copyRaw=idr.toFixed(2);
  const parts=[p1,p1+p2,p1+p2,p1];
  document.querySelectorAll('.part').forEach((el,i)=>{ el.textContent=idrFmt.format(parts[i]); el.dataset.copy=idrFmt.format(parts[i]); el.dataset.copyRaw=parts[i].toFixed(2); });
}
function message(text,type=''){ $('msg').textContent=text; $('msg').className='msg '+type; }
async function loadRate(force=false){
  $('refreshBtn').disabled=true;
  try{
    const r=await fetch('api.php?action=rate'+(force?'&refresh=1':''),{headers:{Accept:'application/json'},cache:'no-store'});
    const d=await r.json(); if(!r.ok||!d.ok) throw new Error(d.error||'Rate gagal');
    currentRate=Number(d.rate)||0;
    $('rate').textContent=idrFmt.format(currentRate);
    $('rate').dataset.copy=idrFmt.format(currentRate);
    $('rate').dataset.copyRaw=String(currentRate);
    $('rateStatus').textContent=(d.stale?'Stale cache · ':d.cached?'Cache · ':'Live · ')+new Date(d.updated_at).toLocaleString('id-ID');
    recalc();
  }catch(e){ $('rateStatus').textContent='Kurs tidak tersedia'; message(e.message,'err'); }
  finally{$('refreshBtn').disabled=false;}
}
async function save(){
  const net1=num($('net1').value), net2=num($('net2').value);
  if(net1+net2<=0){message('NET1 + NET2 harus lebih dari 0.','err');return;}
  $('saveBtn').disabled=true; message('Menyimpan…');
  try{
    const r=await fetch('api.php?action=save',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({csrf:CSRF,net1,net2})});
    const d=await r.json(); if(!r.ok||!d.ok) throw new Error(d.error||'Simpan gagal');
    currentRate=Number(d.item.rate)||currentRate; $('rate').textContent=idrFmt.format(currentRate); $('rate').dataset.copy=idrFmt.format(currentRate); $('rate').dataset.copyRaw=String(currentRate); recalc(); message('Tersimpan.','ok'); await loadHistory();
  }catch(e){message(e.message,'err');}
  finally{$('saveBtn').disabled=false;}
}
function esc(s){return String(s).replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));}
async function loadHistory(){
  try{
    const r=await fetch('api.php?action=history',{headers:{Accept:'application/json'},cache:'no-store'}); const d=await r.json(); if(!r.ok||!d.ok) throw new Error(d.error||'History gagal');
    $('history').innerHTML=d.history.length?d.history.map(x=>`<tr><td>${esc(new Date(x.created_at).toLocaleString('id-ID'))}</td><td class="copy" data-copy="${esc(usdFmt.format(Number(x.net1_usd)))}" data-copy-raw="${esc(String(x.net1_usd))}">${esc(usdFmt.format(Number(x.net1_usd)))}</td><td class="copy" data-copy="${esc(usdFmt.format(Number(x.net2_usd)))}" data-copy-raw="${esc(String(x.net2_usd))}">${esc(usdFmt.format(Number(x.net2_usd)))}</td><td class="copy" data-copy="${esc(usdFmt.format(Number(x.total_usd)))}" data-copy-raw="${esc(String(x.total_usd))}">${esc(usdFmt.format(Number(x.total_usd)))}</td><td class="copy" data-copy="${esc(idrFmt.format(Number(x.rate)))}" data-copy-raw="${esc(String(x.rate))}">${esc(idrFmt.format(Number(x.rate)))}</td><td class="copy" data-copy="${esc(idrFmt.format(Number(x.total_idr)))}" data-copy-raw="${esc(String(x.total_idr))}">${esc(idrFmt.format(Number(x.total_idr)))}</td><td class="copy" data-copy="${esc(idrFmt.format(Number(x.part_m)))}" data-copy-raw="${esc(String(x.part_m))}">${esc(idrFmt.format(Number(x.part_m)))}</td><td class="copy" data-copy="${esc(idrFmt.format(Number(x.part_b)))}" data-copy-raw="${esc(String(x.part_b))}">${esc(idrFmt.format(Number(x.part_b)))}</td></tr>`).join(''):'<tr><td colspan="8">Belum ada data.</td></tr>';
    $('weekly').innerHTML=d.weekly.length?d.weekly.map(w=>`<div class="week"><div><strong class="copy" data-copy="${esc(String(w.week||''))}">${esc(w.week||'-')}</strong><br><small>${esc(w.transactions)} transaksi · ${esc(usdFmt.format(Number(w.total_usd||0)))}</small></div><div><strong class="copy" data-copy="${esc(idrFmt.format(Number(w.total_idr||0)))}" data-copy-raw="${esc(String(w.total_idr||0))}">${esc(idrFmt.format(Number(w.total_idr||0)))}</strong><br><small>M/Misc ${esc(idrFmt.format(Number(w.total_part_m||0)))} · B/R ${esc(idrFmt.format(Number(w.total_part_b||0)))}</small></div></div>`).join(''):'<div class="muted">Belum ada data.</div>';
  }catch(e){console.error(e);}
}
async function clearHistory(){
  if(!confirm('Hapus seluruh histori?')) return;
  try{
    const r=await fetch('api.php?action=clear',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({csrf:CSRF})});
    const d=await r.json(); if(!r.ok||!d.ok) throw new Error(d.error||'Hapus gagal'); await loadHistory(); message('Histori dihapus.','ok');
  }catch(e){message(e.message,'err');}
}
$('net1').addEventListener('input',recalc); $('net2').addEventListener('input',recalc);
$('saveBtn').addEventListener('click',save); $('refreshBtn').addEventListener('click',()=>loadRate(true)); $('clearBtn').addEventListener('click',clearHistory);
let installEvent=null;
addEventListener('beforeinstallprompt',e=>{ e.preventDefault(); installEvent=e; $('installBtn').hidden=false; });
$('installBtn').addEventListener('click',async()=>{
  if(!installEvent) return;
  installEvent.prompt();
  const choice=await installEvent.userChoice;
  installEvent=null; $('installBtn').hidden=true;
  message(choice.outcome==='accepted'?'Aplikasi dipasang.':'Pemasangan dibatalkan.',choice.outcome==='accepted'?'ok':'');
});
addEventListener('appinstalled',()=>{ installEvent=null; $('installBtn').hidden=true; message('Aplikasi terpasang.','ok'); });
if(/iPad|iPhone|iPod/.test(navigator.userAgent)&&!window.navigator.standalone){ $('installHint').hidden=false; }
loadRate(); loadHistory(); setInterval(loadHistory,5000); setInterval(()=>loadRate(false),60000);
if ('serviceWorker' in navigator) {
  addEventListener('load', () => {
    navigator.serviceWorker.register('sw.js', { scope: './' }).catch(() => {});
  }, { once: true });
}
</script>
</body>
</html>
