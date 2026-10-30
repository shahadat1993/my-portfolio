import { useEffect, useState } from 'react';

const fmt = () => new Date().toLocaleTimeString('en-GB', { timeZone: 'Asia/Dhaka', hour: '2-digit', minute: '2-digit' }) + ' BDT';
export default function Clock({ as: Tag = 'span' }) {
  const [t, setT] = useState(fmt());
  useEffect(() => { const i = setInterval(() => setT(fmt()), 1000); return () => clearInterval(i); }, []);
  return <Tag className="clk">{t}</Tag>;
}
