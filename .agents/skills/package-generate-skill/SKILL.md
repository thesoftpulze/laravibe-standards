---
name: package-generate-skill
description: "Use this skill when creating or updating the bundled Laravel Boost guideline under resources/boost/guidelines or the Boost skills under resources/boost/skills from the package implementation and package documentation. Trigger after public APIs, commands, config, routes, views, publish tags, README content, or examples change."
license: MIT
metadata:
  author: laravel
---

# Package Generate Skill

## Primary Goal

Keep the package's bundled Boost guideline and skills accurate, concise, and focused on helping Laravel applications adopt the package.

## Workflow

1. Inspect the package implementation before editing the Boost guideline or skills: service provider, facades, public classes, commands, config, routes, migrations, events, views, publish tags, and tests.
2. Inspect package documentation: `README.md`, contributing docs, examples, and changelog entries that describe user-facing behavior.
3. Identify the public integration surface only. Include install, configure, publish, command, route, facade, helper, middleware, event, and testing guidance only when the package actually exposes it.
4. Update `resources/boost/guidelines/core.blade.php` only for rules that must hold in every session. Boost loads it upfront, so keep it short and point to the matching skill for details.
5. Update the focused skill under `resources/boost/skills/{name}/SKILL.md` (one per feature area: DTOs, enums, resources) with practical adoption steps, examples, and anti-patterns. Add a new skill for a new feature area rather than growing an existing one.
6. Keep each skill's front matter `name` equal to its folder name, and write a `description` that names the task and classes it covers, since agents activate skills from the description.
7. Wrap code in the guideline in `@verbatim` and `<code-snippet>` blocks, because the guideline is rendered as Blade.
8. Validate that the guideline and skills don't describe internals as public API or document features that aren't implemented, then run `composer test:unit -- --filter=BoostResources`.

## Writing Rules

- Write for consumers installing the package in a Laravel application, not for maintainers changing package internals.
- Prefer short, task-oriented steps over broad explanations.
- Point to concrete files only when they help the consumer understand package adoption.
- Keep examples runnable and aligned with documented package names, config keys, commands, and publish tags.
- Keep the skill small enough for an agent to load and apply quickly.

## References

- `resources/boost/guidelines/core.blade.php`
- `resources/boost/skills/`
- `tests/Feature/BoostResourcesTest.php`
- `src/*ServiceProvider.php`
- `src/Facades/`
- `src/Console/Commands/`
- `config/*.php`
- `routes/*.php`
- `database/migrations/`
- `README.md`
- `tests/Feature/` and `tests/Unit/`

## Examples

- After adding a new Artisan command, update the Boost skill with when to run it, required options, expected output, and any related config.
- After adding config and publish tags, update the Boost skill with publish commands and the minimum config keys users need to set.
- After adding a facade or public class, update the Boost skill with one practical integration example and the test assertion a consuming app should use.

## Anti-Patterns

- Regenerating the Boost guideline or skills from documentation alone without checking implementation.
- Putting detailed patterns in the guideline. It is loaded in every session, so details belong in a skill.
- Documenting private classes, test helpers, workbench-only routes, or implementation details as consumer API.
- Adding speculative examples for features the package does not provide.
- Removing package metadata that consuming agents need to identify and apply the skill.
