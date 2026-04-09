export function portal(node: HTMLElement) {
  if (typeof document === "undefined") {
    return { destroy() {} };
  }

  document.body.appendChild(node);

  return {
    destroy() {
      node.remove();
    },
  };
}
