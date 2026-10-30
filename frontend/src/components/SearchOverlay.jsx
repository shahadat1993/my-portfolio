import { useEffect, useRef, useState } from 'react';
import TLink from './TLink';
import { api } from '../api';
import { useUI } from '../context/UI';

const PAGES = [['/', 'Home'], ['/about', 'About'], ['/projects', 'Projects'], ['/blogs', 'Blogs'], ['/contact', 'Contact']];

/** ⌘K / "/" live search. Calls GET /api/search?q= on every pause in typing; image shown on the right. */
export default function SearchOverlay() {
  const { search, setSearch } = useUI();
  const [q, setQ] = useState('');
  const [res, setRes] = useState([]);
  const [sel, setSel] = useState(0);
  const [loading, setLoading] = useState(false);
  const input = useRef(null);

  useEffect(() => {
    const k = (e) => {
      const t = e.target.tagName;
      if ((e.key === 'k' && (e.metaKey || e.ctrlKey)) || (e.key === '/' && t !== 'INPUT' && t !== 'TEXTAREA')) { e.preventDefault(); setSearch(true); }
      else if (e.key === 'Escape') setSearch(false);
    };
    addEventListener('keydown', k);
    return () => removeEventListener('keydown', k);
  }, [setSearch]);

  useEffect(() => { if (search) { setQ(''); setRes([]); setTimeout(() => input.current?.focus(), 250); } }, [search]);

  useEffect(() => {
    if (q.trim().length < 2) { setRes([]); return undefined; }
    setLoading(true);
    const ctl = new AbortController();
    const t = setTimeout(() => {
      api(`/search?q=${encodeURIComponent(q.trim())}`, { signal: ctl.signal })
        .then((j) => { setRes(j.results); setSel(0); setLoading(false); }).catch(() => {});
    }, 180);
    return () => { clearTimeout(t); ctl.abort(); };
  }, [q]);

  const list = q.trim().length < 2 ? PAGES.map(([url, title]) => ({ url, title, type: 'page' })) : res;
  const onKey = (e) => {
    if (e.key === 'ArrowDown') { e.preventDefault(); setSel((s) => (s + 1) % Math.max(list.length, 1)); }
    if (e.key === 'ArrowUp') { e.preventDefault(); setSel((s) => (s - 1 + list.length) % Math.max(list.length, 1)); }
    if (e.key === 'Enter' && list[sel]) { document.getElementById(`ri-${sel}`)?.click(); }
  };

  return (
    <div id="sr" className={search ? 'on' : ''} onClick={(e) => e.target.id === 'sr' && setSearch(false)}>
      <div className="sbx">
        <input id="si" ref={input} value={q} onChange={(e) => setQ(e.target.value)} onKeyDown={onKey} placeholder="Search projects, articles, pages…" autoComplete="off" />
        <button className="ib" onClick={() => setSearch(false)} aria-label="Close">✕</button>
      </div>
      <div id="sl">
        {q.trim().length >= 2 && !loading && !res.length && <p className="no">No results for “{q}”. Try “laravel”, “react” or “gsap”.</p>}
        {list.map((r, i) => (
          <TLink key={r.url} id={`ri-${i}`} to={r.url} className={`ri ${i === sel ? 'on' : ''}`} onMouseEnter={() => setSel(i)}>
            <small className="mono">{r.type}</small>
            <span className="t"><span style={{ font: '400 1.7rem var(--sf)' }}>{r.title}</span>{r.excerpt && <em>{r.excerpt}</em>}</span>
            {r.image ? <img className="rim" src={r.image} alt="" loading="lazy" /> : <i>↗</i>}
          </TLink>
        ))}
      </div>
    </div>
  );
}
