#!/usr/bin/env python3
"""Bind the tested ZIP to its source/version; never rebuild at publication."""
import hashlib
import json
import re
import stat
import sys
import zipfile
from pathlib import Path, PurePosixPath

SLUG = 'html-social-share-buttons'


def digest(data):
    return hashlib.sha256(data).hexdigest()


def inspect_archive(archive):
    files = {}
    with zipfile.ZipFile(archive) as package:
        for entry in package.infolist():
            name = entry.filename
            parts = PurePosixPath(name).parts
            if (len(parts) < 2 or parts[0] != SLUG or name.startswith('/')
                    or str(PurePosixPath(name)) != name.rstrip('/')
                    or '\\' in name or '..' in parts or '.' in name.split('/')
                    or stat.S_ISLNK(entry.external_attr >> 16)):
                raise ValueError('Unsafe archive path: ' + name)
            if entry.is_dir():
                continue
            if name in files:
                raise ValueError('Duplicate archive path: ' + name)
            files[name] = digest(package.read(entry))
        header = package.read(SLUG + '/html-social-share.php').decode()
        readme = package.read(SLUG + '/readme.txt').decode()
    version = re.search(r'^Version:\s*(\S+)\s*$', header, re.M)
    stable = re.search(r'^Stable tag:\s*(\S+)\s*$', readme, re.M)
    if not version or not stable or version[1] != stable[1]:
        raise ValueError('Plugin header and stable tag disagree')
    if not re.fullmatch(r'\d+\.\d+\.\d+(?:[.-][0-9A-Za-z.-]+)?', version[1]):
        raise ValueError('Invalid release version')
    return version[1], files


def manifest(directory, source):
    if not re.fullmatch('[0-9a-f]{40}', source):
        raise ValueError('Expected full source commit SHA')
    archive = directory / 'candidate.zip'
    version, files = inspect_archive(archive)
    return {'schema': 1, 'source_sha': source, 'version': version,
            'sha256': digest(archive.read_bytes()), 'files': files}


def verify(directory, source, tag=None, expected_hash=None):
    actual = manifest(directory, source)
    recorded = json.loads((directory / 'manifest.json').read_text())
    if actual != recorded:
        raise ValueError('Candidate manifest/hash/source mismatch')
    if tag is not None and tag != 'v' + actual['version']:
        raise ValueError('Tag does not match candidate version')
    if expected_hash is not None and expected_hash != actual['sha256']:
        raise ValueError('Candidate differs from manually reviewed SHA-256')
    return actual


def main():
    mode, location, source, *arguments = sys.argv[1:]
    directory = Path(location)
    if mode == 'create':
        target = directory / 'manifest.json'
        with target.open('x') as output:
            json.dump(manifest(directory, source), output, indent=2, sort_keys=True)
            output.write('\n')
    elif mode == 'verify':
        verify(directory, source, *arguments)
    elif mode == 'extract':
        tag, expected_hash, destination = arguments
        verify(directory, source, tag, expected_hash)
        target = Path(destination)
        target.mkdir(parents=True, exist_ok=False)
        with zipfile.ZipFile(directory / 'candidate.zip') as package:
            package.extractall(target)
    elif mode == 'installed':
        recorded = verify(directory, source)
        root = Path(arguments[0])
        if any(file.is_symlink() for file in root.rglob('*')):
            raise ValueError('Installed candidate contains a symlink')
        actual = {SLUG + '/' + str(file.relative_to(root)): digest(file.read_bytes())
                  for file in root.rglob('*') if file.is_file()}
        if actual != recorded['files']:
            raise ValueError('Installed candidate files differ from ZIP')
    else:
        raise ValueError('Unknown command')
    print('Candidate ' + mode + ' passed')


if __name__ == '__main__':
    main()
