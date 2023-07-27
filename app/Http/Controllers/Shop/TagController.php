<?php

namespace App\Http\Controllers\Shop;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:191|unique:tags,name'
        ]);
        $tag = Tag::create($request->only(['name']));
        if ($request->ajax()) {
            return $tag;
        }
        return redirect()->back()->withSuccess('Tag created successfully');
    }
}
