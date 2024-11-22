@php
    use Illuminate\Support\Str;
@endphp
@extends('larasnap::layouts.app', ['class' => ''])
<link rel="stylesheet" href="{{ asset('vendor/laradocs/css/lara-docs.css') }}">
@section('content')

<!-- Page Heading  Start-->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 nhead">Manage Documents</h1>
                                        @canAccess('document.index')
                                        <a href="{{ route('document.index') }}">
                                            <button class="btn btn-primary" type="button" id="submit-disable">
                                                + Upload Document
                                            </button>
                                        </a>
                                        @endcanAccess
</div>
<!-- Page Heading End-->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="container-fluid pt-4">
                <div class="row">
                    <div class="col-md-12 mb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <form method="GET" action="{{ route('document.list') }}" id="searchForm"
                                class="m-0 d-flex justify-content-between align-items-center" style="width:100%;">
                                <div class="d-flex justify-content-between w-100">
                                    <div class="d-flex justify-content-start ">
                                       <div class="col-md-4 pl-0 d-flex justify-content-start">
                                            <input type="text" name="search" placeholder="Search by Name, Category name"
                                            class="form-control mr-2 w-100" value="{{ request('search') }}"
                                            data-toggle="tooltip" data-placement="top"
                                            title="Search by document name, category name">
                                            <button type="submit" class="btn btn-primary ml-2">Search</button>
                                        </div>

                                            <!-- File Type Filter -->
                                            <div class="col-md-2 pl-0">
                                                <select name="type" id="type" class="form-control" onchange="this.form.submit()">
                                                    <option value="">File Type</option>
                                                    <option value="docx" {{ in_array(request('type'), ['doc', 'docx']) ? 'selected' : '' }}>Doc/Docx</option>
                                                    <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>Pdf</option>
                                                </select>
                                            </div>

                                            <!-- Status Filter -->
                                            <div class="col-md-3 pl-0">
                                                <select name="status" id="status" class="form-control"
                                                    onchange="this.form.submit()">
                                                    <option value="">Category Status</option>
                                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Category Filter -->
                                            <div class="col-md-2 pl-0">
                                                <select name="category" id="category" class="form-control"
                                                    onchange="this.form.submit()">
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }} title="{{ $category->name }}">
                                                        {{ Str::limit($category->name, 20) }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                    </div>
                                    <div class="d-flex justify-content-end">
                                          <a href="{{ route('document.list') }}">
                                                <button class="btn btn-warning" type="button" id="submit-disable">
                                                    Reset
                                                </button>
                                            </a>                                        
                                    </div>
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="text-black">
                            <tr>
                                <th>S.No</th>
                                <th>Document Name</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Created Date</th>
                                <th>Category Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $index = ($document->currentPage() - 1) * $document->perPage() + 1; ?>
                            @forelse ($document as $data)
                                <tr>
                                    <td>{{$index++}}</td>
                                    <td>
                                        <span title="{{ $data->files ? basename($data->files) : '-' }}">
                                            {{ Str::limit(basename($data->files), 30) }}
                                        </span>
                                    </td>
                                    <td> <span class="docbg">{{ $data->file_type ? strtoupper($data->file_type) : '-' }}</span></td>
                                    <td>
                                    <span title="{{ $data->category_name ? basename($data->category_name) : '-' }}">
                                            {{ Str::limit(basename($data->category_name), 15) }}
                                        </span>
                                        </td>
                                    <!-- <td>{{ $data->category_name ?? '-' }}</td> -->
                                    <td>{{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->format('M-j-Y') : '-' }}
                                    </td>
                                    <!-- <td>{{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->format('m/d/Y') : '-' }} -->
                                    </td>
                                    <td>
                                        @if($data->category->status == 1)
                                            <span class="active">Active</span>
                                        @else
                                            <span class="inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="border-none" type="button"
                                             onclick="viewDocument('{{ route('view.document', ['id' => $data->id, 'category_id' => $data->category_id]) }}', '{{ basename($data->files) }}')"
                                            data-toggle="tooltip" data-placement="top" title="View File">
                                            <img src="{{  asset('vendor/laradocs/images/view.png') }}" alt="IMG">
                                        </button>

                                        <form action="{{ route('download.document') }}" method="GET" class="d-inline">
                                            <input type="hidden" name="category_id" value="{{ $data->category_id }}">
                                            <input type="hidden" name="document_id" value="{{ $data->id }}">
                                            <button class="border-none" type="submit" id="download-document"
                                                data-toggle="tooltip" data-placement="top" title="Download File">
                                                <img src="{{  asset('vendor/laradocs/images/download.png') }}" alt="IMG">
                                            </button>
                                        </form>
                                        <button class="border-none delete-button" type="button"
                                            data-document-id="{{ $data->id }}" data-category-id="{{ $data->category_id }}"
                                            data-toggle="tooltip" data-placement="top" title="Delete File">
                                            <img src="{{  asset('vendor/laradocs/images/elete.png') }}" alt="IMG">
                                        </button>

                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="nodata">
                                        <p><img src="{{  asset('vendor/laradocs/images/pload.png') }}" alt="IMG"></p>
                                        No data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination links -->
                <div class="mt-3">
                    {{$document->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for displaying PDF -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header pb-0">
                <h5 class="modal-title" id="pdfModalLabel">File</h5>
                <button type="button" class="btn-close" data-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <iframe id="pdfFrame" src="" width="100%" height="500px" style="border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog"
    aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
               <p> <b>Are you sure you want to delete this file?</b> </p> 
            </div> 
            <div class="d-flex justify-content-center align-items-center">
            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger mx-1" id="confirmDelete">Delete</button> 
            </div>            
                                             
        </div>
    </div>
</div>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script>
    $(document).ready(function () {
        $('.delete-button').click(function () {
            // Get data attributes from the button
            var documentId = $(this).data('document-id');
            var categoryId = $(this).data('category-id');

            // Store the parameters as data attributes on the confirm button
            $('#confirmDelete')
                .data('document-id', documentId)
                .data('category-id', categoryId)

            // Show the modal
            $('#deleteConfirmationModal').modal('show');
        });
        $('#deleteConfirmationModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var url = button.data('url');
            var modal = $(this);
            modal.find('#confirmDelete').attr('href', url);
        });
        $('#confirmDelete').click(function () {
            var documentId = $(this).data('document-id');
            var categoryId = $(this).data('category-id');
            console.log(documentId, categoryId);
            $.ajax({
                url: '{{ route('delete.document') }}',
                type: 'GET',
                data: {
                    documentId: documentId,
                    categoryId: categoryId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    $('#deleteConfirmationModal').modal('hide');
                    toastr.success("File deleted successfully.");
                    location.reload();
                },
                error: function (xhr) {
                    toastr.error('An error occurred while deleting the file. Please try again.');
                }
            });
        });
    });
    function viewDocument(documentUrl,fileName) {
        $.ajax({
            url: documentUrl,
            type: 'GET',
            success: function (response) {
                var fileUrl = response.url;
                var encodedUrl = encodeURIComponent(fileUrl);
                var iframe = document.getElementById('pdfFrame');
                iframe.src = '';

                // Display PDFs directly; use Google Docs Viewer for .docx files
                if (fileUrl.endsWith('.pdf')) {
                    iframe.src = fileUrl;
                } else if (fileUrl.endsWith('.docx')) {
                    iframe.src = 'https://docs.google.com/gview?url=' + encodedUrl + '&embedded=true';
                }
                else if (fileUrl.endsWith('.doc')) {
                    iframe.src = 'https://docs.google.com/gview?url=' + encodedUrl + '&embedded=true';
                }
                $('#pdfModalLabel').text(fileName);
                $('#pdfModal').modal('show');
            },
            error: function () {
                toastr.error('An error occurred while retrieving the file. Please try again.');
            }
        });
    }


</script>
@endsection