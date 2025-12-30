
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ckeditor').forEach(editor => {
        ClassicEditor
            .create(editor, {
                toolbar: [
                    'heading', '|', 
                    'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                    'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo'
                ],
                language: 'ru',
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Параграф', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Заголовок 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Заголовок 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Заголовок 3', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .catch(error => {
                console.error('CKEditor error:', error);
            });
    });
});