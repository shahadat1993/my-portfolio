import { createContext, useContext, useEffect, useRef } from 'react';
import Lenis from 'lenis';
import { gsap, ScrollTrigger } from '../lib/gsap';
import { usePrefs } from './Prefs';

const Ctx = createContext(null);
export const useScroll = () => useContext(Ctx);

/** Lenis smooth scrolling, wired into GSAP's ticker so ScrollTrigger stays in sync. */
export function SmoothProvider({ children }) {
  const { motion, prefs } = usePrefs();
  const lenis = useRef(null);

  useEffect(() => {
    if (!(motion && prefs.smooth)) return undefined;
    const l = new Lenis({ lerp: 0.09 });
    lenis.current = l;
    l.on('scroll', ScrollTrigger.update);
    const tick = (t) => l.raf(t * 1000);
    gsap.ticker.add(tick);
    gsap.ticker.lagSmoothing(0);
    return () => { gsap.ticker.remove(tick); l.destroy(); lenis.current = null; };
  }, [motion, prefs.smooth]);

  const api = {
    toTop: (immediate = false) => (lenis.current ? lenis.current.scrollTo(0, { immediate, duration: 1.4 }) : window.scrollTo({ top: 0, behavior: immediate ? 'auto' : 'smooth' })),
    to: (target, offset = -90) => (lenis.current ? lenis.current.scrollTo(target, { offset }) : target?.scrollIntoView?.({ behavior: 'smooth' })),
    y: (y) => (lenis.current ? lenis.current.scrollTo(y) : window.scrollTo({ top: y, behavior: 'smooth' })),
  };
  return <Ctx.Provider value={api}>{children}</Ctx.Provider>;
}
