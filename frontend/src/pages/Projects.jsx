import { useState } from 'react';
import TLink from '../components/TLink';
import Ring3D from '../components/Ring3D';
import { Cards, Chips, CtaBand, K, PROCESS, Thumb } from '../components/Bits';
import { SplitChars } from '../lib/utils';
import { gsap } from '../lib/gsap';
import { useGsap, flipTitle, revealRv } from '../hooks/useGsap';
import { useApi, useTitle } from '../hooks/useApi';
import { useGo } from '../context/Transition';
import { useSite } from '../context/Site';
import { usePrefs } from '../context/Prefs';

const TECH = ['Laravel', 'React', 'React Native', 'Expo', 'MySQL', 'REST APIs', 'Sanctum', 'Spatie', 'Tailwind', 'GSAP', 'Vite', 'Git'];

export default function Projects() {
  const { settings: s, categories } = useSite();
  const { motion } = usePrefs();
  const go = useGo();
  const { data } = useApi('/projects');
  const [cat, setCat] = useState('all'), [q, setQ] = useState(''), [view, setView] = useState('3d'), [idx, setIdx] = useState(0);
  useTitle(`Projects — ${s.site_name}`);
  const items = data || [];
  const match = (p) => (cat === 'all' || p.category === cat) && (!q || `${p.title} ${p.summary} ${p.tags.join(' ')}`.toLowerCase().includes(q.toLowerCase()));
  const cur = items[idx] || items[0];

  const ref = useGsap((root) => {
    flipTitle(root); revealRv(root);
    gsap.from('.stg', { rotationX: 25, scale: 0.7, opacity: 0, transformPerspective: 1000, duration: 1.4, ease: 'power4.out', delay: 0.3, clearProps: 'transform,opacity' });
  }, [items.length]);

  return (
    <div className="pg" ref={ref}>
      <section className="pt">
        <span className="mono">(Projects) · 3D experience</span>
        <h1><SplitChars>Selected <em>work.</em></SplitChars></h1>
        <p className="lead rv">Products I’ve designed, built and shipped. Drag the ring, filter by type, search by name — or click a card for the full case study.</p>
        <div className="ctl rv">
          <label className="sf"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="7" /><path d="M20 20l-4-4" /></svg><input value={q} onChange={(e) => setQ(e.target.value)} placeholder="Search projects…" /></label>
          <div className="fl">{[{ slug: 'all', name: 'All' }, ...categories.project].map((c) => <button key={c.slug} className={cat === c.slug ? 'on' : ''} onClick={() => setCat(c.slug)}>{c.name}</button>)}</div>
          <div className="vw"><button className={view === '3d' ? 'on' : ''} onClick={() => setView('3d')}>3D</button><button className={view === 'grid' ? 'on' : ''} onClick={() => setView('grid')}>Grid</button></div>
        </div>

        {view === '3d' && items.length > 0 && (
          <>
            <Ring3D items={items} motion={motion} dim={(p) => !match(p)} onActive={setIdx} onOpen={(i) => go(`/projects/${items[i].slug}`)}
              renderCard={(p) => <><Thumb src={p.cover} code={p.code} /><p>{p.title}</p><small className="mono">{p.category_name}</small></>} />
            {cur && (
              <div className="cp">
                <small className="mono">(0{idx + 1}) {cur.category_name}</small><h3>{cur.title}</h3><p>{cur.summary}</p>
                <div className="tg2">{cur.tags.map((t) => <span key={t}>{t}</span>)}</div>
                <TLink className="cta mag" to={`/projects/${cur.slug}`} data-c="OPEN">View case study ↗</TLink>
              </div>
            )}
          </>
        )}
        {view === 'grid' && (
          <div className="pg2 on">
            {items.map((p) => (
              <TLink key={p.slug} to={`/projects/${p.slug}`} className={`pc tilt ${match(p) ? '' : 'dim'}`} data-s="1" data-k=".5">
                <Thumb src={p.cover} code={p.code} />
                <div className="in"><h3>{p.title}</h3><p>{p.summary}</p><div className="tg2">{p.tags.map((t) => <span key={t}>{t}</span>)}</div></div>
              </TLink>
            ))}
          </div>
        )}
        {data && !items.length && <p className="lead">No projects yet — add one in the admin dashboard.</p>}
      </section>
      <section><K n="01" left="Capabilities" right="What goes into each build" /><Chips items={TECH} /></section>
      <section><K n="02" left="Process" right="How a project runs" /><Cards items={PROCESS} /></section>
      <CtaBand />
    </div>
  );
}
