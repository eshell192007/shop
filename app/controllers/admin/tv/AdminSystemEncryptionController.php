<?php

class AdminSystemEncryptionController extends AdminController
{
    public function index()
    {
        return View::make("admin.tv.transponder.index", 
            ['items' => Transponder::all()]
        );
    }

    public function add()
    {
        return View::make("admin.tv.transponder.add");
    }

    public function postAdd()
    {
        $rules = array(
            'name' => 'required|min:2|max:500',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            $parameters = json_decode(Input::get('parameters'));
            return Redirect::to("admin/tv-channel/add")
                ->with('uploadfile', isset($parameters->name) ? $parameters->name : '')
                ->withErrors($validator)
                ->withInput(Input::except(''));
        }

        $table = new Transponder;
        $table->name = Input::get('name');
        $table->transponders_id = Input::get('transponder_id');
        $table->system_encryption_id = Input::get('system_encryption_id');
        $table->keys_id = Input::get('keys_id');
        $table->active = Input::get('active', 0);

        if ($table->save()) {
            if (Input::get('parameters')) {
                $parameters = json_decode(Input::get('parameters'));
                $img = Image::make($parameters->name);

                if ($img->width() > $img->height()) {
                    $img->resize(100, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                } else {
                    $img->resize(null, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }

                $img->save("uploads/images/tv/logo/{$table->id}.png");
                $img->destroy();
            }
            $name = trans("name.tv");
            return Redirect::to("admin/tv-channel")->with('success', trans("message.add", ['name' => $name]));
        }

        return Redirect::to("admin/tv-channel")->with('error', trans('message.error'));

    }

    public function edit($id)
    {
        return View::make("admin.tv.transponder.edit", 
            ['item' => Transponder::find($id)]
        );
    }

    public function postEdit($id)
    {
        $rules = array(
            'name' => 'required|min:2|max:500',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            $parameters = json_decode(Input::get('parameters'));
            $img = isset($parameters->name) ? $parameters->name : null;
            return Redirect::to("admin/tv-channel/{$id}/edit")
                ->with('uploadfile', $img)
                ->withErrors($validator)
                ->withInput(Input::except(''));
        }

        $table = Transponder::find($id);
        $table->name = Input::get('name');
        $table->transponders_id = Input::get('transponder_id');
        $table->system_encryption_id = Input::get('system_encryption_id');
        $table->keys_id = Input::get('keys_id');
        $table->active = Input::get('active', 0);

        if ($table->save()) {
            if (Input::get('parameters')) {
                $parameters = json_decode(Input::get('parameters'));
                $img = Image::make($parameters->name);

                if ($img->width() > $img->height()) {
                    $img->resize(100, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                } else {
                    $img->resize(null, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }

                $img->save("uploads/images/tv/logo/{$table->id}.png");
                $img->destroy();
            }
            $name = trans("name.tv");
            return Redirect::to("admin/tv-channel")->with('success', trans("message.adit", ['name' => $name]));
        }

        return Redirect::to("admin/tv-channel")->with('error', trans('message.error'));
    }

    public function postDelete()
    {
        if (Request::ajax()) {
            $id = (int) Input::get('id');

            $rules = array(
                'id' => 'required'
            );

            $validator = Validator::make(Input::all(), $rules);
            if ( $validator->fails() ) {
                return Response::json(['success' => false]);
            }

            $table = Transponder::find($id);
            if ($table->delete()) {
                return Response::json(['success' => true]);
            }
            return Response::json(['success' => false]);
        }
    }
}