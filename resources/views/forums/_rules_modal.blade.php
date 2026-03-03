<button type="button" class="btn btn-sm btn-primary text-uppercase font-weight-bold" data-toggle="modal" data-target="#forumRules{{ $forum->id ?? '' }}">
    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
    Forum Rules
</button>

<div class="modal fade text-left" id="forumRules{{ $forum->id ?? '' }}" tabindex="-1" role="dialog" aria-labelledby="forumRules{{ $forum->id ?? '' }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h5 class="modal-title" id="exampleModalLabel">
                    {!! $forum->name !!} Rules
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-1">
                @if (isset($ruleSets['current']) && count($ruleSets['current']))
                    <ul class="mb-0">
                        @foreach ($ruleSets['current'] as $currentForumRule)
                            <li>
                                {!! $currentForumRule !!}
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if (isset($ruleSets['parents']) && count($ruleSets['parents']))
                    @if (isset($ruleSets['current']) && count($ruleSets['current']))
                        <hr class="mx-auto w-75 my-1">
                    @endif
                    <div class="font-weight-bold" style="font-size: 1.25em;">
                        & Rules Inherited From:
                    </div>
                    @foreach ($ruleSets['parents'] as $parentKey => $parentForumSet)
                        <a class="collapse-toggle collapsed mb-1 ml-2" href="#forumRuleSet{!! $parentKey ?? '_' !!}" data-toggle="collapse" aria-expanded="false">
                            {!! $parentForumSet['name'] !!} {!! add_help('Click to toggle rules.') !!}
                        </a>
                        <div class="collapse border rounded py-2" id="forumRuleSet{!! $parentKey ?? '_' !!}">
                            <ul class="mb-0">
                                @foreach($parentForumSet['rules'] as $parentForumRule)
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
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
