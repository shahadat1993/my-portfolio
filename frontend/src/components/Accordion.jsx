import { useState } from 'react';

/** Smooth open/close FAQ row. */
export default function Acc({ q, a, n, children }) {
  const [open, setOpen] = useState(false);
  return (
    <div className={`acc rv ${open ? 'open' : ''}`}>
      <button className="ah" onClick={() => setOpen(!open)}><span>{n && <i className="mono">{n}</i>}{q}</span><b>+</b></button>
      <div className="ab2"><div><p>{a}</p>{children}</div></div>
    </div>
  );
}
