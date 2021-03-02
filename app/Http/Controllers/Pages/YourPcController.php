<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Section;
use App\Traits\RelationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class YourPcController extends Controller
{
    use RelationTrait;

    public function indexGet(Request $request) {
        $token = Session::get('pc-constructor')[0];
        $sections = Section::where('required', 1)->get();
        $countSections = $sections->count();

        $configurations = Configuration::where('token_configuration', $token)
            ->leftJoin('components', 'components.id', 'configurations.component_id')
            ->leftJoin('sections', 'sections.id', 'components.section_id')
            ->with('values')
            ->get(
                [
                    "components.id as component_id",
                    "configurations.id as config_id",
                    "short_name",
                    "full_name",
                    "picture",
                    "price",
                    "sections.id as section_id",
                    "sections.name",
                ]
            );

//        $this->checkSpecialCases($configurations);

        $countConfig = $configurations->count();
        $percent = round((100 * $countConfig) / $countSections);

        return view('pages.your-pc', compact(
            'configurations',
                'percent'
        ));
    }

    public function indexPost(Request $request) {
        if($request->isMethod('post') && $request->post('component-id')) {
            $id = $request->post('component-id');

            if($request->post('action') == 'add') {
                Configuration::create([
                    'component_id' => $id,
                    'token_configuration' => Session::get('pc-constructor')[0]
                ]);
            }
            elseif ($request->post('action') == 'remove') {
                $component = Configuration::find($id);

                if($component) {
                    $component->delete();
                }
            }
        }

        return redirect()->route('pages.your-pc.index-get');
    }
}
