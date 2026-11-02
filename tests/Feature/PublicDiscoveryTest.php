<?php

namespace Tests\Feature;

use App\Models\Career;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_does_not_download_career_listings(): void
    {
        $this->get('/')->assertInertia(fn (Assert $page) => $page->component('Welcome')->missing('careers'));
    }

    public function test_public_career_search_filters_and_only_returns_previews(): void
    {
        Career::query()->delete();
        Career::create(['title' => 'Electrical technician', 'sector' => 'Engineering', 'description' => str_repeat('Private detailed description. ', 20), 'skills' => ['Wiring', 'Safety', 'Testing', 'Maintenance'], 'is_active' => true]);
        Career::create(['title' => 'Hidden technician', 'sector' => 'Engineering', 'description' => 'Wiring', 'skills' => ['Wiring'], 'is_active' => false]);
        $this->get('/careers?q=wiring&sector=Engineering')->assertInertia(fn (Assert $page) => $page
            ->component('discovery/Careers')->where('filters.q', 'wiring')->where('careers.total', 1)
            ->where('careers.data.0.title', 'Electrical technician')->has('careers.data.0.skills', 3)
            ->missing('careers.data.0.description'));
        $this->get('/careers?q=unmatched')->assertInertia(fn (Assert $page) => $page->where('careers.total', 0));
    }

    public function test_career_pagination_preserves_search(): void
    {
        Career::query()->delete();
        foreach (range(1, 13) as $number) {
            Career::create(['title' => 'Technician '.$number, 'sector' => 'Engineering', 'description' => 'Technical work', 'skills' => [], 'is_active' => true]);
        }
        $this->get('/careers?q=technician&page=2')->assertInertia(fn (Assert $page) => $page
            ->has('careers.data', 1)->where('careers.total', 13)->where('careers.current_page', 2)
            ->where('careers.prev_page_url', fn ($url) => str_contains($url, 'q=technician')));
    }

    public function test_talent_search_only_returns_labelled_examples(): void
    {
        $this->get('/talent?q=networking')->assertInertia(fn (Assert $page) => $page
            ->component('discovery/Talent')->has('samples', 1)
            ->where('samples.0.programme', 'Information technology')
            ->missing('samples.0.email')->missing('students'));
        $this->get('/institution')->assertRedirect('/login');
    }
}
