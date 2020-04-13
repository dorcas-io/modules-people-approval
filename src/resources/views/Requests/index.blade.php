@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')
    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="col-md-12 align-items-start" >

                <a class="btn btn-primary btn-pill m-4" href="{{route('approval-main')}}">
                    <span><i class="fe fe-arrow-left"></i></span>
                    Approvals Home
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h1 class="card-header">Details of the request</h1>
                    <div class="row">
                        @foreach($request->model_data as $key=>$value)
                            <p class="col-md-6 p-3 border-bottom ">{{$key}}</p>
                            <p class="col-md-6 p-3 border-bottom ">{{$value}}</p>
                        @endforeach
                    </div>
                    <button class="btn  btn-outline-info"  data-toggle="modal" data-target="#request-approve-modal" >Approve Request</button>
                    <button class="btn btn-outline-danger float-right"  data-toggle="modal" data-target="#request-decline-modal" >Decline Request</button>
                    @include('modules-people-approval::modals.request_decline')
                    @include('modules-people-approval::modals.request_approve')
                </div>
            </div>


        </div>
    </div>

@endsection
@section('body_js')
    <script>

    </script>
@endsection