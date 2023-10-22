<?php

namespace App\Http\Controllers\BillingContext;

use App\Models\BillingContext\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Konekt\PdfInvoice\InvoicePrinter;

class InvoiceController
{
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'string',
            'project_id' => 'string',
            'task_id' => 'string',
            'status' => 'string',
            'recipient' => 'string',
            'payer' => 'string',
            'items' => 'array',
            'total' => 'string',
            'currency' => 'string',
            'tax_rate' => 'string',
            'tax_applied' => 'string',
            'total_including_tax' => 'string',
            'create_date' => 'string',
            'due_date' => 'string',
            'paid_date' => 'string',
            'amount_paid' => 'string',
            'title' => 'string',
            'description' => 'string',
        ]);

        $user = Auth::user()->toArray();
        $requestArray = $request->toArray();
        $requestArray['company_id'] = $user['company_id'];
        $requestArray['user_id'] = $user['id'];
        $requestArray['recipient'] = $user['id'];
        Invoice::create($requestArray);
        return new JsonResponse();
    }

    public function update()
    {}

    public function updateStatus()
    {}

    public function delete()
    {}

    public function download()
    {
        /** @var Invoice $invoiceEntity */
        $invoiceEntity = Invoice::query()->get()->first();
        $invoice = new InvoicePrinter();

        /* Header settings */
        // $invoice->setLogo("images/sample1.jpg");   //logo image path
        // $invoice->setColor("#007fff");      // pdf color scheme
        $invoice->setType("Invoice");    // Invoice Type
        $invoice->setReference($invoiceEntity->getAttribute('invoice_number'));   // Reference
        $invoice->setDate($invoiceEntity->getAttribute('create_date'));   //Billing Date
        $invoice->setDue($invoiceEntity->getAttribute('due_date'));    // Due Date
        $invoice->setFrom(explode('|', $invoiceEntity->getAttribute('payer')));
        $invoice->setTo(explode('|', $invoiceEntity->getAttribute('recipient')));
        foreach ($invoiceEntity->getAttribute('items') as $item) {
            $invoice->addItem($item['description'], null, $item['quantity'], $item['tax'], $item['price'], null, $item['total']);
        }

        $invoice->addTotal("Total", $invoiceEntity->total, is_null($invoiceEntity->tax_rate));
        if ($invoiceEntity->tax_rate) {
            $invoice->addTotal("VAT $invoiceEntity->tax_rate%",$invoiceEntity->tax_applied);
            $invoice->addTotal("Total due", $invoiceEntity->total_including_tax,true);
        }

        $invoice->addTitle("Important Notice");
        $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you.");

        return $invoice->render($invoiceEntity->invoice_number.'.pdf','D');
    }

    public function list(): JsonResponse
    {
        $user = Auth::user()->toArray();
        $objects = Invoice::query()
            ->where('company_id', $user['company_id'])
            ->get()
            ->all();
        return new JsonResponse([
            'data' => $objects,
        ]);
    }

    public function find(string $id): JsonResponse
    {
        $objects = Invoice::find($id);
        return new JsonResponse([
            'data' => $objects,
        ]);
    }
}