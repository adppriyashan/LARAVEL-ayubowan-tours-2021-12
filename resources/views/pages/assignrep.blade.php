@extends('layouts.app')

@section('content')

    @include('layouts.navbar')
    @include('layouts.sidebar')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="row">
                    @include('layouts.flash')
                    @if(doPermitted('//representatives/assign/enroll'))
                    <div class="col-md-8">
                    @else
                    <div class="col-md-12">
                    @endif
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Assigned Representatives</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a onclick="refreshTable()" data-action="reload"><i
                                                    class="ft-rotate-cw"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body pt-0">
                                    <div class="table-responsive">
                                        <table class="table w-100" id="representativeAssignTable">
                                            <thead>
                                                <tr>
                                                    <th>Representative</th>
                                                    @if(doPermitted('//representatives/assign/delete'))
                                                    <th>Actions</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(doPermitted('//representatives/assign/enroll'))
                    <div class="col-md-4">
                        <form action="{{ route('admin.representatives.assign.enroll') }}" method="POST" id="representative_form"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="isnew" name="isnew" value="{{ old('isnew') ? old('isnew') : '1' }}">
                            <input type="hidden" id="record" name="record"
                                value="{{ old('record') ? old('record') : '' }}">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Enrollment </h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><label for="resetbtn"><a data-action="reload"><i
                                                            class="ft-rotate-cw"></i></a></label></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="representative"><small
                                                    class="text-dark">Representative{!! required_mark() !!}</small></label>
                                                <br>
                                                <select class="form-control select2reset"
                                                    name="representative" id="representative">
                                                    <option selected disabled></option>
                                                    @foreach ($representatives as $representative)
                                                        <option
                                                            {{ old('representative') == $representative->id ? 'selected' : '' }}
                                                            value="{{ $representative->id }}">
                                                            {{ $representative->first_name }}
                                                            {{ $representative->last_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('representative')
                                                    <span class="text-danger"><small>{{ $message }}</small></span>
                                                @enderror
                                            </div>
                                        </div>
                                         <div class="row mt-1">
                                            <div class="col-md-12">
                                                <label for="pin"><small
                                                        class="text-dark">Pin{!! required_mark() !!}</small></label>
                                                <input type="password" value="{{ old('pin') }}" name="pin" id="pin"
                                                    class="form-control" placeholder="Enter Pin Number">
                                                @error('pin')
                                                    <span class="text-danger"><small>{{ $message }}</small></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-md-6"> <input id="submitbtn" class="btn btn-success w-100"
                                                    type="submit" value="Enroll">
                                            </div>
                                            <div class="col-md-6 mt-md-0 mt-1"><input class="btn btn-danger w-100"
                                                    type="button" form="representative_form" id="resetbtn" value="Reset">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    </div>
    <!-- END: Content-->




    @include('layouts.footer')
    @include('layouts.scripts')

    <script>
    
        $('#representative').select2({
            placeholder: 'Select Representative',
            allowClear: true
        });
    
        let listTable = $('#representativeAssignTable').DataTable({
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            serverSide: true,
            responsive: true,
            language: {
                searchPlaceholder: "Search By Name"
            },
            ajax: {
                url: '{{ route('admin.representatives.list') }}',
                data: {
                    'typeactions':2
                }
            },
            columns: [{
                    name: 'first_name'
                },
                @if(doPermitted('//representatives/assign/delete'))
                {
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
                @endif
            ],
            createdRow: function(row, data, dataIndex, cells) {
                $(cells).addClass(' align-middle datatables-sm');
            }
        });


        function doDelete(id) {
            showAlert('Are you sure to delete this record ?', function() {
                window.location = "{{ route('admin.representatives.assign.delete.one') }}?id=" + id;
            });
        }

        @if (old('record'))
            $('#record').val({{ old('record') }});
        @endif

        @if (old('isnew'))
            $('#isnew').val({{ old('isnew') }}).trigger('change');
        @endif
    </script>


@endsection
