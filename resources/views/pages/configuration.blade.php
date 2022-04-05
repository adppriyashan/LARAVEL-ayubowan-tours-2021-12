@extends('layouts.app')

@section('content')

    @include('layouts.navbar')
    @include('layouts.sidebar')
    <!-- END: Main Menu-->
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Revenue, Hit Rate & Deals -->
                <div class="row">
                    @include('layouts.flash')
                     <div class="col-md-12">
                        <form action="{{ route('admin.configutation.enroll') }}" method="POST" id="configuration_form">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Configuration Manager</h4>
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
                                            <div class="col-md-4">
                                                <label for="lkr_value"><small class="text-dark">LKR value of 1 USD
                                                        {!! required_mark() !!}</small></label>
                                                <input type="number" name="lkr_value" id="lkr_value"
                                                    class="form-control" placeholder="Type LKR value" value="{{ $data->lkrvalueofusd }}">
                                                @error('lkr_value')
                                                    <span class="text-danger"><small>{{ $message }}</small></span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="show_currency"><small class="text-dark">Currency Manager
                                                        {!! required_mark() !!}</small></label>
                                                <select class="form-control" name="show_currency" id="show_currency">
                                                    <option {{ ($data->showcurrency==1)?'selected':'' }} value="1">Show</option>
                                                    <option {{ ($data->showcurrency==2)?'selected':'' }} value="2">Hide</option>
                                                </select>
                                                @error('show_currency')
                                                    <span class="text-danger"><small>{{ $message }}</small></span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="list_count"><small class="text-dark">Invoice List Records Count
                                                        {!! required_mark() !!}</small></label>
                                                <select class="form-control" name="list_count" id="list_count">
                                                    <option {{ ($data->listrecordscount==10)?'selected':'' }} value="10">10</option>
                                                    <option {{ ($data->listrecordscount==25)?'selected':'' }}  value="25">25</option>
                                                    <option {{ ($data->listrecordscount==50)?'selected':'' }}  value="50">50</option>
                                                </select>
                                                @error('list_count')
                                                    <span class="text-danger"><small>{{ $message }}</small></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row ">
                                            <div class="col-md-6"></div>
                                            <div class="col-md-3"> <input id="submitbtn"
                                                    class="btn btn-success w-100" type="submit" value="Submit">
                                            </div>
                                            <div class="col-md-3 mt-md-0 mt-1"><input class="btn btn-danger w-100"
                                                    type="button" form="configuration_form" id="resetbtn" value="Reset"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>

    @include('layouts.footer')
    @include('layouts.scripts')

@endsection
