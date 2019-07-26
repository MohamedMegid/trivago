<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Hotel;
use App\Location;
use App\Http\Requests\HotelRequest;
use Storage;
use Auth;

class HotelController extends Controller
{
	public $successStatus = 200;

    /** 
     * hotels api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function index(Request $request) 
    { 
    	$query = Hotel::with('location')->select('*');
        if (isset($request->rating) && is_numeric($request->rating) && !empty($request->rating)){
            $query->where('rating', $request->rating);
        }
        $query = $query->where('created_by', Auth::user()->id);
        $hotels = $query->get(); 

        $code = $this-> successStatus;
		$status = 'true';
		$message = "Hotels list";
		$dataContent = $hotels;
		return $this->returnApiResult($code, $status, $message, $dataContent);
    } 

    /** 
     * Validate Hotel Name
     * 
     * @param $request 
     */ 
    public function validate_hotelName($request){
    	$hotelName = $request->name;
		if(preg_match('/(free|hotel|Book|Website)/i', $hotelName) === 1) { 
            $code = 400;
			$status = 'false';
			$message = "Hotel name can't contain free|hotel|Book|Website";
			$dataContent = "";
			return $this->returnApiResult($code, $status, $message, $dataContent);
		} 
    }

    /** 
     * Validate Category
     * 
     * @param $request 
     */ 
    public function validate_category($request){
    	$category = $request->category;
		if(preg_match('/\b(hotel|alternative|hostel|lodge|resort|guest-house)\b/', $category) === 1) { 

		}
		else{
            $code = 400;
			$status = 'false';
			$message = "Category must be a word of (hotel|alternative|hostel|lodge|resort|guest-house)";
			$dataContent = "";
			return $this->returnApiResult($code, $status, $message, $dataContent);
		}
    }

    /** 
     * Validate Image URL
     * 
     * @param $request 
     */ 
    public function validate_imageURL($request){
    	$url = $request->image;
		if (filter_var($url, FILTER_VALIDATE_URL)) {

		} 
		else {
            $code = 400;
			$status = 'false';
			$message = "$url is not a valid URL";
			$dataContent = "";
			return $this->returnApiResult($code, $status, $message, $dataContent);
		}
    }

    /**
     *	return APi request result
     *	if failed or successed
     */
	public function returnApiResult($code, $status, $message, $dataContent){
		$metadata['metadata'] = [
	        'code' => $code,
	        'status' => $status,
	        'message' => $message,
	    ];
	    $data['data'] = [
	    	$dataContent,
	    ];
	    $response = [
	    	$metadata,
	    	$data,
	    ];
	    return response()->json($response);
	}

	/** 
     * Save Hotel Info
     * 
     * @param $request 
     */ 
	public function saveHotelInfo($request){
		$hotel = new Hotel;
        $hotel->name = $request->name;
        $hotel->rating = $request->rating;
        $hotel->category = $request->category;
        $hotel->image = $request->image;
        $reputation = $request->reputation;
        $hotel->reputation = $reputation;
        if ($reputation <= 500){
        	$hotel->reputationBadge = "red";
        }
        elseif ($reputation >= 500 && $reputation <= 799){
        	$hotel->reputationBadge = "yellow";
        }
        else{
        	$hotel->reputationBadge = "green";
        }
        $hotel->price = $request->price;
        $hotel->availability = $request->availability;
        $hotel->created_by = Auth::user()->id;
        $hotel->save();

        return $hotel->id;
	}

	/** 
     * Update Hotel Info
     * 
     * @param $request 
     */ 
	public function updateHotelInfo($request, $id){
		$hotel = Hotel::find($id);
        $hotel->name = $request->name;
        $hotel->rating = $request->rating;
        $hotel->category = $request->category;
        $hotel->image = $request->image;
        $reputation = $request->reputation;
        $hotel->reputation = $reputation;
        if ($reputation <= 500){
        	$hotel->reputationBadge = "red";
        }
        elseif ($reputation >= 500 && $reputation <= 799){
        	$hotel->reputationBadge = "yellow";
        }
        else{
        	$hotel->reputationBadge = "green";
        }
        $hotel->price = $request->price;
        $hotel->availability = $request->availability;
        $hotel->created_by = Auth::user()->id;
        $hotel->update();

        return $hotel->id;
	}

	/** 
     * Save Location Info
     * 
     * @param $request, $hotel_id
     */ 
	public function saveAndUpdateLocationInfo($request, $hotel_id){
		$location = new Location;
        $location->city = $request->city;
        $location->state = $request->state;
        $location->country = $request->country;
        $location->zip_code = $request->zip_code;
        $location->address = $request->address;
        $location->hotel_id = $hotel_id;
        $location->save();
	}

    /** 
     * create new hotel api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function create(HotelRequest $request) 
    { 
    	// return hotel name - validation
    	$validate_hotelName = $this->validate_hotelName($request);
    	if (!empty($validate_hotelName) || !empty($validate_category)){
    		return $validate_hotelName;
    	}

    	// return Category - validation
    	$validate_category = $this->validate_category($request);
    	if (!empty($validate_category)){
    		return $validate_category;
    	}

    	// return image url - validation
    	$validate_imageURL = $this->validate_imageURL($request);
    	if (!empty($validate_imageURL)){
    		return $validate_imageURL;
    	}

    	//return hotel id - saved
    	$hotel_id = $this->saveHotelInfo($request);

    	// save location info - calling
		$this->saveAndUpdateLocationInfo($request, $hotel_id);

        $code = $this->successStatus;
		$status = 'true';
		$message = "Saved successfully";
		$dataContent = "";
		return $this->returnApiResult($code, $status, $message, $dataContent);
    } 

    /** 
     * update hotel api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function update(HotelRequest $request, $id) 
    { 
    	// return hotel name - validation
    	$validate_hotelName = $this->validate_hotelName($request);
    	if (!empty($validate_hotelName) || !empty($validate_category)){
    		return $validate_hotelName;
    	}

    	// return Category - validation
    	$validate_category = $this->validate_category($request);
    	if (!empty($validate_category)){
    		return $validate_category;
    	}

    	// return image url - validation
    	$validate_imageURL = $this->validate_imageURL($request);
    	if (!empty($validate_imageURL)){
    		return $validate_imageURL;
    	}

    	//return hotel id - saved
    	$hotel_id = $this->updateHotelInfo($request, $id);

    	// save location info - calling
		$this->saveAndUpdateLocationInfo($request, $hotel_id);

        $code = $this->successStatus;
		$status = 'true';
		$message = "Updated successfully";
		$dataContent = "";
		return $this->returnApiResult($code, $status, $message, $dataContent);
    } 

    /** 
     * delete hotel api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function delete($id) 
    { 
    	Location::where('hotel_id', $id)->first()->delete();
    	Hotel::find($id)->delete();
    	$code = $this->successStatus;
		$status = 'true';
		$message = "Deleted successfully";
		$dataContent = "";
		return $this->returnApiResult($code, $status, $message, $dataContent);
    } 
}

