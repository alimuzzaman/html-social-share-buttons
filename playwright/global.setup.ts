import { spawn } from 'child_process';
import fs from 'fs';
import path from 'path';

// Launch the Playground server
const playgroundProcess = spawn('npx', [
  '@wp-playground/cli',
  'server',
  '--blueprint=./blueprints/test-blueprint.json',
], { stdio: ['ignore', 'pipe', 'pipe'] });

// Listen for stdout to capture the resolved URL
let serverUrl = '';
playgroundProcess.stdout.on('data', (chunk) => {
  const line = String(chunk);
  const match = line.match(/http:\/\/localhost:\d+/);
  if (match) {
    serverUrl = match[0];
    fs.writeFileSync(path.join(__dirname, '..', 'playground-url.txt'), serverUrl);
  }
});

// Persist the PID for teardown
fs.writeFileSync(path.join(__dirname, '..', 'playground-pid.txt'), String(playgroundProcess.pid));

// Wait for the server to be ready
await new Promise((resolve) => {
  const checkReady = () => {
    if (serverUrl) {
      resolve();
    } else {
      setTimeout(checkReady, 100);
    }
  };
  checkReady();
});