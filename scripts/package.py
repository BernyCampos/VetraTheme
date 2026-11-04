"""Build a deterministic Pelican plugin ZIP. Never installs or modifies a panel."""
import argparse
import hashlib
import json
import re
from pathlib import Path
from zipfile import ZipFile, ZipInfo, ZIP_DEFLATED

ROOT = Path(__file__).resolve().parents[1]
parser = argparse.ArgumentParser()
parser.add_argument('--repository', help='GitHub owner/repo; enables release update feed')
args = parser.parse_args()
if args.repository and not re.fullmatch(r'[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+', args.repository):
    parser.error('repository must be owner/repo')

manifest = json.loads((ROOT / 'plugin.json').read_text(encoding='utf-8'))
assert manifest['id'] == 'vetra-theme'
assert manifest['namespace'] == 'Vetra\\Theme' and manifest['class'] == 'VetraThemePlugin'
assert manifest['category'] == 'theme' and manifest['api_version'] == 1
assert 'meta' not in manifest
assert manifest['version'] == json.loads((ROOT / 'package.json').read_text())['version']
assert (ROOT / 'dist/vetra.css').is_file(), 'Run npm run build first'
version = manifest['version']
assert re.fullmatch(r'\d+\.\d+\.\d+(?:-[a-zA-Z0-9.-]+)?', version)

if args.repository:
    manifest['update_url'] = f'https://github.com/{args.repository}/releases/latest/download/update.json'

output = ROOT / 'build'
output.mkdir(exist_ok=True)
archive = output / f'vetra-theme-{version}.zip'
files = []
for folder in ('src', 'config', 'resources', 'lang', 'dist', 'docs'):
    files.extend(p for p in (ROOT / folder).rglob('*') if p.is_file())
files.extend(ROOT / name for name in ('README.md', 'CHANGELOG.md', 'CONTRIBUTING.md', 'SECURITY.md', 'LICENSE'))
payload = {p.relative_to(ROOT).as_posix(): p.read_bytes() for p in sorted(files)}
payload['plugin.json'] = (json.dumps(manifest, indent=2, ensure_ascii=False) + '\n').encode()
with ZipFile(archive, 'w', compression=ZIP_DEFLATED, compresslevel=9) as z:
    for relative, content in sorted(payload.items()):
        info = ZipInfo('vetra-theme/' + relative, date_time=(2026, 11, 2, 0, 0, 0))
        info.compress_type = ZIP_DEFLATED
        info.external_attr = 0o100644 << 16
        z.writestr(info, content)
with ZipFile(archive) as z:
    assert z.testzip() is None
    names = z.namelist()
    assert all(n.startswith('vetra-theme/') and '..' not in n for n in names)
    assert 'vetra-theme/plugin.json' in names
    assert not any('/node_modules/' in n or '/.env' in n for n in names)
digest = hashlib.sha256(archive.read_bytes()).hexdigest()
source = output / f'vetra-theme-{version}-source.zip'
source_files = list(files) + [ROOT / 'plugin.json']
for folder in ('tests', 'scripts', '.github'):
    source_files.extend(p for p in (ROOT / folder).rglob('*') if p.is_file() and '__pycache__' not in p.parts)
source_files.extend(ROOT / name for name in ('package.json', 'package-lock.json', 'vite.config.js', '.gitignore'))
with ZipFile(source, 'w', compression=ZIP_DEFLATED, compresslevel=9) as z:
    for path in sorted(set(source_files)):
        info = ZipInfo('vetra-theme/' + path.relative_to(ROOT).as_posix(), date_time=(2026, 11, 2, 0, 0, 0))
        info.compress_type = ZIP_DEFLATED
        info.external_attr = 0o100644 << 16
        z.writestr(info, path.read_bytes())
source_digest = hashlib.sha256(source.read_bytes()).hexdigest()
(output / 'SHA256SUMS').write_text(f'{digest}  {archive.name}\n{source_digest}  {source.name}\n', encoding='ascii')
if args.repository:
    feed = {manifest['panel_version']: {
        'version': version,
        'download_url': f'https://github.com/{args.repository}/releases/download/v{version}/{archive.name}',
        'sha256': digest,
    }}
    (output / 'update.json').write_text(json.dumps(feed, indent=2) + '\n', encoding='utf-8')
elif (output / 'update.json').exists():
    # Never leave a stale release feed beside a local-only archive.
    (output / 'update.json').unlink()
print(f'{archive.name}: {len(payload)} files, {archive.stat().st_size} bytes, SHA256 {digest}')
