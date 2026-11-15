<?php

namespace App\Http\Controllers;

use App\Models\CutPlan;
use App\Models\Shipment;
use App\Services\Calendar\FactoryCalendar;

class PdfController extends Controller
{
    public function cutTicket(CutPlan $cutPlan, FactoryCalendar $calendar)
    {
        $cutPlan->load(['marker.style', 'buyerOrder.buyer', 'bundles.components', 'factory']);

        return unicode_pdf()
            ->view('pdf.cut-ticket', [
                'plan' => $cutPlan,
                'printed' => $calendar->format(now(), app()->getLocale()),
            ])
            ->locale(app()->getLocale())
            ->title('Cut ticket '.$cutPlan->lay_no)
            ->download('cut-ticket-'.$cutPlan->lay_no.'.pdf');
    }

    public function invoice(Shipment $shipment, FactoryCalendar $calendar)
    {
        $shipment->load(['buyerOrder.buyer', 'buyerOrder.style', 'buyerOrder.matrixLines', 'factory.company', 'cartons']);

        return unicode_pdf()
            ->view('pdf.invoice', [
                'shipment' => $shipment,
                'printed' => $calendar->format(now(), app()->getLocale()),
            ])
            ->locale(app()->getLocale())
            ->title('Invoice '.$shipment->invoice_no)
            ->download('invoice-'.$shipment->shipment_no.'.pdf');
    }

    public function packingList(Shipment $shipment, FactoryCalendar $calendar)
    {
        $shipment->load(['buyerOrder.buyer', 'buyerOrder.style', 'cartons', 'factory']);

        return unicode_pdf()
            ->view('pdf.packing-list', [
                'shipment' => $shipment,
                'printed' => $calendar->format(now(), app()->getLocale()),
            ])
            ->locale(app()->getLocale())
            ->title('Packing list '.$shipment->shipment_no)
            ->download('packing-list-'.$shipment->shipment_no.'.pdf');
    }
}
