export function setTitle(to, from, next) {
  document.title = `${to.meta.title} | ${import.meta.env.VITE_APP_NAME}`
  next()
}
