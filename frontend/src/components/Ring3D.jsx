import { useEffect, useRef, useState } from 'react';

/**
 * Draggable 3D carousel. items = any array; renderCard(item, i) returns the card inner content.
 * dim(item) → true greys a card out (used for filters/search). onOpen(i) is called on click.
 */
export default function Ring3D({ items, renderCard, onOpen, onActive, dim, motion, active = true }) {
  const stage = useRef(null), ring = useRef(null);
  const st = useRef({ rot: 0, vel: motion ? -18 : 0, drag: false, lx: 0, mv: 0, li: -1 });
  const [W, setW] = useState(typeof innerWidth !== 'undefined' && innerWidth < 900 ? 180 : 250);
  const n = items.length || 1;
  const RR = Math.round(W / 2 / Math.tan(Math.PI / Math.max(n, 3))) + 40;

  useEffect(() => { const f = () => setW(innerWidth < 900 ? 180 : 250); addEventListener('resize', f); return () => removeEventListener('resize', f); }, []);
  useEffect(() => { const up = () => { st.current.drag = false; }; addEventListener('pointerup', up); return () => removeEventListener('pointerup', up); }, []);

  useEffect(() => {
    let raf;
    const loop = () => {
      raf = requestAnimationFrame(loop);
      const s = st.current;
      if (!active || !ring.current) return;
      if (!s.drag) { s.rot += s.vel; s.vel += ((motion ? 0.12 : 0) - s.vel) * 0.05; }
      ring.current.style.transform = `translateZ(${-RR}px) rotateY(${s.rot}deg)`;
      const i = ((Math.round(-s.rot / (360 / n)) % n) + n) % n;
      if (i !== s.li) { s.li = i; onActive?.(i); }
    };
    loop();
    return () => cancelAnimationFrame(raf);
  }, [RR, n, motion, active, onActive]);

  return (
    <div
      className="stg" ref={stage} data-c="DRAG"
      onPointerDown={(e) => { const s = st.current; s.drag = true; s.lx = e.clientX; s.mv = 0; }}
      onPointerMove={(e) => { const s = st.current; if (!s.drag) return; const dx = e.clientX - s.lx; s.lx = e.clientX; s.mv += Math.abs(dx); s.rot += dx * 0.4; s.vel = dx * 0.4; }}
      onClick={(e) => { const c = e.target.closest('.rc'); if (c && st.current.mv < 6) onOpen?.(+c.dataset.i); }}
    >
      <div id="r3" ref={ring} style={{ '--cw': `${W}px` }}>
        {items.map((it, i) => (
          <div key={it.id ?? i} className={`rc ${dim?.(it) ? 'dim' : ''}`} data-i={i} style={{ transform: `rotateY(${(i * 360) / n}deg) translateZ(${RR}px)` }}>{renderCard(it, i)}</div>
        ))}
      </div>
    </div>
  );
}
