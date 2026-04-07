<div class="card mb-2">
    <div class="card-body pt-2">
        <h4 class="mb-2 d-flex align-items-center">
            <span class="btn badge-warning py-1 mr-2">
                Pending
            </span>
            <span class="pt-1">
                {!! $otherCharacter->displayName !!} owned by {!! $otherCharacter->user->displayName !!}
            </span>
        </h4>

        <div class="row no-gutters">
            <div class="col-md-3 pr-md-1 text-center">
                <a href="{{ $otherCharacter->url }}">
                    <img src="{{ $otherCharacter->image->thumbnailUrl }}" class="img-thumbnail" alt="{{ $otherCharacter->fullName }}">
                </a>
            </div>

            <div class="col-md-9 pl-md-1 text-center text-md-left">
                <span class="faded">{{ ucfirst(__('links.link')) }} request created {!! pretty_date($link->created_at) !!} by {!! $link->initialLog()->sender->displayName !!}.</span>

                @if (Auth::user()->id != $recipient->id)
                    <div class="mt-3">
                        <span class="font-weight-bold text-info">This {{ __('links.link') }} request is still pending approval by the recipient.</span> If the request is rejected, the item you used to send the request will be refunded back to you.
                    </div>
                @else
                    <div class="mt-3">
                        <span class="font-weight-bold text-info">You may choose to accept or reject this {{ __('links.link') }} request.</span> Rejecting the request will refund the item used to establish the {{ __('links.link') }} back to the
                        sender.
                        <div class="row no-gutters mt-2">
                            <div class="col-md-6 pb-2 pb-md-0 pr-md-1">
                                {!! Form::open(['url' => '/' . __('links.links') . '/accept/' . $link->id]) !!}
                                {!! Form::submit('Accept', ['class' => 'btn btn-success w-100']) !!}
                                {!! Form::close() !!}
                            </div>

                            <div class="col-md-6 pl-md-1">
                                {!! Form::open(['url' => '/' . __('links.links') . '/reject/' . $link->id]) !!}
                                {!! Form::submit('Reject', ['class' => 'btn btn-danger w-100']) !!}
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
