<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>Test CKEditor</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CKEditor 4.25.1-lts (Latest LTS Version) -->
    <script src="https://cdn.ckeditor.com/4.25.1/standard/ckeditor.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Test CKEditor Integration</h1>

        <form>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="Test Article">
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="10">
                    <h2>Test Content</h2>
                    <p>This is a test of the <strong>rich text editor</strong>!</p>
                    <ul>
                        <li>Feature 1</li>
                        <li>Feature 2</li>
                        <li>Feature 3</li>
                    </ul>
                </textarea>
            </div>

            <button type="button" class="btn btn-primary" onclick="getData()">Get Editor Data</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof CKEDITOR !== 'undefined') {
                console.log('CKEditor loaded successfully!');

                CKEDITOR.replace('content', {
                    height: 400,
                    toolbar: [
                        { name: 'document', items: ['Source', '-', 'NewPage', 'Preview'] },
                        { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                        { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                        '/',
                        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
                        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                        { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                        { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                        '/',
                        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                        { name: 'colors', items: ['TextColor', 'BGColor'] },
                        { name: 'tools', items: ['Maximize'] }
                    ],
                    removePlugins: 'elementspath',
                    resize_enabled: true,
                    language: 'vi',
                    entities_latin: false,
                    entities: false,
                    basicEntities: false
                });

                console.log('CKEditor initialized!');
            } else {
                console.error('CKEditor not loaded!');
                alert('CKEditor failed to load!');
            }
        });

        function getData() {
            var editor = CKEDITOR.instances.content;
            if (editor) {
                var data = editor.getData();
                alert('Editor content: ' + data);
            } else {
                alert('Editor instance not found!');
            }
        }
    </script>
</body>
</html>
