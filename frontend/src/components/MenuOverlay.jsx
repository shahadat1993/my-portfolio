import TLink from './TLink';
import Clock from './Clock';
import { useUI } from '../context/UI';
import { useSite } from '../context/Site';

const ITEMS = [['/', 'Home'], ['/about', 'About'], ['/projects', 'Projects'], ['/blogs', 'Blogs'], ['/contact', 'Contact']];

/** Full-screen numbered menu (mobile + "Menu" button). */
export default function MenuOverlay() {
  const { menu } = useUI();
  const { settings: s } = useSite();
  return (
    <div id="menu" className={menu ? 'on' : ''}>
      <div className="mn">{ITEMS.map(([to, t], i) => <TLink key={to} to={to}><i>0{i + 1}</i>{t}</TLink>)}</div>
      <div className="mi">
        <small className="mono">(Contact)</small><a href={`mailto:${s.public_email}`}>{s.public_email}</a>
        <small className="mono">(Local time)</small><Clock as="a" />
        <small className="mono">(Social)</small>
        {s.github_url && <a href={s.github_url} target="_blank" rel="noreferrer">GitHub ↗</a>}
        {s.linkedin_url && <a href={s.linkedin_url} target="_blank" rel="noreferrer">LinkedIn ↗</a>}
      </div>
    </div>
  );
}
