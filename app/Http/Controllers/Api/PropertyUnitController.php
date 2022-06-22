<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PropertyUnitRequest;
use App\Http\Resources\PropertyUnitResource;
use App\Rental\Repositories\Contracts\PropertyUnitInterface;


use App\Http\Resources\PeriodResource;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\UnitInterface;
use App\Traits\CommunicationMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PropertyUnitController extends ApiController
{
    /**
     * @var PropertyInterface
     */
    protected $propertyRepository, $load, $accountRepository, $unitRepository, $landlordRepository, $invoiceRepository;

    /**
     * PropertyController constructor.
     * @param PropertyInterface $propertyInterface
     * @param UnitInterface $unitRepository
     * @param LandlordInterface $landlordRepository
     * @param InvoiceInterface $invoiceRepository
     */
    public function __construct(PropertyInterface $propertyInterface, UnitInterface $unitRepository,
                                LandlordInterface $landlordRepository, InvoiceInterface $invoiceRepository)
    {
        $this->propertyRepository = $propertyInterface;
        $this->landlordRepository = $landlordRepository;
        $this->unitRepository = $unitRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->load = [
            'property_type',
			'landlord',
			'payment_methods',
			'extra_charges',
			'late_fees',
			'utility_costs'
        ];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
    
         $data = PropertyResource::collection( DB::table('')->get() );

        return $this->respondWithData($data);
    }

    /**
     * @param PropertyRequest $request
     * @return array|mixed
     * @throws \Exception
     */
    public function store(PropertyRequest $request)
    {
    
       
                $data = $request->all();
                $newProperty = $this->propertyRepository->create($data);
                // var_dump($newProperty);

                $propertyImage = $request->file('file_name');
                $imagePath = '/images/'. time().'.'.$propertyImage->extension();
                $propertyImage->move(public_path().'/images/', $name); 
                 // $this->generalSettingRepository->update($data, $data['id']);
                //store image file into directory and db
                $data = $request->all();
                $data['property_id'] = $newProperty->id;
                $data['file_name'] = $imagePath;
                $newPropertyImages = $this->propertImageRepository->create($data);
               
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $property = $this->propertyRepository->getById($uuid, $this->load);
        if(!$property)
            return $this->respondNotFound('Property not found.');

        return $this->respondWithData(new PropertyResource($property));
    }

    /**
     * @param PropertyRequest $request
     * @param $id
     * @return array|mixed
     * @throws \Exception
     */
    public function updateProperty(Request $request, $uuid)
    {
       
        $save = $this->propertyRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! Property has been updated.');
    }

    /**
     * @param $uuid
     * @return array
     * @throws \Exception
     */
    public function destroy($uuid)
    {
        
        if ($this->propertyRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Property has been deleted');
        }
        return $this->respondNotFound('Property not deleted');
    }

    /**
     * @param Request $request
     */
    public function uploadPhoto(Request $request) {
        $data = $request->all();
        $fileNameToStore = '';
        // Upload logo
        if($request->hasFile('property_photo')) {
            $filenameWithExt = $request->file('property_photo')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('property_photo')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file('property_photo')->storeAs('photos', $fileNameToStore);
            $data['property_photo'] = $fileNameToStore;

            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'photos'.DIRECTORY_SEPARATOR. $fileNameToStore;

            // Update the property
            $this->propertyRepository->update(
                [
                    'property_photo' => $fileNameToStore
                ], $data['property_id']);
        }
        return json_encode($fileNameToStore);
        // also, delete previous image file from server
       // $this->memberRepository->update(array_filter($data), $data['id']);
    }

    /**
     * @param Request $request
     */
    public function profilePic(Request $request)
    {
        $data = $request->all();
        if( array_key_exists('file_path', $data) ) {
            $file_path = $data['file_path'];
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'photos'.DIRECTORY_SEPARATOR. $file_path;
            return response()->file($local_path);
        }
        return $this->respondNotFound('file_path not provided');
    }

    /**
     * @param Request $request
     * @return mixed
     */
	public function search(Request $request) {
        $data = $request->all();
        if (array_key_exists('filter', $data)) {
            $filter = $data['filter'];

			$data = $this->propertyRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
			return PropertyResource::collection($data);

           // return $this->propertyRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function periods(Request $request) {
        $data = $request->all();
        if (array_key_exists('id', $data)) {
            $property = $this->propertyRepository->getById($data['id']);
            return PeriodResource::collection($property->periods);
        }
        return [];
    }
}

