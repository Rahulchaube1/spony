const fs = require('fs');
const path = require('path');

const functionsDir = path.resolve(__dirname, '../../.vercel/output/functions/api');

function findConfigs(dir) {
  let results = [];
  if (!fs.existsSync(dir)) return results;
  const list = fs.readdirSync(dir);
  for (const file of list) {
    const fullPath = path.join(dir, file);
    const stat = fs.statSync(fullPath);
    if (stat.isDirectory()) {
      results = results.concat(findConfigs(fullPath));
    } else if (file === '.vc-config.json') {
      results.push(fullPath);
    }
  }
  return results;
}

const configs = findConfigs(functionsDir);
console.log('Found configs to patch:', configs);

const ignoredPrefixes = [
  'user/.git/',
  'user/tests/',
  'user/docs/',
  'user/.agents/',
  'user/.claude/',
  'user/.cursor/',
  'user/.dev/',
  'user/marketing/',
  'user/changelogs/',
  'user/node_modules/',
  'user/docker/',
  'user/vyntra-cloud/'
];

for (const configPath of configs) {
  const config = JSON.parse(fs.readFileSync(configPath, 'utf8'));

  // Use Native Node.js HTTP request listener (req, res) on Vercel
  config.handler = 'launcher.js';
  config.launcherType = 'Nodejs';
  delete config.awsLambdaHandler;
  config.shouldAddHelpers = false;
  config.shouldAddSourcemapSupport = false;

  // Wire up native HTTP launcher
  if (config.filePathMap && config.filePathMap['launcher.js']) {
    config.filePathMap['launcher.js'] = 'scripts/vercel/native-http-launcher.js';
    config.filePathMap['user/database/database.sqlite'] = 'database/database.sqlite';

    let removedCount = 0;
    for (const key of Object.keys(config.filePathMap)) {
      if (ignoredPrefixes.some(prefix => key.startsWith(prefix))) {
        delete config.filePathMap[key];
        removedCount++;
      }
    }
    console.log(`[${path.basename(path.dirname(configPath))}] Removed ${removedCount} files, remaining: ${Object.keys(config.filePathMap).length}`);
  }

  fs.writeFileSync(configPath, JSON.stringify(config, null, 2), 'utf8');
}

console.log('All configs patched with native HTTP launcher successfully!');
