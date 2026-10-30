import { useRef } from 'react';
import { useUI } from '../context/UI';
import { usePrefs } from '../context/Prefs';
import { useSite } from '../context/Site';

/** Right-hand drawer: theme, accent colours (managed in admin → Theme colours), font, motion, cursor, sound. */
export default function SettingsPanel() {
  const { settings, setSettings } = useUI();
  const { prefs, set, reset } = usePrefs();
  const { colors } = useSite();
  const custom = useRef(null);
  const Seg = ({ k, opts }) => (
    <div className="seg">{opts.map(([v, l]) => <button key={v} className={prefs[k] === v ? 'on' : ''} onClick={() => set(k, v)}>{l}</button>)}</div>
  );
  const Tg = ({ k, label }) => (
    <button className={`tg ${prefs[k] ? 'on' : ''}`} onClick={() => set(k, !prefs[k])}>{label} <b /></button>
  );
  return (
    <aside id="set" className={settings ? 'on' : ''}>
      <h3>Settings <button className="ib" onClick={() => setSettings(false)} aria-label="Close">✕</button></h3>
      <div className="g"><label className="mono">Theme</label><Seg k="theme" opts={[['light', 'Light'], ['dark', 'Dark'], ['auto', 'Auto']]} /></div>
      <div className="g"><label className="mono">Accent colour</label>
        <div className="sw">
          {colors.map((c) => <i key={c.hex} title={c.name} style={{ background: c.hex }} className={prefs.accent.toLowerCase() === c.hex.toLowerCase() ? 'on' : ''} onClick={() => set('accent', c.hex)} />)}
          <input ref={custom} type="color" value={prefs.accent} onChange={(e) => set('accent', e.target.value)} aria-label="Custom colour" />
        </div>
      </div>
      <div className="g"><label className="mono">Heading font</label><Seg k="font" opts={[['serif', 'Serif'], ['sans', 'Sans']]} /></div>
      <div className="g"><label className="mono">Experience</label>
        <Tg k="motion" label="Motion & animations" /><Tg k="smooth" label="Smooth scroll" /><Tg k="cursor" label="Custom cursor" /><Tg k="sound" label="Click-clack sound" />
      </div>
      <button className="rs" onClick={reset}>Reset to default</button>
    </aside>
  );
}
