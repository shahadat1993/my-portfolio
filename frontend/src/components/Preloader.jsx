import { useEffect, useRef, useState } from 'react';
import { gsap } from '../lib/gsap';
import { usePrefs } from '../context/Prefs';
import { useSite } from '../context/Site';

/** Dark intro screen with a 000→100 counter, then slides up and drops the navbar in. */
export default function Preloader() {
  const { motion } = usePrefs();
  const { settings } = useSite();
  const [done, setDone] = useState(false);
  const root = useRef(null), cnt = useRef(null);

  useEffect(() => {
    const d = document.documentElement.dataset;
    if (!motion) { setDone(true); return undefined; }
    d.delay = '1.9';
    const o = { v: 0 };
    const tl = gsap.timeline()
      .to(o, { v: 100, duration: 1.1, ease: 'power2.inOut', onUpdate: () => { if (cnt.current) cnt.current.textContent = String(Math.round(o.v)).padStart(3, '0'); } })
      .to(root.current, { yPercent: -100, duration: 1, ease: 'power4.inOut', onComplete: () => { delete d.delay; setDone(true); } })
      .from('#nv', { yPercent: -160, duration: 0.8, ease: 'power3.out', clearProps: 'transform' }, '-=.5');
    return () => { tl.kill(); gsap.set('#nv', { clearProps: 'transform' }); delete d.delay; };
  }, [motion]);

  if (done) return null;
  return (
    <div id="pre" ref={root}>
      <span>{settings.hero_name}.</span>
      <small ref={cnt}>000</small>
    </div>
  );
}
