<div class="modal fade" id="approval-edit-modal" tabindex="-1" role="dialog" aria-labelledby="entries-add-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update  Approval</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" @submit.prevent="updateApproval()"  id="approval_edit" method="put">
                    <fieldset>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label" for="approval">Name</label>
                                <input class="form-control" id="approval" v-model="form_data.title" placeholder="Enter Approval Title " type="text" required>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label" for="transaction">Select  Scope</label>
                                <select   class="form-control custom-select selectized" tabindex="-1"  v-model="form_data.scope_type" required >
                                    <option selected :value="form_data.scope_type" disabled >Select  Scope</option>
                                    <option value="min_number" > Minimum Number of People</option>
                                    <option value="key_person" > Key Person Approval</option>
                                    <option value="both" > Both Minimum Number and Critical </option>
                                </select>

                            </div>
                            <div class="form-group col-md-12">
                                <p>Choose How you want the approvals to be delivered to appropriate Authorities</p>
                                <label class="form-label" for="transaction">Select  Frequency </label>
                                <select   class="form-control custom-select selectized" tabindex="-1"  v-model="form_data.frequency_type" required >
                                    <option selected :value="form_data.frequency_type" disabled>Select Frequency </option>
                                    <option value="sequential" >Sequential </option>
                                    <option value="random" >Random</option>
                                </select>

                            </div>
                            <div class="form-group col-md-12">
                                <label class="custom-switch">
                                    <input type="checkbox" v-model="form_data.status" class="custom-switch-input" :checked="form_data.status">
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">Status</span>
                                </label>
                            </div>

                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit"  name="action" id="edit-approval" form="approval_edit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
