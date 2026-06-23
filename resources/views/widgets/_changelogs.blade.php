@php
    $changelogTitle = $changelogTitle ?? null;
    $subject = $subject ?? null;
    $subjectType = $subjectType ?? null;
    $showAll = isset($showAll) && $showAll;
    $limit = $limit ?? 10;
    if (!$changelogTitle && $subject) {
        $changelogTitle = $subject->displayName ?? $subject->name;
    } elseif (!$changelogTitle && ($subjectType && class_exists($subjectType))) {
        $changelogTitle = class_basename($subjectType);
    }

    $query = \App\Models\Changelog::isStaff(Auth::user() ?? null, isset($staffOnly) && $staffOnly);
    if (!$showAll && ($subject || $subjectType)) {
        $query->subjectLogs($subject, $subjectType);
    }
    $changelogs = $query->orderBy('created_at', 'DESC')->limit($limit)->get();
@endphp

<div class="card my-1">
    <div class="card-header font-weight-bold py-2 px-3" style="font-size: 1.15em;">
        <i class="fas fa-list" aria-hidden="true"></i>
        {!! $changelogTitle ?? '' !!}
        Changelogs
    </div>
    <div class="card-body py-2">
        @if (count($changelogs))
            <ul class="list-unstyled changelog-list mb-0" style="max-height: 150px; overflow: auto;">
                @foreach ($changelogs as $changelog)
                    <li class="{{ !$loop->last ? 'border-bottom pb-2' : '' }} mb-1">
                        <div class="row no-gutters align-items-start">
                            @if ($showAll || $changelog->isRecent)
                                <div class="col-lg-auto">
                                    @if ($changelog->isRecent)
                                        <i class="fas fa-exclamation-circle text-primary mr-1" data-toggle="tooltip" title="This changelog was posted within the past {{ Settings::get('recent_changelog_days') }} days!"></i>
                                    @endif
                                    @if ($showAll)
                                        <span class="text-muted pr-1">
                                            {!! $changelog->displayName !!}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            <div class="col">
                                {!! $changelog->parsed_text !!}
                            </div>
                        </div>

                        <div class="small text-muted">
                            <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                            On
                            <b>{!! $changelog->created_at->format('Y-m-d H:i') !!}</b>
                            <span class="d-block d-sm-inline-block pl-2 pl-sm-0">
                                ({!! pretty_date($changelog->created_at) !!})
                                @if ($changelog->staff_only)
                                    <span class="badge badge-danger ml-1" data-toggle="tooltip" title="This changelog entry is only visible to staff.">Staff Only</span>
                                @endif
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-muted text-center">No changelogs to display.</div>
        @endif
    </div>
</div>
