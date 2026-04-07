<h3>Character Links</h3>
<p>
    <b>Users may use this item to establish a link request between one of their characters and another.</b> Here you may assign a list of link statuses/titles a user can set their character to feel here. Both characters in a given link link may only
    have a single status/title designated to feel about the other.
</p>

<div class="text-right mb-3">
    <a href="#" class="btn btn-outline-info" id="addLink">Add Link Type</a>
</div>

<div class="mb-3" id="linksBody">
    @if (isset($tag->getData()['links']) && count($tag->getData()['links']))
        @foreach ($tag->getData()['links'] as $link)
            <div class="row mb-2">
                <div class="col">
                    {!! Form::text('links[]', $link, ['class' => 'form-control']) !!}
                </div>
                <div class="col-auto text-right">
                    <a href="#" class="btn btn-danger remove-link">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        @endforeach
    @endif
</div>

<script>
    $(document).ready(function() {
        var $linkTable = $('#linksBody');
        var $linkRow = $('.link-row');

        attachRemoveListener($('#linksBody .remove-link'));

        $('#addLink').on('click', function(e) {
            e.preventDefault();
            var $clone = $linkRow.clone();
            $clone.removeClass('hide');

            $linkTable.append($clone);
            attachRemoveListener($clone.find('.remove-link'));
        });

        function attachRemoveListener(node) {
            node.on('click', function(e) {
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });
        }
    });
</script>
