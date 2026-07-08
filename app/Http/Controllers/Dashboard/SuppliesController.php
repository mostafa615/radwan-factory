<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Student;
use App\Admin;
use App\Reposite;
use App\Supplies;
use App\SupplieTypes;
use App\SuppliesExchanges;
use App\MachineSupplie;
use App\Store;
use App\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Machines;
use App\MachineSupplieTrack;
use App\TrackingMachineSupplies;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;

class SuppliesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['ability:admin,reade_supplies'])->only('index');
        $this->middleware(['ability:admin,create_supplies'])->only('create');
        $this->middleware(['ability:admin,update_supplies'])->only('edit');
        $this->middleware(['ability:admin,delete_supplies'])->only('destroy');
    }


    public function getData(Request $request)
    {
        $query = Supplies::query();


        return FacadesDataTables::eloquent($query->latest())
            ->addColumn('action', function (Supplies $supplies) {
                $type = "action";
                return view("dashboard.supplies.action", compact("supplies", "type"));
            })
            ->editColumn('type', function (Supplies $supplies) {
                return optional($supplies->SupplieType)->name;
            })
            ->editColumn('store_id', function (Supplies $supplies) {
                return optional($supplies->Store)->name;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function index(Request $request)
    {


        $query = Supplies::query();

        $supplies = $query->with(['SupplieType'])->get();
        $store = $query->with(['Store'])->get();

        return view('dashboard.supplies.index', compact('supplies'));
    }

    public function create()
    {
        $supplie_types = SupplieTypes::get();
        $stores = Store::get();

        return view('dashboard.supplies.create', compact('supplie_types', 'stores'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'store_id' => 'required',
            'height',
            'width',
            'quantity' => 'required',
            'description',
        ]);

        $request_data = $request->all();

        $Supplies = Supplies::create($request_data);


        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.supplies.index');
    }

    public function edit($supplies)
    {
        $supplies = Supplies::find($supplies);

        $supplie_types = SupplieTypes::get();
        $stores = Store::get();

        return view('dashboard.supplies.edit', compact('supplies', 'supplie_types', 'stores'));
    }

    public function update(Request $request, $supplies)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'store_id' => 'required',
            'height',
            'width' => 'required',
            'quantity' => 'required',
            'description',
        ]);
        $supplies = Supplies::find($supplies);

        $request_data = $request->all();

        $supplies->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.supplies.index');
    }


    public function exchange_create_view()
    {

        $machines = Machines::all();
        return view('dashboard.supplies.exchange_create', compact('machines'));
    }

    public function exchange_view()
    {
        $data = SuppliesExchanges::with('old_machine', 'new_machine', 'supplies')->get();
        return view('dashboard.supplies.exchange', compact('data'));
    }

    /**
     * Momaher
     */
     
    public function exchange(Request $request)
    {
        try {
            $validators = Validator::make($request->all(), [
                'old_machine_id' => 'required',
                'new_machine_id' => 'required',
                'supplie_id' => 'required',
                'date' => 'required'
            ], [
                'old_machine_id.required' => "اختر الآله القديمة",
                'new_machine_id.required' => "اختر الآله الجديدة",
                'supplie_id.required' => "اختر المستلزم"
            ]);

            if ($validators->fails()) {
                return redirect()->back()->withErrors($validators->errors())->withInput($request->all());
            }

            $old_machine_supply = MachineSupplie::find($request['supplie_id']);
            $supply = Supplies::find($old_machine_supply->supplie_id);
            $new_machine_supply = MachineSupplie::where('machine_id', $request['new_machine_id'])->where('supplie_id', $old_machine_supply->supplie_id)->first();

            if ($request->quantity > $old_machine_supply->used) {
                session()->flash('error', 'الكمية المستخدمة اكبر من الكمية المتاحة');
                return redirect()->back()->withInput($request->all());
            }

            DB::beginTransaction();

            $track_old_machine_supplie = TrackingMachineSupplies::where('machine_id', $request['old_machine_id'])->where('supplie_id', $supply->id)->where('date', $request['date'])->latest()->first();
            $track_new_machine_supplie = TrackingMachineSupplies::where('machine_id', $request['new_machine_id'])->where('supplie_id', $supply->id)->where('date', $request['date'])->latest()->first();

            $new_track_old_machine_supplie = TrackingMachineSupplies::create([
                'machine_id' => $request['old_machine_id'],
                'supplie_id' => $supply->id,
                'type' => 'exchange_from',
                'date' => $request['date']
            ]);

            $new_track_new_machine_supplie = TrackingMachineSupplies::create([
                'machine_id' => $request['new_machine_id'],
                'supplie_id' => $supply->id,
                'type' => 'exchange_to',
                'date' => $request['date']
            ]);

            if ($request['old_machine_id'] == $request['new_machine_id']) {
                session()->flash('error', 'لا يمكن التحويل الى نفس الآله');
                return redirect()->back()->withInput($request->all());
            }

            $track_operation = SuppliesExchanges::create([
                'old_machine_id' => $request['old_machine_id'],
                'new_machine_id' => $request['new_machine_id'],
                'transferred_quantity' => $request['quantity'],
                'supplie_id' => $supply->id,
                'old_machine_pre_used' => $old_machine_supply->used,
            ]);

            $old_machine_supply->decrement('used', $request['quantity']);
            $old_machine_supply->quantity = ceil($old_machine_supply->used / $supply->used);

            $old_machine_supply->save();
            if ($new_machine_supply) {
                $track_operation->new_machine_pre_used = $new_machine_supply->used;
                // $track_operation->save();
                $new_machine_supply->increment('used', $request['quantity']);
                if (($new_machine_supply->used / $supply->used) > 0) {
                    if (is_float($new_machine_supply->used / $supply->used)) {
                        $new_machine_supply->quantity = ceil($new_machine_supply->used / $supply->used);
                    }
                }
                if (!empty($request['date'])) {
                    $new_machine_supply->date = $request['date'];
                    $track_operation->date = $request['date'];
                }
                if (!empty($request['notes'])) {
                    $new_machine_supply->notes = $request['notes'];
                    $track_operation->notes = $request['notes'];
                }
                $new_machine_supply->save();
            } else {
                $new_machine_supply = MachineSupplie::create([
                    'machine_id' => $request['new_machine_id'],
                    'supplie_id' => $supply->id,
                    'used' => $request['quantity'],
                    'notes' => $request['notes'],
                    'date' => $request['date'],
                    'quantity' => ceil($request['quantity'] / $supply->used),
                    'transfer_quantity' => $request['quantity']
                ]);
                $track_operation->new_machine_pre_used = $new_machine_supply->used;
                // $track_operation->save();
            }

            $track_operation->old_machine_used = $old_machine_supply->used;
            $track_operation->new_machine_used = $new_machine_supply->used;

            $new_track_old_machine_supplie->quantity = -$track_operation->transferred_quantity;
            $new_track_new_machine_supplie->quantity = $track_operation->transferred_quantity;
            $new_track_old_machine_supplie->exchange_id = $track_operation->id;
            $new_track_new_machine_supplie->exchange_id = $track_operation->id;

            if ($track_old_machine_supplie) {
                $new_track_old_machine_supplie->init_quantity = $track_old_machine_supplie->init_quantity;
                $new_track_old_machine_supplie->last_quantity = $track_old_machine_supplie->last_quantity + $new_track_old_machine_supplie->quantity;
            } else {
                $last_track = TrackingMachineSupplies::where('machine_id', $request['old_machine_id'])->orderBy('date', 'DESC')->where('id', '!=', $new_track_old_machine_supplie->id)->where('supplie_id', $supply->id)->latest()->first();
                if ($last_track) {
                    $new_track_old_machine_supplie->init_quantity = $last_track->last_quantity;
                } else {
                    $new_track_old_machine_supplie->init_quantity = $old_machine_supply->used + $request['quantity'];
                }
                $new_track_old_machine_supplie->last_quantity = $new_track_old_machine_supplie->init_quantity + $new_track_old_machine_supplie->quantity;
            }


            if ($track_new_machine_supplie) {
                $new_track_new_machine_supplie->init_quantity = $track_new_machine_supplie->init_quantity;
                $new_track_new_machine_supplie->last_quantity = $track_new_machine_supplie->last_quantity + $new_track_new_machine_supplie->quantity;
            } else {
                $last_track = TrackingMachineSupplies::where('machine_id', $request['new_machine_id'])->orderBy('date', 'DESC')->where('id', '!=', $new_track_new_machine_supplie->id)->where('supplie_id', $supply->id)->latest()->first();
                if ($last_track) {
                    $new_track_new_machine_supplie->init_quantity = $last_track->last_quantity;
                } else {
                    $new_track_new_machine_supplie->init_quantity = $new_machine_supply->used - $request['quantity'];
                }
                $new_track_new_machine_supplie->last_quantity = $new_track_new_machine_supplie->init_quantity + $new_track_new_machine_supplie->quantity;
            }
            $track_operation->save();
            $new_track_old_machine_supplie->save();
            $new_track_new_machine_supplie->save();

            MachineSupplieTrack::create([
                'type' => 'exchange_out',
                'date' => $request->date,
                'machine_id' => $request->old_machine_id,
                'supplie_id' => $supply->id,
                'quantity' => $request->quantity,
                'notes' => $request->notes ?? null,
            ]);

            MachineSupplieTrack::create([
                'type' => 'exchange_in',
                'date' => $request->date,
                'machine_id' => $request->new_machine_id,
                'supplie_id' => $supply->id,
                'quantity' => $request->quantity,
                'notes' => $request->notes ?? null,
            ]);

            DB::commit();
            session()->flash('success', 'تم التحويل بنجاح');
            return redirect()->back();
        } catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('error', 'هناك مشكله حدثت , جرب مره اخرى');
            return redirect()->back()->withErrors(['errors' => $ex->getMessage()])->withInput($request->all());
        }
    }
     
    /**
     * Momaher
     */

    // public function exchange(Request $request)
    // {
    //     try {
    //         $validators = Validator::make($request->all(), [
    //             'old_machine_id' => 'required',
    //             'new_machine_id' => 'required',
    //             'supplie_id' => 'required'
    //         ], [
    //             'old_machine_id.required' => "اختر الآله القديمة",
    //             'new_machine_id.required' => "اختر الآله الجديدة",
    //             'supplie_id.required' => "اختر المستلزم"
    //         ]);



    //         if ($validators->fails()) {
    //             return redirect()->back()->withErrors($validators->errors())->withInput($request->all());
    //         }

    //         $old_machine_supply = MachineSupplie::find($request['supplie_id']);
    //         $supply = Supplies::find($old_machine_supply->supplie_id);
    //         $new_machine_supply = MachineSupplie::where('machine_id', $request['new_machine_id'])->where('supplie_id', $old_machine_supply->supplie_id)->first();


    //         if ($request['old_machine_id'] == $request['new_machine_id']) {
    //             session()->flash('error', 'لا يمكن التحويل الى نفس الآله');
    //             return redirect()->back()->withInput($request->all());
    //         }
    //         /**
    //          * Decrement Quantity and used from old machine supplie
    //          */
    //         DB::beginTransaction();

    //         $track_operation = SuppliesExchanges::create([
    //             'old_machine_id' => $request['old_machine_id'],
    //             'new_machine_id' => $request['new_machine_id'],
    //             'transferred_quantity' => $request['quantity'],
    //             'supplie_id' => $supply->id,
    //             'old_machine_pre_used' => $old_machine_supply->used,
    //         ]);

    //         $old_machine_supply->decrement('used', $request['quantity']);
    //         $old_machine_supply->quantity = ceil($old_machine_supply->used / $supply->used);

    //         $old_machine_supply->save();
    //         if ($new_machine_supply) {
    //             $track_operation->new_machine_pre_used = $new_machine_supply->used;
    //             // $track_operation->save();
    //             $new_machine_supply->increment('used', $request['quantity']);
    //             if (($new_machine_supply->used / $supply->used) > 0) {
    //                 if (is_float($new_machine_supply->used / $supply->used)) {
    //                     $new_machine_supply->quantity = ceil($new_machine_supply->used / $supply->used);
    //                 }
    //             }
    //             if (!empty($request['date'])) {
    //                 $new_machine_supply->date = $request['date'];
    //                 $track_operation->date = $request['date'];
    //             }
    //             if (!empty($request['notes'])) {
    //                 $new_machine_supply->notes = $request['notes'];
    //                 $track_operation->notes = $request['notes'];
    //             }
    //             $new_machine_supply->save();
    //         } else {
    //             $new_machine_supply = MachineSupplie::create([
    //                 'machine_id' => $request['new_machine_id'],
    //                 'supplie_id' => $supply->id,
    //                 'used' => $request['quantity'],
    //                 'notes' => $request['notes'],
    //                 'date' => $request['date'],
    //                 'quantity' => ceil($request['quantity'] / $supply->used)
    //             ]);
    //             $track_operation->new_machine_pre_used = $new_machine_supply->used;
    //             // $track_operation->save();
    //         }

    //         $track_operation->old_machine_used = $old_machine_supply->used;
    //         $track_operation->new_machine_used = $new_machine_supply->used;
    //         $track_operation->save();
    //         DB::commit();
    //         session()->flash('success', 'تم التحويل بنجاح');
    //         return redirect()->back();
    //     } catch (\Exception $ex) {
    //         DB::rollBack();
    //         session()->flash('error', 'هناك مشكله حدثت , جرب مره اخرى');
    //         return redirect()->route('dashboard.supplie_types.index')->withErrors(['errors' => $ex->getMessage()])->withInput($request->all());
    //     }
    // }

    public function edit_exchange($exchange_id)
    {
        $data = SuppliesExchanges::find($exchange_id);
        $machines = Machines::all();
        $supplies = MachineSupplie::where('machine_id', $data->old_machine_id)->get();
        return view('dashboard.supplies.exchange_edit', compact('data', 'machines', 'supplies'));
    }
    public function exchange_update($exchange_id, Request $request)
    {
        $validators = Validator::make($request->all(), [
            'old_machine_id' => 'required',
            'new_machine_id' => 'required',
            'supplie_id' => 'required'
        ], [
            'old_machine_id.required' => "اختر الآله القديمة",
            'new_machine_id.required' => "اختر الآله الجديدة",
            'supplie_id.required' => "اختر المستلزم"
        ]);

        if ($validators->fails()) {
            return redirect()->back()->withErrors($validators->errors())->withInput($request->all());
        }

        $exchange = SuppliesExchanges::find($exchange_id);


        $old_machine_supply = MachineSupplie::where('machine_id', $exchange['new_machine_id'])->where('supplie_id', $exchange->supplie_id)->first();
        $new_machine_supply = MachineSupplie::where('machine_id', $exchange['new_machine_id'])->where('supplie_id', $exchange->supplie_id)->first();


        if ($request['old_machine_id'] == $request['new_machine_id']) {
            session()->flash('error', 'لا يمكن التحويل الى نفس الآله');
            return redirect()->back()->withInput($request->all());
        }
    }

    public function exchange_delete($exchange_id)
    {
        $exchange = SuppliesExchanges::find($exchange_id);
        // return $exchange;
        $supplie = Supplies::find($exchange->supplie_id);
        $old_machine_supply = MachineSupplie::where('machine_id', $exchange['old_machine_id'])->where('supplie_id', $exchange->supplie_id)->first();
        $new_machine_supply = MachineSupplie::where('machine_id', $exchange['new_machine_id'])->where('supplie_id', $exchange->supplie_id)->first();

        $old_machine_supply->increment('used', $exchange->transferred_quantity);
        $old_machine_supply->quantity = ceil($old_machine_supply->used / $supplie->used);
        $old_machine_supply->save();
        $new_machine_supply->decrement('used', $exchange->transferred_quantity);
        $new_machine_supply->quantity = ceil($new_machine_supply->used / $supplie->used);
        $new_machine_supply->save();
        
        $tracking_machine_supplie = TrackingMachineSupplies::where('exchange_id', $exchange_id)->get();
        if ($tracking_machine_supplie) {
            foreach ($tracking_machine_supplie as $item) {
                $item->delete();
            }
        }
        $exchange->delete();

        session()->flash('success', 'تم الحذف بنجاح');
        return redirect()->back();
    }
    public function getSuppliesByMachine(Request $request)
    {
        if (!$request->machine_id) {
            $html = '<option value="">' . trans('site.items') . '</option>';
        } else {
            $html = '<option disable value="">المستلزمات</option>';

            $machineSupplies = MachineSupplie::where("machine_id", $request->machine_id)
                ->where("used", ">", 1)
                ->get();
            foreach ($machineSupplies as $machineSupplie) {
                $html .= '<option data-quantity="' . $machineSupplie['quantity'] . '" data-used="' . $machineSupplie['used'] . '"  value="' . $machineSupplie['id']  . '"> ' . $machineSupplie->Supplie['name'] . ' </option>';
            }
        }

        return response()->json(['html' => $html]);
    }
    
    public function getSuppliesByMachineReport(Request $request)
    {
        if (!$request->machine_id) {
            $html = '<option value="">' . trans('site.items') . '</option>';
        } else {
            $html = '<option disable value="">المستلزمات</option>';

            $machineSupplies = MachineSupplie::where("machine_id", $request->machine_id)
                ->get();
            foreach ($machineSupplies as $machineSupplie) {
                $html .= '<option data-quantity="' . $machineSupplie['quantity'] . '" data-used="' . $machineSupplie['used'] . '"  value="' . $machineSupplie['id']  . '"> ' . $machineSupplie->Supplie['name'] . ' </option>';
            }
        }

        return response()->json(['html' => $html]);
    }

    public function destroy($supplies)
    {
        $supplies = Supplies::find($supplies);
        $supplies->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.supplies.index');
    }
}
