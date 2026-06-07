<?php

namespace Nepal360\FilamentCmsPro\API\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Nepal360\FilamentCmsPro\Models\Post;
use Nepal360\FilamentCmsPro\Models\Category;
use Nepal360\FilamentCmsPro\Models\Tag;
use Nepal360\FilamentCmsPro\Workflow\WorkflowService;
use Nepal360\FilamentCmsPro\SEO\Services\SeoEngineService;
use Illuminate\Http\JsonResponse;

class PostApiController extends Controller
{
    /**
     * Display a listing of published posts.
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->header('X-Locale', app()->getLocale());

        $query = Post::where('status', 'published')
            ->with(['translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            }, 'authors']);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories.translations', function($q) use ($request, $locale) {
                $q->where('slug', $request->query('category'))->where('locale', $locale);
            });
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags.translations', function($q) use ($request, $locale) {
                $q->where('slug', $request->query('tag'))->where('locale', $locale);
            });
        }

        // Apply sorting
        $sort = $request->query('sort', 'latest');
        if ($sort === 'trending') {
            $query->orderBy('trending_score', 'desc');
        } else {
            $query->orderBy('published_at', $sort === 'oldest' ? 'asc' : 'desc');
        }

        $posts = $query->paginate($request->query('per_page', 15));

        return response()->json($posts);
    }

    /**
     * Display the specified post with SEO metadata.
     */
    public function show(string $slug, Request $request, SeoEngineService $seoService): JsonResponse
    {
        $locale = $request->header('X-Locale', app()->getLocale());

        $post = Post::whereHas('translations', function($q) use ($slug, $locale) {
            $q->where('slug', $slug)->where('locale', $locale);
        })->where('status', 'published')->first();

        if (!$post) {
            return response()->json([
                'message' => "The requested post '{$slug}' was not found in locale '{$locale}'."
            ], 404);
        }

        $translation = $post->translations()->where('locale', $locale)->first();

        return response()->json([
            'data' => [
                'id' => $post->id,
                'title' => $translation->title,
                'slug' => $translation->slug,
                'excerpt' => $translation->excerpt,
                'content' => $translation->content,
                'featured_image' => $post->featured_image,
                'read_time_minutes' => $post->read_time_minutes,
                'published_at' => $post->published_at,
                'seo' => [
                    'meta_title' => $translation->seo_title,
                    'meta_description' => $translation->seo_description,
                    'keywords' => $translation->seo_keywords,
                    'social' => $translation->social_meta,
                    'schema' => $translation->schema_markup,
                ]
            ]
        ]);
    }

    /**
     * Transition a post status.
     */
    public function transition(Post $post, Request $request, WorkflowService $workflowService): JsonResponse
    {
        $request->validate([
            'target_status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $workflowService->transitionTo($post, $request->input('target_status'), [
                'notes' => $request->input('notes'),
            ]);

            return response()->json([
                'message' => "Post status transitioned to {$post->status} successfully.",
                'status' => $post->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * List all categories.
     */
    public function categories(Request $request): JsonResponse
    {
        $locale = $request->header('X-Locale', app()->getLocale());
        $categories = Category::with(['translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        }])->get();

        return response()->json(['data' => $categories]);
    }

    /**
     * List all tags.
     */
    public function tags(Request $request): JsonResponse
    {
        $locale = $request->header('X-Locale', app()->getLocale());
        $tags = Tag::with(['translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        }])->get();

        return response()->json(['data' => $tags]);
    }
}
