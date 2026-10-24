## Laravibe Standards

This application uses `thesoftpulze/laravibe-standards` for its DTO, enum, and API resource conventions. Follow these rules whenever you create or change those classes.

- DTOs live in `app/DTOs` (or `app/DTOs/{Domain}`), are `final readonly` classes, and use the `TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO` trait. Generate them with `php artisan make:dto {Domain}/{Name}DTO`.
- Enums live in `app/Enums` and use the `TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata` trait. Generate them with `php artisan make:enum {Name} --int` (or `--string`).
- API resources live in `app/Http/Resources`, are `final`, and extend `TheSoftPulze\LaravibeStandards\Resources\AppResource`. Collections extend `TheSoftPulze\LaravibeStandards\Resources\AppResourceCollection`.
- Build resource fields with the package helpers (`id()`, `attribute()`, `optionalAttribute()`, `relation()`, `timestamps()`). Use `relation()` so relations are only serialized when eager loaded.
- Pass resources to Inertia with `->toInertia()`, not the resource object itself.
- `make:enum` and `make:resource` only generate classes in this style after the stubs are published. If `stubs/resource.stub` is missing, run `php artisan vendor:publish --tag=laravibe-standards-stubs` first.

Before writing one of these classes, activate the matching skill for the full rules: `laravibe-dto-development`, `laravibe-enum-development`, or `laravibe-resource-development`.

@verbatim
<code-snippet name="DTO, enum, and resource in the Laravibe style" lang="php">
final readonly class UpdateProfileDTO implements Arrayable, Jsonable
{
    use AsDTO;

    public function __construct(
        public string $name,
        public AccountStatus $status,
        public ?string $bio = null,
    ) {}
}

$dto = UpdateProfileDTO::from($request); // validated() data from a FormRequest

final class UserResource extends AppResource
{
    public function toArray(Request $request): array
    {
        return [
            $this->id(),
            $this->attribute('name'),
            $this->relation('posts', fn ($posts) => PostResource::collection($posts)),
            ...$this->timestamps(),
        ];
    }
}
</code-snippet>
@endverbatim
