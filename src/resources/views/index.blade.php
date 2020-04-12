@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')


    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="row row-cards row-deck " >
                <a href="#" class="btn btn-primary col-md-2 ml-auto mb-2" data-toggle="modal" data-target="#approval-add-modal"  >
                    Add  Approval
                </a>
                @if (auth()->user()->is_employee !== 1)
                    <div class="col-sm-12" id="approvals">
                        @if(!empty($approvals))
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                                       data-pagination="true"
                                       data-search="true"
                                       data-side-pagination="server"
                                       data-show-refresh="true"
                                       data-unique-id="id"
                                       data-id-field="id"
                                       data-row-attributes="processRows"
                                       data-url="{{ route('approval-search') . '?' . http_build_query($args) }}"
                                       data-page-list="[10,25,50,100,200,300,500]"
                                       data-sort-class="sortable"
                                       data-search-on-enter-key="true"
                                       id="approvals-table"
                                       v-on:click="clickAction($event)">
                                    <thead>
                                    <tr>
                                        <th data-field="title">Title</th>
                                        <th data-field="scope_type">Scope </th>
                                        <th data-field="frequency_type">Frequency of Delivery </th>
                                        <th data-field="created_at">Added On</th>
                                        <th data-field="buttons">Action</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>

                        @else
                            <div class="col-md-12" >
                                @component('layouts.blocks.tabler.empty-fullpage')
                                    @slot('title')
                                        No Approval  Generated
                                    @endslot
                                    <a href="#" class="btn btn-primary" v-on:click.prevent="showApproval">Add Approval </a>
                                    &nbsp;
                                    @slot('buttons')
                                    @endslot
                                @endcomponent

                            </div>
                        @endif
                        @include('modules-people-approval::modals.edit-approval')
                        @include('modules-people-approval::.modals.add-approval')
                    </div>
                @endif
{{--                    <div class="col-sm-8">--}}
{{--                        @component('layouts.blocks.tabler.empty-fullpage')--}}
{{--                            @slot('title')--}}
{{--                                 View Your Approval Requests--}}
{{--                            @endslot--}}
{{--                            @slot('buttons')--}}
{{--                            @endslot--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="card-text">--}}
{{--                                    Approval Request: Grant Approvals--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="row row-cards row-deck">--}}
{{--                                <div class="col-md-4 mt-4">--}}
{{--                                    <a href="{{route('approval-request-authorizers')}}" class="btn btn-primary text-white">Approval Request </a>--}}


{{--                                </div>--}}

{{--                            </div>--}}
{{--                        @endcomponent--}}
{{--                    </div>--}}
            </div>

        </div>
    </div>

@endsection
@section('body_js')
    <script>
        app.currentUser = {!! json_encode($dorcasUser) !!};
        const table = $('.bootstrap-table');
        let Approval =  new Vue({
            el: '#approvals',
            data:{
                {{--approvals: {!! $approvals !!},--}}
                form_data:{
                    title:null,
                    scope_type: null,
                    frequency_type: null,
                    status: null,
                    approval_id: null,
                }
            },
            methods:{
                submitForm: function () {
                    $('#submit-approval').addClass('btn-loading btn-icon')
                    // console.log(this.form_data)
                    axios.post('/mpe/approval',this.form_data)
                        .then(response=>{
                            $('#submit-approval').removeClass('btn-loading btn-icon')
                            this.form_data = {};
                            $('#approval-add-modal').modal('hide')
                            swal({
                                title:"Success!",
                                text:" Approval Successfully Created",
                                type:"success",
                                showLoaderOnConfirm: true,
                            }).then(function () {
                                location.reload()
                            });
                        })
                        .catch(e=>{
                            console.log(e.response.data);
                            $('#submit-approval').removeClass('btn-loading btn-icon')

                            swal.fire({
                                title:"Error!",
                                text:e.response.data.message,
                                type:"error",
                                showLoaderOnConfirm: true,
                            });
                        })
                },
                showApproval: function(){
                    $('#approval-add-modal').modal('show')
                },
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
                        case 'delete_approval':
                            this.deleteApproval(id,index,name);
                            break;
                        case 'edit_approval':
                            this.editApproval(id,index,name);
                            break;
                    }

                },
                async editApproval(id)
                {
                    const self = this;
                    await axios.get("/mpe/approval/single/" + id)
                        .then(function (response) {
                            self.form_data = {
                                title:response.data[0].title,
                                scope_type:response.data[0].scope_type,
                                frequency_type:response.data[0].frequency_type,
                                status:response.data[0].active,
                                approval_id:response.data[0].id,
                            }
                            $('#approval-edit-modal').modal('show')

                        })
                        .catch(function (error) {
                            var message = '';
                            console.log(error);
                            swal.fire({
                                title:"Error!",
                                text:error.response.data,
                                type:"error",
                                showLoaderOnConfirm: true,
                            });
                        });

                    console.log(self.form_data)

                },
                deleteApproval(id,index,name){
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to delete  " + name + " from this Approval.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return axios.delete("/mpe/approval/" + id)
                                .then(function (response) {
                                    $('#approvals-table').bootstrapTable('removeByUniqueId', response.data.id);
                                    return swal("Deleted!", "The Approval was successfully deleted.", "success");
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
                updateApproval: function () {
                    $('#edit-approval').addClass('btn-loading btn-icon')
                    axios.put('/mpe/approval/'+this.form_data.approval_id,this.form_data)
                        .then(response=>{
                            $('#edit-approval').removeClass('btn-loading btn-icon')
                            form_data = {};
                            $('#approval-edit-modal').modal('hide')

                            swal({
                                title:"Success!",
                                text:"Approval  Successfully Updated",
                                type:"success",
                                showLoaderOnConfirm: true,
                            }).then(function () {
                                location.reload()
                            });
                        })
                        .catch(e=>{
                            console.log(e.response.data);
                            $('#edit-approval').removeClass('btn-loading btn-icon')
                            swal.fire({
                                title:"Error!",
                                text:e.response.data.message,
                                type:"error",
                                showLoaderOnConfirm: true,
                            });
                        })
                },
            },
            mounted(){
            }
        })

        function processRows(row, index) {
            row.created_at = moment(row.created_at).format('DD MMM, YYYY');
            row.buttons = '<a class="btn btn-sm btn-primary text-white"  data-index="'+index+'"  data-action="edit_approval" data-id="'+row.id+'" data-name="'+row.name+'">Update</a> &nbsp; ' +
                '<a class="btn btn-sm btn-danger text-white"   data-index="'+index+'" data-action="delete_approval" data-id="'+row.id+'" data-name="'+row.name+'">Delete</a> &nbsp;' +
                '<a class="btn btn-sm btn-cyan text-white"    href="/mpe/approval/authorizers/'+row.id+'">  Manage Authorizers</a> &nbsp';

            if(row.active !== 1){
                row.buttons = '<a class="btn btn-sm btn-primary text-white"  data-index="'+index+'"  data-action="edit_approval" data-id="'+row.id+'" data-name="'+row.name+'">Update</a> &nbsp; ' +
                    '<a class="btn btn-sm btn-danger text-white"   data-index="'+index+'" data-action="delete_approval" data-id="'+row.id+'" data-name="'+row.name+'">Delete</a> &nbsp;';
            }
                // '<a class="btn btn-sm btn-cyan text-white"    href="/mpe/approval/requests/'+row.id+'">Requests</a>';
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
