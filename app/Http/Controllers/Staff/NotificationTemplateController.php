<?php

namespace App\Http\Controllers\Staff;

use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTableAbstract;

class NotificationTemplateController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param string $type
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|void
     */
    public function index(Request $request, $type = 'email')
    {
        if (!in_array($type, ['email', 'sms', 'fcm'])) return abort(404);
        $query = NotificationTemplate::query()->where('channel', $type);
        if (!(clone $query)->count()) {
            DB::beginTransaction();
            try {
                foreach (config($type) as $name => $item) {
                    $item['name'] = $name;
                    $item['channel'] = $type;
                    NotificationTemplate::create($item);
                }
            } catch (\Exception $exception) {
                DB::rollBack();
            }
            DB::commit();
        }
        if ($request->ajax()) {
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->addColumn('actions', function (NotificationTemplate $notificationTemplate) use ($type) {
                return sprintf('<a class="btn btn-warning btn-sm nimmu-btn nimmu-btn-warning" href="%s"><i class="fa fa-edit"></i></a>', route('staff.setting.template.edit', [$notificationTemplate->id]));
            });
            $table->editColumn('name', function (NotificationTemplate $notificationTemplate) {
                return ucwords(str_replace('_', ' ', $notificationTemplate->name));
            });
            $table->rawColumns(['actions']);
            return $table->make(true);
        }
        return view('staff.setting.template.index', compact('type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NotificationTemplate  $notificationTemplate
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function edit(NotificationTemplate $notificationTemplate)
    {
        if (!$notificationTemplate) return abort(404);
        return view('staff.setting.template.edit', compact('notificationTemplate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NotificationTemplate  $notificationTemplate
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function update(Request $request, NotificationTemplate $notificationTemplate)
    {
        if (!$notificationTemplate) return abort(404);
        $request->validate([
            'title' => 'required|string|max:191',
            'content' => 'required'
        ], [
            'title.*' => 'Please write a valid ' . ($notificationTemplate->channel == 'email'?'Subject':'Title')
        ]);
        $p = $request->only(['title', 'content']);
        try {
            $r = $notificationTemplate->update($p);
            if (!$r) throw new \Exception('Unable to update template');
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors($exception->getMessage());
        }
        return redirect()->back()->withSuccess('Template updated successfully');
    }
}
