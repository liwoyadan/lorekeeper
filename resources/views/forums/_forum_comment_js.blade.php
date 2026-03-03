<script>
    $(document).ready(function() {
        tinymce.init({
            selector: '.comment-wysiwyg',
            height: 250,
            menubar: false,
            convert_urls: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen spoiler',
                'insertdatetime media table paste help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | spoiler-add spoiler-remove | removeformat | code',
            mobile: {
                theme: 'silver',
                menubar: false,
                toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | spoiler-add spoiler-remove | removeformat | code',
                resize: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen spoiler',
                    'insertdatetime media table paste help wordcount'
                ],
            },
            content_css: [
                '{{ asset('css/app.css?v=' . filemtime(public_path('css/app.css'))) }}',
                '{{ asset('css/lorekeeper.css?v=' . filemtime(public_path('css/lorekeeper.css'))) }}'
            ],
            spoiler_caption: 'Toggle Spoiler',
            target_list: false
        });

        @if (isset($forum->characters_enabled) && $forum->characters_enabled)
            $('.comment-character-select').selectize();
        @endif
    });
</script>
