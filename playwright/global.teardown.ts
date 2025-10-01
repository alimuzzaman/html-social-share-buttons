const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Read the PID from the file
const pidFile = path.join(__dirname, '..', 'playground-pid.txt');
if (fs.existsSync(pidFile)) {
	const pid = fs.readFileSync(pidFile, 'utf-8');
	try {
		// Kill the process
		execSync(`kill -9 ${pid}`);
	} catch (e) {
		console.log('Error killing process:', e);
	}
	fs.unlinkSync(pidFile);
}

// Clean up URL file
const urlFile = path.join(__dirname, '..', 'playground-url.txt');
if (fs.existsSync(urlFile)) {
	fs.unlinkSync(urlFile);
}
