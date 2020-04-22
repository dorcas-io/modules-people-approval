<?php

namespace Dorcas\ModulesPeopleApproval\Http\Controllers;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use http\Exception\RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;

class ModulesPeopleApprovalController extends Controller
{
    public function __construct()
    {
        $this->data = [
            'page' => ['title' => config('modules-people.title')],
            'header' => ['title' => config('modules-people-approval.title')],
            'selectedMenu' => 'people-payroll-approvals',
            'submenuConfig' => 'navigation-menu.modules-people.sub-menu',
            'submenuAction' => ''
        ];

    }

    public function index(Request $request, Sdk $sdk){
        try {
//            if(auth()->user()->is_employee === 1){
//                return response(view('errors.404'), 404);
//            }
            $this->data['page']['title'] .= ' &rsaquo; Approvals';
            $this->data['header']['title'] = ' Approvals';
            $this->data['selectedSubMenu'] = 'people-payroll-approvals';
            $this->setViewUiResponse($request);
            $this->data['args'] = $request->query->all();
            $this->data['approvals'] = $this->getPeopleApprovals($sdk);
            $this->data['requests'] = $this->getRequests($sdk);
            switch ($this->data){
                case !empty($this->data['approvals']):
                    $this->data['submenuAction'] .= '
                    <div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Actions</button>
                            <div class="dropdown-menu">
                          <a href="#" data-toggle="modal" data-target="#approval-add-modal" class="dropdown-item">Add New  Approval</a>
                          </div>
                          </div>';

            }
            return view('modules-people-approval::index', $this->data);


        }
        catch (\Exception $e){
            $this->setViewUiResponse($request);
            return view('modules-people-approval::index', $this->data);

        }

    }

    public function searchApproval(Request $request, Sdk $sdk){
        $search = $request->query('search', '');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);

        # get the request parameters
        $path = ['approval'];

        $query = $sdk->createApprovalsResource();
        $query = $query->addQueryArgument('limit', $limit)
            ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $query = $query->addQueryArgument('search', $search);
        }
        $response = $query->send('get', $path);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching approval.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }

    public function createApproval(Request $request, Sdk $sdk){
        try{
            $resource = $sdk->createApprovalsResource();
            $resource = $resource->addBodyParam('title',$request->title)
                ->addBodyParam('scope_type',$request->scope_type)
                ->addBodyParam('frequency_type',$request->frequency_type);
            $response = $resource->send('post',['approval']);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the Approval  '.$message);

            }
            return response()->json(['message'=>'Approval  Created Successfully'],200);

        }
        catch (\Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);


        }
    }

    public function approvalSingle(Sdk $sdk, string $id){
        try {
            $response = $sdk->createApprovalsResource()->send('get',['approval',$id]);
            if(!$response->isSuccessful()){
                throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find the approval');
            }
            $approval = $response->getData(true);
            return response()->json([$approval, 200]);
        }
        catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 400);

        }
    }

    public function updateApproval(Request $request, Sdk $sdk,string $id){
        try {
            $status = ($request->status === false ? 0 : 1);
            $resource = $sdk->createApprovalsResource();
            $resource = $resource->addBodyParam('title', $request->title)
                ->addBodyParam('scope_type', $request->scope_type)
                ->addBodyParam('frequency_type',$request->frequency_type)
                ->addBodyParam('active',$status);
            $response = $resource->send('put', ['approval',$id]);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the  Approval ' . $message);

            }
            return response()->json(['message' => ' Approval Updated Successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function deleteApproval(Request $request, Sdk $sdk, string $id){
        try{
            $resource = $sdk->createApprovalsResource();
            $response = $resource->send('delete', ['approval',$id]);
            if (!$response->isSuccessful()) {
                throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the Approval.');

            }
            $this->data = $response->getData();
            return response()->json($this->data);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function approvalAuthorizer(Request $request, Sdk $sdk, string $id){
        try {
            if(auth()->user()->is_employee === 1){
                return response(view('errors.404'), 404);
            }
            $response = $sdk->createApprovalsResource()->send('get',['approval',$id]);
            if(!$response->isSuccessful()){
                $message = $response->errors[0]['title'] ?? 'Could not find the approval';
                $error = (tabler_ui_html_response([$message]))->setType(UiResponse::TYPE_ERROR);
                return redirect()->route('approval-main')->with('UiResponse', $error);
            }
            $approval = $response->getData(true);
            $this->data['page']['title'] .= ' &rsaquo; Approval Authorizers';
            $this->data['header']['title'] = $approval->title;
            $this->data['submenuAction'] = '';
            $this->data['selectedSubMenu'] = 'people-payroll-approvals';

            $this->setViewUiResponse($request);
            $users = $this->getUsers($sdk);
            $this->data['args'] = $request->query->all();
            $authorizedUsers = collect($approval->authorizers['data'])->pluck('id');
            $leftUser = collect($users)->whereNotIn('id',$authorizedUsers);
            $this->data['users'] = $leftUser;

            $this->data['approval'] = $approval;
            switch ($this->data){
                case !empty($this->data['authorizers']):
                    $this->data['submenuAction'] .= '
                    <div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Actions</button>
                            <div class="dropdown-menu">
                          <a href="/mpe/approval/authorizer/create/'.$id.'" class="dropdown-item">Add  Authorizers </a>
                          </div>
                          </div>';

            }
            return view('modules-people-approval::Authorizer/index', $this->data);


        }
        catch (\Exception $e){
            $this->setViewUiResponse($request);
            return view('modules-people-approval::index', $this->data);

        }
    }

    public function createAuthorizer(Request $request, Sdk $sdk){
        try{
            $users = explode(',',$request->users);
            $resource = $sdk->createApprovalsResource();
            $resource = $resource->addBodyParam('users',$users)
                ->addBodyParam('approval_scope',$request->approval_scope)
                ->addBodyParam('approval_id',$request->approval_id);
            $response = $resource->send('post',['authorizers']);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                $response = (tabler_ui_html_response(['Failed while adding the Authorizer  '.$message]))->setType(UiResponse::TYPE_ERROR);
                return redirect()->route('approval-authorizers',['id'=>$request->approval_id])->with('UiResponse', $response);

            }
            $response = (tabler_ui_html_response(['Authorizer Created Successfully']))->setType(UiResponse::TYPE_SUCCESS);
            return redirect()->route('approval-authorizers',['id'=>$request->approval_id])->with('UiResponse', $response);

        }
        catch (\Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);


        }
    }

    public function deleteAuthorizer(Request $request, Sdk $sdk){
        try{
            $resource = $sdk->createApprovalsResource()
            ->addBodyParam('approval_id',$request->approval_id)
            ->addBodyParam('user',$request->user);
            $response = $resource->send('delete', ['authorizers']);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                $response = (tabler_ui_html_response(['Failed while adding the Authorizer  '.$message]))->setType(UiResponse::TYPE_ERROR);
                return redirect()->route('approval-authorizers',['id'=>$request->approval_id])->with('UiResponse', $response);

            }
            $response = (tabler_ui_html_response(['Authorizer removed  Successfully']))->setType(UiResponse::TYPE_SUCCESS);
            return redirect()->route('approval-authorizers',['id'=>$request->approval_id])->with('UiResponse', $response);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }





    private function getRequests(Sdk $sdk){
        $sdk = $sdk ?: app(Sdk::class);
        $company = auth()->user()->company(true, true);
        # get the company

        $response = $sdk->createApprovalsResource()
            ->addQueryArgument('user_id', auth()->user()->id)
            ->send('get', ['requests','authorizer']);
        if (!$response->isSuccessful()) {
            return null;
        }
        return collect($response->getData())->map(function ($requests) {
            return (object) $requests;
        });
        return $response;
    }

    private function getSingleRequest(Sdk $sdk,string $id){
        $sdk = $sdk ?: app(Sdk::class);
        $company = auth()->user()->company(true, true);
        # get the company

        $response = $sdk->createApprovalsResource()
            ->send('get', ['requests',$id]);
        if (!$response->isSuccessful()) {
            return null;
        }
        return $response->getData(true);
    }

    public function approvalRequestForm(Request $request, Sdk $sdk,string $id)
    {
        try {

            $this->data['submenuAction'] = '';
            $this->data['selectedSubMenu'] = 'people-payroll-approvals';
            $this->setViewUiResponse($request);
            if ($this->getSingleRequest($sdk,$id) === null){
                $error = (tabler_ui_html_response(['Could not Load View for this approval ']))->setType(UiResponse::TYPE_ERROR);
                return redirect()->route('approval-main')->with('UiResponse', $error);
            }
            $leaveRequest = $this->getSingleRequest($sdk,$id);
            $this->data['request'] = $leaveRequest;
            $this->data['request_id'] = $leaveRequest->id;
            $this->data['args'] = $request->query->all();
            $this->data['page']['title'] .= ' &rsaquo; Approval Requests';
            $this->data['header']['title'] = $leaveRequest->model_data['request_type'];
            return view('modules-people-approval::Requests/index', $this->data);
        }
        catch (\Exception $e){
            dd($e);
            $this->setViewUiResponse($request);
            return redirect()->route('approval-main');

        }

    }

    public function requestAction(Request $request, Sdk $sdk){
        try{
            $resource = $sdk->createApprovalsResource();
            $resource = $resource->addBodyParam('request_id',$request->request_id)
                ->addBodyParam('user_id',$request->user_id)
                ->addBodyParam('status',$request->status);

            if ($request->input('rejection_comment')){
                $resource->addBodyParam('rejection_comment',$request->rejection_comment);
            }
            $response = $resource->send('post',['requests']);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                $error = (tabler_ui_html_response([$message]))->setType(UiResponse::TYPE_ERROR);
                return redirect()->route('view-request',['id'=>$request->request_id])->with('UiResponse', $error);

            }
            $message = (tabler_ui_html_response(['Action Recorded Successfully']))->setType(UiResponse::TYPE_SUCCESS);
            return redirect()->route('approval-main')->with('UiResponse', $message);

        }
        catch (\Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);


        }
    }


}
