@extends('larasnap::layouts.app', ['class' => ''])
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" />
<link rel="stylesheet" href="{{ asset('vendor/laradocs/css/lara-docs.css') }}">

@section('content')
<style>
    /* Styling for Dropzone upload area */
    #document-dropzone {
        border: 2px dashed #ccc;
        padding: 50px 20px;
        min-height: 150px;
        text-align: center;
        background-color: #f8f9fa;
        color: #6c757d;
        font-size: 1.1em;
        border-radius: 15px;
    }

    /* Styling for File Previews outside the Dropzone box */
    .file-preview-container {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .file-preview {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        background-color: #ffffff;
        border: 1px solid #ced4da;
        border-radius: 5px;
        width: 180px;
        justify-content: space-between;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .file-preview img {
        width: 30px;
        height: 30px;
    }

    .file-preview span {
        flex: 1;
        margin-left: 10px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 0.95em;
    }

    .remove-file {
        color: red;
        cursor: pointer;
        font-size: 1.2em;
    }

    #submit-upload {
        margin-top: 20px;
    }

    /* Remove file preview from the dropzone itself */
    .dz-preview {
        display: none !important;
    }

    .display-4 {
        font-size: 18px;
        font-weight: 500;
        line-height: 24px;
    }

    .select2-container .select2-selection--multiple {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        min-height: 40px;
        user-select: none;
        -webkit-user-select: none;
    }

    .file-preview {
        position: relative;
    }

    .remove-file {
        position: absolute;
        right: 9px;
        top: 0;
    }

    span.select2-selection.select2-selection--multiple {
        position: relative;
    }

    span.select2-selection.select2-selection--multiple:after {
        content: '';
        background-image: url(/public/vendor/larasnap-auth/images/downarrow.png);
        background-repeat: no-repeat;
        width: 10px;
        height: 10px;
        position: absolute;
        right: 10px;
        top: 15px;
    }

    .upload-status-section {
        margin-top: 20px;
    }

    .status-card {
        display: flex;
        align-items: center;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .status-card.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-card.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        width: 300px;
        margin-top: 15px;
    }

    .status-card-icon {
        font-size: 31px;
        margin-right: 10px;
    }

    .status-card-text {
        font-size: 16px;
    }

    /* Failed File List Styling */
    .failed-file-list {
        list-style-type: disc;
        padding-left: 20px;
        color: #d9534f;
        /* Red color for errors */
    }

    .failed-file-item {
        margin: 5px 0;
        font-size: 14px;
    }

    .failed-file-item i {
        margin-right: 5px;
        color: #d9534f;
        /* Red color for the icon */
    }
</style>
<!-- Page Heading  Start-->
<div class="d-sm-flex align-items-center justify-content-start mb-4">
    <a href="{{ route('document.list') }}" title="Back to User List" class="btn btn-primary btn-sm mr-2"><i
            aria-hidden="true" class="fa fa-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0 text-gray-800 nhead">Manage Documents</h1>
</div>
<!-- Page Heading End-->
<div class="card shadow my-4 py-4">
    <div class="card-body pt-0">
        <div class="card-body p-0">
            <div class="container-fluid p-0">
                <!-- Category Select Dropdown -->
                <div class="form-group">
                    <label class="control-label" for="category-select">Category <small
                            class="text-danger required">*</small></label><br>
                    <select name="category-select" class="form-control searchable-dropdown-category"
                        id="category-select" style="width: 100%;" multiple>
                        <option value="">Select a category</option>
                        @foreach($category as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropzone File Upload -->
                <div class="form-group" id="upload-trigger">
                    <label class="control-label" for="documents">Upload Documents <small
                            class="text-danger required">*</small></label>
                    <form action="{{ route('document.index') }}" method="POST" id="document-dropzone">
                        @csrf
                        <p><img src="{{  asset('vendor/laradocs/images/upload.png') }}" alt="IMG"></p>
                        <div class="py-3">Drag & Drop Doc/Docx or PDF files here to upload</div>
                        <div class="text-primary display-4" style="cursor: pointer;">Click or Browse
                        </div>
                    </form>
                </div>

                <!-- File Preview and Remove Option -->
                <div class="file-preview-container" id="file-preview-container"></div>

                <!-- Submit Button -->
                <button id="submit-upload" class="btn btn-primary" disabled>
                    <span id="createLoader" class="d-none ml-2"><i class="fa fa-spinner fa-pulse"></i>
                    </span>Submit</button>

                <!-- Display Upload Status Messages -->
                <div id="upload-status">
                    <!-- The error messages will be displayed as a bulleted list -->
                    <ul class="failed-file-list">
                        <!-- Error messages will be added here dynamically -->
                    </ul>
                </div>

            </div>
            <div class="modal fade success-model" id="successpopup" tabindex="-1" role="dialog" aria-labelledby="successpopup"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="text-center">
                                <p><img style="width: 115px;"
                                        src="{{  asset('vendor/laradocs/images/success-icon.png') }}" alt="IMG"></p>
                                <h1>Document Uploaded</h1>
                                <p>Your files have been saved. You can now proceed or view the uploaded documents.</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary mb-2" id="confirm">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

<script>
    $(document).ready(function () {
        $('#file').on('change', function () {
            $('#error-message').hide();
        });
        // Initialize Select2
        $('.searchable-dropdown-category').select2({
            placeholder: "Select Category",
            allowClear: true
        });

        // Initialize Dropzone
        var dropzoneInstance = new Dropzone("#document-dropzone", {
            url: "{{ route('document.index') }}",
            autoProcessQueue: false,
            maxFilesize: 5,
            acceptedFiles: ".doc,.docx,.pdf",
            clickable: "#upload-trigger", // Makes "+" button act as the clickable trigger
            init: function () {
                this.on("addedfile", function (file) {
                    // Create a preview container with a data attribute instead of an ID
                    var filePreview = $(
                        '<div class="file-preview" data-file-name="' + file.name + '">' +
                        '<img src="' + (file.type.includes("pdf") ? "{{ asset('vendor/laradocs/images/pdflogo.png') }}" : "{{ asset('vendor/laradocs/images/wordlogo.png') }}") + '" alt="File Logo" />' +
                        '<span>' + file.name + '</span>' +
                        '<span class="remove-file" data-file-name="' + file.name + '">&times;</span>' +
                        '</div>'
                    );

                    // Add the preview to the container
                    $('#file-preview-container').append(filePreview);

                    // Enable submit button after adding a file
                    $('#submit-upload').prop('disabled', false);
                });

                var failedFiles = []; // Array to track failed uploads

                this.on("success", function (file, response) {
                    toastr.success("File uploaded successfully!");
                    $('#upload-status').append('<p>File "' + file.name + '" uploaded successfully.</p>');
                });

                this.on("error", function (file, response) {
                    $('#submit-upload').prop('disabled', true);
                    let errorMessage = response;
                    if (response.includes("File is too big")) {
                        errorMessage = `Error uploading "${file.name}": File is too big (${(file.size / (1024 * 1024)).toFixed(1)}MB). Max filesize: 5MB.`;
                    } else {
                        errorMessage = `Error uploading "${file.name}"</span> :  ${response} Please upload a valid file<strong>(doc/docx/pdf)<strong>`;
                    }

                    // Append the error message to the failed file list container
                    $('#upload-status .failed-file-list').append(errorMessage);
                    if ($('#upload-status .failed-file-list').children().length === 0) {
                        $('#submit-upload').prop('disabled', true);
                    }
                    $('#createLoader').addClass('d-none');
                });

                this.on("complete", function (file) {
                    // After all files are processed, check for any failed files
                    if (failedFiles.length > 0) {
                        var failedFileList = '<p>The following files failed to upload:</p><ul>';
                        failedFiles.forEach(function (fileName) {
                            failedFileList += '<li>' + fileName + '</li>';
                        });
                        failedFileList += '</ul>';
                        $('#upload-status').append(failedFileList);
                    }
                });
            }
        });

        $('#submit-upload').on('click', function (e) {
            $('#createLoader').removeClass('d-none');
            e.preventDefault();
            var selectedCategory = $('#category-select').val();
            console.log(selectedCategory);
            if (selectedCategory.length === 0) {
                $('#createLoader').addClass('d-none');
                toastr.warning("Please select a category to upload files.");
                return;
            }

            var files = dropzoneInstance.getAcceptedFiles(); // Get only accepted files still in Dropzone

            if(files.length == 0)
            {
                $('#createLoader').addClass('d-none');
                toastr.warning("Please uploaded atleast one file!"); 
                return;
            }


            if (files.length > 0) {
                var formData = new FormData();
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("category_id", selectedCategory);

                files.forEach(function (file) {
                    formData.append("documents[]", file);
                });

                $.ajax({
                    url: "{{ route('document.upload') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('#createLoader').addClass('d-none');
                        dropzoneInstance.removeAllFiles(true);
                        $('#file-preview-container').empty();
                        $('#submit-upload').prop('disabled', true);
                        $('#category-select').val('').trigger('change');
                        $('#successpopup').modal('show');

                    },
                    error: function (xhr, status, error) {
                        $('#createLoader').addClass('d-none');
                        toastr.error("Error uploading files: " + error);
                        // window.location.href = "{{ route('document.list') }}";
                    }
                });
            }
        });

        $('#confirm').click(function () {
            window.location.href = "{{ route('document.list') }}";
        });
        // Event delegation for remove button click
        $('#file-preview-container').on('click', '.remove-file', function () {
            const fileName = $(this).data('file-name');
            const fileToRemove = dropzoneInstance.files.find(file => file.name === fileName);
            if (fileToRemove) {
                $('#upload-status .failed-file-list').empty();
                dropzoneInstance.removeFile(fileToRemove);
                $(this).parent().remove();
            }
            if ($('#upload-status .failed-file-list').children().length === 0) {
                $('#submit-upload').prop('disabled', false);
            }
        });
    });
</script>
@endsection