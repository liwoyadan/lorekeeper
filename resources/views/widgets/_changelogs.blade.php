@php
    $subject = $subject ?? null;
    $subjectType = $subjectType ?? null;
    $isStaff = isset($isStaff) && $isStaff;
    $limit = $limit ?? 10;

    $query = \App\Models\Changelog::query();
    if ($subject || $subjectType) {
        $query->subject($subject, $subjectType);
    }
    if (!$isStaff) {
        $query->where('staff_only', 0);
    }
    $changelogs = $query->orderBy('created_at', 'DESC')->limit($limit)->get();
@endphp

<div class="card my-2">
    <div class="card-header">
        <h5 class="mb-0">Changelogs</h5>
    </div>
    <div class="card-body">
        @if (count($changelogs))
            <ul class="list-unstyled mb-0">
                @foreach ($changelogs as $changelog)
                    <li class="mb-2">
                        <div class="small text-muted">
                            {!! $changelog->created_at->format('Y-m-d H:i') !!}
                            @if ($changelog->staff_only)
                                <span class="badge badge-danger ml-1">Staff Only</span>
                            @endif
                        </div>
                        <div>{!! $changelog->parsed_text !!}</div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-muted text-center">No changelogs to display.</div>
        @endif
    </div>
</div>
