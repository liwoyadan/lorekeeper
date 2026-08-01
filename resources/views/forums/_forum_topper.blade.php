@if ($forum->has_image)
    <div class="text-center mb-2">
        <a href="{!! $forum->imageUrl !!}" data-lightbox="entry" data-title="{!! $forum->name !!}">
            <img src="{!! $forum->imageUrl !!}" class="img-fluid" />
        </a>
    </div>
@endif

<div class="row mb-md-2">
    @if (isset($forum->description))
        <div class="col-md">
            <div class="card h-100">
                <div class="card-header h5 text-center">
                    Description
                </div>
                <div class="card-body px-3 py-2">
                    {!! $forum->parsed_description !!}
                </div>
            </div>
        </div>
    @endif

    @if (isset($forum->allRules) && $forum->allRules && count($forum->allRules))
        <div class="col-md {{ isset($forum->description) && $forum->description ? 'pt-2 pt-md-0' : '' }}">
            <div class="card h-100">
                <div class="card-header h5 text-center">
                    Forum Rules
                </div>
                <div class="card-body px-3 py-2">
                    @if (isset($forum->allRules['current']) && count($forum->allRules['current']))
                        <div class="font-weight-bold" style="font-size: 1.25em;">
                            {!! $forum->name !!} Rules
                        </div>
                        <ul class="mb-0">
                            @foreach ($forum->allRules['current'] as $currentForumRule)
                                <li>
                                    {!! $currentForumRule !!}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if (isset($forum->allRules['parents']) && count($forum->allRules['parents']))
                        @if (isset($forum->allRules['current']) && count($forum->allRules['current']))
                            <hr class="mx-auto w-75 my-1">
                        @endif
                        <div class="font-weight-bold" style="font-size: 1.25em;">
                            Rules Inherited From:
                        </div>
                        @foreach ($forum->allRules['parents'] as $parentKey => $parentForumSet)
                            <a class="collapse-toggle collapsed ml-2 mb-1" href="#forumRuleSet{!! $parentKey ?? '_' !!}" data-toggle="collapse" aria-expanded="false">
                                {!! $parentForumSet['name'] !!} {!! add_help('Click to toggle rules.') !!}
                            </a>
                            <div class="collapse border rounded py-2" id="forumRuleSet{!! $parentKey ?? '_' !!}">
                                <ul class="mb-0">
                                    @foreach ($parentForumSet['rules'] as $parentForumRule)
                                        <li>
                                            {!! $parentForumRule !!}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @if (!$loop->last)
                                <hr class="mx-auto w-50 my-1">
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
