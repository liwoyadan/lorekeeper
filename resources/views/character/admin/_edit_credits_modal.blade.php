{{ html()->form('POST', 'admin/character/image/' . $image->id . '/credits')->open() }}
<div class="form-group">
    {{ html()->label('Designer(s)') }}
    <div id="designerList">
        <?php $designerCount = count($image->designers); ?>
        @foreach ($image->designers as $count => $designer)
            <div class="mb-2 d-flex">
                {{ html()->select('designer_id[' . $designer->id . ']', $users, $designer->user_id)->class('form-control mr-2 selectize')->placeholder('Select a Designer') }}
                {{ html()->text('designer_url[' . $designer->id . ']', $designer->url)->class('form-control mr-2')->attribute('placeholder', 'Designer URL') }}

                <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer" @if ($count != $designerCount - 1) style="visibility: hidden;" @endif>+</a>
            </div>
        @endforeach
        @if (!count($image->designers))
            <div class="mb-2 d-flex">
                {{ html()->select('designer_id[]', $users, null)->class('form-control mr-2 selectize')->placeholder('Select a Designer') }}
                {{ html()->text('designer_url[]', null)->class('form-control mr-2')->attribute('placeholder', 'Designer URL') }}

                <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer">+</a>
            </div>
        @endif
    </div>
    <div class="designer-row hide mb-2">
        {{ html()->select('designer_id[]', $users, null)->class('form-control mr-2 designer-select')->placeholder('Select a Designer') }}
        {{ html()->text('designer_url[]', null)->class('form-control mr-2')->attribute('placeholder', 'Designer URL') }}
        <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer">+</a>
    </div>
</div>
<div class="form-group">
    {{ html()->label('Artist(s)') }}
    <div id="artistList">
        <?php $artistCount = count($image->artists); ?>
        @foreach ($image->artists as $count => $artist)
            <div class="mb-2 d-flex">
                {{ html()->select('artist_id[' . $artist->id . ']', $users, $artist->user_id)->class('form-control mr-2 selectize')->placeholder('Select an Artist') }}
                {{ html()->text('artist_url[' . $artist->id . ']', $artist->url)->class('form-control mr-2')->attribute('placeholder', 'Artist URL') }}
                <a href="#" class="add-artist btn btn-link" data-toggle="tooltip" title="Add another artist" @if ($count != $artistCount - 1) style="visibility: hidden;" @endif>+</a>
            </div>
        @endforeach
        @if (!count($image->artists))
            <div class="mb-2 d-flex">
                {{ html()->select('artist_id[]', $users, null)->class('form-control mr-2 selectize')->placeholder('Select an Artist') }}
                {{ html()->text('artist_url[]', null)->class('form-control mr-2')->attribute('placeholder', 'Artist URL') }}
                <a href="#" class="add-artist btn btn-link" data-toggle="tooltip" title="Add another artist">+</a>
            </div>
        @endif
    </div>
    <div class="artist-row hide mb-2">
        {{ html()->select('artist_id[]', $users, null)->class('form-control mr-2 artist-select')->placeholder('Select an Artist') }}
        {{ html()->text('artist_url[]', null)->class('form-control mr-2')->attribute('placeholder', 'Artist URL') }}
        <a href="#" class="add-artist btn btn-link mb-2" data-toggle="tooltip" title="Add another artist">+</a>
    </div>
</div>

<div class="text-right">
    {{ html()->submit('Edit')->class('btn btn-primary') }}
</div>
{{ html()->form()->close() }}

<script>
    $(document).ready(function() {
        $('.selectize').selectize();
        $('.add-designer').on('click', function(e) {
            e.preventDefault();
            addDesignerRow($(this));
        });

        function addDesignerRow($trigger) {
            var $clone = $('.designer-row').clone();
            $('#designerList').append($clone);
            $clone.removeClass('hide designer-row');
            $clone.addClass('d-flex');
            $clone.find('.add-designer').on('click', function(e) {
                e.preventDefault();
                addDesignerRow($(this));
            })
            $trigger.css({
                visibility: 'hidden'
            });
            $clone.find('.designer-select').selectize();
        }

        $('.add-artist').on('click', function(e) {
            e.preventDefault();
            addArtistRow($(this));
        });

        function addArtistRow($trigger) {
            var $clone = $('.artist-row').clone();
            $('#artistList').append($clone);
            $clone.removeClass('hide artist-row');
            $clone.addClass('d-flex');
            $clone.find('.add-artist').on('click', function(e) {
                e.preventDefault();
                addArtistRow($(this));
            })
            $trigger.css({
                visibility: 'hidden'
            });
            $clone.find('.artist-select').selectize();
        }
    });
</script>
