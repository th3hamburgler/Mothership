<?php namespace Stwt\Mothership;

use Auth;
use Controller;
use Config;
use Input;
use Redirect;
use Log;
use Hash;
use View;
use URI;
use URL;
use Session;
use Stwt\GoodForm\GoodForm as GoodForm;
use Validator;

class BaseController extends Controller
{
    
    protected $breadcrumbs;

    public function __construct ()
    {
        $this->breadcrumbs  = ['/'  => 'Home'];
    }

    /*
     * Sets up common data required for the layout views
     *
     * @return array 
     */
    protected function getTemplateData()
    {
        $data = [];

        $data['breadcrumbs'] = $this->breadcrumbs;
        $data['navigation']  = Config::get('mothership::primaryNavigation');
        
        if (Auth::check()) {
            $data['user'] = Auth::user();
        }
        return $data;
    }
}
