<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Factory;
use App\Models\Inquiry;
use App\Models\Post;
use App\Models\Style;
use App\Support\FactoryOptions;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        return view('site.home', [
            'styles' => Style::query()->latest()->limit(4)->get(),
            'factories' => Factory::query()->get(),
            'posts' => Post::query()->where('is_published', true)->latest('published_at')->limit(3)->get(),
            'certifications' => Certification::query()->orderBy('name')->limit(4)->get(),
        ]);
    }

    public function about(): View
    {
        return view('site.about', [
            'factories' => Factory::query()->with('company')->get(),
        ]);
    }

    public function capabilities(): View
    {
        return view('site.capabilities');
    }

    public function capacity(): View
    {
        return view('site.capacity', [
            'factories' => Factory::query()->withCount('sewingLines')->get(),
        ]);
    }

    public function certifications(): View
    {
        return view('site.certifications', [
            'certifications' => Certification::query()->orderBy('name')->get(),
        ]);
    }

    public function catalog(Request $request): View
    {
        $styles = Style::query()
            ->with('buyer')
            ->when($request->string('category')->toString(), fn ($q, $category) => $q->where('category', $category))
            ->when($request->string('fit')->toString(), fn ($q, $fit) => $q->where('fit', $fit))
            ->when($request->string('fabric')->toString(), fn ($q, $fabric) => $q->where('fabric_type', 'like', '%'.$fabric.'%'))
            ->when($request->string('wash')->toString(), fn ($q, $wash) => $q->where('wash', 'like', '%'.$wash.'%'))
            ->orderBy('style_no')
            ->get();

        return view('site.catalog', [
            'styles' => $styles,
            'categories' => FactoryOptions::styleCategories(),
            'fits' => FactoryOptions::fits(),
        ]);
    }

    public function style(Style $style): View
    {
        $style->load(['buyer', 'techPacks.bomItems', 'washRecipes']);

        return view('site.style', ['style' => $style]);
    }

    public function blog(): View
    {
        $posts = Post::query()
            ->where('is_published', true)
            ->where('locale', app()->getLocale())
            ->latest('published_at')
            ->get();

        if ($posts->isEmpty()) {
            $posts = Post::query()->where('is_published', true)->latest('published_at')->get();
        }

        return view('site.blog', ['posts' => $posts]);
    }

    public function post(Post $post): View
    {
        abort_unless($post->is_published, 404);

        return view('site.post', ['post' => $post]);
    }

    public function contact(): View
    {
        return view('site.contact');
    }

    public function storeInquiry(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:inquiry,sample,tour'],
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'style_interest' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Inquiry::query()->create([
            ...$data,
            'locale' => app()->getLocale(),
            'status' => 'new',
        ]);

        return back()->with('sent', true);
    }

    public function locale(string $locale)
    {
        abort_unless(in_array($locale, ['en', 'bn'], true), 404);
        session(['locale' => $locale]);

        return back();
    }
}
