@extends('layouts.tabler')
@section('body_content_header_extras')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')

    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="row row-cards row-deck " >
                <div class="col-sm-12" id="approvals">
                    <div class="col s12" >
                        <form action="{{route('authorizer-update',['id'=>$authorizer->id])}}"    method="post">
                            {{csrf_field()}}
                            <fieldset>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <div class="form-group">
                                            <label class="form-label" for="transaction">Select Authorizer</label>
                                            <select   class="form-control  " id="select2"  name="employee_id" required >
                                                <option value="{{$authorizer->employees['data'][0]['id']}}"  selected >
                                                    {{$authorizer->employees['data'][0]['firstname']}}
                                                </option>
                                            @foreach($employees as $employee)
                                                    <option value="{{$employee->id}}"> {{$employee->firstname . ' ' . $employee->lastname}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="transaction">Approval Scope</label>

                                            <select   class="form-control  " id="select2"  name="approval_scope" required >
                                                <option value="{{$authorizer->approval_scope}}"  selected >{{$authorizer->approval_scope}}</option>
                                                <option value="critical">Critical</option>
                                                <option value="standard"> Standard</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <button type="submit"   class="btn btn-primary">Submit</button>

                            </fieldset>
                        </form>
                    </div>

                </div>



            </div>

        </div>
    </div>

@endsection
@section('body_js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>


        $("#select2").select2({
            matcher: function(params, data) {
                // If there are no search terms, return all of the data
                if ($.trim(params.term) === '') { return data; }

                // Do not display the item if there is no 'text' property
                if (typeof data.text === 'undefined') { return null; }

                // `params.term` is the user's search term
                // `data.id` should be checked against
                // `data.text` should be checked against
                var q = params.term.toLowerCase();
                if (data.text.toLowerCase().indexOf(q) > -1 || data.id.toLowerCase().indexOf(q) > -1) {
                    return $.extend({}, data, true);
                }

                // Return `null` if the term should not be displayed
                return null;
            }
        });
    </script>
@endsection

