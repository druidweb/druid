import { T, Z as Ziggy } from "../app.js";
function useRoutes(name, params, absolute = true) {
  return T(name, params, absolute, Ziggy);
}
export {
  useRoutes as u
};
