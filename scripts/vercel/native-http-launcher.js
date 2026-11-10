process.env.LAMBDA_TASK_ROOT = process.env.LAMBDA_TASK_ROOT || "/var/task";

const http = require("http");
const net = require("net");
const { spawn } = require("child_process");
const { join } = require("path");

const getUserDir = () => join(process.env.LAMBDA_TASK_ROOT || "/var/task", "user");
const getPhpDir = () => join(process.env.LAMBDA_TASK_ROOT || "/var/task", "php");

let phpServer = null;
let serverInitPromise = null;

function whenPortOpens(port, attempts = 500) {
  return new Promise((resolve, reject) => {
    function tryConnect(remaining) {
      const client = net.connect(port, "127.0.0.1");
      client.on("error", (err) => {
        if (remaining <= 0) return reject(err);
        setTimeout(() => tryConnect(remaining - 1), 10);
      });
      client.on("connect", () => {
        client.destroy();
        resolve();
      });
    }
    tryConnect(attempts);
  });
}

async function ensurePhpServer() {
  if (phpServer) return;
  if (!serverInitPromise) {
    serverInitPromise = (async () => {
      const phpDir = getPhpDir();
      const userDir = getUserDir();
      const docroot = join(userDir, process.env.VERCEL_PHP_DOCROOT || "");
      const entrypoint = process.env.NOW_ENTRYPOINT || "api/index.php";
      const router = join(userDir, entrypoint);

      console.log(`🐘 Spawning PHP Server at docroot ${docroot} with router ${router}`);

      const options = {
        stdio: ["pipe", "pipe", "pipe"],
        env: {
          ...process.env,
          LAMBDA_TASK_ROOT: process.env.LAMBDA_TASK_ROOT || "/var/task",
          PATH: `${phpDir}:${process.env.PATH || ""}`,
          LD_LIBRARY_PATH: `/var/task/lib:${process.env.LD_LIBRARY_PATH || ""}`
        },
        cwd: phpDir
      };

      phpServer = spawn("php", ["-c", "php.ini", "-S", "127.0.0.1:3000", "-t", docroot, router], options);

      phpServer.stdout.on("data", (d) => console.log(`🐘STDOUT: ${d}`));
      phpServer.stderr.on("data", (d) => console.error(`🐘STDERR: ${d}`));
      phpServer.on("close", (c, s) => {
        console.log(`🐘 PHP Server process closed (code: ${c}, signal: ${s})`);
        phpServer = null;
        serverInitPromise = null;
      });
      phpServer.on("error", (err) => {
        console.error("🐘 PHP Server spawn error:", err);
      });

      await whenPortOpens(3000, 500);
      console.log("🐘 PHP Server listening on 127.0.0.1:3000");
    })();
  }
  return serverInitPromise;
}

// Native Node.js HTTP request listener for Vercel
async function handler(req, res) {
  try {
    await ensurePhpServer();

    const forwardHeaders = { ...req.headers };
    forwardHeaders.host = req.headers["x-forwarded-host"] || req.headers.host || "localhost";

    const proxyReq = http.request(
      {
        hostname: "127.0.0.1",
        port: 3000,
        path: req.url,
        method: req.method,
        headers: forwardHeaders
      },
      (proxyRes) => {
        res.writeHead(proxyRes.statusCode, proxyRes.headers);
        proxyRes.pipe(res);
      }
    );

    proxyReq.on("error", (err) => {
      console.error("🐘 Proxy request error:", err);
      res.statusCode = 502;
      res.setHeader("Content-Type", "text/plain");
      res.end(`PHP Proxy Error: ${err.message}`);
    });

    req.pipe(proxyReq);
  } catch (err) {
    console.error("🐘 Handler initialization error:", err);
    res.statusCode = 500;
    res.setHeader("Content-Type", "text/plain");
    res.end(`Internal Server Error: ${err.message}`);
  }
}

module.exports = handler;
module.exports.default = handler;
