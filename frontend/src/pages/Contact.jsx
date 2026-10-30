import { useState } from 'react';
import Clock from '../components/Clock';
import { Cards, CtaBand, ENGAGE, Faqs, K, STEPS } from '../components/Bits';
import { SplitChars } from '../lib/utils';
import { post } from '../api';
import { useGsap, flipTitle, revealRv } from '../hooks/useGsap';
import { useTitle } from '../hooks/useApi';
import { useSite } from '../context/Site';

const TYPES = ['Web app', 'Mobile app', 'Admin panel', 'Animation / UI'];
const BUDGETS = ['Under $500', '$500 – $2k', '$2k – $5k', '$5k+'];

export default function Contact() {
  const { settings: s, faqs } = useSite();
  const [f, setF] = useState({ name: '', email: '', message: '', project_type: TYPES[0], budget: BUDGETS[1], website: '' });
  const [state, setState] = useState({ busy: false, ok: null, msg: '' });
  useTitle(`Contact — ${s.site_name}`);
  const up = (k) => (e) => setF({ ...f, [k]: e.target.value });

  const ref = useGsap((root) => { flipTitle(root); revealRv(root); }, []);

  const submit = async (e) => {
    e.preventDefault();
    setState({ busy: true, ok: null, msg: '' });
    try {
      const r = await post('/contact', f);
      setState({ busy: false, ok: true, msg: r.message || 'Message sent!' });
      setF({ ...f, name: '', email: '', message: '' });
    } catch (err) {
      const first = err.errors ? Object.values(err.errors)[0][0] : err.message;
      setState({ busy: false, ok: false, msg: first });
    }
  };
  const Pick = ({ k, list }) => <div className="chips sel">{list.map((v) => <button type="button" key={v} className={f[k] === v ? 'on' : ''} onClick={() => setF({ ...f, [k]: v })}>{v}</button>)}</div>;

  return (
    <div className="pg" ref={ref}>
      <section className="pt">
        <span className="mono">(Contact)</span>
        <h1><SplitChars>Let’s build <em>together.</em></SplitChars></h1>
        <p className="lead rv">Tell me about your idea. I reply within one working day — usually sooner.</p>
        <div className="cg">
          <form className="fm" onSubmit={submit}>
            <input className="rv" placeholder="Your name" required value={f.name} onChange={up('name')} />
            <input className="rv" type="email" placeholder="Email address" required value={f.email} onChange={up('email')} />
            <div className="grp rv"><small className="mono">Project type</small><Pick k="project_type" list={TYPES} /></div>
            <div className="grp rv"><small className="mono">Budget</small><Pick k="budget" list={BUDGETS} /></div>
            <textarea className="rv" rows="5" placeholder="Tell me about your project" required minLength={10} value={f.message} onChange={up('message')} />
            <input className="hp" tabIndex={-1} autoComplete="off" aria-hidden="true" value={f.website} onChange={up('website')} placeholder="website" />
            <button className="cta mag rv" style={{ justifySelf: 'start' }} data-c="SEND" disabled={state.busy}>{state.busy ? 'Sending…' : 'Send message ↗'}</button>
            {state.ok !== null && <div className={`form-msg ${state.ok ? 'ok' : 'bad'}`}>{state.msg}</div>}
          </form>
          <div className="ci rv">
            <div><small className="mono">(Local time · {s.location.split(',')[0]})</small><Clock /></div>
            <a href={`mailto:${s.public_email}`}><small className="mono">(Email)</small>{s.public_email}</a>
            {s.phone && <a href={`tel:${s.phone}`}><small className="mono">(Phone / WhatsApp)</small>{s.phone}</a>}
            {s.github_url && <a href={s.github_url} target="_blank" rel="noreferrer"><small className="mono">(GitHub)</small>{s.github_url.replace('https://github.com/', '')} ↗</a>}
            {s.linkedin_url && <a href={s.linkedin_url} target="_blank" rel="noreferrer"><small className="mono">(LinkedIn)</small>Profile ↗</a>}
            <div><small className="mono">(Availability)</small>{s.availability_text}</div>
          </div>
        </div>
      </section>
      <section><K n="01" left="What happens next" right="Four simple steps" /><Cards items={STEPS} /></section>
      <section><K n="02" left="Ways to work together" right="Pick what fits" /><Cards items={ENGAGE} className="eng" /></section>
      <section><K n="03" left="Questions" right="Before you write" /><Faqs items={faqs.filter((q) => q.scope === 'contact')} /></section>
      <CtaBand />
    </div>
  );
}
