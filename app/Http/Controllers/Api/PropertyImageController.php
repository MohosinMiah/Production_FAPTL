<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PropertyImageRequest;
use App\Http\Resources\PropertyImageResource;
use App\Models\PropertyImage;
use App\Models\GeneralSetting;
use App\Models\Lease;
use App\Models\LeaseSetting;
use App\Rental\Repositories\Contracts\PropertyImageInterface;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class PropertyImageController extends ApiController
{
    /**
     * @var PropertyImageInterface
     */
    protected $propertImageRepository, $load;

    /**
     * AccountController constructor.
     * @param PropertyImageInterface $propertyImageInterface
     */
    public function __construct(PropertyImageInterface $propertyImageInterface)
    {
        $this->propertImageRepository = $propertyImageInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->propertImageRepository->listAll($this->formatFields($select));
        }
        $data = $this->propertImageRepository->getAllPaginate($this->load);

  
        return $this->respondWithData(PropertyImageResource::collection($data));
    }

    /**
     * @param PropertyImageRequest $request
     * @return mixed
     */
    public function store(Request $request)
    {

        // $allowedfileExtension=['pdf','jpg','png'];
        // $files = $request->file('file_name'); 

     
            // $check = in_array($extension,$allowedfileExtension);
    //  return $request->file('file_name');
            // if($check) {

                // foreach( $request->file('file_name') as $mediaFiles ) 
                // {
                $propertyImage = $request->file('file_name');
                $imagePath = '/images/'. time().'.'.$propertyImage->extension();
                $propertyImage->move(public_path().'/images/', $name); 

                    // $this->generalSettingRepository->update($data, $data['id']);
        
          
                    //store image file into directory and db
                    $data = $request->all();
                    $data['file_name'] = $imagePath;
                    $newProperty = $this->propertImageRepository->create($data);
                    
                  
                // }

            // } else {
            //     return $this->respondNotSaved('Sorry !! Property Image has not created.');
            // }
     
            return $this->respondWithSuccess('Success !! Property Image has been created.');
     
        


        // $save = $this->propertImageRepository->create($request->all());

        // if (!is_null($save) && $save['error']) {
        //     return $this->respondNotSaved($save['message']);
        // } else {
        //     return $this->respondWithSuccess('Success !! Account has been created.');

        // }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $account = $this->propertImageRepository->getById($uuid);

        if (!$account) {
            return $this->respondNotFound('Account not found.');
        }
        return $this->respondWithData(new PropertyImageResource($account));

    }

    /**
     * @param PropertyImageRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(PropertyImageRequest $request, $uuid)
    {
        $save = $this->propertImageRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! Account has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        return $this->respondNotFound('Account not deleted');
    }

    /**
     * Fetch statement given a $loanId
     * @param Request $request
     * @return mixed
     */
    public function leaseAccountStatement(Request $request) {
        $data = $request->all();
        $leaseID = $data['id'];
        if(isset($data['pdf'])){
            $request['type'] = 'lease';
            return $this->downloadAccountStatement($request);
        }
        $account = Account::where('lease_id', $leaseID)
            ->where('account_name', LEASE_ACCOUNT)
            ->first();
        if (isset($account))
            $account['statement'] = $this->propertImageRepository->fetchAccountStatement($account->id);
        return $this->respondWithData(new PropertyImageResource($account));
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    private function downloadAccountStatement(Request $request) {
        $account = $this->getAccount($request->all());
        $settings = GeneralSetting::first();
        $leaseSettings = LeaseSetting::first();
        $file_path = $settings->logo;
        $local_path = '';
        if($file_path != '')
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;
        $settings->logo_url = $local_path;
        $settings->invoice_footer = $leaseSettings->invoice_footer;

        if (isset($account)){
            $lease = [];
            if (isset($account->lease_id)) {
                $lease =  Lease::with('tenants', 'property', 'units')->find($account->lease_id);
            }

            $rawStatement = $this->propertImageRepository->fetchAccountStatement($account->id);
            $account['statement'] =  $rawStatement;
           // $pageData = PropertyImageResource::make($account)->toArray($request);
            $pageData = PropertyImageResource::make($account)->resolve();
            $pdf = PDF::loadView('reports.account-statement', compact('pageData', 'settings', 'lease'));
           // return view('reports.account-statement', compact('pageData', 'setting'));
            return $pdf->download('statement.pdf');
        }
        return null;
    }

    /**
     * Special cases for member deposit and loan accounts as compared to other accounts
     * @param $data
     * @return mixed
     */
    private function getAccount($data) {
        switch ($data['type']){
            case  'lease' :
                return Account::where('lease_id', $data['id'])->where('account_type', LEASE_ACCOUNT)
                    ->first();
                break;
            default :
                return $this->propertImageRepository->getById($data['id']);
                break;
        }
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    public function generalAccountStatement(Request $request) {
        $data = $request->all();
        $uuid = $data['id'];
        if(isset($data['pdf']) && $data['pdf'] == true){
            $request['type'] = 'general';
            return $this->downloadAccountStatement($request);
        }
        $account = $this->propertImageRepository->getById($uuid);
        if (isset($account))
            $account['statement'] = $this->propertImageRepository->fetchAccountStatement($account->id);
        return $this->respondWithData(new PropertyImageResource($account));
    }
}
