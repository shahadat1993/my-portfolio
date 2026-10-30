import { useEffect, useRef, useState } from 'react';
import { useScroll } from '../context/Smooth';
import { usePrefs } from '../context/Prefs';

/** Back-to-top arrow with scroll-progress ring + the MOTION ON/OFF pill + top progress bar. */
export default function FloatingButtons() {
  const scroll = useScroll();
  const { prefs, set, motion } = usePrefs();
  const [show, setShow] = useState(false);
  const ring = useRef(null), bar = useRef(null);

  useEffect(() => {
    const f = () => {
      const h = document.documentElement.scrollHeight - innerHeight, p = h > 0 ? scrollY / h : 0;
      if (ring.current) ring.current.style.strokeDashoffset = 170 - 170 * p;
      if (bar.current) bar.current.style.transform = `scaleX(${p})`;
      setShow(scrollY > 400);
    };
    f(); addEventListener('scroll', f, { passive: true }); addEventListener('resize', f);
    return () => { removeEventListener('scroll', f); removeEventListener('resize', f); };
  }, []);

  return (
    <>
      <div id="bar" ref={bar} />
      <button id="top" className={show ? 'on' : ''} onClick={() => scroll.toTop()} aria-label="Back to top">
        <svg viewBox="0 0 60 60"><circle cx="30" cy="30" r="27" fill="none" stroke="var(--line)" strokeWidth="2" /><circle ref={ring} cx="30" cy="30" r="27" fill="none" stroke="var(--a)" strokeWidth="3" strokeLinecap="round" strokeDasharray="170" strokeDashoffset="170" /></svg>
        <span>↑</span>
      </button>
      <button id="mo" className={prefs.motion ? '' : 'off'} onClick={() => set('motion', !prefs.motion)}>
        <i /><span className="mono">{motion ? 'Motion on' : 'Motion off'}</span>
      </button>
    </>
  );
}
