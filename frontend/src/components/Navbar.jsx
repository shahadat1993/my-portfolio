import { useEffect, useLayoutEffect, useRef, useState } from 'react';
import { useLocation } from 'react-router-dom';
import TLink from './TLink';
import { useUI } from '../context/UI';
import { usePrefs } from '../context/Prefs';
import { useSite } from '../context/Site';

const LINKS = [['/', 'Home'], ['/about', 'About'], ['/projects', 'Projects'], ['/blogs', 'Blogs'], ['/contact', 'Contact']];
const isActive = (p, to) => (to === '/' ? p === '/' : p.startsWith(to));

const Roll = ({ t }) => <b className="rl" style={{ fontWeight: 400 }}><i data-t={t}>{t}</i></b>;

/** Floating glass navbar: sliding highlight, rolling text, hide-on-scroll-down, search / theme / settings / menu. */
export default function Navbar() {
  const { pathname } = useLocation();
  const ui = useUI();
  const { toggleTheme } = usePrefs();
  const { settings } = useSite();
  const nl = useRef(null), ind = useRef(null);
  const [hidden, setHidden] = useState(false), [small, setSmall] = useState(false);

  const moveTo = (el) => {
    const i = ind.current; if (!i) return;
    if (!el) { i.style.opacity = 0; return; }
    i.style.opacity = 1; i.style.width = `${el.offsetWidth}px`; i.style.transform = `translateX(${el.offsetLeft}px)`;
  };
  const activeEl = () => nl.current?.querySelector('a.on');
  useLayoutEffect(() => { moveTo(activeEl()); const f = () => moveTo(activeEl()); addEventListener('resize', f); return () => removeEventListener('resize', f); }, [pathname]);

  useEffect(() => {
    let last = 0;
    const f = () => {
      const y = scrollY;
      setSmall(y > 80);
      if (!document.getElementById('menu')?.classList.contains('on')) {
        if (y > last + 6 && y > 240) setHidden(true); else if (y < last - 6) setHidden(false);
      }
      last = y;
    };
    addEventListener('scroll', f, { passive: true });
    return () => removeEventListener('scroll', f);
  }, []);

  return (
    <header id="nv" className={`${hidden && !ui.search ? 'h' : ''} ${small ? 'sm' : ''}`}>
      <div className="bar">
        <TLink className="logo" to="/">{settings.hero_name}<em>{settings.hero_accent}</em></TLink>
        <nav className="links" id="nl" ref={nl} onMouseLeave={() => moveTo(activeEl())}>
          <span className="ind" ref={ind} />
          {LINKS.map(([to, t]) => (
            <TLink key={to} to={to} className={isActive(pathname, to) ? 'on' : ''} onMouseEnter={(e) => moveTo(e.currentTarget)}><Roll t={t} /></TLink>
          ))}
        </nav>
        <div className="tools">
          <button className="ib sbn" onClick={() => ui.setSearch(true)} aria-label="Search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="7" /><path d="M20 20l-4-4" /></svg>
            <span className="sx">Search</span><kbd className="sx">⌘K</kbd>
          </button>
          <button className="ib" onClick={toggleTheme} aria-label="Toggle theme">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" strokeWidth="2" /><path d="M12 3.5a8.5 8.5 0 0 1 0 17z" fill="currentColor" /></svg>
          </button>
          <button className="ib" onClick={() => ui.setSettings(true)} aria-label="Settings">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z" /></svg>
          </button>
          <TLink className="lt" to="/contact"><Roll t="Let’s talk" /><b>↗</b></TLink>
          <button className={`ib ${ui.menu ? 'on' : ''}`} id="mb" onClick={() => ui.setMenu(!ui.menu)} aria-label="Menu"><span className="bg3"><i /><i /></span></button>
        </div>
      </div>
    </header>
  );
}
