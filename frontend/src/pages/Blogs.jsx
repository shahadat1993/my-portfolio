import { useState } from 'react';
import TLink from '../components/TLink';
import Ring3D from '../components/Ring3D';
import { Chips, CtaBand, K, Thumb } from '../components/Bits';
import { SplitChars } from '../lib/utils';
import { post } from '../api';
import { gsap } from '../lib/gsap';
import { useGsap, flipTitle, revealRv } from '../hooks/useGsap';
import { useApi, useTitle } from '../hooks/useApi';
import { useGo } from '../context/Transition';
import { useSite } from '../context/Site';
import { usePrefs } from '../context/Prefs';

export default function Blogs() {
  const { settings: s, categories } = useSite();
  const { motion } = usePrefs();
  const go = useGo();
  const { data } = useApi('/posts');
  const [cat, setCat] = useState('all'), [q, setQ] = useState(''), [idx, setIdx] = useState(0);
  const [email, setEmail] = useState(''), [sub, setSub] = useState('');
  useTitle(`Blogs — ${s.site_name}`);
  const items = data || [];
  const match = (p) => (cat === 'all' || p.category === cat) && (!q || `${p.title} ${p.excerpt} ${p.tags.join(' ')}`.toLowerCase().includes(q.toLowerCase()));
  const cur = items[idx] || items[0];
  const featured = items.find((p) => p.featured) || items[0];
  const rest = items.filter((p) => p !== featured);

  const ref = useGsap((root) => {
    flipTitle(root); revealRv(root);
    gsap.from('.stg', { rotationX: 25, scale: 0.7, opacity: 0, transformPerspective: 1000, duration: 1.4, ease: 'power4.out', delay: 0.3, clearProps: 'transform,opacity' });
  }, [items.length]);

  const subscribe = async (e) => {
    e.preventDefault();
    try { const r = await post('/subscribe', { email }); setSub(r.message); setEmail(''); } catch (err) { setSub(err.message); }
  };

  return (
    <div className="pg" ref={ref}>
      <section className="pt">
        <span className="mono">(Blogs) · 3D reading list</span>
        <h1><SplitChars>Notes &amp; <em>writing.</em></SplitChars></h1>
        <p className="lead rv">Short, practical notes on Laravel, React, motion and shipping products. Drag the ring, search, or open an article.</p>
        <div className="ctl rv">
          <label className="sf"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="7" /><path d="M20 20l-4-4" /></svg><input value={q} onChange={(e) => setQ(e.target.value)} placeholder="Search articles…" /></label>
          <div className="fl">{[{ slug: 'all', name: 'All' }, ...categories.blog].map((c) => <button key={c.slug} className={cat === c.slug ? 'on' : ''} onClick={() => setCat(c.slug)}>{c.name}</button>)}</div>
        </div>
        {items.length > 0 && (
          <>
            <Ring3D items={items} motion={motion} dim={(p) => !match(p)} onActive={setIdx} onOpen={(i) => go(`/blogs/${items[i].slug}`)}
              renderCard={(p, i) => <><Thumb src={p.cover} code={`0${i + 1}`} /><p>{p.title.length > 46 ? `${p.title.slice(0, 44)}…` : p.title}</p><small className="mono">{p.category_name}</small></>} />
            {cur && (
              <div className="cp">
                <small className="mono">(0{idx + 1}) {cur.category_name} · {cur.minutes} min</small><h3>{cur.title}</h3><p>{cur.excerpt}</p>
                <div className="tg2"><span>{cur.date}</span></div>
                <TLink className="cta mag" to={`/blogs/${cur.slug}`} data-c="READ">Read article ↗</TLink>
              </div>
            )}
          </>
        )}
        {data && !items.length && <p className="lead">No articles yet — write one in the admin dashboard.</p>}
      </section>

      {featured && (
        <section>
          <K n="01" left="Featured" right="Start here" />
          <TLink className={`bf tilt rv ${match(featured) ? '' : 'dim'}`} data-s="1" data-k=".3" to={`/blogs/${featured.slug}`}>
            <h3>{featured.title}</h3><p>{featured.category_name?.toUpperCase()} · {featured.minutes} MIN READ · {featured.date.toUpperCase()}</p>
          </TLink>
        </section>
      )}
      <section>
        <K n="02" left="All articles" right="Newest first" />
        {rest.map((p) => (
          <TLink key={p.slug} className={`bl rv ${match(p) ? '' : 'dim'}`} to={`/blogs/${p.slug}`}>
            <small>{p.date.toUpperCase()}</small><h3>{p.title}</h3><small>{p.category_name?.toUpperCase()} · {p.minutes} MIN</small>
          </TLink>
        ))}
      </section>
      <section><K n="03" left="Topics" right="Browse by subject" /><Chips items={categories.blog.map((c) => c.name)} /></section>
      <section>
        <K n="04" left="Newsletter" right="No spam, ever" />
        <h2 className="rv">New notes, <em>straight to your inbox.</em></h2>
        <form className="nl rv" onSubmit={subscribe}>
          <input type="email" required value={email} onChange={(e) => setEmail(e.target.value)} placeholder="Your email address" />
          <button className="cta mag" data-c="JOIN">Subscribe ↗</button>
        </form>
        {sub && <p className="lead">{sub}</p>}
      </section>
      <CtaBand />
    </div>
  );
}
