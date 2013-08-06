<?php namespace Stwt\Mothership;

use Template;
use ContentItems;
use ContentRegion;
use Config;
use URL;

class PageModel extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'status', 'template',];

    /**
     * Define the slug fields config
     * 
     * @see: https://github.com/cviebrock/eloquent-sluggable
     * @var array
     */
    public static $sluggable = [
        'build_from' => 'name',
        'save_to'    => 'slug',
    ];

    /**
     * This models db column properties
     * ---
     * This array is auto loaded from the database details but any of the
     * attributes can be overridden here.
     * 
     * Example of column properties:
     * 
     * [column_name] => [
     *     'label'      => '',  // the column label
     *     'form'       => '',  // the type of form element e.g. [input, select, textarea]
     *     'validation' => [],  // array of validation rules
     * ],
     * 
     * @var array
     */
    protected $properties = [
        'page_id' => [
            'label' => 'Parent Page',
        ]
    ];

    // ------------------------- //
    // Magic Methods             //
    // ------------------------- //

    public function __toString()
    {
        if ($this->id) {
            return $this->name;
        }
        return parent::__toString();
    }

    // ------------------------- //
    // Initialisation Methods    //
    // ------------------------- //

    /**
     * Initialise any custom properties
     * 
     * @param array $properties The models properties array
     * 
     * @return array
     */
    protected function initProperties($properties)
    {
        $templateOptions = function () {
            $templates = Config::get('templates.templates', []);
            $options = [];
            foreach ($templates as $name => $spec) {
                $options[$name] = humanize($name);
            }
            return $options;
        };

        $properties['template'] = [
            'form'      => 'select',
            'options'   => $templateOptions,
        ];
        return $properties;
    }

    /**
     * Initialise custom table columns
     * 
     * @param  array $columns The models existing columns array
     * @return array
     */
    protected function initColumns($columns)
    {
        $name = function ($page) {
            if ($page->parentPage) {
                $depth = 1;
            } else {
                $depth = 0;
            }
            $depth = str_repeat('— ', $depth);
            return $depth.$page->name;
        };

        $columns = [
            'Name' => $name,
            'slug',
            'status',
            'template',
        ];

        return $columns;
    }

    // ------------------------- //
    // Eloquent Relationships    //
    // ------------------------- //
    
    public function navigationMenus()
    {
        return $this->belongsToMany('NavigationMenu');
    }
    
    /*public function page()
    {
        return $this->hasOne('Page');
    }*/
    
    public function contentRegions()
    {
        return $this->hasMany('ContentRegion');
    }
    
    public function navigationItems()
    {
        return $this->hasMany('NavigationItems');
    }

    public function attributes()
    {
        return $this->morphMany('Attributes', 'attributeable');
    }

    public function pages()
    {
        // assuming my comments table has an $id column and a second $comment_id table
        // do I even need to specify the column name?
        return $this->hasMany(get_called_class(), 'page_id');
    }

    public function parentPage()
    {
        return $this->belongsTo(get_called_class(), 'page_id');
    }

    // ------------------------- //
    // Page Generation           //
    // * Move to it's own class? //
    // ------------------------- //

    /**
     * Generate the html for this page
     * 
     * @return string HTML
     */
    public function generate()
    {
        $template = $this->getTemplate();
        if ($template and $template->exists()) {
            
            $regions = $template->regions();

            $data = array_merge(
                $this->getPageMeta(),
                $this->getRegionPlacehoders($regions),
                $this->getGlobalRegions($regions),
                $this->getPageRegions($regions)
            );
            
            return View::make($template->path(), $data);
        }
        App::abort(404, 'Template "'.$template->path().'" not found');
    }

    public function getAllRegions($generate = true)
    {
        $template = $this->getTemplate();
        if ($template and $template->exists()) {
            
            $regions = $template->regions();
            return array_merge(
                $this->getRegionPlacehoders($regions, $generate),
                $this->getGlobalRegions($regions, $generate),
                $this->getPageRegions($regions, $generate)
            );
        }
    }

    /**
     * Return an array of page meta attributes
     * 
     * @return array
     */
    public function getPageMeta()
    {
        return [
            'metaTitle'       => 'United Wind',
            'metaDescription' => '',
            'htmlId'          => ($this->slug == '/' ? 'homepage' : $this->slug),
        ];
    }

    /**
     * Generate html placehoders for all regions 
     * incase we're missing some
     * 
     * @return array
     */
    public function getRegionPlacehoders($templateRegions, $generate = true)
    {
        $data = [];

        foreach ($templateRegions as $region) {
            $data[$region] = '<!-- '.$region.' is missing -->';
        }
        return $data;
    }

    /**
     * Generate the HTML for all global regions
     * 
     * @return array
     */
    public function getGlobalRegions($templateRegions, $generate = true)
    {
        $data = [];

        $regions = ContentRegion::where('page_id', '=', null)
                                ->whereIn('key', $templateRegions)
                                ->with('contentItems')
                                ->get();

        foreach ($regions as $region) {
            $data[$region->key] = ($generate ? $region->generate() : $region);
        }

        return $data;
    }

    /**
     * Generate the HTML for all regions unique to this page
     * 
     * @return array
     */
    public function getPageRegions($templateRegions, $generate = true)
    {
        $data = [];

        $regions = $this->contentRegions()
                        ->whereIn('key', $templateRegions)
                        ->with('contentItems')
                        ->get();

        foreach ($regions as $region) {
            $data[$region->key] = ($generate ? $region->generate() : $region);
        }

        return $data;
    }

    /**
     * Generate a url to the page
     * 
     * @return string
     */
    public function url()
    {
        return URL::to($this->slug);
    }

    /**
     * Returns the template model for this page
     * 
     * @return Template (object)
     */
    public function getTemplate()
    {
        if (isset($this->template)) {
            return Template::get($this->template);
        }
        return false;
    }
}