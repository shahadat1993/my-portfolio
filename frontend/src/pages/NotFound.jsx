import TLink from '../components/TLink';

export default function NotFound() {
  return (
    <div className="pg">
      <section className="pt" style={{ minHeight: '80vh' }}>
        <span className="mono">(404)</span>
        <h1>Lost in <em>space.</em></h1>
        <p className="lead">The page you’re looking for doesn’t exist.</p><br />
        <TLink className="cta" to="/">Back home ↗</TLink>
      </section>
    </div>
  );
}
