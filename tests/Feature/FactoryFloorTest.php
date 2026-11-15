<?php

namespace Tests\Feature;

use App\Models\FactoryIssue;
use App\Models\Inquiry;
use App\Models\PayrollLine;
use App\Models\Style;
use App\Models\User;
use Database\Seeders\FactoryDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoryFloorTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_floor_opens_the_issue_desk_and_public_site(): void
    {
        $this->seed(FactoryDemoSeeder::class);

        $types = FactoryIssue::query()->pluck('type')->unique()->sort()->values()->all();

        $this->assertEqualsCanonicalizing([
            'bundle_mixup',
            'late_material',
            'line_imbalance',
            'low_efficiency',
            'machine_downtime',
            'recurring_defect',
            'shade_mismatch',
        ], $types);

        $this->assertGreaterThan(0, FactoryIssue::query()->whereHas('actions')->count());
        $this->assertGreaterThan(0, PayrollLine::query()->count());
        $this->assertNotEmpty(Style::query()->where('style_no', 'PT-2401')->value('slug'));

        $this->get('/')->assertOk()->assertSee('Padma');
        $this->get('/catalog')->assertOk()->assertSee('PT-2401');
        $this->get('/news')->assertOk();
        $this->get('/locale/bn')->assertRedirect();

        $this->post('/contact', [
            'type' => 'sample',
            'name' => 'Helen Ward',
            'company' => 'Harrow & Co',
            'email' => 'helen@harrow.test',
            'style_interest' => 'PT-2401',
            'message' => 'Please send a size set in navy and charcoal.',
        ])->assertRedirect();

        $this->assertDatabaseHas('inquiries', [
            'email' => 'helen@harrow.test',
            'type' => 'sample',
        ]);

        $admin = User::query()->where('email', 'admin@trouser.test')->first();
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/hub')->assertOk();
    }

    public function test_contact_page_is_public(): void
    {
        $this->get('/contact')->assertOk();
        $this->get('/admin/login')->assertOk();
    }
}
