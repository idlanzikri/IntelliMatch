<?php

namespace App\Http\Controllers;

use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DiscoveryController extends Controller
{
    public function careers(Request $request): Response
    {
        $filters = $request->validate(['q' => 'nullable|string|max:500', 'sector' => 'nullable|string|max:255']);
        $query = Career::where('is_active', true);
        $words = collect(preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($filters['q'] ?? ''), -1, PREG_SPLIT_NO_EMPTY))
            ->filter(fn ($word) => mb_strlen($word) > 2 && ! in_array($word, ['the', 'and', 'for', 'with', 'enjoy', 'career', 'careers', 'want']));
        if ($words->isNotEmpty()) {
            $query->where(function ($query) use ($words) {
                foreach ($words as $word) {
                    foreach (['title', 'sector', 'description', 'skills'] as $column) {
                        $query->orWhereRaw('LOWER('.$column.') LIKE ?', ['%'.$word.'%']);
                    }
                }
            });
        }
        if (! empty($filters['sector'])) {
            $query->where('sector', $filters['sector']);
        }

        return Inertia::render('discovery/Careers', [
            'filters' => ['q' => $filters['q'] ?? '', 'sector' => $filters['sector'] ?? ''],
            'sectors' => Career::where('is_active', true)->distinct()->orderBy('sector')->pluck('sector'),
            'careers' => $query->orderBy('title')->orderBy('id')->paginate(12)->withQueryString()->through(fn ($career) => [
                'id' => $career->id, 'title' => $career->title, 'sector' => $career->sector,
                'preview' => Str::limit($career->description, 140), 'skills' => array_slice($career->skills ?? [], 0, 3),
            ]),
        ]);
    }

    public function talent(Request $request): Response
    {
        $filters = $request->validate(['q' => 'nullable|string|max:500', 'programme' => 'nullable|string|max:255']);
        // Public examples only: real profiles have no public visibility consent yet.
        $samples = collect([
            ['id' => 1, 'programme' => 'Mechanical engineering', 'skills' => ['Maintenance', 'Technical drawing', 'Troubleshooting']],
            ['id' => 2, 'programme' => 'Information technology', 'skills' => ['Networking', 'Technical support', 'Problem solving']],
            ['id' => 3, 'programme' => 'Electrical technology', 'skills' => ['Wiring', 'Safety procedures', 'Troubleshooting']],
        ]);
        $programmes = $samples->pluck('programme');
        $samples = $samples->filter(fn ($sample) =>
            (empty($filters['q']) || Str::contains(mb_strtolower($sample['programme'].' '.implode(' ', $sample['skills'])), mb_strtolower($filters['q'])))
            && (empty($filters['programme']) || $sample['programme'] === $filters['programme']))->values();

        return Inertia::render('discovery/Talent', [
            'filters' => ['q' => $filters['q'] ?? '', 'programme' => $filters['programme'] ?? ''],
            'programmes' => $programmes, 'samples' => $samples,
        ]);
    }
}
