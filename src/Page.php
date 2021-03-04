<?php
namespace HansSchouten\LaravelPageBuilder;
use HansSchouten\LaravelPageBuilder\Contracts\PageContract;
use PHPageBuilder\Page as BasePage;

class Page extends BasePage implements PageContract
{
    /**
     * @var array variables that should be accessible in the view
     */
    protected $variables;

    public function setData($data, $fullOverwrite = true)
    {
        // if page builder data is set, try to decode json
        if (isset($data['data']) && is_string($data['data'])) {
            $data['data'] = json_decode($data['data'], true);
        }
        if ($fullOverwrite) {
            $this->attributes = $data;
        }  elseif (is_array($data)) {
            $this->attributes = is_null($this->attributes) ? [] : $this->attributes;
            foreach ($data as $key => $value) {
                $this->attributes[$key] = $value;
            }
        }
    }

    /**
     * Gets the page meta
     *
     * @return array|null
     */
    public function getMeta(){
        if(isset($this->attributes['meta'])){
            return json_decode($this->attributes['meta'], true);
        }
        return null;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        if($this->variables){
            return $this->variables;
        }
        return [];
    }

    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

}