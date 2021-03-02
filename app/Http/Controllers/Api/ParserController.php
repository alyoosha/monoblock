<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 29.12.2020
 * Time: 12:59
 */

namespace App\Http\Controllers\Api;


use App\Models\Component;
use App\Models\Feature;
use App\Models\FeatureType;
use App\Models\Parser;
use App\Models\Section;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParserController
{
    public function get() {
        $parser = new \App\Parser\Parser();
        $dbParser = Parser::find(1);
        $parser->parseComponents();

    }
    public function updateCatalog() {
        try {
            $parser = new \App\Parser\Parser();
            $dbParser = Parser::find(1);

            if($dbParser->parsing) {
                return response( json_encode(['message' => 'В данный момент осуществляется парсинг']), 500);
            }

            $dbParser->parsing = 1;
            $dbParser->save();

            $parser->parseSections();

            try {
                $parser->updateOrInsertSections();
            } catch(QueryException $e){
                $dbParser->parsing = 0;
                $dbParser->save();
                Log::warning("В ходе парсинга произошла ошибка: " . $e->getMessage());
                abort(500, "В ходе парсинга произошла ошибка");
            }

            $parser->parseFilterTypes();

            try {
                $parser->updateOrInsertFilterTypes();
            } catch(QueryException $e){
                $dbParser->parsing = 0;
                $dbParser->save();
                Log::warning("В ходе парсинга произошла ошибка: " . $e->getMessage());
                abort(500, "В ходе парсинга произошла ошибка");
            }

            $parser->parseFeatureTypes();

            try {
                $parser->updateOrInsertFeatureTypes();
            } catch(QueryException $e){
                $dbParser->parsing = 0;
                $dbParser->save();
                Log::warning("В ходе парсинга произошла ошибка: " . $e->getMessage());
                abort(500, "В ходе парсинга произошла ошибка");
            }

            $parser->parseFeatures();

            try {
                $parser->updateOrInsertFeatures();
            } catch(QueryException $e){
                $dbParser->parsing = 0;
                $dbParser->save();
                Log::warning("В ходе парсинга произошла ошибка: " . $e->getMessage());
                abort(500, "В ходе парсинга произошла ошибка");
            }

            $parser->parseComponents();

            try {
                $parser->updateOrInsertComponents();
            } catch(QueryException $e){
                $dbParser->parsing = 0;
                $dbParser->save();
                Log::warning("В ходе парсинга произошла ошибка: " . $e->getMessage());
                abort(500, "В ходе парсинга произошла ошибка");
            }

            $parser->parseValues();

            try {
                $parser->updateOrInsertValues();
            } catch(QueryException $e){
                $dbParser->parsing = 0;
                $dbParser->save();
                Log::warning("В ходе парсинга произошла ошибка: " . $e->getMessage());
                abort(500, "В ходе парсинга произошла ошибка");
            }

            $parser->parseSectionFeature();

            try {
                $parser->updateOrInsertSectionFeature();
            } catch(QueryException $e){
                $dbParser->parsing = 0;
                $dbParser->save();
                Log::warning("В ходе парсинга произошла ошибка: " . $e->getMessage());
                abort(500, "В ходе парсинга произошла ошибка");
            }

            $parser->writeDownUpdate();

        } catch (\Illuminate\Http\Client\RequestException $exception) {
            $dbParser->parsing = 0;
            $dbParser->save();
            Log::warning("В ходе парсинга произошла ошибка: " . $exception->getMessage());
            abort(500, "В ходе парсинга произошла ошибка");
        }
        catch (\Exception $exception) {
            $dbParser->parsing = 0;
            $dbParser->save();
            Log::warning("В ходе парсинга произошла ошибка: " . $exception->getMessage());
            abort(500, "В ходе парсинга произошла ошибка");
        }
    }
}