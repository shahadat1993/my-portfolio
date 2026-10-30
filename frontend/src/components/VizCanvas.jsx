import { useEffect, useRef } from 'react';

/** mode 0 = dot wave, 1 = squares (Build), 2 = rings (Teach), 3 = network (Connect). Reacts to the cursor. */
export default function VizCanvas({ mode, accent, dark }) {
  const ref = useRef(null);
  const live = useRef({ mode, accent, dark, fade: 1 });
  useEffect(() => { live.current.mode = mode; live.current.accent = accent; live.current.dark = dark; live.current.fade = 0; }, [mode, accent, dark]);

  useEffect(() => {
    const c = ref.current, x = c.getContext('2d');
    let w = 0, h = 0, dpr = 1, raf = 0, m = { x: 0.5, y: 0.5 };
    const nodes = Array.from({ length: 26 }, () => ({ x: Math.random(), y: Math.random(), vx: (Math.random() - 0.5) * 0.0009, vy: (Math.random() - 0.5) * 0.0009 }));
    const size = () => { const r = c.getBoundingClientRect(); if (!r.width) return; dpr = Math.min(window.devicePixelRatio || 1, 1.5); w = c.width = Math.round(r.width * dpr); h = c.height = Math.round(r.height * dpr); };
    const onMove = (e) => { const r = c.getBoundingClientRect(); m = { x: (e.clientX - r.left) / r.width, y: (e.clientY - r.top) / r.height }; };
    const draw = (t) => {
      raf = requestAnimationFrame(draw);
      const r = c.getBoundingClientRect(); if (r.bottom < 0 || r.top > innerHeight) return;
      if (!w) size(); if (!w) return;
      const L = live.current, fg = L.dark ? '241,240,234' : '14,14,16', a = L.accent; L.fade = Math.min(1, L.fade + 0.04);
      x.clearRect(0, 0, w, h); const cX = w / 2, cY = h / 2;
      if (L.mode === 0) {
        const gx = 18 * dpr, cs = Math.ceil(w / gx), rs = Math.ceil(h / gx);
        for (let i = 0; i < cs; i++) for (let j = 0; j < rs; j++) {
          const px = i * gx + gx / 2, py = j * gx + gx / 2, d = Math.hypot(px / w - m.x, py / h - m.y);
          const z = Math.sin(i * 0.35 + t * 0.0015) + Math.cos(j * 0.3 + t * 0.0012), kk = Math.max(0, 1 - d * 2.4);
          x.globalAlpha = (0.2 + 0.25 * (z + 1) / 2 + kk * 0.6) * L.fade; x.fillStyle = kk > 0.25 ? a : `rgb(${fg})`;
          x.beginPath(); x.arc(px, py + z * 3 * dpr, (1 + z * 0.4 + kk * 2.6) * dpr, 0, 7); x.fill();
        }
      } else if (L.mode === 1) {
        x.lineWidth = 1.2 * dpr;
        for (let n = 0; n < 8; n++) {
          const s = w * 0.07 * (n + 1) + Math.sin(t * 0.002 + n) * 5 * dpr;
          x.save(); x.translate(cX, cY); x.rotate((n % 2 ? 1 : -1) * Math.sin(t * 0.0004) * 0.25);
          x.globalAlpha = (0.9 - n * 0.09) * L.fade; x.strokeStyle = a; x.strokeRect(-s / 2, -s / 2, s, s); x.restore();
        }
      } else if (L.mode === 2) {
        x.lineWidth = 1.4 * dpr;
        for (let n = 0; n < 6; n++) {
          const ph = (t * 0.0004 + n / 6) % 1; x.globalAlpha = (1 - ph) * L.fade; x.strokeStyle = n % 2 ? a : `rgb(${fg})`;
          x.beginPath(); x.arc(cX, cY, ph * Math.min(w, h) * 0.55, 0, 7); x.stroke();
        }
      } else {
        nodes.forEach((n) => { n.x += n.vx * 16; n.y += n.vy * 16; if (n.x < 0 || n.x > 1) n.vx *= -1; if (n.y < 0 || n.y > 1) n.vy *= -1; });
        x.lineWidth = dpr;
        for (let i = 0; i < nodes.length; i++) {
          const p = nodes[i];
          for (let j = i + 1; j < nodes.length; j++) {
            const q = nodes[j], d = Math.hypot((p.x - q.x) * w, (p.y - q.y) * h);
            if (d < w * 0.22) { x.globalAlpha = (1 - d / (w * 0.22)) * 0.6 * L.fade; x.strokeStyle = a; x.beginPath(); x.moveTo(p.x * w, p.y * h); x.lineTo(q.x * w, q.y * h); x.stroke(); }
          }
          const dm = Math.hypot(p.x - m.x, p.y - m.y); x.globalAlpha = L.fade; x.fillStyle = dm < 0.18 ? a : `rgb(${fg})`;
          x.beginPath(); x.arc(p.x * w, p.y * h, (2.5 + (dm < 0.18 ? 3 : 0)) * dpr, 0, 7); x.fill();
        }
      }
      x.globalAlpha = 1;
    };
    size(); c.addEventListener('mousemove', onMove); addEventListener('resize', size); raf = requestAnimationFrame(draw);
    return () => { cancelAnimationFrame(raf); c.removeEventListener('mousemove', onMove); removeEventListener('resize', size); };
  }, []);

  return <canvas ref={ref} />;
}
