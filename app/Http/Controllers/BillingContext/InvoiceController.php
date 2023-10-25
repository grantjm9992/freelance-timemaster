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
        Invoice::create($requestArray);
        return new JsonResponse();
    }

    public function update(Request $request, string $id): JsonResponse
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

        $requestArray = $request->toArray();
        Invoice::find($id)->update($requestArray);
        return new JsonResponse();
    }

    public function updateStatus()
    {}

    public function statistics(): JsonResponse
    {
        $user = Auth::user()->toArray();
        $objects = Invoice::query()
            ->where('company_id', $user['company_id'])
            ->get()
            ->all();

        $paid = array_filter($objects, function ($a) {
            return $a->status === 'PAID';
        });
        $total = 0;
        foreach ($paid as $object) {
            $total += (float)$object->total+(float)$object->tax_applied;
        }

        $pending = array_filter($objects, function ($a) {
            return $a->status === 'PENDING';
        });
        $totalPending = 0;
        foreach ($pending as $object) {
            $totalPending += (float)$object->total+(float)$object->tax_applied;
        }

        return response()->json([
            'total' => $total,
            'totalCount' => count($paid),
            'pending' => $totalPending,
            'pendingCount' => count($pending),
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        Invoice::destroy($id);
        return new JsonResponse();
    }

    public function download(string $id): string
    {
        /** @var Invoice $invoiceEntity */
        $invoiceEntity = Invoice::find($id);
        $invoice = new InvoicePrinter();

        /* Header settings */
        // $invoice->setLogo("images/sample1.jpg");   //logo image path
        // $invoice->setColor("#007fff");      // pdf color scheme
        $invoice->setType($invoiceEntity->getAttribute('title'));    // Invoice Type
        $invoice->setReference($invoiceEntity->getAttribute('description'));   // Reference
        $invoice->setDate($invoiceEntity->getAttribute('create_date'));   //Billing Date
        $invoice->setDue($invoiceEntity->getAttribute('due_date'));    // Due Date
        $invoice->setFrom(explode(',', $invoiceEntity->getAttribute('payer')));
        $invoice->setTo(explode(',', $invoiceEntity->getAttribute('recipient')));
        foreach ($invoiceEntity->getAttribute('items') as $item) {
            $invoice->addItem(
                $item['description'], null, $item['quantity'], null, $item['unitCost'], null, $item['unitCost']*$item['quantity']);
        }

        $invoice->addTotal("Total", $invoiceEntity->sub_total, is_null($invoiceEntity->tax_rate));
        if ($invoiceEntity->tax_rate) {
            $invoice->addTotal("VAT $invoiceEntity->tax_rate%", $invoiceEntity->tax_applied);
            $invoice->addTotal("Total due", $invoiceEntity->total_including_tax,true);
        }

        $invoice->addTitle("Important Notice");
        $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you.");

        return $invoice->render($invoiceEntity->description.'.pdf','D');
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