#!/usr/bin/env python3
import importlib.util
import json
import tempfile
import subprocess
import sys
import unittest
import zipfile
from pathlib import Path

sys.dont_write_bytecode = True

spec = importlib.util.spec_from_file_location('candidate', Path(__file__).parents[1] / 'scripts/release-candidate.py')
candidate = importlib.util.module_from_spec(spec)
spec.loader.exec_module(candidate)


class CandidateContract(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.addCleanup(self.temp.cleanup)
        self.root = Path(self.temp.name)
        self.source = 'a' * 40
        self.write_zip()
        self.record()

    def write_zip(self, stable='3.2.0', extra=None):
        with zipfile.ZipFile(self.root / 'candidate.zip', 'w') as archive:
            archive.writestr(candidate.SLUG + '/html-social-share.php', 'Version: 3.2.0\n')
            archive.writestr(candidate.SLUG + '/readme.txt', 'Stable tag: ' + stable + '\n')
            if extra:
                archive.writestr(extra, 'untrusted')

    def record(self):
        (self.root / 'manifest.json').write_text(json.dumps(candidate.manifest(self.root, self.source)))

    def test_exact_identity(self):
        original = (self.root / 'candidate.zip').read_bytes()
        candidate.verify(self.root, self.source, 'v3.2.0', candidate.digest(original))
        self.assertEqual(original, (self.root / 'candidate.zip').read_bytes())

    def test_changed_bytes(self):
        with (self.root / 'candidate.zip').open('ab') as archive:
            archive.write(b'changed')
        with self.assertRaisesRegex(ValueError, 'mismatch'):
            candidate.verify(self.root, self.source)

    def test_wrong_source(self):
        with self.assertRaisesRegex(ValueError, 'mismatch'):
            candidate.verify(self.root, 'b' * 40)

    def test_wrong_tag(self):
        with self.assertRaisesRegex(ValueError, 'Tag'):
            candidate.verify(self.root, self.source, 'v3.2.1')

    def test_wrong_reviewed_hash(self):
        with self.assertRaisesRegex(ValueError, 'reviewed'):
            candidate.verify(self.root, self.source, 'v3.2.0', '0' * 64)

    def test_wrong_stable_tag(self):
        self.write_zip(stable='3.1.0')
        with self.assertRaisesRegex(ValueError, 'disagree'):
            candidate.manifest(self.root, self.source)

    def test_missing_manifest(self):
        (self.root / 'manifest.json').unlink()
        with self.assertRaises(FileNotFoundError):
            candidate.verify(self.root, self.source)

    def test_extract_and_installed_bytes(self):
        destination = self.root / 'extracted'
        script = Path(__file__).parents[1] / 'scripts/release-candidate.py'
        command = [sys.executable, str(script)]
        archive_hash = candidate.digest((self.root / 'candidate.zip').read_bytes())
        result = subprocess.run(command + ['extract', str(self.root), self.source, 'v3.2.0', archive_hash, str(destination)], capture_output=True)
        self.assertEqual(0, result.returncode, result.stderr)
        installed = destination / candidate.SLUG
        check = command + ['installed', str(self.root), self.source, str(installed)]
        self.assertEqual(0, subprocess.run(check, capture_output=True).returncode)
        (installed / 'readme.txt').write_text('changed')
        self.assertNotEqual(0, subprocess.run(check, capture_output=True).returncode)
        # Never overwrite a previous extraction, even when its content changed.
        result = subprocess.run(command + ['extract', str(self.root), self.source, 'v3.2.0', archive_hash, str(destination)], capture_output=True)
        self.assertNotEqual(0, result.returncode)

    def test_path_traversal(self):
        for name in ['../escape', candidate.SLUG + '/../escape', '/absolute', candidate.SLUG + '/a\\b']:
            with self.subTest(name=name):
                self.write_zip(extra=name)
                with self.assertRaisesRegex(ValueError, 'Unsafe'):
                    candidate.manifest(self.root, self.source)


if __name__ == '__main__':
    unittest.main()
