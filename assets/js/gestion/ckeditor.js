
import ClassicEditor from '@ckeditor/ckeditor5-build-classic/build/ckeditor';
import AppUploadPlugin from '../ckeditor/upload-adapter';

const elements = document.querySelectorAll('[data-editor]');

if (elements.length) {
    for (let i = 0; i < elements.length; i++) {
        ClassicEditor
            .create(elements[i], {
                extraPlugins: [AppUploadPlugin],
            })
            .catch(error => console.error(error))
        ;
    }
}
