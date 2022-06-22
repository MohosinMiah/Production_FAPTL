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

		if($request->hasfile('file_name'))
		{
			foreach($request->file('file_name') as $image)
			{
				$imageName=time().'_'.$image->getClientOriginalName();
				$image->move(public_path().'/images/', $imageName);  
				$data = $request->all();
				$data['file_name']  = $imageName;
				$data['isActive'] = 1;
				$data['isFeatured'] = 0;
				$newProperty = $this->propertImageRepository->create($data);
			}
			return $this->respondWithSuccess('Success !! Property Image has been created.');
		}
		else
		{
		return $this->respondNotSaved( 'Error !! Property Image has Not  created.' );
		}
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show( $uuid )
    {
        $image = $this->propertImageRepository->getById( $uuid );

        if( !$image )
		{
            return $this->respondNotFound('Property Image not found.');
        }
        return $this->respondWithData(new PropertyImageResource( $image ) );

    }

    /**
     * @param PropertyImageRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(PropertyImageRequest $request, $uuid)
    {
		return $request->all();

		$save = $this->propertImageRepository->update($request->all(), $uuid);

	

    }


	public function updatePropertyImage( Request $request, $uuid )
	{
		

		if($request->hasfile('file_name'))
		{
			foreach($request->file('file_name') as $image)
			{
				$imageName=time().'_'.$image->getClientOriginalName();
				$image->move(public_path().'/images/', $imageName);  
				$data = $request->all();
				$data['file_name']  = $imageName;
				$newProperty = $this->propertImageRepository->create($data);
			}
			return $this->respondWithSuccess('Success !! Property Image has been Updated.');
		}
		else
		{
		  $save = $this->propertImageRepository->update( $request->all(), $uuid );
		  
			if (!is_null($save) && $save['error'])
			{
				return $this->respondNotSaved($save['message']);
			} 
			else
			{
				return $this->respondWithSuccess('Success !! Property Image has been updated.');
			}
            
		}
	}

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
		if ($this->propertImageRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Property Image has been deleted');
        }
        return $this->respondNotFound('Property Image not deleted');

    }

    
}
