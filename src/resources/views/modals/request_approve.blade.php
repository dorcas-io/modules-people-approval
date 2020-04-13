<div class="modal fade" id="request-approve-modal" tabindex="-1" role="dialog" aria-labelledby="entries-add-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="body">
                <p class="m-3">Are You Sure you want to Approve this request</p>

                <form action="{{route('request-action')}}"  id="request_approve" method="post">
                    {{csrf_field()}}
                    <input type="hidden" value="{{auth()->user()->id}}" name="user_id">
                    <input type="hidden" value="{{$request_id}}" name="request_id">
                    <input type="hidden" value="1" name="status">
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit"  name="action"  form="request_approve" class="btn btn-primary">Yes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
