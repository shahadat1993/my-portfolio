import { createContext, useCallback, useContext, useEffect, useRef } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { gsap, ScrollTrigger } from '../lib/gsap';
import { usePrefs } from './Prefs';
import { useScroll } from './Smooth';
import { useUI } from './UI';
import { useSite } from './Site';

const Ctx = createContext(null);
/** go('/projects') — navigates with the 3D curtain animation (or instantly when Motion is off). */
export const useGo = () => useContext(Ctx);

const label = (to) => {
  const s = to.split('?')[0].split('/').filter(Boolean);
  if (!s.length) return 'Home';
  if (s[0] === 'projects' && s[1]) return 'Case study';
  if (s[0] === 'blogs' && s[1]) return 'Article';
  return s[0];
};

export function TransitionProvider({ children }) {
  const navigate = useNavigate();
  const loc = useLocation();
  const { motion } = usePrefs();
  const scroll = useScroll();
  const ui = useUI();
  const { settings } = useSite();
  const ref = useRef(null);
  const busy = useRef(false);
  const path = useRef(loc.pathname);
  path.current = loc.pathname;

  // every route change: back to top + let ScrollTrigger re-measure
  useEffect(() => {
    scroll.toTop(true);
    const t = setTimeout(() => ScrollTrigger.refresh(), 120);
    return () => clearTimeout(t);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [loc.pathname]);

  const go = useCallback((to) => {
    ui.closeAll();
    if (to === path.current) { scroll.toTop(); return; }
    if (!motion) { navigate(to); return; }
    if (busy.current) return;
    busy.current = true;
    const root = ref.current, cols = root.querySelectorAll('.cvs i'), tx = root.querySelector('#cvn');
    tx.textContent = label(to);
    root.classList.add('on');
    const d = document.documentElement.dataset;
    const tl = gsap.timeline({ onComplete: () => { root.classList.remove('on'); busy.current = false; } });
    tl.set(cols, { rotationX: -92, transformOrigin: '50% 0%' })
      .set(tx, { rotationX: 80, y: 70, opacity: 0, transformPerspective: 900 })
      .to(cols, { rotationX: 0, duration: 0.7, ease: 'power3.inOut', stagger: { each: 0.06, from: 'center' } })
      .to(tx, { rotationX: 0, y: 0, opacity: 1, duration: 0.7, ease: 'power4.out' }, '-=.4')
      .add(() => { d.delay = '1.0'; navigate(to); setTimeout(() => { delete d.delay; }, 1600); })
      .to({}, { duration: 0.3 })
      .to(tx, { rotationX: -80, y: -70, opacity: 0, duration: 0.5, ease: 'power3.in' })
      .set(cols, { transformOrigin: '50% 100%' })
      .to(cols, { rotationX: 92, duration: 0.7, ease: 'power3.inOut', stagger: { each: 0.06, from: 'edges' } }, '-=.3');
    setTimeout(() => { busy.current = false; root.classList.remove('on'); }, 5000);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [motion, navigate]);

  return (
    <Ctx.Provider value={go}>
      {children}
      <div id="cv" ref={ref} aria-hidden="true">
        <div className="cvs">{[0, 1, 2, 3, 4, 5, 6].map((i) => <i key={i} />)}</div>
        <div className="cvt"><div><span id="cvn" /><small className="mono">{settings.site_name}</small></div></div>
      </div>
    </Ctx.Provider>
  );
}
