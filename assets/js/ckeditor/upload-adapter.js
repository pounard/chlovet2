import FileRepository from '@ckeditor/ckeditor5-upload/src/filerepository';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import { fetchToken, upload } from "../../dist/filechunk-front/upload";

// Quelques exemples:
//   https://ckeditor.com/docs/ckeditor5/latest/api/module_upload_filerepository-FileLoader.html#member-file
//   https://ckeditor.com/docs/ckeditor5/latest/builds/guides/integration/configuration.html
//   https://ckeditor.com/docs/ckeditor5/latest/framework/guides/deep-dive/upload-adapter.html#the-complete-implementation
//   https://github.com/ckeditor/ckeditor5-easy-image/blob/master/src/cloudservicesuploadadapter.js

export default function AppUploadPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        return new AppUploadAdapter(loader);
    };
}

class AppUploadAdapter {

    constructor(loader) {
        this.loader = loader;
    }

    doUpload(file) {
        const total = file.size;

        return fetchToken()
            .then(token => upload(file, {
                chunksize: 984593,
                endpoint: "/filechunk/upload",
                fieldname: "ckeditor", // "ckeditor", @todo needs to be pre-configured in session
                // propose "special" fields with a fixed configuration in yaml config
                onUpdate: (percent) => {
                    percent = parseInt(percent, 10);
                    this.loader.uploadTotal = total;
                    this.loader.uploaded = Math.round(total * (percent / 100));
                },
                token: token,
            }))
            .then(data => {
            	console.log(data);
                return {"default": data.url};
            })
        ;
    }

    upload() {
        console.log(this.loader.file);
        console.log(this.loader);

        if (this.loader.file.then) {
            return this.loader.file.then(this.doUpload);
        }

        return this.doUpload(this.loader.file);
    }

    abort() {
        // @todo
    }
}
