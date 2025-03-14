# Przykłady Testów

## 1. Testy Rejestracji i KYC

```php
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_user_cannot_access_projects_without_kyc()
    {
        $user = User::factory()->create(['kyc_verified_at' => null]);
        
        $response = $this->actingAs($user)->get('/projects');
        
        $response->assertRedirect('/kyc/verify');
    }
}
```

## 2. Testy Projektów Inwestycyjnych

```php
class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_user_can_create_project()
    {
        $user = User::factory()->create(['kyc_verified_at' => now()]);
        
        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'Test Project',
            'description' => 'Project description',
            'investment_amount' => 100000,
            'duration' => 12,
            'return_rate' => 10.5,
        ]);

        $response->assertRedirect('/projects/1');
        $this->assertDatabaseHas('projects', ['name' => 'Test Project']);
    }

    public function test_project_requires_team_approval()
    {
        $user = User::factory()->create(['kyc_verified_at' => now()]);
        $team = Team::factory()->create(['user_id' => $user->id]);
        $project = Project::factory()->create(['team_id' => $team->id]);
        
        $this->assertTrue($project->requiresApproval());
        $this->assertFalse($project->isApproved());
    }
}
```

## 3. Testy Subskrypcji

```php
class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe_to_plan()
    {
        $user = User::factory()->create([
            'kyc_verified_at' => now(),
            'stripe_id' => 'cus_123'
        ]);

        $response = $this->actingAs($user)->post('/subscription', [
            'plan' => 'professional',
            'payment_method' => 'pm_card_visa'
        ]);

        $response->assertSuccessful();
        $this->assertTrue($user->subscribed('default'));
    }

    public function test_subscription_gives_access_to_features()
    {
        $user = User::factory()->create();
        $user->newSubscription('default', 'professional')->create('pm_card_visa');

        $this->assertTrue($user->canCreateProjects());
        $this->assertTrue($user->hasUnlimitedTeams());
    }
}
```

## 4. Testy API

```php
class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_authentication_requires_token()
    {
        $response = $this->getJson('/api/projects');
        
        $response->assertStatus(401);
    }

    public function test_api_can_list_projects()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        Project::factory()->count(3)->create();

        $response = $this->getJson('/api/projects', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertSuccessful();
        $response->assertJsonCount(3, 'data');
    }
}
```

## 5. Testy Wielojęzyczności

```php
class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_language()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/language/pl');
            
        $response->assertSessionHas('locale', 'pl');
    }

    public function test_project_can_have_translations()
    {
        $project = Project::factory()->create([
            'name:en' => 'Investment Project',
            'name:pl' => 'Projekt Inwestycyjny',
            'name:de' => 'Investitionsprojekt'
        ]);

        $this->assertEquals('Investment Project', $project->getTranslation('name', 'en'));
        $this->assertEquals('Projekt Inwestycyjny', $project->getTranslation('name', 'pl'));
    }
}
```

## 6. Testy Integracji z Zewnętrznymi Serwisami

```php
class ExternalServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_veriff_kyc_verification()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/kyc/verify', [
            'session_id' => 'test_session',
            'verification_status' => 'approved'
        ]);

        $this->assertNotNull($user->fresh()->kyc_verified_at);
    }

    public function test_stripe_webhook_handling()
    {
        $user = User::factory()->create();
        
        $response = $this->postJson('/stripe/webhook', [
            'type' => 'customer.subscription.created',
            'data' => [
                'object' => [
                    'customer' => $user->stripe_id,
                    'status' => 'active'
                ]
            ]
        ]);

        $response->assertSuccessful();
        $this->assertTrue($user->fresh()->subscribed('default'));
    }
}
```

## 7. Testy Powiadomień

```php
class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_receives_project_approval_notification()
    {
        Notification::fake();
        
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        
        $project->approve();
        
        Notification::assertSentTo(
            $user,
            ProjectApprovedNotification::class,
            function ($notification) use ($project) {
                return $notification->project->id === $project->id;
            }
        );
    }

    public function test_team_members_receive_new_project_notification()
    {
        Notification::fake();
        
        $team = Team::factory()->create();
        $members = User::factory()->count(3)->create();
        
        foreach ($members as $member) {
            $team->users()->attach($member, ['role' => 'member']);
        }
        
        $project = Project::factory()->create(['team_id' => $team->id]);
        
        Notification::assertSentTo(
            $members,
            NewProjectNotification::class
        );
    }
}
``` 