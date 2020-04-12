@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')


    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="row row-cards row-deck " >
                <div class="col-sm-12" id="requests">
                    @if(!empty($requests))
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                                   data-pagination="true"
                                   data-search="true"
                                   data-side-pagination="server"
                                   data-show-refresh="true"
                                   data-unique-id="id"
                                   data-id-field="id"
                                   data-row-attributes="processRows"
                                   data-url="{{ route('search-requests') . '?' . http_build_query($args) }}"
                                   data-page-list="[10,25,50,100,200,300,500]"
                                   data-sort-class="sortable"
                                   data-search-on-enter-key="true"
                                   id="requests-table"
                                   v-on:click="clickAction($event)">
                                <thead>
                                <tr>
                                    <th data-field="approval_name">Approval Name</th>
                                    <th data-field="status">Status </th>
                                    <th data-field="created_at">Added On</th>
{{--                                    <th data-field="buttons">Action</th>--}}
                                </tr>
                                </thead>
                            </table>
                        </div>
                    @else
                        <div class="col s12" >
                            @component('layouts.blocks.tabler.empty-fullpage')
                                @slot('title')
                                    No Requests  Generated
                                @endslot
                                @slot('buttons')
                                @endslot
                            @endcomponent
                        </div>
                    @endif

                </div>



            </div>

        </div>
    </div>

@endsection
@section('body_js')
    <script>
        let Requests =  new Vue({
            el: '#requests',
            data:{
            },
            methods:{

                clickAction: function (event) {
                    let target = event.target;
                    if (!target.hasAttribute('data-action')) {
                        target = target.parentNode.hasAttribute('data-action') ? target.parentNode : target;
                    }

                    let action = target.getAttribute('data-action');
                    let name = target.getAttribute('data-name');
                    let id = target.getAttribute('data-id');
                    let index = parseInt(target.getAttribute('data-index'), 10);
                    switch (action) {
                        case 'view':
                            return true;
                    }

                },


            },
            mounted(){
            }
        })
        function processRows(row, index) {
            console.log(row)
            row.approval_name = row.approval_title;
            row.status = (row.approval_status === 0 ? 'Not Approved' : 'Approved');
            row.created_at = moment(row.created_at).format('DD MMM, YYYY');
                '<a class="btn btn-sm btn-danger text-white"   data-index="'+index+'" data-action="delete_approval" data-id="'+row.id+'" data-name="'+row.employee_name+'">Delete</a> &nbsp;';
            // row.account_link = '<a href="/mfn/finance-entries?account=' + row.account.data.id + '">' + row.account.data.display_name + '</a>';
            // row.created_at = moment(row.created_at).format('DD MMM, YYYY');
            // row.buttons = '<a class="btn btn-danger btn-sm remove" data-action="remove" href="#" data-id="'+row.id+'">Delete</a>';
            // if (typeof row.account.data !== 'undefined' && row.account.data.name == 'unconfirmed') {
            //     row.buttons += '<a class="btn btn-warning btn-sm views" data-action="views" href="/mfn/finance-entries/' + row.id + '" >Confirm</a>'
            // }
            // return row;
        }
    </script>
@endsection