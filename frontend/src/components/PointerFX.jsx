import { useEffect, useRef } from 'react';
import { usePrefs } from '../context/Prefs';

/**
 * Custom cursor, project-row preview bubble, 3D tilt cards (.tilt) and magnetic buttons (.mag).
 * It listens on the whole document, so any element with those classes works, even ones created later.
 */
export default function PointerFX() {
  const { motion, prefs } = usePrefs();
  const cur = useRef(null), pv = useRef(null);
  const fine = typeof window !== 'undefined' && window.matchMedia('(pointer:fine)').matches;

  useEffect(() => {
    if (!motion) return undefined;
    let mx = -100, my = -100, cx = -100, cy = -100, raf, lastT = null, lastM = null;

    const tiltMove = (el, e) => {
      const cd = el.querySelector('.cd'), r = el.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5, y = (e.clientY - r.top) / r.height - 0.5, k = +el.dataset.k || 1;
      el.classList.add('mv');
      if (cd) {
        cd.style.transform = `rotateY(${x * 22 * k}deg) rotateX(${-y * 22 * k}deg)`;
        cd.style.setProperty('--mx', `${(x + 0.5) * 100}%`); cd.style.setProperty('--my', `${(y + 0.5) * 100}%`);
        el.querySelectorAll('.ly').forEach((l) => { l.style.transform = `translate(${x * l.dataset.d}px,${y * l.dataset.d}px)`; });
      } else el.style.transform = `perspective(900px) rotateY(${x * 14 * k}deg) rotateX(${-y * 14 * k}deg)`;
    };
    const tiltReset = (el) => {
      el.classList.remove('mv');
      const cd = el.querySelector('.cd');
      if (cd) { cd.style.transform = ''; el.querySelectorAll('.ly').forEach((l) => { l.style.transform = ''; }); } else el.style.transform = '';
    };
    const move = (e) => {
      mx = e.clientX; my = e.clientY;
      if (pv.current?.classList.contains('on')) pv.current.style.transform = `translate(${e.clientX}px,${e.clientY}px)`;
      const t = e.target.closest?.('.tilt');
      if (lastT && lastT !== t) tiltReset(lastT);
      if (t) tiltMove(t, e);
      lastT = t || null;
      const m = e.target.closest?.('.mag');
      if (lastM && lastM !== m) lastM.style.transform = '';
      if (m) { const r = m.getBoundingClientRect(); m.style.transform = `translate(${(e.clientX - r.left - r.width / 2) * 0.3}px,${(e.clientY - r.top - r.height / 2) * 0.3}px)`; }
      lastM = m || null;
    };
    const over = (e) => {
      const c = cur.current; if (!c) return;
      const d = e.target.closest?.('[data-c]');
      if (d) { c.textContent = d.dataset.c; c.style.cssText += ';width:74px;height:74px;margin:-37px 0 0 -37px'; }
      else if (c.textContent) { c.textContent = ''; c.style.width = c.style.height = '14px'; c.style.margin = '-7px 0 0 -7px'; }
      const r = e.target.closest?.('.pr');
      pv.current?.classList.toggle('on', !!r);
      if (r && pv.current) pv.current.textContent = r.dataset.p;
    };
    const loop = () => { raf = requestAnimationFrame(loop); cx += (mx - cx) * 0.22; cy += (my - cy) * 0.22; if (cur.current) cur.current.style.transform = `translate(${cx}px,${cy}px)`; };
    addEventListener('mousemove', move); document.addEventListener('mouseover', over); loop();
    return () => { removeEventListener('mousemove', move); document.removeEventListener('mouseover', over); cancelAnimationFrame(raf); };
  }, [motion]);

  return <><div className="cur" id="cur" ref={cur} style={{ display: fine && prefs.cursor && motion ? undefined : 'none' }} /><div id="pv" ref={pv} /></>;
}
