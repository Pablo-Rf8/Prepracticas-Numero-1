<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SeedDemoUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_demo_users_without_changing_real_accounts_or_sending_messages(): void
    {
        $realUser = User::factory()->create(['email' => 'persona@example.test']);
        $originalAttributes = $realUser->fresh()->getRawOriginal();
        Mail::fake();
        Notification::fake();
        Event::fake([Registered::class, 'eloquent.created: '.User::class]);

        $this->artisan('users:seed-demo', ['--count' => '12'])->assertSuccessful();

        $this->assertDatabaseCount('users', 13);
        $this->assertSame($originalAttributes, $realUser->fresh()->getRawOriginal());
        $demoUsers = User::where('email', 'like', 'practica-%@usuarios.example.invalid')->get();
        $this->assertCount(12, $demoUsers);

        foreach ($demoUsers as $demoUser) {
            $this->assertMatchesRegularExpression('/^practica-\d{6}@usuarios\.example\.invalid$/', $demoUser->email);
            $this->assertNotEmpty($demoUser->name);
            $this->assertNotNull($demoUser->email_verified_at);
            $this->assertNull($demoUser->remember_token);
            $this->assertFalse(Hash::check('password', $demoUser->password));
            $this->assertNotSame('password', $demoUser->password);
        }

        Mail::assertNothingOutgoing();
        Notification::assertNothingSent();
        Event::assertNotDispatched(Registered::class);
        Event::assertNotDispatched('eloquent.created: '.User::class);
    }

    public function test_command_can_resume_and_increase_its_target_without_duplicating_or_overwriting_users(): void
    {
        $existingUser = User::factory()->create(['email' => 'practica-000003@usuarios.example.invalid']);
        $originalAttributes = $existingUser->fresh()->getRawOriginal();

        $this->artisan('users:seed-demo', ['--count' => '3'])->assertSuccessful();
        $this->assertDatabaseCount('users', 3);
        $firstUser = User::where('email', 'practica-000001@usuarios.example.invalid')->firstOrFail();
        $firstAttributes = $firstUser->getRawOriginal();

        $this->artisan('users:seed-demo', ['--count' => '3'])->assertSuccessful();
        $this->assertDatabaseCount('users', 3);
        $this->assertSame($firstAttributes, $firstUser->fresh()->getRawOriginal());

        $this->artisan('users:seed-demo', ['--count' => '5'])->assertSuccessful();
        $this->assertDatabaseCount('users', 5);
        $this->assertSame($originalAttributes, $existingUser->fresh()->getRawOriginal());
        $this->assertSame($firstAttributes, $firstUser->fresh()->getRawOriginal());
        $this->assertDatabaseHas('users', ['email' => 'practica-000004@usuarios.example.invalid']);
        $this->assertDatabaseHas('users', ['email' => 'practica-000005@usuarios.example.invalid']);
    }

    public function test_command_fills_more_than_one_batch(): void
    {
        $this->artisan('users:seed-demo', ['--count' => '1001'])->assertSuccessful();

        $this->assertDatabaseCount('users', 1001);
        $this->assertDatabaseHas('users', ['email' => 'practica-001000@usuarios.example.invalid']);
        $this->assertDatabaseHas('users', ['email' => 'practica-001001@usuarios.example.invalid']);
    }

    #[DataProvider('invalidCounts')]
    public function test_command_rejects_invalid_counts_without_creating_users(string $count): void
    {
        $this->artisan('users:seed-demo', ['--count' => $count])
            ->expectsOutputToContain('La cantidad debe ser un número entero entre 1 y 100000.')
            ->assertExitCode(2);

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidCounts(): array
    {
        return [
            'zero' => ['0'],
            'negative' => ['-1'],
            'over the maximum' => ['100001'],
            'decimal' => ['1.5'],
            'text' => ['muchos'],
            'empty' => [''],
        ];
    }

    public function test_production_requires_confirmation_unless_forced(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->artisan('users:seed-demo', ['--count' => '1'])
            ->expectsConfirmation('Are you sure you want to run this command?', 'no')
            ->assertFailed();
        $this->assertDatabaseCount('users', 0);

        $this->artisan('users:seed-demo', ['--count' => '1', '--force' => true])->assertSuccessful();
        $this->assertDatabaseCount('users', 1);
    }
}
