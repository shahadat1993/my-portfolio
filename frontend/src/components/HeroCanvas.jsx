import { useEffect, useRef } from 'react';

/**
 * Portrait made of scanline "pixels". The cursor reveals + glitches the picture; on scroll the pixels
 * fly apart to the left and right, and snap back together when you scroll up again.
 * `photo` should be a CORS-enabled URL (the API serves one as settings.hero_photo_cors).
 */
export default function HeroCanvas({ photo, motion }) {
  const ref = useRef(null);

  useEffect(() => {
    const hc = ref.current, hx = hc.getContext('2d'), hero = hc.parentElement, srcC = document.createElement('canvas');
    let hw = 0, hh = 0, dpr = 1, PX = null, N = 0, IW = 0, IH = 0, OX = 0, OY = 0, pS = 0, own = false, k = 0.4;
    let mx = 0, my = 0, tx = 0, ty = 0, raf = 0, img = null, alive = true;
    const CELL = 5;

    const placeholder = (c, w, h) => {
      let g = c.createRadialGradient(w / 2, h * 0.4, w * 0.05, w / 2, h * 0.45, w * 0.6);
      g.addColorStop(0, '#2b2b30'); g.addColorStop(1, '#000'); c.fillStyle = g; c.fillRect(0, 0, w, h);
      g = c.createLinearGradient(0, h * 0.7, 0, h); g.addColorStop(0, '#9d9d9d'); g.addColorStop(1, '#303030');
      c.fillStyle = g; c.beginPath(); c.ellipse(w / 2, h * 1.04, w * 0.46, h * 0.3, 0, 0, 7); c.fill();
      c.fillStyle = '#7d7d7d'; c.fillRect(w * 0.42, h * 0.5, w * 0.16, h * 0.25);
      g = c.createRadialGradient(w * 0.46, h * 0.32, w * 0.02, w / 2, h * 0.38, w * 0.25); g.addColorStop(0, '#f0f0f0'); g.addColorStop(1, '#6a6a6a');
      c.fillStyle = g; c.beginPath(); c.ellipse(w / 2, h * 0.38, w * 0.19, h * 0.26, 0, 0, 7); c.fill();
      c.fillStyle = '#101010'; c.beginPath(); c.ellipse(w / 2, h * 0.2, w * 0.2, h * 0.12, 0, 0, 7); c.fill(); c.fillRect(w * 0.3, h * 0.2, w * 0.4, h * 0.07);
      c.fillStyle = '#2a2a2a'; [0.43, 0.57].forEach((x) => { c.beginPath(); c.ellipse(w * x, h * 0.37, w * 0.025, h * 0.012, 0, 0, 7); c.fill(); }); c.fillRect(w * 0.45, h * 0.5, w * 0.1, h * 0.006);
    };

    const buildParticles = () => {
      const cell = CELL * dpr, cols = Math.floor(IW / cell), rows = Math.floor(IH / cell);
      if (cols < 2 || rows < 2) return true;
      const t = document.createElement('canvas'); t.width = cols; t.height = rows;
      const tc = t.getContext('2d'); tc.drawImage(srcC, 0, 0, cols, rows);
      let d; try { d = tc.getImageData(0, 0, cols, rows).data; } catch { return false; } // tainted (no CORS) → caller falls back
      const xs = [], ys = [], bs = [], r1 = [], r2 = [];
      for (let j = 0; j < rows; j++) for (let i = 0; i < cols; i++) {
        const q = (j * cols + i) * 4, b = d[q] * 0.3 + d[q + 1] * 0.59 + d[q + 2] * 0.11;
        if (b > 20) { xs.push(i * cell); ys.push(j * cell); bs.push(b / 255); r1.push(Math.random()); r2.push(Math.random()); }
      }
      PX = { xs: Float32Array.from(xs), ys: Float32Array.from(ys), bs: Float32Array.from(bs), r1: Float32Array.from(r1), r2: Float32Array.from(r2) };
      N = xs.length; return true;
    };
    const buildSrc = () => {
      srcC.width = IW; srcC.height = IH; const c = srcC.getContext('2d'); c.fillStyle = '#000'; c.fillRect(0, 0, IW, IH);
      if (img) {
        const s = Math.max(IW / img.width, IH / img.height), dw = img.width * s, dh = img.height * s;
        c.filter = 'grayscale(1) contrast(1.2) brightness(1.05)'; c.drawImage(img, (IW - dw) / 2, 0, dw, dh); c.filter = 'none';
      } else placeholder(c, IW, IH);
      if (!buildParticles() && img) { img = null; buildSrc(); }
    };
    const size = () => {
      const r = hc.getBoundingClientRect(); if (!r.width || !r.height) return;
      dpr = Math.min(window.devicePixelRatio || 1, 1.5);
      hw = hc.width = Math.round(r.width * dpr); hh = hc.height = Math.round(r.height * dpr);
      IW = Math.round(Math.min(r.width * (innerWidth < 900 ? 0.84 : 0.56), 640) * dpr); IH = Math.round(hh * 0.64);
      OX = Math.round((hw - IW) / 2); OY = Math.round(hh * 0.07);
      buildSrc(); mx = tx = hw / 2; my = ty = OY + IH * 0.42;
    };
    const draw = (t) => {
      if (!hw || !PX) return;
      const tp = motion ? Math.min(1, Math.max(0, scrollY / (innerHeight * 0.85))) : 0;
      pS += (tp - pS) * 0.1; if (pS < 0.001) pS = 0;
      const e = pS * pS * (3 - 2 * pS);
      if (!own) { tx = OX + IW * (0.5 + 0.2 * Math.sin(t * 0.0006)); ty = OY + IH * (0.42 + 0.18 * Math.sin(t * 0.0008)); }
      mx += (tx - mx) * 0.12; my += (ty - my) * 0.12; k += ((own ? 1 : 0.4) - k) * 0.06;
      hx.clearRect(0, 0, hw, hh); hx.fillStyle = '#f1f0ea';
      const cell = CELL * dpr, R0 = IW * 0.45, bar = cell * (0.5 + 0.5 * e), { xs, ys, bs, r1, r2 } = PX;
      for (let i = 0; i < N; i++) {
        let x = OX + xs[i], y = OY + ys[i];
        const dd = Math.min(1, Math.hypot(x - mx, y - my) / R0), rv = 0.14 + 0.86 * (1 - dd) * (1 - dd);
        const f = Math.max(0, 1 - Math.abs(y - my) / R0);
        x += Math.sin((y * 0.05) / dpr + t * 0.004) * IW * 0.025 * f * f * k;
        let a = bs[i] * rv * Math.max(0, 1 - Math.max(0, (ys[i] / IH - 0.62) / 0.38));
        if (e > 0) { x += (x < hw / 2 ? -1 : 1) * e * hw * 0.5 * (0.3 + r1[i] * 1.1); y += (r2[i] - 0.5) * e * hh * 0.28; a *= 1 - e * 0.85; }
        if (a < 0.03) continue;
        hx.globalAlpha = a; hx.fillRect(x, y, cell, bar);
      }
      hx.globalAlpha = 1;
    };
    const loop = (t) => {
      if (!alive) return; raf = requestAnimationFrame(loop);
      const r = hc.getBoundingClientRect(); if (r.bottom < -50 || r.top > innerHeight + 50) return; // off-screen: skip work
      draw(t);
    };
    const onMove = (e) => { const r = hc.getBoundingClientRect(); if (!r.width) return; own = true; tx = (e.clientX - r.left) * hw / r.width; ty = (e.clientY - r.top) * hh / r.height; };
    const onLeave = () => { own = false; };

    size();
    if (photo) { const im = new Image(); im.crossOrigin = 'anonymous'; im.onload = () => { img = im; size(); if (!motion) draw(0); }; im.src = photo; }
    if (motion) raf = requestAnimationFrame(loop); else draw(0);
    hero.addEventListener('mousemove', onMove); hero.addEventListener('mouseleave', onLeave); addEventListener('resize', size);
    return () => { alive = false; cancelAnimationFrame(raf); hero.removeEventListener('mousemove', onMove); hero.removeEventListener('mouseleave', onLeave); removeEventListener('resize', size); };
  }, [photo, motion]);

  return <canvas id="hcv" ref={ref} aria-hidden="true" />;
}
