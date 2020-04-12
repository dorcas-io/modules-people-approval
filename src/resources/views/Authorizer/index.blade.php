@extends('layouts.tabler')
@section('body_content_header_extras')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link href="{{cdn('zvendors/Datatable/data-tables.min.css')}}"rel="stylesheet" type="text/css" />
    <link href="{{cdn('vendors/Datatable/data-tables.checkbox.min.css')}}"rel="stylesheet" type="text/css" />

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')


    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="row row-cards row-deck " >
                <a href="#" class="btn btn-primary col-md-2 ml-auto mb-2" data-toggle="modal" data-target="#authorizer-add-modal"  >
                    Add  Authorizer
                </a>
                <div class="col-md-12 align-items-start" >

                    <a class="btn btn-primary btn-pill m-4" href="{{route('approval-main')}}">
                        <span><i class="fe fe-arrow-left"></i></span>
                        Approvals Home
                    </a>
                </div>
                <div class="col-md-12" id="authorizers">
                        <div class="table-responsive">
                                <table class="table" id="authorizers-table" >
                                    <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Scope</th>
                                        <th>actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($approval->authorizers['data'] as $authorizer)
                                        <tr>
                                            <td>{{$authorizer['firstname']}}</td>
                                            <td>{{$authorizer['lastname']}}</td>
                                            <td>{{$authorizer['email']}}</td>
                                            <td>{{$authorizer['approval_scope']}} Authorizer</td>
                                            <td>
                                                <form method="post" action="{{route('authorizer-delete')}}">
                                                    {{csrf_field()}}
                                                    <input type="hidden" name="approval_id" value="{{$approval->id}}">
                                                    <input type="hidden" name="user" value="{{$authorizer['id']}}">
                                                    <button class="btn btn-danger" type="submit" >Remove </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                </div>



            </div>

        </div>
        @include('modules-people-approval::modals.add-authorizer')
    </div>

@endsection
@section('body_js')
    <script src="{{cdn('vendors/Datatable/data-tables.min.js')}}"></script>
    <script src="{{cdn('vendors/Datatable/data-tables.checkbox.min.js')}}"></script>
    <script src="{{cdn('vendors/Datatable/data-tables.bootstrap.min.js')}}"></script>
    <script>
        var table = $('#authorizers-table').DataTable({
            // 'ajax': '/lab/jquery-datatables-checkboxes/ids-arrays.txt',
            'select': {
                'style': 'multi'
            },
            'order': [[1, 'asc']]
        });

        $(document).ready(function () {
            $('#select-tags-advanced').selectize({
                plugins: ['remove_button'],
                onChange: function(value) {
                    $('#users').val(value)
                }
            });
        })
    </script>
    @endsection