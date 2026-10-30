import { useGo } from '../context/Transition';

/** Internal link that plays the page-transition curtain. Use instead of <a> for in-site links. */
export default function TLink({ to, children, onClick, ...rest }) {
  const go = useGo();
  const external = /^(https?:|mailto:|tel:)/.test(to);
  return (
    <a
      href={to}
      {...rest}
      onClick={(e) => {
        onClick?.(e);
        if (external || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey) return;
        e.preventDefault();
        go(to);
      }}
    >
      {children}
    </a>
  );
}
