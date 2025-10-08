# FotoArt Puzzle - CHANGELOG

## [1.0.1] - 2024-05-18
### Fixed
- Hardened `FAPPathValidator` to forbid files that only share a prefix with allowed roots, preventing crafted paths from escaping the module storage.
- Added regression test ensuring prefix-based traversal attempts are rejected.

### Verification
- Token lifecycle, download authorization (admin/front), and session handling remain aligned with the legacy `fotoart/` implementation per README test plan.
- Automated suite (`tests/run.php`) covers token, path, and session services.
