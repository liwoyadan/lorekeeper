tinymce.init({
selector: '#modal .wysiwyg',
height: 200,
menubar: false,
plugins: [
'advlist autolink lists link image charmap print preview anchor',
'searchreplace visualblocks code fullscreen',
'insertdatetime media table paste code help wordcount'
],
toolbar: 'undo redo | bold italic | bullist numlist outdent indent | removeformat | code',
content_css: [
'{{ asset('css/app.css') }}',
'{{ asset('css/lorekeeper.css') }}'
]
});
