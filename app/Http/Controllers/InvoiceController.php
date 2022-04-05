<?php

namespace App\Http\Controllers;

use App\Exports\InvoiceExport;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Invoice;
use App\Models\InvoicePricing;
use App\Models\Configuration;
use App\Models\Location;
use App\Models\Representative;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    public function index()
    {
        $locations = Location::whereIn('status', [1, 2])->orderBy('location', 'ASC')->get();
        $defaultlocation = Location::where('setdefault', 1)->first();
        $vehicletypes = VehicleType::whereIn('status', [1, 2])->orderBy('type', 'ASC')->get();
        $representatives = Representative::whereIn('status', [1, 2])->where('isassigned',1)->get();
        $drivers = Driver::where('status',1)->orderBy('turnno','ASC')->get();
        $configurationdata=Configuration::first();
        return view('pages.posinvoice', compact(['locations', 'vehicletypes', 'representatives', 'drivers','defaultlocation','configurationdata']));
    }

    public function deleteOne(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:invoices,id'
        ]);
        Invoice::where('id', $request->id)->update(['status' => 4]);
        return 1;
    }

    public function billedList()
    {
        $representatives = Representative::whereIn('status', [1, 2])->get();
        $drivers = Driver::whereIn('status', [1, 2])->orderBy('fname', 'ASC')->get();
        $configurationdata=Configuration::first();
        return view('pages.salesreport', compact(['representatives', 'drivers','configurationdata']));
    }

    public function list(Request $request)
    {
        $data = [];
        $query = Invoice::where('status', 1);

        if ($request->has('from') && $request->from != '') {
            $query->whereDate('date', '>=', Carbon::parse($request->from)->format('Y-m-d'));
        }

        if ($request->has('to') && $request->to != '') {
            $query->whereDate('date', '<', Carbon::parse($request->to)->format('Y-m-d'));
        }

        if ($request->has('driver') && $request->driver != '') {
            $query->where('driver', $request->driver);
        }

        if ($request->has('representative') && $request->representative != '') {
            $query->where('representative', $request->representative);
        }

        $index = $request->start;
        $term = $request->search['value'];
        $query->where('refno', 'LIKE', '%' . $term . '%');
        
        $allCount=$query->count();
        
        if ($request->length != '-1') {
            $query->skip($request->start)->take($request->length);
        }

        $totalextrakm = 0;
        $totaljourney = 0;
        $totaldiscount = 0;
        $totaldriver = 0;
        $totalcompany = 0;

        foreach ($query->with('driverdata')->with('representativedata')->with('pricingrecords')->orderBy('id', 'DESC')->get() as $key => $invoice) {
            $totalextrakm += $invoice->kmtotal + $invoice->extrakm;
            $totaljourney += $invoice->journey_total;
            $totaldiscount += $invoice->discount_total;
            $totaldriver += $invoice->driver_total - $invoice->discount_total;
            $totalcompany += $invoice->journey_total - $invoice->driver_total;
            
            $endLocations='';
            
            
            foreach($invoice['pricingrecords'] as $keyPri => $ValuePri){
                 $endLocations.=$ValuePri['enddata']->location.((count($invoice['pricingrecords'])>1)?'<br>':'');
            }
            
            $data[] = [
                $invoice->id,
                $invoice->created_at->format('Y/m/d').'<br>'.$invoice->created_at->format('H:i A'),
                $invoice->refno.'<br>'.' <span class="badge badge-primary">' . $invoice->dcc . '</span>',
                $invoice['driverdata']->turnno . ' - ' . $invoice['driverdata']->fname . ' ' . $invoice['driverdata']->lname,
                $invoice['representativedata']->first_name . ' ' . $invoice['representativedata']->last_name,
                // $invoice->kmtotal + $invoice->extrakm,
                $endLocations,
                format_currency($invoice->journey_total),
                format_currency($invoice->discount_total),
                format_currency($invoice->driver_total - $invoice->discount_total),
                format_currency($invoice->journey_total - $invoice->driver_total),
                '<i onclick="doView(' . $invoice->id . ')" class="la la-eye ml-1 text-warning"></i><i onclick="doPrint(' . $invoice->id . ')" title="Print" class="la la-print ml-1 text-primary"></i>'.((doPermitted('//invoice/delete'))?'<i onclick="doDelete(' . $invoice->id . ')" class="la la-trash ml-1 text-danger"></i>':'')
            ];
            $index++;
        }

        if(count($data)){
            // $data[] = [0,'-', '-', '<strong>Total</strong>', '<strong>' . $totalextrakm . '</strong>', '<strong>'.format_currency($totaljourney).'</strong>', '<strong>'.format_currency($totaldiscount).'</strong>', '<strong>'.format_currency($totaldriver).'</strong>', '<strong>'.format_currency($totalcompany).'</strong>', ' - ','-'];
            $data[] = [0,'-', '-', '-','-', '<strong>Total</strong>', '<strong>'.format_currency($totaljourney).'</strong>', '<strong>'.format_currency($totaldiscount).'</strong>', '<strong>'.format_currency($totaldriver).'</strong>', '<strong>'.format_currency($totalcompany).'</strong>', ' - ','-'];
        }
        
        return [
            'data' => $data,
            'recordsFiltered' => $allCount,
            'recordsTotal' => $query->count(),
        ];
    }

    public function getRefNo()
    {
        return env('INVPREFIX', '') . str_pad((new Invoice)->getNextId(), 4, "0", STR_PAD_LEFT);
    }

    public function export($exportable)
    {
        return Excel::download(new InvoiceExport(json_decode($exportable)), getDownloadFileName('invoices') . '.xlsx');
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'isusd'=>'required',
            'lkrrate'=>'required|numeric',
            'driver' => 'required|exists:drivers,id',
            'representative' => 'required|exists:representatives,id',
            'passenger' => 'nullable',
            'passport' => 'nullable',
            'remark' => 'nullable',
            'date' => 'nullable|date',
            'pax' => 'nullable',
            'kmtotal' => 'required|numeric',
            'waiting' => 'nullable|numeric',
            'extrakm' => 'nullable|numeric',
            'extrakmtotal' => 'nullable|numeric',
            'waitingtotal' => 'nullable|numeric',
            'driver_total' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'journey_total' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'discount_total' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'grand_total' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'invoicerecords' => 'required|array'
        ]);


        $data = [
            'refno' => $this->getRefNo(),
            'driver' => $request->driver,
            'representative' => $request->representative,
            'date' =>  ($request->has('date'))?$request->date:Carbon::now()->timezone('Asia/Colombo'),
            'created_at' =>(($request->has('date'))?$request->date:Carbon::now()->timezone('Asia/Colombo')),
            'extrapay'=>$request->extrapay,
            'waiting' => ($request->waiting)?$request->waiting:0,
            'extrakm' => ($request->extrakm)?$request->waiting:0,
            'kmtotal' => $request->kmtotal,
            'extrakmtotal' => $request->extrakmtotal,
            'waitingtotal' => $request->waitingtotal,
            'driver_total' => $request->driver_total,
            'journey_total' => $request->journey_total,
            'discount_total' => $request->discount_total,
            'dcc' =>  Carbon::now()->format('Y') . '/' . $request->driver_total . '/' . (($request->journey_total-$request->driver_total)-$request->discount_total),
            'grand_total' => $request->grand_total,
            'isusd'=>$request->isusd,
            'lkrrate'=>$request->lkrrate
        ];

        if ($request->has('passenger') && $request->passenger != '') {
            $data['passenger'] = $request->passenger;
        }
        if ($request->has('passport') && $request->passport != '') {
            $data['passport'] = $request->passport;
        }
        if ($request->has('remark') && $request->remark != '') {
            $data['remark'] = $request->remark;
        }
        if ($request->has('pax') && $request->pax != '') {
            $data['pax'] = $request->pax;
        }

        $invoice = Invoice::create($data);

        foreach ($request->invoicerecords as $pricingKey => $pricingValue) {
            InvoicePricing::create([
                'invoice' => $invoice->id,
                'start' => $pricingValue['start'],
                'end' => $pricingValue['end'],
                'vehicletype' => $pricingValue['vehicletype'],
                'discount' => $pricingValue['discount'],
                'journey_price' => $pricingValue['journey_price'],
                'driver_price' => $pricingValue['driver_price'],
                'km' => $pricingValue['km'],
                'extra' => $pricingValue['extra'],
                'waiting' => $pricingValue['waiting'],
            ]);
        }

        return $this->getInvoiceView($invoice->id, 1);
    }

    protected function getInvoiceView($id, $type = 2)
    {
        $invoice = Invoice::where('id', $id)->with('driverdata')->with('representativedata')->with('pricingrecords')->first();
        if ($invoice) {
            return view('prints.invoice')->with('invoice', $invoice)->with('type', $type);
        } else {
            return 2;
        }
    }
}
