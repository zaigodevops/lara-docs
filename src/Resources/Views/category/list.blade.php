@php
    use Illuminate\Support\Str;
@endphp
@extends('larasnap::layouts.app', ['class' => ''])
<link rel="stylesheet" href="{{ asset('vendor/laradocs/css/styles.css') }}">
@section('content')
<style>
.modal-dialog {
    position: absolute;
    width: auto;
    margin: 0;
    pointer-events: none;
    right: 0;
    max-width: 650px!important;
    width: 650px;
    height: 100%;
}
.modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: 0;
    outline: 0;
    height: 100%;
}
.modal.fade .modal-dialog {
    transition: transform .3s ease-out;
    transform: translate(0,0px)!important;
}
</style>
<!-- Page Heading  Start-->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 nhead">Category</h1>
    @canAccess('category.create')
                    <button class="btn btn-primary" type="button" id="submit-disable">
                        + Add New Category
                    </button>
                    @endcanAccess
</div>
<!-- Page Heading End-->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <form method="GET" action="{{ route('category.index') }}" id="searchForm"
                        class="m-0 d-flex justify-content-between align-items-center" style="width:100%;">
                        <div class="d-flex justify-content-start">
                            <input type="text" name="search" placeholder="Search by category name" class="form-control mr-2 w-100"
                                value="{{ request('search') }}" data-toggle="tooltip" data-placement="top"
                                title="Search by category name">
                                <button type="submit" class="btn btn-primary ml-2">Search</button>
                            <div class="col-md-3">
                                <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">Select Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input class="form-control dateicon" type="text" id="date" name="date" value="{{ request('date') }}"
                                    placeholder="Created Date" onchange="this.form.submit()">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('category.index') }}"><button class="btn btn-warning"
                            type="button">Reset</button></a>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="text-black">
                                <tr>
                                    <th>S.No</th>
                                    <th>Category Name</th>
                                    <th>Created Date</th>
                                    <th>Category Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $index = ($category->currentPage() - 1) * $category->perPage() + 1; ?>
                                @forelse ($category as $data)
                                <tr>
                                    <td>{{$index++}}</td>
                                    <td>
                                    <span title="{{ $data->name ? basename($data->name) : '-' }}">
                                            {{ Str::limit(basename($data->name), 30) }}
                                        </span></td>
                                    <!-- <td>{{ $data->name}}</td> -->
                                    <!-- <td>{{ \Carbon\Carbon::parse($data->created_at)->format('m/d/Y') }}</td> -->
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('M-j-Y') }}</td>
                                    <td>
                                        @if($data->status == 1)
                                        <span class="active">Active</span>
                                        @else
                                        <span class="inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @canAccess('category.update')
                                        <button class="edit-category border-none" type="button" id="edit_category"
                                            data-id="{{ $data->id }}" data-name="{{ $data->name }}" data-status="{{ $data->status }}" data-toggle="tooltip"
                                            data-placement="top" title="Edit Category">
                                            <img src="{{  asset('vendor/laradocs/images/edit.png') }}" alt="IMG">
                                        </button>
                                        @endcanAccess
                                        @if($data->status == 1)
                                        @canAccess('category.delete')
                                        <button class="delete-category border-none" type="button" id="delete_category"
                                            data-id="{{ $data->id }}" data-name="{{ $data->name }}" data-toggle="tooltip"
                                            data-placement="top" title="Delete Category">
                                            <img src="{{  asset('vendor/laradocs/images/delete.png') }}" alt="IMG">
                                        </button>
                                        @endcanAccess
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center align-middle">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Pagination links -->
                        <div class="mt-3">
                            {{$category->links('pagination::bootstrap-4')}}
                        </div>
                    </div>
                
            </div>
        </div>
    </div>
</div>


<!-- Modal for Adding Category -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark" id="addCategoryModalLabel">Add New Category</h5>
                <button type="button" class="btn-close" data-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <form id="addCategoryForm">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label control-lable">Category <small class="text-danger required">*</small></label>
                        <input type="text" class="form-control" id="categoryName" name="name" placeholder="Enter category name">
                    </div>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Editing Category -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="btn" data-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm">
                    <input type="hidden" id="editCategoryId" name="id">
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label">Category Name <small class="text-danger required">*</small></label>
                        <input type="text" class="form-control" id="editCategoryName" name="categoryname">
                    </div>
                    <div class="mb-3">
                        <label for="editCategoryStatus" class="form-label">Category Status <small class="text-danger required">*</small></label>
                        <select name="status" id="editCategoryStatus" class="form-control" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Delete Category -->
<div class="modal fade delete-modal" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <h4 class="modal-title text-dark" id="deleteCategoryModalLabel">Delete Category</h4>
                <!-- <button type="button" class="btn" data-dismiss="modal">X</button> -->
            </div>
            <div class="modal-body">
                <form id="deleteCategoryForm">
                    <input type="hidden" id="deleteCategoryId" name="id">
                    <p class="my-3">If you delete this category, <span class="text-primary">
                        <b>"you will no longer be able to access any documents related to it."</b>
                    </span> Are you sure you want to proceed with the deletion?</p>
                    <div class="d-flex justify-content-center align-items-center mt-4">
                        <button type="submit" class="btn btn-success mx-1">Yes</button>
                        <button type="submit" class="btn btn-danger mx-1" data-dismiss="modal">No</button>
                    </div>                   
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade success-model" id="successpopup" tabindex="-1" role="dialog" aria-labelledby="successpopup"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="text-center">
                                <p><img style="width: 115px;"
                                        src="{{ asset('vendor/larasnap-auth/images/success-icon.png') }}" alt="IMG"></p>
                                <h1>New Category Added</h1>
                                <p>You can now manage or view the category in the list.</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary mb-2" id="confirm">Close</button>
                    </div>
                </div>
            </div>


<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(function() {
        flatpickr("#date", {
            dateFormat: "m/d/Y",
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Add Category 
        $('#submit-disable').click(function() {
            $('#addCategoryModal').modal('show');
        });

        //Edit Category
        $('.edit-category').click(function() {
            const categoryId = $(this).data('id');
            const categoryName = $(this).data('name');
            const categoryStatus = $(this).data('status');

            $('#editCategoryId').val(categoryId);
            $('#editCategoryName').val(categoryName);
            $('#editCategoryStatus').val(categoryStatus);
            $('#editCategoryModal').modal('show');
        });

        //Delete category
        $('.delete-category').click(function() {
            const categoryId = $(this).data('id');
            const categoryName = $(this).data('name');
            console.log(categoryId);

            $('#deleteCategoryId').val(categoryId);
            $('#deleteCategoryName').val(categoryName);
            $('#deleteCategoryModal').modal('show');
        });

        //Add Category Ajax
        $('#addCategoryForm').submit(function(e) {
            e.preventDefault();

            const categoryName = $('#categoryName').val();
            console.log(categoryName);

            $.ajax({
                url: '{{ route("category.create") }}',
                method: 'POST',
                data: {
                    name: categoryName,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#addCategoryModal').modal('hide');
                    $('#successpopup').modal('show');
                    // toastr.success('d-none').text('Category created successfully');
                    // location.reload();
                },
                error: function(xhr) {
            if (xhr.status === 422) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('An error occurred during creation');
            }
        }
            });
        });

        $('#confirm').click(function () {
            window.location.href = "{{ route('category.index') }}";
        });

        //Edit Category Ajax
        $('#editCategoryForm').submit(function(e) {
            e.preventDefault();

            const categoryId = $('#editCategoryId').val();
            const categoryName = $('#editCategoryName').val();
            const categoryStatus = $('#editCategoryStatus').val();


            $.ajax({
                url: `category/update/${categoryId}`,
                method: 'POST',
                data: {
                    name: categoryName,
                    status: categoryStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#editCategoryModal').modal('hide');
                    toastr.success('Category updated successfully');
                    location.reload();
                },
                error: function(xhr) {  
            if (xhr.status === 422) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('An error occurred during creation');
            }
                }
            });
        });

        //Delete Category Ajax
        $('#deleteCategoryForm').submit(function(e) {
            e.preventDefault();

            const categoryId = $('#deleteCategoryId').val();
            const categoryName = $('#deleteCategoryName').val();
            console.log(categoryId);

            $.ajax({
                url: `category/delete/${categoryId}`,
                method: 'POST',
                data: {
                    name: categoryName,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#deleteCategoryModal').modal('hide');
                    toastr.success('Category removed successfully');
                    location.reload();
                },
                error: function(xhr) {
                    toastr.error('An error occurred during delete');
                }
            });
        });

    });
</script>

@endsection