<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 29.12.2020
 * Time: 16:03
 */

namespace App\Parser;


use App\Models\Component;
use App\Models\ComponentFeature;
use App\Models\Feature;
use App\Models\FeatureType;
use App\Models\FilterType;
use App\Models\Section;
use App\Models\SectionFeature;
use App\Models\SectionFeatureType;
use App\Models\Value;
use App\Models\ValueComponent;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function PHPUnit\Framework\throwException;

class Parser
{
    protected $data = [];
    protected $dataValue = [];
    protected $sections = [];
    protected $featureTypes = [];
    protected $filterTypes = [];
    protected $features = [];
    protected $components = [];
    protected $values = [];
    protected $valueComponents = [];
    protected $sectionFeatureType = [];
    protected $sectionFeature = [];
    protected $componentFeature = [];

    function __construct() {
        $this->data = self::getDataFromCurl('https://api.n-tech.by/properties/v1');
        $this->dataValue = self::getDataFromCurl('https://api.n-tech.by/properties/v1/values');
    }

    public function parseSections() {
        $data = json_decode($this->data, true);

        foreach ($data['data']['sections'] as $section) {
            $this->sections[] = [
                'id' => (int)$section['id'],
                'sort' => (int)($section['sort']),
                'name' => (string)htmlspecialchars(my_ucfirst(trim($section['name'])), ENT_QUOTES),
                'slug' => (string)Str::slug(htmlspecialchars(trim($section['name']), ENT_QUOTES), '-'),
                'required' => (int)($section['required']),
                'is_visible' => (int)($section['is_visible'])
            ];
        }
    }

    public function updateOrInsertSections() {
        Section::insertOnDuplicateKey($this->sections, ['id', 'sort', 'name', 'slug', 'required', 'is_visible']);
    }

    public function parseFilterTypes() {
        $data = json_decode($this->data, true);

        $data = array_unique(Arr::pluck(Arr::sort($data['data']['feature_types'], function ($value) {
            return $value['filter_type'];
        }), 'filter_type'));

        foreach ($data as $filterType) {
            $this->filterTypes[] = [
                'name' => (string)htmlspecialchars($filterType, ENT_QUOTES),
            ];
        }
    }

    public function updateOrInsertFilterTypes() {
        FilterType::insertOnDuplicateKey($this->filterTypes, ['name']);
    }

    public function parseFeatureTypes() {
        $data = json_decode($this->data, true);

        foreach ($data['data']['feature_types'] as $featureType) {
            $unit = isset($featureType['unit']) ? $featureType['unit'] : null;

            $this->featureTypes[] = [
                'id' => (int)($featureType['id']),
                'sort' => (int)($featureType['sort']),
                'name' => (string)htmlspecialchars(my_ucfirst(trim($featureType['name'])), ENT_QUOTES),
                'slug' => (string)Str::slug(htmlspecialchars(trim($featureType['name']), ENT_QUOTES), '-'),
                'unit' => (string)htmlspecialchars(trim($unit), ENT_QUOTES),
                'section_id' => (int)($featureType['section_id']),
                'filter_type' => (string)htmlspecialchars(trim($featureType['filter_type']), ENT_QUOTES)
            ];
        }
    }

    public function updateOrInsertFeatureTypes() {
        FeatureType::insertOnDuplicateKey($this->featureTypes, ['id', 'sort', 'name', 'slug', 'unit', 'section_id', 'filter_type']);
    }

    public function parseFeatures() {
        $data = json_decode($this->data, true);

        foreach ($data['data']['features'] as $feature) {
            $this->features[] = [
                'id' => (int)$feature['id'],
                'sort' => (int)($feature['sort']),
                'name' => preg_replace( '/\s[2,]/',' ', (string)my_ucfirst(trim($feature['name']))),
                'slug' => Str::slug(trim($feature['name']),  '-'),
                'feature_type_id' => (int)($feature['feature_type_id']),
                'is_visible' => (int)($feature['is_visible'])
            ];
        }
    }

    public function updateOrInsertFeatures() {
        Feature::insertOnDuplicateKey($this->features, ['id', 'sort', 'name', 'slug', 'feature_type_id', 'is_visible']);
    }

    public function parseComponents() {
        $data = self::getDataFromCurl(
            'https://api.n-tech.by/properties/v1/products'
        );

        $data = json_decode($data, true);

//        echo "<pre>";
//        var_dump($data);
//        echo "</pre>";
//        exit();

        foreach ($data['data'] as $component) {
            $this->components[] = [
                'id' => (int)$component['id'],
                'short_name' => (string)my_ucfirst(trim($component['name'])),
                'full_name' => (string)my_ucfirst(trim($component['name'])),
                'section_id' => (int)($component['section_id']),
                'picture' => (string)htmlspecialchars(trim($component['picture']),ENT_QUOTES),
                'recomendation' => (int)($component['recomendation']),
                'sku' => (string)htmlspecialchars(trim($component['sku']), ENT_QUOTES),
                'price' => (int)($component['price']),
                'qty' => (int)($component['qty'])
            ];
        }
    }

    public function updateOrInsertComponents() {
        Component::insertOnDuplicateKey($this->components, ['id', 'short_name', 'full_name', 'section_id', 'picture', 'recomendation', 'sku', 'price', 'qty']);
    }

    public function parseValues() {
        $data = self::getDataFromCurl(
            'https://api.n-tech.by/properties/v1/values'
        );

        $data = json_decode($data, true);

        $featureTypes = Arr::pluck(FeatureType::all(), null, 'id');

        foreach ($data['data'] as $value) {
            if(isset($featureTypes[$value['feature_type_id']]) && $featureTypes[$value['feature_type_id']]['filter_type'] == 'list') {
                $this->values[] = [
                    'component_id' => (int) $value['product_id'],
                    'feature_type_id' => (int) $value['feature_type_id'],
                    'feature_id' => (int) $value['value'],
                    'value' => null
                ];
            }
            else {
                $this->values[] = [
                    'component_id' => (int) $value['product_id'],
                    'feature_type_id' => (int) $value['feature_type_id'],
                    'feature_id' => null,
                    'value' => preg_replace( '/\s[2,]/',' ', (string)trim($value['value'])),
                ];
            }
        }
    }

    public function updateOrInsertValues() {
        DB::table('values')->truncate();

        foreach (array_chunk($this->values, 1000) as $item) {
            Value::insertOnDuplicateKey($item, ['component_id', 'feature_type_id', 'feature_id','value']);
        }
    }

    public function parseSectionFeature() {
        DB::table('section_feature')->truncate();

        $featureTypes = FeatureType::all('id', 'section_id');
        $features = Feature::all();
        $featureTypes = Arr::pluck($featureTypes, 'section_id', 'id');

        $features->map(function ($f) use ($featureTypes) {
            if(array_key_exists($f['feature_type_id'], $featureTypes)) {
                $this->sectionFeature[] = [
                    'section_id' => (int) $featureTypes[$f['feature_type_id']],
                    'feature_id' => (int) $f['id']
                ];
            }
        });
    }

    public function updateOrInsertSectionFeature() {
        SectionFeature::insertOnDuplicateKey($this->sectionFeature, ['section_id', 'feature_id']);
    }

    public function writeDownUpdate() {
        DB::table('parser')->upsert(
            ['id' => 1, 'updated_at' => Carbon::now("Europe/Minsk"), 'parsing' => 0], ['id'], ['updated_at', 'parsing']
        );
    }

    public static function getDataFromCurl(string $url, array $header = []) {
        $header = empty($header) ? ['Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJleHAiOjM3NjA5MjQxNTg0LCJkYXRhIjp7InVzZXJfaWQiOiI0NjI2In19.4tVSq0Tr7Gb3gPASKtYeKJx0K5wZ2q1U1kSjYnTu0IHYjp6mlOYGsHkTSTBr6-ZljKAIsrA_GkH0T_DqjBHlWg'] : $header;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}