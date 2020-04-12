<div class="modal fade" id="authorizer-add-modal" tabindex="-1" role="dialog" aria-labelledby="entries-add-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add  Authorizer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('authorizer-create')}}"  id="approval_add" method="post">
                     {{csrf_field()}}
                    <fieldset>
                        <div class="row">
                            <div class="form-group col-md-12">
                                    <label class="form-label" for="transaction">Select leave types for this group </label>
                                    <select  id="select-tags-advanced"  class="form-control" multiple required>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}"> {{$user->firstname. ' '. $user->lastname }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="users" name="users">

                            </div>
                            <div class="form-group col-md-12">
                                <input name="approval_id" value="{{$approval->id}}" type="hidden"/>
                                <p>Choose approval scope for the above selected authorizer(s)</p>
                                <label class="form-label" for="transaction">Select  Approval Scope </label>
                                <select   class="form-control custom-select selectized" tabindex="-1"  name="approval_scope" required >
                                    <option selected :value="undefined" disabled>Select Frequency </option>
                                    <option value="critical" >Critical Authorizer </option>
                                    <option value="standard" >Standard Authorizer </option>
                                </select>

                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit"  name="action" id="submit-approval" form="approval_add" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
