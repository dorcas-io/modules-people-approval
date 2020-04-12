@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')

    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="row row-cards row-deck " >
                <div class="col-sm-12" id="request">
                    <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                                   data-pagination="true"
                                   data-search="true"
                                   data-side-pagination="server"
                                   data-show-refresh="true"
                                   data-unique-id="id"
                                   data-id-field="id"
                                   data-row-attributes="processRows"
                                   data-url="{{ route('search-requests-authorizers') . '?' . http_build_query($args) }}"
                                   data-page-list="[10,25,50,100,200,300,500]"
                                   data-sort-class="sortable"
                                   data-search-on-enter-key="true"
                                   id="request-table"
                                   v-on:click="clickAction($event)">
                                <thead>
                                <tr>
                                    <th data-field="title">Approval</th>
                                    <th data-field="scope_type">Scope</th>
{{--                                    <th data-field="leave_type">Leave Type</th>--}}
{{--                                    <th data-field="days_utilized">Days Utilized</th>--}}
{{--                                    <th data-field="days_remaining">Days Remaining</th>--}}
{{--                                    <th data-field="days_requesting">Days Requesting</th>--}}
{{--                                    <th data-field="start_date">Start Date</th>--}}
{{--                                    <th data-field="report_back">Reporting  Back</th>--}}
{{--                                    <th data-field="created_at">Added on </th>--}}
                                    <th data-field="buttons">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>

                </div>



            </div>

        </div>
    </div>

@endsection
@section('body_js')
    <script>
        let LeaveGroup =  new Vue({
            el: '#request',
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
                        case 'delete_leave_request':
                            this.deleteLeaveGroup(id,index,name);
                            break;
                    }

                },
                deleteLeaveGroup(id,index,name){
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to delete Leave Reqyest",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return axios.delete("/mpe/leave-request/" + id)
                                .then(function (response) {
                                    $('#request-table').bootstrapTable('removeByUniqueId', response.data.id);
                                    return swal("Deleted!", "The Leave Group was successfully deleted.", "success");
                                }).catch(function (error) {
                                    var message = '';
                                    console.log(error);
                                    swal.fire({
                                        title:"Error!",
                                        text:error.response.data.message,
                                        type:"error",
                                        showLoaderOnConfirm: true,
                                    });
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading()


                    });
                },

            },
            mounted(){
            }
        })
        function processRows(row, index) {
            console.log(row)
            row.report_back = moment(row.report_back).format('DD MMM, YYYY');
            row.leave_type = row.groups.data[0].leavetypes.data[0]['title']
            row.start_date = moment(row.start_date).format('DD MMM, YYYY');
            row.created_at = moment(row.created_at).format('DD MMM, YYYY');
            row.buttons = '<a class="btn btn-sm btn-primary text-white"   href="/mpe/leave-request/update/'+row.id+'">Update</a> &nbsp; ' +
                '<a class="btn btn-sm btn-danger text-white"   data-index="'+index+'" data-action="delete_leave_request" data-id="'+row.id+'" >Delete</a> &nbsp;';
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