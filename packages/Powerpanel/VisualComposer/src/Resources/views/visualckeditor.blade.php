<script src="{{ url('resources/pages/scripts/packages/visualcomposer/ckeditor.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{ url('resources/pages/scripts/packages/visualcomposer/ckfinder.js') }}"></script>
<script type="text/javascript">
var allEditors = document.querySelectorAll('.txtarea');

class ImageUploadAdapter
{
    constructor( loader ) {
        // The file loader instance to use during the upload.
        this.loader = loader;
    }

    // Starts the upload process.
    upload() {
        return this.loader.file
            .then( file => new Promise( ( resolve, reject ) => {
                this._initRequest();
                this._initListeners( resolve, reject, file );
                this._sendRequest( file );
            } ) );
    }

    // Aborts the upload process.
    abort() {
        if ( this.xhr ) {
            this.xhr.abort();
        }
    }

    // Initializes the XMLHttpRequest object using the URL passed to the constructor.
    _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();

        // Note that your request may look different. It is up to you and your editor
        // integration to choose the right communication channel. This example uses
        // a POST request with JSON as a data structure but your configuration
        // could be different.

        xhr.open( 'POST', '{{ url("/powerpanel/ckeditor/upload-image") }}', true);
        xhr.responseType = 'json';
    }

    // Initializes XMLHttpRequest listeners.
    _initListeners( resolve, reject, file ) {
        const xhr = this.xhr;
        const loader = this.loader;
        const genericErrorText = `Couldn't upload file: ${ file.name }.`;

        xhr.addEventListener( 'error', () => reject( genericErrorText ) );
        xhr.addEventListener( 'abort', () => reject() );
        xhr.addEventListener( 'load', () => {

            const response = xhr.response;

            // This example assumes the XHR server's "response" object will come with
            // an "error" which has its own "message" that can be passed to reject()
            // in the upload promise.
            //
            // Your integration may handle upload errors in a different way so make sure
            // it is done properly. The reject() function must be called when the upload fails.
            if ( !response || response.error ) {
                return reject( response && response.error ? response.error.message : genericErrorText );
            }

            // If the upload is successful, resolve the upload promise with an object containing
            // at least the "default" URL, pointing to the image on the server.
            // This URL will be used to display the image in the content. Learn more in the
            // UploadAdapter#upload documentation.
            resolve( {
                default: response.url
            } );
        } );

        // Upload progress when it is supported. The file loader has the #uploadTotal and #uploaded
        // properties which are used e.g. to display the upload progress bar in the editor
        // user interface.
        if ( xhr.upload ) {
            xhr.upload.addEventListener( 'progress', evt => {
                if ( evt.lengthComputable ) {
                    loader.uploadTotal = evt.total;
                    loader.uploaded = evt.loaded;
                }
            } );
        }
    }

    // Prepares the data and sends the request.
    _sendRequest( file ) {
        // Prepare the form data.
        const data = new FormData();

        data.append( 'upload', file );

        // Important note: This is the right place to implement security mechanisms
        // like authentication and CSRF protection. For instance, you can use
        // XMLHttpRequest.setRequestHeader() to set the request headers containing
        // the CSRF token generated earlier by your application.
        // Send the request.
        this.xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        this.xhr.send( data );
    }
}

function ImageUploadAdapterPlugin( editor ) {
    editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
        // Configure the URL to the upload script in your back-end here!
        return new ImageUploadAdapter( loader );
    };
}

var simpleConfig = {
      alignment: {
          options: [ 'left', 'right', 'center' , 'justify' ]
      },
      toolbar: [ 'heading', '|', 'bold', 'italic', 'bulletedList', 'alignment', 'undo', 'redo' ],
      heading: {
          options: [
              { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },                
              { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
              { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
              { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
              { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
              { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
          ]
      }
  };

var cmsConfig = {
      alignment: {
          options: [ 'left', 'right', 'center' , 'justify' ]
      },//'imageUpload'
      toolbar: [
      'heading',       
      '|', 
      'bold', 
      'italic',
      'underline',      
      //'highlight',
      'link', 
      'bulletedList', 
      'numberedList',
      'fontSize',
      'fontFamily',
      'fontColor', 
      'fontBackgroundColor',
//      '|', 
//      'code',
      'imageUpload',
      //'ckfinder',
      //'|', 
      'insertTable',
      'blockQuote',
      'alignment',
      'undo', 
      'redo' ],
      extraPlugins: [ ImageUploadAdapterPlugin ],
      heading: {
          options: [
              { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },                
              { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
              { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
              { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
              { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
              { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
          ]
      },
      fontSize: {
          options: [
              9,
              11,
              13,
              'default',
              17,
              19,
              21
          ]
      },
      link: {
            // Automatically add target="_blank" and rel="noopener noreferrer" to all external links.
            addTargetToExternalLinks: true,
      },
      table: {
        contentToolbar: [
          'tableColumn',
          'tableRow',
          'mergeTableCells'
        ]
      },
      highlight: {
        options: [
            {
                model: 'greenPen',
                class: 'pen-green',
                title: 'Green pen',
                color: 'hsl(120,100%,25%)',
                type: 'pen'
            },
            {
                model: 'redPen',
                class: 'pen-red',
                title: 'Red pen',
                color: 'hsl(343, 82%, 58%)',
                type: 'pen'
            },
            {
                model: 'greenMarker',
                class: 'marker-green',
                title: 'Green marker',
                color: 'rgb(25, 156, 25)',
                type: 'marker'
            },
            {
                model: 'yellowMarker',
                class: 'marker-yellow',
                title: 'Yellow marker',
                color: '#cac407',
                type: 'marker'
            }            
        ]
    },
    // ckfinder: {
    //     uploadUrl: window.site_url+'/powerpanel/media/upload_image'
    //     //uploadUrl: window.site_url+'/ckfinder/connector?command=QuickUpload&type=Files&responseType=json'
    //     //uploadUrl:'https://cksource.com/weuy2g4ryt278ywiue/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json'
    // }
  };

var ckconfig = cmsConfig;

@if(isset($config) && $config == 'simpleConfig')
ckconfig = simpleConfig;
@endif

var editors = {};
for (var i = 0; i < allEditors.length; ++i) {    
    ClassicEditor.create(allEditors[i], ckconfig).then( editor => {      
      var id = $(editor.sourceElement).attr('id');      
      editors[id] = editor;
    } );
}
   
</script>
