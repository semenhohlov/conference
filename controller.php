<?php
namespace Controller;
use DB\DB;
use Model\Country;
use Model\Conference;
use Response\Response;
use Request\Request;
use Request\Validator;
use View\View;

class Controller {
    public Request $request;
    public DB $db;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->db = new DB();
    }
}
class CountryController extends Controller{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
    public function create()
    {
        $valid = new Validator($this->request, [
            'country_name' => ['required' => true, 'minLength' => 2, 'maxLength' => 255],
            'zoom' => ['required' => true],
            'lat' => ['required' => true],
            'lon' => ['required' => true]
        ]);
        if (!$valid->isValid)
        {
            $countries = Country::getAll($this->db);
            return Response::show(View::render('countryform', ['countries' => $countries,
            'error' => $valid->errorMessage]));
        }
        // create country
        $country = Country::create($this->db, [
            'name' => $this->request->country_name,
            'lat' => floatval($this->request->lat),
            'lon' => floatval($this->request->lon),
            'zoom' => floatval($this->request->zoom)
        ]);
        Response::redirect('/add');
    }
    public function edit($id)
    {
        echo 'Edit';
    }
    public function save($id)
    {
        echo 'Save';
    }
    public function delete($id)
    {
        echo 'Delete';
    }
    public function getAll()
    {
        $countries = Country::getAll($this->db);
        return Response::show(View::render('countryform', ['countries' => $countries]));
    }
    public function getOne($id)
    {
        echo 'GetOne';
    }
}
class ConferenceController extends Controller{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
    public function add()
    {
        $countries = Country::getAll($this->db);
        return Response::show(View::render('addform', ['countries' => $countries]));
    }
    public function create()
    {
        // валидация!!!
        $valid = new Validator($this->request, [
            'title' => ['required' => true, 'minLength' => 2, 'maxLength' => 255],
            'date' => ['required' => true, 'date' => true],
            'time' => ['required' => true, 'time' => true],
            'country_id' => ['required' => true],
        ]);
        if (!$valid->isValid)
        {
            $countries = Country::getAll($this->db);
            return Response::show(View::render('addform', ['countries' => $countries, 'error' => $valid->errorMessage])); 
        }
        $conf_date = $this->request->date.' '.$this->request->time;
        $conference = Conference::create($this->db, [
            'title' => $this->request->title,
            'conf_date' => $conf_date,
            'country_id' => $this->request->country_id,
            'adress' => $this->request->adress,
            'lat' => floatval($this->request->latitude),
            'lon' => floatval($this->request->longitude)
        ]);
        Response::redirect('/');
    }
    public function edit($id)
    {
        $countries = Country::getAll($this->db);
        $conf = Conference::getOne($this->db, $id);
        $datetime = explode(' ', $conf->conf_date);
        return Response::show(View::render('editform', [
            'countries' => $countries, 'conference' => $conf,
            'country' => $conf->one,
            'date' => $datetime[0], 'time' => $datetime[1]
        ]));
    }
    public function save($id)
    {
        // валидация!!!
        $valid = new Validator($this->request, [
            'title' => ['required' => true, 'minLength' => 2, 'maxLength' => 255],
            'date' => ['required' => true, 'date' => true],
            'time' => ['required' => true, 'time' => true],
            'country_id' => ['required' => true],
        ]);
        $conf = Conference::getOne($this->db, $id);
        if (!$valid->isValid)
        {
            $countries = Country::getAll($this->db);
            $datetime = explode(' ', $conf->conf_date);
            return Response::show(View::render('editform', [
                'countries' => $countries, 'conference' => $conf,
                'country_name' => $conf->one->name,
                'date' => $datetime[0],
                'time' => $datetime[1],
                'error' => $valid->errorMessage
            ]));            
        }
        $conf->title = $this->request->title;
        $conf->country_id = $this->request->country_id;
        $conf->conf_date = $this->request->date.' '.$this->request->time;
        $conf->adress = $this->request->adress;
        $conf->lon = floatval($this->request->longitude);
        $conf->lat = floatval($this->request->latitude);
        $conf->save();
        Response::redirect('/');
    }
    public function delete($id)
    {
        $conf = Conference::getOne($this->db, $id);
        $conf->delete();
        Response::redirect('/');
    }
    public function getAll()
    {
        $conferences = Conference::getAll($this->db);
        return Response::show(View::render('content', ['conferences' => $conferences]));
    }
    public function getOne($id)
    {
        $conf = Conference::getOne($this->db, $id);
        return Response::show(View::render('detail', ['conference' => $conf, 'country' => $conf->one->name]));
    }
    public function test($id1, $id2)
    {
        return Response::show('Test id1:{'.$id1.'}, id2:{'.$id2.'}');
    }
}

?>