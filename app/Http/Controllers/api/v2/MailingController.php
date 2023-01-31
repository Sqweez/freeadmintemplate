<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\v2\Models\Mailing;
use App\v2\Models\MailingTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MailingController extends Controller
{
    public function store(Request $request): Response {
        $clients = $request->get('clients', []);
        $saveAsTemplate = !!$request->get('saveAsTemplate');
        $user_id = auth()->id();
        $mailing = Mailing::query()
            ->create([
                'user_id' => $user_id,
                'mailing_text' => $request->get('text', '')
            ])
            ->refresh();
        $mailing->recipients()->createMany($clients);
        if ($saveAsTemplate) {
            MailingTemplate::query()
                ->create([
                    'name' => $request->get('template_name'),
                    'template' => $mailing->mailing_text,
                ]);
        }
        return response()->noContent();
    }
}
