<?php

namespace Dorcas\ModulesPeopleApproval\Http\Controllers;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use http\Exception\RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ModulesPeopleApprovalController extends Controller
{
    public function __construct()
    {
        $this->data = [
            'page' => ['title' => config('modules-people.title')],
            'header' => ['title' => config('modules-people-approval.title')],
            'selectedMenu' => 'modules-people-approval',
            'submenuConfig' => 'navigation-menu.modules-people.sub-menu',
            'submenuAction' => ''
        ];

    }

    public function index(Request $request, Sdk $sdk){
        try {
            $this->data['page']['title'] .= ' &rsaquo; Approvals';
            $this->data['header']['title'] = 'People Approvals';
            $this->data['submenuAction'] = '';
            $this->setViewUiResponse($request);
            $this->data['args'] = $request->query->all();
            $this->data['approvals'] = $this->getPeopleApprovals($sdk);
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
                ->addBodyParam('scope_type',$request->scope_type);
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

    public function updateApproval(Request $request, Sdk $sdk){

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

}