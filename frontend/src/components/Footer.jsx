import { useSite } from '../context/Site';

export default function Footer() {
  const { settings: s } = useSite();
  const social = [['github_url', 'GitHub'], ['linkedin_url', 'LinkedIn'], ['facebook_url', 'Facebook'], ['twitter_url', 'X'], ['instagram_url', 'Instagram'], ['youtube_url', 'YouTube']].filter(([k]) => s[k]);
  return (
    <footer>
      <div className="fb">{s.hero_name}<i>.</i></div>
      <div className="ft mono">
        <span>© {new Date().getFullYear()} {s.site_name}</span>
        <span>{s.footer_text}</span>
        <span>{social.map(([k, l], i) => <span key={k}>{i ? ' · ' : ''}<a href={s[k]} target="_blank" rel="noreferrer">{l}</a></span>)}</span>
      </div>
    </footer>
  );
}
