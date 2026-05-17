<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use App\helper\Message;
use App\Translation;
use App\helper\Helper;
class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view("dashboard.options.index");
    }

    /**
     * update any option
     */
    public function update(Request $request) {
        /* if (!$request->value)
             return Message::error(__('please enter value'));*/
         try {
             $option = Setting::find($request->id);

             if (!$option)
                 $option = Setting::create([
                     "id" => $request->id,
                     "name" => '-',
                     "value" => '-',
                 ]);

             $option->value = $request->value;
             $option->update();


             //notify(__('edit setting'), __('edit setting') . " " . $option->created_at, "fa fa-cogs");
             //return Message::success(Message::$DONE);
             session()->flash('success', __('site.updated_successfully'));

         } catch (\Exception $ex) {
             //return Message::error(Message::$ERROR . $ex->getMessage());
             session()->flash('error', __('site.error'));

         }
     }


    /**
     * edit the translation of Arabic and English
     *
     * @param Request $request
     */
    public function updateTranslation(Request $request) {
        $translations = json_decode($request->translations);

        foreach ($translations as $item) {
            $translation = Translation::find($item->id);

            if ($translation)
                $translation->update([
                    "word_en" => $item->word_en,
                    "word_ar" => $item->word_ar,
                ]);
        }


        //return Message::success(Message::$DONE);
        return [
            "status" => 1,
            "message" => __('done')
        ];
    }







    }
