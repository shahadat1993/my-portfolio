import { useLayoutEffect, useRef } from 'react';
import { gsap, ScrollTrigger } from '../lib/gsap';
import { usePrefs } from '../context/Prefs';

/**
 * const ref = useGsap((root) => { gsap.from('.x', {...}) }, [deps]);
 * Runs your animation code scoped to ref's element, only when Motion is ON, and cleans everything up on unmount.
 */
export function useGsap(build, deps = []) {
  const ref = useRef(null);
  const { motion } = usePrefs();
  useLayoutEffect(() => {
    if (!motion || !ref.current) return undefined;
    const ctx = gsap.context(() => build(ref.current), ref);
    const t = setTimeout(() => ScrollTrigger.refresh(), 300);
    return () => { clearTimeout(t); ctx.revert(); };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [motion, ...deps]);
  return ref;
}

/** Reveal for elements with class "rv" (fade + rise + slight 3D tilt) when they scroll into view. */
export function revealRv(root) {
  const els = root.querySelectorAll('.rv');
  if (!els.length) return;
  gsap.set(els, { opacity: 0, y: 60, rotationX: 10, transformPerspective: 1000, transformOrigin: '50% 0%' });
  ScrollTrigger.batch(els, {
    start: 'top 94%', once: true,
    onEnter: (b) => gsap.to(b, { opacity: 1, y: 0, rotationX: 0, duration: 1, stagger: 0.1, ease: 'power3.out', clearProps: 'transform' }),
  });
}

/** Delay for page intros: longer while the preloader / page curtain is covering the screen. */
export const introDelay = () => parseFloat(document.documentElement.dataset.delay || '0.15');

/** 3D letter-by-letter flip of the page title (needs <SplitChars> inside .pt h1). */
export function flipTitle(root) {
  const c = root.querySelectorAll('.pt h1 .c3');
  if (c.length) gsap.from(c, { rotationX: -100, yPercent: 45, opacity: 0, transformOrigin: '50% 100% -40px', transformPerspective: 900, stagger: 0.03, duration: 1, ease: 'power4.out', delay: introDelay(), clearProps: 'transform,opacity' });
}

export function scrubWords(root) {
  root.querySelectorAll('.ws').forEach((el) =>
    gsap.to(el.querySelectorAll('.w'), { opacity: 1, stagger: 0.1, ease: 'none', scrollTrigger: { trigger: el, start: 'top 82%', end: 'bottom 45%', scrub: true } }));
}

export function countUp(root) {
  root.querySelectorAll('[data-n]').forEach((e) => {
    const c = { v: 0 };
    gsap.to(c, { v: +e.dataset.n, duration: 2, ease: 'power3.out', onUpdate: () => { e.textContent = Math.round(c.v); }, scrollTrigger: { trigger: e, start: 'top 95%', once: true } });
  });
}
