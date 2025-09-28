import { spawn } from 'child_process';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default async function globalSetup() {
  console.log('Starting WordPress Playground server...');
  // Launch the Playground server
  const playgroundBin = path.join(__dirname, '..', 'node_modules', '.bin', 'wp-playground.cmd');
  const playgroundProcess = spawn('cmd', ['/c', playgroundBin, 'server', '--blueprint=./blueprints/test-blueprint.json'], { stdio: ['ignore', 'pipe', 'pipe'] });

  console.log('Playground process started with PID:', playgroundProcess.pid);

  // Listen for stdout to capture the resolved URL
  let serverUrl = '';
  playgroundProcess.stdout.on('data', (chunk: Buffer) => {
    const line = String(chunk);
    console.log('Playground output:', line.trim());
    const match = line.match(/http:\/\/localhost:\d+/);
    if (match) {
      serverUrl = match[0];
      fs.writeFileSync(path.join(__dirname, '..', 'playground-url.txt'), serverUrl);
      console.log('Server URL captured:', serverUrl);
    }
  });

  // Persist the PID for teardown
  fs.writeFileSync(path.join(__dirname, '..', 'playground-pid.txt'), String(playgroundProcess.pid));
  console.log('PID and URL files written.');

  // Wait for the server to be ready
  await new Promise((resolve) => {
    const checkReady = () => {
      if (serverUrl) {
        console.log('Playground server is ready.');
        resolve();
      } else {
        setTimeout(checkReady, 100);
      }
    };
    checkReady();
  });
}