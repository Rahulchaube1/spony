console.log("=== VEDAILOG: BRIDGE LAUNCHER MODULE LOADING ===");
process.env.LAMBDA_TASK_ROOT = process.env.LAMBDA_TASK_ROOT || "/var/task";
const orig = require("./launcher-orig.js");
console.log("=== VEDAILOG: ORIG LAUNCHER REQUIRED ===");

exports.launcher = async (event, context) => {
  console.log("=== VEDAILOG: LAUNCHER INVOKED ===", typeof event, Object.keys(event || {}));
  const e = { ...event };
  if (Array.isArray(e.body)) {
    e.body = Buffer.from(e.body);
  } else if (typeof e.body === "string") {
    e.body = Buffer.from(e.body, e.isBase64Encoded ? "base64" : "utf8");
  } else {
    e.body = undefined;
  }
  const h = e.headers || {};
  e.host = e.host || h["x-forwarded-host"] || h.host || "localhost";
  return orig.launcher(e, context);
};
