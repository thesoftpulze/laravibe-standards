# Release Notes

## [Unreleased](https://github.com/thesoftpulze/laravibe-standards/compare/v0.3.5...HEAD)

## [v0.3.5](https://github.com/softpulze/laravibe-standards/compare/v0.3.4...v0.3.5) - 2026-07-27

<!-- Release notes generated using configuration in .github/release.yml at v0.3.5 -->
### What's Changed

#### Documentation

* docs: keep [Unreleased] clean for fully automated changelog by @ashokbaruaakas in https://github.com/softpulze/laravibe-standards/pull/11

**Full Changelog**: https://github.com/softpulze/laravibe-standards/compare/v0.3.4...v0.3.5

## [v0.3.4](https://github.com/softpulze/laravibe-standards/compare/v0.3.3...v0.3.4) - 2026-07-27

<!-- Release notes generated using configuration in .github/release.yml at v0.3.4 -->
### What's Changed

#### Documentation

* docs: add --generate-notes to release command by @ashokbaruaakas in https://github.com/softpulze/laravibe-standards/pull/10

**Full Changelog**: https://github.com/softpulze/laravibe-standards/compare/v0.3.3...v0.3.4

## [v0.3.3](https://github.com/softpulze/laravibe-standards/compare/v0.3.2...v0.3.3) - 2026-07-27

### Documentation

- Added release command to AGENTS.md with required `--title` flag for automated changelog update workflow.

## [v0.3.2](https://github.com/softpulze/laravibe-standards/compare/v0.3.1...v0.3.2) - 2026-07-27

### Fixed

- Restored manually written changelog entries in the v0.3.1 section that were overwritten by auto-generated release notes.

### Documentation

- Added release conventions and PR labeling instructions to AGENTS.md and CONTRIBUTING.md for automated changelog generation.

## [v0.3.1](https://github.com/softpulze/laravibe-standards/compare/v0.3.0...v0.3.1) - 2026-07-27

### Changed

- Updated Boost skill description with task-specific keywords (DTOs, Enums, API Resources, Action classes, stubs) to enable automatic agent discovery in OpenCode.
- Enum convention now prefers int-backed enums; string-backed enums are reserved for values that must be human-readable or interoperate with external systems.
- Updated enum guide, README, and Boost skill examples to use int-backed enums with the `--int` flag.
- Enum stubs (`enum.stub`, `enum.backed.stub`) now include `declare(strict_types=1)`.

## [v0.3.0](https://github.com/softpulze/laravibe-standards/compare/v0.2.0...v0.3.0) - 2026-07-24

### Added

- feat: add resource conventions by @ashokbaruaakas in https://github.com/softpulze/laravibe-standards/pull/4

## [v0.2.0](https://github.com/softpulze/laravibe-standards/compare/v0.1.0...v0.2.0) - 2026-07-23

### Added

- Enum convention with `HasEnumMetadata` trait for label, option, and validation helpers
- Custom `stubs/enum.stub` and `stubs/enum.backed.stub` published via the `laravibe-standards-stubs` tag, used by Laravel's built-in `make:enum` command
- Full enum guide under `src/Enums/README.md`
- Boost skill updated with enum conventions and prompt templates

### Changed

- Raised minimum PHP to 8.4 and dropped Laravel 12 support
- Updated CI matrix to run on PHP 8.4/8.5 and Laravel 13 only

## [v0.1.0](https://github.com/softpulze/laravibe-standards/compare/...v0.1.0) - 2026-07-23

### Added

- DTO convention with `AsDTO` trait for typed, immutable data transfer objects
- `make:dto` Artisan command with package stub that is also publishable
- Stub publishing tag `laravibe-standards-stubs` for customization
- Full DTO convention guide under `src/DTOs/README.md`
- Boost skill updated with DTO adoption workflow for Laravel apps
