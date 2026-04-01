<?php namespace App\Http\Controllers\Front;

use App\Models\Page;
use Illuminate\Support\Facades\Validator;
use Praust\App\Http\Controllers\Front\PraustCmsController;
use Praust\App\Models\PraustInfoBox;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Mail;

class CmsController extends PraustCmsController
{
    public function postContact(Request $request): mixed
    {
        $rules = [];
        $rules['g-recaptcha-response'] = ['required', 'captcha'];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames(['g-recaptcha-response' => 'kod Captcha']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'company' => $request->input('company'),
            'phone' => $request->input('phone'),
            'userMessage' => $request->input('message')
        ];
        Mail::send('emails.contact', $data,
            function ($message) use ($data) {
                $message
                    ->to(getClass('Configuration')::getValue('email-contact'))
                    ->replyTo($data['email'])
                    ->subject('Kontakt - ' . $data['name']);
            }
        );

        return redirect()->to(custom_route(app()->getLocale().'.fpage-page-contact-thanks'));
    }
    public function postReference(Request $request): mixed
    {
        $rules = [];
        $rules['g-recaptcha-response'] = ['required', 'captcha'];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames(['g-recaptcha-response' => 'kod Captcha']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'company' => $request->input('company'),
            'phone' => $request->input('phone'),
            'reference' => $request->input('reference'),
            'userMessage' => $request->input('message')
        ];
        Mail::send('emails.reference', $data,
            function ($message) use ($data) {
                $message
                    ->to(getClass('Configuration')::getValue('email-contact'))
                    ->replyTo($data['email'])
                    ->subject('Zapytaj o produkt - '.$data['name'].' - ' . $data['reference']);
            }
        );

        return redirect()->to(custom_route(app()->getLocale().'.fpage-page-contact-thanks'));
    }

    public function postCareer(Request $request): mixed
    {
        $rules = [];
        $rules['g-recaptcha-response'] = ['required', 'captcha'];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames(['g-recaptcha-response' => 'kod Captcha']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        /** @var \App\Models\Work $work */
        $work = (new \App\Models\Work())->newQuery()->findOrFail($request->integer('work_id'));

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'work' => $work,
            'file' => $request->file('file'),
            'userMessage' => $request->input('message')
        ];

        if(in_array($data['file']->extension(), ['php'])) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        Mail::send('emails.career', $data,
            function ($message) use ($data, $work) {
                $message
                    ->to(getClass('Configuration')::getValue('email-contact'))
                    ->replyTo($data['email'])
                    ->subject($work->getName().' - ' . $data['name']);

                if($data['file']) {
                    $message->attach($data['file']->getRealPath(), [
                        'as' => $data['file']->getClientOriginalName(), // If you want you can chnage original name to custom name
                        'mime' => $data['file']->getMimeType()
                    ]);
                }
            }
        );

        return redirect()->to(custom_route(app()->getLocale().'.fpage-page-contact-thanks'));
    }

    public function getThanks(Request $request): mixed
    {
        return response()->view('theme.templates.thanks');
    }
}
